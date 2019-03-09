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
namespace Fisharebest\Webtrees\Census;

/**
 * Test harness for the class CensusOfDenmark1801
 */
class CensusOfDenmark1801Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDenmark1801
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfDenmark1801();

        $this->assertSame('Danmark', $census->censusPlace());
        $this->assertSame('01 FEB 1801', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfDenmark1801
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfDenmark1801();
        $columns = $census->columns();

        $this->assertCount(5, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFullName', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnRelationToHead', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAge', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionDanish', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[4]);

        $this->assertSame('Navn', $columns[0]->abbreviation());
        $this->assertSame('Stilling i familien', $columns[1]->abbreviation());
        $this->assertSame('Alder', $columns[2]->abbreviation());
        $this->assertSame('Civilstand', $columns[3]->abbreviation());
        $this->assertSame('Erhverv', $columns[4]->abbreviation());

        $this->assertSame('', $columns[0]->title());
        $this->assertSame('', $columns[1]->title());
        $this->assertSame('', $columns[2]->title());
        $this->assertSame('', $columns[3]->title());
        $this->assertSame('', $columns[4]->title());
    }
}
