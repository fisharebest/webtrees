<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Database\Connection;
use Fisharebest\Database\Schema\IntegerType;
use Fisharebest\Database\Schema\MigrationMode;
use Fisharebest\Database\Schema\Schema;
use Fisharebest\Webtrees\Schema\WebtreesSchema;
use Fisharebest\Webtrees\Webtrees;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

use function array_diff;
use function array_map;
use function count;

final class DatabaseTransferService
{
    private const int BACKUP_APPLICATION_ID = 0x5754424B; // WTBK

    // SQLite defaults to 999 parameters before 3.32.0 and 32766 from 3.32.0 onward.
    // The widest table has 13 columns, so 75 rows also fit the older limit.
    private const int BATCH_SIZE = 75;

    public function completeBackup(Connection $connection): void
    {
        $connection->exec('PRAGMA application_id = ' . self::BACKUP_APPLICATION_ID);
    }

    public function copy(Connection $source, Connection $destination, Schema $schema, OutputInterface $output): void
    {
        $tables = $schema->getTables();
        $total  = 0;

        foreach ($tables as $table) {
            $total += $source->query(table: $table->getName())->count();
        }

        $progress_bar = new ProgressBar($output, $total);
        $progress_bar->setFormat(' %current%/%max% [%bar%] %percent%% %memory%, %elapsed% elapsed');
        $progress_bar->minSecondsBetweenRedraws(0.1);
        $progress_bar->start();

        $destination->transaction(function () use ($source, $destination, $tables, $progress_bar): void {
            foreach ($tables as $table) {
                $name = $table->getName();

                $copy_rows = static function () use ($source, $destination, $name, $progress_bar): void {
                    $batch = [];

                    foreach ($source->query(table: $name)->select('*')->cursor() as $row) {
                        $batch[] = (array) $row;

                        if (count($batch) === self::BATCH_SIZE) {
                            $destination->query(table: $name)->insertBatch($batch);
                            $progress_bar->advance(count($batch));
                            $batch = [];
                        }
                    }

                    if ($batch !== []) {
                        $destination->query(table: $name)->insertBatch($batch);
                        $progress_bar->advance(count($batch));
                    }
                };

                foreach ($table->getColumns() as $column) {
                    if ($column instanceof IntegerType && $column->isAutoIncrement()) {
                        $destination->identityInsert($name, $copy_rows);
                        continue 2;
                    }
                }

                $copy_rows();
            }
        });

        $progress_bar->finish();
        $output->writeln('');
    }

    public function createTables(Connection $connection, Schema $schema): void
    {
        $connection->migrate(
            target: $schema,
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
    }

    public function replaceTables(Connection $connection, Schema $schema): void
    {
        $mode = new MigrationMode(strict_not_null: true, drop_orphan_columns: true, drop_orphan_tables: true);

        $connection->migrate(target: new Schema(), mode: $mode, execute: true);
        $this->createTables($connection, $schema);
    }

    public function schema(): Schema
    {
        return (new WebtreesSchema())->latestVersion()->sortByForeignKeyDependencies();
    }

    public function validateBackup(Connection $connection, Schema $schema): void
    {
        if ((int) $connection->queryOne('PRAGMA application_id') !== self::BACKUP_APPLICATION_ID) {
            throw new RuntimeException('Backup database is incomplete or was created by an older version of webtrees.');
        }

        $this->validateSource($connection, $schema);
    }

    public function validateSource(Connection $connection, Schema $schema): void
    {
        $actual = $connection->introspect();

        foreach ($schema->getTables() as $table) {
            if (!$actual->hasTable($table->getName())) {
                throw new RuntimeException('Missing database table: ' . $table->getName());
            }
        }

        foreach ($actual->getTables() as $table) {
            if (!$schema->hasTable($table->getName())) {
                throw new RuntimeException('Unexpected database table: ' . $table->getName());
            }
        }

        foreach ($schema->getTables() as $table) {
            $columns        = array_map(static fn ($column): string => $column->getName(), $table->getColumns());
            $actual_columns = array_map(static fn ($column): string => $column->getName(), $actual->getTable($table->getName())->getColumns());

            if (count($columns) !== count($actual_columns) || array_diff($columns, $actual_columns) !== [] || array_diff($actual_columns, $columns) !== []) {
                throw new RuntimeException('Unexpected columns in database table: ' . $table->getName());
            }
        }

        $version = $connection->query(table: 'site_setting')
            ->whereEqual('setting_name', 'WT_SCHEMA_VERSION')
            ->value('setting_value');

        if ((string) $version !== (string) Webtrees::SCHEMA_VERSION) {
            throw new RuntimeException('Database schema version does not match this version of webtrees.');
        }
    }
}
