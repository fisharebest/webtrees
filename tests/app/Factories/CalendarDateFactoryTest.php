<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

/**
 * Test harness for the class CalendarDateFactory
 *
 * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory
 */
class CalendarDateFactoryTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testEmptyDate(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('');

        $this->assertSame(GregorianDate::ESCAPE, $date->format('%@'));
        $this->assertSame(0, $date->year);
        $this->assertSame(0, $date->month);
        $this->assertSame(0, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
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
            $this->assertSame($calendar_escape, $date->format('%@'));
            $this->assertSame(0, $date->year);
            $this->assertSame(0, $date->month);
            $this->assertSame(0, $date->day);
        }
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testInvalidCalendarEscapeIgnored(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('@#DSTARDATE@');
        $this->assertSame('@#DGREGORIAN@', $date->format('%@'));
        $this->assertSame(0, $date->year);
        $this->assertSame(0, $date->month);
        $this->assertSame(0, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testDayMonthAndYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('01 JAN 1970');
        $this->assertSame('@#DGREGORIAN@', $date->format('%@'));
        $this->assertSame(1970, $date->year);
        $this->assertSame(1, $date->month);
        $this->assertSame(1, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testMonthAndYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('JAN 1970');
        $this->assertSame('@#DGREGORIAN@', $date->format('%@'));
        $this->assertSame(1970, $date->year);
        $this->assertSame(1, $date->month);
        $this->assertSame(0, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('1970');
        $this->assertSame('@#DGREGORIAN@', $date->format('%@'));
        $this->assertSame(1970, $date->year);
        $this->assertSame(0, $date->month);
        $this->assertSame(0, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testExtractedYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('THE MID 1960S');
        $this->assertSame('@#DGREGORIAN@', $date->format('%@'));
        $this->assertSame(1960, $date->year);
        $this->assertSame(0, $date->month);
        $this->assertSame(0, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testExtractedMonthAndYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('PERHAPS FEB OR MAR IN 1960 or 1961');
        $this->assertSame('@#DGREGORIAN@', $date->format('%@'));
        $this->assertSame(1960, $date->year);
        $this->assertSame(2, $date->month);
        $this->assertSame(0, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testExtractedDayMonthAndYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('PERHAPS 11 OR 12 FEB OR MAR IN 1960 or 1961');
        $this->assertSame('@#DGREGORIAN@', $date->format('%@'));
        $this->assertSame(1960, $date->year);
        $this->assertSame(2, $date->month);
        $this->assertSame(11, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testExtractedMonth(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('PERHAPS FEB OR MAR');
        $this->assertSame('@#DGREGORIAN@', $date->format('%@'));
        $this->assertSame(0, $date->year);
        $this->assertSame(2, $date->month);
        $this->assertSame(0, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testExtractedDayAndMonth(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('PERHAPS 11 OR 12 FEB OR MAR');
        $this->assertSame('@#DGREGORIAN@', $date->format('%@'));
        $this->assertSame(0, $date->year);
        $this->assertSame(2, $date->month);
        $this->assertSame(11, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testUnambiguousOverrideWithHebrewMonth(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('@#DGREGORIAN@ 10 NSN 5432');
        $this->assertSame('@#DHEBREW@', $date->format('%@'));
        $this->assertSame(5432, $date->year);
        $this->assertSame(8, $date->month);
        $this->assertSame(10, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testUnambiguousOverrideWithFrenchMonth(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('@#DGREGORIAN@ 10 PLUV 11');
        $this->assertSame('@#DFRENCH R@', $date->format('%@'));
        $this->assertSame(11, $date->year);
        $this->assertSame(5, $date->month);
        $this->assertSame(10, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testUnambiguousOverrideWithHijriMonth(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('@#DGREGORIAN@ 10 SHAAB 1234');
        $this->assertSame('@#DHIJRI@', $date->format('%@'));
        $this->assertSame(1234, $date->year);
        $this->assertSame(8, $date->month);
        $this->assertSame(10, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testUnambiguousOverrideWithJalaliMonth(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('@#DGREGORIAN@ 10 BAHMA 1234');
        $this->assertSame('@#DJALALI@', $date->format('%@'));
        $this->assertSame(1234, $date->year);
        $this->assertSame(11, $date->month);
        $this->assertSame(10, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testUnambiguousOverrideWithJulianBCYear(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('@#DGREGORIAN@ 10 AUG 44 B.C.');
        $this->assertSame('@#DJULIAN@', $date->format('%@'));
        $this->assertSame(-44, $date->year);
        $this->assertSame(8, $date->month);
        $this->assertSame(10, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::make
     */
    public function testUnambiguousYearWithNoCalendar(): void
    {
        $factory = new CalendarDateFactory();

        $date = $factory->make('3456');
        $this->assertSame('@#DHEBREW@', $date->format('%@'));
        $this->assertSame(3456, $date->year);
        $this->assertSame(0, $date->month);
        $this->assertSame(0, $date->day);
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\CalendarDateFactory::supportedCalendars
     */
    public function testSupportedCalendars(): void
    {
        $factory = new CalendarDateFactory();

        $calendars = $factory->supportedCalendars();

        $this->assertIsArray($calendars);
    }
}
