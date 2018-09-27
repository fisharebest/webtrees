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

use Fisharebest\Webtrees\Place;
use Mockery;

/**
 * Test harness for the class CensusColumnFatherBirthPlace
 */
class CensusColumnFatherBirthPlaceTest extends \Fisharebest\Webtrees\TestCase
{
    /**
     * Delete mock objects
     *
     * @return void
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * Get place mock.
     *
     * @param string $place Gedcom Place
     *
     * @return Place
     */
    private function getPlaceMock($place): Place
    {
        $placeParts = explode(', ', $place);

        $placeMock = Mockery::mock('\Fisharebest\Webtrees\Place');
        $placeMock->shouldReceive('getGedcomName')->andReturn($place);
        $placeMock->shouldReceive('lastPart')->andReturn(end($placeParts));

        return $placeMock;
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnFatherBirthPlace
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testSameCountry()
    {
        $father = Mockery::mock('Fisharebest\Webtrees\Individual');
        $father->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('London, England'));

        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getHusband')->andReturn($father);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getPrimaryChildFamily')->andReturn($family);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnFatherBirthPlace($census, '', '');

        $this->assertSame('London', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnFatherBirthPlace
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testDifferentCountry()
    {
        $father = Mockery::mock('Fisharebest\Webtrees\Individual');
        $father->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('London, England'));

        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getHusband')->andReturn($father);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getPrimaryChildFamily')->andReturn($family);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Ireland');

        $column = new CensusColumnFatherBirthPlace($census, '', '');

        $this->assertSame('London, England', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnFatherBirthPlace
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testPlaceNoParent()
    {
        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getHusband')->andReturn(null);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getPrimaryChildFamily')->andReturn($family);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnFatherBirthPlace($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnFatherBirthPlace
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testPlaceNoParentFamily()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getPrimaryChildFamily')->andReturn(null);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnFatherBirthPlace($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }
}
