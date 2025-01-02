<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use function str_replace;
use function substr_count;

class ReportHtmlFootnote extends ReportBaseFootnote
{
    /**
     * HTML Footnotes number renderer
     *
     * @param HtmlRenderer $renderer
     *
     * @return void
     */
    public function render($renderer): void
    {
        $renderer->setCurrentStyle('footnotenum');
        echo '<a href="#footnote', $this->num, '"><sup>';
        $renderer->write($renderer->entityRTL . $this->num);
        echo "</sup></a>\n";
    }

    /**
     * Write the Footnote text
     * Uses style name "footnote" by default
     *
     * @param HtmlRenderer $renderer
     *
     * @return void
     */
    public function renderFootnote($renderer): void
    {
        if ($renderer->getCurrentStyle() !== $this->styleName) {
            $renderer->setCurrentStyle($this->styleName);
        }

        $temptext = str_replace('#PAGENUM#', (string) $renderer->pageNo(), $this->text);
        // underline «title» part of Source item
        $temptext = str_replace([
            '«',
            '»',
        ], [
            '<u>',
            '</u>',
        ], $temptext);
        echo '<div><a id="footnote', $this->num, '"></a>';
        $renderer->write($this->num . '. ' . $temptext);
        echo '</div>';

        $renderer->setXy(0, $renderer->getY() + $this->getFootnoteHeight($renderer));
    }

    /**
     * Calculates the Footnotes height
     *
     * @param HtmlRenderer $renderer
     * @param float        $cellWidth The width of the cell to use it for text wrapping
     *
     * @return float     Footnote height in points
     */
    public function getFootnoteHeight(HtmlRenderer $renderer, float $cellWidth = 0): float
    {
        if ($renderer->getCurrentStyle() !== $this->styleName) {
            $renderer->setCurrentStyle($this->styleName);
        }

        if ($cellWidth > 0) {
            $this->text = $renderer->textWrap($this->text, $cellWidth);
        }

        $this->text .= "\n\n";
        $ct         = substr_count($this->text, "\n");
        $fsize      = $renderer->getCurrentStyleHeight();

        return $fsize * $ct * $renderer->cellHeightRatio;
    }

    /**
     * Get the width of text
     * Breaks up a text into lines if needed
     *
     * @param HtmlRenderer $renderer
     *
     * @return array{0:float,1:int,2:float}
     */
    public function getWidth($renderer): array
    {
        // Setup the style name
        $renderer->setCurrentStyle('footnotenum');

        // Check for the largest font size in the box
        $fsize = $renderer->getCurrentStyleHeight();
        if ($fsize > $renderer->largestFontHeight) {
            $renderer->largestFontHeight = $fsize;
        }

        // Returns the Object if already numbered else false
        if (empty($this->num)) {
            $renderer->checkFootnote($this);
        }

        // Get the line width for the text in points + a little margin
        $lw = $renderer->getStringWidth($this->numText);
        // Line Feed counter - Number of lines in the text
        $lfct = $renderer->countLines($this->numText);
        // If there is still remaining wrap width...
        if ($this->wrapWidthRemaining > 0) {
            // Check with line counter too!
            if ($lw >= $this->wrapWidthRemaining || $lfct > 1) {
                $newtext            = '';
                $wrapWidthRemaining = $this->wrapWidthRemaining;
                $lines              = explode("\n", $this->numText);
                // Go through the text line by line
                foreach ($lines as $line) {
                    // Line width in points + a little margin
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
                        // Add a new line feed as long as it’s not the last line
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
