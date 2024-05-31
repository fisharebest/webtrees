<?php

/**
 * Class ArabicCalendar - calculations for the Arabic (Hijri) calendar.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2014-2021 Greg Roach
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

namespace Fisharebest\ExtCalendar;

use InvalidArgumentException;

class ArabicCalendar implements CalendarInterface
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
        if ($month === 2) {
            return 28;
        }

        if ($month % 2 === 1 || $month === 12 && $this->isLeapYear($year)) {
            return 30;
        }

        return 29;
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
        return '@#DHIJRI@';
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
        return ((11 * $year + 14) % 30) < 11;
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
        return 1948440; // 1 Muharram 1 AH, 16 July 622 AD
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
        $year  = (int) ((30 * ($julian_day - 1948440) + 10646) / 10631);
        $month = (int) ((11 * ($julian_day - $year * 354 - (int) ((3 + 11 * $year) / 30) - 1948086) + 330) / 325);
        $day   = $julian_day - 29 * ($month - 1) - (int) ((6 * $month - 1) / 11) - $year * 354 - (int) ((3 + 11 * $year) / 30) - 1948085;

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

        return $day + 29 * ($month - 1) + (int) ((6 * $month - 1) / 11) + $year * 354 + (int) ((3 + 11 * $year) / 30) + 1948085;
    }
}
