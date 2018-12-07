<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
namespace Fisharebest\Webtrees\Census;

/**
 * Test harness for the class CensusOfUnitedStates1790
 */
class CensusOfUnitedStates1790Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1790
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfUnitedStates1790();

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('02 AUG 1790', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1790
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfUnitedStates1790();
        $columns = $census->columns();

        $this->assertCount(8, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFullName', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[7]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('Occupation', $columns[1]->abbreviation());
        $this->assertSame('White male 16+', $columns[2]->abbreviation());
        $this->assertSame('White male 0-16', $columns[3]->abbreviation());
        $this->assertSame('White female', $columns[4]->abbreviation());
        $this->assertSame('Free', $columns[5]->abbreviation());
        $this->assertSame('Slaves', $columns[6]->abbreviation());
        $this->assertSame('Total', $columns[7]->abbreviation());

        $this->assertSame('Name of head of family', $columns[0]->title());
        $this->assertSame('Professions and occupation', $columns[1]->title());
        $this->assertSame('White male of 16 yrs upward', $columns[2]->title());
        $this->assertSame('White males of under 16 yrs', $columns[3]->title());
        $this->assertSame('All White Females', $columns[4]->title());
        $this->assertSame('All other free persons', $columns[5]->title());
        $this->assertSame('Number of slaves', $columns[6]->title());
        $this->assertSame('Total', $columns[7]->title());
    }
}
