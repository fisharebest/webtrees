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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * Test harness for the class CensusColumnMarriedWithinYear
 */
class CensusColumnMarriedWithinYearTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnMarriedWithinYear
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testMarriedWithinYear(): void
    {
        $fact = self::createMock(Fact::class);
        $fact->method('date')->willReturn(new Date('01 DEC 1859'));

        $family = self::createMock(Family::class);
        $family->method('facts')->with(['MARR'])->willReturn(new Collection([$fact]));

        $individual = self::createMock(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnMarriedWithinYear($census, '', '');

        self::assertSame('Y', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnMarriedWithinYear
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testMarriedOverYearBeforeTheCensus(): void
    {
        $fact = self::createMock(Fact::class);
        $fact->method('date')->willReturn(new Date('01 JAN 1859'));

        $family = self::createMock(Family::class);
        $family->method('facts')->with(['MARR'])->willReturn(new Collection([$fact]));

        $individual = self::createMock(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnMarriedWithinYear($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnMarriedWithinYear
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testMarriedAfterTheCensus(): void
    {
        $fact = self::createMock(Fact::class);
        $fact->method('date')->willReturn(new Date('02 JUN 1860'));

        $family = self::createMock(Family::class);
        $family->method('facts')->with(['MARR'])->willReturn(new Collection([$fact]));

        $individual = self::createMock(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnMarriedWithinYear($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnMarriedWithinYear
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testNoMarriage(): void
    {
        $family = self::createMock(Family::class);
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = self::createMock(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = self::createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnMarriedWithinYear($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnMarriedWithinYear
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testNoSpouseFamily(): void
    {
        $individual = self::createMock(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection());

        $census = self::createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnMarriedWithinYear($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }
}
