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

use Com\Tecnick\Pdf\Font\Import;
use Fisharebest\Webtrees\Webtrees;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function is_file;
use function realpath;

/**
 * Convert a TTF font file into the JSON format required by tc-lib-pdf-font.
 */
final class ConvertFont extends AbstractCommand
{
    private const string FONT_PATH = Webtrees::ROOT_DIR . 'resources/fonts/';

    protected function configure(): void
    {
        $this
            ->setName(name: 'convert-font')
            ->setDescription(description: 'Convert a TTF font to tc-lib-pdf-font JSON format')
            ->addArgument(name: 'font', mode: InputArgument::REQUIRED, description: 'Filename of the TTF font in /resources/fonts/');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        $font_file = self::FONT_PATH . $this->stringArgument($input, 'font');

        if (!is_file($font_file)) {
            $io->error('Font file not found: ' . $font_file);

            return self::FAILURE;
        }

        // Resolve to an absolute path without ".." segments, which tc-lib-pdf-font rejects.
        $real_font_path = realpath($font_file);
        $real_output_path = realpath(self::FONT_PATH) . '/';

        try {
            $import    = new Import($real_font_path, $real_output_path, 'TrueTypeUnicode', '', 32, 3, 1, false);
            $font_name = $import->getFontName();
        } catch (Throwable $exception) {
            $io->error('Font conversion failed: ' . $exception->getMessage());

            return self::FAILURE;
        }

        $io->success('Converted font "' . $font_name . '" to ' . $real_output_path);

        return self::SUCCESS;
    }
}
