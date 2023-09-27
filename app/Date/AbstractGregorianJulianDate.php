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

use Fisharebest\Webtrees\I18N;

/**
 * Common definitions for Gregorian and Julian dates.
 */
abstract class AbstractGregorianJulianDate extends AbstractCalendarDate
{
    // Convert GEDCOM month names to month numbers
    protected const MONTH_ABBREVIATIONS = [
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

    protected const MONTH_TO_NUMBER = [
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

    protected const NUMBER_TO_MONTH = [
        1  => 'JAN',
        2  => 'FEB',
        3  => 'MAR',
        4  => 'APR',
        5  => 'MAY',
        6  => 'JUN',
        7  => 'JUL',
        8  => 'AUG',
        9  => 'SEP',
        10 => 'OCT',
        11 => 'NOV',
        12 => 'DEC',
    ];

    /**
     * Full month name in nominative case.
     *
     * We put these in the base class, to save duplicating it in the Julian and Gregorian calendars.
     *
     * @param int  $month
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameNominativeCase(int $month, bool $leap_year): string
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

        return $translated_month_names[$month];
    }

    /**
     * Full month name in genitive case.
     *
     * We put these in the base class, to save duplicating it in the Julian and Gregorian calendars.
     *
     * @param int  $month
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameGenitiveCase(int $month, bool $leap_year): string
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

        return $translated_month_names[$month];
    }

    /**
     * Full month name in locative case.
     *
     * We put these in the base class, to save duplicating it in the Julian and Gregorian calendars.
     *
     * @param int  $month
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameLocativeCase(int $month, bool $leap_year): string
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

        return $translated_month_names[$month];
    }

    /**
     * Full month name in instrumental case.
     *
     * We put these in the base class, to save duplicating it in the Julian and Gregorian calendars.
     *
     * @param int  $month
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameInstrumentalCase(int $month, bool $leap_year): string
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

        return $translated_month_names[$month];
    }

    /**
     * Abbreviated month name
     *
     * @param int  $month
     * @param bool $leap_year Some calendars use leap months
     *
     * @return string
     */
    protected function monthNameAbbreviated(int $month, bool $leap_year): string
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

        return $translated_month_names[$month];
    }
}
