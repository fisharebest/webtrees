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
 * Test harness for the class CensusOfUnitedStates1810
 */
class CensusOfUnitedStates1810Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1810
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfUnitedStates1810();

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('06 AUG 1810', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1810
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfUnitedStates1810();
        $columns = $census->columns();

        $this->assertCount(14, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFullName', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[13]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('M0-10', $columns[1]->abbreviation());
        $this->assertSame('M10-16', $columns[2]->abbreviation());
        $this->assertSame('M16-26', $columns[3]->abbreviation());
        $this->assertSame('M26-45', $columns[4]->abbreviation());
        $this->assertSame('M45+', $columns[5]->abbreviation());
        $this->assertSame('F0-10', $columns[6]->abbreviation());
        $this->assertSame('F10-16', $columns[7]->abbreviation());
        $this->assertSame('F16-26', $columns[8]->abbreviation());
        $this->assertSame('F26-45', $columns[9]->abbreviation());
        $this->assertSame('F45+', $columns[10]->abbreviation());
        $this->assertSame('Free', $columns[11]->abbreviation());
        $this->assertSame('Slaves', $columns[12]->abbreviation());
        $this->assertSame('Total', $columns[13]->abbreviation());

        $this->assertSame('Name of head of family', $columns[0]->title());
        $this->assertSame('Free white males 0-10 years', $columns[1]->title());
        $this->assertSame('Free white males 10-16 years', $columns[2]->title());
        $this->assertSame('Free white males 16-26 years', $columns[3]->title());
        $this->assertSame('Free white males 26-45 years', $columns[4]->title());
        $this->assertSame('Free white males 45+ years', $columns[5]->title());
        $this->assertSame('Free white females 0-10 years', $columns[6]->title());
        $this->assertSame('Free white females 10-16 years', $columns[7]->title());
        $this->assertSame('Free white females 16-26 years', $columns[8]->title());
        $this->assertSame('Free white females 26-45 years', $columns[9]->title());
        $this->assertSame('Free white females 45+ years', $columns[10]->title());
        $this->assertSame('All other free persons, except Indians not taxed', $columns[11]->title());
        $this->assertSame('Number of slaves', $columns[12]->title());
        $this->assertSame('Total number of individuals', $columns[13]->title());
    }
}
