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
 * Children take their mother’s surname.
 */
class MatrilinealSurnameTradition extends DefaultSurnameTradition
{
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
        if (preg_match(self::REGEX_SPFX_SURN, $mother_name, $match)) {
            return array_filter([
                'NAME' => $match['NAME'],
                'SPFX' => $match['SPFX'],
                'SURN' => $match['SURN'],
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
        if ($parent_sex === 'F' && preg_match(self::REGEX_SPFX_SURN, $child_name, $match)) {
            return array_filter([
                'NAME' => $match['NAME'],
                'SPFX' => $match['SPFX'],
                'SURN' => $match['SURN'],
            ]);
        }

        return [
            'NAME' => '//',
        ];
    }
}
