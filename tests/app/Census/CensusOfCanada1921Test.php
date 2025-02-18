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

/**
 * @covers \Fisharebest\Webtrees\Census\CensusOfCanada1921
 */
class CensusOfCanada1921Test extends TestCase
{
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfCanada1921();

        self::assertSame('Canada', $census->censusPlace());
        self::assertSame('01 JUN 1921', $census->censusDate());
    }

    public function testColumns(): void
    {
        $census  = new CensusOfCanada1921();
        $columns = $census->columns();

        self::assertCount(32, $columns);
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
        self::assertInstanceOf(CensusColumnNull::class, $columns[25]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[26]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[27]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[28]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[29]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[30]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[31]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Sec/Twp', $columns[1]->abbreviation());
        self::assertSame('Municipality', $columns[2]->abbreviation());
        self::assertSame('Own/Rent', $columns[3]->abbreviation());
        self::assertSame('Rent $', $columns[4]->abbreviation());
        self::assertSame('Home Type', $columns[5]->abbreviation());
        self::assertSame('Materials', $columns[6]->abbreviation());
        self::assertSame('Rooms', $columns[7]->abbreviation());
        self::assertSame('Relation', $columns[8]->abbreviation());
        self::assertSame('Sex', $columns[9]->abbreviation());
        self::assertSame('S/M/W/D/L', $columns[10]->abbreviation());
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
        self::assertSame('Read', $columns[23]->abbreviation());
        self::assertSame('Write', $columns[24]->abbreviation());
        self::assertSame('Ms school', $columns[25]->abbreviation());
        self::assertSame('Occupation', $columns[26]->abbreviation());
        self::assertSame('E/W/OA', $columns[27]->abbreviation());
        self::assertSame('Where employed', $columns[28]->abbreviation());
        self::assertSame('Unemployed', $columns[29]->abbreviation());
        self::assertSame('Earnings', $columns[30]->abbreviation());
        self::assertSame('Weeks unemployed', $columns[31]->abbreviation());

        self::assertSame('Name of each person in family, household or institution', $columns[0]->title());
        self::assertSame('Place of Abode (Section or Township)', $columns[1]->title());
        self::assertSame('Place of Abode (Municipality)', $columns[2]->title());
        self::assertSame('Home owned or rented', $columns[3]->title());
        self::assertSame('If rented, give rent paid per month', $columns[4]->title());
        self::assertSame('Class of houses: Apartment, row or Terrace, Single house, semi-Detached', $columns[5]->title());
        self::assertSame('Materials of Construction. Stone, Brick, Wood, Brick Veneered, Plastered with Lime morar, Plastered with Cement mortar, cement blocks or CONcrete', $columns[6]->title());
        self::assertSame('Rooms occupied by this family', $columns[7]->title());
        self::assertSame('Relationship to Head of Family or household', $columns[8]->title());
        self::assertSame('Sex', $columns[9]->title());
        self::assertSame('Single, Married, Widowed, Divorced or Legally Separated', $columns[10]->title());
        self::assertSame('Age at last birthday - on June 1, 1921', $columns[11]->title());
        self::assertSame('Place of birth of person', $columns[12]->title());
        self::assertSame('Place of birth of father', $columns[13]->title());
        self::assertSame('Place of birth of mother', $columns[14]->title());
        self::assertSame('Year of immigration to Canada', $columns[15]->title());
        self::assertSame('Year of naturalization', $columns[16]->title());
        self::assertSame('Nationality', $columns[17]->title());
        self::assertSame('Racial or tribal origin', $columns[18]->title());
        self::assertSame('Can speak English', $columns[19]->title());
        self::assertSame('Can speak French', $columns[20]->title());
        self::assertSame('Language other than English or French spoken as mother tongue', $columns[21]->title());
        self::assertSame('Religious body, Denomination or Community to which this person adheres or belongs
The religion to which an individual claimed to belong written in full', $columns[22]->title());
        self::assertSame('Can read', $columns[23]->title());
        self::assertSame('Can write', $columns[24]->title());
        self::assertSame('Months at school since Sept. 1, 1920', $columns[25]->title());
        self::assertSame('Chief occupation or trade', $columns[26]->title());
        self::assertSame('Employer or employee or Worker, working on Own Account', $columns[27]->title());
        self::assertSame('"a" if "Employer" state principal product, "b" if "Employee" state where employed as "Farm", "Cotton Mill", "Foundry", "Grocery", etc. "c" if on "Own account" state nature of work', $columns[28]->title());
        self::assertSame('If an employee, where you out of work June 1st , 1920', $columns[29]->title());
        self::assertSame('Total earnings past 12 months since June 1, 1920', $columns[30]->title());
        self::assertSame('Weeks unemployed in the past 12 months since June 1st, 1920', $columns[31]->title());
    }
}
