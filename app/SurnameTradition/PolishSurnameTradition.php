<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\SurnameTradition;

/**
 * Children take their father’s surname. Wives take their husband’s surname. Surnames are inflected to indicate an individual’s sex.
 */
class PolishSurnameTradition extends PaternalSurnameTradition
{
    // Inflect a surname for females
    private const INFLECT_FEMALE = [
        'cki\b'  => 'cka',
        'dzki\b' => 'dzka',
        'ski\b'  => 'ska',
        'żki\b'  => 'żka',
    ];

    // Inflect a surname for males
    private const INFLECT_MALE = [
        'cka\b'  => 'cki',
        'dzka\b' => 'dzki',
        'ska\b'  => 'ski',
        'żka\b'  => 'żki',
    ];

    /**
     * What names are given to a new child
     *
     * @param string $father_name A GEDCOM NAME
     * @param string $mother_name A GEDCOM NAME
     * @param string $child_sex   M, F or U
     *
     * @return string[] Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
     */
    public function newChildNames(string $father_name, string $mother_name, string $child_sex): array
    {
        if (preg_match(self::REGEX_SURN, $father_name, $match)) {
            if ($child_sex === 'F') {
                return array_filter([
                    'NAME' => $this->inflect($match['NAME'], self::INFLECT_FEMALE),
                    'SURN' => $this->inflect($match['SURN'], self::INFLECT_MALE),
                ]);
            }

            return array_filter([
                'NAME' => $this->inflect($match['NAME'], self::INFLECT_MALE),
                'SURN' => $this->inflect($match['SURN'], self::INFLECT_MALE),
            ]);
        }

        return [
            'NAME' => '//',
        ];
    }

    /**
     * What names are given to a new parent
     *
     * @param string $child_name A GEDCOM NAME
     * @param string $parent_sex M, F or U
     *
     * @return string[] Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
     */
    public function newParentNames(string $child_name, string $parent_sex): array
    {
        if ($parent_sex === 'M' && preg_match(self::REGEX_SURN, $child_name, $match)) {
            return array_filter([
                'NAME' => $this->inflect($match['NAME'], self::INFLECT_MALE),
                'SURN' => $this->inflect($match['SURN'], self::INFLECT_MALE),
            ]);
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
     * @return string[] Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
     */
    public function newSpouseNames(string $spouse_name, string $spouse_sex): array
    {
        if ($spouse_sex === 'F' && preg_match(self::REGEX_SURN, $spouse_name, $match)) {
            return [
                'NAME'   => '//',
                '_MARNM' => $this->inflect($match['NAME'], self::INFLECT_FEMALE),
            ];
        }

        return [
            'NAME' => '//',
        ];
    }
}
