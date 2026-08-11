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
use Fisharebest\Webtrees\Enums\CalendarEscape;

final class FrenchDate extends AbstractCalendarDate
{
    // Convert GEDCOM month names to month numbers
    public const array MONTH_TO_NUMBER = [
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

    public const array NUMBER_TO_MONTH = [
        0  => '',
        1  => 'VEND',
        2  => 'BRUM',
        3  => 'FRIM',
        4  => 'NIVO',
        5  => 'PLUV',
        6  => 'VENT',
        7  => 'GERM',
        8  => 'FLOR',
        9  => 'PRAI',
        10 => 'MESS',
        11 => 'THER',
        12 => 'FRUC',
        13 => 'COMP',
    ];

    public function __construct($date)
    {
        parent::__construct($date, new FrenchCalendar(), CalendarEscape::French);
    }
}
