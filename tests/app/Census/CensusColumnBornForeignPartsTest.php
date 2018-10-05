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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Place;
use Mockery;

/**
 * Test harness for the class CensusColumnBornForeignParts
 */
class CensusColumnBornForeignPartsTest extends \Fisharebest\Webtrees\TestCase
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
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornEnglandCensusEngland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('London, England'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornWalesCensusEngland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornScotlandCensusEngland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('S', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornIrelandCensusEngland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('I', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornForeignCensusEngland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Elbonia'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('F', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornEnglandCensusIreland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('London, England'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('E', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornWalesCensusIreland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('E', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornScotlandCensusIreland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('S', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornIrelandCensusIreland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornForeignCensusIreland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Elbonia'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('F', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornEnglandCensusScotland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('London, England'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('E', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornWalesCensusScotland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('E', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornScotlandCensusScotland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornIrelandCensusScotland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('I', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornForeignCensusScotland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Elbonia'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('F', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornEnglandCensusWales()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('London, England'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornWalesCensusWales()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornScotlandCensusWales()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('S', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornIrelandCensusWales()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('I', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornForeignCensusWales()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Elbonia'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('F', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornNowhereCensusEngland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock(''));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornNowhereCensusWales()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock(''));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornNowhereCensusScotland()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock(''));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusPlace')->andReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }
}
