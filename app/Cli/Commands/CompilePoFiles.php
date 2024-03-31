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

use Fisharebest\Localization\Translation;
use Fisharebest\Webtrees\Webtrees;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function basename;
use function count;
use function dirname;
use function file_put_contents;
use function glob;
use function realpath;
use function var_export;

class CompilePoFiles extends Command
{
    private const PO_FILE_PATTERN = Webtrees::ROOT_DIR . 'resources/lang/*/*.po';

    protected function configure(): void
    {
        $this
            ->setName(name: 'compile-po-files')
            ->setDescription(description: 'Convert the PO files into PHP files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        $po_files = glob(pattern: self::PO_FILE_PATTERN);

        if ($po_files === false || $po_files === []) {
            $io->error('Failed to find any PO files matching ' . self::PO_FILE_PATTERN);

            return Command::FAILURE;
        }

        $error = false;

        foreach ($po_files as $po_file) {
            $po_file      = realpath($po_file);
            $translation  = new Translation(filename: $po_file);
            $translations = $translation->asArray();
            $php_file     = dirname(path: $po_file) . '/' . basename(path: $po_file, suffix: '.po') . '.php';
            $php_code     = "<?php\n\nreturn " . var_export(value: $translations, return: true) . ";\n";
            $bytes        = file_put_contents(filename: $php_file, data: $php_code);

            if ($bytes === false) {
                $io->error('Failed to write to ' . $php_file);
                $error = true;
            } else {
                $io->success('Created ' . $php_file . ' with ' . count(value: $translations) . ' translations');
            }
        }

        return $error ? Command::FAILURE : Command::SUCCESS;
    }
}
