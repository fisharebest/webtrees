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

use Fisharebest\ExtCalendar\ArabicCalendar;
use Fisharebest\Webtrees\I18N;

/**
 * Definitions for Hijri dates.
 *
 * Note that these are "theoretical" dates.
 * "True" dates are based on local lunar observations, and can be a +/- one day.
 */
class HijriDate extends AbstractCalendarDate
{
    // GEDCOM calendar escape
    public const string ESCAPE = '@#DHIJRI@';

    // Convert GEDCOM month names to month numbers
    protected const array MONTH_TO_NUMBER = [
        'MUHAR' => 1,
        'SAFAR' => 2,
        'RABIA' => 3,
        'RABIT' => 4,
        'JUMAA' => 5,
        'JUMAT' => 6,
        'RAJAB' => 7,
        'SHAAB' => 8,
        'RAMAD' => 9,
        'SHAWW' => 10,
        'DHUAQ' => 11,
        'DHUAH' => 12,
    ];

    protected const array NUMBER_TO_MONTH = [
        1  => 'MUHAR',
        2  => 'SAFAR',
        3  => 'RABIA',
        4  => 'RABIT',
        5  => 'JUMAA',
        6  => 'JUMAT',
        7  => 'RAJAB',
        8  => 'SHAAB',
        9  => 'RAMAD',
        10 => 'SHAWW',
        11 => 'DHUAQ',
        12 => 'DHUAH',
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
        $this->calendar = new ArabicCalendar();
        parent::__construct($date);
    }

    /**
     * Full month name in nominative case.
     *
     * @param int<0,12> $month
     * @param bool $leap_year Some calendars use leap months
     */
    protected function monthNameNominativeCase(int $month, bool $leap_year): string
    {
        return match ($month) {
            0 => '',
            1 => I18N::translateContext('NOMINATIVE', 'Muḥarram'),
            2 => I18N::translateContext('NOMINATIVE', 'Ṣafar'),
            3 => I18N::translateContext('NOMINATIVE', 'Rabiʿ al-awwal'),
            4 => I18N::translateContext('NOMINATIVE', 'Rabiʿ al-thani'),
            5 => I18N::translateContext('NOMINATIVE', 'Jumādá al-awwal'),
            6 => I18N::translateContext('NOMINATIVE', 'Jumādá al-thānī'),
            7 => I18N::translateContext('NOMINATIVE', 'Rajab'),
            8 => I18N::translateContext('NOMINATIVE', 'Shaʿbān'),
            9 => I18N::translateContext('NOMINATIVE', 'Ramadan'),
            10 => I18N::translateContext('NOMINATIVE', 'Shawwal'),
            11 => I18N::translateContext('NOMINATIVE', 'Dhū al-Qiʿdah'),
            12 => I18N::translateContext('NOMINATIVE', 'Dhū al-Ḥijjah'),
        };
    }

    /**
     * Full month name in genitive case.
     *
     * @param int<0,12> $month
     * @param bool $leap_year Some calendars use leap months
     */
    protected function monthNameGenitiveCase(int $month, bool $leap_year): string
    {
        return match ($month) {
            0 => '',
            1 => I18N::translateContext('GENITIVE', 'Muḥarram'),
            2 => I18N::translateContext('GENITIVE', 'Ṣafar'),
            3 => I18N::translateContext('GENITIVE', 'Rabiʿ al-awwal'),
            4 => I18N::translateContext('GENITIVE', 'Rabiʿ al-thani'),
            5 => I18N::translateContext('GENITIVE', 'Jumādá al-awwal'),
            6 => I18N::translateContext('GENITIVE', 'Jumādá al-thānī'),
            7 => I18N::translateContext('GENITIVE', 'Rajab'),
            8 => I18N::translateContext('GENITIVE', 'Shaʿbān'),
            9 => I18N::translateContext('GENITIVE', 'Ramadan'),
            10 => I18N::translateContext('GENITIVE', 'Shawwal'),
            11 => I18N::translateContext('GENITIVE', 'Dhū al-Qiʿdah'),
            12 => I18N::translateContext('GENITIVE', 'Dhū al-Ḥijjah'),
        };
    }

    /**
     * Full month name in locative case.
     *
     * @param int<0,12> $month
     * @param bool $leap_year Some calendars use leap months
     */
    protected function monthNameLocativeCase(int $month, bool $leap_year): string
    {
        return match ($month) {
            0 => '',
            1 => I18N::translateContext('LOCATIVE', 'Muḥarram'),
            2 => I18N::translateContext('LOCATIVE', 'Ṣafar'),
            3 => I18N::translateContext('LOCATIVE', 'Rabiʿ al-awwal'),
            4 => I18N::translateContext('LOCATIVE', 'Rabiʿ al-thani'),
            5 => I18N::translateContext('LOCATIVE', 'Jumādá al-awwal'),
            6 => I18N::translateContext('LOCATIVE', 'Jumādá al-thānī'),
            7 => I18N::translateContext('LOCATIVE', 'Rajab'),
            8 => I18N::translateContext('LOCATIVE', 'Shaʿbān'),
            9 => I18N::translateContext('LOCATIVE', 'Ramadan'),
            10 => I18N::translateContext('LOCATIVE', 'Shawwal'),
            11 => I18N::translateContext('LOCATIVE', 'Dhū al-Qiʿdah'),
            12 => I18N::translateContext('LOCATIVE', 'Dhū al-Ḥijjah'),
        };
    }

    /**
     * Full month name in instrumental case.
     *
     * @param int<0,12> $month
     * @param bool $leap_year Some calendars use leap months
     */
    protected function monthNameInstrumentalCase(int $month, bool $leap_year): string
    {
        return match ($month) {
            0 => '',
            1 => I18N::translateContext('INSTRUMENTAL', 'Muḥarram'),
            2 => I18N::translateContext('INSTRUMENTAL', 'Ṣafar'),
            3 => I18N::translateContext('INSTRUMENTAL', 'Rabiʿ al-awwal'),
            4 => I18N::translateContext('INSTRUMENTAL', 'Rabiʿ al-thani'),
            5 => I18N::translateContext('INSTRUMENTAL', 'Jumādá al-awwal'),
            6 => I18N::translateContext('INSTRUMENTAL', 'Jumādá al-thānī'),
            7 => I18N::translateContext('INSTRUMENTAL', 'Rajab'),
            8 => I18N::translateContext('INSTRUMENTAL', 'Shaʿbān'),
            9 => I18N::translateContext('INSTRUMENTAL', 'Ramadan'),
            10 => I18N::translateContext('INSTRUMENTAL', 'Shawwal'),
            11 => I18N::translateContext('INSTRUMENTAL', 'Dhū al-Qiʿdah'),
            12 => I18N::translateContext('INSTRUMENTAL', 'Dhū al-Ḥijjah'),
        };
    }

    /**
     * Abbreviated month name
     *
     * @param int<0,12> $month
     * @param bool $leap_year Some calendars use leap months
     */
    protected function monthNameAbbreviated(int $month, bool $leap_year): string
    {
        return $this->monthNameNominativeCase($month, $leap_year);
    }
}
