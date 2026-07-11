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

use function file_get_contents;
use function file_put_contents;
use function gzdecode;
use function gzuncompress;
use function is_file;
use function json_encode;
use function ord;
use function pathinfo;
use function preg_match;
use function realpath;
use function str_contains;
use function strlen;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const PATHINFO_FILENAME;

/**
 * Convert a TTF font file into the JSON format required by tc-lib-pdf-font.
 */
final class ConvertFont extends AbstractCommand
{
    private const string FONT_PATH = Webtrees::ROOT_DIR . 'resources/fonts/';
    private const string COVERAGE_FILE_SUFFIX = '.coverage.json';

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
            if (!$this->isAlreadyImportedError($exception->getMessage())) {
                $io->error('Font conversion failed: ' . $exception->getMessage());

                return self::FAILURE;
            }

            $font_name = $this->fontNameFromAlreadyImportedError($exception->getMessage());

            if ($font_name === null) {
                $io->error('Font conversion failed: ' . $exception->getMessage());

                return self::FAILURE;
            }

            $io->note('Font already imported. Reusing existing assets for: ' . $font_name);
        }

        try {
            $this->writeCoverageIndex($font_name, $real_output_path);
        } catch (Throwable $exception) {
            $io->error('Coverage generation failed: ' . $exception->getMessage());

            return self::FAILURE;
        }

        $io->success('Converted font "' . $font_name . '" to ' . $real_output_path);

        return self::SUCCESS;
    }

    private function isAlreadyImportedError(string $message): bool
    {
        return str_contains($message, 'already imported:');
    }

    private function fontNameFromAlreadyImportedError(string $message): string|null
    {
        if (!preg_match('/already imported:\s+(\S+\.json)/', $message, $matches)) {
            return null;
        }

        return pathinfo($matches[1], PATHINFO_FILENAME);
    }

    private function writeCoverageIndex(string $font_name, string $output_path): void
    {
        $ctg_file = $output_path . $font_name . '.ctg.z';

        if (!is_file($ctg_file)) {
            throw new \RuntimeException('Converted coverage file not found: ' . $ctg_file);
        }

        $compressed_data = file_get_contents($ctg_file);

        if ($compressed_data === false) {
            throw new \RuntimeException('Unable to read converted coverage file: ' . $ctg_file);
        }

        $coverage_data = gzuncompress($compressed_data);

        if ($coverage_data === false) {
            $coverage_data = gzdecode($compressed_data);
        }

        if ($coverage_data === false) {
            throw new \RuntimeException('Unable to decode converted coverage file: ' . $ctg_file);
        }

        $ranges = $this->extractBmpRanges($coverage_data);

        $coverage_json = json_encode([
            'font'   => $font_name,
            'format' => 'tc-lib-pdf-bmp-coverage-v1',
            'ranges' => $ranges,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        $written = file_put_contents($output_path . $font_name . self::COVERAGE_FILE_SUFFIX, $coverage_json . "\n");

        if ($written === false) {
            throw new \RuntimeException('Unable to write coverage JSON for font: ' . $font_name);
        }
    }

    /**
     * The tc-lib-pdf .ctg.z payload is a 65536-entry UTF-16BE mapping table.
     * A non-zero entry means the corresponding BMP codepoint has a glyph.
     *
     * @return list<list<int>>
     */
    private function extractBmpRanges(string $coverage_data): array
    {
        if (strlen($coverage_data) !== 0x10000 * 2) {
            throw new \RuntimeException('Unexpected .ctg coverage table size: ' . strlen($coverage_data));
        }

        $ranges = [];
        $start  = null;

        for ($codepoint = 0; $codepoint < 0x10000; ++$codepoint) {
            $offset = $codepoint * 2;
            $glyph  = (ord($coverage_data[$offset]) << 8) + ord($coverage_data[$offset + 1]);

            if ($glyph !== 0) {
                if ($start === null) {
                    $start = $codepoint;
                }
            } elseif ($start !== null) {
                $ranges[] = [$start, $codepoint - 1];
                $start    = null;
            }
        }

        if ($start !== null) {
            $ranges[] = [$start, 0xFFFF];
        }

        return $ranges;
    }
}
