<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

namespace Fisharebest\Webtrees\Statistics\Service;

use function array_map;
use function hexdec;
use function ltrim;
use function round;
use function sprintf;

/**
 * Functions for managing and manipulating colors.
 */
class ColorService
{
    /**
     * Interpolates the number of color steps between a given start and end color.
     *
     * @param string $startColor The start color
     * @param string $endColor   The end color
     * @param int    $steps      The number of steps to interpolate
     *
     * @return array<string>
     */
    public function interpolateRgb(string $startColor, string $endColor, int $steps): array
    {
        if ($steps === 0) {
            return [];
        }

        $s       = $this->hexToRgb($startColor);
        $e       = $this->hexToRgb($endColor);
        $colors  = [];
        $factorR = ($e[0] - $s[0]) / $steps;
        $factorG = ($e[1] - $s[1]) / $steps;
        $factorB = ($e[2] - $s[2]) / $steps;

        for ($x = 1; $x < $steps; ++$x) {
            $colors[] = $this->rgbToHex(
                (int) round($s[0] + $factorR * $x),
                (int) round($s[1] + $factorG * $x),
                (int) round($s[2] + $factorB * $x)
            );
        }

        $colors[] = $this->rgbToHex($e[0], $e[1], $e[2]);

        return $colors;
    }

    /**
     * Converts the color values to the HTML hex representation.
     *
     * @param int $r The red color value
     * @param int $g The green color value
     * @param int $b The blue color value
     *
     * @return string
     */
    private function rgbToHex(int $r, int $g, int $b): string
    {
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Converts the HTML color hex representation to an array of color values.
     *
     * @param string $hex The HTML hex color code
     *
     * @return array<int>
     */
    private function hexToRgb(string $hex): array
    {
        return array_map(static function (string $hex): int {
            return (int) hexdec($hex);
        }, str_split(ltrim($hex, '#'), 2));
    }
}
