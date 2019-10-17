<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * Test harness for the class CensusColumnAgeMarried
 */
class CensusColumnAgeMarriedTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnAgeMarried
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testAgeMarried(): void
    {
        $fact = $this->createMock(Fact::class);
        $fact->method('date')->willReturn(new Date('01 DEC 1859'));

        $family = $this->createMock(Family::class);
        $family->method('facts')->with(['MARR'], true)->willReturn(new Collection([$fact]));

        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthDate')->willReturn(new Date('15 MAR 1840'));
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnAgeMarried($census, '', '');

        $this->assertSame('19', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnAgeMarried
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testNoBirthDate(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthDate')->willReturn(new Date(''));
        $individual->method('spouseFamilies')->willReturn(new Collection());

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnAgeMarried($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnAgeMarried
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testNoMarriage(): void
    {
        $family = $this->createMock(Family::class);
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthDate')->willReturn(new Date(''));
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnAgeMarried($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnAgeMarried
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testNoSpouseFamily(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('getBirthDate')->willReturn(new Date('15 MAR 1840'));
        $individual->method('spouseFamilies')->willReturn(new Collection());

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnAgeMarried($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }
}
