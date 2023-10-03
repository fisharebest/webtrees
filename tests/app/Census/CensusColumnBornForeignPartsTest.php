<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * Test harness for the class CensusColumnBornForeignParts
 */
class CensusColumnBornForeignPartsTest extends TestCase
{
    private function getPlaceMock(string $place): Place
    {
        $placeParts = explode(', ', $place);

        $placeMock = $this->createMock(Place::class);
        $placeMock->method('gedcomName')->willReturn($place);
        $placeMock->method('lastParts')->willReturn((new Collection($placeParts))->slice(-1));

        return $placeMock;
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornEnglandCensusEngland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornWalesCensusEngland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornScotlandCensusEngland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('S', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornIrelandCensusEngland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('I', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornForeignCensusEngland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Elbonia'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('F', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornEnglandCensusIreland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('E', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornWalesCensusIreland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('E', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornScotlandCensusIreland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('S', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornIrelandCensusIreland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornForeignCensusIreland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Elbonia'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('F', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornEnglandCensusScotland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('E', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornWalesCensusScotland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('E', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornScotlandCensusScotland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornIrelandCensusScotland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('I', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornForeignCensusScotland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Elbonia'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('F', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornEnglandCensusWales(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornWalesCensusWales(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornScotlandCensusWales(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('S', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornIrelandCensusWales(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('I', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornForeignCensusWales(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Elbonia'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('F', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornNowhereCensusEngland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock(''));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornNowhereCensusWales(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock(''));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBornNowhereCensusScotland(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock(''));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }
}
