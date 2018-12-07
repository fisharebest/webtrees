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

use Fisharebest\Webtrees\Date;
use Mockery;

/**
 * Test harness for the class CensusColumnYearsMarried
 */
class CensusColumnYearsMarriedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Delete mock objects
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnYearsMarried
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testNoSpouseFamily()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSpouseFamilies')->andReturn(array());

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnYearsMarried
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testNoMarriage()
    {
        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getFacts')->with('MARR', true)->andReturn(array());

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSpouseFamilies')->andReturn(array($family));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnYearsMarried
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testUndatedMarriage()
    {
        $fact = Mockery::mock('Fisharebest\Webtrees\Fact');
        $fact->shouldReceive('getDate')->andReturn(new Date(''));

        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getFacts')->with('MARR', true)->andReturn(array($fact));

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSpouseFamilies')->andReturn(array($family));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnYearsMarried
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testMarriageAfterCensus()
    {
        $fact = Mockery::mock('Fisharebest\Webtrees\Fact');
        $fact->shouldReceive('getDate')->andReturn(new Date('1861'));

        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getFacts')->with('MARR', true)->andReturn(array($fact));

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSpouseFamilies')->andReturn(array($family));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnYearsMarried
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testMarriageBeforeCensus()
    {
        $fact = Mockery::mock('Fisharebest\Webtrees\Fact');
        $fact->shouldReceive('getDate')->andReturn(new Date('OCT 1851'));

        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getFacts')->with('MARR', true)->andReturn(array($fact));

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSpouseFamilies')->andReturn(array($family));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        $this->assertSame('8', $column->generate($individual));
    }
}
