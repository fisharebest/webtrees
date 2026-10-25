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

use Fisharebest\ExtCalendar\PersianCalendar;
use Fisharebest\Webtrees\Enums\CalendarEscape;

final class JalaliDate extends AbstractCalendarDate
{
    // Convert GEDCOM month names to month numbers
    public const array MONTH_TO_NUMBER = [
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

    public const array NUMBER_TO_MONTH = [
        0  => '',
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

    public function __construct($date)
    {
        parent::__construct($date, new PersianCalendar(), CalendarEscape::Jalali);
    }
}
