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
 * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1910
 */
class CensusOfUnitedStates1910Test extends TestCase
{
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1910();

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('15 APR 1910', $census->censusDate());
    }

    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1910();
        $columns = $census->columns();

        self::assertCount(29, $columns);
        self::assertInstanceOf(CensusColumnSurnameGivenNameInitial::class, $columns[0]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[4]);
        self::assertInstanceOf(CensusColumnConditionUs::class, $columns[5]);
        self::assertInstanceOf(CensusColumnYearsMarried::class, $columns[6]);
        self::assertInstanceOf(CensusColumnChildrenBornAlive::class, $columns[7]);
        self::assertInstanceOf(CensusColumnChildrenLiving::class, $columns[8]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[9]);
        self::assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[10]);
        self::assertInstanceOf(CensusColumnMotherBirthPlaceSimple::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[20]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[21]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[22]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[23]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[24]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[25]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[26]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[27]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[28]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Relation', $columns[1]->abbreviation());
        self::assertSame('Sex', $columns[2]->abbreviation());
        self::assertSame('Race', $columns[3]->abbreviation());
        self::assertSame('Age', $columns[4]->abbreviation());
        self::assertSame('Cond', $columns[5]->abbreviation());
        self::assertSame('Marr', $columns[6]->abbreviation());
        self::assertSame('Chil', $columns[7]->abbreviation());
        self::assertSame('Chil', $columns[8]->abbreviation());
        self::assertSame('BP', $columns[9]->abbreviation());
        self::assertSame('FBP', $columns[10]->abbreviation());
        self::assertSame('MBP', $columns[11]->abbreviation());
        self::assertSame('Imm', $columns[12]->abbreviation());
        self::assertSame('Nat', $columns[13]->abbreviation());
        self::assertSame('Lang', $columns[14]->abbreviation());
        self::assertSame('Occupation', $columns[15]->abbreviation());
        self::assertSame('Ind', $columns[16]->abbreviation());
        self::assertSame('Emp', $columns[17]->abbreviation());
        self::assertSame('Unemp', $columns[18]->abbreviation());
        self::assertSame('Unemp', $columns[19]->abbreviation());
        self::assertSame('R', $columns[20]->abbreviation());
        self::assertSame('W', $columns[21]->abbreviation());
        self::assertSame('Sch', $columns[22]->abbreviation());
        self::assertSame('Home', $columns[23]->abbreviation());
        self::assertSame('Mort', $columns[24]->abbreviation());
        self::assertSame('Farm', $columns[25]->abbreviation());
        self::assertSame('CW', $columns[26]->abbreviation());
        self::assertSame('Blind', $columns[27]->abbreviation());
        self::assertSame('Deaf', $columns[28]->abbreviation());

        self::assertSame('Name', $columns[0]->title());
        self::assertSame('Relationship of each person to the head of the family', $columns[1]->title());
        self::assertSame('Sex', $columns[2]->title());
        self::assertSame('Color or race', $columns[3]->title());
        self::assertSame('Age at last birthday', $columns[4]->title());
        self::assertSame('Whether single, married, widowed, or divorced', $columns[5]->title());
        self::assertSame('Number of years of present marriage', $columns[6]->title());
        self::assertSame('Mother of how many children', $columns[7]->title());
        self::assertSame('Number of these children living', $columns[8]->title());
        self::assertSame('Place of birth of this person', $columns[9]->title());
        self::assertSame('Place of birth of father of this person', $columns[10]->title());
        self::assertSame('Place of birth of mother of this person', $columns[11]->title());
        self::assertSame('Year of immigration to the United States', $columns[12]->title());
        self::assertSame('Whether naturalized or alien', $columns[13]->title());
        self::assertSame('Whether able to speak English, of if not, give language spoken', $columns[14]->title());
        self::assertSame('Trade or profession of, or particular kind of work done by this person', $columns[15]->title());
        self::assertSame('General nature of industry', $columns[16]->title());
        self::assertSame('Whether an employer, employee, or work on own account', $columns[17]->title());
        self::assertSame('Whether out of work on April 15, 1910', $columns[18]->title());
        self::assertSame('Number of weeks out of work in 1909', $columns[19]->title());
        self::assertSame('Whether able to read', $columns[20]->title());
        self::assertSame('Whether able to write', $columns[21]->title());
        self::assertSame('Attended school since September 1, 1909', $columns[22]->title());
        self::assertSame('Owned or rented', $columns[23]->title());
        self::assertSame('Owned free or mortgaged', $columns[24]->title());
        self::assertSame('Farm or house', $columns[25]->title());
        self::assertSame('Whether a survivor of the Union or Confederate Army or Navy', $columns[26]->title());
        self::assertSame('Whether blind (both eyes)', $columns[27]->title());
        self::assertSame('Whether deaf and dumb', $columns[28]->title());
    }
}
