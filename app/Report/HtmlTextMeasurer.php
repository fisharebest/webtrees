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

use Fisharebest\Webtrees\Encodings\UTF8;

/**
 * Approximate text measurement for the HTML backend.
 *
 * Since the HTML output relies on the browser for final layout, we only
 * need a rough estimate for pre-computing element dimensions during the
 * layout pass.
 */
final class HtmlTextMeasurer extends AbstractTextMeasurer
{
    // Typical character widths for sans-serif fonts.
    private const array CHARACTER_WIDTHS = [
        '0'                           => 0.55,
        '1'                           => 0.55,
        '2'                           => 0.55,
        '3'                           => 0.55,
        '4'                           => 0.55,
        '5'                           => 0.55,
        '6'                           => 0.55,
        '7'                           => 0.55,
        '8'                           => 0.55,
        '9'                           => 0.55,
        'a'                           => 0.50,
        'b'                           => 0.52,
        'c'                           => 0.48,
        'd'                           => 0.52,
        'e'                           => 0.50,
        'f'                           => 0.30,
        'g'                           => 0.50,
        'h'                           => 0.52,
        'i'                           => 0.22,
        'j'                           => 0.25,
        'k'                           => 0.48,
        'l'                           => 0.22,
        'm'                           => 0.78,
        'n'                           => 0.52,
        'o'                           => 0.50,
        'p'                           => 0.52,
        'q'                           => 0.52,
        'r'                           => 0.34,
        's'                           => 0.46,
        't'                           => 0.30,
        'u'                           => 0.52,
        'v'                           => 0.48,
        'w'                           => 0.72,
        'x'                           => 0.48,
        'y'                           => 0.48,
        'z'                           => 0.46,
        'A'                           => 0.62,
        'B'                           => 0.60,
        'C'                           => 0.62,
        'D'                           => 0.64,
        'E'                           => 0.58,
        'F'                           => 0.56,
        'G'                           => 0.66,
        'H'                           => 0.64,
        'I'                           => 0.26,
        'J'                           => 0.48,
        'K'                           => 0.60,
        'L'                           => 0.52,
        'M'                           => 0.80,
        'N'                           => 0.68,
        'O'                           => 0.66,
        'P'                           => 0.58,
        'Q'                           => 0.66,
        'R'                           => 0.60,
        'S'                           => 0.60,
        'T'                           => 0.56,
        'U'                           => 0.66,
        'V'                           => 0.62,
        'W'                           => 0.92,
        'X'                           => 0.62,
        'Y'                           => 0.62,
        'Z'                           => 0.58,
        ' '                           => 0.30,
        '.'                           => 0.25,
        ','                           => 0.25,
        ':'                           => 0.25,
        ';'                           => 0.25,
        '!'                           => 0.28,
        '|'                           => 0.28,
        '('                           => 0.33,
        ')'                           => 0.33,
        '['                           => 0.33,
        ']'                           => 0.33,
        '-'                           => 0.30,
        '–'                           => 0.50,
        '—'                           => 1.00,
        '\''                          => 0.25,
        '"'                           => 0.30,
        '/'                           => 0.40,
        '\\'                          => 0.40,
        '@'                           => 0.90,
        '#'                           => 0.75,
        '%'                           => 0.75,
        '&'                           => 0.75,
        UTF8::FIRST_STRONG_ISOLATE    => 0.0,
        UTF8::POP_DIRECTIONAL_ISOLATE => 0.0,
    ];

    public function getStringWidth(string $text, Style $style): float
    {
        $chars  = mb_str_split($text);
        $widths = array_map(fn ($char) => self::CHARACTER_WIDTHS[$char] ?? 0.55, $chars);

        $font_style_multiplier = $style->style === 'b' ? 1.05 : 1.0;

        return array_sum($widths) * $style->size * $font_style_multiplier;
    }
}
