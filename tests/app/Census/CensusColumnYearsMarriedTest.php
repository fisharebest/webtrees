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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusColumnYearsMarried::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusColumnYearsMarriedTest extends TestCase
{
    public function testNoSpouseFamily(): void
    {
        $individual = $this->createStub(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection());

        $census = $this->createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testNoMarriage(): void
    {
        $family = $this->createStub(Family::class);
        $family->method('facts')->with(['MARR'], true)->willReturn(new Collection());

        $individual = $this->createStub(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = $this->createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testUndatedMarriage(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('date')->willReturn(new Date(''));

        $family = $this->createStub(Family::class);
        $family->method('facts')->with(['MARR'], true)->willReturn(new Collection([$fact]));

        $individual = $this->createStub(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = $this->createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testMarriageAfterCensus(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('date')->willReturn(new Date('1861'));

        $family = $this->createStub(Family::class);
        $family->method('facts')->with(['MARR'], true)->willReturn(new Collection([$fact]));

        $individual = $this->createStub(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = $this->createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testMarriageBeforeCensus(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('date')->willReturn(new Date('OCT 1851'));

        $family = $this->createStub(Family::class);
        $family->method('facts')->with(['MARR'], true)->willReturn(new Collection([$fact]));

        $individual = $this->createStub(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = $this->createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('01 JUN 1860');

        $column = new CensusColumnYearsMarried($census, '', '');

        self::assertSame('8', $column->generate($individual, $individual));
    }
}
