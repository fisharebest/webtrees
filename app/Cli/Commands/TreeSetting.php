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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class TreeSetting extends Command
{
    protected function configure(): void
    {
        $this
            ->setName(name: 'tree-setting')
            ->setDescription(description: 'Configure tree settings')
            ->addOption(name: 'list', shortcut: 'l', mode: InputOption::VALUE_NONE, description: 'List tree settings (optionally filtered by setting name)')
            ->addOption(name: 'delete', shortcut: 'd', mode: InputOption::VALUE_NONE, description: 'Delete a tree setting')
            ->addArgument(name: 'tree-name', mode: InputArgument::REQUIRED, description: 'The tree to update')
            ->addArgument(name: 'setting-name', mode: InputArgument::OPTIONAL, description: 'The setting to update')
            ->addArgument(name: 'setting-value', mode: InputArgument::OPTIONAL, description: 'The new value of the setting.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $quiet  = (bool) $input->getOption(name: 'quiet');
        $list   = (bool) $input->getOption(name: 'list');
        $delete = (bool) $input->getOption(name: 'delete');

        /** @var string $tree_name */
        $tree_name = $input->getArgument(name: 'tree-name');

        /** @var string|null $setting_name */
        $setting_name = $input->getArgument(name: 'setting-name');

        /** @var string|null $setting_value */
        $setting_value = $input->getArgument(name: 'setting-value');

        $io = new SymfonyStyle(input: $input, output: $output);

        $tree_id = DB::table('gedcom')
            ->where(column: 'gedcom_name', operator: '=', value: $tree_name)
            ->value(column: 'gedcom_id');

        if ($tree_id === null) {
            $io->error(message: 'Tree ‘' . $tree_name . '’ not found.');

            return Command::FAILURE;
        }

        if ($list) {
            if ($delete) {
                $io->error(message: 'Cannot specify --list and --delete together.');

                return Command::FAILURE;
            }

            if ($setting_value !== null) {
                $io->error(message: 'Cannot specify --list and a new value together.');

                return Command::FAILURE;
            }

            $table = new Table(output: $output);
            $table->setHeaders(headers: ['Setting name', 'Setting value']);

            /** @var array<object{setting_name:string,setting_value:string}> $settings */
            $settings = DB::table(table: 'gedcom_setting')
                ->where(column: 'gedcom_id', operator: '=', value: $tree_id)
                ->orderBy(column: 'setting_name')
                ->select(columns: ['setting_name', 'setting_value'])
                ->get()
                ->all();

            foreach ($settings as $setting) {
                if ($setting_name === null || str_contains(haystack: $setting->setting_name, needle: $setting_name)) {
                    $table->addRow(row: [$setting->setting_name, $setting->setting_value]);
                }
            }

            $table->render();

            return Command::SUCCESS;
        }

        /** @var string|null $old_setting_value */
        $old_setting_value = DB::table('gedcom_setting')
            ->where(column: 'gedcom_id', operator: '=', value: $tree_id)
            ->where(column: 'setting_name', operator: '=', value: $setting_name)
            ->value(column: 'setting_value');

        if ($delete) {
            if ($setting_name === null) {
                $io->error(message: 'Setting name must be specified for --delete.');

                return Command::FAILURE;
            }

            if ($setting_value !== null) {
                $io->error(message: 'Cannot specify --delete and a new value together.');

                return Command::FAILURE;
            }

            if ($old_setting_value === null) {
                $io->warning(message: 'Tree setting ‘' . $setting_name . '’ not found.  Nothing to delete.');
            } else {
                DB::table('gedcom_setting')
                    ->where(column: 'gedcom_id', operator: '=', value: $tree_id)
                    ->where('setting_name', '=', $setting_name)
                    ->delete();

                $io->success(message: 'Tree setting ‘' . $setting_name . '’ deleted.  Previous value was ‘' . $old_setting_value . '’.');
            }

            return Command::SUCCESS;
        }


        if ($setting_name === null) {
            $io->error(message: 'A setting name is required, unless the --list option is used.');

            return Command::FAILURE;
        }

        if ($setting_value === null) {
            if ($old_setting_value === null) {
                $io->info(message: 'Tree setting ‘' . $setting_name . '’ is not currently set.');
            } elseif ($quiet) {
                $verbosity = $io->getVerbosity();
                $io->setVerbosity(level: OutputInterface::VERBOSITY_NORMAL);
                $io->writeln(messages: $old_setting_value);
                $io->setVerbosity(level: $verbosity);
            } else {
                $io->info(message: 'Tree setting ‘' . $setting_name . '’ is currently set to ‘' . $old_setting_value . '’.');
            }

            return Command::SUCCESS;
        }

        if ($old_setting_value === $setting_value) {
            $io->warning(message: 'Tree setting ' . $setting_name . ' is already set to ' . $setting_value);

            return Command::SUCCESS;
        }

        if ($old_setting_value === null) {
            DB::table(table: 'gedcom_setting')
                ->insert(values: [
                    'gedcom_id'     => $tree_id,
                    'setting_name'  => $setting_name,
                    'setting_value' => $setting_value,
                ]);

            $io->success(message: 'gedcom setting ‘' . $setting_name . '’ was created as ‘' . $setting_value . '’.');
        } else {
            DB::table(table: 'gedcom_setting')
                ->where(column: 'gedcom_id', operator: '=', value: $tree_id)
                ->where(column: 'setting_name', operator: '=', value: $setting_name)
                ->update(values: ['setting_value' => $setting_value]);

            $io->success(message: 'Tree setting ‘' . $setting_name . '’ was changed from ‘' . $old_setting_value . '’ to ‘' . $setting_value . '’.');
        }

        return Command::SUCCESS;
    }
}
