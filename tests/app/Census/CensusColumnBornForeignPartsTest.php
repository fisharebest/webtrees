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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Individual;
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

        $placeMock = Mockery::mock(Place::class);
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
    public function testBornEnglandCensusEngland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('London, England'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornWalesCensusEngland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornScotlandCensusEngland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornIrelandCensusEngland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornForeignCensusEngland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Elbonia'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornEnglandCensusIreland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('London, England'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornWalesCensusIreland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornScotlandCensusIreland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornIrelandCensusIreland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornForeignCensusIreland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Elbonia'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornEnglandCensusScotland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('London, England'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornWalesCensusScotland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornScotlandCensusScotland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornIrelandCensusScotland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornForeignCensusScotland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Elbonia'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornEnglandCensusWales(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('London, England'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornWalesCensusWales(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornScotlandCensusWales(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornIrelandCensusWales(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornForeignCensusWales(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock('Elbonia'));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornNowhereCensusEngland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock(''));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornNowhereCensusWales(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock(''));

        $census = Mockery::mock(CensusInterface::class);
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
    public function testBornNowhereCensusScotland(): void
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getBirthPlace')->andReturn($this->getPlaceMock(''));

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusPlace')->andReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }
}
