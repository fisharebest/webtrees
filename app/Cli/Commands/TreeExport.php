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
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\TreeService;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ZipArchive;

use function addcslashes;
use function filesize;
use function stream_get_contents;

final class TreeExport extends AbstractCommand
{
    private const array ACCESS_LEVELS = [
        'none'    => Auth::PRIV_HIDE,
        'manager' => Auth::PRIV_NONE,
        'member'  => Auth::PRIV_USER,
        'visitor' => Auth::PRIV_PRIVATE,
    ];

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
            ->addOption(name: 'format', mode: InputOption::VALUE_REQUIRED, description: 'Export format: gedcom (default), gedzip, zip or zipmedia')
            ->addOption(name: 'privacy', mode: InputOption::VALUE_REQUIRED, description: 'Apply privacy: none (default), manager, member or visitor')
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

        $tree_name = $this->stringArgument(input: $input, name: 'tree_name');
        $format    = $this->stringOption(input: $input, name: 'format');
        $privacy   = $this->stringOption(input: $input, name: 'privacy');

        if ($format === '') {
            $format = 'gedcom';
        }

        if ($privacy === '') {
            $privacy = 'none';
        }

        $access_level = self::ACCESS_LEVELS[$privacy] ?? null;

        if ($access_level === null) {
            $io->error(message: 'privacy option should be none, manager, member or visitor');

            return self::FAILURE;
        }

        $tree = $this->tree_service->all()[$tree_name] ?? null;

        if ($tree === null) {
            $io->error(message: 'Tree "' . $tree_name . '" not found.');

            return self::FAILURE;
        }

        $start_time = microtime(true);

        switch ($format) {
            case 'gedcom':
                $media_path     = null;
                $filename       = $tree_name . '.ged';
                $zip_filesystem = null;
                break;

            case 'gedzip':
                $media_path     = '';
                $filename       = $tree_name . '.gdz';
                $zip_filesystem = new ZipArchive();
                $zip_filesystem->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                break;

            case 'zip':
                $media_path     = null;
                $filename       = $tree_name . '.zip';
                $zip_filesystem = new ZipArchive();
                $zip_filesystem->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                break;

            case 'zipmedia':
                $media_path     = $tree->getPreference('MEDIA_DIRECTORY');
                $filename       = $tree_name . '.zip';
                $zip_filesystem = new ZipArchive();
                $zip_filesystem->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                break;

            default:
                $io->error(message: 'Format option should be gedcom, gedzip, zip or zipmedia');

                return self::FAILURE;
        }

        $resource = $this->gedcom_export_service->export(
            tree: $tree,
            sort_by_xref: true,
            access_level: $access_level,
            zip_filesystem: $zip_filesystem,
            media_path: $media_path,
        );

        $gedcom = stream_get_contents($resource);
        fclose($resource);

        if ($gedcom === false) {
            $io->error(message: 'Failed to read GEDCOM');

            return self::FAILURE;
        }

        switch ($format) {
            case 'gedcom':
                file_put_contents($filename, $gedcom);
                break;

            case 'gedzip':
                $zip_filesystem->addFromString('gedcom.ged', $gedcom);
                $zip_filesystem->close();
                break;

            case 'zip':
            case 'zipmedia':
                $zip_filesystem->addFromString($tree_name . '.ged', $gedcom);
                $zip_filesystem->close();
                break;
        }

        $bytes = filesize($filename);
        $seconds = microtime(true) - $start_time;
        $message = sprintf('File exported successfully.  %d bytes written to %s in %.3f seconds', $bytes, $filename, $seconds);

        $io->success($message);

        return self::SUCCESS;
    }
}
