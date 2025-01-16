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

namespace Fisharebest\Webtrees\Statistics\Service;

use function array_map;
use function hexdec;
use function ltrim;
use function round;
use function sprintf;

class ColorService
{
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
            $colors[] = $this->rgbToHex(
                (int) round($s[0] + $factor_r * $x),
                (int) round($s[1] + $factor_g * $x),
                (int) round($s[2] + $factor_b * $x)
            );
        }

        $colors[] = $this->rgbToHex($e[0], $e[1], $e[2]);

        return $colors;
    }

    private function rgbToHex(int $r, int $g, int $b): string
    {
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * @return array<int>
     */
    private function hexToRgb(string $hex): array
    {
        return array_map(static fn (string $hex): int => (int) hexdec($hex), str_split(ltrim($hex, '#'), 2));
    }
}
