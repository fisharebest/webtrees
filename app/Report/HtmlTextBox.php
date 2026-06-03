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

/**
 * @extends AbstractTextBox<AbstractRenderer&HtmlRendererInterface>
 */
class HtmlTextBox extends AbstractTextBox
{
    public function render(AbstractRenderer $renderer, bool $attrib = true): void
    {
        $this->collapseElements($renderer);

        $cell_padding = $this->padding ? $renderer::CELL_PADDING : 0.0;

        // Used with line breaks and cell height calculation within this box only
        $renderer->resetLargestFontHeight();

        // If current position (left)
        if ($this->left === AbstractElement::CURRENT_POSITION) {
            $cX = $renderer->getX();
        } else {
            $cX = $this->left;
            $renderer->setX($cX);
        }
        // If current position (top)
        if ($this->top === AbstractElement::CURRENT_POSITION) {
            $this->top = $renderer->getY();
        } else {
            $renderer->setY($this->top);
        }

        // Check the width if set to page wide OR set by xml to larger then page width (margin)
        if ($this->width === 0.0 || $this->width > $renderer->getRemainingWidth()) {
            $this->width = $renderer->getRemainingWidth();
        }
        // For padding, we have to use less wrap width
        $cW = $this->width - $cell_padding * 2.0;

        // Calculate element dimensions
        $dimensions = $this->calculateElementDimensions($renderer, $cW);
        $cHT        = $dimensions['line_count'];
        $eH         = $dimensions['element_height'];
        $fH         = $dimensions['footnote_height'];

        // Add up what's the final height
        $cH = $this->height;
        // If any element exist
        if (count($this->elements) > 0) {
            // Check if this is text or some other element, like images
            if ($eH === 0.0) {
                // Number of LF but at least one line
                $cHT = ($cHT + 1) * $renderer::LINE_HEIGHT_RATIO;
                // Calculate the cell height with the largest font size used
                $cHT *= $renderer->getLargestFontHeight();
                if ($cH < $cHT) {
                    $cH = $cHT;
                }
            } else {
                // This is any other element
                if ($cH < $eH) {
                    $cH = $eH;
                }
                // Add Footnote height to the rest of the height
                $cH += $fH;
            }
        }


        // Finally, check the last cells height
        if ($cH < $renderer->getLastCellHeight()) {
            $cH = $renderer->getLastCellHeight();
        }
        // Update max Y in case of a pagebreak
        // We don't want to over write any images or other stuff
        $renderer->addMaxY($this->top + $cH);

        // Start to print HTML
        echo '<div style="position:absolute;top:', $this->top, 'pt;';
        // LTR (left) or RTL (right)
        echo $renderer->config->align_rtl, ':', $cX, 'pt;';

        if ($this->bgcolor !== '') {
            echo ' background-color:', $this->bgcolor, ';';
        }

        if ($this->padding) {
            // Use Cell around padding to support RTL also
            echo 'padding:', $cell_padding, 'pt;';
        }

        if ($this->border) {
            echo ' border:solid black 1pt;';
            echo 'width:', $this->width - 1 - $cell_padding * 2, 'pt;height:', $cH - 1, 'pt;';
        } else {
            echo 'width:', $this->width - $cell_padding * 2, 'pt;height:', $cH, 'pt;';
        }
        echo '">';

        // Do a little "margin" trick before print
        // to get the correct current position => "."
        $cXT = $renderer->getX();
        $cYT = $renderer->getY();
        $renderer->setXy(0, 0);

        // Print the text elements
        foreach ($this->elements as $element) {
            if ($element instanceof AbstractText) {
                $element->render($renderer, false);
            } else {
                $element->render($renderer);
            }
        }
        echo "</div>\n";

        // Reset "margins"
        $renderer->setXy($cXT, $cYT);
        // This will be mostly used to trick the multiple images last height
        if ($this->reseth) {
            $cH = 0;
        }
        // New line and some clean-up
        if (!$this->newline) {
            $renderer->setXy($cX + $this->width, $this->top);
            $renderer->setLastCellHeight($cH);
        } else {
            $renderer->setXy(0, $this->top + $cH + $cell_padding * 2);
            $renderer->resetLastCellHeight();
        }
    }
}
