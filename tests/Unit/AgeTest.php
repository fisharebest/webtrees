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

namespace Fisharebest\Webtrees\Tests\Unit;

use Fisharebest\Webtrees\Age;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function view;

#[CoversClass(Age::class)]
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
        self::assertSame('0 days', $age->toString());
        self::assertTrue($age->isZero());
        self::assertFalse($age->isNegative());
        self::assertFalse($age->isRange());
        self::assertTrue($age->isValid());
        self::assertFalse($age->isImplausible());
    }

    public function testSameMonthYear(): void
    {
        $x = new Date('APR 2019');
        $y = new Date('APR 2019');
        $age = new Age($x, $y);

        self::assertSame(0, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0', $age->toString());
        self::assertFalse($age->isZero()); // Not exact (no day precision)
    }

    public function testSameYear(): void
    {
        $x = new Date('2019');
        $y = new Date('2019');
        $age = new Age($x, $y);

        self::assertSame(0, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0', $age->toString());
        self::assertFalse($age->isZero()); // Not exact (no day precision)
    }

    public function testReversed(): void
    {
        $x = new Date('13 FEB 2019');
        $y = new Date('07 JAN 2019');
        $age = new Age($x, $y);

        self::assertSame(-37, $age->ageDays());
        self::assertSame(-1, $age->ageYears());
        self::assertSame(view('icons/warning'), $age->ageYearsString());
        self::assertSame(view('icons/warning'), $age->toString());
        self::assertTrue($age->isNegative());
        self::assertFalse($age->isZero());
    }

    public function testStartDateInvalid(): void
    {
        $x = new Date('');
        $y = new Date('07 JAN 2019');
        $age = new Age($x, $y);

        self::assertSame(-1, $age->ageDays());
        self::assertSame(-1, $age->ageYears());
        self::assertSame('', $age->ageYearsString());
        self::assertSame('', $age->toString());
        self::assertFalse($age->isValid());
        self::assertFalse($age->isZero());
    }

    public function testEndDateInvalid(): void
    {
        $x = new Date('07 JAN 2019');
        $y = new Date('');
        $age = new Age($x, $y);

        self::assertSame(-1, $age->ageDays());
        self::assertSame(-1, $age->ageYears());
        self::assertSame('', $age->ageYearsString());
        self::assertSame('', $age->toString());
        self::assertFalse($age->isValid());
    }

    public function testOverlappingDates1(): void
    {
        $x = new Date('07 JAN 2019');
        $y = new Date('JAN 2019');
        $age = new Age($x, $y);

        self::assertSame(-6, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0', $age->toString());
    }

    public function testOverlappingDates2(): void
    {
        $x = new Date('JAN 2019');
        $y = new Date('07 JAN 2019');
        $age = new Age($x, $y);

        self::assertSame(6, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0', $age->toString());
    }

    public function testDifferentDay(): void
    {
        $x = new Date('13 APR 2019');
        $y = new Date('27 APR 2019');
        $age = new Age($x, $y);

        self::assertSame(14, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('14 days', $age->toString());
    }

    public function testDifferentMonth(): void
    {
        $x = new Date('13 APR 2019');
        $y = new Date('27 JUN 2019');
        $age = new Age($x, $y);

        self::assertSame(75, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('2 months', $age->toString());
    }

    public function testDifferentYear(): void
    {
        $x = new Date('13 APR 2012');
        $y = new Date('27 JUN 2019');
        $age = new Age($x, $y);

        self::assertSame(2631, $age->ageDays());
        self::assertSame(7, $age->ageYears());
        self::assertSame('7', $age->ageYearsString());
        self::assertSame('7 years', $age->toString());
    }

    public function testDateRangeBirth(): void
    {
        // Birth between 1800 and 1804, event in 1847
        $x = new Date('BET 1800 AND 1804');
        $y = new Date('1847');
        $age = new Age($x, $y);

        self::assertTrue($age->isRange());
        self::assertTrue($age->isValid());
        self::assertSame(47, $age->ageYears()); // max years (earliest birth to latest event)
        // Display should show range "43–47 years"
        self::assertStringContainsString('43', $age->toString());
        self::assertStringContainsString('47', $age->toString());
    }

    public function testDateRangeEvent(): void
    {
        // Exact birth, event is a range
        $x = new Date('1800');
        $y = new Date('BET 1843 AND 1847');
        $age = new Age($x, $y);

        self::assertTrue($age->isRange());
        self::assertSame(47, $age->ageYears()); // max years
        self::assertStringContainsString('43', $age->toString());
        self::assertStringContainsString('47', $age->toString());
    }

    public function testMatchesRecordedAgeExactMatch(): void
    {
        $x = new Date('13 APR 2012');
        $y = new Date('27 JUN 2019');
        $age = new Age($x, $y);

        // Exact match at year level
        self::assertTrue($age->matchesRecordedAge('7y'));
        // Non-match
        self::assertFalse($age->matchesRecordedAge('8y'));
        self::assertFalse($age->matchesRecordedAge('6y'));
    }

    public function testMatchesRecordedAgeWithMonths(): void
    {
        $x = new Date('13 APR 2012');
        $y = new Date('27 JUN 2019');
        $age = new Age($x, $y);

        // Match at year+month level
        self::assertTrue($age->matchesRecordedAge('7y 2m'));
        // Non-match
        self::assertFalse($age->matchesRecordedAge('7y 5m'));
    }

    public function testMatchesRecordedAgeApproximate(): void
    {
        $x = new Date('13 APR 2012');
        $y = new Date('27 JUN 2019');
        $age = new Age($x, $y);

        // Recorded ages with < or > should never match (always show calculated)
        self::assertFalse($age->matchesRecordedAge('<7y'));
        self::assertFalse($age->matchesRecordedAge('>7y'));
    }

    public function testMatchesRecordedAgeEmpty(): void
    {
        $x = new Date('13 APR 2012');
        $y = new Date('27 JUN 2019');
        $age = new Age($x, $y);

        // Empty recorded age should not match
        self::assertFalse($age->matchesRecordedAge(''));
    }

    public function testMatchesRecordedAgeInvalid(): void
    {
        $x = new Date('');
        $y = new Date('27 JUN 2019');
        $age = new Age($x, $y);

        // Invalid age should never match
        self::assertFalse($age->matchesRecordedAge('7y'));
    }

    public function testMatchesRecordedAgeRange(): void
    {
        // Birth range: recorded age within the possible range
        $x = new Date('BET 1800 AND 1804');
        $y = new Date('1847');
        $age = new Age($x, $y);

        // 45y is within the 43-47 range
        self::assertTrue($age->matchesRecordedAge('45y'));
        // 43y and 47y are at the edges
        self::assertTrue($age->matchesRecordedAge('43y'));
        self::assertTrue($age->matchesRecordedAge('47y'));
        // Outside the range
        self::assertFalse($age->matchesRecordedAge('42y'));
        self::assertFalse($age->matchesRecordedAge('48y'));
    }

    public function testIsZeroExactDates(): void
    {
        $x = new Date('15 MAR 2000');
        $y = new Date('15 MAR 2000');
        $age = new Age($x, $y);

        self::assertTrue($age->isZero());
    }

    public function testIsZeroImpreciseDates(): void
    {
        // Without day precision, isZero should be false
        $x = new Date('MAR 2000');
        $y = new Date('MAR 2000');
        $age = new Age($x, $y);

        self::assertFalse($age->isZero());
    }

    public function testImplausibleAge(): void
    {
        $x = new Date('1 JAN 1800');
        $y = new Date('1 JAN 1950');
        $age = new Age($x, $y);

        self::assertTrue($age->isImplausible());
        self::assertSame(150, $age->ageYears());
        // ageYearsString should contain warning
        self::assertStringContainsString(view('icons/warning'), $age->ageYearsString());
        // toString should contain warning
        self::assertStringContainsString(view('icons/warning'), $age->toString());
        // But also contains the age text
        self::assertStringContainsString('150', $age->toString());
    }

    public function testPlausibleAge(): void
    {
        $x = new Date('1 JAN 1950');
        $y = new Date('1 JAN 2000');
        $age = new Age($x, $y);

        self::assertFalse($age->isImplausible());
        self::assertSame(50, $age->ageYears());
        self::assertSame('50', $age->ageYearsString());
    }

    public function testBeforeBirthDate(): void
    {
        $x = new Date('BEF 1850');
        $y = new Date('1 JAN 1880');
        $age = new Age($x, $y);

        self::assertFalse($age->isValid());
        self::assertSame(-1, $age->ageYears());
        self::assertSame(-1, $age->ageDays());
        self::assertSame('', $age->toString());
    }

    public function testAfterBirthDate(): void
    {
        $x = new Date('AFT 1800');
        $y = new Date('1 JAN 1880');
        $age = new Age($x, $y);

        self::assertFalse($age->isValid());
        self::assertSame('', $age->toString());
    }

    public function testBeforeEventDate(): void
    {
        $x = new Date('1 JAN 1800');
        $y = new Date('BEF 1850');
        $age = new Age($x, $y);

        self::assertFalse($age->isValid());
        self::assertSame('', $age->toString());
    }

    public function testAfterEventDate(): void
    {
        $x = new Date('1 JAN 1800');
        $y = new Date('AFT 1850');
        $age = new Age($x, $y);

        self::assertFalse($age->isValid());
        self::assertSame('', $age->toString());
    }
}
