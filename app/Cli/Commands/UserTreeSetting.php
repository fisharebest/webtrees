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
use stdClass;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class UserTreeSetting extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName(name: 'user-tree-setting')
            ->setDescription(description: 'Configure user-tree settings')
            ->addOption(name: 'list', shortcut: 'l', mode: InputOption::VALUE_NONE, description: 'List user-tree settings (optionally filtered by setting name)')
            ->addOption(name: 'delete', shortcut: 'd', mode: InputOption::VALUE_NONE, description: 'Delete a user-tree setting')
            ->addArgument(name: 'user-name', mode: InputArgument::REQUIRED, description: 'The user to update')
            ->addArgument(name: 'tree-name', mode: InputArgument::REQUIRED, description: 'The tree to update')
            ->addArgument(name: 'setting-name', mode: InputArgument::OPTIONAL, description: 'The setting to update')
            ->addArgument(name: 'setting-value', mode: InputArgument::OPTIONAL, description: 'The new value of the setting.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $quiet  = $this->boolOption(input: $input, name: 'quiet');
        $list   = $this->boolOption(input: $input, name: 'list');
        $delete = $this->boolOption(input: $input, name: 'delete');

        /** @var string $user_name */
        $user_name = $input->getArgument(name: 'user-name');

        /** @var string $tree_name */
        $tree_name = $input->getArgument(name: 'tree-name');

        /** @var string|null $setting_name */
        $setting_name = $input->getArgument(name: 'setting-name');

        /** @var string|null $setting_value */
        $setting_value = $input->getArgument(name: 'setting-value');

        $io = new SymfonyStyle(input: $input, output: $output);

        $user_id = DB::table('user')
            ->where(column: 'user_name', operator: '=', value: $user_name)
            ->value(column: 'user_id');

        if ($user_id === null) {
            $io->error(message: 'User ‘' . $user_name . '’ not found.');

            return self::FAILURE;
        }

        $tree_id = DB::table('gedcom')
            ->where(column: 'gedcom_name', operator: '=', value: $tree_name)
            ->value(column: 'gedcom_id');

        if ($tree_id === null) {
            $io->error(message: 'Tree ‘' . $tree_name . '’ not found.');

            return self::FAILURE;
        }

        if ($list) {
            if ($delete) {
                $io->error(message: 'Cannot specify --list and --delete together.');

                return self::FAILURE;
            }

            if ($setting_value !== null) {
                $io->error(message: 'Cannot specify --list and a new value together.');

                return self::FAILURE;
            }

            $table = new Table(output: $output);
            $table->setHeaders(headers: ['Setting name', 'Setting value']);

            $settings = DB::table(table: 'user_gedcom_setting')
                ->where(column: 'user_id', operator: '=', value: $user_id)
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

            return self::SUCCESS;
        }

        /** @var string|null $old_setting_value */
        $old_setting_value = DB::table('user_gedcom_setting')
            ->where(column: 'user_id', operator: '=', value: $user_id)
            ->where(column: 'gedcom_id', operator: '=', value: $tree_id)
            ->where(column: 'setting_name', operator: '=', value: $setting_name)
            ->value(column: 'setting_value');

        if ($delete) {
            if ($setting_name === null) {
                $io->error(message: 'Setting name must be specified for --delete.');

                return self::FAILURE;
            }

            if ($setting_value !== null) {
                $io->error(message: 'Cannot specify --delete and a new value together.');

                return self::FAILURE;
            }

            if ($old_setting_value === null) {
                $io->warning(message: 'User-tree setting ‘' . $setting_name . '’ not found.  Nothing to delete.');
            } else {
                DB::table('user_gedcom_setting')
                    ->where(column: 'user_id', operator: '=', value: $user_id)
                    ->where(column: 'gedcom_id', operator: '=', value: $tree_id)
                    ->where('setting_name', '=', $setting_name)
                    ->delete();

                $io->success(message: 'User-tree setting ‘' . $setting_name . '’ deleted.  Previous value was ‘' . $old_setting_value . '’.');
            }

            return self::SUCCESS;
        }


        if ($setting_name === null) {
            $io->error(message: 'A setting name is required, unless the --list option is used.');

            return self::FAILURE;
        }

        if ($setting_value === null) {
            if ($old_setting_value === null) {
                $io->info(message: 'User-tree setting ‘' . $setting_name . '’ is not currently set.');
            } elseif ($quiet) {
                $verbosity = $io->getVerbosity();
                $io->setVerbosity(level: OutputInterface::VERBOSITY_NORMAL);
                $io->writeln(messages: $old_setting_value);
                $io->setVerbosity(level: $verbosity);
            } else {
                $io->info(message: 'User-tree setting ‘' . $setting_name . '’ is currently set to ‘' . $old_setting_value . '’.');
            }

            return self::SUCCESS;
        }

        if ($old_setting_value === $setting_value) {
            $io->warning(message: 'User-tree setting ' . $setting_name . ' is already set to ' . $setting_value);

            return self::SUCCESS;
        }

        if ($old_setting_value === null) {
            DB::table(table: 'user_gedcom_setting')
                ->insert(values: [
                    'user_id'       => $user_id,
                    'gedcom_id'     => $tree_id,
                    'setting_name'  => $setting_name,
                    'setting_value' => $setting_value,
                ]);

            $io->success(message: 'User-tree setting ‘' . $setting_name . '’ was created as ‘' . $setting_value . '’.');
        } else {
            DB::table(table: 'user_gedcom_setting')
                ->where(column: 'user_id', operator: '=', value: $user_id)
                ->where(column: 'gedcom_id', operator: '=', value: $tree_id)
                ->where(column: 'setting_name', operator: '=', value: $setting_name)
                ->update(values: ['setting_value' => $setting_value]);

            $io->success(message: 'User-tree setting ‘' . $setting_name . '’ was changed from ‘' . $old_setting_value . '’ to ‘' . $setting_value . '’.');
        }

        return self::SUCCESS;
    }
}
