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
 * Test harness for the class CensusOfFrance1911
 */
class CensusOfFrance1911Test extends \PHPUnit_Framework_TestCase {
	/**
	 * Test the census place and date
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfFrance1911
	 */
	public function testPlaceAndDate() {
		$census = new CensusOfFrance1911;

		$this->assertSame('France', $census->censusPlace());
		$this->assertSame('1911', $census->censusDate());
	}

	/**
	 * Test the census columns
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfFrance1911
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testColumns() {
		$census  = new CensusOfFrance1911;
		$columns = $census->columns();

		$this->assertCount(1, $columns);
		$this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[0]);

		$this->assertSame('XXXX', $columns[0]->abbreviation());

		$this->assertSame('XXXX', $columns[0]->title());
	}
}
