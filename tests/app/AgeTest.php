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

namespace Fisharebest\Webtrees;

use function view;

/**
 * @covers \Fisharebest\Webtrees\Age
 */
class AgeTest extends TestCase
{
    public function testSameDayMonthYear(): void
    {
        $x = new Date('27 APR 2019');
        $y = new Date('27 APR 2019');
        $age = new Age($x, $y);

        self::assertSame(0, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0 days', (string) $age);
    }

    public function testSameMonthYear(): void
    {
        $x = new Date('APR 2019');
        $y = new Date('APR 2019');
        $age = new Age($x, $y);

        self::assertSame(0, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0', (string) $age);
    }

    public function testSameYear(): void
    {
        $x = new Date('2019');
        $y = new Date('2019');
        $age = new Age($x, $y);

        self::assertSame(0, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0', (string) $age);
    }

    public function testReversed(): void
    {
        $x = new Date('13 FEB 2019');
        $y = new Date('07 JAN 2019');
        $age = new Age($x, $y);

        self::assertSame(-37, $age->ageDays());
        self::assertSame(-1, $age->ageYears());
        self::assertSame(view('icons/warning'), $age->ageYearsString());
        self::assertSame(view('icons/warning'), (string) $age);
    }

    public function testStartDateInvalid(): void
    {
        $x = new Date('');
        $y = new Date('07 JAN 2019');
        $age = new Age($x, $y);

        self::assertSame(-1, $age->ageDays());
        self::assertSame(-1, $age->ageYears());
        self::assertSame('', $age->ageYearsString());
        self::assertSame('', (string) $age);
    }

    public function testEndDateInvalid(): void
    {
        $x = new Date('07 JAN 2019');
        $y = new Date('');
        $age = new Age($x, $y);

        self::assertSame(-1, $age->ageDays());
        self::assertSame(-1, $age->ageYears());
        self::assertSame('', $age->ageYearsString());
        self::assertSame('', (string) $age);
    }

    public function testOverlappingDates1(): void
    {
        $x = new Date('07 JAN 2019');
        $y = new Date('JAN 2019');
        $age = new Age($x, $y);

        self::assertSame(-6, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0', (string) $age);
    }

    public function testOverlappingDates2(): void
    {
        $x = new Date('JAN 2019');
        $y = new Date('07 JAN 2019');
        $age = new Age($x, $y);

        self::assertSame(6, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0', (string) $age);
    }

    public function testDifferentDay(): void
    {
        $x = new Date('13 APR 2019');
        $y = new Date('27 APR 2019');
        $age = new Age($x, $y);

        self::assertSame(14, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('14 days', (string) $age);
    }

    public function testDifferentMonth(): void
    {
        $x = new Date('13 APR 2019');
        $y = new Date('27 JUN 2019');
        $age = new Age($x, $y);

        self::assertSame(75, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('2 months', (string) $age);
    }

    public function testDifferentYear(): void
    {
        $x = new Date('13 APR 2012');
        $y = new Date('27 JUN 2019');
        $age = new Age($x, $y);

        self::assertSame(2631, $age->ageDays());
        self::assertSame(7, $age->ageYears());
        self::assertSame('7', $age->ageYearsString());
        self::assertSame('7 years', (string) $age);
    }
}
