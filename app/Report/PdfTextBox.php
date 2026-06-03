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
use function is_array;

/**
 * @extends AbstractTextBox<AbstractRenderer&PdfRendererInterface>
 */
class PdfTextBox extends AbstractTextBox
{
    public function render(AbstractRenderer $renderer, bool $attrib = true): void
    {
        $this->collapseElements($renderer);

        // Used with line breaks and cell height calculation within this box
        $renderer->resetLargestFontHeight();

        // If current position (left)
        if ($this->left === AbstractElement::CURRENT_POSITION) {
            $cX = $renderer->getX();
        } else {
            // For static position add margin (returns and updates X)
            $cX = $renderer->addMarginX($this->left);
        }

        // If current position (top)
        if ($this->top === AbstractElement::CURRENT_POSITION) {
            $cY = $renderer->getY();
        } else {
            $cY = $this->top;
            $renderer->setY($cY);
        }

        // Check the width if set to page wide OR set by xml to larger then page width (margin)
        if ($this->width === 0.0 || $this->width > $renderer->getRemainingWidthPDF()) {
            $cW = $renderer->getRemainingWidthPDF();
        } else {
            $cW = $this->width;
        }

        // Save the original margins
        $cM = $renderer->getMargins();
        // Use cell padding to wrap the width
        // Temp Width with cell padding
        if (is_array($cM['cell'])) {
            $cWT = $cW - ($cM['padding_left'] + $cM['padding_right']);
        } else {
            $cWT = $cW - $cM['cell'] * 2;
        }
        // Calculate element dimensions
        $dimensions = $this->calculateElementDimensions($renderer, $cWT);
        $cHT        = $dimensions['line_count'];
        $eH         = $dimensions['element_height'];

        // Add up what's the final height
        $cH = $this->height;
        // If any element exist
        if (count($this->elements) > 0) {
            // Check if this is text or some other element, like images
            if ($eH === 0.0) {
                // This is text elements. Number of LF but at least one line
                $cHT = ($cHT + 1) * $renderer->getCellHeightRatio();
                // Calculate the cell height with the largest font size used within this Box
                $cHT *= $renderer->getLargestFontHeight();
                // Add cell padding
                if ($this->padding) {
                    if (is_array($cM['cell'])) {
                        $cHT += $cM['padding_bottom'] + $cM['padding_top'];
                    } else {
                        $cHT += $cM['cell'] * 2;
                    }
                }
                if ($cH < $cHT) {
                    $cH = $cHT;
                }
            } elseif ($cH < $eH) {
                // This is any other element
                $cH = $eH;
            }
        }
        // Finally, check the last cells height
        if ($cH < $renderer->getLastCellHeight()) {
            $cH = $renderer->getLastCellHeight();
        }
        // Add a new page if needed
        if ($this->pagecheck) {
            // Reset last cell height, or Header/Footer will inherit it, in case of page break
            $renderer->resetLastCellHeight();
            if ($renderer->checkPageBreakPDF($cH)) {
                $cY = $renderer->getY();
            }
        }

        // Set up the border and background color
        $cS = ''; // Class Style

        if ($this->border) {
            $cS = 'D';
        } // D or empty string: Draw (default)

        if ($this->bgcolor !== '') {
            $hex = new HexColor($this->bgcolor);
            $renderer->setFillColor($hex->red, $hex->green, $hex->blue);
            $cS .= 'F';
        }
        // Draw the border
        if (!empty($cS)) {
            if (!$renderer->isRTL()) {
                $cXM = $cX;
            } else {
                $cXM = $renderer->getPageWidth() - $cX - $cW;
            }
            $renderer->drawRect($cXM, $cY, $cW, $cH, $cS);
        }
        // Add cell padding if set and if any text (element) exist
        if ($this->padding) {
            if ($cHT > 0) {
                if (is_array($cM['cell'])) {
                    $renderer->setY($cY + $cM['padding_top']);
                } else {
                    $renderer->setY($cY + $cM['cell']);
                }
            }
        }
        // Change the margins X, Width
        if (!$renderer->isRTL()) {
            if ($this->padding) {
                if (is_array($cM['cell'])) {
                    $renderer->setLeftMargin($cX + $cM['padding_left']);
                } else {
                    $renderer->setLeftMargin($cX + $cM['cell']);
                }
            } else {
                $renderer->setLeftMargin($cX);
            }
            $renderer->setRightMargin($renderer->getRemainingWidthPDF() - $cW + $cM['right']);
        } elseif ($this->padding) {
            if (is_array($cM['cell'])) {
                $renderer->setRightMargin($cX + $cM['padding_right']);
            } else {
                $renderer->setRightMargin($cX + $cM['cell']);
            }
            $renderer->setLeftMargin($renderer->getRemainingWidthPDF() - $cW + $cM['left']);
        } else {
            $renderer->setRightMargin($cX);
            $renderer->setLeftMargin($renderer->getRemainingWidthPDF() - $cW + $cM['left']);
        }
        // Save the current page number
        $cPN = $renderer->getPageIndex();

        // Render the elements (write text, print picture...)
        foreach ($this->elements as $element) {
            $element->render($renderer);
        }
        // Restore the margins
        $renderer->setLeftMargin($cM['left']);
        $renderer->setRightMargin($cM['right']);

        // This will be mostly used to trick the multiple images last height
        if ($this->reseth) {
            $cH = 0;
            // This can only happen with multiple images and with pagebreak
            if ($cPN !== $renderer->getPageIndex()) {
                $renderer->setPageIndex($cPN);
            }
        }
        // New line and some clean up
        if (!$this->newline) {
            $renderer->setXY($cX + $cW, $cY);
            $renderer->setLastCellHeight($cH);
        } else {
            // addMarginX() also updates X
            $renderer->addMarginX(0);
            $renderer->setY($cY + $cH);
            $renderer->resetLastCellHeight();
        }
    }
}
