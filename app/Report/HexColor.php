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

use DomainException;

use function hexdec;
use function preg_match;
use function sprintf;

/**
 * An RGB color parsed from an HTML "#RRGGBB" string.
 *
 * Callers that distinguish "no color set" from "malformed color"
 * should check for an empty string before instantiating, since the
 * constructor treats every non-"#RRGGBB" input — including the empty
 * string — as an error.
 */
final readonly class HexColor
{
    public int $red;

    public int $green;

    public int $blue;

    /**
     * Parse a "#RRGGBB" string.  Throws DomainException for any input
     * that is not a literal "#" followed by exactly six hexadecimal
     * digits, so malformed color values surface as a hard error
     * instead of being silently reinterpreted as black.
     */
    public function __construct(string $color)
    {
        if (preg_match('/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $color, $match) !== 1) {
            throw new DomainException('Invalid HTML color code: "' . $color . '"');
        }

        $this->red   = (int) hexdec($match[1]);
        $this->green = (int) hexdec($match[2]);
        $this->blue  = (int) hexdec($match[3]);
    }

    /**
     * Return the color as a normalized "#RRGGBB" uppercase hex string.
     */
    public function hex(): string
    {
        return sprintf('#%02X%02X%02X', $this->red, $this->green, $this->blue);
    }
}
