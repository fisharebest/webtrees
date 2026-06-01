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
 * Where the rendering cursor should be placed after an image has been drawn.
 *
 * The single-letter scalar values match TCPDF's Image() $ln parameter so that
 * the PDF backend can pass them through unchanged.
 */
enum ImageContinuation: string
{
    /** Continue rendering on the same line as the image. */
    case SameLine = 'T';

    /** Advance the cursor to the line below the image. */
    case NextLine = 'N';
}
