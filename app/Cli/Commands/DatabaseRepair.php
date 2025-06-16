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

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Index;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\DB\Schema;
use Fisharebest\Webtrees\DB\WebtreesSchema;
use Fisharebest\Webtrees\Webtrees;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_filter;
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

        // doctrine/dbal 4.x does not have the concept of "saveSQL"
        foreach ($source->getTables() as $table) {
            if (!$target->hasTable(name: $table->getName())) {
                $source->dropTable(name: $table->getName());
            }
        }

        $schema_diff = $comparator->compareSchemas(oldSchema: $source, newSchema: $target);
        $queries     = $platform->getAlterSchemaSQL(diff: $schema_diff);

        // Workaround for https://github.com/doctrine/dbal/issues/6092
        $phase1 = array_filter(array: $queries, callback: $this->phase1(...));
        $phase2 = array_filter(array: $queries, callback: $this->phase2(...));
        $phase3 = array_filter(array: $queries, callback: $this->phase3(...));

        if ($phase3 === []) {
            $phase3a = [];
        } else {
            // If we are creating foreign keys, delete any invalid references first.
            $phase3a = $this->deleteOrphans(target: $target, platform: $platform);
        }

        foreach ([...$phase1, ...$phase2, ...$phase3a, ...$phase3] as $query) {
            $io->info(message: $query);
            DB::exec(sql: $query);
        }

        return Command::SUCCESS;
    }

    private function phase1(string $query): bool
    {
        return str_contains($query, 'DROP FOREIGN KEY');
    }

    private function phase2(string $query): bool
    {
        return !str_contains($query, 'FOREIGN KEY');
    }

    /** @return list<string> */
    private function deleteOrphans(Schema $target, AbstractPlatform $platform): array
    {
        $queries = [];

        foreach ($target->getTables() as $table) {
            foreach ($table->getForeignKeys() as $foreign_key) {
                $foreign_table = $foreign_key->getQuotedForeignTableName(platform: $platform);

                if ($table->getName() !== $foreign_key->getForeignTableName()) {
                    $local_columns = implode(separator: ',', array: $foreign_key->getQuotedLocalColumns(platform: $platform));
                    $foreign_columns = implode(separator: ',', array: $foreign_key->getQuotedForeignColumns(platform: $platform));

                    $query = DB::delete(table: $table->getName())
                        ->where(
                            '(' . $local_columns . ') NOT IN (SELECT ' . $foreign_columns . ' FROM ' . $foreign_table . ')'
                        );

                    foreach ($foreign_key->getLocalColumns() as $column) {
                        $query = $query->andWhere(DB::expression()->isNotNull(x: $column));
                    }

                    $queries[] = $query->getSQL();
                }
            }
        }

        return $queries;
    }

    private function phase3(string $query): bool
    {
        return str_contains($query, 'FOREIGN KEY') && !str_contains($query, 'DROP FOREIGN KEY');
    }
}
