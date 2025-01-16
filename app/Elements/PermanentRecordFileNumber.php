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
 * PERMANENT_RECORD_FILE_NUMBER := {Size=1:90}
 * <REGISTERED_RESOURCE_IDENTIFIER>:<RECORD_IDENTIFIER>
 * The record number that uniquely identifies this record within a registered
 * network resource. The number will be usable as a cross-reference pointer.
 * The use of the colon (:) is reserved to indicate the separation of the
 * "registered resource identifier" (which precedes the colon) and the unique
 * "record identifier" within that resource (which follows the colon). If the
 * colon is used, implementations that check pointers should not expect to find
 * a matching cross-reference identifier in the transmission but would find it
 * in the indicated database within a network. Making resource files available
 * to a public network is a future implementation.
 */
class PermanentRecordFileNumber extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 90;
}
