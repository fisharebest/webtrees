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
 * Test harness for the class CensusOfEngland1841
 */
class CensusOfEngland1841Test extends \PHPUnit_Framework_TestCase {
	/**
	 * Test the census place and date
	 */
	public function testPlaceAndDate() {
		$census = new CensusOfEngland1841;

		$this->assertSame('England', $census->censusPlace());
		$this->assertSame('06 MAY 1841', $census->censusDate());
	}

	/**
	 * Test the census place and date
	 */
	public function testColumns() {
		$census  = new CensusOfEngland1841;
		$columns = $census->columns();

		$this->assertCount(6, $columns);
		$this->assertInstanceOf(CensusColumnFullName::class, $columns[0]);
		$this->assertInstanceOf(CensusColumnAgeMale5Years::class, $columns[1]);
		$this->assertInstanceOf(CensusColumnAgeFemale5Years::class, $columns[2]);
		$this->assertInstanceOf(CensusColumnOccupation::class, $columns[3]);
		$this->assertInstanceOf(CensusColumnBornSameCounty::class, $columns[4]);
		$this->assertInstanceOf(CensusColumnBornForeignParts::class, $columns[5]);
	}
}
