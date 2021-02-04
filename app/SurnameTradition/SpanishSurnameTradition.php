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
 * Children take one surname from the father and one surname from the mother.
 *
 * Mother: Maria /AAAA/ /BBBB/
 * Father: Jose  /CCCC/ /DDDD/
 * Child:  Pablo /CCCC/ /AAAA/
 */
class SpanishSurnameTradition extends DefaultSurnameTradition
{
    /**
     * What names are given to a new child
     *
     * @param string $father_name A GEDCOM NAME
     * @param string $mother_name A GEDCOM NAME
     * @param string $child_sex   M, F or U
     *
     * @return array<string,string> Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
     */
    public function newChildNames(string $father_name, string $mother_name, string $child_sex): array
    {
        if (preg_match(self::REGEX_SURNS, $father_name, $match_father)) {
            $father_surname = $match_father['SURN1'];
        } else {
            $father_surname = '';
        }

        if (preg_match(self::REGEX_SURNS, $mother_name, $match_mother)) {
            $mother_surname = $match_mother['SURN1'];
        } else {
            $mother_surname = '';
        }

        return [
            'NAME' => '/' . $father_surname . '/ /' . $mother_surname . '/',
            'SURN' => trim($father_surname . ',' . $mother_surname, ','),
        ];
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
        if (preg_match(self::REGEX_SURNS, $child_name, $match)) {
            switch ($parent_sex) {
                case 'M':
                    return [
                        'NAME' => '/' . $match['SURN1'] . '/ //',
                        'SURN' => $match['SURN1'],
                    ];
                case 'F':
                    return [
                        'NAME' => '/' . $match['SURN2'] . '/ //',
                        'SURN' => $match['SURN2'],
                    ];
            }
        }

        return [
            'NAME' => '// //',
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
        return [
            'NAME' => '// //',
        ];
    }
}
