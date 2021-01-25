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
class CensusOfUnitedStates1920 extends CensusOfUnitedStates implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return 'JAN 1920';
    }

    /**
     * The columns of the census.
     *
     * @return CensusColumnInterface[]
     */
    public function columns(): array
    {
        return [
            new CensusColumnSurnameGivenNameInitial($this, 'Name', 'Name'),
            new CensusColumnRelationToHeadEnglish($this, 'Relation', 'Relationship of each person to the head of the family'),
            new CensusColumnNull($this, 'Home', 'Owned or rented'),
            new CensusColumnNull($this, 'Mort', 'If owned, free or mortgaged'),
            new CensusColumnSexMF($this, 'Sex', 'Sex'),
            new CensusColumnNull($this, 'Race', 'Color or race'),
            new CensusColumnAge($this, 'Age', 'Age at last birthday'),
            new CensusColumnConditionUs($this, 'Condition', 'Whether single, married, widowed, or divorced'),
            new CensusColumnNull($this, 'Imm', 'Year of immigration to the United States'),
            new CensusColumnNull($this, 'Nat', 'Naturalized or alien'),
            new CensusColumnNull($this, 'NatY', 'If naturalized, year of naturalization'),
            new CensusColumnNull($this, 'School', 'Attended school since Sept. 1, 1919'),
            new CensusColumnNull($this, 'R', 'Whether able to read'),
            new CensusColumnNull($this, 'W', 'Whether able to write'),
            new CensusColumnBirthPlaceSimple($this, 'BP', 'Place of birth'),
            new CensusColumnNull($this, 'Lang', 'Mother tongue'),
            new CensusColumnFatherBirthPlaceSimple($this, 'FBP', 'Place of birth of father'),
            new CensusColumnNull($this, 'Father lang', 'Mother tongue of father'),
            new CensusColumnFatherBirthPlaceSimple($this, 'MBP', 'Place of birth of mother'),
            new CensusColumnNull($this, 'Mother lang', 'Mother tongue of mother'),
            new CensusColumnNull($this, 'Eng', 'Whether able to speak English'),
            new CensusColumnOccupation($this, 'Occupation', 'Trade, profession, or particular kind of work done'),
            new CensusColumnNull($this, 'Ind', 'Industry, business of establishment in which at work'),
            new CensusColumnNull($this, 'Emp', 'Employer, salary or wage worker, or work on own account'),
        ];
    }
}
