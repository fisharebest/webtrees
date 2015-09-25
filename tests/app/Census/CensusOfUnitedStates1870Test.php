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
 * Test harness for the class CensusOfUnitedStates1870
 */
class CensusOfUnitedStates1870Test extends \PHPUnit_Framework_TestCase {
	/**
	 * Test the census place and date
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1870
	 */
	public function testPlaceAndDate() {
		$census = new CensusOfUnitedStates1870;

		$this->assertSame('United States', $census->censusPlace());
		$this->assertSame('JUN 1870', $census->censusDate());
	}

	/**
	 * Test the census columns
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1870
	 */
	public function testColumns() {
		$census  = new CensusOfUnitedStates1870;
		$columns = $census->columns();

		$this->assertCount(18, $columns);
		$this->assertInstanceOf(CensusColumnFullName::class, $columns[0]);
		$this->assertInstanceOf(CensusColumnAge::class, $columns[1]);
		$this->assertInstanceOf(CensusColumnSexMF::class, $columns[2]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[3]);
		$this->assertInstanceOf(CensusColumnOccupation::class, $columns[4]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[5]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[6]);
		$this->assertInstanceOf(CensusColumnBirthPlace::class, $columns[7]);
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
	}
}
