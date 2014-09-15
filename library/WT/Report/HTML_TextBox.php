<?php
// HTML Report Generator
//
// used by the SAX parser to generate HTML reports from the XML report file.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

class WT_Report_HTML_TextBox extends WT_Report_Base_TextBox {
	/**
	 * @param $html
	 *
	 * @return void
	 */
	function render($html) {
		// checkFootnote
		$newelements = array();
		$lastelement = array();
		$footnote_element = array();
		// Element counter
		$cE = count($this->elements);
		//-- collapse duplicate elements
		for ($i = 0; $i < $cE; $i++) {
			$element = $this->elements[$i];
			if (is_object($element)) {
				if ($element instanceof WT_Report_Base_Text) {
					if (!empty($footnote_element)) {
						ksort($footnote_element);
						foreach ($footnote_element as $links) {
							$newelements[] = $links;
						}
						$footnote_element = array();
					}
					if (empty($lastelement)) {
						$lastelement = $element;
					} else {
						// Checking if the Text has the same style
						if ($element->getStyleName() == $lastelement->getStyleName()) {
							$lastelement->addText(str_replace("\n", "<br>", $element->getValue()));
						} elseif (!empty($lastelement)) {
							$newelements[] = $lastelement;
							$lastelement = $element;
						}
					}
				} // Collect the Footnote links
				elseif ($element instanceof WT_Report_Base_Footnote) {
					// Check if the Footnote has been set with it’s link number
					$html->checkFootnote($element);
					// Save first the last element if any
					if (!empty($lastelement)) {
						$newelements[] = $lastelement;
						$lastelement = array();
					}
					// Save the Footnote with it’s link number as key for sorting later
					$footnote_element[$element->num] = $element;
				} //-- do not keep empty footnotes
				elseif (!($element instanceof WT_Report_Base_Footnote) || trim($element->getValue()) != "") {
					if (!empty($footnote_element)) {
						ksort($footnote_element);
						foreach ($footnote_element as $links) {
							$newelements[] = $links;
						}
						$footnote_element = array();
					}
					if (!empty($lastelement)) {
						$newelements[] = $lastelement;
						$lastelement = array();
					}
					$newelements[] = $element;
				}
			} else {
				if (!empty($lastelement)) {
					$newelements[] = $lastelement;
					$lastelement = array();
				}
				if (!empty($footnote_element)) {
					ksort($footnote_element);
					foreach ($footnote_element as $links) {
						$newelements[] = $links;
					}
					$footnote_element = array();
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

		/**
		 * Use these variables to update/manipulate values
		 * Repeted classes would reupdate all their class variables again, Header/Page Header/Footer
		 * This is the bugfree version
		 */
		$cH = 0; // Class Height
		$cX = 0; // Class Left
		// Protect height, width, lastheight from padding
		$cP = 0; // Class Padding
		$cW = 0; // Class Width
		// Used with line breaks and cell height calculation within this box only
		$html->largestFontHeight = 0;

		// Current position
		if ($this->left == ".") {
			$cX = $html->GetX();
		} else {
			$cX = $this->left;
			$html->SetX($cX);
		}
		// Current position (top)
		if ($this->top == ".") {
			$this->top = $html->GetY();
		} else {
			$html->SetY($this->top);
		}

		// Check the width if set to page wide OR set by xml to larger then page wide
		if ($this->width == 0 || $this->width > $html->getRemainingWidth()) {
			$this->width = $html->getRemainingWidth();
		}
		// Setup the CellPadding
		if ($this->padding) {
			$cP = $html->cPadding;
		}

		// For padding, we have to use less wrap width
		$cW = $this->width - ($cP * 2);

		//-- calculate the text box height
		// Number of lines, will be converted to height
		$cHT = 0;
		// Element height (exept text)
		$eH = 0;
		// Footnote height (in points)
		$fH = 0;
		$w = 0;
		//-- $lw is an array
		// 0 => last line width
		// 1 => 1 if text was wrapped, 0 if text did not wrap
		// 2 => number of LF
		$lw = array();
		// Element counter
		$cE = count($this->elements);
		for ($i = 0; $i < $cE; $i++) {
			if (is_object($this->elements[$i])) {
				$ew = $this->elements[$i]->setWrapWidth($cW - $w - 2, $cW);
				if ($ew == $cW)
					$w = 0;
				$lw = $this->elements[$i]->getWidth($html);
				// Text is already gets the # LF
				$cHT += $lw[2];
				if ($lw[1] == 1) {
					$w = $lw[0];
				} elseif ($lw[1] == 2) {
					$w = 0;
				} else {
					$w += $lw[0];
				}
				if ($w > $cW) {
					$w = $lw[0];
				}
				// For anything else but text (images), get the height
				$eH += $this->elements[$i]->getHeight($html);
			} else {
				//if (is_string($element) and $element == "footnotetexts") $html->Footnotes();
				$fH += abs($html->getFootnotesHeight($cW));
			}
		}
		// Add up what’s the final height
		$cH = $this->height;
		// If any element exist
		if ($cE > 0) {
			// Check if this is text or some other element, like images
			if ($eH == 0) {
				// Number of LF but at least one line
				$cHT = ($cHT + 1) * $html->cellHeightRatio;
				// Calculate the cell hight with the largest font size used
				$cHT = $cHT * $html->largestFontHeight;
				if ($cH < $cHT) {
					$cH = $cHT;
				}
			} // This is any other element
			else {
				if ($cH < $eH) {
					$cH = $eH;
				}
				// Add Footnote height to the rest of the height
				$cH += $fH;
			}
		}

		unset($lw, $cHT, $fH, $w);

		// Finaly, check the last cells height
		if ($cH < $html->lastCellHeight) {
			$cH = $html->lastCellHeight;
		}
		// Update max Y incase of a pagebreak
		// We don't want to over write any images or other stuff
		$html->addMaxY($this->top + $cH);

		// Start to print HTML
		echo "<div style=\"position:absolute;top:", $this->top, "pt;";
		// LTR (left) or RTL (right)
		echo $html->alignRTL, ":", $cX, "pt;";
		// Background color
		if ($this->fill) {
			if (!empty($this->bgcolor)) {
				echo " background-color:", $this->bgcolor, ";";
			}
		}
		// Print padding only when it’s set
		if ($this->padding) {
			// Use Cell around padding to support RTL also
			echo "padding:", $cP, "pt;";
		}
		// Border setup
		if ($this->border) {
			echo " border:solid black 1pt;";
			echo "width:", ($this->width - 1 - ($cP * 2)), "pt;height:", $cH - 1, "pt;";
		} else {
			echo "width:", ($this->width - ($cP * 2)), "pt;height:", $cH, "pt;";
		}
		echo "\">";

		// Do a little "margin" trick before print
		// to get the correct current position => "."
		$cXT = $html->GetX();
		$cYT = $html->GetY();
		$html->SetXY(0, 0);

		// Print the text elements
		foreach ($this->elements as $element) {
			if (is_object($element)) {
				$element->render($html, $cX, false);
			} elseif (is_string($element) && $element == "footnotetexts") {
				$html->Footnotes();
			} elseif (is_string($element) && $element == "addpage") {
				$html->AddPage();
			}
		}
		echo "</div>\n";

		// Reset "margins"
		$html->SetXY($cXT, $cYT);
		// This will be mostly used to trick the multiple images last height
		if ($this->reseth) {
			$cH = 0;
		}
		// New line and some clean up
		if (!$this->newline) {
			$html->SetXY($cX + $this->width, $this->top);
			$html->lastCellHeight = $cH;
		} else {
			$html->SetXY(0, $this->top + $cH + ($cP * 2));
			$html->lastCellHeight = 0;
		}
	}
}
