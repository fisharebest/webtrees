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

use Fisharebest\Webtrees\Comparators\IndividualComparator;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(IndividualComparator::class)]
class IndividualComparatorTest extends TestCase
{
    public function testByBirthDateSortsAscending(): void
    {
        $earlier = $this->getMockBuilder(Individual::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEstimatedBirthDate'])
            ->getMock();

        $earlier->expects($this->once())
            ->method('getEstimatedBirthDate')
            ->willReturn(new Date('1 JAN 1900'));

        $later = $this->getMockBuilder(Individual::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEstimatedBirthDate'])
            ->getMock();

        $later->expects($this->once())
            ->method('getEstimatedBirthDate')
            ->willReturn(new Date('1 JAN 1950'));

        self::assertSame(-1, IndividualComparator::byBirthDate($earlier, $later));
    }

    public function testByDeathDateSortsAscending(): void
    {
        $earlier = $this->getMockBuilder(Individual::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEstimatedDeathDate'])
            ->getMock();

        $earlier->expects($this->once())
            ->method('getEstimatedDeathDate')
            ->willReturn(new Date('1 JAN 1970'));

        $later = $this->getMockBuilder(Individual::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEstimatedDeathDate'])
            ->getMock();

        $later->expects($this->once())
            ->method('getEstimatedDeathDate')
            ->willReturn(new Date('1 JAN 2010'));

        self::assertSame(-1, IndividualComparator::byDeathDate($earlier, $later));
    }
}
