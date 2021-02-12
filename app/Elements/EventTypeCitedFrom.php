<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
 * EVENT_OR_FACT_CLASSIFICATION := {Size=1:15}
 * [ <EVENT_ATTRIBUTE_TYPE> ]
 * A code that indicates the type of event which was responsible for the source
 * entry being recorded. For example, if the entry was created to record a
 * birth of a child, then the type would be BIRT regardless of the assertions
 * made from that record, such as the mother's name or mother's birth date.
 * This will allow a prioritized best view choice and a determination of the
 * certainty associated with the source used in asserting the cited fact.
 */
class EventTypeCitedFrom extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 15;
}
