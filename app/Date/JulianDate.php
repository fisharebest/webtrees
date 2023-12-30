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

namespace Fisharebest\Webtrees\Date;

use Fisharebest\ExtCalendar\JulianCalendar;
use Fisharebest\Webtrees\I18N;

/**
 * Definitions for proleptic Julian dates.
 */
class JulianDate extends AbstractGregorianJulianDate
{
    // GEDCOM calendar escape
    public const ESCAPE = '@#DJULIAN@';

    // For dates recorded in new-style/old-style format, e.g. 2 FEB 1743/44
    private bool $new_old_style = false;

    /**
     * Create a date from either:
     * a Julian day number
     * day/month/year strings from a GEDCOM date
     * another CalendarDate object
     *
     * @param array<string>|int|AbstractCalendarDate $date
     */
    public function __construct($date)
    {
        $this->calendar = new JulianCalendar();
        parent::__construct($date);
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
        if ($year === -1) {
            return 1;
        }

        return $year + 1;
    }

    /**
     * Process new-style/old-style years and years BC
     *
     * @param string $year
     *
     * @return int
     */
    protected function extractYear(string $year): int
    {
        if (preg_match('/^(\d\d\d\d)\/\d{1,4}$/', $year, $match)) {
            // Assume the first year is correct
            $this->new_old_style = true;

            return (int) $match[1] + 1;
        }

        if (preg_match('/^(\d+) B\.C\.$/', $year, $match)) {
            return -(int) $match[1];
        }

        return (int) $year;
    }

    /**
     * Generate the %Y format for a date.
     *
     * @return string
     */
    protected function formatLongYear(): string
    {
        if ($this->year < 0) {
            return /*  I18N: BCE=Before the Common Era, for Julian years < 0. See https://en.wikipedia.org/wiki/Common_Era */
                I18N::translate('%s&nbsp;BCE', I18N::digits(-$this->year));
        }

        if ($this->new_old_style) {
            return I18N::translate('%s&nbsp;CE', I18N::digits(sprintf('%d/%02d', $this->year - 1, $this->year % 100)));
        }

        /* I18N: CE=Common Era, for Julian years > 0. See https://en.wikipedia.org/wiki/Common_Era */
        return I18N::translate('%s&nbsp;CE', I18N::digits($this->year));
    }

    /**
     * Generate the %E format for a date.
     *
     * @return string
     */
    protected function formatGedcomYear(): string
    {
        if ($this->year < 0) {
            return sprintf('%04d B.C.', -$this->year);
        }

        if ($this->new_old_style) {
            return sprintf('%04d/%02d', $this->year - 1, $this->year % 100);
        }

        return sprintf('%04d', $this->year);
    }
}
