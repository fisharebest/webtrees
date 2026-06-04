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
abstract class AbstractFootnote extends AbstractElement
{
    // The style for this element
    protected Style $style;

    // Numbers for the links
    public int $num = 0;

    // The text that will be printed with the number
    protected string $numText = '';

    // Remaining width of a cell
    protected float $wrapWidthRemaining;

    // Original width of a cell (points)
    protected float $wrapWidthCell;

    // A link
    protected string $addlink;

    public function __construct(Style $style)
    {
        $this->style = $style;
    }

    public function setWrapWidth(float $wrapwidth, float $cellwidth): float
    {
        $this->wrapWidthCell = $cellwidth;
        if (str_contains($this->numText, "\n")) {
            $this->wrapWidthRemaining = $cellwidth;
        } else {
            $this->wrapWidthRemaining = $wrapwidth;
        }

        return $this->wrapWidthRemaining;
    }

    public function setNumAndLink(int $num, string $link): void
    {
        $this->num     = $num;
        $this->numText = (string) $num;
        $this->addlink = $link;
    }

    /**
     * Set the current style and return the footnote text with title markup applied.
     *
     * @param TRenderer $renderer
     */
    protected function resolvedFootnoteText(AbstractRenderer $renderer): string
    {
        $renderer->setCurrentStyle($this->style);

        return $this->resolvedText($renderer);
    }

    /**
     * @param TRenderer $renderer
     */
    abstract public function getFootnoteHeight(AbstractRenderer $renderer, float $cellWidth = 0): float;

    /**
     * Calculate the width of the footnote number after word-wrapping.
     *
     * @param TRenderer $renderer
     *
     * @return array{0:float,1:int,2:float}
     */
    public function getWidth(AbstractRenderer $renderer): array
    {
        // Setup the style name, a font must be selected to calculate the width
        $renderer->setCurrentStyle($renderer->getStyle('footnotenum'));

        // Check for the largest font size in the box
        $fsize = $renderer->getCurrentStyleHeight();
        $renderer->trackFontHeight($fsize);

        // Returns the Object if already numbered else false
        if ($this->num === 0) {
            $renderer->checkFootnote($this);
        }

        // Get the line width for the text in points
        $lw = $renderer->getStringWidth($this->numText);
        // Line Feed counter - Number of lines in the text
        $lfct = $renderer->countLines($this->numText);
        // If there is still remaining wrap width...
        $wrapWidthRemaining = $this->wrapWidthRemaining;
        if ($wrapWidthRemaining > 0) {
            // Check with line counter too!
            if ($lw >= $wrapWidthRemaining || $lfct > 1) {
                $newtext = '';
                $lines   = explode("\n", $this->numText);
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
                $this->numText = $newtext;
                $lfct          = substr_count($this->numText, "\n");

                return [
                    $lw,
                    1,
                    $lfct,
                ];
            }
        }
        $l    = 0;
        $lfct = substr_count($this->numText, "\n");
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
