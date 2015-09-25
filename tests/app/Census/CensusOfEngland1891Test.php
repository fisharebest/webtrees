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
 * Test harness for the class CensusOfEngland1891
 */
class CensusOfEngland1891Test extends \PHPUnit_Framework_TestCase {
	/**
	 * Test the census place and date
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfEngland1891
	 */
	public function testPlaceAndDate() {
		$census = new CensusOfEngland1891;

		$this->assertSame('England', $census->censusPlace());
		$this->assertSame('05 MAR 1891', $census->censusDate());
	}

	/**
	 * Test the census columns
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfEngland1891
	 */
	public function testColumns() {
		$census  = new CensusOfEngland1891;
		$columns = $census->columns();

		$this->assertCount(11, $columns);
		$this->assertInstanceOf(CensusColumnFullName::class, $columns[0]);
		$this->assertInstanceOf(CensusColumnRelationToHead::class, $columns[1]);
		$this->assertInstanceOf(CensusColumnCondition::class, $columns[2]);
		$this->assertInstanceOf(CensusColumnAgeMale::class, $columns[3]);
		$this->assertInstanceOf(CensusColumnAgeFemale::class, $columns[4]);
		$this->assertInstanceOf(CensusColumnOccupation::class, $columns[5]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[6]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[7]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[8]);
		$this->assertInstanceOf(CensusColumnBirthPlace::class, $columns[9]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[10]);
	}
}
