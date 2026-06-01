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

namespace Fisharebest\Webtrees\Report;

/**
 * Which logical section of a report is currently being assembled.
 *
 * Reports are composed of a header (rendered at the top of every page), a body
 * (the main flowing content), and a footer (rendered at the bottom of every
 * page).  The XML report parser switches between the three as it processes
 * the matching <Header>, <Body> and <Footer> elements.
 *
 * The single-letter scalar values are kept identical to the historical
 * "H"/"B"/"F" string sentinels so that any third-party renderer that
 * subclasses {@see AbstractRenderer} continues to work after serialisation
 * to and from the backed value.
 */
enum ReportSection: string
{
    case Header = 'H';
    case Body   = 'B';
    case Footer = 'F';
}
