<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function basename;
use function count;
use function dirname;
use function file_put_contents;
use function glob;
use function is_dir;
use function rtrim;
use function var_export;

use const DIRECTORY_SEPARATOR;

final class CompilePoFiles extends AbstractCommand
{
    private const string DEFAULT_SOURCE = Webtrees::ROOT_DIR . 'resources/lang';

    protected function configure(): void
    {
        $this
            ->setName(name: 'compile-po-files')
            ->setDescription(description: 'Convert the PO files into PHP files')
            ->addOption(name: 'source', shortcut: 's', mode: InputOption::VALUE_REQUIRED, description: 'Source folder containing LANG/messages.po files', default: self::DEFAULT_SOURCE)
            ->addOption(name: 'destination', shortcut: 'd', mode: InputOption::VALUE_REQUIRED, description: 'Destination folder for LANG/messages.php files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        $source      = rtrim($this->stringOption(input: $input, name: 'source'), DIRECTORY_SEPARATOR);
        $destination = $this->stringOption(input: $input, name: 'destination');
        $destination = $destination === '' ? $source : rtrim($destination, DIRECTORY_SEPARATOR);

        if (!is_dir($source)) {
            $io->error('The source directory does not exist: ' . $source);

            return self::FAILURE;
        }

        if (!is_dir($destination)) {
            $io->error('The destination directory does not exist: ' . $destination);

            return self::FAILURE;
        }

        $po_file_pattern = $source . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'messages.po';
        $po_files        = glob(pattern: $po_file_pattern);

        if ($po_files === false || $po_files === []) {
            $io->error('Failed to find any PO files matching ' . $po_file_pattern);

            return self::FAILURE;
        }

        $error = false;

        foreach ($po_files as $po_file) {
            $translation  = new Translation(filename: $po_file);
            $translations = $translation->asArray();
            $language     = basename(path: dirname(path: $po_file));
            $php_file     = $destination . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . 'messages.php';
            $php_code     = "<?php\n\nreturn " . var_export(value: $translations, return: true) . ";\n";
            $bytes        = file_put_contents(filename: $php_file, data: $php_code);

            if ($bytes === false) {
                $io->error('Failed to write to ' . $php_file);
                $error = true;
            } else {
                $io->success('Created ' . $php_file . ' with ' . count(value: $translations) . ' translations');
            }
        }

        return $error ? self::FAILURE : self::SUCCESS;
    }
}
