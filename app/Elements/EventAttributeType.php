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

namespace Fisharebest\Webtrees\Elements;

/**
 * EVENT_ATTRIBUTE_TYPE := {Size=1:15}
 * [ <EVENT_TYPE_INDIVIDUAL> | <EVENT_TYPE_FAMILY> | <ATTRIBUTE_TYPE> ]
 * A code that classifies the principal event or happening that caused the
 * source record entry to be created. If the event or attribute doesn't
 * translate to one of these tag codes, then a user supplied value is
 * expected and will be generally classified in the category of other.
 */
class EventAttributeType extends AbstractElement
{
    protected const int MAXIMUM_LENGTH = 15;
}
