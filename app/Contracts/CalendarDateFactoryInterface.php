<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Contracts;

use Fisharebest\Webtrees\Date\AbstractCalendarDate;

/**
 * Create a calendar date object.
 */
interface CalendarDateFactoryInterface
{
    /**
     * Parse a string containing a calendar date.
     *
     * @param string $date
     *
     * @return AbstractCalendarDate
     */
    public function make(string $date): AbstractCalendarDate;

    /**
     * A list of supported calendars and their names.
     *
     * @return array<string,string>
     */
    public function supportedCalendars(): array;
}
