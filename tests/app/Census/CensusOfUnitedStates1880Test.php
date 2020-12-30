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
 * Test harness for the class CensusOfUnitedStates1880
 */
class CensusOfUnitedStates1880Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1880
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1880();

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('JUN 1880', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1880
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1880();
        $columns = $census->columns();

        self::assertCount(23, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[2]);
        self::assertInstanceOf(CensusColumnMonthIfBornWithinYear::class, $columns[3]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnMarriedWithinYear::class, $columns[8]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[20]);
        self::assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[21]);
        self::assertInstanceOf(CensusColumnMotherBirthPlaceSimple::class, $columns[22]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Age', $columns[1]->abbreviation());
        self::assertSame('Sex', $columns[2]->abbreviation());
        self::assertSame('Mon', $columns[3]->abbreviation());
        self::assertSame('Relation', $columns[4]->abbreviation());
        self::assertSame('S', $columns[5]->abbreviation());
        self::assertSame('M', $columns[6]->abbreviation());
        self::assertSame('W/D', $columns[7]->abbreviation());
        self::assertSame('MY', $columns[8]->abbreviation());
        self::assertSame('Occupation', $columns[9]->abbreviation());
        self::assertSame('UnEm', $columns[10]->abbreviation());
        self::assertSame('Sick', $columns[11]->abbreviation());
        self::assertSame('Blind', $columns[12]->abbreviation());
        self::assertSame('DD', $columns[13]->abbreviation());
        self::assertSame('Idiotic', $columns[14]->abbreviation());
        self::assertSame('Insane', $columns[15]->abbreviation());
        self::assertSame('Disabled', $columns[16]->abbreviation());
        self::assertSame('School', $columns[17]->abbreviation());
        self::assertSame('Read', $columns[18]->abbreviation());
        self::assertSame('Write', $columns[19]->abbreviation());
        self::assertSame('BP', $columns[20]->abbreviation());
        self::assertSame('FBP', $columns[21]->abbreviation());
        self::assertSame('MBP', $columns[22]->abbreviation());

        self::assertSame('Name', $columns[0]->title());
        self::assertSame('Age', $columns[1]->title());
        self::assertSame('Sex', $columns[2]->title());
        self::assertSame('If born within the year, state month', $columns[3]->title());
        self::assertSame('Relation to head of household', $columns[4]->title());
        self::assertSame('Single', $columns[5]->title());
        self::assertSame('Married', $columns[6]->title());
        self::assertSame('Widowed, Divorced', $columns[7]->title());
        self::assertSame('Married during census year', $columns[8]->title());
        self::assertSame('Profession, occupation, or trade', $columns[9]->title());
        self::assertSame('Number of months the person has been unemployed during the census year', $columns[10]->title());
        self::assertSame('Sickness or disability', $columns[11]->title());
        self::assertSame('Blind', $columns[12]->title());
        self::assertSame('Deaf and dumb', $columns[13]->title());
        self::assertSame('Idiotic', $columns[14]->title());
        self::assertSame('Insane', $columns[15]->title());
        self::assertSame('Maimed, crippled, bedridden or otherwise disabled', $columns[16]->title());
        self::assertSame('Attended school within the census year', $columns[17]->title());
        self::assertSame('Cannot read', $columns[18]->title());
        self::assertSame('Cannot write', $columns[19]->title());
        self::assertSame('Place of birth, naming the state, territory, or country', $columns[20]->title());
        self::assertSame('Place of birth of father, naming the state, territory, or country', $columns[21]->title());
        self::assertSame('Place of birth of mother, naming the state, territory, or country', $columns[22]->title());
    }
}
