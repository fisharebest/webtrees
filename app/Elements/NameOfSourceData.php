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
 * NAME_OF_SOURCE_DATA := {Size=1:90}
 * The name of the electronic data source that was used to obtain the data in
 * this transmission. For example, the data may have been obtained from a
 * CD-ROM disc that was named "U.S. 1880 CENSUS CD-ROM vol. 13."
 */
class NameOfSourceData extends AbstractElement
{
    protected const int MAXIMUM_LENGTH = 90;
}
