<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
 * Test harness for the class CensusColumnBornForeignParts
 */
class CensusColumnBornForeignPartsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Delete mock objects
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornEnglandCensusEngland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('London, England');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornWalesCensusEngland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Cardiff, Wales');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornScotlandCensusEngland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Edinburgh, Scotland');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('S', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornIrelandCensusEngland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Dublin, Ireland');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('I', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornForeignCensusEngland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Elbonia');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('F', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornEnglandCensusIreland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('London, England');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('E', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornWalesCensusIreland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Cardiff, Wales');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('E', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornScotlandCensusIreland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Edinburgh, Scotland');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('S', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornIrelandCensusIreland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Dublin, Ireland');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornForeignCensusIreland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Elbonia');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('F', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornEnglandCensusScotland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('London, England');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('E', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornWalesCensusScotland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Cardiff, Wales');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('E', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornScotlandCensusScotland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Edinburgh, Scotland');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornIrelandCensusScotland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Dublin, Ireland');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('I', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornForeignCensusScotland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Elbonia');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('F', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornEnglandCensusWales()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('London, England');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornWalesCensusWales()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Cardiff, Wales');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornScotlandCensusWales()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Edinburgh, Scotland');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('S', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornIrelandCensusWales()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Dublin, Ireland');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('I', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornForeignCensusWales()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('Elbonia');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('F', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornNowhereCensusEngland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornNowhereCensusWales()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }

    /**
     * @covers Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornNowhereCensusScotland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn('');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual));
    }
}
