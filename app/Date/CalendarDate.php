<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\I18N;

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
class CalendarDate
{
    /** @var int[] Convert GEDCOM month names to month numbers */
    public static $MONTH_ABBREV = [
        ''    => 0,
        'JAN' => 1,
        'FEB' => 2,
        'MAR' => 3,
        'APR' => 4,
        'MAY' => 5,
        'JUN' => 6,
        'JUL' => 7,
        'AUG' => 8,
        'SEP' => 9,
        'OCT' => 10,
        'NOV' => 11,
        'DEC' => 12,
    ];

    /** @var string[] Convert numbers to/from roman numerals */
    protected static $roman_numerals = [
        1000 => 'M',
        900  => 'CM',
        500  => 'D',
        400  => 'CD',
        100  => 'C',
        90   => 'XC',
        50   => 'L',
        40   => 'XL',
        10   => 'X',
        9    => 'IX',
        5    => 'V',
        4    => 'IV',
        1    => 'I',
    ];

    /** @var CalendarInterface The calendar system used to represent this date */
    protected $calendar;

    /** @var int Year number */
    public $y;

    /** @var int Month number */
    public $m;

    /** @var int Day number */
    public $d;

    /** @var int Earliest Julian day number (start of month/year for imprecise dates) */
    public $minJD;

    /** @var int Latest Julian day number (end of month/year for imprecise dates) */
    public $maxJD;

    /**
     * Create a date from either:
     * a Julian day number
     * day/month/year strings from a GEDCOM date
     * another CalendarDate object
     *
     * @param array|int|CalendarDate $date
     */
    protected function __construct($date)
    {
        // Construct from an integer (a julian day number)
        if (is_int($date)) {
            $this->minJD = $date;
            $this->maxJD = $date;
            list($this->y, $this->m, $this->d) = $this->calendar->jdToYmd($date);

            return;
        }

        // Construct from an array (of three gedcom-style strings: "1900", "FEB", "4")
        if (is_array($date)) {
            $this->d = (int) $date[2];
            if (array_key_exists($date[1], static::$MONTH_ABBREV)) {
                $this->m = static::$MONTH_ABBREV[$date[1]];
            } else {
                $this->m = 0;
                $this->d = 0;
            }
            $this->y = $this->extractYear($date[0]);

            // Our simple lookup table above does not take into account Adar and leap-years.
            if ($this->m === 6 && $this->calendar instanceof JewishCalendar && !$this->calendar->isLeapYear($this->y)) {
                $this->m = 7;
            }

            $this->setJdFromYmd();

            return;
        }

        // Contruct from a CalendarDate
        $this->minJD = $date->minJD;
        $this->maxJD = $date->maxJD;

        // Construct from an equivalent xxxxDate object
        if (get_class($this) == get_class($date)) {
            $this->y = $date->y;
            $this->m = $date->m;
            $this->d = $date->d;

            return;
        }

        // Not all dates can be converted
        if (!$this->inValidRange()) {
            $this->y = 0;
            $this->m = 0;
            $this->d = 0;

            return;
        }

        // ...else construct an inequivalent xxxxDate object
        if ($date->y == 0) {
            // Incomplete date - convert on basis of anniversary in current year
            $today = $date->calendar->jdToYmd(unixtojd());
            $jd    = $date->calendar->ymdToJd($today[0], $date->m, $date->d == 0 ? $today[2] : $date->d);
        } else {
            // Complete date
            $jd = (int) (($date->maxJD + $date->minJD) / 2);
        }
        list($this->y, $this->m, $this->d) = $this->calendar->jdToYmd($jd);
        // New date has same precision as original date
        if ($date->y == 0) {
            $this->y = 0;
        }
        if ($date->m == 0) {
            $this->m = 0;
        }
        if ($date->d == 0) {
            $this->d = 0;
        }
        $this->setJdFromYmd();
    }

