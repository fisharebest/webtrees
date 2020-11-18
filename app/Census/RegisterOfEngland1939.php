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

namespace Fisharebest\Webtrees\Census;

/**
 * Definitions for a census
 */
class RegisterOfEngland1939 extends CensusOfEngland implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '29 SEP 1939';
    }

    /**
     * The columns of the census.
     *
     * @return CensusColumnInterface[]
     */
    public function columns(): array
    {
        return [
            new CensusColumnNull($this, 'Schedule', 'Schedule Number'),
            new CensusColumnNull($this, 'SubNum', 'Schedule Sub Number'),
            new CensusColumnSurnameGivenNames($this, 'Name', 'Surname & other names'),
            new CensusColumnNull($this, 'Role', 'For institutions only – for example, Officer, Visitor, Servant, Patient, Inmate'),
            new CensusColumnSexMF($this, 'Sex', 'Male or Female'),
            new CensusColumnBirthDayMonthYear($this, 'DOB', 'Date of birth'),
            new CensusColumnConditionEnglish($this, 'MC', 'Marital Condition - Married, Single, Unmarried, Widowed or Divorced'),
            new CensusColumnOccupation($this, 'Occupation', 'Occupation'),
        ];
    }
}
