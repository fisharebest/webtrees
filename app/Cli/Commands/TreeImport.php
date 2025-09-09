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
use Fisharebest\Webtrees\Exceptions\GedcomErrorException;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function addcslashes;
use function file_exists;
use function file_get_contents;
use function filesize;
use function fopen;
use function gc_collect_cycles;
use function preg_split;
use function str_replace;

final class TreeImport extends AbstractCommand
{
    public function __construct(
        private readonly GedcomImportService $gedcom_import_service,
        private readonly TreeService $tree_service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(name: 'tree-import')
            ->addArgument(name: 'tree-name', mode: InputArgument::REQUIRED, description: 'The name of the tree', suggestedValues: self::autoCompleteTreeName(...))
            ->addArgument(name: 'gedcom-file', mode: InputArgument::REQUIRED, description: 'Path to the GEDCOM file')
            ->addOption(name: 'encoding', mode: InputOption::VALUE_REQUIRED, description: 'Encoding of the GEDCOM file')
            ->addOption(name: 'keep-media', mode: InputOption::VALUE_OPTIONAL, description: 'Merge existing media with the new file')
            ->addOption(name: 'conc-spaces', mode: InputOption::VALUE_OPTIONAL, description: 'Add spaces when wrapping CONC tags')
            ->addOption(name: 'gedcom-media-path', mode: InputOption::VALUE_OPTIONAL, description: 'Strip media path from OBJE records')
            ->setDescription(description: 'Import a tree from a GEDCOM file');
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

        $tree_name          = $this->stringArgument(input: $input, name: 'tree-name');
        $gedcom_file        = $this->stringArgument(input: $input, name: 'gedcom-file');
        $keep_media         = $this->boolOption(input: $input, name: 'keep-media');
        $word_wrapped_notes = $this->boolOption(input: $input, name: 'conc-spaces');
        $gedcom_media_path  = $this->stringOption(input: $input, name: 'gedcom-media-path');
        $encoding           = $this->stringOption(input: $input, name: 'encoding');

        $tree = $this->tree_service->all()[$tree_name] ?? null;

        if ($tree === null) {
            $io->error(message: 'Tree "' . $tree_name . '" not found.');

            return self::FAILURE;
        }

        if (!file_exists($gedcom_file)) {
            $io->error(message: 'File "' . $gedcom_file . '" does not exist.');

            return self::FAILURE;
        }

        try {
            DB::connection()->beginTransaction();

            $tree->setPreference('imported', '0');
            $tree->setPreference('keep_media', $keep_media ? '1' : '0');
            $tree->setPreference('WORD_WRAPPED_NOTES', $word_wrapped_notes ? '1' : '0');
            $tree->setPreference('GEDCOM_MEDIA_PATH', $gedcom_media_path);

            $queries = [
                'individuals' => DB::table('individuals')->where('i_file', '=', $tree->id()),
                'families'    => DB::table('families')->where('f_file', '=', $tree->id()),
                'sources'     => DB::table('sources')->where('s_file', '=', $tree->id()),
                'other'       => DB::table('other')->where('o_file', '=', $tree->id()),
                'places'      => DB::table('places')->where('p_file', '=', $tree->id()),
                'placelinks'  => DB::table('placelinks')->where('pl_file', '=', $tree->id()),
                'name'        => DB::table('name')->where('n_file', '=', $tree->id()),
                'dates'       => DB::table('dates')->where('d_file', '=', $tree->id()),
                'change'      => DB::table('change')->where('gedcom_id', '=', $tree->id()),
            ];


            if ($keep_media) {
                $queries['link'] = DB::table('link')
                    ->where('l_file', '=', $tree->id())
                    ->where('l_type', '<>', 'OBJE');
            } else {
                $queries += [
                    'link'       => DB::table('link')->where('l_file', '=', $tree->id()),
                    'media_file' => DB::table('media_file')->where('m_file', '=', $tree->id()),
                    'media'      => DB::table('media')->where('m_file', '=', $tree->id()),
                ];
            }

            $io->info('Deleting old genealogy data.');

            $progress_bar = new ProgressBar($output, count($queries));
            $progress_bar->setFormat(' %current%/%max% [%bar%] %percent%% %memory%, %elapsed% elapsed');
            $progress_bar->setRedrawFrequency(1);
            $progress_bar->start();

            foreach ($queries as $name => $query) {
                $query->delete();
                $progress_bar->advance();
            }

            $progress_bar->finish();
            $output->writeln('');

            $io->info('Importing new genealogy data.');

            $total_bytes  = filesize($gedcom_file);

            $bytes_loaded = 0;

            $fp     = fopen($gedcom_file, 'rb');
            $buffer = '';

            $progress_bar = new ProgressBar($output, $total_bytes);
            $progress_bar->setFormat(' %current%/%max% [%bar%] %percent%%, %memory%, %elapsed% elapsed, %remaining% remaining');
            $progress_bar->setRedrawFrequency(1);
            $progress_bar->minSecondsBetweenRedraws(0.1);

            while ($bytes_loaded < $total_bytes) {
                $tmp = fread($fp, 8192);
                $buffer .= $tmp;
                $bytes_loaded += strlen($tmp);

                $records = preg_split('/[\r\n]+(?=0)/', $buffer);
                $buffer = array_pop($records);

                foreach ($records as $record) {
                    $this->gedcom_import_service->importRecord($record, $tree, false);
                }

                $progress_bar->setProgress($bytes_loaded);
            }
            $progress_bar->finish();

            $output->writeln('');

            $tree->setPreference('imported', '1');

            DB::connection()->commit();
        } catch (Throwable $ex) {
            $io->error(message: $ex->getMessage());
            DB::connection()->rollBack();

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
