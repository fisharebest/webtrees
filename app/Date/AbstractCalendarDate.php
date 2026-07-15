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

namespace Fisharebest\Webtrees\Date;

use Fisharebest\ExtCalendar\CalendarInterface;
use Fisharebest\ExtCalendar\JewishCalendar;
use Fisharebest\Webtrees\Enums\CalendarEscape;
use Fisharebest\Webtrees\Registry;
use InvalidArgumentException;

use function intdiv;
use function is_array;
use function is_int;

/**
 * Classes for Gedcom Date/Calendar functionality.
 *
 * CalendarDate is a base class for classes such as GregorianDate, etc.
 * + All supported calendars have non-zero days/months/years.
 * + We store dates as both Y/M/D and Julian Days.
 * + For imprecise dates such as "JAN 2000" we store the start/end julian day.
 *
 * NOTE: Since different calendars start their days at different times, (civil
 * midnight, solar midnight, sunset, sunrise, etc.), we convert on the basis of
 * midday.
 */
abstract class AbstractCalendarDate
{
    /** @var array<string,int>  */
    public const array MONTH_TO_NUMBER = [];

    /** @var array<int,string>  */
    public const array NUMBER_TO_MONTH = [];

    public int $year;

    public int $month;

    public int $day;

    private int $minimum_julian_day;

    private int $maximum_julian_day;

    // For dates recorded in new-style/old-style format, e.g. 2 FEB 1743/44
    // Only used by Julian dates.
    public bool $new_old_style = false;

    /**
     * Create a date from either:
     * a Julian day number
     * day/month/year strings from a GEDCOM date
     * another CalendarDate object
     *
     * @param array<string>|int|AbstractCalendarDate $date
     */
    protected function __construct(
        array|int|AbstractCalendarDate $date,
        private CalendarInterface $calendar,
        private CalendarEscape $calendar_escape,
    ) {
        // Construct from an integer (a julian day number)
        if (is_int($date)) {
            $this->minimum_julian_day = $date;
            $this->maximum_julian_day = $date;
            [$this->year, $this->month, $this->day] = $this->calendar->jdToYmd($date);

            return;
        }

        // Construct from an array (of three gedcom-style strings: "1900", "FEB", "4")
        if (is_array($date)) {
            $this->day = (int) $date[2];
            $this->month = static::MONTH_TO_NUMBER[$date[1]] ?? 0;

            if ($this->month === 0) {
                $this->day   = 0;
            }

            $this->year = $this->extractYear($date[0]);

            // Our simple lookup table above does not take into account Adar and leap-years.
            if ($this->month === 6 && $this->calendar instanceof JewishCalendar && !$this->calendar->isLeapYear($this->year)) {
                $this->month = 7;
            }

            $this->setJdFromYmd();

            return;
        }

        // Construct from a CalendarDate
        $this->minimum_julian_day = $date->minimum_julian_day;
        $this->maximum_julian_day = $date->maximum_julian_day;

        // Construct from an equivalent xxxxDate object
        if ($this->calendar_escape === $date->calendar_escape) {
            $this->year  = $date->year;
            $this->month = $date->month;
            $this->day   = $date->day;

            return;
        }

        // Not all dates can be converted
        if (!$this->inValidRange()) {
            $this->year  = 0;
            $this->month = 0;
            $this->day   = 0;

            return;
        }

        // ...else construct an inequivalent xxxxDate object
        if ($date->year === 0) {
            // Incomplete date - convert on basis of anniversary in current year
            $today = $date->calendar->jdToYmd(Registry::timestampFactory()->now()->julianDay());
            $jd    = $date->calendar->ymdToJd($today[0], $date->month, $date->day === 0 ? $today[2] : $date->day);
        } else {
            // Complete date
            $jd = intdiv($date->maximum_julian_day + $date->minimum_julian_day, 2);
        }
        [$this->year, $this->month, $this->day] = $this->calendar->jdToYmd($jd);
        // New date has same precision as original date
        if ($date->year === 0) {
            $this->year = 0;
        }
        if ($date->month === 0) {
            $this->month = 0;
        }
        if ($date->day === 0) {
            $this->day = 0;
        }
        $this->setJdFromYmd();
    }

    public function calendarEscape(): CalendarEscape
    {
        return $this->calendar_escape;
    }

    public function maximumJulianDay(): int
    {
        return $this->maximum_julian_day;
    }

    public function year(): int
    {
        return $this->year;
    }

    public function month(): int
    {
        return $this->month;
    }

    public function gedcomMonth(): string
    {
        return static::NUMBER_TO_MONTH[$this->month];
    }

    public function day(): int
    {
        return $this->day;
    }

    public function minimumJulianDay(): int
    {
        return $this->minimum_julian_day;
    }

    /**
     * Is the current year a leap year?
     */
    public function isLeapYear(): bool
    {
        return $this->calendar->isLeapYear($this->year);
    }

