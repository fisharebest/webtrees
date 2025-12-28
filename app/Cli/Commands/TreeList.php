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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function addcslashes;
use function array_map;
use function implode;

final class TreeList extends AbstractCommand
{
    public function __construct(private readonly TreeService $tree_service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(name: 'tree-list')
            ->setDescription(description: 'List trees')
            ->addOption(
                name: 'format',
                shortcut: 'f',
                mode: InputOption::VALUE_REQUIRED,
                description: 'Output format (table, json, csv)',
                default: 'table',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $format = $this->stringOption(input: $input, name: 'format');

        $io = new SymfonyStyle(input: $input, output: $output);

        $trees = $this->tree_service->all()->sort(callback: fn ($a, $b) => $a->id() <=> $b->id());

        $headers = ['ID', 'Name', 'Title', 'Media directory', 'Imported'];

        $rows = $trees->map(callback: static fn (Tree $tree): array => [
            'id'              => $tree->id(),
            'name'            => $tree->name(),
            'title'           => $tree->title(),
            'media_directory' => $tree->getPreference(setting_name: 'MEDIA_DIRECTORY'),
            'imported'        => $tree->getPreference(setting_name: 'imported') === '1' ? 'yes' : 'no',
        ])
            ->values()
            ->all();

        switch ($format) {
            case 'table':
                $table = new Table(output: $output);
                $table->setHeaders(headers: $headers);
                $table->setRows(rows: $rows);
                $table->render();
                break;

            case 'csv':
                $output->writeln(messages: $this->quoteCsvRow(columns: $headers));

                foreach ($rows as $row) {
                    $output->writeln(messages: $this->quoteCsvRow(columns: $row));
                }
                break;

            case 'json':
                $output->writeln(messages: json_encode(value: $rows, flags: JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
                break;

            default:
                $io->error(message: 'Invalid format: ‘' . $format . '’');

                return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @param array<string|int> $columns
     */
    private function quoteCsvRow(array $columns): string
    {
        $columns = array_map(callback: $this->quoteCsvValue(...), array: $columns);

        return implode(separator: ',', array: $columns);
    }

    private function quoteCsvValue(string|int $value): string
    {
        return '"' . addcslashes(string: (string) $value, characters: '"') . '"';
    }
}
