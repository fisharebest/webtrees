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

namespace Fisharebest\Webtrees\Census;

/**
 * Definitions for a census
 */
class CensusOfCanada1881 extends CensusOfCanada implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '04 APR 1881';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
            new CensusColumnFullName($this, 'Name', 'Name'),
            new CensusColumnSexMF($this, 'Sex', 'Sex'),
            new CensusColumnAge($this, 'Age', 'Age at last birthday'),
            new CensusColumnNull($this, 'Born', 'Born within last twelve months'),
            new CensusColumnBirthPlaceSimple($this, 'Birth Loc', 'Country or Province of Birth'),
            new CensusColumnNull($this, 'Religion', 'Religion'),
            new CensusColumnNationality($this, 'Origin', 'Origin'),
            new CensusColumnOccupation($this, 'Occupation', 'Profession, Occupation, or Trade'),
            new CensusColumnConditionCanadaMarriedWidowed($this, 'M/W', 'Married or Widowed'),
            new CensusColumnNull($this, 'School', 'Instruction - Going to School'),
            new CensusColumnNull($this, 'Deaf', 'Infirmities - Deaf and Dumb'),
            new CensusColumnNull($this, 'Blind', 'Infirmities - Blind'),
            new CensusColumnNull($this, 'Unsound', 'Infirmities - Unsound Mind'),
            new CensusColumnNull($this, 'Date', 'Dates of Operations and Remarks'),
        ];
    }
}
