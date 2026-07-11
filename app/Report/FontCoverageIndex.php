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

namespace Fisharebest\Webtrees\Report;

use Fisharebest\Webtrees\Webtrees;
use RuntimeException;
use Throwable;

use function file_get_contents;
use function is_array;
use function is_file;
use function json_decode;
use function rtrim;
use function strtolower;

use const JSON_THROW_ON_ERROR;

final class FontCoverageIndex
{
    private const string COVERAGE_FILE_SUFFIX = '.coverage.json';

    private string $font_directory;

    /** @var array<string,list<array{0:int,1:int}>> */
    private array $ranges_by_font = [];

    public function __construct(string $font_directory = Webtrees::ROOT_DIR . 'resources/fonts/')
    {
        $this->font_directory = rtrim($font_directory, '/') . '/';
    }

    /**
     * @return list<array{0:int,1:int}>
     */
    public function rangesForFont(string $font_name): array
    {
        $font_key = strtolower($font_name);

        if (!isset($this->ranges_by_font[$font_key])) {
            $this->ranges_by_font[$font_key] = $this->loadRanges($font_key);
        }

        return $this->ranges_by_font[$font_key];
    }

    public function fontSupportsCodepoint(string $font_name, int $codepoint): bool
    {
        if ($codepoint < 0 || $codepoint > 0x10FFFF) {
            throw new RuntimeException('Invalid codepoint: ' . $codepoint);
        }

        // tc-lib-pdf coverage maps in .ctg.z are BMP-only.
        if ($codepoint > 0xFFFF) {
            return false;
        }

        foreach ($this->rangesForFont($font_name) as [$start, $end]) {
            if ($codepoint >= $start && $codepoint <= $end) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<string> $font_names
     */
    public function firstSupportingFont(array $font_names, int $codepoint): string|null
    {
        foreach ($font_names as $font_name) {
            if ($this->fontSupportsCodepoint($font_name, $codepoint)) {
                return $font_name;
            }
        }

        return null;
    }

    /**
     * @return list<array{0:int,1:int}>
     */
    private function loadRanges(string $font_key): array
    {
        $coverage_file = $this->font_directory . $font_key . self::COVERAGE_FILE_SUFFIX;

        if (!is_file($coverage_file)) {
            throw new RuntimeException('Font coverage file not found: ' . $coverage_file);
        }

        $json = file_get_contents($coverage_file);

        if ($json === false) {
            throw new RuntimeException('Unable to read font coverage file: ' . $coverage_file);
        }

        try {
            $coverage_data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new RuntimeException('Invalid font coverage JSON: ' . $coverage_file . ' - ' . $exception->getMessage());
        }

        if (!is_array($coverage_data) || !isset($coverage_data['ranges']) || !is_array($coverage_data['ranges'])) {
            throw new RuntimeException('Invalid coverage format in: ' . $coverage_file);
        }

        $ranges = [];

        foreach ($coverage_data['ranges'] as $range) {
            if (!is_array($range) || !isset($range[0], $range[1])) {
                throw new RuntimeException('Invalid coverage range in: ' . $coverage_file);
            }

            $start = (int) $range[0];
            $end   = (int) $range[1];

            if ($start < 0 || $end < $start || $end > 0xFFFF) {
                throw new RuntimeException('Out-of-range coverage entry in: ' . $coverage_file);
            }

            $ranges[] = [$start, $end];
        }

        return $ranges;
    }
}

