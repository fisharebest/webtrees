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
 * Test harness for the class CensusOfFrance1876
 */
class CensusOfFrance1876Test extends \PHPUnit_Framework_TestCase {
	/**
	 * Test the census place and date
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfFrance1876
	 */
	public function testPlaceAndDate() {
		$census = new CensusOfFrance1876;

		$this->assertSame('France', $census->censusPlace());
		$this->assertSame('1876', $census->censusDate());
	}

	/**
	 * Test the census columns
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfFrance1876
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testColumns() {
		$census  = new CensusOfFrance1876;
		$columns = $census->columns();

		$this->assertCount(7, $columns);
		$this->assertInstanceOf(CensusColumnSurname::class, $columns[0]);
		$this->assertInstanceOf(CensusColumnGivenNames::class, $columns[1]);
		$this->assertInstanceOf(CensusColumnOccupation::class, $columns[2]);
		$this->assertInstanceOf(CensusColumnAge::class, $columns[3]);
		$this->assertInstanceOf(CensusColumnConditionEnglish::class, $columns[4]);
		$this->assertInstanceOf(CensusColumnNationality::class, $columns[5]);
		$this->assertInstanceOf(CensusColumnBirthPlace::class, $columns[6]);

		$this->assertSame('XXXX', $columns[0]->abbreviation());
		$this->assertSame('XXXX', $columns[1]->abbreviation());
		$this->assertSame('XXXX', $columns[2]->abbreviation());
		$this->assertSame('XXXX', $columns[3]->abbreviation());
		$this->assertSame('XXXX', $columns[4]->abbreviation());
		$this->assertSame('XXXX', $columns[5]->abbreviation());
		$this->assertSame('XXXX', $columns[6]->abbreviation());

		$this->assertSame('XXXX', $columns[0]->title());
		$this->assertSame('XXXX', $columns[1]->title());
		$this->assertSame('XXXX', $columns[2]->title());
		$this->assertSame('XXXX', $columns[3]->title());
		$this->assertSame('XXXX', $columns[4]->title());
		$this->assertSame('XXXX', $columns[5]->title());
		$this->assertSame('XXXX', $columns[6]->title());
	}
}
