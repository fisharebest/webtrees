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

use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class TreeEdit extends AbstractCommand
{
    public function __construct(private readonly TreeService $tree_service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(name: 'tree')
            ->setDescription(description: 'Create/delete/edit a tree')
            ->addArgument(name: 'name', mode: InputArgument::REQUIRED, description: 'The name of the tree')
            ->addOption(name: 'create', mode: InputOption::VALUE_NONE, description: 'Create a new tree')
            ->addOption(name: 'delete', mode: InputOption::VALUE_NONE, description: 'Delete an existing tree')
            ->addOption(name: 'title', mode: InputOption::VALUE_REQUIRED, description: 'Set the title of the tree');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        $name   = $this->stringArgument(input: $input, name: 'name');
        $title  = $this->stringOption(input: $input, name: 'title');
        $create = $this->boolOption(input: $input, name: 'create');
        $delete = $this->boolOption(input: $input, name: 'delete');

        if ($name === '') {
            $io->error(message: 'The tree name cannot be empty.');

            return Command::INVALID;
        }

        if ($delete && $create) {
            $io->error(message: 'Invalid options: cannot use --delete and --create at the same time.');

            return Command::INVALID;
        }

        if ($delete && $title !== '') {
            $io->error(message: 'Invalid options: cannot use --delete and --title at the same time.');

            return Command::INVALID;
        }

        $tree = $this->tree_service->all()->get('name');

        if ($create) {
            if ($tree instanceof Tree) {
                $io->error(message: 'A tree with the name "' . $name . '" already exists.');

                return Command::FAILURE;
            }

            if ($title === '') {
                $io->error(message: 'Invalid options: --title is required when using --create.');

                return Command::FAILURE;
            }

            $this->tree_service->create(name: $name, title: $title);
            $io->info(message: 'Tree ‘' . $name . '’ was created with title ‘' . $title . '’.');

            return self::SUCCESS;
        }

        if ($tree === null) {
            $io->error(message: 'Tree ‘' . $name . '’ does not exist.');

            return Command::FAILURE;
        }

        if ($delete) {
            $this->tree_service->delete($tree);
            $io->success(message: 'Tree ‘' . $name . '’ was deleted.');

            return self::SUCCESS;
        }

        if ($title === '') {
            $io->info(message: 'Nothing to do. Specify --title, --create or --delete.');

            return Command::INVALID;
        }

        $tree->setPreference('title', $title);
        $io->info(message: 'Tree title set to ‘' . $title . '’.');

        return self::SUCCESS;
    }
}
