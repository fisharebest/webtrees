<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * Test harness for the class CensusColumnBornForeignParts
 */
class CensusColumnBornForeignPartsTest extends TestCase
{
    /**
     * Get place mock.
     *
     * @param string $place Gedcom Place
     *
     * @return Place
     */
    private function getPlaceMock(string $place): Place
    {
        $placeParts = explode(', ', $place);

        $placeMock = self::createMock(Place::class);
        $placeMock->method('gedcomName')->willReturn($place);
        $placeMock->method('lastParts')->willReturn((new Collection($placeParts))->slice(-1));

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
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornWalesCensusEngland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornScotlandCensusEngland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('S', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornIrelandCensusEngland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('I', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornForeignCensusEngland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Elbonia'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('F', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornEnglandCensusIreland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('E', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornWalesCensusIreland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('E', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornScotlandCensusIreland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('S', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornIrelandCensusIreland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornForeignCensusIreland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Elbonia'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Ireland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('F', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornEnglandCensusScotland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('E', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornWalesCensusScotland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('E', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornScotlandCensusScotland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornIrelandCensusScotland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('I', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornForeignCensusScotland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Elbonia'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('F', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornEnglandCensusWales(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornWalesCensusWales(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Cardiff, Wales'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornScotlandCensusWales(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Edinburgh, Scotland'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('S', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornIrelandCensusWales(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Dublin, Ireland'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('I', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornForeignCensusWales(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Elbonia'));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('F', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornNowhereCensusEngland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock(''));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornNowhereCensusWales(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock(''));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Wales');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnBornForeignParts
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testBornNowhereCensusScotland(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock(''));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Scotland');

        $column = new CensusColumnBornForeignParts($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }
}
