<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Contracts\TreeInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Services\TreeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function bin2hex;
use function random_bytes;

final class TreeCreate extends Command
{
    public function __construct(private readonly TreeService $tree_service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(name: 'tree-create')
            ->setDescription(description: 'Create a new tree')
            ->addOption(name: 'name', shortcut: null, mode: InputOption::VALUE_REQUIRED, description: 'The name of the new tree')
            ->addOption(name: 'title', shortcut: null, mode: InputOption::VALUE_REQUIRED, description: 'The title of the new tree');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        $name  = $input->getOption(name: 'name');
        $title = $input->getOption(name: 'title');

        $missing = false;

        if ($name === null) {
            $io->error(message: 'Missing required option: --name');
            $missing = true;
        }

        if ($title === null) {
            $io->error(message: 'Missing required option: --title');
            $missing = true;
        }

        if ($missing) {
            return Command::INVALID;
        }

        $tree = $this->tree_service->all()[$name] ?? null;

        if ($tree !== null) {
            $io->error(message: 'A tree with the name "' . $name . '" already exists.');

            return Command::FAILURE;
        }

        $tree = $this->tree_service->create(name: $name, title: $title);

        DB::exec(sql: 'COMMIT');

        return Command::SUCCESS;
    }
}
