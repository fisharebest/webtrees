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
class CensusOfCanada1931 extends CensusOfCanada implements CensusInterface
{
    /**
     * When did this census occur.
     *
* @return string
     */
    public function censusDate(): string
    {
        return '01 JUN 1931';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
new CensusColumnFullName($this, 'Name', 'Name of each person in family, household or institution'),
            new CensusColumnNull($this, 'Place of Abode', '(In rural localities give parish or township. In cities, towns and villages, give street a>
            new CensusColumnNull($this, 'Own/Rent', 'Home owned or rented'),
            new CensusColumnNull($this, 'Value', 'If Owned give value. If rented, give rent paid per month'),
            new CensusColumnNull($this, 'Class', 'Class of House: Apartment, Row or Terrace, Single house, Semi-Detached, Flat'),
            new CensusColumnNull($this, 'Materials', 'Material of Construction: Stone, Brick, Wood, Brick Veneered, Stucco, Cement bricks'),
            new CensusColumnNull($this, 'Rooms', 'Rooms occupied by this family'),
            new CensusColumnNull($this, 'Radio', 'Has this fmily a radio?'),
            new CensusColumnRelationToHeadEnglish($this, 'Relation', 'Relationship to Head of Family or household'),
            new CensusColumnSexMF($this, 'Sex', 'Sex'),
            new CensusColumnConditionCanada($this, 'S/M/W/D', 'Single, Married, Widowed, Divorced'),
            new CensusColumnAge($this, 'Age', 'Age at last birthday'),
            new CensusColumnBirthPlaceSimple($this, 'Birth Loc', 'Place of birth of person'),
            new CensusColumnFatherBirthPlaceSimple($this, 'FBP', 'Place of birth of father'),
            new CensusColumnMotherBirthPlaceSimple($this, 'MBP', 'Place of birth of mother'),
            new CensusColumnNull($this, 'Yr. immigrated', 'Year of immigration to Canada'),
            new CensusColumnNull($this, 'Yr. naturalized', 'Year of naturalization'),
            new CensusColumnNationality($this, 'Nationality', '(Country to which this person owes allegiance.)'),
            new CensusColumnNull($this, 'Origin', 'Racial origin'),
            new CensusColumnNull($this, 'English', 'Can speak English'),
            new CensusColumnNull($this, 'French', 'Can speak French'),
            new CensusColumnNull($this, 'Language', 'Language other than English or French spoken as mother tongue'),
            new CensusColumnNull($this, 'Religion', 'Religious body, Denomination or Community to which this person adheres or belongs'),
            new CensusColumnNull($this, 'Read/Write', 'Can read and write'),
            new CensusColumnNull($this, 'Ms school', 'Months at school since Sept. 1, 1930'),
            new CensusColumnOccupation($this, 'Occupation', 'Trade, profession or particular kind of work, as carpenter, weaver, sawyer, merchant, fa>
            new CensusColumnNull($this, 'Industry', 'Industry or business in which engaged or employed as cotton mill, brass foundry, grocery, coal m>
            new CensusColumnNull($this, 'Class', 'Class of worker'),
            new CensusColumnNull($this, 'Earnings', 'Total earnings in the past twelve months (Since June 1st, 1930)'),
            new CensusColumnNull($this, 'Employed', 'If an employee, where you at work Monday June 1st , 1930'),
            new CensusColumnNull($this, 'WHY', 'If answer to previous question is NO. Why were you not at work on Monday, June 1st, 1931. (For Exampl>
            new CensusColumnNull($this, 'Weeks unemployed', 'Total number of weeks unemployed from any cause in the last 12 months.'),
            new CensusColumnNull($this, 'No Job', 'Of the total numer of weeks reported out of work in column 34, how many were due to-'),
            new CensusColumnNull($this, 'Illness', 'Of the total numer of weeks reported out of work in column 34, how many were due to-'),
            new CensusColumnNull($this, 'Accident', 'Of the total numer of weeks reported out of work in column 34, how many were due to-'),
            new CensusColumnNull($this, 'Strike or Lock-out', 'Of the total numer of weeks reported out of work in column 34, how many were due to-'),
            new CensusColumnNull($this, 'Temporary Lay-off', 'Of the total numer of weeks reported out of work in column 34, how many were due to-'),
            new CensusColumnNull($this, 'Other Causes', 'Of the total numer of weeks reported out of work in column 34, how many were due to-'),
        ];
    }
}
