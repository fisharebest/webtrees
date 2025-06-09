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

use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusColumnBirthPlaceSimple::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusColumnBirthPlaceSimpleTest extends TestCase
{
    private function getPlaceMock(string $place): Place
    {
        $placeMock = $this->createMock(Place::class);
        $placeMock->method('gedcomName')->willReturn($place);

        return $placeMock;
    }

    public function testForeignCountry(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Westminster, London, England'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('United States');

        $column = new CensusColumnBirthPlaceSimple($census, '', '');

        self::assertSame('England', $column->generate($individual, $individual));
    }

    public function testJustCountry(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('United States'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('United States');

        $column = new CensusColumnBirthPlaceSimple($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testKnownState(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Maryland, United States'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('United States');

        $column = new CensusColumnBirthPlaceSimple($census, '', '');

        self::assertSame('Maryland', $column->generate($individual, $individual));
    }

    public function testKnownStateAndTown(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthPlace')->willReturn($this->getPlaceMock('Miami, Florida, United States'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('United States');

        $column = new CensusColumnBirthPlaceSimple($census, '', '');

        self::assertSame('Florida', $column->generate($individual, $individual));
    }
}
