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
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Services\DatabaseTransferService;
use Fisharebest\Webtrees\Webtrees;
use Pdo\Sqlite;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function file_exists;
use function gmdate;
use function str_ends_with;
use function strpbrk;
use function unlink;

final class DatabaseExport extends AbstractCommand
{
    public function __construct(private readonly DatabaseTransferService $database_transfer_service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('database-export')
            ->setDescription('Export the application database to an SQLite file')
            ->addArgument('filename', InputArgument::OPTIONAL, 'Output filename or path (.sqlite)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io       = new SymfonyStyle($input, $output);
        $filename = $this->stringArgument($input, 'filename');

        if ($filename === '') {
            $filename = 'export-' . gmdate('Ymd-His') . '.sqlite';
        }

        if (!str_ends_with($filename, '.sqlite')) {
            $io->error('Export filename must end in .sqlite: ' . $filename);

            return self::FAILURE;
        }

        $path = strpbrk($filename, '/\\') === false ? Webtrees::DATA_DIR . $filename : $filename;

        if (file_exists($path)) {
            $io->error('Export file already exists: ' . $path);

            return self::FAILURE;
        }

        try {
            $schema = $this->database_transfer_service->schema();
            $source = DB::connection();

            $destination = $source->transaction(function () use ($source, $schema, $output, $path): Connection {
                $this->database_transfer_service->validateSource($source, $schema);

                $pdo = new Sqlite('sqlite:' . $path);
                $pdo->exec('PRAGMA foreign_keys = ON');
                $destination = new Connection($pdo);

                $this->database_transfer_service->createTables($destination, $schema);
                $this->database_transfer_service->copy($source, $destination, $schema, $output);

                return $destination;
            });

            $this->database_transfer_service->completeBackup($destination);
        } catch (Throwable $exception) {
            if (file_exists($path)) {
                unlink($path);
            }

            $io->error($exception->getMessage());

            return self::FAILURE;
        }

        $io->success('Database exported to ' . $path);

        return self::SUCCESS;
    }
}
