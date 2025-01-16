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

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Webtrees;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function file_exists;
use function file_put_contents;
use function parse_ini_file;

final class ConfigIni extends Command
{
    protected function configure(): void
    {
        if (file_exists(filename: Webtrees::CONFIG_FILE)) {
            $config = parse_ini_file(filename: Webtrees::CONFIG_FILE);

            if ($config === false) {
                $config = [];
            }
        } else {
            $config = [];
        }

        $this
            ->setName(name: 'config-ini')
            ->setDescription(description: 'Set values in data/config.ini.php')
            ->addOption(name: 'dbtype', mode: InputOption::VALUE_OPTIONAL, description: 'Database type', default: $config['dbtype'] ?? 'mysql')
            ->addOption(name: 'dbhost', mode: InputOption::VALUE_OPTIONAL, description: 'Database host', default: $config['dbhost'] ?? '')
            ->addOption(name: 'dbport', mode: InputOption::VALUE_OPTIONAL, description: 'Database port', default: $config['dbport'] ?? '')
            ->addOption(name: 'dbuser', mode: InputOption::VALUE_OPTIONAL, description: 'Database user', default: $config['dbuser'] ?? '')
            ->addOption(name: 'dbpass', mode: InputOption::VALUE_OPTIONAL, description: 'Database password', default: $config['dbpass'] ?? '')
            ->addOption(name: 'dbname', mode: InputOption::VALUE_OPTIONAL, description: 'Database name', default: $config['dbname'] ?? 'webtrees')
            ->addOption(name: 'dbkey', mode: InputOption::VALUE_OPTIONAL, description: 'Location of SSL key for encrypted database connection', default: $config['dbkey'] ?? '')
            ->addOption(name: 'dbcert', mode: InputOption::VALUE_OPTIONAL, description: 'Location of SSL certificate for encrypted database connection', default: $config['dbcert'] ?? '')
            ->addOption(name: 'dbca', mode: InputOption::VALUE_OPTIONAL, description: 'Location of certificate authority file for encrypted database connection', default: $config['dbca'] ?? '')
            ->addOption(name: 'dbverify', mode: InputOption::VALUE_NEGATABLE, description: 'Verify SSL certificate', default: (bool) ($config['dbverify'] ?? false))
            ->addOption(name: 'tblpfx', mode: InputOption::VALUE_OPTIONAL, description: 'Table prefix', default: $config['tblpfx'] ?? '')
            ->addOption(name: 'base-url', mode: InputOption::VALUE_OPTIONAL, description: 'Base URL', default: $config['base_url'] ?? '')
            ->addOption(name: 'rewrite-urls', mode: InputOption::VALUE_NEGATABLE, description: 'Use pretty URLs', default: (bool) ($config['rewrite_urls'] ?? false))
            ->addOption(name: 'block-asn', mode: InputOption::VALUE_OPTIONAL, description: 'List of ASNs to block', default: $config['block_asn'] ?? '')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        $data =
            '; <?php return; ?> DO NOT DELETE THIS LINE' . PHP_EOL;

        $config = [
            'dbtype'       => $input->getOption(name: 'dbtype'),
            'dbhost'       => $input->getOption(name: 'dbhost'),
            'dbport'       => $input->getOption(name: 'dbport'),
            'dbuser'       => $input->getOption(name: 'dbuser'),
            'dbpass'       => $input->getOption(name: 'dbpass'),
            'dbname'       => $input->getOption(name: 'dbname'),
            'dbkey'        => $input->getOption(name: 'dbkey'),
            'dbcert'       => $input->getOption(name: 'dbcert'),
            'dbca'         => $input->getOption(name: 'dbca'),
            'dbverify'     => (int) (bool) $input->getOption(name: 'dbverify'),
            'tblpfx'       => $input->getOption(name: 'tblpfx'),
            'base_url'     => rtrim(string: $input->getOption(name: 'base-url'), characters: '/'),
            'rewrite_urls' => (int) (bool) $input->getOption(name: 'rewrite-urls'),
            'block_asn'    => $input->getOption(name: 'block-asn'),
        ];

        foreach ($config as $key => $value) {
            $data .= $key . ' = "' . addcslashes(string: (string) $value, characters: '"') . '"' . PHP_EOL;
        }

        $io->info(message: $data);
        file_put_contents(filename: Webtrees::CONFIG_FILE, data: $data);

        if ($input->getOption(name: 'base-url') === '') {
            $io->warning(message: 'You must set the base URL');
        }

        try {
            $config = parse_ini_file(filename: Webtrees::CONFIG_FILE);

            DB::connect(
                driver: $config['dbtype'],
                host: $config['dbhost'],
                port: $config['dbport'],
                database: $config['dbname'],
                username: $config['dbuser'],
                password: $config['dbpass'],
                prefix: $config['tblpfx'],
                key: $config['dbkey'],
                certificate: $config['dbcert'],
                ca: $config['dbca'],
                verify_certificate: (bool) $config['dbverify'],
            );

            $io->success(message: 'Database connection successful');
        } catch (Throwable $ex) {
            $io->error(message: 'Database connection failed: ' . $ex->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
