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

use Fisharebest\Webtrees\Comparators\GedcomRecordComparator;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Timestamp;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GedcomRecordComparator::class)]
class GedcomRecordComparatorTest extends TestCase
{
    public function testByTypeSortsByTag(): void
    {
        $family = $this->getMockBuilder(GedcomRecord::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['tag'])
            ->getMock();

        $family->expects($this->once())->method('tag')->willReturn('FAM');

        $individual = $this->getMockBuilder(GedcomRecord::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['tag'])
            ->getMock();

        $individual->expects($this->once())->method('tag')->willReturn('INDI');

        self::assertSame(-1, GedcomRecordComparator::byType($family, $individual));
    }

    public function testByNameSortsVisibleRecords(): void
    {
        $alpha = $this->getMockBuilder(GedcomRecord::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['canShowName', 'sortName'])
            ->getMock();

        $alpha->expects($this->once())->method('canShowName')->willReturn(true);
        $alpha->expects($this->once())->method('sortName')->willReturn('Alpha');

        $zulu = $this->getMockBuilder(GedcomRecord::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['canShowName', 'sortName'])
            ->getMock();

        $zulu->expects($this->once())->method('canShowName')->willReturn(true);
        $zulu->expects($this->once())->method('sortName')->willReturn('Zulu');

        self::assertSame(-1, GedcomRecordComparator::byName($alpha, $zulu));
    }

    public function testByNameSortsVisibleRecordsBeforePrivateRecords(): void
    {
        $visible = $this->getMockBuilder(GedcomRecord::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['canShowName'])
            ->getMock();

        $visible->expects($this->once())->method('canShowName')->willReturn(true);

        $private = $this->getMockBuilder(GedcomRecord::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['canShowName'])
            ->getMock();

        $private->expects($this->once())->method('canShowName')->willReturn(false);

        self::assertSame(-1, GedcomRecordComparator::byName($visible, $private));
    }

    public function testByLastChangeSortsAscending(): void
    {
        $older = $this->getMockBuilder(GedcomRecord::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['lastChangeTimestamp'])
            ->getMock();

        $older->expects($this->once())
            ->method('lastChangeTimestamp')
            ->willReturn(new Timestamp(100, 'UTC', 'en-US'));

        $newer = $this->getMockBuilder(GedcomRecord::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['lastChangeTimestamp'])
            ->getMock();

        $newer->expects($this->once())
            ->method('lastChangeTimestamp')
            ->willReturn(new Timestamp(200, 'UTC', 'en-US'));

        self::assertSame(-1, GedcomRecordComparator::byLastChange($older, $newer));
    }
}
