<?php
namespace Fisharebest\ExtCalendar;

use InvalidArgumentException;

/**
 * Class JulianCalendar - calculations for the Julian calendar.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014-2017 Greg Roach
 * @license   This program is free software: you can redistribute it and/or modify
 *            it under the terms of the GNU General Public License as published by
 *            the Free Software Foundation, either version 3 of the License, or
 *            (at your option) any later version.
 *
 *            This program is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *            GNU General Public License for more details.
 *
 *            You should have received a copy of the GNU General Public License
 *            along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class JulianCalendar implements CalendarInterface
{
    /**
     * Determine the number of days in a specified month, allowing for leap years, etc.
     *
     * @param int $year
     * @param int $month
     *
     * @return int
     */
    public function daysInMonth($year, $month)
    {
        if ($year === 0) {
            throw new InvalidArgumentException('Year ' . $year . ' is invalid for this calendar');
        } elseif ($month < 1 || $month > 12) {
            throw new InvalidArgumentException('Month ' . $month . ' is invalid for this calendar');
        } elseif ($month === 1 || $month === 3 || $month === 5 || $month === 7 || $month === 8 || $month === 10 || $month === 12) {
            return 31;
        } elseif ($month === 4 || $month === 6 || $month === 9 || $month === 11) {
            return 30;
        } elseif ($this->isLeapYear($year)) {
            return 29;
        } else {
            return 28;
        }
    }

    /**
     * Determine the number of days in a week.
     *
     * @return int
     */
    public function daysInWeek()
    {
        return 7;
    }

    /**
     * The escape sequence used to indicate this calendar in GEDCOM files.
     *
     * @return string
     */
    public function gedcomCalendarEscape()
    {
        return '@#DJULIAN@';
    }

    /**
     * Determine whether or not a given year is a leap-year.
     *
     * @param int $year
     *
     * @return bool
     */
    public function isLeapYear($year)
    {
        if ($year < 0) {
            $year++;
        }

        return $year % 4 == 0;
    }

    /**
     * What is the highest Julian day number that can be converted into this calendar.
     *
     * @return int
     */
    public function jdEnd()
    {
        return PHP_INT_MAX;
    }

    /**
     * What is the lowest Julian day number that can be converted into this calendar.
     *
     * @return int
     */
    public function jdStart()
    {
        return 1;
    }

    /**
     * Convert a Julian day number into a year/month/day.
     *
     * @param int $julian_day
     *
     * @return int[]
     */
    public function jdToYmd($julian_day)
    {
        $c = $julian_day + 32082;
        $d = (int) ((4 * $c + 3) / 1461);
        $e = $c - (int) (1461 * $d / 4);
        $m = (int) ((5 * $e + 2) / 153);

        $day   = $e - (int) ((153 * $m + 2) / 5) + 1;
        $month = $m + 3 - 12 * (int) ($m / 10);
        $year  = $d - 4800 + (int) ($m / 10);
        if ($year < 1) {
            // 0 is 1 BCE, -1 is 2 BCE, etc.
            $year--;
        }

        return array($year, $month, $day);
    }

    /**
     * Determine the number of months in a year (if given),
     * or the maximum number of months in any year.
     *
     * @param int|null $year
     *
     * @return int
     */
    public function monthsInYear($year = null)
    {
        return 12;
    }

    /**
     * Convert a year/month/day to a Julian day number.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return int
     */
    public function ymdToJd($year, $month, $day)
    {
        if ($month < 1 || $month > $this->monthsInYear()) {
            throw new InvalidArgumentException('Month ' . $month . ' is invalid for this calendar');
        }

        if ($year < 0) {
            // 1 BCE is 0, 2 BCE is -1, etc.
            ++$year;
        }
        $a     = (int) ((14 - $month) / 12);
        $year  = $year + 4800 - $a;
        $month = $month + 12 * $a - 3;

        return $day + (int) ((153 * $month + 2) / 5) + 365 * $year + (int) ($year / 4) - 32083;
    }

    /**
     * Get the number of days after March 21 that easter falls, for a given year.
     *
     * Uses the algorithm found in PHP’s ext/calendar/easter.c
     *
     * @param int $year
     *
     * @return int
     */
    public function easterDays($year)
    {
        // The “golden” number
        $golden = 1 + $year % 19;

        // The “dominical” number (finding a Sunday)
        $dom = ($year + (int) ($year / 4) + 5) % 7;
        if ($dom < 0) {
            $dom += 7;
        }

        // The uncorrected “Paschal full moon” date
        $pfm = (3 - 11 * $golden - 7) % 30;
        if ($pfm < 0) {
            $pfm += 30;
        }

        // The corrected “Paschal full moon” date
        if ($pfm === 29 || $pfm === 28 && $golden > 11) {
            $pfm--;
        }

        $tmp = (4 - $pfm - $dom) % 7;
        if ($tmp < 0) {
            $tmp += 7;
        }

        return $pfm + $tmp + 1;
    }
}
