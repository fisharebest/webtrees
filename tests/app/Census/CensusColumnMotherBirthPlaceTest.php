<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Mockery;

/**
 * Test harness for the class CensusColumnMotherBirthPlace
 */
class CensusColumnMotherBirthPlaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Delete mock objects
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnMotherBirthPlace
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testSameCountry()
    {
        $mother = Mockery::mock('Fisharebest\Webtrees\Individual');
        $mother->shouldReceive('getBirthPlace')->andReturn('London, England');

        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getWife')->andReturn($mother);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getPrimaryChildFamily')->andReturn($family);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnMotherBirthPlace($census, '', '');

        $this->assertSame('London', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnMotherBirthPlace
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testDifferentCountry()
    {
        $mother = Mockery::mock('Fisharebest\Webtrees\Individual');
        $mother->shouldReceive('getBirthPlace')->andReturn('London, England');

        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getWife')->andReturn($mother);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getPrimaryChildFamily')->andReturn($family);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Ireland');

        $column = new CensusColumnMotherBirthPlace($census, '', '');

        $this->assertSame('London, England', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnMotherBirthPlace
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testPlaceNoParent()
    {
        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getWife')->andReturn(null);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getPrimaryChildFamily')->andReturn($family);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnMotherBirthPlace($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnMotherBirthPlace
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testPlaceNoParentFamily()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getPrimaryChildFamily')->andReturn(null);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnMotherBirthPlace($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }
}
