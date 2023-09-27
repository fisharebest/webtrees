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

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class CensusOfRhodeIsland1905
 */
class CensusOfRhodeIsland1905Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfRhodeIsland1905
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfRhodeIsland1905();

        self::assertSame('Rhode Island, United States', $census->censusPlace());
        self::assertSame('JUN 1905', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfRhodeIsland1905
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfRhodeIsland1905();
        $columns = $census->columns();
        self::assertCount(28, $columns);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[0]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSurnameGivenNameInitial::class, $columns[2]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[5]);
        self::assertInstanceOf(CensusColumnConditionUs::class, $columns[6]);
        self::assertInstanceOf(CensusColumnBirthYear::class, $columns[7]);
        self::assertInstanceOf(CensusColumnBirthMonthDay::class, $columns[8]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[17]);
        self::assertInstanceOf(CensusColumnMotherBirthPlaceSimple::class, $columns[18]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[19]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[20]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[21]);
        self::assertInstanceOf(CensusColumnReligion::class, $columns[22]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[23]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[24]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[25]);
        self::assertInstanceOf(CensusColumnChildrenBornAlive::class, $columns[26]);
        self::assertInstanceOf(CensusColumnChildrenLiving::class, $columns[27]);

        self::assertSame('Sex', $columns[0]->abbreviation());
        self::assertSame('Num', $columns[1]->abbreviation());
        self::assertSame('Name', $columns[2]->abbreviation());
        self::assertSame('Relation', $columns[3]->abbreviation());
        self::assertSame('Race', $columns[4]->abbreviation());
        self::assertSame('Age', $columns[5]->abbreviation());
        self::assertSame('Cond', $columns[6]->abbreviation());
        self::assertSame('Year', $columns[7]->abbreviation());
        self::assertSame('Month Day', $columns[8]->abbreviation());
        self::assertSame('BP', $columns[9]->abbreviation());
        self::assertSame('N/F', $columns[10]->abbreviation());
        self::assertSame('R', $columns[11]->abbreviation());
        self::assertSame('W', $columns[12]->abbreviation());
        self::assertSame('Imm', $columns[13]->abbreviation());
        self::assertSame('US yrs', $columns[14]->abbreviation());
        self::assertSame('RI yrs', $columns[15]->abbreviation());
        self::assertSame('Town mnths', $columns[16]->abbreviation());
        self::assertSame('FBP', $columns[17]->abbreviation());
        self::assertSame('MBP', $columns[18]->abbreviation());
        self::assertSame('Occupation', $columns[19]->abbreviation());
        self::assertSame('Unemp', $columns[20]->abbreviation());
        self::assertSame('Pen', $columns[21]->abbreviation());
        self::assertSame('Rel', $columns[22]->abbreviation());
        self::assertSame('Mil', $columns[23]->abbreviation());
        self::assertSame('Nat', $columns[24]->abbreviation());
        self::assertSame('Vtr', $columns[25]->abbreviation());
        self::assertSame('Chil born', $columns[26]->abbreviation());
        self::assertSame('Chil liv', $columns[27]->abbreviation());

        self::assertSame('Sex', $columns[0]->title());
        self::assertSame('Number of people in the family', $columns[1]->title());
        self::assertSame('Name', $columns[2]->title());
        self::assertSame('Relationship to head of household', $columns[3]->title());
        self::assertSame('Color or race', $columns[4]->title());
        self::assertSame('Age at last birthday', $columns[5]->title());
        self::assertSame('Congugal Condition', $columns[6]->title());
        self::assertSame('Year of Birth', $columns[7]->title());
        self::assertSame('Month Day', $columns[8]->title());
        self::assertSame('Place of birth', $columns[9]->title());
        self::assertSame('Native or Foreign Born', $columns[10]->title());
        self::assertSame('Read', $columns[11]->title());
        self::assertSame('Write', $columns[12]->title());
        self::assertSame('Year of immigration to the United States', $columns[13]->title());
        self::assertSame('Years in US', $columns[14]->title());
        self::assertSame('Years resident of Rhode Island', $columns[15]->title());
        self::assertSame('Months in current year as a Rhode Island resident', $columns[16]->title());
        self::assertSame('Place of birth of father of this person', $columns[17]->title());
        self::assertSame('Place of birth of mother of this person', $columns[18]->title());
        self::assertSame('Occupation', $columns[19]->title());
        self::assertSame('Months unemployed during Census Year', $columns[20]->title());
        self::assertSame('Did you receive a pension', $columns[21]->title());
        self::assertSame('Religious preference', $columns[22]->title());
        self::assertSame('Military or widow of military', $columns[23]->title());
        self::assertSame('Naturalization information', $columns[24]->title());
        self::assertSame('Voter information', $columns[25]->title());
        self::assertSame('Mother of how many children', $columns[26]->title());
        self::assertSame('Number of these children living on June 1 1905', $columns[27]->title());
    }
}
