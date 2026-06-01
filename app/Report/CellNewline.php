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
 * Where the cursor should move to after a cell has been rendered.
 *
 * The integer scalar values match TCPDF's MultiCell() $ln parameter, so the
 * PDF backend can pass them through unchanged.
 */
enum CellNewline: int
{
    /** Continue rendering to the right of the cell that was just emitted. */
    case Right = 0;

    /** Move to the start of the next line, against the page margin. */
    case NextLine = 1;

    /** Move to the next line, but start at the X position of the cell. */
    case Below = 2;
}
