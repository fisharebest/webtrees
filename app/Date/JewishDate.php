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
namespace Fisharebest\Webtrees\Date;

use Fisharebest\ExtCalendar\JewishCalendar;
use Fisharebest\Webtrees\I18N;

/**
 * Definitions for the Jewish calendar
 */
class JewishDate extends CalendarDate
{
    /** @var int[] Convert GEDCOM month names to month numbers */
    public static $MONTH_ABBREV = [
        ''    => 0,
        'TSH' => 1,
        'CSH' => 2,
        'KSL' => 3,
        'TVT' => 4,
        'SHV' => 5,
        'ADR' => 6,
        'ADS' => 7,
        'NSN' => 8,
        'IYR' => 9,
        'SVN' => 10,
        'TMZ' => 11,
        'AAV' => 12,
        'ELL' => 13,
    ];

    /**
     * Create a date from either:
     * a Julian day number
     * day/month/year strings from a GEDCOM date
     * another CalendarDate object
     *
     * @param array|int|CalendarDate $date
     */
    public function __construct($date)
    {
        $this->calendar = new JewishCalendar;
        parent::__construct($date);
    }

    /**
     * Generate the %j format for a date.
     *
     * @return string
     */
    protected function formatDay()
    {
        if (WT_LOCALE === 'he' || WT_LOCALE === 'yi') {
            return (new JewishCalendar)->numberToHebrewNumerals($this->d, true);
        } else {
            return $this->d;
        }
    }

    /**
     * Generate the %y format for a date.
     *
     * NOTE Short year is NOT a 2-digit year. It is for calendars such as hebrew
     * which have a 3-digit form of 4-digit years.
     *
     * @return string
     */
    protected function formatShortYear()
    {
        if (WT_LOCALE === 'he' || WT_LOCALE === 'yi') {
            return (new JewishCalendar)->numberToHebrewNumerals($this->y, false);
        } else {
            return $this->y;
        }
    }

    /**
     * Generate the %Y format for a date.
     *
     * @return string
     */
    protected function formatLongYear()
    {
        if (WT_LOCALE === 'he' || WT_LOCALE === 'yi') {
            return (new JewishCalendar)->numberToHebrewNumerals($this->y, true);
        } else {
            return $this->y;
        }
    }

