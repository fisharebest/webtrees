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
 * Test harness for the class CensusOfFrance1866
 */
class CensusOfFrance1866Test extends \PHPUnit_Framework_TestCase {
	/**
	 * Test the census place and date
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfFrance1866
	 */
	public function testPlaceAndDate() {
		$census = new CensusOfFrance1866;

		$this->assertSame('France', $census->censusPlace());
		$this->assertSame('1866', $census->censusDate());
	}

	/**
	 * Test the census columns
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfFrance1866
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testColumns() {
		$census  = new CensusOfFrance1866;
		$columns = $census->columns();

		$this->assertCount(5, $columns);
		$this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSurname', $columns[0]);
		$this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnGivenNames', $columns[1]);
		$this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[2]);
		$this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAge', $columns[3]);
		$this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionEnglish', $columns[4]);

		$this->assertSame('XXXX', $columns[0]->abbreviation());
		$this->assertSame('XXXX', $columns[1]->abbreviation());
		$this->assertSame('XXXX', $columns[2]->abbreviation());
		$this->assertSame('XXXX', $columns[3]->abbreviation());
		$this->assertSame('XXXX', $columns[4]->abbreviation());

		$this->assertSame('XXXX', $columns[0]->title());
		$this->assertSame('XXXX', $columns[1]->title());
		$this->assertSame('XXXX', $columns[2]->title());
		$this->assertSame('XXXX', $columns[3]->title());
		$this->assertSame('XXXX', $columns[4]->title());
	}
}
