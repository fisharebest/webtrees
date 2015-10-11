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
 * Test harness for the class CensusOfEngland
 */
class CensusOfEnglandTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test the census place
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfEngland
	 */
	public function testPlace() {
		$census = new CensusOfEngland;

		$this->assertSame('England', $census->censusPlace());
	}

	/**
	 * Test the census dates
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfEngland
	 */
	public function testAllDates() {
		$census  = new CensusOfEngland;

		$census_dates = $census->allCensusDates();

		$this->assertCount(8, $census_dates);
		$this->assertInstanceOf(CensusOfEngland1841::class, $census_dates[0]);
		$this->assertInstanceOf(CensusOfEngland1851::class, $census_dates[1]);
		$this->assertInstanceOf(CensusOfEngland1861::class, $census_dates[2]);
		$this->assertInstanceOf(CensusOfEngland1871::class, $census_dates[3]);
		$this->assertInstanceOf(CensusOfEngland1881::class, $census_dates[4]);
		$this->assertInstanceOf(CensusOfEngland1891::class, $census_dates[5]);
		$this->assertInstanceOf(CensusOfEngland1901::class, $census_dates[6]);
		$this->assertInstanceOf(CensusOfEngland1911::class, $census_dates[7]);
	}
}
