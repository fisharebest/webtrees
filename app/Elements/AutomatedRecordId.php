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
 * AUTOMATED_RECORD_ID := {Size=1:12}
 * A unique record identification number assigned to the record by the source
 * system. This number is intended to serve as a more sure means of
 * identification of a record for reconciling differences in data between two
 * interfacing systems.
 */
class AutomatedRecordId extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 12;
}
