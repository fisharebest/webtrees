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
class CensusOfCanada1901 extends CensusOfCanada implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '31 MAR 1901';
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
            new CensusColumnNull($this, 'Color', 'Colour'),
            new CensusColumnRelationToHeadEnglish($this, 'Relation', 'Relationship to Head of Family'),
            new CensusColumnConditionCanada($this, 'S/M/W/D', 'Single, married, widowed or divorced'),
            new CensusColumnBirthMonthDay($this, 'M/D', 'Month and date of birth'),
            new CensusColumnBirthYear($this, 'Year', 'Year of birth'),
            new CensusColumnAge($this, 'Age', 'Age at last birthday'),
            new CensusColumnBirthPlaceSimple($this, 'Birth Loc', 'Country or Place of Birth, plus Rural or Urban'),
            new CensusColumnNull($this, 'Origin', 'Racial or Tribal origin'),
            new CensusColumnNationality($this, 'Nationality', 'Nationality'),
            new CensusColumnNull($this, 'Religion', 'Religion'),
            new CensusColumnOccupation($this, 'Occupation', 'Profession, Occupation or Trade'),
            new CensusColumnNull($this, 'Retired', 'Living on own means'),
            new CensusColumnNull($this, 'Employer', 'Employer'),
            new CensusColumnNull($this, 'Employee', 'Employee'),
            new CensusColumnNull($this, 'Work on own', 'Working on own account'),
            new CensusColumnNull($this, 'Trade', 'Working at trade in factory or home'),
            new CensusColumnNull($this, 'Ms Fac', 'Months employed at trade in factory'),
            new CensusColumnNull($this, 'Ms Home', 'Months employed at trade in home'),
            new CensusColumnNull($this, 'Ms Other', 'Months employed in other than trade in factory or home'),
            new CensusColumnNull($this, 'Earnings', 'Earnings from occupation or trade $'),
            new CensusColumnNull($this, 'Extra Earn', 'Extra earnings (from other than occupation or trade) $'),
            new CensusColumnNull($this, 'Edu Month', 'Months at school in year'),
            new CensusColumnNull($this, 'Read', 'Can Read'),
            new CensusColumnNull($this, 'Write', 'Can Write'),
            new CensusColumnNull($this, 'English', 'Speaks English'),
            new CensusColumnNull($this, 'French', 'Speaks French'),
            new CensusColumnNull($this, 'Mother toungue', 'Mother toungue'),
            new CensusColumnNull($this, 'Infirmities', 'Infirmities - a. Deaf and dumb, b. Blind, c. Unsound mind'),
        ];
    }
}
