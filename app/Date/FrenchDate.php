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
     * @param int<0,13> $month
     * @param bool $leap_year Some calendars use leap months
     */
    protected function monthNameNominativeCase(int $month, bool $leap_year): string
    {
        return match ($month) {
            0 => '',
            1 => I18N::translateContext('NOMINATIVE', 'Vendemiaire'),
            2 => I18N::translateContext('NOMINATIVE', 'Brumaire'),
            3 => I18N::translateContext('NOMINATIVE', 'Frimaire'),
            4 => I18N::translateContext('NOMINATIVE', 'Nivose'),
            5 => I18N::translateContext('NOMINATIVE', 'Pluviose'),
            6 => I18N::translateContext('NOMINATIVE', 'Ventose'),
            7 => I18N::translateContext('NOMINATIVE', 'Germinal'),
            8 => I18N::translateContext('NOMINATIVE', 'Floreal'),
            9 => I18N::translateContext('NOMINATIVE', 'Prairial'),
            10 => I18N::translateContext('NOMINATIVE', 'Messidor'),
            11 => I18N::translateContext('NOMINATIVE', 'Thermidor'),
            12 => I18N::translateContext('NOMINATIVE', 'Fructidor'),
            13 => I18N::translateContext('NOMINATIVE', 'jours complementaires'),
        };
    }

    /**
     * Full month name in genitive case.
     *
     * @param int<0,13> $month
     * @param bool $leap_year Some calendars use leap months
     */
    protected function monthNameGenitiveCase(int $month, bool $leap_year): string
    {
        return match ($month) {
            0 => '',
            1 => I18N::translateContext('GENITIVE', 'Vendemiaire'),
            2 => I18N::translateContext('GENITIVE', 'Brumaire'),
            3 => I18N::translateContext('GENITIVE', 'Frimaire'),
            4 => I18N::translateContext('GENITIVE', 'Nivose'),
            5 => I18N::translateContext('GENITIVE', 'Pluviose'),
            6 => I18N::translateContext('GENITIVE', 'Ventose'),
            7 => I18N::translateContext('GENITIVE', 'Germinal'),
            8 => I18N::translateContext('GENITIVE', 'Floreal'),
            9 => I18N::translateContext('GENITIVE', 'Prairial'),
            10 => I18N::translateContext('GENITIVE', 'Messidor'),
            11 => I18N::translateContext('GENITIVE', 'Thermidor'),
            12 => I18N::translateContext('GENITIVE', 'Fructidor'),
            13 => I18N::translateContext('GENITIVE', 'jours complementaires'),
        };
    }

    /**
     * Full month name in locative case.
     *
     * @param int<0,13> $month
     * @param bool $leap_year Some calendars use leap months
     */
    protected function monthNameLocativeCase(int $month, bool $leap_year): string
    {
        return match ($month) {
            0 => '',
            1 => I18N::translateContext('LOCATIVE', 'Vendemiaire'),
            2 => I18N::translateContext('LOCATIVE', 'Brumaire'),
            3 => I18N::translateContext('LOCATIVE', 'Frimaire'),
            4 => I18N::translateContext('LOCATIVE', 'Nivose'),
            5 => I18N::translateContext('LOCATIVE', 'Pluviose'),
            6 => I18N::translateContext('LOCATIVE', 'Ventose'),
            7 => I18N::translateContext('LOCATIVE', 'Germinal'),
            8 => I18N::translateContext('LOCATIVE', 'Floreal'),
            9 => I18N::translateContext('LOCATIVE', 'Prairial'),
            10 => I18N::translateContext('LOCATIVE', 'Messidor'),
            11 => I18N::translateContext('LOCATIVE', 'Thermidor'),
            12 => I18N::translateContext('LOCATIVE', 'Fructidor'),
            13 => I18N::translateContext('LOCATIVE', 'jours complementaires'),
        };
    }

    /**
     * Full month name in instrumental case.
     *
     * @param int<0,13> $month
     * @param bool $leap_year Some calendars use leap months
     */
    protected function monthNameInstrumentalCase(int $month, bool $leap_year): string
    {
        return match ($month) {
            0 => '',
            1 => I18N::translateContext('INSTRUMENTAL', 'Vendemiaire'),
            2 => I18N::translateContext('INSTRUMENTAL', 'Brumaire'),
            3 => I18N::translateContext('INSTRUMENTAL', 'Frimaire'),
            4 => I18N::translateContext('INSTRUMENTAL', 'Nivose'),
            5 => I18N::translateContext('INSTRUMENTAL', 'Pluviose'),
            6 => I18N::translateContext('INSTRUMENTAL', 'Ventose'),
            7 => I18N::translateContext('INSTRUMENTAL', 'Germinal'),
            8 => I18N::translateContext('INSTRUMENTAL', 'Floreal'),
            9 => I18N::translateContext('INSTRUMENTAL', 'Prairial'),
            10 => I18N::translateContext('INSTRUMENTAL', 'Messidor'),
            11 => I18N::translateContext('INSTRUMENTAL', 'Thermidor'),
            12 => I18N::translateContext('INSTRUMENTAL', 'Fructidor'),
            13 => I18N::translateContext('INSTRUMENTAL', 'jours complementaires'),
        };
    }

    /**
     * Abbreviated month name
     *
     * @param int<0,13> $month
     * @param bool $leap_year Some calendars use leap months
     */
    protected function monthNameAbbreviated(int $month, bool $leap_year): string
    {
        return $this->monthNameNominativeCase($month, $leap_year);
    }

    /**
     * Full day of the week
     *
     * @param int<0,9> $day_number
     */
    public function dayNames(int $day_number): string
    {
        return match ($day_number) {
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
        };
    }

    /**
     * Abbreviated day of the week
     *
     * @param int<0,9> $day_number
     */
    protected function dayNamesAbbreviated(int $day_number): string
    {
        return $this->dayNames($day_number);
    }

    /**
     * Generate the %Y format for a date.
     */
    protected function formatLongYear(): string
    {
        return 'An ' . $this->roman_numerals_service->numberToRomanNumerals($this->year);
    }
}