    /**
     * Is the current year a leap year?
     *
     * @return bool
     */
    public function isLeapYear(): bool
    {
        return $this->calendar->isLeapYear($this->y);
    }

    /**
     * Set the object’s Julian day number from a potentially incomplete year/month/day
     *
     * @return void
     */
    public function setJdFromYmd()
    {
        if ($this->y == 0) {
            $this->minJD = 0;
            $this->maxJD = 0;
        } elseif ($this->m == 0) {
            $this->minJD = $this->calendar->ymdToJd($this->y, 1, 1);
            $this->maxJD = $this->calendar->ymdToJd($this->nextYear($this->y), 1, 1) - 1;
        } elseif ($this->d == 0) {
            list($ny, $nm) = $this->nextMonth();
            $this->minJD = $this->calendar->ymdToJd($this->y, $this->m, 1);
            $this->maxJD = $this->calendar->ymdToJd($ny, $nm, 1) - 1;
        } else {
            $this->minJD = $this->calendar->ymdToJd($this->y, $this->m, $this->d);
            $this->maxJD = $this->minJD;
        }
    }

    /**
     * Full month name in nominative case.
     *
     * We put these in the base class, to save duplicating it in the Julian and Gregorian calendars.
     *
     * @param int  $month_number
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameNominativeCase(int $month_number, bool $leap_year): string
    {
        static $translated_month_names;

        if ($translated_month_names === null) {
            $translated_month_names = [
                0  => '',
                1  => I18N::translateContext('NOMINATIVE', 'January'),
                2  => I18N::translateContext('NOMINATIVE', 'February'),
                3  => I18N::translateContext('NOMINATIVE', 'March'),
                4  => I18N::translateContext('NOMINATIVE', 'April'),
                5  => I18N::translateContext('NOMINATIVE', 'May'),
                6  => I18N::translateContext('NOMINATIVE', 'June'),
                7  => I18N::translateContext('NOMINATIVE', 'July'),
                8  => I18N::translateContext('NOMINATIVE', 'August'),
                9  => I18N::translateContext('NOMINATIVE', 'September'),
                10 => I18N::translateContext('NOMINATIVE', 'October'),
                11 => I18N::translateContext('NOMINATIVE', 'November'),
                12 => I18N::translateContext('NOMINATIVE', 'December'),
            ];
        }

        return $translated_month_names[$month_number];
    }

    /**
     * Full month name in genitive case.
     *
     * We put these in the base class, to save duplicating it in the Julian and Gregorian calendars.
     *
     * @param int  $month_number
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameGenitiveCase(int $month_number, bool $leap_year): string
    {
        static $translated_month_names;

        if ($translated_month_names === null) {
            $translated_month_names = [
                0  => '',
                1  => I18N::translateContext('GENITIVE', 'January'),
                2  => I18N::translateContext('GENITIVE', 'February'),
                3  => I18N::translateContext('GENITIVE', 'March'),
                4  => I18N::translateContext('GENITIVE', 'April'),
                5  => I18N::translateContext('GENITIVE', 'May'),
                6  => I18N::translateContext('GENITIVE', 'June'),
                7  => I18N::translateContext('GENITIVE', 'July'),
                8  => I18N::translateContext('GENITIVE', 'August'),
                9  => I18N::translateContext('GENITIVE', 'September'),
                10 => I18N::translateContext('GENITIVE', 'October'),
                11 => I18N::translateContext('GENITIVE', 'November'),
                12 => I18N::translateContext('GENITIVE', 'December'),
            ];
        }

        return $translated_month_names[$month_number];
    }

    /**
     * Full month name in locative case.
     *
     * We put these in the base class, to save duplicating it in the Julian and Gregorian calendars.
     *
     * @param int  $month_number
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameLocativeCase(int $month_number, bool $leap_year): string
    {
        static $translated_month_names;

        if ($translated_month_names === null) {
            $translated_month_names = [
                0  => '',
                1  => I18N::translateContext('LOCATIVE', 'January'),
                2  => I18N::translateContext('LOCATIVE', 'February'),
                3  => I18N::translateContext('LOCATIVE', 'March'),
                4  => I18N::translateContext('LOCATIVE', 'April'),
                5  => I18N::translateContext('LOCATIVE', 'May'),
                6  => I18N::translateContext('LOCATIVE', 'June'),
                7  => I18N::translateContext('LOCATIVE', 'July'),
                8  => I18N::translateContext('LOCATIVE', 'August'),
                9  => I18N::translateContext('LOCATIVE', 'September'),
                10 => I18N::translateContext('LOCATIVE', 'October'),
                11 => I18N::translateContext('LOCATIVE', 'November'),
                12 => I18N::translateContext('LOCATIVE', 'December'),
            ];
        }

        return $translated_month_names[$month_number];
    }

    /**
     * Full month name in instrumental case.
     *
     * We put these in the base class, to save duplicating it in the Julian and Gregorian calendars.
     *
     * @param int  $month_number
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameInstrumentalCase(int $month_number, bool $leap_year): string
    {
        static $translated_month_names;

        if ($translated_month_names === null) {
            $translated_month_names = [
                0  => '',
                1  => I18N::translateContext('INSTRUMENTAL', 'January'),
                2  => I18N::translateContext('INSTRUMENTAL', 'February'),
                3  => I18N::translateContext('INSTRUMENTAL', 'March'),
                4  => I18N::translateContext('INSTRUMENTAL', 'April'),
                5  => I18N::translateContext('INSTRUMENTAL', 'May'),
                6  => I18N::translateContext('INSTRUMENTAL', 'June'),
                7  => I18N::translateContext('INSTRUMENTAL', 'July'),
                8  => I18N::translateContext('INSTRUMENTAL', 'August'),
                9  => I18N::translateContext('INSTRUMENTAL', 'September'),
                10 => I18N::translateContext('INSTRUMENTAL', 'October'),
                11 => I18N::translateContext('INSTRUMENTAL', 'November'),
                12 => I18N::translateContext('INSTRUMENTAL', 'December'),
            ];
        }

        return $translated_month_names[$month_number];
    }

    /**
     * Abbreviated month name
     *
     * @param int  $month_number
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameAbbreviated(int $month_number, bool $leap_year): string
    {
        static $translated_month_names;

        if ($translated_month_names === null) {
            $translated_month_names = [
                0  => '',
                1  => I18N::translateContext('Abbreviation for January', 'Jan'),
                2  => I18N::translateContext('Abbreviation for February', 'Feb'),
                3  => I18N::translateContext('Abbreviation for March', 'Mar'),
                4  => I18N::translateContext('Abbreviation for April', 'Apr'),
                5  => I18N::translateContext('Abbreviation for May', 'May'),
                6  => I18N::translateContext('Abbreviation for June', 'Jun'),
                7  => I18N::translateContext('Abbreviation for July', 'Jul'),
                8  => I18N::translateContext('Abbreviation for August', 'Aug'),
                9  => I18N::translateContext('Abbreviation for September', 'Sep'),
                10 => I18N::translateContext('Abbreviation for October', 'Oct'),
                11 => I18N::translateContext('Abbreviation for November', 'Nov'),
                12 => I18N::translateContext('Abbreviation for December', 'Dec'),
            ];
        }

        return $translated_month_names[$month_number];
    }

    /**
     * Full day of th eweek
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
     * @param CalendarDate $d1
     * @param CalendarDate $d2
     *
     * @return int
     */
    public static function compare(CalendarDate $d1, CalendarDate $d2): int
    {
        if ($d1->maxJD < $d2->minJD) {
            return -1;
        }

        if ($d2->maxJD < $d1->minJD) {
            return 1;
        }

        return 0;
    }

