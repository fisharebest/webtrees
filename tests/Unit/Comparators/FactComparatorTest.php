<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Tests\Unit\Comparators;

use Fisharebest\Webtrees\Comparators\FactComparator;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FactComparator::class)]
class FactComparatorTest extends TestCase
{
    public function testByTypeSortsUsingFactOrder(): void
    {
        $record = self::createStub(GedcomRecord::class);

        $birth = self::createStub(Fact::class);
        $birth->method('record')->willReturn($record);
        $birth->method('tag')->willReturn('INDI:BIRT');
        $birth->method('value')->willReturn('');
        $birth->method('attribute')->willReturn('');

        $death = self::createStub(Fact::class);
        $death->method('record')->willReturn($record);
        $death->method('tag')->willReturn('INDI:DEAT');
        $death->method('value')->willReturn('');
        $death->method('attribute')->willReturn('');

        self::assertLessThan(0, FactComparator::byType($birth, $death));
    }

    public function testByDateFallsBackToTypeForSameDate(): void
    {
        $record = self::createStub(GedcomRecord::class);

        $birth = self::createStub(Fact::class);
        $birth->method('date')->willReturn(new Date('1 JAN 1900'));
        $birth->method('record')->willReturn($record);
        $birth->method('tag')->willReturn('INDI:BIRT');
        $birth->method('value')->willReturn('');
        $birth->method('attribute')->willReturn('1 JAN 1900');

        $death = self::createStub(Fact::class);
        $death->method('date')->willReturn(new Date('1 JAN 1900'));
        $death->method('record')->willReturn($record);
        $death->method('tag')->willReturn('INDI:DEAT');
        $death->method('value')->willReturn('');
        $death->method('attribute')->willReturn('1 JAN 1900');

        self::assertLessThan(0, FactComparator::byDate($birth, $death));
    }
}
