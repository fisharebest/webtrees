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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusColumnAgeFemale::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusColumnAgeFemaleTest extends TestCase
{
    public function testMale(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('M');

        $census = $this->createMock(CensusInterface::class);

        $column = new CensusColumnAgeFemale($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testFemale(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('01 JAN 1800'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1832');

        $column = new CensusColumnAgeFemale($census, '', '');

        self::assertSame('32', $column->generate($individual, $individual));
    }

    public function testUnknownSex(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('U');
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('01 JAN 1800'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1832');

        $column = new CensusColumnAgeFemale($census, '', '');

        self::assertSame('32', $column->generate($individual, $individual));
    }

    public function testLessThanOneYear(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('01 JAN 1800'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1800');

        $column = new CensusColumnAgeFemale($census, '', '');

        self::assertSame('0', $column->generate($individual, $individual));
    }
}
