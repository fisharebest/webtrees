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
 * Test harness for the class CensusOfEngland1841
 */
class CensusOfEngland1841Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfEngland1841
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfEngland1841();

        $this->assertSame('England', $census->censusPlace());
        $this->assertSame('06 JUN 1841', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfEngland1841
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfEngland1841();
        $columns = $census->columns();

        $this->assertCount(6, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFullName', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAgeMale5Years', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAgeFemale5Years', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBornForeignParts', $columns[5]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('AgeM', $columns[1]->abbreviation());
        $this->assertSame('AgeF', $columns[2]->abbreviation());
        $this->assertSame('Occupation', $columns[3]->abbreviation());
        $this->assertSame('BiC', $columns[4]->abbreviation());
        $this->assertSame('SIF', $columns[5]->abbreviation());

        $this->assertSame('Name', $columns[0]->title());
        $this->assertSame('Age (males)', $columns[1]->title());
        $this->assertSame('Age (females)', $columns[2]->title());
        $this->assertSame('Profession, trade, employment or of independent means', $columns[3]->title());
        $this->assertSame('Born in same county', $columns[4]->title());
        $this->assertSame('Born in Scotland, Ireland or foreign parts', $columns[5]->title());
    }
}
