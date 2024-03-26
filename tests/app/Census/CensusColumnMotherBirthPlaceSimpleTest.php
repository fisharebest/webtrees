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

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;


#[CoversClass(CensusColumnMotherBirthPlaceSimple::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusColumnMotherBirthPlaceSimpleTest extends TestCase
{
    private function getPlaceMock(string $place): Place
    {
        $placeParts = explode(', ', $place);

        $placeMock = $this->createMock(Place::class);
        $placeMock->method('gedcomName')->willReturn($place);
        $placeMock->method('lastParts')->willReturn(new Collection($placeParts));

        return $placeMock;
    }

    public function testKnownStateAndTown(): void
    {
        $father = $this->createMock(Individual::class);
        $father->method('getBirthPlace')->willReturn($this->getPlaceMock('Miami, Florida, United States'));

        $family = $this->createMock(Family::class);
        $family->method('wife')->willReturn($father);

        $individual = $this->createMock(Individual::class);
        $individual->method('childFamilies')->willReturn(new Collection([$family]));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('United States');

        $column = new CensusColumnMotherBirthPlaceSimple($census, '', '');

        self::assertSame('Florida', $column->generate($individual, $individual));
    }
}
