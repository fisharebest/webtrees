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
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusColumnSexF::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusColumnSexFTest extends TestCase
{
    public function testMale(): void
    {
        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('M');

        $census = self::createStub(CensusInterface::class);

        $column = new CensusColumnSexF($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testFeale(): void
    {
        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('F');

        $census = self::createStub(CensusInterface::class);

        $column = new CensusColumnSexF($census, '', '');

        self::assertSame('X', $column->generate($individual, $individual));
    }

    public function testUnknownSex(): void
    {
        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('U');

        $census = self::createStub(CensusInterface::class);

        $column = new CensusColumnSexF($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }
}
