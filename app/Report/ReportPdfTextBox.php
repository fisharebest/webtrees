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
use function hexdec;
use function is_array;
use function is_object;
use function ksort;
use function preg_match;
use function str_replace;
use function trim;

/**
 * Class ReportPdfTextBox
 */
class ReportPdfTextBox extends ReportBaseTextbox
{
    /**
     * PDF Text Box renderer
     *
     * @param PdfRenderer $renderer
     *
     * @return void
     */
    public function render($renderer): void
    {
        $newelements      = [];
        $lastelement      = '';
        $footnote_element = [];
        // Element counter
        $cE = count($this->elements);
        //-- collapse duplicate elements
        for ($i = 0; $i < $cE; $i++) {
            $element = $this->elements[$i];
            if ($element instanceof ReportBaseElement) {
                if ($element instanceof ReportBaseText) {
                    ksort($footnote_element);
                    foreach ($footnote_element as $links) {
                        $newelements[] = $links;
                    }
                    $footnote_element = [];
                    if (empty($lastelement)) {
                        $lastelement = $element;
                    } elseif ($element->getStyleName() === $lastelement->getStyleName()) {
                        // Checking if the Text has the same style
                        $lastelement->addText(str_replace("\n", '<br>', $element->getValue()));
                    } else {
                        $newelements[] = $lastelement;
                        $lastelement   = $element;
                    }
                } elseif ($element instanceof ReportPdfFootnote) {
                    // Check if the Footnote has been set with it’s link number
                    $renderer->checkFootnote($element);
                    // Save first the last element if any
                    if (!empty($lastelement)) {
                        $newelements[] = $lastelement;
                        $lastelement   = [];
                    }
                    // Save the Footnote with it’s link number as key for sorting later
                    $footnote_element[$element->num] = $element;
                } elseif (!$element instanceof ReportPdfFootnote || trim($element->getValue()) !== '') {
                    // Do not keep empty footnotes
                    if (!empty($footnote_element)) {
                        ksort($footnote_element);
                        foreach ($footnote_element as $links) {
                            $newelements[] = $links;
                        }
                        $footnote_element = [];
                    }
                    if (!empty($lastelement)) {
                        $newelements[] = $lastelement;
                        $lastelement   = [];
                    }
                    $newelements[] = $element;
                }
            } else {
                if (!empty($lastelement)) {
                    $newelements[] = $lastelement;
                    $lastelement   = [];
                }
                if (!empty($footnote_element)) {
                    ksort($footnote_element);
                    foreach ($footnote_element as $links) {
                        $newelements[] = $links;
                    }
                    $footnote_element = [];
                }
                $newelements[] = $element;
            }
        }
        if (!empty($lastelement)) {
            $newelements[] = $lastelement;
        }
        if (!empty($footnote_element)) {
            ksort($footnote_element);
            foreach ($footnote_element as $links) {
                $newelements[] = $links;
            }
        }
        $this->elements = $newelements;
        unset($footnote_element, $lastelement, $links, $newelements);

        // Used with line breaks and cell height calculation within this box
        $renderer->largestFontHeight = 0;

        // If current position (left)
        if ($this->left === ReportBaseElement::CURRENT_POSITION) {
            $cX = $renderer->tcpdf->GetX();
        } else {
            // For static position add margin (returns and updates X)
            $cX = $renderer->addMarginX($this->left);
        }

        // If current position (top)
        if ($this->top === ReportBaseElement::CURRENT_POSITION) {
            $cY = $renderer->tcpdf->GetY();
        } else {
            $cY = $this->top;
            $renderer->tcpdf->setY($cY);
        }

        // Check the width if set to page wide OR set by xml to larger then page width (margin)
        if ($this->width === 0.0 || $this->width > $renderer->getRemainingWidthPDF()) {
            $cW = $renderer->getRemainingWidthPDF();
        } else {
            $cW = $this->width;
        }

        // Save the original margins
        $cM = $renderer->tcpdf->getMargins();
        // Use cell padding to wrap the width
        // Temp Width with cell padding
        if (is_array($cM['cell'])) {
            $cWT = $cW - ($cM['padding_left'] + $cM['padding_right']);
        } else {
            $cWT = $cW - $cM['cell'] * 2;
        }
        // Element height (except text)
        $eH = 0.0;
        $w  = 0;
        // Temp Height
        $cHT = 0;
        //-- $lw is an array
        // 0 => last line width
        // 1 => 1 if text was wrapped, 0 if text did not wrap
        // 2 => number of LF
        $lw = [];
        // Element counter
        $cE = count($this->elements);
        //-- calculate the text box height + width
        for ($i = 0; $i < $cE; $i++) {
            if (is_object($this->elements[$i])) {
                $ew = $this->elements[$i]->setWrapWidth($cWT - $w, $cWT);
                if ($ew === $cWT) {
                    $w = 0;
                }
                $lw = $this->elements[$i]->getWidth($renderer);
                // Text is already gets the # LF
                $cHT += $lw[2];
                if ($lw[1] === 1) {
                    $w = $lw[0];
                } elseif ($lw[1] === 2) {
                    $w = 0;
                } else {
                    $w += $lw[0];
                }
                if ($w > $cWT) {
                    $w = $lw[0];
                }
                // Footnote is at the bottom of the page. No need to calculate it’s height or wrap the text!
                // We are changing the margins anyway!
                // For anything else but text (images), get the height
                $eH += $this->elements[$i]->getHeight($renderer);
            }
        }

        // Add up what’s the final height
        $cH = $this->height;
        // If any element exist
        if ($cE > 0) {
            // Check if this is text or some other element, like images
            if ($eH === 0.0) {
                // This is text elements. Number of LF but at least one line
                $cHT = ($cHT + 1) * $renderer->tcpdf->getCellHeightRatio();
                // Calculate the cell hight with the largest font size used within this Box
                $cHT *= $renderer->largestFontHeight;
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
        // Finaly, check the last cells height
        if ($cH < $renderer->lastCellHeight) {
            $cH = $renderer->lastCellHeight;
        }
        // Add a new page if needed
        if ($this->pagecheck) {
            // Reset last cell height or Header/Footer will inherit it, in case of pagebreak
            $renderer->lastCellHeight = 0;
            if ($renderer->checkPageBreakPDF($cH)) {
                $cY = $renderer->tcpdf->GetY();
            }
        }

        // Setup the border and background color
        $cS = ''; // Class Style
        if ($this->border) {
            $cS = 'D';
        } // D or empty string: Draw (default)
        $match = [];
        // Fill the background
        if ($this->fill) {
            if (preg_match('/#?(..)(..)(..)/', $this->bgcolor, $match)) {
                $cS .= 'F'; // F: Fill the background
                $r  = hexdec($match[1]);
                $g  = hexdec($match[2]);
                $b  = hexdec($match[3]);
                $renderer->tcpdf->setFillColor($r, $g, $b);
            }
        }
        // Clean up a bit
        unset($lw, $w, $match, $cE, $eH);
        // Draw the border
        if (!empty($cS)) {
            if (!$renderer->tcpdf->getRTL()) {
                $cXM = $cX;
            } else {
                $cXM = $renderer->tcpdf->getPageWidth() - $cX - $cW;
            }
            $renderer->tcpdf->Rect($cXM, $cY, $cW, $cH, $cS);
        }
        // Add cell padding if set and if any text (element) exist
        if ($this->padding) {
            if ($cHT > 0) {
                if (is_array($cM['cell'])) {
                    $renderer->tcpdf->setY($cY + $cM['padding_top']);
                } else {
                    $renderer->tcpdf->setY($cY + $cM['cell']);
                }
            }
        }
        // Change the margins X, Width
        if (!$renderer->tcpdf->getRTL()) {
            if ($this->padding) {
                if (is_array($cM['cell'])) {
                    $renderer->tcpdf->setLeftMargin($cX + $cM['padding_left']);
                } else {
                    $renderer->tcpdf->setLeftMargin($cX + $cM['cell']);
                }
            } else {
                $renderer->tcpdf->setLeftMargin($cX);
            }
            $renderer->tcpdf->setRightMargin($renderer->getRemainingWidthPDF() - $cW + $cM['right']);
        } elseif ($this->padding) {
            if (is_array($cM['cell'])) {
                $renderer->tcpdf->setRightMargin($cX + $cM['padding_right']);
            } else {
                $renderer->tcpdf->setRightMargin($cX + $cM['cell']);
            }
            $renderer->tcpdf->setLeftMargin($renderer->getRemainingWidthPDF() - $cW + $cM['left']);
        } else {
            $renderer->tcpdf->setRightMargin($cX);
            $renderer->tcpdf->setLeftMargin($renderer->getRemainingWidthPDF() - $cW + $cM['left']);
        }
        // Save the current page number
        $cPN = $renderer->tcpdf->getPage();

        // Render the elements (write text, print picture...)
        foreach ($this->elements as $element) {
            if ($element instanceof ReportBaseElement) {
                $element->render($renderer);
            } elseif ($element === 'footnotetexts') {
                $renderer->footnotes();
            } elseif ($element === 'addpage') {
                $renderer->newPage();
            }
        }
        // Restore the margins
        $renderer->tcpdf->setLeftMargin($cM['left']);
        $renderer->tcpdf->setRightMargin($cM['right']);

        // This will be mostly used to trick the multiple images last height
        if ($this->reseth) {
            $cH = 0;
            // This can only happen with multiple images and with pagebreak
            if ($cPN !== $renderer->tcpdf->getPage()) {
                $renderer->tcpdf->setPage($cPN);
            }
        }
        // New line and some clean up
        if (!$this->newline) {
            $renderer->tcpdf->setXY($cX + $cW, $cY);
            $renderer->lastCellHeight = $cH;
        } else {
            // addMarginX() also updates X
            $renderer->addMarginX(0);
            $renderer->tcpdf->setY($cY + $cH);
            $renderer->lastCellHeight = 0;
        }
    }
}
