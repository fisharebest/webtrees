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

use Fisharebest\Webtrees\Services\TreeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TreeList extends Command
{
    public function __construct(private readonly TreeService $tree_service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(name: 'tree-list')
            ->setDescription(description: 'List trees');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $trees = $this->tree_service->all()->sort(callback: fn ($a, $b) => $a->id() <=> $b->id());

        $table = new Table(output: $output);

        $table->setHeaders(headers: ['ID', 'Name', 'Title', 'Imported']);

        foreach ($trees as $tree) {
            $table->addRow(row: [
                $tree->id(),
                $tree->name(),
                $tree->title(),
                $tree->getPreference(setting_name: 'imported') ? 'Yes' : 'No'
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
