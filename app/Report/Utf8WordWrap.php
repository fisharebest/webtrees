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

use function mb_strlen;
use function mb_strrpos;
use function mb_substr;

/**
 * A UTF-8 aware variant of PHP's wordwrap() that breaks lines on whitespace.
 *
 * Unlike the built-in wordwrap(), the entire implementation works in code-points
 * rather than bytes, so multi-byte characters can never be split.  Words that
 * are longer than $width are cut at the column boundary; shorter words are
 * preserved and the line break is placed at the preceding space.
 */
final class Utf8WordWrap
{
    /**
     * Wrap $string so that no line is longer than $width code-points.
     *
     * @param string $string The text to wrap.  May contain existing newlines.
     * @param int    $width  Maximum line length in characters (must be > 0).
     */
    public static function wrap(string $string, int $width): string
    {
        if ($width < 1) {
            // Caller passed a degenerate width - return the input unchanged
            // rather than looping forever.
            return $string;
        }

        $result    = '';
        $remaining = $string;

        while (mb_strlen($remaining) > $width) {
            // Default: take exactly $width characters.
            $sub = mb_substr($remaining, 0, $width);

            // If the character immediately after the cut is a space, include
            // the word that ends at that space in the current line.
            if (mb_substr($remaining, $width, 1) === ' ') {
                $sub = mb_substr($remaining, 0, $width + 1);
            }

            $space_position = mb_strrpos($sub, ' ');

            if ($space_position === false) {
                // No space on this line - cut a long word at the column boundary.
                $result    .= $sub . "\n";
                $remaining  = mb_substr($remaining, mb_strlen($sub));
            } else {
                // Break at the last space and drop that space from the next line.
                $result    .= mb_substr($remaining, 0, $space_position) . "\n";
                $remaining  = mb_substr($remaining, $space_position + 1);
            }
        }

        return $result . $remaining;
    }
}
