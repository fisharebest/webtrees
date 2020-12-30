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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * Test harness for the class CensusColumnChildrenBornAlive
 */
class CensusColumnChildrenBornAliveTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnChildrenBornAlive
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testMale(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('sex')->willReturn('M');
        $individual->method('spouseFamilies')->willReturn(new Collection([]));

        $census = self::createMock(CensusInterface::class);

        $column = new CensusColumnChildrenBornAlive($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnChildrenBornAlive
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testCountChildren(): void
    {
        // Stillborn
        $child1 = self::createMock(Individual::class);
        $child1->method('getBirthDate')->willReturn(new Date('01 FEB 1904'));
        $child1->method('getDeathDate')->willReturn(new Date('01 FEB 1904'));

        // Died after census
        $child2 = self::createMock(Individual::class);
        $child2->method('getBirthDate')->willReturn(new Date('02 FEB 1904'));
        $child2->method('getDeathDate')->willReturn(new Date('20 DEC 1912'));

        // Died before census
        $child3 = self::createMock(Individual::class);
        $child3->method('getBirthDate')->willReturn(new Date('02 FEB 1904'));
        $child3->method('getDeathDate')->willReturn(new Date('20 DEC 1910'));

        // Still living
        $child4 = self::createMock(Individual::class);
        $child4->method('getBirthDate')->willReturn(new Date('01 FEB 1904'));
        $child4->method('getDeathDate')->willReturn(new Date(''));

        $family = self::createMock(Family::class);
        $family->method('children')->willReturn(new Collection([
            $child1,
            $child2,
            $child3,
            $child4,
        ]));

        $individual = self::createMock(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 MAR 1911');

        $column = new CensusColumnChildrenBornAlive($census, '', '');

        self::assertSame('3', $column->generate($individual, $individual));
    }
}
