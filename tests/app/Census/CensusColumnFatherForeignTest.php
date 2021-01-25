<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

/**
 * Test harness for the class CensusColumnFatherForeign
 */
class CensusColumnFatherForeignTest extends TestCase
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
        $placeMock = self::createMock(Place::class);
        $placeMock->method('gedcomName')->willReturn($place);

        return $placeMock;
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnFatherForeign
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testSameCountry(): void
    {
        $father = self::createMock(Individual::class);
        $father->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));

        $family = self::createMock(Family::class);
        $family->method('husband')->willReturn($father);

        $individual = self::createMock(Individual::class);
        $individual->method('childFamilies')->willReturn(new Collection([$family]));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnFatherForeign($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnFatherForeign
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testDifferentCountry(): void
    {
        $father = self::createMock(Individual::class);
        $father->method('getBirthPlace')->willReturn($this->getPlaceMock('London, England'));

        $family = self::createMock(Family::class);
        $family->method('husband')->willReturn($father);

        $individual = self::createMock(Individual::class);
        $individual->method('childFamilies')->willReturn(new Collection([$family]));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('Ireland');

        $column = new CensusColumnFatherForeign($census, '', '');

        self::assertSame('Y', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnFatherForeign
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testPlaceNoParent(): void
    {
        $family = self::createMock(Family::class);
        $family->method('husband')->willReturn(null);

        $individual = self::createMock(Individual::class);
        $individual->method('childFamilies')->willReturn(new Collection([$family]));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnFatherForeign($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnFatherForeign
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testPlaceNoParentFamily(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('childFamilies')->willReturn(new Collection());

        $census = self::createMock(CensusInterface::class);
        $census->method('censusPlace')->willReturn('England');

        $column = new CensusColumnFatherForeign($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }
}
