<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Elements;

/**
 * DATE := {Size=4:35}
 * <DATE_VALUE>
 * LDS ordinance dates use only the Gregorian date and most often use the form
 * of day, month, and year. Only in rare instances is there a partial date. The
 * temple tag and code should always accompany temple ordinance dates.
 * Sometimes the LDS_(ordinance)_DATE_STATUS is used to indicate that an
 * ordinance date and temple code is not required, such as when BIC is used.
 * (See LDS_(ordinance)_DATE_STATUS definitions beginning on page 51.)
 */
class DateLdsOrd extends AbstractElement
{
}
