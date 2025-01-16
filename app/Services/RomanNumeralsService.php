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

namespace Fisharebest\Webtrees\Services;

use function str_starts_with;
use function strlen;
use function substr;

/**
 * Convert to and from Roman Numerals
 */
class RomanNumeralsService
{
    // Convert numbers to/from roman numerals
    private const ROMAN_NUMERALS = [
        1000 => 'M',
        900  => 'CM',
        500  => 'D',
        400  => 'CD',
        100  => 'C',
        90   => 'XC',
        50   => 'L',
        40   => 'XL',
        10   => 'X',
        9    => 'IX',
        5    => 'V',
        4    => 'IV',
        1    => 'I',
    ];

    /**
     * Convert a decimal number to roman numerals
     *
     * @param int $number
     *
     * @return string
     */
    public function numberToRomanNumerals(int $number): string
    {
        if ($number < 1) {
            // Cannot convert zero/negative numbers
            return (string) $number;
        }
        $roman = '';
        foreach (self::ROMAN_NUMERALS as $key => $value) {
            while ($number >= $key) {
                $roman  .= $value;
                $number -= $key;
            }
        }

        return $roman;
    }

    /**
     * Convert a roman numeral to decimal
     *
     * @param string $roman
     *
     * @return int
     */
    public function romanNumeralsToNumber(string $roman): int
    {
        $num = 0;
        foreach (self::ROMAN_NUMERALS as $key => $value) {
            while (str_starts_with($roman, $value)) {
                $num += $key;
                $roman = substr($roman, strlen($value));
            }
        }

        return $num;
    }
}
