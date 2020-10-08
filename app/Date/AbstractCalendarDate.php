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

namespace Fisharebest\Webtrees\Date;

use Fisharebest\ExtCalendar\CalendarInterface;
use Fisharebest\ExtCalendar\JewishCalendar;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Http\RequestHandlers\CalendarPage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use InvalidArgumentException;

use function array_key_exists;
use function array_search;
use function get_class;
use function intdiv;
use function is_array;
use function is_int;
use function max;
use function preg_match;
use function route;
use function sprintf;
use function str_contains;
use function strpbrk;
use function strtr;
use function trigger_error;
use function trim;
use function view;

use const E_USER_DEPRECATED;

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
    // GEDCOM calendar escape
    public const ESCAPE = '@#DUNKNOWN@';

    // Convert GEDCOM month names to month numbers.
    protected const MONTH_ABBREVIATIONS = [];

    /** @var CalendarInterface The calendar system used to represent this date */
    protected $calendar;

    /** @var int Year number */
    public $year;

    /** @var int Month number */
    public $month;

    /** @var int Day number */
    public $day;

    /** @var int Earliest Julian day number (start of month/year for imprecise dates) */
    private $minimum_julian_day;

    /** @var int Latest Julian day number (end of month/year for imprecise dates) */
    private $maximum_julian_day;

    /**
     * Create a date from either:
     * a Julian day number
     * day/month/year strings from a GEDCOM date
     * another CalendarDate object
     *
     * @param array<string>|int|AbstractCalendarDate $date
     */
    protected function __construct($date)
    {
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
            if (array_key_exists($date[1], static::MONTH_ABBREVIATIONS)) {
                $this->month = static::MONTH_ABBREVIATIONS[$date[1]];
            } else {
                $this->month = 0;
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
        if (get_class($this) === get_class($date)) {
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
            $today = $date->calendar->jdToYmd(Carbon::now()->julianDay());
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

    /**
     * @return int
     */
    public function maximumJulianDay(): int
    {
        return $this->maximum_julian_day;
    }

    /**
     * @return int
     */
    public function year(): int
    {
        return $this->year;
    }

    /**
     * @return int
     */
    public function month(): int
    {
        return $this->month;
    }

    /**
     * @return int
     */
    public function day(): int
    {
        return $this->day;
    }

    /**
     * @return int
     */
    public function minimumJulianDay(): int
    {
        return $this->minimum_julian_day;
    }

    /**
     * Is the current year a leap year?
     *
     * @return bool
     */
    public function isLeapYear(): bool
    {
        return $this->calendar->isLeapYear($this->year);
    }

    /**
     * Set the object’s Julian day number from a potentially incomplete year/month/day
     *
     * @return void
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
     * Full day of the week
     *
     * @param int $day_number
     *
     * @return string
     */
    public function dayNames(int $day_number): string
    {
        static $translated_day_names;

        if ($translated_day_names === null) {
            $translated_day_names = [
                0 => I18N::translate('Monday'),
                1 => I18N::translate('Tuesday'),
                2 => I18N::translate('Wednesday'),
                3 => I18N::translate('Thursday'),
                4 => I18N::translate('Friday'),
                5 => I18N::translate('Saturday'),
                6 => I18N::translate('Sunday'),
            ];
        }

        return $translated_day_names[$day_number];
    }

    /**
     * Abbreviated day of the week
     *
     * @param int $day_number
     *
     * @return string
     */
    protected function dayNamesAbbreviated(int $day_number): string
    {
        static $translated_day_names;

        if ($translated_day_names === null) {
            $translated_day_names = [
                /* I18N: abbreviation for Monday */
                0 => I18N::translate('Mon'),
                /* I18N: abbreviation for Tuesday */
                1 => I18N::translate('Tue'),
                /* I18N: abbreviation for Wednesday */
                2 => I18N::translate('Wed'),
                /* I18N: abbreviation for Thursday */
                3 => I18N::translate('Thu'),
                /* I18N: abbreviation for Friday */
                4 => I18N::translate('Fri'),
                /* I18N: abbreviation for Saturday */
                5 => I18N::translate('Sat'),
                /* I18N: abbreviation for Sunday */
                6 => I18N::translate('Sun'),
            ];
        }

        return $translated_day_names[$day_number];
    }

    /**
     * Most years are 1 more than the previous, but not always (e.g. 1BC->1AD)
     *
     * @param int $year
     *
     * @return int
     */
    protected function nextYear(int $year): int
    {
        return $year + 1;
    }

    /**
     * Calendars that use suffixes, etc. (e.g. “B.C.”) or OS/NS notation should redefine this.
     *
     * @param string $year
     *
     * @return int
     */
    protected function extractYear(string $year): int
    {
        return (int) $year;
    }

    /**
     * Compare two dates, for sorting
     *
     * @param AbstractCalendarDate $d1
     * @param AbstractCalendarDate $d2
     *
     * @return int
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
     * @param AbstractCalendarDate $date
     *
     * @return int[] Age in years/months/days
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
     * How long between an event and a given julian day
     * Return result as a number of years.
     *
     * @param int $jd date for calculation
     *
     * @return int
     *
     * @deprecated since 2.0.4.  Will be removed in 2.1.0
     */
    public function getAge(int $jd): int
    {
        trigger_error('AbstractCalendarDate::getAge() is deprecated. Use class Age instead.', E_USER_DEPRECATED);

        if ($this->year === 0 || $jd === 0) {
            return 0;
        }
        if ($this->minimum_julian_day < $jd && $this->maximum_julian_day > $jd) {
            return 0;
        }
        if ($this->minimum_julian_day === $jd) {
            return 0;
        }
        [$y, $m, $d] = $this->calendar->jdToYmd($jd);
        $dy = $y - $this->year;
        $dm = $m - max($this->month, 1);
        $dd = $d - max($this->day, 1);
        if ($dd < 0) {
            $dm--;
        }
        if ($dm < 0) {
            $dy--;
        }

        // Not a full age? Then just the years
        return $dy;
    }

    /**
     * How long between an event and a given julian day
     * Return result as a gedcom-style age string.
     *
     * @param int $jd date for calculation
     *
     * @return string
     *
     * @deprecated since 2.0.4.  Will be removed in 2.1.0
     */
    public function getAgeFull(int $jd): string
    {
        trigger_error('AbstractCalendarDate::getAge() is deprecated. Use class Age instead.', E_USER_DEPRECATED);

        if ($this->year === 0 || $jd === 0) {
            return '';
        }
        if ($this->minimum_julian_day < $jd && $this->maximum_julian_day > $jd) {
            return '';
        }
        if ($this->minimum_julian_day === $jd) {
            return '';
        }
        if ($jd < $this->minimum_julian_day) {
            return view('icons/warning');
        }
        [$y, $m, $d] = $this->calendar->jdToYmd($jd);
        $dy = $y - $this->year;
        $dm = $m - max($this->month, 1);
        $dd = $d - max($this->day, 1);
        if ($dd < 0) {
            $dm--;
        }
        if ($dm < 0) {
            $dm += $this->calendar->monthsInYear();
            $dy--;
        }
        // Age in years?
        if ($dy > 1) {
            return $dy . 'y';
        }
        $dm += $dy * $this->calendar->monthsInYear();
        // Age in months?
        if ($dm > 1) {
            return $dm . 'm';
        }

        // Age in days?
        return ($jd - $this->minimum_julian_day) . 'd';
    }

    /**
     * Convert a date from one calendar to another.
     *
     * @param string $calendar
     *
     * @return AbstractCalendarDate
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
     * Is this date within the valid range of the calendar
     *
     * @return bool
     */
    public function inValidRange(): bool
    {
        return $this->minimum_julian_day >= $this->calendar->jdStart() && $this->maximum_julian_day <= $this->calendar->jdEnd();
    }

    /**
     * How many months in a year
     *
     * @return int
     */
    public function monthsInYear(): int
    {
        return $this->calendar->monthsInYear();
    }

    /**
     * How many days in the current month
     *
     * @return int
     */
    public function daysInMonth(): int
    {
        try {
            return $this->calendar->daysInMonth($this->year, $this->month);
        } catch (InvalidArgumentException $ex) {
            // calendar.php calls this with "DD MMM" dates, for which we cannot calculate
            // the length of a month. Should we validate this before calling this function?
            return 0;
        }
    }

    /**
     * How many days in the current week
     *
     * @return int
     */
    public function daysInWeek(): int
    {
        return $this->calendar->daysInWeek();
    }

    /**
     * Format a date, using similar codes to the PHP date() function.
     *
     * @param string $format    See http://php.net/date
     * @param string $qualifier GEDCOM qualifier, so we can choose the right case for the month name.
     *
     * @return string
     */
    public function format(string $format, string $qualifier = ''): string
    {
        // Dates can include additional punctuation and symbols. e.g.
        // %F %j, %Y
        // %Y. %F %d.
        // %Y年 %n月 %j日
        // %j. %F %Y
        // Don’t show exact details or unnecessary punctuation for inexact dates.
        if ($this->day === 0) {
            $format = strtr($format, ['%d' => '', '%j日' => '', '%j,' => '', '%j' => '', '%l' => '', '%D' => '', '%N' => '', '%S' => '', '%w' => '', '%z' => '']);
        }
        if ($this->month === 0) {
            $format = strtr($format, ['%F' => '', '%m' => '', '%M' => '', '年 %n月' => '', '%n' => '', '%t' => '']);
        }
        if ($this->year === 0) {
            $format = strtr($format, ['%t' => '', '%L' => '', '%G' => '', '%y' => '', '%Y年' => '', '%Y' => '']);
        }
        $format = trim($format, ',. /-');

        if ($this->day !== 0 && preg_match('/%[djlDNSwz]/', $format)) {
            // If we have a day-number *and* we are being asked to display it, then genitive
            $case = 'GENITIVE';
        } else {
            switch ($qualifier) {
                case 'TO':
                case 'ABT':
                case 'FROM':
                    $case = 'GENITIVE';
                    break;
                case 'AFT':
                    $case = 'LOCATIVE';
                    break;
                case 'BEF':
                case 'BET':
                case 'AND':
                    $case = 'INSTRUMENTAL';
                    break;
                case '':
                case 'INT':
                case 'EST':
                case 'CAL':
                default: // There shouldn't be any other options...
                    $case = 'NOMINATIVE';
                    break;
            }
        }
        // Build up the formatted date, character at a time
        if (str_contains($format, '%d')) {
            $format = strtr($format, ['%d' => $this->formatDayZeros()]);
        }
        if (str_contains($format, '%j')) {
            $format = strtr($format, ['%j' => $this->formatDay()]);
        }
        if (str_contains($format, '%l')) {
            $format = strtr($format, ['%l' => $this->formatLongWeekday()]);
        }
        if (str_contains($format, '%D')) {
            $format = strtr($format, ['%D' => $this->formatShortWeekday()]);
        }
        if (str_contains($format, '%N')) {
            $format = strtr($format, ['%N' => $this->formatIsoWeekday()]);
        }
        if (str_contains($format, '%w')) {
            $format = strtr($format, ['%w' => $this->formatNumericWeekday()]);
        }
        if (str_contains($format, '%z')) {
            $format = strtr($format, ['%z' => $this->formatDayOfYear()]);
        }
        if (str_contains($format, '%F')) {
            $format = strtr($format, ['%F' => $this->formatLongMonth($case)]);
        }
        if (str_contains($format, '%m')) {
            $format = strtr($format, ['%m' => $this->formatMonthZeros()]);
        }
        if (str_contains($format, '%M')) {
            $format = strtr($format, ['%M' => $this->formatShortMonth()]);
        }
        if (str_contains($format, '%n')) {
            $format = strtr($format, ['%n' => $this->formatMonth()]);
        }
        if (str_contains($format, '%t')) {
            $format = strtr($format, ['%t' => (string) $this->daysInMonth()]);
        }
        if (str_contains($format, '%L')) {
            $format = strtr($format, ['%L' => $this->isLeapYear() ? '1' : '0']);
        }
        if (str_contains($format, '%Y')) {
            $format = strtr($format, ['%Y' => $this->formatLongYear()]);
        }
        if (str_contains($format, '%y')) {
            $format = strtr($format, ['%y' => $this->formatShortYear()]);
        }
        // These 4 extensions are useful for re-formatting gedcom dates.
        if (str_contains($format, '%@')) {
            $format = strtr($format, ['%@' => $this->formatGedcomCalendarEscape()]);
        }
        if (str_contains($format, '%A')) {
            $format = strtr($format, ['%A' => $this->formatGedcomDay()]);
        }
        if (str_contains($format, '%O')) {
            $format = strtr($format, ['%O' => $this->formatGedcomMonth()]);
        }
        if (str_contains($format, '%E')) {
            $format = strtr($format, ['%E' => $this->formatGedcomYear()]);
        }

        return $format;
    }

    /**
     * Generate the %d format for a date.
     *
     * @return string
     */
    protected function formatDayZeros(): string
    {
        if ($this->day > 9) {
            return I18N::digits($this->day);
        }

        return I18N::digits('0' . $this->day);
    }

    /**
     * Generate the %j format for a date.
     *
     * @return string
     */
    protected function formatDay(): string
    {
        return I18N::digits($this->day);
    }

    /**
     * Generate the %l format for a date.
     *
     * @return string
     */
    protected function formatLongWeekday(): string
    {
        return $this->dayNames($this->minimum_julian_day % $this->calendar->daysInWeek());
    }

    /**
     * Generate the %D format for a date.
     *
     * @return string
     */
    protected function formatShortWeekday(): string
    {
        return $this->dayNamesAbbreviated($this->minimum_julian_day % $this->calendar->daysInWeek());
    }

    /**
     * Generate the %N format for a date.
     *
     * @return string
     */
    protected function formatIsoWeekday(): string
    {
        return I18N::digits($this->minimum_julian_day % 7 + 1);
    }

    /**
     * Generate the %w format for a date.
     *
     * @return string
     */
    protected function formatNumericWeekday(): string
    {
        return I18N::digits(($this->minimum_julian_day + 1) % $this->calendar->daysInWeek());
    }

    /**
     * Generate the %z format for a date.
     *
     * @return string
     */
    protected function formatDayOfYear(): string
    {
        return I18N::digits($this->minimum_julian_day - $this->calendar->ymdToJd($this->year, 1, 1));
    }

    /**
     * Generate the %n format for a date.
     *
     * @return string
     */
    protected function formatMonth(): string
    {
        return I18N::digits($this->month);
    }

    /**
     * Generate the %m format for a date.
     *
     * @return string
     */
    protected function formatMonthZeros(): string
    {
        if ($this->month > 9) {
            return I18N::digits($this->month);
        }

        return I18N::digits('0' . $this->month);
    }

    /**
     * Generate the %F format for a date.
     *
     * @param string $case Which grammatical case shall we use
     *
     * @return string
     */
    protected function formatLongMonth($case = 'NOMINATIVE'): string
    {
        switch ($case) {
            case 'GENITIVE':
                return $this->monthNameGenitiveCase($this->month, $this->isLeapYear());
            case 'NOMINATIVE':
                return $this->monthNameNominativeCase($this->month, $this->isLeapYear());
            case 'LOCATIVE':
                return $this->monthNameLocativeCase($this->month, $this->isLeapYear());
            case 'INSTRUMENTAL':
                return $this->monthNameInstrumentalCase($this->month, $this->isLeapYear());
            default:
                throw new InvalidArgumentException($case);
        }
    }

    /**
     * Full month name in genitive case.
     *
     * @param int  $month
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    abstract protected function monthNameGenitiveCase(int $month, bool $leap_year): string;

    /**
     * Full month name in nominative case.
     *
     * @param int  $month
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    abstract protected function monthNameNominativeCase(int $month, bool $leap_year): string;

    /**
     * Full month name in locative case.
     *
     * @param int  $month
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    abstract protected function monthNameLocativeCase(int $month, bool $leap_year): string;

    /**
     * Full month name in instrumental case.
     *
     * @param int  $month
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    abstract protected function monthNameInstrumentalCase(int $month, bool $leap_year): string;

    /**
     * Abbreviated month name
     *
     * @param int  $month
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    abstract protected function monthNameAbbreviated(int $month, bool $leap_year): string;

    /**
     * Generate the %M format for a date.
     *
     * @return string
     */
    protected function formatShortMonth(): string
    {
        return $this->monthNameAbbreviated($this->month, $this->isLeapYear());
    }

    /**
     * Generate the %y format for a date.
     * NOTE Short year is NOT a 2-digit year. It is for calendars such as hebrew
     * which have a 3-digit form of 4-digit years.
     *
     * @return string
     */
    protected function formatShortYear(): string
    {
        return $this->formatLongYear();
    }

    /**
     * Generate the %A format for a date.
     *
     * @return string
     */
    protected function formatGedcomDay(): string
    {
        if ($this->day === 0) {
            return '';
        }

        return sprintf('%02d', $this->day);
    }

    /**
     * Generate the %O format for a date.
     *
     * @return string
     */
    protected function formatGedcomMonth(): string
    {
        // Our simple lookup table doesn't work correctly for Adar on leap years
        if ($this->month === 7 && $this->calendar instanceof JewishCalendar && !$this->calendar->isLeapYear($this->year)) {
            return 'ADR';
        }

        return array_search($this->month, static::MONTH_ABBREVIATIONS, true);
    }

    /**
     * Generate the %E format for a date.
     *
     * @return string
     */
    protected function formatGedcomYear(): string
    {
        if ($this->year === 0) {
            return '';
        }

        return sprintf('%04d', $this->year);
    }

    /**
     * Generate the %@ format for a calendar escape.
     *
     * @return string
     */
    protected function formatGedcomCalendarEscape(): string
    {
        return static::ESCAPE;
    }

    /**
     * Generate the %Y format for a date.
     *
     * @return string
     */
    protected function formatLongYear(): string
    {
        return I18N::digits($this->year);
    }

    /**
     * Which months follows this one? Calendars with leap-months should provide their own implementation.
     *
     * @return int[]
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
     * @return int[]
     */
    public function todayYmd(): array
    {
        return $this->calendar->jdToYmd(Carbon::now()->julianDay());
    }

    /**
     * Convert to today’s date.
     *
     * @return AbstractCalendarDate
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

    /**
     * Create a URL that links this date to the WT calendar
     *
     * @param string $date_format
     * @param Tree   $tree
     *
     * @return string
     */
    public function calendarUrl(string $date_format, Tree $tree): string
    {
        if ($this->day !== 0 && strpbrk($date_format, 'dDj')) {
            // If the format includes a day, and the date also includes a day, then use the day view
            $view = 'day';
        } elseif ($this->month !== 0 && strpbrk($date_format, 'FMmn')) {
            // If the format includes a month, and the date also includes a month, then use the month view
            $view = 'month';
        } else {
            // Use the year view
            $view = 'year';
        }

        return route(CalendarPage::class, [
            'cal'   => $this->calendar->gedcomCalendarEscape(),
            'year'  => $this->formatGedcomYear(),
            'month' => $this->formatGedcomMonth(),
            'day'   => $this->formatGedcomDay(),
            'view'  => $view,
            'tree'  => $tree->name(),
        ]);
    }
}