    /**
     * Full month name in nominative case.
     *
     * @param int  $month_number
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    public static function monthNameNominativeCase($month_number, $leap_year)
    {
        static $translated_month_names;

        if ($translated_month_names === null) {
            $translated_month_names = [
                0  => '',
                /* I18N: a month in the Jewish calendar */
                1  => I18N::translateContext('NOMINATIVE', 'Tishrei'),
                /* I18N: a month in the Jewish calendar */
                2  => I18N::translateContext('NOMINATIVE', 'Heshvan'),
                /* I18N: a month in the Jewish calendar */
                3  => I18N::translateContext('NOMINATIVE', 'Kislev'),
                /* I18N: a month in the Jewish calendar */
                4  => I18N::translateContext('NOMINATIVE', 'Tevet'),
                /* I18N: a month in the Jewish calendar */
                5  => I18N::translateContext('NOMINATIVE', 'Shevat'),
                /* I18N: a month in the Jewish calendar */
                6  => I18N::translateContext('NOMINATIVE', 'Adar I'),
                /* I18N: a month in the Jewish calendar */
                7  => I18N::translateContext('NOMINATIVE', 'Adar'),
                /* I18N: a month in the Jewish calendar */
                -7 => I18N::translateContext('NOMINATIVE', 'Adar II'),
                /* I18N: a month in the Jewish calendar */
                8  => I18N::translateContext('NOMINATIVE', 'Nissan'),
                /* I18N: a month in the Jewish calendar */
                9  => I18N::translateContext('NOMINATIVE', 'Iyar'),
                /* I18N: a month in the Jewish calendar */
                10 => I18N::translateContext('NOMINATIVE', 'Sivan'),
                /* I18N: a month in the Jewish calendar */
                11 => I18N::translateContext('NOMINATIVE', 'Tamuz'),
                /* I18N: a month in the Jewish calendar */
                12 => I18N::translateContext('NOMINATIVE', 'Av'),
                /* I18N: a month in the Jewish calendar */
                13 => I18N::translateContext('NOMINATIVE', 'Elul'),
            ];
        }

        if ($month_number === 7 && $leap_year) {
            return $translated_month_names[-7];
        } else {
            return $translated_month_names[$month_number];
        }
    }

    /**
     * Full month name in genitive case.
     *
     * @param int  $month_number
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameGenitiveCase($month_number, $leap_year)
    {
        static $translated_month_names;

        if ($translated_month_names === null) {
            $translated_month_names = [
                0  => '',
                /* I18N: a month in the Jewish calendar */
                1  => I18N::translateContext('GENITIVE', 'Tishrei'),
                /* I18N: a month in the Jewish calendar */
                2  => I18N::translateContext('GENITIVE', 'Heshvan'),
                /* I18N: a month in the Jewish calendar */
                3  => I18N::translateContext('GENITIVE', 'Kislev'),
                /* I18N: a month in the Jewish calendar */
                4  => I18N::translateContext('GENITIVE', 'Tevet'),
                /* I18N: a month in the Jewish calendar */
                5  => I18N::translateContext('GENITIVE', 'Shevat'),
                /* I18N: a month in the Jewish calendar */
                6  => I18N::translateContext('GENITIVE', 'Adar I'),
                /* I18N: a month in the Jewish calendar */
                7  => I18N::translateContext('GENITIVE', 'Adar'),
                /* I18N: a month in the Jewish calendar */
                -7 => I18N::translateContext('GENITIVE', 'Adar II'),
                /* I18N: a month in the Jewish calendar */
                8  => I18N::translateContext('GENITIVE', 'Nissan'),
                /* I18N: a month in the Jewish calendar */
                9  => I18N::translateContext('GENITIVE', 'Iyar'),
                /* I18N: a month in the Jewish calendar */
                10 => I18N::translateContext('GENITIVE', 'Sivan'),
                /* I18N: a month in the Jewish calendar */
                11 => I18N::translateContext('GENITIVE', 'Tamuz'),
                /* I18N: a month in the Jewish calendar */
                12 => I18N::translateContext('GENITIVE', 'Av'),
                /* I18N: a month in the Jewish calendar */
                13 => I18N::translateContext('GENITIVE', 'Elul'),
            ];
        }

        if ($month_number === 7 && $leap_year) {
            return $translated_month_names[-7];
        } else {
            return $translated_month_names[$month_number];
        }
    }

    /**
     * Full month name in locative case.
     *
     * @param int  $month_number
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameLocativeCase($month_number, $leap_year)
    {
        static $translated_month_names;

        if ($translated_month_names === null) {
            $translated_month_names = [
                0  => '',
                /* I18N: a month in the Jewish calendar */
                1  => I18N::translateContext('LOCATIVE', 'Tishrei'),
                /* I18N: a month in the Jewish calendar */
                2  => I18N::translateContext('LOCATIVE', 'Heshvan'),
                /* I18N: a month in the Jewish calendar */
                3  => I18N::translateContext('LOCATIVE', 'Kislev'),
                /* I18N: a month in the Jewish calendar */
                4  => I18N::translateContext('LOCATIVE', 'Tevet'),
                /* I18N: a month in the Jewish calendar */
                5  => I18N::translateContext('LOCATIVE', 'Shevat'),
                /* I18N: a month in the Jewish calendar */
                6  => I18N::translateContext('LOCATIVE', 'Adar I'),
                /* I18N: a month in the Jewish calendar */
                7  => I18N::translateContext('LOCATIVE', 'Adar'),
                /* I18N: a month in the Jewish calendar */
                -7 => I18N::translateContext('LOCATIVE', 'Adar II'),
                /* I18N: a month in the Jewish calendar */
                8  => I18N::translateContext('LOCATIVE', 'Nissan'),
                /* I18N: a month in the Jewish calendar */
                9  => I18N::translateContext('LOCATIVE', 'Iyar'),
                /* I18N: a month in the Jewish calendar */
                10 => I18N::translateContext('LOCATIVE', 'Sivan'),
                /* I18N: a month in the Jewish calendar */
                11 => I18N::translateContext('LOCATIVE', 'Tamuz'),
                /* I18N: a month in the Jewish calendar */
                12 => I18N::translateContext('LOCATIVE', 'Av'),
                /* I18N: a month in the Jewish calendar */
                13 => I18N::translateContext('LOCATIVE', 'Elul'),
            ];
        }

        if ($month_number === 7 && $leap_year) {
            return $translated_month_names[-7];
        } else {
            return $translated_month_names[$month_number];
        }
    }

    /**
     * Full month name in instrumental case.
     *
     * @param int  $month_number
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameInstrumentalCase($month_number, $leap_year)
    {
        static $translated_month_names;

        if ($translated_month_names === null) {
            $translated_month_names = [
                0  => '',
                /* I18N: a month in the Jewish calendar */
                1  => I18N::translateContext('INSTRUMENTAL', 'Tishrei'),
                /* I18N: a month in the Jewish calendar */
                2  => I18N::translateContext('INSTRUMENTAL', 'Heshvan'),
                /* I18N: a month in the Jewish calendar */
                3  => I18N::translateContext('INSTRUMENTAL', 'Kislev'),
                /* I18N: a month in the Jewish calendar */
                4  => I18N::translateContext('INSTRUMENTAL', 'Tevet'),
                /* I18N: a month in the Jewish calendar */
                5  => I18N::translateContext('INSTRUMENTAL', 'Shevat'),
                /* I18N: a month in the Jewish calendar */
                6  => I18N::translateContext('INSTRUMENTAL', 'Adar I'),
                /* I18N: a month in the Jewish calendar */
                7  => I18N::translateContext('INSTRUMENTAL', 'Adar'),
                /* I18N: a month in the Jewish calendar */
                -7 => I18N::translateContext('INSTRUMENTAL', 'Adar II'),
                /* I18N: a month in the Jewish calendar */
                8  => I18N::translateContext('INSTRUMENTAL', 'Nissan'),
                /* I18N: a month in the Jewish calendar */
                9  => I18N::translateContext('INSTRUMENTAL', 'Iyar'),
                /* I18N: a month in the Jewish calendar */
                10 => I18N::translateContext('INSTRUMENTAL', 'Sivan'),
                /* I18N: a month in the Jewish calendar */
                11 => I18N::translateContext('INSTRUMENTAL', 'Tamuz'),
                /* I18N: a month in the Jewish calendar */
                12 => I18N::translateContext('INSTRUMENTAL', 'Av'),
                /* I18N: a month in the Jewish calendar */
                13 => I18N::translateContext('INSTRUMENTAL', 'Elul'),
            ];
        }

        if ($month_number === 7 && $leap_year) {
            return $translated_month_names[-7];
        } else {
            return $translated_month_names[$month_number];
        }
    }

    /**
     * Abbreviated month name
     *
     * @param int  $month_number
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameAbbreviated($month_number, $leap_year)
    {
        return self::monthNameNominativeCase($month_number, $leap_year);
    }

    /**
     * Which months follows this one? Calendars with leap-months should provide their own implementation.
     *
     * @return int[]
     */
    protected function nextMonth()
    {
        if ($this->m == 6 && !$this->isLeapYear()) {
            return [
                $this->y,
                8,
            ];
        } else {
            return [
                $this->y + ($this->m == 13 ? 1 : 0),
                ($this->m % 13) + 1,
            ];
        }
    }
}
