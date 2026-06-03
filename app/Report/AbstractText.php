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

use function count;
use function explode;
use function str_contains;
use function substr_count;

/**
 * @template TRenderer of AbstractRenderer
 *
 * @extends AbstractElement<TRenderer>
 */
abstract class AbstractText extends AbstractElement
{
    // Remaining width of a cell (points)
    public float $wrapWidthRemaining;

    // Original width of a cell (points)
    public float $wrapWidthCell;

    public function __construct(
        protected Style $style,
        protected string $color,
    ) {
        $this->text               = '';
        $this->wrapWidthRemaining = 0.0;
    }

    public function setWrapWidth(float $wrapwidth, float $cellwidth): float
    {
        $this->wrapWidthCell = $cellwidth;
        if (str_contains($this->text, "\n")) {
            $this->wrapWidthRemaining = $cellwidth;
        } else {
            $this->wrapWidthRemaining = $wrapwidth;
        }

        return $this->wrapWidthRemaining;
    }

    public function getStyle(): Style
    {
        return $this->style;
    }

    /**
     * Calculate the width of this text element after word-wrapping.
     *
     * @param TRenderer $renderer
     *
     * @return array{0:float,1:int,2:float}
     */
    public function getWidth(AbstractRenderer $renderer): array
    {
        $renderer->setCurrentStyle($this->style);

        // Check for the largest font size in the box
        $fsize = $renderer->getCurrentStyleHeight();
        $renderer->trackFontHeight($fsize);

        // Get the line width for the text in points
        $lw = $renderer->getStringWidth($this->text);
        // Line Feed counter - Number of lines in the text
        $lfct = $renderer->countLines($this->text);
        // If there is still remaining wrap width...
        $wrapWidthRemaining = $this->wrapWidthRemaining;
        if ($wrapWidthRemaining > 0) {
            // Check with line counter too!
            if ($lw >= $wrapWidthRemaining || $lfct > 1) {
                $newtext = '';
                $lines   = explode("\n", $this->text);
                // Go through the text line by line
                foreach ($lines as $line) {
                    // Line width in points
                    $lw = $renderer->getStringWidth($line);
                    // If the line has to be wrapped
                    if ($lw > $wrapWidthRemaining) {
                        $words    = explode(' ', $line);
                        $addspace = count($words);
                        $lw       = 0;
                        foreach ($words as $word) {
                            $addspace--;
                            $lw += $renderer->getStringWidth($word . ' ');
                            if ($lw <= $wrapWidthRemaining) {
                                $newtext .= $word;
                                if ($addspace !== 0) {
                                    $newtext .= ' ';
                                }
                            } else {
                                $lw = $renderer->getStringWidth($word . ' ');
                                $newtext .= "\n$word";
                                if ($addspace !== 0) {
                                    $newtext .= ' ';
                                }
                                // Reset the wrap width to the cell width
                                $wrapWidthRemaining = $this->wrapWidthCell;
                            }
                        }
                    } else {
                        $newtext .= $line;
                    }
                    // Check the Line Feed counter
                    if ($lfct > 1) {
                        // Add a new line as long as it's not the last line
                        $newtext .= "\n";
                        // Reset the line width
                        $lw = 0;
                        // Reset the wrap width to the cell width
                        $wrapWidthRemaining = $this->wrapWidthCell;
                    }
                    $lfct--;
                }
                $this->text = $newtext;
                $lfct       = substr_count($this->text, "\n");

                return [
                    $lw,
                    1,
                    $lfct,
                ];
            }
        }
        $l    = 0;
        $lfct = substr_count($this->text, "\n");
        if ($lfct > 0) {
            $l = 2;
        }

        return [
            $lw,
            $l,
            $lfct,
        ];
    }
}
