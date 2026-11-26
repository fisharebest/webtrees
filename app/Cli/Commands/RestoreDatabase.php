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

namespace Fisharebest\Webtrees\Cli\Commands;

use Fisharebest\Database\Connection;
use Fisharebest\Database\Driver;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\DatabaseTransferService;
use Fisharebest\Webtrees\Services\MaintenanceModeService;
use Fisharebest\Webtrees\Webtrees;
use PDO;
use Pdo\Sqlite;
use RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function is_file;
use function realpath;

final class RestoreDatabase extends AbstractCommand
{
    public function __construct(
        private readonly DatabaseTransferService $database_transfer_service,
        private readonly MaintenanceModeService $maintenance_mode_service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('restore-database')
            ->setDescription('Restore the application database from an SQLite file')
            ->addArgument('filename', InputArgument::REQUIRED, 'The backup filename in data/');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io       = new SymfonyStyle($input, $output);
        $filename = $this->stringArgument($input, 'filename');
        $path     = Webtrees::DATA_DIR . $filename;

        if (!is_file($path)) {
            $io->error('Backup file not found in data/: ' . $filename);

            return self::FAILURE;
        }

        $offline = $this->maintenance_mode_service->isOffline();

        if (!$offline) {
            $message = 'Database restore in progress';
            $result  = Registry::container()
                ->get(SiteOffline::class)
                ->run(input: new ArrayInput(parameters: ['message' => $message]), output: $output);

            if ($result !== self::SUCCESS) {
                return self::FAILURE;
            }
        }

        try {
            $destination = DB::connection();

            if ($destination->driver() === Driver::SQLite) {
                $database = $destination->pdo()->query('PRAGMA database_list')->fetchAll(PDO::FETCH_ASSOC);

                foreach ($database as $entry) {
                    if ($entry['name'] === 'main' && realpath($entry['file']) === realpath($path)) {
                        throw new RuntimeException('Cannot restore a database from itself.');
                    }
                }
            }

            $pdo = new Sqlite('sqlite:' . $path);
            $pdo->exec('PRAGMA foreign_keys = ON');
            $source = new Connection($pdo);

            if (
                $pdo->query('PRAGMA integrity_check')->fetchColumn() !== 'ok' ||
                $pdo->query('PRAGMA foreign_key_check')->fetch() !== false
            ) {
                throw new RuntimeException('Backup database failed SQLite integrity checks.');
            }

            $source->transaction(function () use ($source, $destination, $output): void {
                $schema = $this->database_transfer_service->schema();
                $this->database_transfer_service->validateBackup($source, $schema);

                $this->database_transfer_service->replaceTables($destination, $schema);
                $this->database_transfer_service->copy($source, $destination, $schema, $output);
            });
        } catch (Throwable $exception) {
            $io->error($exception->getMessage());

            return self::FAILURE;
        }

        $io->success('Database restored from data/' . $filename);

        // Restore the original on/offline state.
        if (!$offline) {
            Registry::container()
                ->get(SiteOnline::class)
                ->run(input: new ArrayInput(parameters: []), output: $output);
        }

        return self::SUCCESS;
    }
}
