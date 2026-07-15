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

namespace Fisharebest\Webtrees;

use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\Webtrees\Date\AbstractCalendarDate;
use Fisharebest\Webtrees\Enums\DateType;

/**
 * A representation of GEDCOM dates and date ranges.
 * Since different calendars start their days at different times, (civil
 * midnight, solar midnight, sunset, sunrise, etc.), we convert on the basis of
 * midday.
 * We assume that years start on the first day of the first month. Where
 * this is not the case (e.g. England prior to 1752), we need to use modified
 * years or the OS/NS notation "4 FEB 1750/51".
 */
class Date
{
    public DateType $type;

    // The first (or only) date
    private AbstractCalendarDate $date1;

    // Optional second date
    private AbstractCalendarDate|null $date2 = null;

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
            $this->type  = DateType::from($match[1] . $match[3]);
            $this->date1 = $calendar_date_factory->make($match[2]);
            $this->date2 = $calendar_date_factory->make($match[4]);
        } elseif (preg_match('/^(TO|FROM|BEF|AFT|CAL|EST|INT|ABT) (.+)/', $date, $match)) {
            $this->type  = DateType::from($match[1]);
            $this->date1 = $calendar_date_factory->make($match[2]);
        } else {
            $this->type  = DateType::Exact;
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

    public static function fromCalendarDate(AbstractCalendarDate $date): self
    {
        $tmp        = new self('');
        $tmp->type  = DateType::Exact;
        $tmp->date1 = $date;

        return $tmp;
    }

    public function yearOnly(): self
    {
        $tmp               = clone $this;
        $tmp->date1->month = 0;
        $tmp->date1->day   = 0;
        $tmp->date1->setJdFromYmd();
        $tmp->date2 = null;
        $tmp->type  = DateType::Exact;

        return $tmp;
    }

    /**
     * Convert a date to the preferred format and calendar(s) display.
     *
     * @param Tree|null   $tree              Wrap the date in a link to the calendar page for the tree
     * @param string|null $date_format       Override the default date format
     * @param bool        $convert_calendars Convert the date into other calendars (requires a tree)
     */
    public function display(Tree|null $tree = null, string|null $date_format = null, bool $convert_calendars = false): string
    {
        if ($tree instanceof Tree) {
            $CALENDAR_FORMAT = $tree->getPreference('CALENDAR_FORMAT');
        } else {
            $CALENDAR_FORMAT = 'none';
        }

        if ($convert_calendars) {
            $calendar_formats = explode('_and_', $CALENDAR_FORMAT);
        } else {
            $calendar_formats = [];
        }

        $date = I18N::language()->formatDate($this);

        if ($this->text !== '') {
            $date .= ' (' . e($this->text) . ')';
        }

        // Convert to other calendars, if requested.
        // Note that we convert, regardless of the calendar, and only display
        // the conversion if different.  This avoids converting year-only dates
        // between Julian and Gregorian.
        foreach ($calendar_formats as $calendar_format) {
            if ($calendar_format !== 'none') {
                $different = false;
                $conv      = clone($this);

                $conv1 = $this->date1->convertToCalendar($calendar_format);
                if ($conv1->inValidRange()) {
                    $conv->date1 = $conv1;
                    if (
                        $this->date1->year() !== $conv1->year() ||
                        $this->date1->month() !== $conv1->month() ||
                        $this->date1->day() !== $conv1->day()
                    ) {
                        $different = true;
                    }
                }

                if ($this->date2 !== null) {
                    $conv2 = $this->date2->convertToCalendar($calendar_format);
                    if ($conv2->inValidRange()) {
                        $conv->date2 = $conv2;
                        if (
                            $this->date2->year() !== $conv2->year() ||
                            $this->date2->month() !== $conv2->month() ||
                            $this->date2->day() !== $conv2->day()
                        ) {
                            $different = true;
                        }
                    }

                    if ($different) {
                        $date .= ' [' . I18N::language()->formatDate($conv) . ']';
                    }
                }
            }
        }

        return $date;
    }


    /**
     * Get the earliest calendar date from this GEDCOM date.
     * In the date “FROM 1900 TO 1910”, this would be 1900.
     */
    public function minimumDate(): AbstractCalendarDate
    {
        return $this->date1;
    }

    /**
     * Get the latest calendar date from this GEDCOM date.
     * In the date “FROM 1900 TO 1910”, this would be 1910.
     */
    public function maximumDate(): AbstractCalendarDate
    {
        return $this->date2 ?? $this->date1;
    }

    /**
     * Get the earliest Julian day number from this GEDCOM date.
     */
    public function minimumJulianDay(): int
    {
        return $this->minimumDate()->minimumJulianDay();
    }

    /**
     * Get the latest Julian day number from this GEDCOM date.
     */
    public function maximumJulianDay(): int
    {
        return $this->maximumDate()->maximumJulianDay();
    }

    /**
     * Get the middle Julian day number from the GEDCOM date.
     * For a month-only date, this would be somewhere around the 16th day.
     * For a year-only date, this would be somewhere around 1st July.
     */
    public function julianDay(): int
    {
        return intdiv($this->minimumJulianDay() + $this->maximumJulianDay(), 2);
    }

    /**
     * Offset this date by N years, and round to the whole year.
     * This is typically used to create an estimated death date,
     * which is before a certain number of years after the birth date.
     */
    public function addYears(int $years, DateType $type): Date
    {
        $tmp               = clone $this;
        $tmp->date1->year  += $years;
        $tmp->date1->month = 0;
        $tmp->date1->day   = 0;
        $tmp->date1->setJdFromYmd();
        $tmp->type  = $type;
        $tmp->date2 = null;

        return $tmp;
    }

    /**
     * Compare two dates, so they can be sorted.
     * return -1 if $a<$b
     * return +1 if $b>$a
     * return  0 if dates same/overlap
     * BEF/AFT sort as the day before/after
     */
    public static function compare(Date $a, Date $b): int
    {
        // Get min/max JD for each date.
        switch ($a->type) {
            case DateType::Before:
                $amin = $a->minimumJulianDay() - 1;
                $amax = $amin;
                break;
            case DateType::After:
                $amax = $a->maximumJulianDay() + 1;
                $amin = $amax;
                break;
            default:
                $amin = $a->minimumJulianDay();
                $amax = $a->maximumJulianDay();
                break;
        }
        switch ($b->type) {
            case DateType::Before:
                $bmin = $b->minimumJulianDay() - 1;
                $bmax = $bmin;
                break;
            case DateType::After:
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
     * An incomplete date such as "12 AUG" would be invalid, as
     * we cannot sort it.
     */
    public function isOK(): bool
    {
        return $this->minimumJulianDay() !== 0 && $this->maximumJulianDay() !== 0;
    }

    /**
     * Calculate the gregorian year for a date. This should NOT be used internally
     * within WT - we should keep the code "calendar neutral" to allow support for
     * Jewish/Arabic users. This is only for interfacing with external entities,
     * such as the ancestry.com search interface or the dated fact icons.
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
