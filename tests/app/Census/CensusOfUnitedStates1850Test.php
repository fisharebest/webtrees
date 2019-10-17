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
 * Test harness for the class CensusOfUnitedStates1850
 */
class CensusOfUnitedStates1850Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1850
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1850();

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('01 JUN 1850', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1850
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1850();
        $columns = $census->columns();

        $this->assertCount(11, $columns);
        $this->assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        $this->assertInstanceOf(CensusColumnAge::class, $columns[1]);
        $this->assertInstanceOf(CensusColumnSexMF::class, $columns[2]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[3]);
        $this->assertInstanceOf(CensusColumnOccupation::class, $columns[4]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[5]);
        $this->assertInstanceOf(CensusColumnBirthPlace::class, $columns[6]);
        $this->assertInstanceOf(CensusColumnMarriedWithinYear::class, $columns[7]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[8]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[9]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[10]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('Age', $columns[1]->abbreviation());
        $this->assertSame('Sex', $columns[2]->abbreviation());
        $this->assertSame('Color', $columns[3]->abbreviation());
        $this->assertSame('Occupation', $columns[4]->abbreviation());
        $this->assertSame('RE', $columns[5]->abbreviation());
        $this->assertSame('Birthplace', $columns[6]->abbreviation());
        $this->assertSame('Mar', $columns[7]->abbreviation());
        $this->assertSame('School', $columns[8]->abbreviation());
        $this->assertSame('R+W', $columns[9]->abbreviation());
        $this->assertSame('Infirm', $columns[10]->abbreviation());

        $this->assertSame('Name', $columns[0]->title());
        $this->assertSame('Age', $columns[1]->title());
        $this->assertSame('Sex', $columns[2]->title());
        $this->assertSame('White, black, or mulatto', $columns[3]->title());
        $this->assertSame('Profession, occupation, or trade', $columns[4]->title());
        $this->assertSame('Value of real estate owned', $columns[5]->title());
        $this->assertSame('Place of birth, naming the state, territory, or country', $columns[6]->title());
        $this->assertSame('Married within the year', $columns[7]->title());
        $this->assertSame('Attended school within the year', $columns[8]->title());
        $this->assertSame('Persons over 20 years of age who cannot read and write', $columns[9]->title());
        $this->assertSame('Whether deaf and dumb, blind, insane, idiotic, pauper or convict', $columns[10]->title());
    }
}
