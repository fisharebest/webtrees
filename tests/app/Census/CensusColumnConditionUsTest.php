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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Mockery;

/**
 * Test harness for the class CensusColumnConditionUs
 */
class CensusColumnConditionUsTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Delete mock objects
	 */
	public function tearDown() {
		Mockery::close();
	}

	/**
	 * @covers Fisharebest\Webtrees\Census\CensusColumnConditionUs
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testNoSpouseFamilies() {
		$individual = Mockery::mock('Fisharebest\Webtrees\Individual');
		$individual->shouldReceive('getSpouseFamilies')->andReturn(array());

		$census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');

		$column = new CensusColumnConditionUs($census, '', '');

		$this->assertSame('S', $column->generate($individual));
	}

	/**
	 * @covers Fisharebest\Webtrees\Census\CensusColumnConditionUs
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testNoFamilyNoFacts() {
		$family = Mockery::mock('Fisharebest\Webtrees\Family');
		$family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
		$family->shouldReceive('getFacts')->andReturn(array());

		$individual = Mockery::mock('Fisharebest\Webtrees\Individual');
		$individual->shouldReceive('getSpouseFamilies')->andReturn(array($family));

		$census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');

		$column = new CensusColumnConditionUs($census, '', '');
		$census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

		$this->assertSame('M', $column->generate($individual));
	}

	/**
	 * @covers Fisharebest\Webtrees\Census\CensusColumnConditionUs
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testNoFamilyUnmarried() {
		$fact = Mockery::mock('Fisharebest\Webtrees\Fact');

		$family = Mockery::mock('Fisharebest\Webtrees\Family');
		$family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
		$family->shouldReceive('getFacts')->with('_NMR')->andReturn(array($fact));

		$individual = Mockery::mock('Fisharebest\Webtrees\Individual');
		$individual->shouldReceive('getSpouseFamilies')->andReturn(array($family));

		$census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');

		$column = new CensusColumnConditionUs($census, '', '');
		$census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

		$this->assertSame('S', $column->generate($individual));
	}

	/**
	 * @covers Fisharebest\Webtrees\Census\CensusColumnConditionUs
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testNoFamilyDivorced() {
		$fact = Mockery::mock('Fisharebest\Webtrees\Fact');

		$family = Mockery::mock('Fisharebest\Webtrees\Family');
		$family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
		$family->shouldReceive('getFacts')->with('_NMR')->andReturn(array());
		$family->shouldReceive('getFacts')->with('DIV')->andReturn(array($fact));

		$individual = Mockery::mock('Fisharebest\Webtrees\Individual');
		$individual->shouldReceive('getSpouseFamilies')->andReturn(array($family));

		$census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');

		$column = new CensusColumnConditionUs($census, '', '');
		$census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

		$this->assertSame('D', $column->generate($individual));
	}
}
