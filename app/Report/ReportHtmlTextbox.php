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

use function abs;
use function count;
use function is_object;
use function ksort;
use function str_replace;
use function trim;

/**
 * Class ReportHtmlTextbox
 */
class ReportHtmlTextbox extends ReportBaseTextbox
{
    /**
     * Render the elements.
     *
     * @param HtmlRenderer $renderer
     *
     * @return void
     */
    public function render($renderer): void
    {
        static $lastBoxYfinal;
        // checkFootnote
        $newelements      = [];
        $lastelement      = null;
        $footnote_element = [];
        // Element counter
        $cE = count($this->elements);
        //-- collapse duplicate elements
        for ($i = 0; $i < $cE; $i++) {
            $element = $this->elements[$i];
            if ($element instanceof ReportBaseElement) {
                if ($element instanceof ReportBaseText) {
                    if (!empty($footnote_element)) {
                        ksort($footnote_element);
                        foreach ($footnote_element as $links) {
                            $newelements[] = $links;
                        }
                        $footnote_element = [];
                    }
                    if (empty($lastelement)) {
                        $lastelement = $element;
                    } elseif ($element instanceof ReportBaseText && $lastelement instanceof ReportBaseText)
                        if ($element->getStyleName() === $lastelement->getStyleName()) {
                            // Checking if the Text has the same style
                            $lastelement->addText(str_replace("\n", '<br>', $element->getValue()));
                        }
                    } else {
                        $newelements[] = $lastelement;
                        $lastelement   = $element;
                    }
                } elseif ($element instanceof ReportHtmlImage) {
                    $lastelement   = $element;
                } elseif ($element instanceof ReportHtmlFootnote) {
                    // Check if the Footnote has been set with it’s link number
                    $renderer->checkFootnote($element);
                    // Save first the last element if any
                    if (isset($lastelement)) {
                        $newelements[] = $lastelement;
                        $lastelement   = null;
                    }
                    // Save the Footnote with it’s link number as key for sorting later
                    $footnote_element[$element->num] = $element;
                } elseif (trim($element->getValue()) !== '') {
                    // Do not keep empty footnotes
                    if (!empty($footnote_element)) {
                        ksort($footnote_element);
                        foreach ($footnote_element as $links) {
                            $newelements[] = $links;
                        }
                        $footnote_element = [];
                    }
                    if (isset($lastelement)) {
                        $newelements[] = $lastelement;
                        $lastelement   = null;
                    }
                    $newelements[] = $element;
                }
            } else {
                if (isset($lastelement)) {
                    $newelements[] = $lastelement;
                    $lastelement   = null;
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
        if (isset($lastelement)) {
            $newelements[] = $lastelement;
        }
        if (!empty($footnote_element)) {
            ksort($footnote_element);
            foreach ($footnote_element as $links) {
                $newelements[] = $links;
            }
        }
        $this->elements = $newelements;
        unset($footnote_element, $lastelement, $newelements);

        $cP = 0; // Class Padding

        // Used with line breaks and cell height calculation within this box only
        $renderer->largestFontHeight = 0;

        // If current position (left)
        if ($this->left === ReportBaseElement::CURRENT_POSITION) {
            $cX = $renderer->getX();
        } else {
            $cX = $this->left;
            $renderer->setX($cX);
        }
        // If current position (top)
        $align_Y = false;
        $topstr = "";
        if ($this->top < -110000) { // pos='abs'
            $this->top += 222000;
        }
        if ($this->top < -10000) { // <= -100000: both pdf and html; -100000 -- -90000: only html
            $this->top += 90000;  //= ReportBaseElement::CURRENT_POSITION;
            if ($this->top < -9000) {
                $this->top += 10000;
            }
            $topstr = "top:" . $this->top . "pt;";
            $align_Y = true;
        }
        if ($this->top === ReportBaseElement::CURRENT_POSITION) {
            $this->top = $renderer->getY();
        } else {
            $renderer->setY($this->top);
        }

        // Check the width if set to page wide OR set by xml to larger then page width (margin)
        if ($this->width === 0.0 || $this->width > $renderer->getRemainingWidth()) {
            $this->width = $renderer->getRemainingWidth();
        }
        // Setup the CellPadding
        if ($this->padding) {
            $cP = $renderer->cPadding;
        }

        // For padding, we have to use less wrap width
        $cW = $this->width - $cP * 2.0;

        //-- calculate the text box height
        // Number of lines, will be converted to height
        $cHT = 0;
        // Element height (except text)
        $eH = 0.0;
        // Footnote height (in points)
        $fH = 0;
        $w  = 0;
        //-- $lw is an array
        // 0 => last line width
        // 1 => 1 if text was wrapped, 0 if text did not wrap
        // 2 => number of LF
        $lw = [];
        // Element counter
        $cE = count($this->elements);
        for ($i = 0; $i < $cE; $i++) {
            if (is_object($this->elements[$i])) {
                $ew = $this->elements[$i]->setWrapWidth($cW - $w - 2, $cW);
                if ($ew === $cW) {
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
                if ($w > $cW) {
                    $w = $lw[0];
                }
                // For anything else but text (images), get the height
                $eH += $this->elements[$i]->getHeight($renderer);
            } else {
                $fH += abs($renderer->getFootnotesHeight($cW));
            }
        }

        // Add up what’s the final height
        //$cH = $this->height;
        $cH = 0;
        // If any element exist
        if ($cE > 0) {
            // Check if this is text or some other element, like images
            if ($eH === 0.0) {
                // Number of LF but at least one line
                $cHT = ($cHT + 1) * $renderer->cellHeightRatio;
                // Calculate the cell height with the largest font size used
                $cHT *= $renderer->largestFontHeight;
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

        unset($lw, $cHT, $fH, $w);

        // Finally, check the last cells height
        if ($cH < $renderer->lastCellHeight) {
            $cH = $renderer->lastCellHeight;
        }
        // Update max Y in case of a pagebreak
        // We don't want to over write any images or other stuff
        $renderer->addMaxY($this->top + $cH);

        // Start to print HTML
        if (!$align_Y) {
            echo '<div style="position:absolute;top:', $this->top, 'pt;';
        } else {
            echo '<div style="position:relative;top:', $this->top, 'pt;';
        }
        //echo '<div style="position:relative;';
        // LTR (left) or RTL (right)
        echo $renderer->alignRTL, ':', $cX, 'pt;';
        // Background color
        if ($this->fill && $this->bgcolor !== '') {
            echo ' background-color:', $this->bgcolor, ';';
        }
        // Print padding only when it’s set
        if ($this->padding) {
            // Use Cell around padding to support RTL also
            echo 'padding:', $cP, 'pt;';
        }
        // Border setup
        if ($this->border) {
            echo ' border:solid black 1pt;';
            if (!$align_Y) {
                echo 'width:', $this->width - 1 - $cP * 2, 'pt;height:', $cH - 1, 'pt;';
            } else {
                echo 'width:', $this->width - 1 - $cP * 2, 'pt;height:auto;';
            } // height:',$this->height,'pt;'; //,$topstr;
        } else {
            if (!$align_Y) {
                echo 'width:', $this->width - $cP * 2, 'pt;height:', $cH, 'pt;';
            } else {
                echo 'width:', $this->width - $cP * 2, 'pt;height:auto;';
            } //height:',$this->height,'pt;'; //,$topstr;
        }
        echo '">';

        // Do a little "margin" trick before print
        // to get the correct current position => "."
        $cXT = $renderer->getX();
        $cYT = $renderer->getY();
        $renderer->setXy(0, 0);

        // Print the text elements
        foreach ($this->elements as $element) {
            if ($element instanceof ReportHtmlText) {
                $element->render($renderer, false);
            } elseif ($element instanceof ReportBaseElement) {
                $element->render($renderer);
            } elseif ($element === 'footnotetexts') {
                $renderer->footnotes();
            } elseif ($element === 'addpage') {
                $renderer->addPage();
            }
        }
        echo "</div>\n";

        // Reset "margins"
        $renderer->setXy($cXT, $cYT);
        // This will be mostly used to trick the multiple images last height
        if ($this->reseth) {
            $cH = 0;
        }
        // New line and some clean up
        if (!$this->newline) {
            $renderer->setXy($cX + $this->width, $this->top);
            $renderer->lastCellHeight = $cH;
        } else {
            $renderer->setXy(0, $this->top + $cH + $cP * 2);
            $renderer->lastCellHeight = 0;
        }
        // This will make images in textboxes to ignore images in previous textboxes
        // Without this trick the $lastpicbottom in the previos textbox will be used in ReportHtmlImage
        $renderer->pageN++;
    }
}
