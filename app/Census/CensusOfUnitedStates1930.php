<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
class CensusOfUnitedStates1930 extends CensusOfUnitedStates implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return 'APR 1930';
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
            new CensusColumnRelationToHeadEnglish($this, 'Relation', 'Relationship of each person to the head of the family'),
            new CensusColumnNull($this, 'Home', 'Home owned or rented'),
            new CensusColumnNull($this, 'V/R', 'Value of house, if owned, or monthly rental if rented'),
            new CensusColumnNull($this, 'Radio', 'Radio set'),
            new CensusColumnNull($this, 'Farm', 'Does this family live on a farm'),
            new CensusColumnSexMF($this, 'Sex', 'Sex'),
            new CensusColumnNull($this, 'Race', 'Color or race'),
            new CensusColumnAge($this, 'Age', 'Age at last birthday'),
            new CensusColumnConditionUs($this, 'Cond', 'Whether single, married, widowed, or divorced'),
            new CensusColumnAgeMarried($this, 'AM', 'Age at first marriage'),
            new CensusColumnNull($this, 'School', 'Attended school since Sept. 1, 1929'),
            new CensusColumnNull($this, 'R/W', 'Whether able to read and write'),
            new CensusColumnBirthPlaceSimple($this, 'BP', 'Place of birth'),
            new CensusColumnFatherBirthPlaceSimple($this, 'FBP', 'Place of birth of father'),
            new CensusColumnMotherBirthPlaceSimple($this, 'MBP', 'Place of birth of mother'),
            new CensusColumnNull($this, 'Lang', 'Language spoken in home before coming to the United States'),
            new CensusColumnNull($this, 'Imm', 'Year of immigration to the United States'),
            new CensusColumnNull($this, 'Nat', 'Naturalization'),
            new CensusColumnNull($this, 'Eng', 'Whether able to speak English'),
            new CensusColumnOccupation($this, 'Occupation', 'Trade, profession, or particular kind of work done'),
            new CensusColumnNull($this, 'Industry', 'Industry, business of establishment in which at work'),
            new CensusColumnNull($this, 'Code', 'Industry code'),
            new CensusColumnNull($this, 'Emp', 'Class of worker'),
            new CensusColumnNull($this, 'Work', 'Whether normally at work yesterday or the last regular working day'),
            new CensusColumnNull($this, 'Unemp', 'If not, …'),
            new CensusColumnNull($this, 'Vet', 'Whether a veteran of U.S. military or …'),
            new CensusColumnNull($this, 'War', 'What war or …'),
        ];
    }
}
