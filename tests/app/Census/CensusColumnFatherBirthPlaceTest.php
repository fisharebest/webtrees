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

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusColumnFatherBirthPlace::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusColumnFatherBirthPlaceTest extends TestCase
{
    private function getPlaceMock(string $place): Place
    {
        $placeParts = explode(', ', $place);

        $placeMock = $this->createStub(Place::class);
        $placeMock->method('gedcomName')->willReturn($place);
        $placeMock->method('lastParts')->willReturn(new Collection($placeParts));

        return $placeMock;
    }

    public function testSameCountry(): void
    {
        $father = $this->createStub(Individual::class);
        $father->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));

        $family = $this->createStub(Family::class);
        $family->method('husband')->willReturn($father);

        $individual = $this->createStub(Individual::class);
        $individual->method('childFamilies')->willReturn(new Collection([$family]));

        $census = $this->createStub(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnFatherBirthPlace($census, '', '');

        self::assertSame('London', $column->generate($individual, $individual));
    }

    public function testDifferentCountry(): void
    {
        $father = $this->createStub(Individual::class);
        $father->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));

        $family = $this->createStub(Family::class);
        $family->method('husband')->willReturn($father);

        $individual = $this->createStub(Individual::class);
        $individual->method('childFamilies')->willReturn(new Collection([$family]));

        $census = $this->createStub(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Ireland');

        $column = new CensusColumnFatherBirthPlace($census, '', '');

        self::assertSame('London, England', $column->generate($individual, $individual));
    }

    public function testPlaceNoParent(): void
    {
        $family = $this->createStub(Family::class);
        $family->method('husband')->willReturn(null);

        $individual = $this->createStub(Individual::class);
        $individual->method('childFamilies')->willReturn(new Collection([$family]));

        $census = $this->createStub(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnFatherBirthPlace($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testPlaceNoParentFamily(): void
    {
        $individual = $this->createStub(Individual::class);
        $individual->method('childFamilies')->willReturn(new Collection());

        $census = $this->createStub(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnFatherBirthPlace($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }
}
