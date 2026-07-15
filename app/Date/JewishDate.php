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

use Fisharebest\ExtCalendar\JewishCalendar;
use Fisharebest\Webtrees\I18N;

/**
 * Definitions for the Jewish calendar
 */
class JewishDate extends AbstractCalendarDate
{
    // GEDCOM calendar escape
    public const string ESCAPE = '@#DHEBREW@';

    // Convert GEDCOM month names to month numbers
    protected const array MONTH_TO_NUMBER = [
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

    protected const array NUMBER_TO_MONTH = [
        1  => 'TSH',
        2  => 'CSH',
        3  => 'KSL',
        4  => 'TVT',
        5  => 'SHV',
        6  => 'ADR',
        7  => 'ADS',
        8  => 'NSN',
        9  => 'IYR',
        10 => 'SVN',
        11 => 'TMZ',
        12 => 'AAV',
        13 => 'ELL',
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
        $this->calendar = new JewishCalendar();
        parent::__construct($date);
    }

    /**
     * Generate the %j format for a date.
     */
    protected function formatDay(): string
    {
        if (I18N::locale()->script()->code() === 'Hebr') {
            return (new JewishCalendar())->numberToHebrewNumerals($this->day, true);
        }

        return parent::formatDay();
    }

    /**
     * Generate the %y format for a date.
     *
     * NOTE Short year is NOT a 2-digit year. It is for calendars such as hebrew
     * which have a 3-digit form of 4-digit years.
     */
    protected function formatShortYear(): string
    {
        if (I18N::locale()->script()->code() === 'Hebr') {
            return (new JewishCalendar())->numberToHebrewNumerals($this->year, false);
        }

        return parent::formatLongYear();
    }

    /**
     * Generate the %Y format for a date.
     */
    protected function formatLongYear(): string
    {
        if (I18N::locale()->script()->code() === 'Hebr') {
            return (new JewishCalendar())->numberToHebrewNumerals($this->year, true);
        }

        return parent::formatLongYear();
    }

    /**
     * Full month name in nominative case.
     *
     * @param int<0,13> $month
     * @param bool $leap_year Some calendars use leap months
     */
    protected function monthNameNominativeCase(int $month, bool $leap_year): string
    {
        if ($month === 7 && $leap_year) {
            return I18N::translateContext('NOMINATIVE', 'Adar II');
        }

        return match ($month) {
            0 => '',
            1 => I18N::translateContext('NOMINATIVE', 'Tishrei'),
            2 => I18N::translateContext('NOMINATIVE', 'Heshvan'),
            3 => I18N::translateContext('NOMINATIVE', 'Kislev'),
            4 => I18N::translateContext('NOMINATIVE', 'Tevet'),
            5 => I18N::translateContext('NOMINATIVE', 'Shevat'),
            6 => I18N::translateContext('NOMINATIVE', 'Adar I'),
            7 => I18N::translateContext('NOMINATIVE', 'Adar'),
            8 => I18N::translateContext('NOMINATIVE', 'Nissan'),
            9 => I18N::translateContext('NOMINATIVE', 'Iyar'),
            10 => I18N::translateContext('NOMINATIVE', 'Sivan'),
            11 => I18N::translateContext('NOMINATIVE', 'Tamuz'),
            12 => I18N::translateContext('NOMINATIVE', 'Av'),
            13 => I18N::translateContext('NOMINATIVE', 'Elul'),
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
        if ($month === 7 && $leap_year) {
            return I18N::translateContext('GENITIVE', 'Adar II');
        }

        return match ($month) {
            0 => '',
            1 => I18N::translateContext('GENITIVE', 'Tishrei'),
            2 => I18N::translateContext('GENITIVE', 'Heshvan'),
            3 => I18N::translateContext('GENITIVE', 'Kislev'),
            4 => I18N::translateContext('GENITIVE', 'Tevet'),
            5 => I18N::translateContext('GENITIVE', 'Shevat'),
            6 => I18N::translateContext('GENITIVE', 'Adar I'),
            7 => I18N::translateContext('GENITIVE', 'Adar'),
            8 => I18N::translateContext('GENITIVE', 'Nissan'),
            9 => I18N::translateContext('GENITIVE', 'Iyar'),
            10 => I18N::translateContext('GENITIVE', 'Sivan'),
            11 => I18N::translateContext('GENITIVE', 'Tamuz'),
            12 => I18N::translateContext('GENITIVE', 'Av'),
            13 => I18N::translateContext('GENITIVE', 'Elul'),
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
        if ($month === 7 && $leap_year) {
            return I18N::translateContext('LOCATIVE', 'Adar II');
        }

        return match ($month) {
            0 => '',
            1 => I18N::translateContext('LOCATIVE', 'Tishrei'),
            2 => I18N::translateContext('LOCATIVE', 'Heshvan'),
            3 => I18N::translateContext('LOCATIVE', 'Kislev'),
            4 => I18N::translateContext('LOCATIVE', 'Tevet'),
            5 => I18N::translateContext('LOCATIVE', 'Shevat'),
            6 => I18N::translateContext('LOCATIVE', 'Adar I'),
            7 => I18N::translateContext('LOCATIVE', 'Adar'),
            8 => I18N::translateContext('LOCATIVE', 'Nissan'),
            9 => I18N::translateContext('LOCATIVE', 'Iyar'),
            10 => I18N::translateContext('LOCATIVE', 'Sivan'),
            11 => I18N::translateContext('LOCATIVE', 'Tamuz'),
            12 => I18N::translateContext('LOCATIVE', 'Av'),
            13 => I18N::translateContext('LOCATIVE', 'Elul'),
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
        if ($month === 7 && $leap_year) {
            return I18N::translateContext('INSTRUMENTAL', 'Adar II');
        }

        return match ($month) {
            0 => '',
            1 => I18N::translateContext('INSTRUMENTAL', 'Tishrei'),
            2 => I18N::translateContext('INSTRUMENTAL', 'Heshvan'),
            3 => I18N::translateContext('INSTRUMENTAL', 'Kislev'),
            4 => I18N::translateContext('INSTRUMENTAL', 'Tevet'),
            5 => I18N::translateContext('INSTRUMENTAL', 'Shevat'),
            6 => I18N::translateContext('INSTRUMENTAL', 'Adar I'),
            7 => I18N::translateContext('INSTRUMENTAL', 'Adar'),
            8 => I18N::translateContext('INSTRUMENTAL', 'Nissan'),
            9 => I18N::translateContext('INSTRUMENTAL', 'Iyar'),
            10 => I18N::translateContext('INSTRUMENTAL', 'Sivan'),
            11 => I18N::translateContext('INSTRUMENTAL', 'Tamuz'),
            12 => I18N::translateContext('INSTRUMENTAL', 'Av'),
            13 => I18N::translateContext('INSTRUMENTAL', 'Elul'),
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
     * Which months follows this one? Calendars with leap-months should provide their own implementation.
     *
     * @return array<int>
     */
    protected function nextMonth(): array
    {
        if ($this->month === 6 && !$this->isLeapYear()) {
            return [
                $this->year,
                8,
            ];
        }

        return [
            $this->year + ($this->month === 13 ? 1 : 0),
            $this->month % 13 + 1,
        ];
    }
}
