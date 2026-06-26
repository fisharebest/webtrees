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
 * Measures text width for a given font style.
 *
 * The layout engine uses this to compute word-wrapping and element widths
 * without coupling to a specific rendering backend. The PDF implementation
 * uses real font metrics; the HTML implementation uses an approximation.
 */
interface TextMeasurerInterface
{
    /**
     * Return the width (in points) of the given text string when rendered
     * in the specified style.
     */
    public function getStringWidth(string $text, Style $style): float;

    /**
     * Truncate a string so that it fits within the given width (in points),
     * appending an ellipsis if the string was shortened.
     */
    public function truncate(string $text, float $width, Style $style): string;
}