    /**
     * Set the object’s Julian day number from a potentially incomplete year/month/day
     */
    public function setJdFromYmd(): void
    {
        if ($this->year === 0) {
            $this->minimum_julian_day = 0;
            $this->maximum_julian_day = 0;
        } elseif ($this->month === 0) {
            $this->minimum_julian_day = $this->calendar->ymdToJd($this->year, 1, 1);
            $this->maximum_julian_day = $this->calendar->ymdToJd($this->nextYear($this->year), 1, 1) - 1;
        } elseif ($this->day === 0) {
            [$ny, $nm] = $this->nextMonth();
            $this->minimum_julian_day = $this->calendar->ymdToJd($this->year, $this->month, 1);
            $this->maximum_julian_day = $this->calendar->ymdToJd($ny, $nm, 1) - 1;
        } else {
            $this->minimum_julian_day = $this->calendar->ymdToJd($this->year, $this->month, $this->day);
            $this->maximum_julian_day = $this->minimum_julian_day;
        }
    }

    /**
     * Most years are 1 more than the previous, but not always (e.g. 1BC->1AD)
     */
    protected function nextYear(int $year): int
    {
        return $year + 1;
    }

    /**
     * Calendars that use suffixes, etc. (e.g. “B.C.”) or OS/NS notation should redefine this.
     */
    protected function extractYear(string $year): int
    {
        return (int) $year;
    }

    /**
     * Compare two dates, for sorting
     */
    public static function compare(AbstractCalendarDate $d1, AbstractCalendarDate $d2): int
    {
        if ($d1->maximum_julian_day < $d2->minimum_julian_day) {
            return -1;
        }

        if ($d2->maximum_julian_day < $d1->minimum_julian_day) {
            return 1;
        }

        return 0;
    }

    /**
     * Calculate the years/months/days between this date and another date.
     * Results assume you add the days first, then the months.
     * 4 February -> 3 July is 27 days (3 March) and 4 months.
     * It is not 4 months (4 June) and 29 days.
     *
     *
     * @return array<int> Age in years/months/days
     */
    public function ageDifference(AbstractCalendarDate $date): array
    {
        // Incomplete dates
        if ($this->year === 0 || $date->year === 0) {
            return [-1, -1, -1];
        }

        // Overlapping dates
        if (self::compare($this, $date) === 0) {
            return [0, 0, 0];
        }

        // Perform all calculations using the calendar of the first date
        [$year1, $month1, $day1] = $this->calendar->jdToYmd($this->minimum_julian_day);
        [$year2, $month2, $day2] = $this->calendar->jdToYmd($date->minimum_julian_day);

        $years  = $year2 - $year1;
        $months = $month2 - $month1;
        $days   = $day2 - $day1;

        if ($days < 0) {
            $days += $this->calendar->daysInMonth($year1, $month1);
            $months--;
        }

        if ($months < 0) {
            $months += $this->calendar->monthsInYear($year2);
            $years--;
        }

        return [$years, $months, $days];
    }

    /**
     * Convert a date from one calendar to another.
     */
    public function convertToCalendar(string $calendar): AbstractCalendarDate
    {
        switch ($calendar) {
            case 'gregorian':
                return new GregorianDate($this);
            case 'julian':
                return new JulianDate($this);
            case 'jewish':
                return new JewishDate($this);
            case 'french':
                return new FrenchDate($this);
            case 'hijri':
                return new HijriDate($this);
            case 'jalali':
                return new JalaliDate($this);
            default:
                return $this;
        }
    }

    /**
     * Is this date within the valid range of the calendar?
     */
    public function inValidRange(): bool
    {
        return $this->minimum_julian_day >= $this->calendar->jdStart() && $this->maximum_julian_day <= $this->calendar->jdEnd();
    }

    /**
     * How many months in a year
     */
    public function monthsInYear(): int
    {
        return $this->calendar->monthsInYear();
    }

    /**
     * How many days in the current month
     */
    public function daysInMonth(): int
    {
        try {
            return $this->calendar->daysInMonth($this->year, $this->month);
        } catch (InvalidArgumentException) {
            // calendar.php calls this with "DD MMM" dates, for which we cannot calculate
            // the length of a month. Should we validate this before calling this function?
            return 0;
        }
    }

    /**
     * How many days in the current week
     */
    public function daysInWeek(): int
    {
        return $this->calendar->daysInWeek();
    }

    /**
     * Which month follows this one? Calendars with leap-months should provide their own implementation.
     *
     * @return array<int>
     */
    protected function nextMonth(): array
    {
        return [
            $this->month === $this->calendar->monthsInYear() ? $this->nextYear($this->year) : $this->year,
            $this->month % $this->calendar->monthsInYear() + 1,
        ];
    }

    /**
     * Get today’s date in the current calendar.
     *
     * @return array<int>
     */
    public function todayYmd(): array
    {
        return $this->calendar->jdToYmd(Registry::timestampFactory()->now()->julianDay());
    }

    /**
     * Convert to today’s date.
     */
    public function today(): AbstractCalendarDate
    {
        $tmp        = clone $this;
        $ymd        = $tmp->todayYmd();
        $tmp->year  = $ymd[0];
        $tmp->month = $ymd[1];
        $tmp->day   = $ymd[2];
        $tmp->setJdFromYmd();

        return $tmp;
    }
}
