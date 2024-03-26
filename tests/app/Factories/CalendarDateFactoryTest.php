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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Date\FrenchDate;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\HijriDate;
use Fisharebest\Webtrees\Date\JalaliDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Date\JulianDate;
use Fisharebest\Webtrees\Date\RomanDate;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CalendarDateFactory::class)]
class CalendarDateFactoryTest extends TestCase
{
    public function testEmptyDate(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('');

        static::assertSame(GregorianDate::ESCAPE, $date->format('%@'));
        static::assertSame(0, $date->year);
        static::assertSame(0, $date->month);
        static::assertSame(0, $date->day);
    }

    public function testValidCalendarEscape(): void
    {
        $factory = new CalendarDateFactory();

        $calendar_escapes = [
            FrenchDate::ESCAPE,
            GregorianDate::ESCAPE,
            HijriDate::ESCAPE,
            JalaliDate::ESCAPE,
            JewishDate::ESCAPE,
            JulianDate::ESCAPE,
            RomanDate::ESCAPE,
        ];

        foreach ($calendar_escapes as $calendar_escape) {
            $date = $factory->make($calendar_escape);
            static::assertSame($calendar_escape, $date->format('%@'));
            static::assertSame(0, $date->year);
            static::assertSame(0, $date->month);
            static::assertSame(0, $date->day);
        }
    }

    public function testInvalidCalendarEscapeIgnored(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('@#DSTARDATE@');
        static::assertSame('@#DGREGORIAN@', $date->format('%@'));
        static::assertSame(0, $date->year);
        static::assertSame(0, $date->month);
        static::assertSame(0, $date->day);
    }

    public function testDayMonthAndYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('01 JAN 1970');
        static::assertSame('@#DGREGORIAN@', $date->format('%@'));
        static::assertSame(1970, $date->year);
        static::assertSame(1, $date->month);
        static::assertSame(1, $date->day);
    }

    public function testMonthAndYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('JAN 1970');
        static::assertSame('@#DGREGORIAN@', $date->format('%@'));
        static::assertSame(1970, $date->year);
        static::assertSame(1, $date->month);
        static::assertSame(0, $date->day);
    }

    public function testYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('1970');
        static::assertSame('@#DGREGORIAN@', $date->format('%@'));
        static::assertSame(1970, $date->year);
        static::assertSame(0, $date->month);
        static::assertSame(0, $date->day);
    }

    public function testExtractedYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('THE MID 1960S');
        static::assertSame('@#DGREGORIAN@', $date->format('%@'));
        static::assertSame(1960, $date->year);
        static::assertSame(0, $date->month);
        static::assertSame(0, $date->day);
    }

    public function testExtractedMonthAndYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('PERHAPS FEB OR MAR IN 1960 or 1961');
        static::assertSame('@#DGREGORIAN@', $date->format('%@'));
        static::assertSame(1960, $date->year);
        static::assertSame(2, $date->month);
        static::assertSame(0, $date->day);
    }

    public function testExtractedDayMonthAndYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('PERHAPS 11 OR 12 FEB OR MAR IN 1960 or 1961');
        static::assertSame('@#DGREGORIAN@', $date->format('%@'));
        static::assertSame(1960, $date->year);
        static::assertSame(2, $date->month);
        static::assertSame(11, $date->day);
    }

    public function testExtractedMonth(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('PERHAPS FEB OR MAR');
        static::assertSame('@#DGREGORIAN@', $date->format('%@'));
        static::assertSame(0, $date->year);
        static::assertSame(2, $date->month);
        static::assertSame(0, $date->day);
    }

    public function testExtractedDayAndMonth(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('PERHAPS 11 OR 12 FEB OR MAR');
        static::assertSame('@#DGREGORIAN@', $date->format('%@'));
        static::assertSame(0, $date->year);
        static::assertSame(2, $date->month);
        static::assertSame(11, $date->day);
    }

    public function testUnambiguousOverrideWithHebrewMonth(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('@#DGREGORIAN@ 10 NSN 5432');
        static::assertSame('@#DHEBREW@', $date->format('%@'));
        static::assertSame(5432, $date->year);
        static::assertSame(8, $date->month);
        static::assertSame(10, $date->day);
    }

    public function testUnambiguousOverrideWithFrenchMonth(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('@#DGREGORIAN@ 10 PLUV 11');
        static::assertSame('@#DFRENCH R@', $date->format('%@'));
        static::assertSame(11, $date->year);
        static::assertSame(5, $date->month);
        static::assertSame(10, $date->day);
    }

    public function testUnambiguousOverrideWithHijriMonth(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('@#DGREGORIAN@ 10 SHAAB 1234');
        static::assertSame('@#DHIJRI@', $date->format('%@'));
        static::assertSame(1234, $date->year);
        static::assertSame(8, $date->month);
        static::assertSame(10, $date->day);
    }

    public function testUnambiguousOverrideWithJalaliMonth(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('@#DGREGORIAN@ 10 BAHMA 1234');
        static::assertSame('@#DJALALI@', $date->format('%@'));
        static::assertSame(1234, $date->year);
        static::assertSame(11, $date->month);
        static::assertSame(10, $date->day);
    }

    public function testUnambiguousOverrideWithJulianBCYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('@#DGREGORIAN@ 10 AUG 44 B.C.');
        static::assertSame('@#DJULIAN@', $date->format('%@'));
        static::assertSame(-44, $date->year);
        static::assertSame(8, $date->month);
        static::assertSame(10, $date->day);
    }

    public function testUnambiguousYearWithNoCalendar(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('3456');
        static::assertSame('@#DHEBREW@', $date->format('%@'));
        static::assertSame(3456, $date->year);
        static::assertSame(0, $date->month);
        static::assertSame(0, $date->day);
    }

    public function testSupportedCalendars(): void
    {
        $factory = new CalendarDateFactory();

        $calendars = $factory->supportedCalendars();

        static::assertNotEmpty($calendars);
    }
}
