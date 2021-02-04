<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\SurnameTradition;

/**
 * Children take their father’s surname. Wives take their husband’s surname.
 */
class PaternalSurnameTradition extends PatrilinealSurnameTradition
{
    /**
     * Does this surname tradition change surname at marriage?
     *
     * @return bool
     */
    public function hasMarriedNames(): bool
    {
        return true;
    }

    /**
     * What names are given to a new parent
     *
     * @param string $child_name A GEDCOM NAME
     * @param string $parent_sex M, F or U
     *
     * @return array<string,string> Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
     */
    public function newParentNames(string $child_name, string $parent_sex): array
    {
        if (preg_match(self::REGEX_SPFX_SURN, $child_name, $match)) {
            switch ($parent_sex) {
                case 'M':
                    return array_filter([
                        'NAME' => $match['NAME'],
                        'SPFX' => $match['SPFX'],
                        'SURN' => $match['SURN'],
                    ]);
                case 'F':
                    return [
                        'NAME'   => '//',
                        '_MARNM' => '/' . trim($match['SPFX'] . ' ' . $match['SURN']) . '/',
                    ];
            }
        }

        return [
            'NAME' => '//',
        ];
    }

    /**
     * What names are given to a new spouse
     *
     * @param string $spouse_name A GEDCOM NAME
     * @param string $spouse_sex  M, F or U
     *
     * @return array<string,string> Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
     */
    public function newSpouseNames(string $spouse_name, string $spouse_sex): array
    {
        if ($spouse_sex === 'F' && preg_match(self::REGEX_SURN, $spouse_name, $match)) {
            return [
                'NAME'   => '//',
                '_MARNM' => $match['NAME'],
            ];
        }

        return [
            'NAME' => '//',
        ];
    }
}
