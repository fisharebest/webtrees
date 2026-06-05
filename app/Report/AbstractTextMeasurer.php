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

use function mb_str_split;
use function mb_substr;
use function str_repeat;

/**
 * Base class for text measurers, providing shared truncation logic.
 */
abstract class AbstractTextMeasurer implements TextMeasurerInterface
{
    public function truncate(
        string $text,
        float $width,
        Style $style,
        string $ellipsis = UTF8::HORIZONTAL_ELLIPSIS,
    ): string {
        $lines = explode("\n", $text);

        foreach ($lines as $n => $line) {
            $lines[$n] = $this->truncateLine($line, $width, $style, $ellipsis);
        }

        return implode("\n", $lines);
    }

    private function truncateLine(
        string $text,
        float $width,
        Style $style,
        string $ellipsis = UTF8::HORIZONTAL_ELLIPSIS,
    ): string {
        if ($this->getStringWidth($text, $style) <= $width) {
            return $text;
        }

        $width -= $this->getStringWidth($ellipsis, $style);

        for ($length = mb_strlen($text); $length > 0; $length--) {
            $substring = mb_substr($text, 0, $length);

            if ($this->getStringWidth($substring, $style) < $width) {
                $count_fsi = mb_substr_count($substring, UTF8::FIRST_STRONG_ISOLATE);
                $count_pdi = mb_substr_count($substring, UTF8::POP_DIRECTIONAL_ISOLATE);
                $substring .= str_repeat(UTF8::POP_DIRECTIONAL_ISOLATE, $count_fsi - $count_pdi);
                return $substring . $ellipsis;
            }
        }

        // Nothing fits?
        return $ellipsis;
    }

    /**
     * Count unclosed FSI (U+2068) isolates in a string and return the
     * necessary PDI (U+2069) closing characters.
     */
    private function bidiClosers(string $text): string
    {
        $depth = 0;

        foreach (mb_str_split($text) as $character) {
            if ($character === UTF8::FIRST_STRONG_ISOLATE) {
                $depth++;
            } elseif ($character === UTF8::POP_DIRECTIONAL_ISOLATE) {
                $depth = max(0, $depth - 1);
            }
        }

        return str_repeat(UTF8::POP_DIRECTIONAL_ISOLATE, $depth);
    }
}
