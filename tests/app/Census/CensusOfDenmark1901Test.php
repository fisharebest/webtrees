<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
 * Test harness for the class CensusOfDenmark1901
 */
class CensusOfDenmark1901Test extends \PHPUnit_Framework_TestCase {
	/**
	 * Test the census place and date
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfDenmark1901
	 */
	public function testPlaceAndDate() {
		$census = new CensusOfDenmark1901;

		$this->assertSame('Danmark', $census->censusPlace());
		$this->assertSame('01 FEB 1901', $census->censusDate());
	}

	/**
	 * Test the census columns
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfDenmark1901
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testColumns() {
		$census  = new CensusOfDenmark1901;
		$columns = $census->columns();

		$this->assertCount(10, $columns);
		$this->assertInstanceOf(CensusColumnFullName::class, $columns[0]);
		$this->assertInstanceOf(CensusColumnDateOfBirth::class, $columns[1]);
		$this->assertInstanceOf(CensusColumnSexMF::class, $columns[2]);
		$this->assertInstanceOf(CensusColumnConditionEnglish::class, $columns[3]);
		$this->assertInstanceOf(CensusColumnRelationToHead::class, $columns[4]);
		$this->assertInstanceOf(CensusColumnOccupation::class, $columns[5]);
		$this->assertInstanceOf(CensusColumnBirthPlace::class, $columns[6]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[7]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[8]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[9]);

		$this->assertSame('TBC', $columns[0]->abbreviation());
		$this->assertSame('TBC', $columns[1]->abbreviation());
		$this->assertSame('TBC', $columns[2]->abbreviation());
		$this->assertSame('TBC', $columns[3]->abbreviation());
		$this->assertSame('TBC', $columns[4]->abbreviation());
		$this->assertSame('TBC', $columns[5]->abbreviation());
		$this->assertSame('TBC', $columns[6]->abbreviation());
		$this->assertSame('TBC', $columns[7]->abbreviation());
		$this->assertSame('TBC', $columns[8]->abbreviation());
		$this->assertSame('TBC', $columns[9]->abbreviation());

		$this->assertSame('To be confirmed', $columns[0]->title());
		$this->assertSame('To be confirmed', $columns[1]->title());
		$this->assertSame('To be confirmed', $columns[2]->title());
		$this->assertSame('To be confirmed', $columns[3]->title());
		$this->assertSame('To be confirmed', $columns[4]->title());
		$this->assertSame('To be confirmed', $columns[5]->title());
		$this->assertSame('To be confirmed', $columns[6]->title());
		$this->assertSame('To be confirmed', $columns[7]->title());
		$this->assertSame('To be confirmed', $columns[8]->title());
		$this->assertSame('To be confirmed', $columns[9]->title());
	}
}
