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
 * RESPONSIBLE_AGENCY := {Size=1:120}
 * The organization, institution, corporation, person, or other entity that has
 * responsibility for the associated context. For example, an employer of a
 * person of an associated occupation, or a church that administered rites or
 * events, or an organization responsible for creating and/or archiving records.
 */
class ResponsibleAgency extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 120;
}
