<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Cli\Commands;

use Doctrine\DBAL\Schema\Index\IndexType;
use Doctrine\DBAL\Schema\Name;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Exception;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\DB\WebtreesSchema;
use Fisharebest\Webtrees\Webtrees;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_map;
use function implode;
use function str_contains;

class DatabaseRepair extends Command
{
    protected function configure(): void
    {
        $this
            ->setName(name: 'database-repair')
            ->setDescription(description: 'Repair the database schema');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        if (Webtrees::SCHEMA_VERSION !== 45) {
            $io->error(message: 'This script only works with schema version 45');

            return Command::FAILURE;
        }

        $platform       = DB::getDBALConnection()->getDatabasePlatform();
        $schema_manager = DB::getDBALConnection()->createSchemaManager();
        $comparator     = $schema_manager->createComparator();
        $source         = $schema_manager->introspectSchema();
        $target         = WebtreesSchema::schema();

        // Do not automatically delete other tables. They may not belong to us.
        foreach ($source->getTables() as $table) {
            if (!$target->hasTable(name: $table->getObjectName()->toString())) {
                $source->dropTable(name: $table->getObjectName()->toString());
            }
        }

        // Remove foreign key constraints before, then add them afterwards after.
        // This prevents problems when the column types or collations change.
        $queries1 = $platform->getAlterSchemaSQL(diff: $comparator->compareSchemas(
            oldSchema: $source,
            newSchema: $this->schemaWithoutConstraints($source),
        ));

        $queries2 = $platform->getAlterSchemaSQL(diff: $comparator->compareSchemas(
            oldSchema: $this->schemaWithoutConstraints($source),
            newSchema: $this->schemaWithoutConstraints($target),
        ));

        // Delete any rows that would violate the new foreign keys.
        $queries3 = $this->deleteOrphans(schema: $target);

        $queries4 = $platform->getAlterSchemaSQL(diff: $comparator->compareSchemas(
            oldSchema: $this->schemaWithoutConstraints($target),
            newSchema: $target,
        ));

        $queries = [
            ...$queries1,
            ...$queries2,
            ...$queries3,
            ...$queries4,
        ];

        foreach ($queries as $query) {
            try {
                if (DB::exec(sql: $query) === false) {
                    $io->error(message: $query);
                } else {
                    $io->success(message: $query);
                }
            } catch (Exception) {
                $io->error(message: $query);
            }
        }

        return Command::SUCCESS;
    }

    /** @return list<string> */
    private function deleteOrphans(Schema $schema): array
    {
        $queries = [];

        foreach ($schema->getTables() as $table) {
            foreach ($table->getForeignKeys() as $foreign_key) {
                $local_table_name   = $table->getObjectName()->toString();
                $foreign_table_name = $foreign_key->getReferencedTableName()->toString();

                if ($local_table_name !== $foreign_table_name) {
                    $referencing_column_names = array_map(
                        static fn (Name $name): string => $name->toString(),
                        $foreign_key->getReferencingColumnNames(),
                    );

                    $referenced_column_names = array_map(
                        static fn (Name $name): string => $name->toString(),
                        $foreign_key->getReferencedColumnNames(),
                    );

                    $local_columns   = implode(separator: ',', array: $referencing_column_names);
                    $foreign_columns = implode(separator: ',', array: $referenced_column_names);

                    $query = DB::delete(table: $local_table_name)
                        ->where(
                            '(' . $local_columns . ') NOT IN (SELECT ' . $foreign_columns . ' FROM ' . $foreign_table_name . ')'
                        );

                    foreach ($foreign_key->getReferencingColumnNames() as $column) {
                        $query = $query->andWhere(DB::expression()->isNotNull(x: $column->toString()));
                    }

                    $queries[] = $query->getSQL();
                }
            }
        }

        return $queries;
    }

    private function schemaWithoutConstraints(Schema $schema): Schema {
        $schema_without_constraints = clone $schema;

        foreach ($schema_without_constraints->getTables() as $table) {
            foreach ($table->getForeignKeys() as $foreign_key) {
                $table->dropForeignKey($foreign_key->getObjectName()->toString());
            }
        }

        return $schema_without_constraints;
    }
}
