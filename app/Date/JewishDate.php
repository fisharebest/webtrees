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
use Fisharebest\Webtrees\Enums\CalendarEscape;

final class JewishDate extends AbstractCalendarDate
{
    // Convert GEDCOM month names to month numbers
    public const array MONTH_TO_NUMBER = [
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

    public const array NUMBER_TO_MONTH = [
        0  => '',
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

    public function __construct($date)
    {
        parent::__construct($date, new JewishCalendar(), CalendarEscape::Jewish);
    }

    /**
     * Which month follows this one? Calendars with leap-months should provide their own implementation.
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
