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

use Fisharebest\ExtCalendar\FrenchCalendar;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\RomanNumeralsService;

/**
 * Definitions for French Republican dates.
 */
class FrenchDate extends AbstractCalendarDate
{
    // GEDCOM calendar escape
    public const string ESCAPE = '@#DFRENCH R@';

    // Convert GEDCOM month names to month numbers
    protected const array MONTH_TO_NUMBER = [
        'VEND' => 1,
        'BRUM' => 2,
        'FRIM' => 3,
        'NIVO' => 4,
        'PLUV' => 5,
        'VENT' => 6,
        'GERM' => 7,
        'FLOR' => 8,
        'PRAI' => 9,
        'MESS' => 10,
        'THER' => 11,
        'FRUC' => 12,
        'COMP' => 13,
    ];

    protected const array NUMBER_TO_MONTH = [
        1 => 'VEND',
        2 => 'BRUM',
        3 => 'FRIM',
        4 => 'NIVO',
        5 => 'PLUV',
        6 => 'VENT',
        7 => 'GERM',
        8 => 'FLOR',
        9 => 'PRAI',
        10 => 'MESS',
        11 => 'THER',
        12 => 'FRUC',
        13 => 'COMP',
    ];

    private RomanNumeralsService $roman_numerals_service;

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
        $this->roman_numerals_service = new RomanNumeralsService();
        $this->calendar               = new FrenchCalendar();

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
                /* I18N: a month in the French republican calendar */
                1  => I18N::translateContext('NOMINATIVE', 'Vendemiaire'),
                /* I18N: a month in the French republican calendar */
                2  => I18N::translateContext('NOMINATIVE', 'Brumaire'),
                /* I18N: a month in the French republican calendar */
                3  => I18N::translateContext('NOMINATIVE', 'Frimaire'),
                /* I18N: a month in the French republican calendar */
                4  => I18N::translateContext('NOMINATIVE', 'Nivose'),
                /* I18N: a month in the French republican calendar */
                5  => I18N::translateContext('NOMINATIVE', 'Pluviose'),
                /* I18N: a month in the French republican calendar */
                6  => I18N::translateContext('NOMINATIVE', 'Ventose'),
                /* I18N: a month in the French republican calendar */
                /* I18N: a month in the French republican calendar */
                7  => I18N::translateContext('NOMINATIVE', 'Germinal'),
                /* I18N: a month in the French republican calendar */
                8  => I18N::translateContext('NOMINATIVE', 'Floreal'),
                /* I18N: a month in the French republican calendar */
                9  => I18N::translateContext('NOMINATIVE', 'Prairial'),
                /* I18N: a month in the French republican calendar */
                10 => I18N::translateContext('NOMINATIVE', 'Messidor'),
                /* I18N: a month in the French republican calendar */
                11 => I18N::translateContext('NOMINATIVE', 'Thermidor'),
                /* I18N: a month in the French republican calendar */
                12 => I18N::translateContext('NOMINATIVE', 'Fructidor'),
                /* I18N: a month in the French republican calendar */
                13 => I18N::translateContext('NOMINATIVE', 'jours complementaires'),
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
                /* I18N: a month in the French republican calendar */
                1  => I18N::translateContext('GENITIVE', 'Vendemiaire'),
                /* I18N: a month in the French republican calendar */
                2  => I18N::translateContext('GENITIVE', 'Brumaire'),
                /* I18N: a month in the French republican calendar */
                3  => I18N::translateContext('GENITIVE', 'Frimaire'),
                /* I18N: a month in the French republican calendar */
                4  => I18N::translateContext('GENITIVE', 'Nivose'),
                /* I18N: a month in the French republican calendar */
                5  => I18N::translateContext('GENITIVE', 'Pluviose'),
                /* I18N: a month in the French republican calendar */
                6  => I18N::translateContext('GENITIVE', 'Ventose'),
                /* I18N: a month in the French republican calendar */
                7  => I18N::translateContext('GENITIVE', 'Germinal'),
                /* I18N: a month in the French republican calendar */
                8  => I18N::translateContext('GENITIVE', 'Floreal'),
                /* I18N: a month in the French republican calendar */
                9  => I18N::translateContext('GENITIVE', 'Prairial'),
                /* I18N: a month in the French republican calendar */
                10 => I18N::translateContext('GENITIVE', 'Messidor'),
                /* I18N: a month in the French republican calendar */
                11 => I18N::translateContext('GENITIVE', 'Thermidor'),
                /* I18N: a month in the French republican calendar */
                12 => I18N::translateContext('GENITIVE', 'Fructidor'),
                /* I18N: a month in the French republican calendar */
                13 => I18N::translateContext('GENITIVE', 'jours complementaires'),
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
                /* I18N: a month in the French republican calendar */
                1  => I18N::translateContext('LOCATIVE', 'Vendemiaire'),
                /* I18N: a month in the French republican calendar */
                2  => I18N::translateContext('LOCATIVE', 'Brumaire'),
                /* I18N: a month in the French republican calendar */
                3  => I18N::translateContext('LOCATIVE', 'Frimaire'),
                /* I18N: a month in the French republican calendar */
                4  => I18N::translateContext('LOCATIVE', 'Nivose'),
                /* I18N: a month in the French republican calendar */
                5  => I18N::translateContext('LOCATIVE', 'Pluviose'),
                /* I18N: a month in the French republican calendar */
                6  => I18N::translateContext('LOCATIVE', 'Ventose'),
                /* I18N: a month in the French republican calendar */
                7  => I18N::translateContext('LOCATIVE', 'Germinal'),
                /* I18N: a month in the French republican calendar */
                8  => I18N::translateContext('LOCATIVE', 'Floreal'),
                /* I18N: a month in the French republican calendar */
                9  => I18N::translateContext('LOCATIVE', 'Prairial'),
                /* I18N: a month in the French republican calendar */
                10 => I18N::translateContext('LOCATIVE', 'Messidor'),
                /* I18N: a month in the French republican calendar */
                11 => I18N::translateContext('LOCATIVE', 'Thermidor'),
                /* I18N: a month in the French republican calendar */
                12 => I18N::translateContext('LOCATIVE', 'Fructidor'),
                /* I18N: a month in the French republican calendar */
                13 => I18N::translateContext('LOCATIVE', 'jours complementaires'),
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
                /* I18N: a month in the French republican calendar */
                1  => I18N::translateContext('INSTRUMENTAL', 'Vendemiaire'),
                /* I18N: a month in the French republican calendar */
                2  => I18N::translateContext('INSTRUMENTAL', 'Brumaire'),
                /* I18N: a month in the French republican calendar */
                3  => I18N::translateContext('INSTRUMENTAL', 'Frimaire'),
                /* I18N: a month in the French republican calendar */
                4  => I18N::translateContext('INSTRUMENTAL', 'Nivose'),
                /* I18N: a month in the French republican calendar */
                5  => I18N::translateContext('INSTRUMENTAL', 'Pluviose'),
                /* I18N: a month in the French republican calendar */
                6  => I18N::translateContext('INSTRUMENTAL', 'Ventose'),
                /* I18N: a month in the French republican calendar */
                7  => I18N::translateContext('INSTRUMENTAL', 'Germinal'),
                /* I18N: a month in the French republican calendar */
                8  => I18N::translateContext('INSTRUMENTAL', 'Floreal'),
                /* I18N: a month in the French republican calendar */
                9  => I18N::translateContext('INSTRUMENTAL', 'Prairial'),
                /* I18N: a month in the French republican calendar */
                10 => I18N::translateContext('INSTRUMENTAL', 'Messidor'),
                /* I18N: a month in the French republican calendar */
                11 => I18N::translateContext('INSTRUMENTAL', 'Thermidor'),
                /* I18N: a month in the French republican calendar */
                12 => I18N::translateContext('INSTRUMENTAL', 'Fructidor'),
                /* I18N: a month in the French republican calendar */
                13 => I18N::translateContext('INSTRUMENTAL', 'jours complementaires'),
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
        return $this->monthNameNominativeCase($month, $leap_year);
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
                /* I18N: The first day in the French republican calendar */
                0 => I18N::translate('Primidi'),
                /* I18N: The second day in the French republican calendar */
                1 => I18N::translate('Duodi'),
                /* I18N: The third day in the French republican calendar */
                2 => I18N::translate('Tridi'),
                /* I18N: The fourth day in the French republican calendar */
                3 => I18N::translate('Quartidi'),
                /* I18N: The fifth day in the French republican calendar */
                4 => I18N::translate('Quintidi'),
                /* I18N: The sixth day in the French republican calendar */
                5 => I18N::translate('Sextidi'),
                /* I18N: The seventh day in the French republican calendar */
                6 => I18N::translate('Septidi'),
                /* I18N: The eighth day in the French republican calendar */
                7 => I18N::translate('Octidi'),
                /* I18N: The ninth day in the French republican calendar */
                8 => I18N::translate('Nonidi'),
                /* I18N: The tenth day in the French republican calendar */
                9 => I18N::translate('Decidi'),
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
        return $this->dayNames($day_number);
    }

    /**
     * Generate the %Y format for a date.
     *
     * @return string
     */
    protected function formatLongYear(): string
    {
        return 'An ' . $this->roman_numerals_service->numberToRomanNumerals($this->year);
    }
}
