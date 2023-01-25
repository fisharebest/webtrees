<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\Webtrees\Date\AbstractCalendarDate;

/**
 * A representation of GEDCOM dates and date ranges.
 *
 * Since different calendars start their days at different times, (civil
 * midnight, solar midnight, sunset, sunrise, etc.), we convert on the basis of
 * midday.
 *
 * We assume that years start on the first day of the first month. Where
 * this is not the case (e.g. England prior to 1752), we need to use modified
 * years or the OS/NS notation "4 FEB 1750/51".
 */
class Date
{
    // Optional qualifier, such as BEF, FROM, ABT
    public string $qual1 = '';

    // The first (or only) date
    private AbstractCalendarDate $date1;

    // Optional qualifier, such as TO, AND
    public string $qual2 = '';

    // Optional second date
    private ?AbstractCalendarDate $date2 = null;

    // Optional text, as included with an INTerpreted date
    private string $text = '';

    /**
     * Create a date, from GEDCOM data.
     *
     * @param string $date A date in GEDCOM format
     */
    public function __construct(string $date)
    {
        $calendar_date_factory = Registry::calendarDateFactory();

        // Extract any explanatory text
        if (preg_match('/^(.*) ?[(](.*)[)]/', $date, $match)) {
            $date       = $match[1];
            $this->text = $match[2];
        }
        if (preg_match('/^(FROM|BET) (.+) (AND|TO) (.+)/', $date, $match)) {
            $this->qual1 = $match[1];
            $this->date1 = $calendar_date_factory->make($match[2]);
            $this->qual2 = $match[3];
            $this->date2 = $calendar_date_factory->make($match[4]);
        } elseif (preg_match('/^(TO|FROM|BEF|AFT|CAL|EST|INT|ABT) (.+)/', $date, $match)) {
            $this->qual1 = $match[1];
            $this->date1 = $calendar_date_factory->make($match[2]);
        } else {
            $this->date1 = $calendar_date_factory->make($date);
        }
    }

    /**
     * When we copy a date object, we need to create copies of
     * its child objects.
     */
    public function __clone()
    {
        $this->date1 = clone $this->date1;
        if ($this->date2 !== null) {
            $this->date2 = clone $this->date2;
        }
    }

