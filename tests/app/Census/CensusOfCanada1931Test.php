<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusOfCanada1931::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfCanada1931Test extends TestCase
{
    /**
     * Test the census place and date
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfCanada1931();

        self::assertSame('Canada', $census->censusPlace());
        self::assertSame('01 JUN 1931', $census->censusDate());
    }

    /**
     * Test the census columns
     */
    public function testColumns(): void
    {
        $census  = new CensusOfCanada1931();
        $columns = $census->columns();

        self::assertCount(38, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[1]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[8]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[9]);
        self::assertInstanceOf(CensusColumnConditionCanada::class, $columns[10]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[11]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[12]);
        self::assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[13]);
        self::assertInstanceOf(CensusColumnMotherBirthPlaceSimple::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNationality::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[20]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[21]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[22]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[23]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[24]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[25]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[26]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[27]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[28]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[29]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[30]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[31]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[32]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[33]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[34]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[35]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[36]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[37]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Place of Abode', $columns[1]->abbreviation());
        self::assertSame('Own/Rent', $columns[2]->abbreviation());
        self::assertSame('Value', $columns[3]->abbreviation());
        self::assertSame('Class', $columns[4]->abbreviation());
        self::assertSame('Materials', $columns[5]->abbreviation());
        self::assertSame('Rooms', $columns[6]->abbreviation());
        self::assertSame('Radio', $columns[7]->abbreviation());
        self::assertSame('Relation', $columns[8]->abbreviation());
        self::assertSame('Sex', $columns[9]->abbreviation());
        self::assertSame('S/M/W/D', $columns[10]->abbreviation());
        self::assertSame('Age', $columns[11]->abbreviation());
        self::assertSame('Birth Loc', $columns[12]->abbreviation());
        self::assertSame('FBP', $columns[13]->abbreviation());
        self::assertSame('MBP', $columns[14]->abbreviation());
        self::assertSame('Yr. immigrated', $columns[15]->abbreviation());
        self::assertSame('Yr. naturalized', $columns[16]->abbreviation());
        self::assertSame('Nationality', $columns[17]->abbreviation());
        self::assertSame('Origin', $columns[18]->abbreviation());
        self::assertSame('English', $columns[19]->abbreviation());
        self::assertSame('French', $columns[20]->abbreviation());
        self::assertSame('Language', $columns[21]->abbreviation());
        self::assertSame('Religion', $columns[22]->abbreviation());
        self::assertSame('Read/Write', $columns[23]->abbreviation());
        self::assertSame('Ms school', $columns[24]->abbreviation());
        self::assertSame('Occupation', $columns[25]->abbreviation());
        self::assertSame('Industry', $columns[26]->abbreviation());
        self::assertSame('Class', $columns[27]->abbreviation());
        self::assertSame('Earnings', $columns[28]->abbreviation());
        self::assertSame('Employed', $columns[29]->abbreviation());
        self::assertSame('WHY', $columns[30]->abbreviation());
        self::assertSame('Weeks unemployed', $columns[31]->abbreviation());
        self::assertSame('No Job', $columns[32]->abbreviation());
        self::assertSame('Illness', $columns[33]->abbreviation());
        self::assertSame('Accident', $columns[34]->abbreviation());
        self::assertSame('Strike or Lock-out', $columns[35]->abbreviation());
        self::assertSame('Temporary Lay-off', $columns[36]->abbreviation());
        self::assertSame('Other Causes', $columns[37]->abbreviation());

        self::assertSame('Name of each person in family, household or institution', $columns[0]->title());
        self::assertSame('In rural localities give parish or township. In cities, towns and villages, give street and number of dwelling', $columns[1]->title());
        self::assertSame('Home owned or rented', $columns[2]->title());
        self::assertSame('If Owned give value. If rented, give rent paid per month', $columns[3]->title());
        self::assertSame('Class of House: Apartment, Row or Terrace, Single house, Semi-Detached, Flat', $columns[4]->title());
        self::assertSame('Material of Construction: Stone, Brick, Wood, Brick Veneered, Stucco, Cement bricks', $columns[5]->title());
        self::assertSame('Rooms occupied by this family', $columns[6]->title());
        self::assertSame('Has this family a radio?', $columns[7]->title());
        self::assertSame('Relationship to Head of Family or household', $columns[8]->title());
        self::assertSame('Sex', $columns[9]->title());
        self::assertSame('Single, Married, Widowed, Divorced', $columns[10]->title());
        self::assertSame('Age at last birthday', $columns[11]->title());
        self::assertSame('Place of birth of person', $columns[12]->title());
        self::assertSame('Place of birth of father', $columns[13]->title());
        self::assertSame('Place of birth of mother', $columns[14]->title());
        self::assertSame('Year of immigration to Canada', $columns[15]->title());
        self::assertSame('Year of naturalization', $columns[16]->title());
        self::assertSame('Country to which this person owes allegiance', $columns[17]->title());
        self::assertSame('Racial origin', $columns[18]->title());
        self::assertSame('Can speak English', $columns[19]->title());
        self::assertSame('Can speak French', $columns[20]->title());
        self::assertSame('Language other than English or French spoken as mother tongue', $columns[21]->title());
        self::assertSame('Religious body, Denomination or Community to which this person adheres or belongs', $columns[22]->title());
        self::assertSame('Can read and write', $columns[23]->title());
        self::assertSame('Months at school since Sept. 1, 1930', $columns[24]->title());
        self::assertSame('Trade, profession or particular kind of work, as carpenter, weaver, sawyer, merchant, farmer,salesman, teacher, etc. (Give as definite and precise information as possible)', $columns[25]->title());
        self::assertSame('Industry or business in which engaged or employed as cotton mill, brass foundry, grocery, coal mine, dairy farm, public school, business college, etc', $columns[26]->title());
        self::assertSame('Class of worker', $columns[27]->title());
        self::assertSame('Total earnings in the past twelve months (Since June 1st, 1930)', $columns[28]->title());
        self::assertSame('If an employee, where you at work Monday June 1st, 1930', $columns[29]->title());
        self::assertSame('If answer to previous question is NO. Why were you not at work on Monday, June 1st, 1931. (For Example, no job, sick, accident, on holidays, strike or lock-out, plant closed, no materials, etc)', $columns[30]->title());
        self::assertSame('Total number of weeks unemployed from any cause in the last 12 months', $columns[31]->title());
        self::assertSame('Of the total numer of weeks reported out of work, how many were due to-', $columns[32]->title());
        self::assertSame('Of the total numer of weeks reported out of work, how many were due to-', $columns[33]->title());
        self::assertSame('Of the total numer of weeks reported out of work, how many were due to-', $columns[34]->title());
        self::assertSame('Of the total numer of weeks reported out of work, how many were due to-', $columns[35]->title());
        self::assertSame('Of the total numer of weeks reported out of work, how many were due to-', $columns[36]->title());
        self::assertSame('Of the total numer of weeks reported out of work, how many were due to-', $columns[37]->title());
    }
}
