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
 * Test harness for the class CensusOfUnitedStates1860
 */
class CensusOfUnitedStates1860Test extends \PHPUnit_Framework_TestCase {
	/**
	 * Test the census place and date
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1860
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testPlaceAndDate() {
		$census = new CensusOfUnitedStates1860;

		$this->assertSame('United States', $census->censusPlace());
		$this->assertSame('BET JUN 1860 AND OCT 1860', $census->censusDate());
	}

	/**
	 * Test the census columns
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1860
	 */
	public function testColumns() {
		$census  = new CensusOfUnitedStates1860;
		$columns = $census->columns();

		$this->assertCount(12, $columns);
		$this->assertInstanceOf(CensusColumnFullName::class, $columns[0]);
		$this->assertInstanceOf(CensusColumnAge::class, $columns[1]);
		$this->assertInstanceOf(CensusColumnSexMF::class, $columns[2]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[3]);
		$this->assertInstanceOf(CensusColumnOccupation::class, $columns[4]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[5]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[6]);
		$this->assertInstanceOf(CensusColumnBirthPlace::class, $columns[7]);
		$this->assertInstanceOf(CensusColumnMarriedWithinYear::class, $columns[8]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[9]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[10]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[11]);

		$this->assertSame('', $columns[0]->abbreviation());
		$this->assertSame('', $columns[1]->abbreviation());
		$this->assertSame('', $columns[2]->abbreviation());
		$this->assertSame('', $columns[3]->abbreviation());
		$this->assertSame('', $columns[4]->abbreviation());
		$this->assertSame('', $columns[5]->abbreviation());
		$this->assertSame('', $columns[6]->abbreviation());
		$this->assertSame('', $columns[7]->abbreviation());
		$this->assertSame('', $columns[8]->abbreviation());
		$this->assertSame('', $columns[9]->abbreviation());
		$this->assertSame('', $columns[10]->abbreviation());
		$this->assertSame('', $columns[11]->abbreviation());

		$this->assertSame('', $columns[0]->title());
		$this->assertSame('', $columns[1]->title());
		$this->assertSame('', $columns[2]->title());
		$this->assertSame('', $columns[3]->title());
		$this->assertSame('', $columns[4]->title());
		$this->assertSame('', $columns[5]->title());
		$this->assertSame('', $columns[6]->title());
		$this->assertSame('', $columns[7]->title());
		$this->assertSame('', $columns[8]->title());
		$this->assertSame('', $columns[9]->title());
		$this->assertSame('', $columns[10]->title());
		$this->assertSame('', $columns[11]->title());
	}
}
