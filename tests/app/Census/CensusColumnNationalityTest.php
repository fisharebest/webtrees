<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * Test harness for the class CensusColumnNationality
 */
class CensusColumnNationalityTest extends TestCase
{
    private function getPlaceMock(string $place): Place
    {
        $placeMock = $this->createMock(Place::class);
        $placeMock->method('gedcomName')->willReturn($place);

        return $placeMock;
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnNationality
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testNoBirthPlace(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock(''));
        $individual->method('facts')->with(['IMMI', 'EMIG', 'NATU'], true)->willReturn(new Collection());

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Deutschland');

        $column = new CensusColumnNationality($census, '', '');

        self::assertSame('Deutsch', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnNationality
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testPlaceCountry(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Australia'));
        $individual->method('facts')->with(['IMMI', 'EMIG', 'NATU'], true)->willReturn(new Collection());

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnNationality($census, '', '');

        self::assertSame('Australia', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnNationality
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testBritish(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));
        $individual->method('facts')->with(['IMMI', 'EMIG', 'NATU'], true)->willReturn(new Collection());

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnNationality($census, '', '');

        self::assertSame('British', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnNationality
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testEmigrated(): void
    {
        $place1 = $this->createMock(Place::class);
        $place1->method('gedcomName')->willReturn('United States');

        $fact1 = $this->createMock(Fact::class);
        $fact1->method('place')->willReturn($place1);
        $fact1->method('date')->willReturn(new Date('1855'));

        $place2 = $this->createMock(Place::class);
        $place2->method('gedcomName')->willReturn('Australia');

        $fact2 = $this->createMock(Fact::class);
        $fact2->method('place')->willReturn($place2);
        $fact2->method('date')->willReturn(new Date('1865'));

        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));
        $individual->method('facts')->with(['IMMI', 'EMIG', 'NATU'], true)->willReturn(new Collection([
            $fact1,
            $fact2,
        ]));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnNationality($census, '', '');

        self::assertSame('United States', $column->generate($individual, $individual));
    }
}