    /**
     * Calculate the years/months/days between this date and another date.
     *
     * Results assume you add the days first, then the months.
     * 4 February -> 3 July is 27 days (3 March) and 4 months.
     * It is not 4 months (4 June) and 29 days.
     *
     * @param CalendarDate $date
     *
     * @return int[] Age in years/months/days
     */
    public function ageDifference(CalendarDate $date): array
    {
        // Incomplete dates
        if ($this->y === 0 || $date->y === 0) {
            return [-1, -1, -1];
        }

        // Overlapping dates
        if (self::compare($this, $date) === 0) {
            return [0, 0, 0];
        }

        // Perform all calculations using the calendar of the first date
        list($year1, $month1, $day1) = $this->calendar->jdToYmd($this->minJD);
        list($year2, $month2, $day2) = $this->calendar->jdToYmd($date->minJD);

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
     */
    public function getAge(int $jd): int
    {
        if ($this->y == 0 || $jd == 0) {
            return 0;
        }
        if ($this->minJD < $jd && $this->maxJD > $jd) {
            return 0;
        }
        if ($this->minJD == $jd) {
            return 0;
        }
        list($y, $m, $d) = $this->calendar->jdToYmd($jd);
        $dy = $y - $this->y;
        $dm = $m - max($this->m, 1);
        $dd = $d - max($this->d, 1);
        if ($dd < 0) {
            $dm--;
        }
        if ($dm < 0) {
            $dm += $this->calendar->monthsInYear();
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
     */
    public function getAgeFull(int $jd): string
    {
        if ($this->y == 0 || $jd == 0) {
            return '';
        }
        if ($this->minJD < $jd && $this->maxJD > $jd) {
            return '';
        }
        if ($this->minJD == $jd) {
            return '';
        }
        if ($jd < $this->minJD) {
            return '<i class="icon-warning"></i>';
        }
        list($y, $m, $d) = $this->calendar->jdToYmd($jd);
        $dy = $y - $this->y;
        $dm = $m - max($this->m, 1);
        $dd = $d - max($this->d, 1);
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
        return ($jd - $this->minJD) . 'd';
    }

    /**
     * Convert a date from one calendar to another.
     *
     * @param string $calendar
     *
     * @return CalendarDate
     */
    public function convertToCalendar(string $calendar): CalendarDate
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
        return $this->minJD >= $this->calendar->jdStart() && $this->maxJD <= $this->calendar->jdEnd();
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
            return $this->calendar->daysInMonth($this->y, $this->m);
        } catch (\InvalidArgumentException $ex) {
            DebugBar::addThrowable($ex);

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
        // Don’t show exact details for inexact dates
        if (!$this->d) {
            // The comma is for US "M D, Y" dates
            $format = preg_replace('/%[djlDNSwz][,]?/', '', $format);
        }
        if (!$this->m) {
            $format = str_replace([
                '%F',
                '%m',
                '%M',
                '%n',
                '%t',
            ], '', $format);
        }
        if (!$this->y) {
            $format = str_replace([
                '%t',
                '%L',
                '%G',
                '%y',
                '%Y',
            ], '', $format);
        }
        // If we’ve trimmed the format, also trim the punctuation
        if (!$this->d || !$this->m || !$this->y) {
            $format = trim($format, ',. ;/-');
        }
        if ($this->d && preg_match('/%[djlDNSwz]/', $format)) {
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
        preg_match_all('/%[^%]/', $format, $matches);
        foreach ($matches[0] as $match) {
            switch ($match) {
                case '%d':
                    $format = str_replace($match, $this->formatDayZeros(), $format);
                    break;
                case '%j':
                    $format = str_replace($match, $this->formatDay(), $format);
                    break;
                case '%l':
                    $format = str_replace($match, $this->formatLongWeekday(), $format);
                    break;
                case '%D':
                    $format = str_replace($match, $this->formatShortWeekday(), $format);
                    break;
                case '%N':
                    $format = str_replace($match, $this->formatIsoWeekday(), $format);
                    break;
                case '%w':
                    $format = str_replace($match, $this->formatNumericWeekday(), $format);
                    break;
                case '%z':
                    $format = str_replace($match, $this->formatDayOfYear(), $format);
                    break;
                case '%F':
                    $format = str_replace($match, $this->formatLongMonth($case), $format);
                    break;
                case '%m':
                    $format = str_replace($match, $this->formatMonthZeros(), $format);
                    break;
                case '%M':
                    $format = str_replace($match, $this->formatShortMonth(), $format);
                    break;
                case '%n':
                    $format = str_replace($match, $this->formatMonth(), $format);
                    break;
                case '%t':
                    $format = str_replace($match, (string) $this->daysInMonth(), $format);
                    break;
                case '%L':
                    $format = str_replace($match, $this->isLeapYear() ? '1' : '0', $format);
                    break;
                case '%Y':
                    $format = str_replace($match, $this->formatLongYear(), $format);
                    break;
                case '%y':
                    $format = str_replace($match, $this->formatShortYear(), $format);
                    break;
                // These 4 extensions are useful for re-formatting gedcom dates.
                case '%@':
                    $format = str_replace($match, $this->calendar->gedcomCalendarEscape(), $format);
                    break;
                case '%A':
                    $format = str_replace($match, $this->formatGedcomDay(), $format);
                    break;
                case '%O':
                    $format = str_replace($match, $this->formatGedcomMonth(), $format);
                    break;
                case '%E':
                    $format = str_replace($match, $this->formatGedcomYear(), $format);
                    break;
            }
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
        if ($this->d > 9) {
            return I18N::digits($this->d);
        }

        return I18N::digits('0' . $this->d);
    }

    /**
     * Generate the %j format for a date.
     *
     * @return string
     */
    protected function formatDay(): string
    {
        return I18N::digits($this->d);
    }

    /**
     * Generate the %l format for a date.
     *
     * @return string
     */
    protected function formatLongWeekday(): string
    {
        return $this->dayNames($this->minJD % $this->calendar->daysInWeek());
    }

    /**
     * Generate the %D format for a date.
     *
     * @return string
     */
    protected function formatShortWeekday(): string
    {
        return $this->dayNamesAbbreviated($this->minJD % $this->calendar->daysInWeek());
    }

    /**
     * Generate the %N format for a date.
     *
     * @return string
     */
    protected function formatIsoWeekday(): string
    {
        return I18N::digits($this->minJD % 7 + 1);
    }

    /**
     * Generate the %w format for a date.
     *
     * @return string
     */
    protected function formatNumericWeekday(): string
    {
        return I18N::digits(($this->minJD + 1) % $this->calendar->daysInWeek());
    }

    /**
     * Generate the %z format for a date.
     *
     * @return string
     */
    protected function formatDayOfYear(): string
    {
        return I18N::digits($this->minJD - $this->calendar->ymdToJd($this->y, 1, 1));
    }

    /**
     * Generate the %n format for a date.
     *
     * @return string
     */
    protected function formatMonth(): string
    {
        return I18N::digits($this->m);
    }

    /**
     * Generate the %m format for a date.
     *
     * @return string
     */
    protected function formatMonthZeros(): string
    {
        if ($this->m > 9) {
            return I18N::digits($this->m);
        }

        return I18N::digits('0' . $this->m);
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
                return $this->monthNameGenitiveCase($this->m, $this->isLeapYear());
            case 'NOMINATIVE':
                return $this->monthNameNominativeCase($this->m, $this->isLeapYear());
            case 'LOCATIVE':
                return $this->monthNameLocativeCase($this->m, $this->isLeapYear());
            case 'INSTRUMENTAL':
                return $this->monthNameInstrumentalCase($this->m, $this->isLeapYear());
            default:
                throw new \InvalidArgumentException($case);
        }
    }

    /**
     * Generate the %M format for a date.
     *
     * @return string
     */
    protected function formatShortMonth(): string
    {
        return $this->monthNameAbbreviated($this->m, $this->isLeapYear());
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
        if ($this->d == 0) {
            return '';
        }

        return sprintf('%02d', $this->d);
    }

    /**
     * Generate the %O format for a date.
     *
     * @return string
     */
    protected function formatGedcomMonth(): string
    {
        // Our simple lookup table doesn't work correctly for Adar on leap years
        if ($this->m == 7 && $this->calendar instanceof JewishCalendar && !$this->calendar->isLeapYear($this->y)) {
            return 'ADR';
        }

        return array_search($this->m, static::$MONTH_ABBREV);
    }

    /**
     * Generate the %E format for a date.
     *
     * @return string
     */
    protected function formatGedcomYear(): string
    {
        if ($this->y == 0) {
            return '';
        }

        return sprintf('%04d', $this->y);
    }

    /**
     * Generate the %Y format for a date.
     *
     * @return string
     */
    protected function formatLongYear(): string
    {
        return I18N::digits($this->y);
    }

    /**
     * Which months follows this one? Calendars with leap-months should provide their own implementation.
     *
     * @return int[]
     */
    protected function nextMonth(): array
    {
        return [
            $this->m === $this->calendar->monthsInYear() ? $this->nextYear($this->y) : $this->y,
            ($this->m % $this->calendar->monthsInYear()) + 1,
        ];
    }

    /**
     * Convert a decimal number to roman numerals
     *
     * @param int $number
     *
     * @return string
     */
    protected function numberToRomanNumerals($number): string
    {
        if ($number < 1) {
            // Cannot convert zero/negative numbers
            return (string) $number;
        }
        $roman = '';
        foreach (self::$roman_numerals as $key => $value) {
            while ($number >= $key) {
                $roman  .= $value;
                $number -= $key;
            }
        }

        return $roman;
    }

    /**
     * Convert a roman numeral to decimal
     *
     * @param string $roman
     *
     * @return int
     */
    protected function romanNumeralsToNumber($roman): int
    {
        $num = 0;
        foreach (self::$roman_numerals as $key => $value) {
            if (strpos($roman, $value) === 0) {
                $num += $key;
                $roman = substr($roman, strlen($value));
            }
        }

        return $num;
    }

    /**
     * Get today’s date in the current calendar.
     *
     * @return int[]
     */
    public function todayYmd(): array
    {
        return $this->calendar->jdToYmd(unixtojd());
    }

    /**
     * Convert to today’s date.
     *
     * @return CalendarDate
     */
    public function today(): CalendarDate
    {
        $tmp    = clone $this;
        $ymd    = $tmp->todayYmd();
        $tmp->y = $ymd[0];
        $tmp->m = $ymd[1];
        $tmp->d = $ymd[2];
        $tmp->setJdFromYmd();

        return $tmp;
    }

    /**
     * Create a URL that links this date to the WT calendar
     *
     * @param string $date_format
     *
     * @return string
     */
    public function calendarUrl(string $date_format): string
    {
        if (strpbrk($date_format, 'dDj') && $this->d) {
            // If the format includes a day, and the date also includes a day, then use the day view
            $view = 'day';
        } elseif (strpbrk($date_format, 'FMmn') && $this->m) {
            // If the format includes a month, and the date also includes a month, then use the month view
            $view = 'month';
        } else {
            // Use the year view
            $view = 'year';
        }

        return route('calendar', [
            'cal'   => $this->calendar->gedcomCalendarEscape(),
            'year'  => $this->formatGedcomYear(),
            'month' => $this->formatGedcomMonth(),
            'day'   => $this->formatGedcomDay(),
            'view'  => $view,
        ]);
    }
}
