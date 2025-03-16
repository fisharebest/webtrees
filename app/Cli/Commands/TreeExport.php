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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\TreeService;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function addcslashes;
use function stream_get_contents;

final class TreeExport extends AbstractCommand
{
    public function __construct(
        private readonly GedcomExportService $gedcom_export_service,
        private readonly TreeService $tree_service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(name: 'tree-export')
            ->addArgument(name: 'tree_name', mode: InputArgument::REQUIRED, description: 'The name of the tree', suggestedValues: self::autoCompleteTreeName(...))
            ->addOption(name: 'format', shortcut: null, mode: InputOption::VALUE_REQUIRED, description: 'Export format')
            ->addOption(name: 'filename', shortcut: null, mode: InputOption::VALUE_REQUIRED, description: 'Export filename')
            ->setDescription(description: 'Export a tree to a GEDCOM file');
    }

    /**
     * @return array<string>
     */
    private function autoCompleteTreeName(CompletionInput $input): array
    {
        return DB::table('tree')
            ->where('tree_name', 'LIKE', addcslashes($input->getCompletionValue(), '%_\\') . '%')
            ->pluck('name')
            ->all();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        $tree_name = $input->getArgument(name: 'tree_name');
        $format    = $this->stringOption(input: $input, name: 'format');
        $filename  = $this->stringOption(input: $input, name: 'filename');

        $tree = $this->tree_service->all()[$tree_name] ?? null;

        if ($tree === null) {
            $io->error(message: 'Tree "' . $tree_name . '" not found.');

            return self::FAILURE;
        }

        $stream = $this->gedcom_export_service->export(
            tree: $tree,
            sort_by_xref: false,
            encoding: UTF8::NAME,
            access_level: Auth::PRIV_HIDE,
            line_endings: 'CRLF',
            records: null,
            zip_filesystem: null,
            media_path: null,
        );

        echo stream_get_contents($stream);

        $io->success('File exported successfully.');

        return self::SUCCESS;
    }
}
