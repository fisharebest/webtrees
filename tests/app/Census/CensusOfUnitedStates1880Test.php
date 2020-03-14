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

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('JUN 1880', $census->censusDate());
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

        $this->assertCount(23, $columns);
        $this->assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        $this->assertInstanceOf(CensusColumnAge::class, $columns[1]);
        $this->assertInstanceOf(CensusColumnSexMF::class, $columns[2]);
        $this->assertInstanceOf(CensusColumnMonthIfBornWithinYear::class, $columns[3]);
        $this->assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[4]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[5]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[6]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[7]);
        $this->assertInstanceOf(CensusColumnMarriedWithinYear::class, $columns[8]);
        $this->assertInstanceOf(CensusColumnOccupation::class, $columns[9]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[10]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[11]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[12]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[13]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[14]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[15]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[16]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[17]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[18]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[19]);
        $this->assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[20]);
        $this->assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[21]);
        $this->assertInstanceOf(CensusColumnMotherBirthPlaceSimple::class, $columns[22]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('Age', $columns[1]->abbreviation());
        $this->assertSame('Sex', $columns[2]->abbreviation());
        $this->assertSame('Mon', $columns[3]->abbreviation());
        $this->assertSame('Relation', $columns[4]->abbreviation());
        $this->assertSame('S', $columns[5]->abbreviation());
        $this->assertSame('M', $columns[6]->abbreviation());
        $this->assertSame('W/D', $columns[7]->abbreviation());
        $this->assertSame('MY', $columns[8]->abbreviation());
        $this->assertSame('Occupation', $columns[9]->abbreviation());
        $this->assertSame('UnEm', $columns[10]->abbreviation());
        $this->assertSame('Sick', $columns[11]->abbreviation());
        $this->assertSame('Blind', $columns[12]->abbreviation());
        $this->assertSame('DD', $columns[13]->abbreviation());
        $this->assertSame('Idiotic', $columns[14]->abbreviation());
        $this->assertSame('Insane', $columns[15]->abbreviation());
        $this->assertSame('Disabled', $columns[16]->abbreviation());
        $this->assertSame('School', $columns[17]->abbreviation());
        $this->assertSame('Read', $columns[18]->abbreviation());
        $this->assertSame('Write', $columns[19]->abbreviation());
        $this->assertSame('BP', $columns[20]->abbreviation());
        $this->assertSame('FBP', $columns[21]->abbreviation());
        $this->assertSame('MBP', $columns[22]->abbreviation());

        $this->assertSame('Name', $columns[0]->title());
        $this->assertSame('Age', $columns[1]->title());
        $this->assertSame('Sex', $columns[2]->title());
        $this->assertSame('If born within the year, state month', $columns[3]->title());
        $this->assertSame('Relation to head of household', $columns[4]->title());
        $this->assertSame('Single', $columns[5]->title());
        $this->assertSame('Married', $columns[6]->title());
        $this->assertSame('Widowed, Divorced', $columns[7]->title());
        $this->assertSame('Married during census year', $columns[8]->title());
        $this->assertSame('Profession, occupation, or trade', $columns[9]->title());
        $this->assertSame('Number of months the person has been unemployed during the census year', $columns[10]->title());
        $this->assertSame('Sickness or disability', $columns[11]->title());
        $this->assertSame('Blind', $columns[12]->title());
        $this->assertSame('Deaf and dumb', $columns[13]->title());
        $this->assertSame('Idiotic', $columns[14]->title());
        $this->assertSame('Insane', $columns[15]->title());
        $this->assertSame('Maimed, crippled, bedridden or otherwise disabled', $columns[16]->title());
        $this->assertSame('Attended school within the census year', $columns[17]->title());
        $this->assertSame('Cannot read', $columns[18]->title());
        $this->assertSame('Cannot write', $columns[19]->title());
        $this->assertSame('Place of birth, naming the state, territory, or country', $columns[20]->title());
        $this->assertSame('Place of birth of father, naming the state, territory, or country', $columns[21]->title());
        $this->assertSame('Place of birth of mother, naming the state, territory, or country', $columns[22]->title());
    }
}
