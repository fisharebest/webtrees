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
 * Test harness for the class CensusOfUnitedStates1870
 */
class CensusOfUnitedStates1870Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1870
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1870();

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('JUN 1870', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1870
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1870();
        $columns = $census->columns();

        $this->assertCount(18, $columns);
        $this->assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        $this->assertInstanceOf(CensusColumnAge::class, $columns[1]);
        $this->assertInstanceOf(CensusColumnSexMF::class, $columns[2]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[3]);
        $this->assertInstanceOf(CensusColumnOccupation::class, $columns[4]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[5]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[6]);
        $this->assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[7]);
        $this->assertInstanceOf(CensusColumnFatherForeign::class, $columns[8]);
        $this->assertInstanceOf(CensusColumnMotherForeign::class, $columns[9]);
        $this->assertInstanceOf(CensusColumnMonthIfBornWithinYear::class, $columns[10]);
        $this->assertInstanceOf(CensusColumnMonthIfMarriedWithinYear::class, $columns[11]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[12]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[13]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[14]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[15]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[16]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[17]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('Age', $columns[1]->abbreviation());
        $this->assertSame('Sex', $columns[2]->abbreviation());
        $this->assertSame('Color', $columns[3]->abbreviation());
        $this->assertSame('Occupation', $columns[4]->abbreviation());
        $this->assertSame('RE', $columns[5]->abbreviation());
        $this->assertSame('PE', $columns[6]->abbreviation());
        $this->assertSame('Birthplace', $columns[7]->abbreviation());
        $this->assertSame('FFB', $columns[8]->abbreviation());
        $this->assertSame('MFB', $columns[9]->abbreviation());
        $this->assertSame('Born', $columns[10]->abbreviation());
        $this->assertSame('Mar', $columns[11]->abbreviation());
        $this->assertSame('School', $columns[12]->abbreviation());
        $this->assertSame('Read', $columns[13]->abbreviation());
        $this->assertSame('Write', $columns[14]->abbreviation());
        $this->assertSame('Infirm', $columns[15]->abbreviation());
        $this->assertSame('Cit', $columns[16]->abbreviation());
        $this->assertSame('Dis', $columns[17]->abbreviation());

        $this->assertSame('Name', $columns[0]->title());
        $this->assertSame('Age', $columns[1]->title());
        $this->assertSame('Sex', $columns[2]->title());
        $this->assertSame('White, Black, Mulatto, Chinese, Indian', $columns[3]->title());
        $this->assertSame('Profession, occupation, or trade', $columns[4]->title());
        $this->assertSame('Value of real estate owned', $columns[5]->title());
        $this->assertSame('Value of personal estate owned', $columns[6]->title());
        $this->assertSame('Place of birth, naming the state, territory, or country', $columns[7]->title());
        $this->assertSame('Father of foreign birth', $columns[8]->title());
        $this->assertSame('Mother of foreign birth', $columns[9]->title());
        $this->assertSame('If born within the year, state month', $columns[10]->title());
        $this->assertSame('If married within the year, state month', $columns[11]->title());
        $this->assertSame('Attended school within the year', $columns[12]->title());
        $this->assertSame('Cannot read', $columns[13]->title());
        $this->assertSame('Cannot write', $columns[14]->title());
        $this->assertSame('Whether deaf and dumb, blind, insane, or idiotic', $columns[15]->title());
        $this->assertSame('Male citizen of US', $columns[16]->title());
        $this->assertSame('Male citizen of US, where right to vote is denied or abridged', $columns[17]->title());
    }
}
