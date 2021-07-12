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
class CensusOfCanada1911 extends CensusOfCanada implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '01 JUN 1911';
    }

    /**
     * The columns of the census.
     *
     * @return CensusColumnInterface[]
     */
    public function columns(): array
    {
        return [
            new CensusColumnFullName($this, 'Name', 'Name of each person in family, household or institution'),
            new CensusColumnNull($this, 'Address', 'Place of Habitation'),
            new CensusColumnSexMF($this, 'Sex', 'Sex'),
            new CensusColumnRelationToHeadEnglish($this, 'Relation', 'Relationship to Head of Family or household'),
            new CensusColumnConditionCanada($this, 'S/M/W/D/L', 'Single, Married, Widowed, Divorced or Legally Separated'),
            new CensusColumnBirthMonth($this, 'Month', 'Month of birth'),
            new CensusColumnBirthYear($this, 'Year', 'Year of birth'),
            new CensusColumnAge($this, 'Age', 'Age at last birthday - on June 1, 1911'),
            new CensusColumnBirthPlaceSimple($this, 'Birth Loc', 'Country or Place of Birth'),
            new CensusColumnNull($this, 'Yr. immigrated', 'Year of immigration to Canada, if an immigrant'),
            new CensusColumnNull($this, 'Yr. naturalized', 'Year of naturalization, if formerly an alien'),
            new CensusColumnNull($this, 'Origin', 'Racial or tribal origin'),
            new CensusColumnNationality($this, 'Nationality', 'Nationality'),
            new CensusColumnNull($this, 'Religion', 'Religion'),
            new CensusColumnOccupation($this, 'Occupation', 'Chief occupation or trade'),
            new CensusColumnNull($this, 'Means', 'Living on own means'),
            new CensusColumnNull($this, 'Employer', 'Employer'),
            new CensusColumnNull($this, 'Employee', 'Employee'),
            new CensusColumnNull($this, 'Work on OwnAcct', 'Working on own account'),
            new CensusColumnNull($this, 'Where employed', 'State where person is employed, as "on Farm," "in Woolen Mill," "at Foundry Shop," "in Drug Store," etc.'),
            new CensusColumnNull($this, 'Weeks employed', 'Weeks employed in 1910 at chief occupation or trade'),
            new CensusColumnNull($this, 'Weeks other', 'Weeks employed in 1910 at other than chief occupation or trade, if any'),
            new CensusColumnNull($this, 'Hrs worked', 'Hours of working time per week at chief occupation'),
            new CensusColumnNull($this, 'Hrs at other', 'Hours of working time per week at other occupation, if any'),
            new CensusColumnNull($this, 'Earned 1910 $', 'Total earnings in 1910 from chief occupation or trade'),
            new CensusColumnNull($this, 'Earned at other 1910 $', 'Total earnings in 1910 from other than chief occupation or trade, if any'),
            new CensusColumnNull($this, 'Rate hr-cents', 'Rate of earnings per hour when employed by the hour-cents'),
            new CensusColumnNull($this, 'Life Ins $', 'Upon life $, as of June 1, 1911'),
            new CensusColumnNull($this, 'Accident/sick Ins $', 'Insurance $ Against accident or sickness, as of June 1, 1911'),
            new CensusColumnNull($this, 'Ins Cost $', 'Cost of insurance in census year $'),
            new CensusColumnNull($this, 'Ms school', 'Months at school in 1910 for individuals aged 5-21 years'),
            new CensusColumnNull($this, 'Read', 'Can read'),
            new CensusColumnNull($this, 'Write', 'Can write'),
            new CensusColumnNull($this, 'Language', 'Language commonly spoken, E and/or F'),
            new CensusColumnNull($this, 'Edu cost', 'Cost of education in 1910 for persons over 16 Years of age'),
        ];
    }
}
