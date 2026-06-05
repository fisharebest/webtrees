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

use LogicException;

use function array_shift;
use function count;
use function explode;
use function in_array;
use function mb_strlen;
use function mb_substr;

/**
 * Word-wraps text to fit within a given width using measured font metrics.
 *
 * This is the single source of truth for text line-breaking across both the
 * HTML and PDF backends.
 */
final class TextWrapper
{
    private const array URL_BREAK_CHARACTERS = ['/', '-', '.', '?', '&', '=', '#', ':'];

    public function __construct(
        private readonly TextMeasurerInterface $measurer,
    ) {
    }

    /**
     * Split text into physical lines that fit within the given width.
     *
     * Respects explicit line breaks (\n) and wraps at word boundaries.
     *
     * When $subsequent_width is provided, the first physical line wraps at
     * $width and all following lines wrap at $subsequent_width. This supports
     * inline continuation where the first line has less available space than
     * subsequent lines (e.g., text following a footnote reference).
     *
     * @return list<string>
     */
    public function wrapText(string $text, Style $style, float $first_width, float|null $subsequent_width = null): array
    {
        if ($first_width <= 0.0) {
            throw new LogicException('Width must be greater than zero: ' . $first_width);
        }

        $next_line_width = $subsequent_width ?? $first_width;
        $logical_lines   = explode("\n", $text);
        $result          = [];
        $space_width     = $this->measurer->getStringWidth(' ', $style);
        $effective_width  = $first_width;

        foreach ($logical_lines as $line) {
            $words         = explode(' ', $line);
            $current_line  = '';
            $current_width = 0.0;

            // Preserve leading space: explode produces an empty first token
            // when the line starts with a space. Prefix the space onto the
            // first real word so it appears in the rendered output (e.g. the
            // gap after footnote references).
            if ($words !== [] && $words[0] === '') {
                array_shift($words);
                if ($words !== []) {
                    $words[0] = ' ' . $words[0];
                }
            }

            foreach ($words as $word) {
                // We need to add a space between words, but there might not be room.
                if ($current_line !== '') {
                    if ($current_width + $space_width >= $effective_width) {
                        $result[]       = $current_line;
                        $effective_width = $next_line_width;
                        $current_line   = '';
                        $current_width  = 0.0;
                    } else {
                        $current_line  .= ' ';
                        $current_width += $space_width;
                    }
                }

                $word_width = $this->measurer->getStringWidth($word, $style);

                if ($current_width + $word_width <= $effective_width) {
                    // The word fits on the current line.
                    $current_line  .= $word;
                    $current_width += $word_width;
                } elseif ($word_width <= $next_line_width) {
                    // The word fits on a subsequent line — push current and start fresh.
                    $result[]       = $current_line;
                    $effective_width = $next_line_width;
                    $current_line   = $word;
                    $current_width  = $word_width;
                } else {
                    // The word is too long even for a subsequent line. Break it.
                    // Firstly, a short part to fill the current line.
                    $fragment   = $this->breakLongWord($word, $effective_width - $current_width, $style);
                    $result[]   = $current_line . $fragment;
                    $effective_width = $next_line_width;
                    $word       = mb_substr($word, mb_strlen($fragment));
                    $word_width = $this->measurer->getStringWidth($word, $style);

                    // Secondly, chunks of the word that fill a full subsequent line.
                    while ($word_width > $effective_width) {
                        $fragment   = $this->breakLongWord($word, $effective_width, $style);
                        $result[]   = $fragment;
                        $word       = mb_substr($word, mb_strlen($fragment));
                        $word_width = $this->measurer->getStringWidth($word, $style);
                    }

                    // Thirdly, the remaining part, less than a full line.
                    $current_line  = $word;
                    $current_width = $word_width;
                }
            }

            $result[]       = $current_line;
            $effective_width = $next_line_width;
        }

        return $result;
    }

    /**
     * Return the portion of a long word that fits within the given width.
     */
    private function breakLongWord(string $word, float $width, Style $style): string
    {
        if ($this->measurer->getStringWidth($word, $style) <= $width) {
            return $word;
        }

        $length     = mb_strlen($word);
        $fit_count  = 0;
        $best_break = 0;

        // Walk character by character to find how many fit within the width
        for ($index = 1; $index <= $length; $index++) {
            if ($this->measurer->getStringWidth(mb_substr($word, 0, $index), $style) > $width) {
                break;
            }

            $fit_count = $index;

            if (in_array(mb_substr($word, $index - 1, 1), self::URL_BREAK_CHARACTERS, true)) {
                $best_break = $index;
            }
        }

        // Prefer breaking at URL punctuation; otherwise break at the last fitting character.
        // max(1, ...) ensures forward progress even when nothing fits.
        $break_at = $best_break > 0 ? $best_break : max(1, $fit_count);

        return mb_substr($word, 0, $break_at);
    }


    /**
     * Count the number of physical lines text will occupy at the given width.
     */
    public function countLines(string $text, float $width, Style $style): int
    {
        return count($this->wrapText($text, $style, $width));
    }

    /**
     * Calculate the total height of wrapped text.
     *
     * @param float $line_height_ratio Multiplier applied to font size for line spacing
     */
    public function textHeight(string $text, float $width, Style $style, float $line_height_ratio = 1.25): float
    {
        $line_count = $this->countLines($text, $width, $style);

        return $line_count * $style->size * $line_height_ratio;
    }

    /**
     * Return the width of the last physical line after wrapping.
     */
    public function lastLineWidth(string $text, float $width, Style $style): float
    {
        $lines     = $this->wrapText($text, $style, $width);
        $last_line = $lines[count($lines) - 1];

        return $this->measurer->getStringWidth($last_line, $style);
    }
}