    /**
     * Convert a date to the preferred format and calendar(s) display.
     *
     * @param Tree|null   $tree              Wrap the date in a link to the calendar page for the tree
     * @param string|null $date_format       Override the default date format
     * @param bool        $convert_calendars Convert the date into other calendars (requires a tree)
     *
     * @return string
     */
    public function display(Tree $tree = null, string $date_format = null, bool $convert_calendars = false): string
    {
        if ($tree instanceof Tree) {
            $CALENDAR_FORMAT = $tree->getPreference('CALENDAR_FORMAT');
        } else {
            $CALENDAR_FORMAT = 'none';
        }

        $date_format ??= I18N::dateFormat();

        if ($convert_calendars) {
            $calendar_format = explode('_and_', $CALENDAR_FORMAT);
        } else {
            $calendar_format = [];
        }

        // Two dates with text before, between and after
        $q1 = $this->qual1;
        $d1 = $this->date1->format($date_format, $this->qual1);
        $q2 = $this->qual2;
        if ($this->date2 === null) {
            $d2 = '';
        } else {
            $d2 = $this->date2->format($date_format, $this->qual2);
        }
        // Con vert to other calendars, if requested
        $conv1 = '';
        $conv2 = '';
        foreach ($calendar_format as $cal_fmt) {
            if ($cal_fmt !== 'none') {
                $d1conv = $this->date1->convertToCalendar($cal_fmt);
                if ($d1conv->inValidRange()) {
                    $d1tmp = $d1conv->format($date_format, $this->qual1);
                } else {
                    $d1tmp = '';
                }
                if ($this->date2 === null) {
                    $d2conv = null;
                    $d2tmp  = '';
                } else {
                    $d2conv = $this->date2->convertToCalendar($cal_fmt);
                    if ($d2conv->inValidRange()) {
                        $d2tmp = $d2conv->format($date_format, $this->qual2);
                    } else {
                        $d2tmp = '';
                    }
                }
                // If the date is different from the unconverted date, add it to the date string.
                if ($d1 !== $d1tmp && $d1tmp !== '') {
                    if ($tree instanceof Tree) {
                        if ($CALENDAR_FORMAT !== 'none') {
                            $conv1 .= ' <span dir="' . I18N::direction() . '">(<a href="' . e($d1conv->calendarUrl($date_format, $tree)) . '" rel="nofollow">' . $d1tmp . '</a>)</span>';
                        } else {
                            $conv1 .= ' <span dir="' . I18N::direction() . '"><br><a href="' . e($d1conv->calendarUrl($date_format, $tree)) . '" rel="nofollow">' . $d1tmp . '</a></span>';
                        }
                    } else {
                        $conv1 .= ' <span dir="' . I18N::direction() . '">(' . $d1tmp . ')</span>';
                    }
                }
                if ($this->date2 !== null && $d2 !== $d2tmp && $d1tmp !== '') {
                    if ($tree instanceof Tree) {
                        $conv2 .= ' <span dir="' . I18N::direction() . '">(<a href="' . e($d2conv->calendarUrl($date_format, $tree)) . '" rel="nofollow">' . $d2tmp . '</a>)</span>';
                    } else {
                        $conv2 .= ' <span dir="' . I18N::direction() . '">(' . $d2tmp . ')</span>';
                    }
                }
            }
        }

        // Add URLs, if requested
        if ($tree instanceof Tree) {
            $d1 = '<a href="' . e($this->date1->calendarUrl($date_format, $tree)) . '" rel="nofollow">' . $d1 . '</a>';
            if ($this->date2 instanceof AbstractCalendarDate) {
                $d2 = '<a href="' . e($this->date2->calendarUrl($date_format, $tree)) . '" rel="nofollow">' . $d2 . '</a>';
            }
        }

        // Localise the date
        switch ($q1 . $q2) {
            case '':
                $tmp = $d1 . $conv1;
                break;
            case 'ABT':
                /* I18N: Gedcom ABT dates */
                $tmp = I18N::translate('about %s', $d1 . $conv1);
                break;
            case 'CAL':
                /* I18N: Gedcom CAL dates */
                $tmp = I18N::translate('calculated %s', $d1 . $conv1);
                break;
            case 'EST':
                /* I18N: Gedcom EST dates */
                $tmp = I18N::translate('estimated %s', $d1 . $conv1);
                break;
            case 'INT':
                /* I18N: Gedcom INT dates */
                $tmp = I18N::translate('interpreted %s (%s)', $d1 . $conv1, e($this->text));
                break;
            case 'BEF':
                /* I18N: Gedcom BEF dates */
                $tmp = I18N::translate('before %s', $d1 . $conv1);
                break;
            case 'AFT':
                /* I18N: Gedcom AFT dates */
                $tmp = I18N::translate('after %s', $d1 . $conv1);
                break;
            case 'FROM':
                /* I18N: Gedcom FROM dates */
                $tmp = I18N::translate('from %s', $d1 . $conv1);
                break;
            case 'TO':
                /* I18N: Gedcom TO dates */
                $tmp = I18N::translate('to %s', $d1 . $conv1);
                break;
            case 'BETAND':
                /* I18N: Gedcom BET-AND dates */
                $tmp = I18N::translate('between %s and %s', $d1 . $conv1, $d2 . $conv2);
                break;
            case 'FROMTO':
                /* I18N: Gedcom FROM-TO dates */
                $tmp = I18N::translate('from %s to %s', $d1 . $conv1, $d2 . $conv2);
                break;
            default:
                $tmp = I18N::translate('Invalid date');
                break;
        }

        if (strip_tags($tmp) === '') {
            return '';
        }

        return '<span class="date">' . $tmp . '</span>';
    }

    /**
     * Get the earliest calendar date from this GEDCOM date.
     *
     * In the date “FROM 1900 TO 1910”, this would be 1900.
     *
     * @return AbstractCalendarDate
     */
    public function minimumDate(): AbstractCalendarDate
    {
        return $this->date1;
    }

