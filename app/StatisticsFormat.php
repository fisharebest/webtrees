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

namespace Fisharebest\Webtrees;

use function array_column;
use function array_map;
use function array_sum;
use function array_unshift;
use function e;
use function hexdec;
use function ltrim;
use function round;
use function sprintf as sprintf1;
use function sprintf as sprintf2;
use function str_split;
use function strip_tags;
use function view;

class StatisticsFormat
{
    public function age(int $days): string
    {
        if ($days < 31) {
            return I18N::plural('%s day', '%s days', $days, I18N::number($days));
        }

        if ($days < 365) {
            $months = (int) ($days / 30.5);
            return I18N::plural('%s month', '%s months', $months, I18N::number($months));
        }

        $years = (int) ($days / 365.25);

        return I18N::plural('%s year', '%s years', $years, I18N::number($years));
    }

    /**
     * Century name, English => 21st, Polish => XXI, etc.
     */
    public function century(int $century): string
    {
        if ($century < 0) {
            return I18N::translate('%s BCE', $this->century(-$century));
        }

        // The current chart engine (Google charts) can't handle <sup></sup> markup
        switch ($century) {
            case 21:
                return strip_tags(I18N::translateContext('CENTURY', '21st'));
            case 20:
                return strip_tags(I18N::translateContext('CENTURY', '20th'));
            case 19:
                return strip_tags(I18N::translateContext('CENTURY', '19th'));
            case 18:
                return strip_tags(I18N::translateContext('CENTURY', '18th'));
            case 17:
                return strip_tags(I18N::translateContext('CENTURY', '17th'));
            case 16:
                return strip_tags(I18N::translateContext('CENTURY', '16th'));
            case 15:
                return strip_tags(I18N::translateContext('CENTURY', '15th'));
            case 14:
                return strip_tags(I18N::translateContext('CENTURY', '14th'));
            case 13:
                return strip_tags(I18N::translateContext('CENTURY', '13th'));
            case 12:
                return strip_tags(I18N::translateContext('CENTURY', '12th'));
            case 11:
                return strip_tags(I18N::translateContext('CENTURY', '11th'));
            case 10:
                return strip_tags(I18N::translateContext('CENTURY', '10th'));
            case 9:
                return strip_tags(I18N::translateContext('CENTURY', '9th'));
            case 8:
                return strip_tags(I18N::translateContext('CENTURY', '8th'));
            case 7:
                return strip_tags(I18N::translateContext('CENTURY', '7th'));
            case 6:
                return strip_tags(I18N::translateContext('CENTURY', '6th'));
            case 5:
                return strip_tags(I18N::translateContext('CENTURY', '5th'));
            case 4:
                return strip_tags(I18N::translateContext('CENTURY', '4th'));
            case 3:
                return strip_tags(I18N::translateContext('CENTURY', '3rd'));
            case 2:
                return strip_tags(I18N::translateContext('CENTURY', '2nd'));
            case 1:
                return strip_tags(I18N::translateContext('CENTURY', '1st'));
            default:
                return ($century - 1) . '01-' . $century . '00';
        }
    }

    public function hitCount(int $count): string
    {
        return view('statistics/hit-count', ['count' => $count]);
    }

    public function percentage(int $count, int $total, int $precision = 1): string
    {
        return $total !== 0 ? I18N::percentage($count / $total, $precision) : '';
    }

    /**
     * @return array<int,string>
     */
    public function interpolateRgb(string $start_color, string $end_color, int $steps): array
    {
        if ($steps === 0) {
            return [];
        }

        $s        = $this->hexToRgb($start_color);
        $e        = $this->hexToRgb($end_color);
        $colors   = [];
        $factor_r = ($e[0] - $s[0]) / $steps;
        $factor_g = ($e[1] - $s[1]) / $steps;
        $factor_b = ($e[2] - $s[2]) / $steps;

        for ($x = 1; $x < $steps; ++$x) {
            $red   = (int) round($s[0] + $factor_r * $x);
            $green = (int) round($s[1] + $factor_g * $x);
            $blue  = (int) round($s[2] + $factor_b * $x);

            $colors[] = sprintf2('#%02x%02x%02x', $red, $green, $blue);
        }

        $colors[] = sprintf1('#%02x%02x%02x', $e[0], $e[1], $e[2]);

        return $colors;
    }

    /**
     * @return array<int>
     */
    private function hexToRgb(string $hex): array
    {
        return array_map(static fn (string $hex): int => (int) hexdec($hex), str_split(ltrim($hex, '#'), 2));
    }

    public function missing(): string
    {
        return I18N::translate('This information is not available.');
    }

    /**
     * @param array<array{0:string,1:int}> $data
     * @param array<string>                $colors
     */
    public function pieChart(
        array $data,
        array $colors,
        string $title,
        string $category,
        string $quantity,
        bool $percentage = false
    ): string {
        // Cannot display a pie chart if there is no data.
        if (array_sum(array_column($data, 1)) === 0) {
            return $this->missing();
        }

        // Google Charts require a header row.
        array_unshift($data, [$category, $quantity]);

        return view('statistics/other/charts/pie', [
            'title'            => $title,
            'data'             => $data,
            'colors'           => $colors,
            'labeledValueText' => $percentage ? 'percentage' : 'value',
            'language'         => I18N::languageTag(),
        ]);
    }

    public function record(?GedcomRecord $record): string
    {
        if ($record === null) {
            return $this->missing();
        }

        if ($record->canShow()) {
            return '<a href="' . e($record->url()) . '">' . $record->fullName() . '</a>';
        }

        return $record->fullName();
    }
}
