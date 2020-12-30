<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

namespace Fisharebest\Webtrees;

use function view;

/**
 * Test the Age functions
 */
class AgeTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Age::__construct
     * @covers \Fisharebest\Webtrees\Age::ageDays
     * @covers \Fisharebest\Webtrees\Age::ageYears
     * @covers \Fisharebest\Webtrees\Age::ageYearsString
     * @covers \Fisharebest\Webtrees\Age::ageString
     * @covers \Fisharebest\Webtrees\Age::ageAtEvent
     * @covers \Fisharebest\Webtrees\Age::timeAfterDeath
     *
     * @return void
     */
    public function testSameDayMonthYear(): void
    {
        $x = new Date('27 APR 2019');
        $y = new Date('27 APR 2019');
        $age = new Age($x, $y);

        self::assertSame(0, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0 days', $age->ageString());
        self::assertSame('(aged 0 days)', $age->ageAtEvent(false));
        self::assertSame('(age 0 days)', $age->ageAtEvent(true));
        self::assertSame('(on the date of death)', $age->timeAfterDeath());
    }

    /**
     * @covers \Fisharebest\Webtrees\Age::__construct
     * @covers \Fisharebest\Webtrees\Age::ageDays
     * @covers \Fisharebest\Webtrees\Age::ageYears
     * @covers \Fisharebest\Webtrees\Age::ageYearsString
     * @covers \Fisharebest\Webtrees\Age::ageString
     * @covers \Fisharebest\Webtrees\Age::ageAtEvent
     * @covers \Fisharebest\Webtrees\Age::timeAfterDeath
     *
     * @return void
     */
    public function testSameMonthYear(): void
    {
        $x = new Date('APR 2019');
        $y = new Date('APR 2019');
        $age = new Age($x, $y);

        self::assertSame(0, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0', $age->ageString());
        self::assertSame('(aged 0)', $age->ageAtEvent(false));
        self::assertSame('(age 0)', $age->ageAtEvent(true));
        self::assertSame('', $age->timeAfterDeath());
    }

    /**
     * @covers \Fisharebest\Webtrees\Age::__construct
     * @covers \Fisharebest\Webtrees\Age::ageDays
     * @covers \Fisharebest\Webtrees\Age::ageYears
     * @covers \Fisharebest\Webtrees\Age::ageYearsString
     * @covers \Fisharebest\Webtrees\Age::ageString
     * @covers \Fisharebest\Webtrees\Age::ageAtEvent
     * @covers \Fisharebest\Webtrees\Age::timeAfterDeath
     *
     * @return void
     */
    public function testSameYear(): void
    {
        $x = new Date('2019');
        $y = new Date('2019');
        $age = new Age($x, $y);

        self::assertSame(0, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0', $age->ageString());
        self::assertSame('(aged 0)', $age->ageAtEvent(false));
        self::assertSame('(age 0)', $age->ageAtEvent(true));
        self::assertSame('', $age->timeAfterDeath());
    }

    /**
     * @covers \Fisharebest\Webtrees\Age::__construct
     * @covers \Fisharebest\Webtrees\Age::ageDays
     * @covers \Fisharebest\Webtrees\Age::ageYears
     * @covers \Fisharebest\Webtrees\Age::ageYearsString
     * @covers \Fisharebest\Webtrees\Age::ageString
     * @covers \Fisharebest\Webtrees\Age::ageAtEvent
     * @covers \Fisharebest\Webtrees\Age::timeAfterDeath
     *
     * @return void
     */
    public function testReversed(): void
    {
        $x = new Date('13 FEB 2019');
        $y = new Date('07 JAN 2019');
        $age = new Age($x, $y);

        self::assertSame(-37, $age->ageDays());
        self::assertSame(-1, $age->ageYears());
        self::assertSame(view('icons/warning'), $age->ageYearsString());
        self::assertSame(view('icons/warning'), $age->ageString());
        self::assertSame('(aged ' . view('icons/warning') . ')', $age->ageAtEvent(false));
        self::assertSame('(age ' . view('icons/warning') . ')', $age->ageAtEvent(true));
        self::assertSame('(' . view('icons/warning') . ' after death)', $age->timeAfterDeath());
    }

    /**
     * @covers \Fisharebest\Webtrees\Age::__construct
     * @covers \Fisharebest\Webtrees\Age::ageDays
     * @covers \Fisharebest\Webtrees\Age::ageYears
     * @covers \Fisharebest\Webtrees\Age::ageYearsString
     * @covers \Fisharebest\Webtrees\Age::ageString
     * @covers \Fisharebest\Webtrees\Age::ageAtEvent
     * @covers \Fisharebest\Webtrees\Age::timeAfterDeath
     *
     * @return void
     */
    public function testStartDateInvalid(): void
    {
        $x = new Date('');
        $y = new Date('07 JAN 2019');
        $age = new Age($x, $y);

        self::assertSame(-1, $age->ageDays());
        self::assertSame(-1, $age->ageYears());
        self::assertSame('', $age->ageYearsString());
        self::assertSame('', $age->ageString());
        self::assertSame('', $age->ageAtEvent(false));
        self::assertSame('', $age->ageAtEvent(true));
        self::assertSame('', $age->timeAfterDeath());
    }

    /**
     * @covers \Fisharebest\Webtrees\Age::__construct
     * @covers \Fisharebest\Webtrees\Age::ageDays
     * @covers \Fisharebest\Webtrees\Age::ageYears
     * @covers \Fisharebest\Webtrees\Age::ageYearsString
     * @covers \Fisharebest\Webtrees\Age::ageString
     * @covers \Fisharebest\Webtrees\Age::ageAtEvent
     * @covers \Fisharebest\Webtrees\Age::timeAfterDeath
     *
     * @return void
     */
    public function testEndDateInvalid(): void
    {
        $x = new Date('07 JAN 2019');
        $y = new Date('');
        $age = new Age($x, $y);

        self::assertSame(-1, $age->ageDays());
        self::assertSame(-1, $age->ageYears());
        self::assertSame('', $age->ageYearsString());
        self::assertSame('', $age->ageString());
        self::assertSame('', $age->ageAtEvent(false));
        self::assertSame('', $age->ageAtEvent(true));
        self::assertSame('', $age->timeAfterDeath());
    }

    /**
     * @covers \Fisharebest\Webtrees\Age::__construct
     * @covers \Fisharebest\Webtrees\Age::ageDays
     * @covers \Fisharebest\Webtrees\Age::ageYears
     * @covers \Fisharebest\Webtrees\Age::ageYearsString
     * @covers \Fisharebest\Webtrees\Age::ageString
     * @covers \Fisharebest\Webtrees\Age::ageAtEvent
     * @covers \Fisharebest\Webtrees\Age::timeAfterDeath
     *
     * @return void
     */
    public function testOverlappingDates1(): void
    {
        $x = new Date('07 JAN 2019');
        $y = new Date('JAN 2019');
        $age = new Age($x, $y);

        self::assertSame(-6, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0', $age->ageString());
        self::assertSame('(aged 0)', $age->ageAtEvent(false));
        self::assertSame('(age 0)', $age->ageAtEvent(true));
        self::assertSame('', $age->timeAfterDeath());
    }

    /**
     * @covers \Fisharebest\Webtrees\Age::__construct
     * @covers \Fisharebest\Webtrees\Age::ageDays
     * @covers \Fisharebest\Webtrees\Age::ageYears
     * @covers \Fisharebest\Webtrees\Age::ageYearsString
     * @covers \Fisharebest\Webtrees\Age::ageString
     * @covers \Fisharebest\Webtrees\Age::ageAtEvent
     * @covers \Fisharebest\Webtrees\Age::timeAfterDeath
     *
     * @return void
     */
    public function testOverlappingDates2(): void
    {
        $x = new Date('JAN 2019');
        $y = new Date('07 JAN 2019');
        $age = new Age($x, $y);

        self::assertSame(6, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('0', $age->ageString());
        self::assertSame('(aged 0)', $age->ageAtEvent(false));
        self::assertSame('(age 0)', $age->ageAtEvent(true));
        self::assertSame('', $age->timeAfterDeath());
    }

    /**
     * @covers \Fisharebest\Webtrees\Age::__construct
     * @covers \Fisharebest\Webtrees\Age::ageDays
     * @covers \Fisharebest\Webtrees\Age::ageYears
     * @covers \Fisharebest\Webtrees\Age::ageYearsString
     * @covers \Fisharebest\Webtrees\Age::ageString
     * @covers \Fisharebest\Webtrees\Age::ageAtEvent
     * @covers \Fisharebest\Webtrees\Age::timeAfterDeath
     *
     * @return void
     */
    public function testDifferentDay(): void
    {
        $x = new Date('13 APR 2019');
        $y = new Date('27 APR 2019');
        $age = new Age($x, $y);

        self::assertSame(14, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('14 days', $age->ageString());
        self::assertSame('(aged 14 days)', $age->ageAtEvent(false));
        self::assertSame('(age 14 days)', $age->ageAtEvent(true));
        self::assertSame('(14 days after death)', $age->timeAfterDeath());
    }

    /**
     * @covers \Fisharebest\Webtrees\Age::__construct
     * @covers \Fisharebest\Webtrees\Age::ageDays
     * @covers \Fisharebest\Webtrees\Age::ageYears
     * @covers \Fisharebest\Webtrees\Age::ageYearsString
     * @covers \Fisharebest\Webtrees\Age::ageString
     * @covers \Fisharebest\Webtrees\Age::ageAtEvent
     * @covers \Fisharebest\Webtrees\Age::timeAfterDeath
     *
     * @return void
     */
    public function testDifferentMonth(): void
    {
        $x = new Date('13 APR 2019');
        $y = new Date('27 JUN 2019');
        $age = new Age($x, $y);

        self::assertSame(75, $age->ageDays());
        self::assertSame(0, $age->ageYears());
        self::assertSame('0', $age->ageYearsString());
        self::assertSame('2 months', $age->ageString());
        self::assertSame('(aged 2 months)', $age->ageAtEvent(false));
        self::assertSame('(age 2 months)', $age->ageAtEvent(true));
        self::assertSame('(2 months after death)', $age->timeAfterDeath());
    }

    /**
     * @covers \Fisharebest\Webtrees\Age::__construct
     * @covers \Fisharebest\Webtrees\Age::ageDays
     * @covers \Fisharebest\Webtrees\Age::ageYears
     * @covers \Fisharebest\Webtrees\Age::ageYearsString
     * @covers \Fisharebest\Webtrees\Age::ageString
     * @covers \Fisharebest\Webtrees\Age::ageAtEvent
     * @covers \Fisharebest\Webtrees\Age::timeAfterDeath
     *
     * @return void
     */
    public function testDifferentYear(): void
    {
        $x = new Date('13 APR 2012');
        $y = new Date('27 JUN 2019');
        $age = new Age($x, $y);

        self::assertSame(2631, $age->ageDays());
        self::assertSame(7, $age->ageYears());
        self::assertSame('7', $age->ageYearsString());
        self::assertSame('7 years', $age->ageString());
        self::assertSame('(aged 7 years)', $age->ageAtEvent(false));
        self::assertSame('(age 7 years)', $age->ageAtEvent(true));
        self::assertSame('(7 years after death)', $age->timeAfterDeath());
    }
}
