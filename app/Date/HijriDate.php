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
use Fisharebest\Webtrees\Enums\CalendarEscape;

final class HijriDate extends AbstractCalendarDate
{
    // Convert GEDCOM month names to month numbers
    public const array MONTH_TO_NUMBER = [
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

    public const array NUMBER_TO_MONTH = [
        0  => '',
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

    public function __construct($date)
    {
        parent::__construct($date, new ArabicCalendar(), CalendarEscape::Hijri);
    }
}
