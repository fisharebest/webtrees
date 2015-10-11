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
 * Test harness for the class CensusOfUnitedStates
 */
class CensusOfUnitedStatesTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test the census place
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates
	 */
	public function testPlace() {
		$census = new CensusOfUnitedStates;

		$this->assertSame('United States', $census->censusPlace());
	}

	/**
	 * Test the census dates
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates
	 */
	public function testAllDates() {
		$census  = new CensusOfUnitedStates;

		$census_dates = $census->allCensusDates();

		$this->assertCount(10, $census_dates);
		$this->assertInstanceOf(CensusOfUnitedStates1850::class, $census_dates[0]);
		$this->assertInstanceOf(CensusOfUnitedStates1860::class, $census_dates[1]);
		$this->assertInstanceOf(CensusOfUnitedStates1870::class, $census_dates[2]);
		$this->assertInstanceOf(CensusOfUnitedStates1880::class, $census_dates[3]);
		$this->assertInstanceOf(CensusOfUnitedStates1890::class, $census_dates[4]);
		$this->assertInstanceOf(CensusOfUnitedStates1900::class, $census_dates[5]);
		$this->assertInstanceOf(CensusOfUnitedStates1910::class, $census_dates[6]);
		$this->assertInstanceOf(CensusOfUnitedStates1920::class, $census_dates[7]);
		$this->assertInstanceOf(CensusOfUnitedStates1930::class, $census_dates[8]);
		$this->assertInstanceOf(CensusOfUnitedStates1940::class, $census_dates[9]);
	}
}
