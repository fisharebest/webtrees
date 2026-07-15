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

use Fisharebest\ExtCalendar\JulianCalendar;
use Fisharebest\Webtrees\Enums\CalendarEscape;

final class JulianDate extends AbstractGregorianJulianDate
{
    public function __construct($date)
    {
        parent::__construct($date, new JulianCalendar(), CalendarEscape::Julian);
    }

    /**
     * Most years are 1 more than the previous, but not always (e.g. 1BC->1AD)
     */
    protected function nextYear(int $year): int
    {
        if ($year === -1) {
            return 1;
        }

        return $year + 1;
    }

    /**
     * Process new-style/old-style years and years BC
     */
    protected function extractYear(string $year): int
    {
        if (preg_match('/^(\d\d\d\d)\/\d{1,4}$/', $year, $match)) {
            // Assume the first year is correct
            $this->new_old_style = true;

            return (int) $match[1] + 1;
        }

        if (preg_match('/^(\d+) B\.C\.$/', $year, $match)) {
            return -(int) $match[1];
        }

        return (int) $year;
    }
}