    /**
     * Get the latest calendar date from this GEDCOM date.
     *
     * In the date “FROM 1900 TO 1910”, this would be 1910.
     *
     * @return AbstractCalendarDate
     */
    public function maximumDate(): AbstractCalendarDate
    {
        return $this->date2 ?? $this->date1;
    }

    /**
     * Get the earliest Julian day number from this GEDCOM date.
     *
     * @return int
     */
    public function minimumJulianDay(): int
    {
        return $this->minimumDate()->minimumJulianDay();
    }

    /**
     * Get the latest Julian day number from this GEDCOM date.
     *
     * @return int
     */
    public function maximumJulianDay(): int
    {
        return $this->maximumDate()->maximumJulianDay();
    }

    /**
     * Get the middle Julian day number from the GEDCOM date.
     *
     * For a month-only date, this would be somewhere around the 16th day.
     * For a year-only date, this would be somewhere around 1st July.
     *
     * @return int
     */
    public function julianDay(): int
    {
        return intdiv($this->minimumJulianDay() + $this->maximumJulianDay(), 2);
    }

    /**
     * Offset this date by N years, and round to the whole year.
     *
     * This is typically used to create an estimated death date,
     * which is before a certain number of years after the birth date.
     *
     * @param int    $years     a number of years, positive or negative
     * @param string $qualifier typically “BEF” or “AFT”
     *
     * @return Date
     */
    public function addYears(int $years, string $qualifier = ''): Date
    {
        $tmp               = clone $this;
        $tmp->date1->year  += $years;
        $tmp->date1->month = 0;
        $tmp->date1->day   = 0;
        $tmp->date1->setJdFromYmd();
        $tmp->qual1 = $qualifier;
        $tmp->qual2 = '';
        $tmp->date2 = null;

        return $tmp;
    }

    /**
     * Compare two dates, so they can be sorted.
     *
     * return -1 if $a<$b
     * return +1 if $b>$a
     * return  0 if dates same/overlap
     * BEF/AFT sort as the day before/after
     *
     * @param Date $a
     * @param Date $b
     *
     * @return int
     */
    public static function compare(Date $a, Date $b): int
    {
        // Get min/max JD for each date.
        switch ($a->qual1) {
            case 'BEF':
                $amin = $a->minimumJulianDay() - 1;
                $amax = $amin;
                break;
            case 'AFT':
                $amax = $a->maximumJulianDay() + 1;
                $amin = $amax;
                break;
            default:
                $amin = $a->minimumJulianDay();
                $amax = $a->maximumJulianDay();
                break;
        }
        switch ($b->qual1) {
            case 'BEF':
                $bmin = $b->minimumJulianDay() - 1;
                $bmax = $bmin;
                break;
            case 'AFT':
                $bmax = $b->maximumJulianDay() + 1;
                $bmin = $bmax;
                break;
            default:
                $bmin = $b->minimumJulianDay();
                $bmax = $b->maximumJulianDay();
                break;
        }
        if ($amax < $bmin) {
            return -1;
        }

        if ($amin > $bmax && $bmax > 0) {
            return 1;
        }

        if ($amin < $bmin && $amax <= $bmax) {
            return -1;
        }

        if ($amin > $bmin && $amax >= $bmax && $bmax > 0) {
            return 1;
        }

        return 0;
    }

    /**
     * Check whether a gedcom date contains usable calendar date(s).
     *
     * An incomplete date such as "12 AUG" would be invalid, as
     * we cannot sort it.
     *
     * @return bool
     */
    public function isOK(): bool
    {
        return $this->minimumJulianDay() && $this->maximumJulianDay();
    }

    /**
     * Calculate the gregorian year for a date. This should NOT be used internally
     * within WT - we should keep the code "calendar neutral" to allow support for
     * jewish/arabic users. This is only for interfacing with external entities,
     * such as the ancestry.com search interface or the dated fact icons.
     *
     * @return int
     */
    public function gregorianYear(): int
    {
        if ($this->isOK()) {
            $gregorian_calendar = new GregorianCalendar();
            [$year] = $gregorian_calendar->jdToYmd($this->julianDay());

            return $year;
        }

        return 0;
    }
}
