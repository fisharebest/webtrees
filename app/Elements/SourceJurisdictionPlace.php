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
 * PLACE_NAME := {Size=1:120}
 * The name of the lowest jurisdiction that encompasses all lower-level places named in this
 * source. For example, "Oneida, Idaho" would be used as a source jurisdiction place for events
 * occurring in the various towns within Oneida County. "Idaho" would be the source jurisdiction
 * place if the events recorded took place in other counties as well as Oneida County.
 */
class SourceJurisdictionPlace extends PlaceName
{
}
