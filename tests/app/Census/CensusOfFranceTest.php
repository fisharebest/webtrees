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
 * Test harness for the class CensusOfFrance
 */
class CensusOfFranceTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test the census place
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfFrance
	 */
	public function testPlace() {
		$census = new CensusOfFrance;

		$this->assertSame('France', $census->censusPlace());
	}

	/**
	 * Test the census dates
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfFrance
	 */
	public function testAllDates() {
		$census  = new CensusOfFrance;

		$census_dates = $census->allCensusDates();

		$this->assertCount(16, $census_dates);
		$this->assertInstanceOf(CensusOfFrance1836::class, $census_dates[0]);
		$this->assertInstanceOf(CensusOfFrance1841::class, $census_dates[1]);
		$this->assertInstanceOf(CensusOfFrance1846::class, $census_dates[2]);
		$this->assertInstanceOf(CensusOfFrance1851::class, $census_dates[3]);
		$this->assertInstanceOf(CensusOfFrance1856::class, $census_dates[4]);
		$this->assertInstanceOf(CensusOfFrance1861::class, $census_dates[5]);
		$this->assertInstanceOf(CensusOfFrance1866::class, $census_dates[6]);
		$this->assertInstanceOf(CensusOfFrance1872::class, $census_dates[7]);
		$this->assertInstanceOf(CensusOfFrance1876::class, $census_dates[8]);
		$this->assertInstanceOf(CensusOfFrance1881::class, $census_dates[9]);
		$this->assertInstanceOf(CensusOfFrance1886::class, $census_dates[10]);
		$this->assertInstanceOf(CensusOfFrance1891::class, $census_dates[11]);
		$this->assertInstanceOf(CensusOfFrance1896::class, $census_dates[12]);
		$this->assertInstanceOf(CensusOfFrance1901::class, $census_dates[13]);
		$this->assertInstanceOf(CensusOfFrance1906::class, $census_dates[14]);
		$this->assertInstanceOf(CensusOfFrance1911::class, $census_dates[15]);
	}
}
