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

namespace Fisharebest\Webtrees\Date;

use Fisharebest\ExtCalendar\PersianCalendar;
use Fisharebest\Webtrees\I18N;

/**
 * Definitions for Jalali dates.
 */
class JalaliDate extends AbstractCalendarDate
{
    // GEDCOM calendar escape
    public const ESCAPE = '@#DJALALI@';

    // Convert GEDCOM month names to month numbers
    protected const MONTH_TO_NUMBER = [
        'FARVA' => 1,
        'ORDIB' => 2,
        'KHORD' => 3,
        'TIR'   => 4,
        'MORDA' => 5,
        'SHAHR' => 6,
        'MEHR'  => 7,
        'ABAN'  => 8,
        'AZAR'  => 9,
        'DEY'   => 10,
        'BAHMA' => 11,
        'ESFAN' => 12,
    ];

    protected const NUMBER_TO_MONTH = [
        1  => 'FARVA',
        2  => 'ORDIB',
        3  => 'KHORD',
        4  => 'TIR',
        5  => 'MORDA',
        6  => 'SHAHR',
        7  => 'MEHR',
        8  => 'ABAN',
        9  => 'AZAR',
        10 => 'DEY',
        11 => 'BAHMA',
        12 => 'ESFAN',
    ];

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
        $this->calendar = new PersianCalendar();
        parent::__construct($date);
    }

    /**
     * Full month name in nominative case.
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
                /* I18N: 1st month in the Persian/Jalali calendar */
                1  => I18N::translateContext('NOMINATIVE', 'Farvardin'),
                /* I18N: 2nd month in the Persian/Jalali calendar */
                2  => I18N::translateContext('NOMINATIVE', 'Ordibehesht'),
                /* I18N: 3rd month in the Persian/Jalali calendar */
                3  => I18N::translateContext('NOMINATIVE', 'Khordad'),
                /* I18N: 4th month in the Persian/Jalali calendar */
                4  => I18N::translateContext('NOMINATIVE', 'Tir'),
                /* I18N: 5th month in the Persian/Jalali calendar */
                5  => I18N::translateContext('NOMINATIVE', 'Mordad'),
                /* I18N: 6th month in the Persian/Jalali calendar */
                6  => I18N::translateContext('NOMINATIVE', 'Shahrivar'),
                /* I18N: 7th month in the Persian/Jalali calendar */
                7  => I18N::translateContext('NOMINATIVE', 'Mehr'),
                /* I18N: 8th month in the Persian/Jalali calendar */
                8  => I18N::translateContext('NOMINATIVE', 'Aban'),
                /* I18N: 9th month in the Persian/Jalali calendar */
                9  => I18N::translateContext('NOMINATIVE', 'Azar'),
                /* I18N: 10th month in the Persian/Jalali calendar */
                10 => I18N::translateContext('NOMINATIVE', 'Dey'),
                /* I18N: 11th month in the Persian/Jalali calendar */
                11 => I18N::translateContext('NOMINATIVE', 'Bahman'),
                /* I18N: 12th month in the Persian/Jalali calendar */
                12 => I18N::translateContext('NOMINATIVE', 'Esfand'),
            ];
        }

        return $translated_month_names[$month];
    }

    /**
     * Full month name in genitive case.
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
                /* I18N: 1st month in the Persian/Jalali calendar */
                1  => I18N::translateContext('GENITIVE', 'Farvardin'),
                /* I18N: 2nd month in the Persian/Jalali calendar */
                2  => I18N::translateContext('GENITIVE', 'Ordibehesht'),
                /* I18N: 3rd month in the Persian/Jalali calendar */
                3  => I18N::translateContext('GENITIVE', 'Khordad'),
                /* I18N: 4th month in the Persian/Jalali calendar */
                4  => I18N::translateContext('GENITIVE', 'Tir'),
                /* I18N: 5th month in the Persian/Jalali calendar */
                5  => I18N::translateContext('GENITIVE', 'Mordad'),
                /* I18N: 6th month in the Persian/Jalali calendar */
                6  => I18N::translateContext('GENITIVE', 'Shahrivar'),
                /* I18N: 7th month in the Persian/Jalali calendar */
                7  => I18N::translateContext('GENITIVE', 'Mehr'),
                /* I18N: 8th month in the Persian/Jalali calendar */
                8  => I18N::translateContext('GENITIVE', 'Aban'),
                /* I18N: 9th month in the Persian/Jalali calendar */
                9  => I18N::translateContext('GENITIVE', 'Azar'),
                /* I18N: 10th month in the Persian/Jalali calendar */
                10 => I18N::translateContext('GENITIVE', 'Dey'),
                /* I18N: 11th month in the Persian/Jalali calendar */
                11 => I18N::translateContext('GENITIVE', 'Bahman'),
                /* I18N: 12th month in the Persian/Jalali calendar */
                12 => I18N::translateContext('GENITIVE', 'Esfand'),
            ];
        }

        return $translated_month_names[$month];
    }

    /**
     * Full month name in locative case.
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
                /* I18N: 1st month in the Persian/Jalali calendar */
                1  => I18N::translateContext('LOCATIVE', 'Farvardin'),
                /* I18N: 2nd month in the Persian/Jalali calendar */
                2  => I18N::translateContext('LOCATIVE', 'Ordibehesht'),
                /* I18N: 3rd month in the Persian/Jalali calendar */
                3  => I18N::translateContext('LOCATIVE', 'Khordad'),
                /* I18N: 4th month in the Persian/Jalali calendar */
                4  => I18N::translateContext('LOCATIVE', 'Tir'),
                /* I18N: 5th month in the Persian/Jalali calendar */
                5  => I18N::translateContext('LOCATIVE', 'Mordad'),
                /* I18N: 6th month in the Persian/Jalali calendar */
                6  => I18N::translateContext('LOCATIVE', 'Shahrivar'),
                /* I18N: 7th month in the Persian/Jalali calendar */
                7  => I18N::translateContext('LOCATIVE', 'Mehr'),
                /* I18N: 8th month in the Persian/Jalali calendar */
                8  => I18N::translateContext('LOCATIVE', 'Aban'),
                /* I18N: 9th month in the Persian/Jalali calendar */
                9  => I18N::translateContext('LOCATIVE', 'Azar'),
                /* I18N: 10th month in the Persian/Jalali calendar */
                10 => I18N::translateContext('LOCATIVE', 'Dey'),
                /* I18N: 11th month in the Persian/Jalali calendar */
                11 => I18N::translateContext('LOCATIVE', 'Bahman'),
                /* I18N: 12th month in the Persian/Jalali calendar */
                12 => I18N::translateContext('LOCATIVE', 'Esfand'),
            ];
        }

        return $translated_month_names[$month];
    }

    /**
     * Full month name in instrumental case.
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
                /* I18N: 1st month in the Persian/Jalali calendar */
                1  => I18N::translateContext('INSTRUMENTAL', 'Farvardin'),
                /* I18N: 2nd month in the Persian/Jalali calendar */
                2  => I18N::translateContext('INSTRUMENTAL', 'Ordibehesht'),
                /* I18N: 3rd month in the Persian/Jalali calendar */
                3  => I18N::translateContext('INSTRUMENTAL', 'Khordad'),
                /* I18N: 4th month in the Persian/Jalali calendar */
                4  => I18N::translateContext('INSTRUMENTAL', 'Tir'),
                /* I18N: 5th month in the Persian/Jalali calendar */
                5  => I18N::translateContext('INSTRUMENTAL', 'Mordad'),
                /* I18N: 6th month in the Persian/Jalali calendar */
                6  => I18N::translateContext('INSTRUMENTAL', 'Shahrivar'),
                /* I18N: 7th month in the Persian/Jalali calendar */
                7  => I18N::translateContext('INSTRUMENTAL', 'Mehr'),
                /* I18N: 8th month in the Persian/Jalali calendar */
                8  => I18N::translateContext('INSTRUMENTAL', 'Aban'),
                /* I18N: 9th month in the Persian/Jalali calendar */
                9  => I18N::translateContext('INSTRUMENTAL', 'Azar'),
                /* I18N: 10th month in the Persian/Jalali calendar */
                10 => I18N::translateContext('INSTRUMENTAL', 'Dey'),
                /* I18N: 11th month in the Persian/Jalali calendar */
                11 => I18N::translateContext('INSTRUMENTAL', 'Bahman'),
                /* I18N: 12th month in the Persian/Jalali calendar */
                12 => I18N::translateContext('INSTRUMENTAL', 'Esfand'),
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
                1  => I18N::translateContext('Abbreviation for Persian month: Farvardin', 'Far'),
                2  => I18N::translateContext('Abbreviation for Persian month: Ordibehesht', 'Ord'),
                3  => I18N::translateContext('Abbreviation for Persian month: Khordad', 'Khor'),
                4  => I18N::translateContext('Abbreviation for Persian month: Tir', 'Tir'),
                5  => I18N::translateContext('Abbreviation for Persian month: Mordad', 'Mor'),
                6  => I18N::translateContext('Abbreviation for Persian month: Shahrivar', 'Shah'),
                7  => I18N::translateContext('Abbreviation for Persian month: Mehr', 'Mehr'),
                8  => I18N::translateContext('Abbreviation for Persian month: Aban', 'Aban'),
                9  => I18N::translateContext('Abbreviation for Persian month: Azar', 'Azar'),
                10 => I18N::translateContext('Abbreviation for Persian month: Dey', 'Dey'),
                11 => I18N::translateContext('Abbreviation for Persian month: Bahman', 'Bah'),
                12 => I18N::translateContext('Abbreviation for Persian month: Esfand', 'Esf'),
            ];
        }

        return $translated_month_names[$month];
    }
}
