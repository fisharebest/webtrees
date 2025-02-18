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
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * @covers \Fisharebest\Webtrees\Census\CensusColumnChildrenDied
 * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
 */
class CensusColumnChildrenDiedTest extends TestCase
{
    public function testMale(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('M');
        $individual->method('spouseFamilies')->willReturn(new Collection([]));

        $census = $this->createMock(CensusInterface::class);

        $column = new CensusColumnChildrenDied($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testCountChildren(): void
    {
        // Stillborn
        $child1 = $this->createMock(Individual::class);
        $child1->method('getBirthDate')->willReturn(new Date('01 FEB 1904'));
        $child1->method('getDeathDate')->willReturn(new Date('01 FEB 1904'));

        // Died after census
        $child2 = $this->createMock(Individual::class);
        $child2->method('getBirthDate')->willReturn(new Date('02 FEB 1904'));
        $child2->method('getDeathDate')->willReturn(new Date('20 DEC 1912'));

        // Died before census
        $child3 = $this->createMock(Individual::class);
        $child3->method('getBirthDate')->willReturn(new Date('02 FEB 1904'));
        $child3->method('getDeathDate')->willReturn(new Date('20 DEC 1910'));

        // Still living
        $child4 = $this->createMock(Individual::class);
        $child4->method('getBirthDate')->willReturn(new Date('01 FEB 1904'));
        $child4->method('getDeathDate')->willReturn(new Date(''));

        $family = $this->createMock(Family::class);
        $family->method('children')->willReturn(new Collection([$child1, $child2, $child3, $child4]));

        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 MAR 1911');

        $column = new CensusColumnChildrenDied($census, '', '');

        self::assertSame('1', $column->generate($individual, $individual));
    }
}
