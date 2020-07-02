<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
    public const ESCAPE = '@#DHIJRI@';

    // Convert GEDCOM month names to month numbers
    protected const MONTH_ABBREVIATIONS = [
        ''      => 0,
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
                /* I18N: http://en.wikipedia.org/wiki/Muharram */
                1  => I18N::translateContext('NOMINATIVE', 'Muharram'),
                /* I18N: http://en.wikipedia.org/wiki/Safar */
                2  => I18N::translateContext('NOMINATIVE', 'Safar'),
                /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-awwal */
                3  => I18N::translateContext('NOMINATIVE', 'Rabi’ al-awwal'),
                /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-thani */
                4  => I18N::translateContext('NOMINATIVE', 'Rabi’ al-thani'),
                /* I18N: http://en.wikipedia.org/wiki/Jumada_al-awwal */
                5  => I18N::translateContext('NOMINATIVE', 'Jumada al-awwal'),
                /* I18N: http://en.wikipedia.org/wiki/Jumada_al-thani */
                6  => I18N::translateContext('NOMINATIVE', 'Jumada al-thani'),
                /* I18N: http://en.wikipedia.org/wiki/Rajab */
                7  => I18N::translateContext('NOMINATIVE', 'Rajab'),
                /* I18N: http://en.wikipedia.org/wiki/Sha%27aban */
                8  => I18N::translateContext('NOMINATIVE', 'Sha’aban'),
                /* I18N: http://en.wikipedia.org/wiki/Ramadan_%28calendar_month%29 */
                9  => I18N::translateContext('NOMINATIVE', 'Ramadan'),
                /* I18N: http://en.wikipedia.org/wiki/Shawwal */
                10 => I18N::translateContext('NOMINATIVE', 'Shawwal'),
                /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Qi%27dah */
                11 => I18N::translateContext('NOMINATIVE', 'Dhu al-Qi’dah'),
                /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Hijjah */
                12 => I18N::translateContext('NOMINATIVE', 'Dhu al-Hijjah'),
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
                /* I18N: http://en.wikipedia.org/wiki/Muharram */
                1  => I18N::translateContext('GENITIVE', 'Muharram'),
                /* I18N: http://en.wikipedia.org/wiki/Safar */
                2  => I18N::translateContext('GENITIVE', 'Safar'),
                /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-awwal */
                3  => I18N::translateContext('GENITIVE', 'Rabi’ al-awwal'),
                /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-thani */
                4  => I18N::translateContext('GENITIVE', 'Rabi’ al-thani'),
                /* I18N: http://en.wikipedia.org/wiki/Jumada_al-awwal */
                5  => I18N::translateContext('GENITIVE', 'Jumada al-awwal'),
                /* I18N: http://en.wikipedia.org/wiki/Jumada_al-thani */
                6  => I18N::translateContext('GENITIVE', 'Jumada al-thani'),
                /* I18N: http://en.wikipedia.org/wiki/Rajab */
                7  => I18N::translateContext('GENITIVE', 'Rajab'),
                /* I18N: http://en.wikipedia.org/wiki/Sha%27aban */
                8  => I18N::translateContext('GENITIVE', 'Sha’aban'),
                /* I18N: http://en.wikipedia.org/wiki/Ramadan_%28calendar_month%29 */
                9  => I18N::translateContext('GENITIVE', 'Ramadan'),
                /* I18N: http://en.wikipedia.org/wiki/Shawwal */
                10 => I18N::translateContext('GENITIVE', 'Shawwal'),
                /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Qi%27dah */
                11 => I18N::translateContext('GENITIVE', 'Dhu al-Qi’dah'),
                /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Hijjah */
                12 => I18N::translateContext('GENITIVE', 'Dhu al-Hijjah'),
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
                /* I18N: http://en.wikipedia.org/wiki/Muharram */
                1  => I18N::translateContext('LOCATIVE', 'Muharram'),
                /* I18N: http://en.wikipedia.org/wiki/Safar */
                2  => I18N::translateContext('LOCATIVE', 'Safar'),
                /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-awwal */
                3  => I18N::translateContext('LOCATIVE', 'Rabi’ al-awwal'),
                /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-thani */
                4  => I18N::translateContext('LOCATIVE', 'Rabi’ al-thani'),
                /* I18N: http://en.wikipedia.org/wiki/Jumada_al-awwal */
                5  => I18N::translateContext('LOCATIVE', 'Jumada al-awwal'),
                /* I18N: http://en.wikipedia.org/wiki/Jumada_al-thani */
                6  => I18N::translateContext('LOCATIVE', 'Jumada al-thani'),
                /* I18N: http://en.wikipedia.org/wiki/Rajab */
                7  => I18N::translateContext('LOCATIVE', 'Rajab'),
                /* I18N: http://en.wikipedia.org/wiki/Sha%27aban */
                8  => I18N::translateContext('LOCATIVE', 'Sha’aban'),
                /* I18N: http://en.wikipedia.org/wiki/Ramadan_%28calendar_month%29 */
                9  => I18N::translateContext('LOCATIVE', 'Ramadan'),
                /* I18N: http://en.wikipedia.org/wiki/Shawwal */
                10 => I18N::translateContext('LOCATIVE', 'Shawwal'),
                /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Qi%27dah */
                11 => I18N::translateContext('LOCATIVE', 'Dhu al-Qi’dah'),
                /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Hijjah */
                12 => I18N::translateContext('LOCATIVE', 'Dhu al-Hijjah'),
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
                /* I18N: http://en.wikipedia.org/wiki/Muharram */
                1  => I18N::translateContext('INSTRUMENTAL', 'Muharram'),
                /* I18N: http://en.wikipedia.org/wiki/Safar */
                2  => I18N::translateContext('INSTRUMENTAL', 'Safar'),
                /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-awwal */
                3  => I18N::translateContext('INSTRUMENTAL', 'Rabi’ al-awwal'),
                /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-thani */
                4  => I18N::translateContext('INSTRUMENTAL', 'Rabi’ al-thani'),
                /* I18N: http://en.wikipedia.org/wiki/Jumada_al-awwal */
                5  => I18N::translateContext('INSTRUMENTAL', 'Jumada al-awwal'),
                /* I18N: http://en.wikipedia.org/wiki/Jumada_al-thani */
                6  => I18N::translateContext('INSTRUMENTAL', 'Jumada al-thani'),
                /* I18N: http://en.wikipedia.org/wiki/Rajab */
                7  => I18N::translateContext('INSTRUMENTAL', 'Rajab'),
                /* I18N: http://en.wikipedia.org/wiki/Sha%27aban */
                8  => I18N::translateContext('INSTRUMENTAL', 'Sha’aban'),
                /* I18N: http://en.wikipedia.org/wiki/Ramadan_%28calendar_month%29 */
                9  => I18N::translateContext('INSTRUMENTAL', 'Ramadan'),
                /* I18N: http://en.wikipedia.org/wiki/Shawwal */
                10 => I18N::translateContext('INSTRUMENTAL', 'Shawwal'),
                /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Qi%27dah */
                11 => I18N::translateContext('INSTRUMENTAL', 'Dhu al-Qi’dah'),
                /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Hijjah */
                12 => I18N::translateContext('INSTRUMENTAL', 'Dhu al-Hijjah'),
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
}
