<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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

use Fisharebest\Webtrees\Date;
use Mockery;

/**
 * Test harness for the class CensusColumnAgeMarried
 */
class CensusColumnAgeMarriedTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Delete mock objects
	 */
	public function tearDown() {
		Mockery::close();
	}

	/**
	 * @covers Fisharebest\Webtrees\Census\CensusColumnAgeMarried
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testAgeMarried() {
		$fact = Mockery::mock('Fisharebest\Webtrees\Fact');
		$fact->shouldReceive('getDate')->andReturn(new Date('01 DEC 1859'));

		$family = Mockery::mock('Fisharebest\Webtrees\Family');
		$family->shouldReceive('getFacts')->with('MARR', true)->andReturn(array($fact));

		$individual = Mockery::mock('Fisharebest\Webtrees\Individual');
		$individual->shouldReceive('getBirthDate')->andReturn(new Date('15 MAR 1840'));
		$individual->shouldReceive('getSpouseFamilies')->andReturn(array($family));

		$census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
		$census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

		$column = new CensusColumnAgeMarried($census, '', '');

		$this->assertSame(19, $column->generate($individual));
	}

	/**
	 * @covers Fisharebest\Webtrees\Census\CensusColumnAgeMarried
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testNoBirthDate() {
	}

	/**
	 * @covers Fisharebest\Webtrees\Census\CensusColumnAgeMarried
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testNoMarriage() {
		$family = Mockery::mock('Fisharebest\Webtrees\Family');
		$family->shouldReceive('getFacts')->with('MARR')->andReturn(array());

		$individual = Mockery::mock('Fisharebest\Webtrees\Individual');
		$individual->shouldReceive('getBirthDate')->andReturn(new Date(''));
		$individual->shouldReceive('getSpouseFamilies')->andReturn(array($family));

		$census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
		$census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

		$column = new CensusColumnAgeMarried($census, '', '');

		$this->assertSame('', $column->generate($individual));
	}

	/**
	 * @covers Fisharebest\Webtrees\Census\CensusColumnAgeMarried
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testNoSpouseFamily() {
		$individual = Mockery::mock('Fisharebest\Webtrees\Individual');
		$individual->shouldReceive('getBirthDate')->andReturn(new Date('15 MAR 1840'));
		$individual->shouldReceive('getSpouseFamilies')->andReturn(array());

		$census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
		$census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

		$column = new CensusColumnAgeMarried($census, '', '');

		$this->assertSame('', $column->generate($individual));
	}
}
