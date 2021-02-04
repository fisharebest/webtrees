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
 * Various cultures have different traditions for the use of surnames within families.
 * By providing defaults for new individuals, we can speed up data entry and reduce errors.
 */
interface SurnameTraditionInterface
{
    /**
     * Does this surname tradition change surname at marriage?
     *
     * @return bool
     */
    public function hasMarriedNames(): bool;

    /**
     * Does this surname tradition use surnames?
     *
     * @return bool
     */
    public function hasSurnames(): bool;

    /**
     * What names are given to a new child
     *
     * @param string $father_name A GEDCOM NAME
     * @param string $mother_name A GEDCOM NAME
     * @param string $child_sex   M, F or U
     *
     * @return array<string,string> Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
     */
    public function newChildNames(string $father_name, string $mother_name, string $child_sex): array;

    /**
     * What names are given to a new parent
     *
     * @param string $child_name A GEDCOM NAME
     * @param string $parent_sex M, F or U
     *
     * @return array<string,string> Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
     */
    public function newParentNames(string $child_name, string $parent_sex): array;

    /**
     * What names are given to a new spouse
     *
     * @param string $spouse_name A GEDCOM NAME
     * @param string $spouse_sex  M, F or U
     *
     * @return array<string,string> Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
     */
    public function newSpouseNames(string $spouse_name, string $spouse_sex): array;
}
