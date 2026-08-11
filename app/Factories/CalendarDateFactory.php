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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Contracts\CalendarDateFactoryInterface;
use Fisharebest\Webtrees\Date\AbstractCalendarDate;
use Fisharebest\Webtrees\Date\FrenchDate;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\HijriDate;
use Fisharebest\Webtrees\Date\JalaliDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Date\JulianDate;
use Fisharebest\Webtrees\Date\RomanDate;
use Fisharebest\Webtrees\Enums\CalendarEscape;
use Fisharebest\Webtrees\I18N;

/**
 * Create a calendar date object.
 */
class CalendarDateFactory implements CalendarDateFactoryInterface
{
    /**
     * Parse a string containing a calendar date.
     */
    public function make(string $date): AbstractCalendarDate
    {
        // Valid calendar escape specified? - use it
        if (preg_match('/^(@#D(?:GREGORIAN|JULIAN|HEBREW|HIJRI|JALALI|FRENCH R|ROMAN)+@) ?(.*)/', $date, $match)) {
            $cal  = CalendarEscape::from($match[1]);
            $date = $match[2];
        } else {
            $cal = null;
        }
        // A date with a month: DM, M, MY or DMY
        if (preg_match('/^(\d?\d?) ?(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC|TSH|CSH|KSL|TVT|SHV|ADR|ADS|NSN|IYR|SVN|TMZ|AAV|ELL|VEND|BRUM|FRIM|NIVO|PLUV|VENT|GERM|FLOR|PRAI|MESS|THER|FRUC|COMP|MUHAR|SAFAR|RABI[AT]|JUMA[AT]|RAJAB|SHAAB|RAMAD|SHAWW|DHUAQ|DHUAH|FARVA|ORDIB|KHORD|TIR|MORDA|SHAHR|MEHR|ABAN|AZAR|DEY|BAHMA|ESFAN) ?((?:\d{1,4}(?: B\.C\.)?|\d\d\d\d\/\d\d)?)$/', $date, $match)) {
            $d = $match[1];
            $m = $match[2];
            $y = $match[3];
        } elseif (preg_match('/^(\d{1,4}(?: B\.C\.)?|\d\d\d\d\/\d\d)$/', $date, $match)) {
            // A date with just a year
            $d = '';
            $m = '';
            $y = $match[1];
        } else {
            // An invalid date - do the best we can.
            $d = '';
            $m = '';
            $y = '';
            // Look for a 3/4 digit year anywhere in the date
            if (preg_match('/(\d{3,4})/', $date, $match)) {
                $y = $match[1];
            }
            // Look for a month anywhere in the date
            if (preg_match('/(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC|TSH|CSH|KSL|TVT|SHV|ADR|ADS|NSN|IYR|SVN|TMZ|AAV|ELL|VEND|BRUM|FRIM|NIVO|PLUV|VENT|GERM|FLOR|PRAI|MESS|THER|FRUC|COMP|MUHAR|SAFAR|RABI[AT]|JUMA[AT]|RAJAB|SHAAB|RAMAD|SHAWW|DHUAQ|DHUAH|FARVA|ORDIB|KHORD|TIR|MORDA|SHAHR|MEHR|ABAN|AZAR|DEY|BAHMA|ESFAN)/', $date, $match)) {
                $m = $match[1];
                // Look for a day number anywhere in the date
                if (preg_match('/\b(\d\d?)\b/', $date, $match)) {
                    $d = $match[1];
                }
            }
        }

        // Unambiguous dates - override calendar escape
        if (preg_match('/^(TSH|CSH|KSL|TVT|SHV|ADR|ADS|NSN|IYR|SVN|TMZ|AAV|ELL)$/', $m)) {
            $cal = CalendarEscape::Jewish;
        } elseif (preg_match('/^(VEND|BRUM|FRIM|NIVO|PLUV|VENT|GERM|FLOR|PRAI|MESS|THER|FRUC|COMP)$/', $m)) {
            $cal = CalendarEscape::French;
        } elseif (preg_match('/^(MUHAR|SAFAR|RABI[AT]|JUMA[AT]|RAJAB|SHAAB|RAMAD|SHAWW|DHUAQ|DHUAH)$/', $m)) {
            $cal = CalendarEscape::Hijri; // This is a WT extension
        } elseif (preg_match('/^(FARVA|ORDIB|KHORD|TIR|MORDA|SHAHR|MEHR|ABAN|AZAR|DEY|BAHMA|ESFAN)$/', $m)) {
            $cal = CalendarEscape::Jalali; // This is a WT extension
        } elseif (preg_match('/^\d{1,4}( B\.C\.)|\d\d\d\d\/\d\d$/', $y)) {
            $cal = CalendarEscape::Julian;
        }

        // Ambiguous dates - don't override calendar escape
        if ($cal === null) {
            if (preg_match('/^(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)$/', $m)) {
                $cal = CalendarEscape::Gregorian;
            } elseif (preg_match('/^[345]\d\d\d$/', $y)) {
                // Year 3000-5999
                $cal = CalendarEscape::Jewish;
            } else {
                $cal = CalendarEscape::Gregorian;
            }
        }

        // Now construct an object of the correct type
        return match ($cal) {
            CalendarEscape::French    => new FrenchDate([$y, $m, $d]),
            CalendarEscape::Gregorian => new GregorianDate([$y, $m, $d]),
            CalendarEscape::Hijri     => new HijriDate([$y, $m, $d]),
            CalendarEscape::Jalali    => new JalaliDate([$y, $m, $d]),
            CalendarEscape::Jewish    => new JewishDate([$y, $m, $d]),
            CalendarEscape::Julian    => new JulianDate([$y, $m, $d]),
            CalendarEscape::Roman     => new RomanDate([$y, $m, $d]),
        };
    }

    /**
     * A list of supported calendars and their names.
     *
     * @return array<string,string>
     */
    public function supportedCalendars(): array
    {
        return [
            /* I18N: The gregorian calendar */
            'gregorian' => I18N::translate('Gregorian'),
            /* I18N: The julian calendar */
            'julian'    => I18N::translate('Julian'),
            /* I18N: The French calendar */
            'french'    => I18N::translate('French'),
            /* I18N: The Hebrew/Jewish calendar */
            'jewish'    => I18N::translate('Jewish'),
            /* I18N: The Arabic/Hijri calendar */
            'hijri'     => I18N::translate('Hijri'),
            /* I18N: The Persian/Jalali calendar */
            'jalali'    => I18N::translate('Jalali'),
        ];
    }
}
