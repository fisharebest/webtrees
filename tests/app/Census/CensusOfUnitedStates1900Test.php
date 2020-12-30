<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class CensusOfUnitedStates1900
 */
class CensusOfUnitedStates1900Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1900
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1900();

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('01 JUN 1900', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1900
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1900();
        $columns = $census->columns();

        self::assertCount(26, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[1]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[2]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[3]);
        self::assertInstanceOf(CensusColumnBirthMonth::class, $columns[4]);
        self::assertInstanceOf(CensusColumnBirthYear::class, $columns[5]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[6]);
        self::assertInstanceOf(CensusColumnConditionUs::class, $columns[7]);
        self::assertInstanceOf(CensusColumnYearsMarried::class, $columns[8]);
        self::assertInstanceOf(CensusColumnChildrenBornAlive::class, $columns[9]);
        self::assertInstanceOf(CensusColumnChildrenLiving::class, $columns[10]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[11]);
        self::assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[12]);
        self::assertInstanceOf(CensusColumnMotherBirthPlaceSimple::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[20]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[21]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[22]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[23]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[24]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[25]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Relation', $columns[1]->abbreviation());
        self::assertSame('Race', $columns[2]->abbreviation());
        self::assertSame('Sex', $columns[3]->abbreviation());
        self::assertSame('Month', $columns[4]->abbreviation());
        self::assertSame('Year', $columns[5]->abbreviation());
        self::assertSame('Age', $columns[6]->abbreviation());
        self::assertSame('Cond', $columns[7]->abbreviation());
        self::assertSame('Marr', $columns[8]->abbreviation());
        self::assertSame('Chil', $columns[9]->abbreviation());
        self::assertSame('Chil', $columns[10]->abbreviation());
        self::assertSame('BP', $columns[11]->abbreviation());
        self::assertSame('FBP', $columns[12]->abbreviation());
        self::assertSame('MBP', $columns[13]->abbreviation());
        self::assertSame('Imm', $columns[14]->abbreviation());
        self::assertSame('US', $columns[15]->abbreviation());
        self::assertSame('Nat', $columns[16]->abbreviation());
        self::assertSame('Occupation', $columns[17]->abbreviation());
        self::assertSame('Unemp', $columns[18]->abbreviation());
        self::assertSame('School', $columns[19]->abbreviation());
        self::assertSame('Read', $columns[20]->abbreviation());
        self::assertSame('Write', $columns[21]->abbreviation());
        self::assertSame('Eng', $columns[22]->abbreviation());
        self::assertSame('Home', $columns[23]->abbreviation());
        self::assertSame('Mort', $columns[24]->abbreviation());
        self::assertSame('Farm', $columns[25]->abbreviation());

        self::assertSame('Name', $columns[0]->title());
        self::assertSame('Relationship of each person to the head of the family', $columns[1]->title());
        self::assertSame('Color or race', $columns[2]->title());
        self::assertSame('Sex', $columns[3]->title());
        self::assertSame('Month of birth', $columns[4]->title());
        self::assertSame('Year of birth', $columns[5]->title());
        self::assertSame('Age at last birthday', $columns[6]->title());
        self::assertSame('Whether single, married, widowed, or divorced', $columns[7]->title());
        self::assertSame('Number of years married', $columns[8]->title());
        self::assertSame('Mother of how many children', $columns[9]->title());
        self::assertSame('Number of these children living', $columns[10]->title());
        self::assertSame('Place of birth of this person', $columns[11]->title());
        self::assertSame('Place of birth of father of this person', $columns[12]->title());
        self::assertSame('Place of birth of mother of this person', $columns[13]->title());
        self::assertSame('Year of immigration to the United States', $columns[14]->title());
        self::assertSame('Number of years in the United States', $columns[15]->title());
        self::assertSame('Naturalization', $columns[16]->title());
        self::assertSame('Occupation, trade of profession', $columns[17]->title());
        self::assertSame('Months not unemployed', $columns[18]->title());
        self::assertSame('Attended school (in months)', $columns[19]->title());
        self::assertSame('Can read', $columns[20]->title());
        self::assertSame('Can write', $columns[21]->title());
        self::assertSame('Can speak English', $columns[22]->title());
        self::assertSame('Owned or rented', $columns[23]->title());
        self::assertSame('Owned free or mortgaged', $columns[24]->title());
        self::assertSame('Farm or house', $columns[25]->title());
    }
}
