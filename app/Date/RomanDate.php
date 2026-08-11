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

/**
 * The 5.5.1 gedcom spec mentions this calendar but gives no details of
 * how it is to be represented... This class is just a placeholder so that
 * webtrees won’t complain if it receives one.
 */
final class RomanDate extends AbstractGregorianJulianDate
{
    public function __construct($date)
    {
        parent::__construct($date, new JulianCalendar(), CalendarEscape::Roman);
    }
}
