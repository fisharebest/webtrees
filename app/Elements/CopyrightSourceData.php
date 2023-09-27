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
 * COPYRIGHT_SOURCE_DATA := {Size=1:90}
 * A copyright statement required by the owner of data from which this
 * information was down- loaded. For example, when a GEDCOM down-load is
 * requested from the Ancestral File, this would be the copyright statement to
 * indicate that the data came from a copyrighted source.
 */
class CopyrightSourceData extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 90;
}
