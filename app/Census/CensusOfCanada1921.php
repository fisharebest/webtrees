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
class CensusOfCanada1921 extends CensusOfCanada implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '01 JUN 1921';
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
            new CensusColumnNull($this, 'Sec/Twp', 'Place of Abode (Section or Township)'),
            new CensusColumnNull($this, 'Municipality', 'Place of Abode (Municipality)'),
            new CensusColumnNull($this, 'Own/Rent', 'Home owned or rented'),
            new CensusColumnNull($this, 'Rent $', 'If rented, give rent paid per month'),
            new CensusColumnNull($this, 'Home Type', 'Class of houses: Apartment, row or Terrace, Single house, semi-Detached'),
            new CensusColumnNull($this, 'Materials', 'Materials of Construction. Stone, Brick, Wood, Brick Veneered, Plastered with Lime morar, Plastered with Cement mortar, cement blocks or CONcrete'),
            new CensusColumnNull($this, 'Rooms', 'Rooms occupied by this family'),
            new CensusColumnRelationToHeadEnglish($this, 'Relation', 'Relationship to Head of Family or household'),
            new CensusColumnSexMF($this, 'Sex', 'Sex'),
            new CensusColumnConditionCanada($this, 'S/M/W/D/L', 'Single, Married, Widowed, Divorced or Legally Separated'),
            new CensusColumnAge($this, 'Age', 'Age at last birthday - on June 1, 1921'),
            new CensusColumnBirthPlaceSimple($this, 'Birth Loc', 'Place of birth of person'),
            new CensusColumnFatherBirthPlaceSimple($this, 'FBP', 'Place of birth of father'),
            new CensusColumnMotherBirthPlaceSimple($this, 'MBP', 'Place of birth of mother'),
            new CensusColumnNull($this, 'Yr. immigrated', 'Year of immigration to Canada'),
            new CensusColumnNull($this, 'Yr. naturalized', 'Year of naturalization'),
            new CensusColumnNationality($this, 'Nationality', 'Nationality'),
            new CensusColumnNull($this, 'Origin', 'Racial or tribal origin'),
            new CensusColumnNull($this, 'English', 'Can speak English'),
            new CensusColumnNull($this, 'French', 'Can speak French'),
            new CensusColumnNull($this, 'Language', 'Language other than English or French spoken as mother tongue'),
            new CensusColumnNull($this, 'Religion', 'Religious body, Denomination or Community to which this person adheres or belongs
The religion to which an individual claimed to belong written in full'),
            new CensusColumnNull($this, 'Read', 'Can read'),
            new CensusColumnNull($this, 'Write', 'Can write'),
            new CensusColumnNull($this, 'Ms school', 'Months at school since Sept. 1, 1920'),
            new CensusColumnOccupation($this, 'Occupation', 'Chief occupation or trade'),
            new CensusColumnNull($this, 'E/W/OA', 'Employer or employee or Worker, working on Own Account'),
            new CensusColumnNull($this, 'Where employed', '"a" if "Employer" state principal product, "b" if "Employee" state where employed as "Farm", "Cotton Mill", "Foundry", "Grocery", etc. "c" if on "Own account" state nature of work'),
            new CensusColumnNull($this, 'Unemployed', 'If an employee, where you out of work June 1st , 1920'),
            new CensusColumnNull($this, 'Earnings', 'Total earnings past 12 months since June 1, 1920'),
            new CensusColumnNull($this, 'Weeks unemployed', 'Weeks unemployed in the past 12 months since June 1st, 1920'),
        ];
    }
}
