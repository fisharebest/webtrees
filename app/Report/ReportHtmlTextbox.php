<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees\Report;

/**
 * Class ReportHtmlTextbox
 */
class ReportHtmlTextbox extends ReportBaseTextbox {
	/**
	 * Render the elements.
	 *
	 * @param ReportHtml $renderer
	 */
	public function render($renderer) {
		// checkFootnote
		$newelements      = array();
		$lastelement      = array();
		$footnote_element = array();
		// Element counter
		$cE = count($this->elements);
		//-- collapse duplicate elements
		for ($i = 0; $i < $cE; $i++) {
			$element = $this->elements[$i];
			if (is_object($element)) {
				if ($element instanceof ReportBaseText) {
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
							$lastelement   = $element;
						}
					}
				} // Collect the Footnote links
				elseif ($element instanceof ReportBaseFootnote) {
					// Check if the Footnote has been set with it’s link number
					$renderer->checkFootnote($element);
					// Save first the last element if any
					if (!empty($lastelement)) {
						$newelements[] = $lastelement;
						$lastelement   = array();
					}
					// Save the Footnote with it’s link number as key for sorting later
					$footnote_element[$element->num] = $element;
				} //-- do not keep empty footnotes
				elseif (!($element instanceof ReportBaseFootnote) || trim($element->getValue()) != "") {
					if (!empty($footnote_element)) {
						ksort($footnote_element);
						foreach ($footnote_element as $links) {
							$newelements[] = $links;
						}
						$footnote_element = array();
					}
					if (!empty($lastelement)) {
						$newelements[] = $lastelement;
						$lastelement   = array();
					}
					$newelements[] = $element;
				}
			} else {
				if (!empty($lastelement)) {
					$newelements[] = $lastelement;
					$lastelement   = array();
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

		$cP = 0; // Class Padding

		// Used with line breaks and cell height calculation within this box only
		$renderer->largestFontHeight = 0;

		// Current position
		if ($this->left == ".") {
			$cX = $renderer->GetX();
		} else {
			$cX = $this->left;
			$renderer->SetX($cX);
		}
		// Current position (top)
		if ($this->top == ".") {
			$this->top = $renderer->GetY();
		} else {
			$renderer->SetY($this->top);
		}

		// Check the width if set to page wide OR set by xml to larger then page wide
		if ($this->width == 0 || $this->width > $renderer->getRemainingWidth()) {
			$this->width = $renderer->getRemainingWidth();
		}
		// Setup the CellPadding
		if ($this->padding) {
			$cP = $renderer->cPadding;
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
		$w  = 0;
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
				if ($ew == $cW) {
					$w = 0;
				}
				$lw = $this->elements[$i]->getWidth($renderer);
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
				$eH += $this->elements[$i]->getHeight($renderer);
			} else {
				$fH += abs($renderer->getFootnotesHeight($cW));
			}
		}
		// Add up what’s the final height
		$cH = $this->height;
		// If any element exist
		if ($cE > 0) {
			// Check if this is text or some other element, like images
			if ($eH == 0) {
				// Number of LF but at least one line
				$cHT = ($cHT + 1) * $renderer->cellHeightRatio;
				// Calculate the cell hight with the largest font size used
				$cHT = $cHT * $renderer->largestFontHeight;
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
		if ($cH < $renderer->lastCellHeight) {
			$cH = $renderer->lastCellHeight;
		}
		// Update max Y incase of a pagebreak
		// We don't want to over write any images or other stuff
		$renderer->addMaxY($this->top + $cH);

		// Start to print HTML
		echo "<div style=\"position:absolute;top:", $this->top, "pt;";
		// LTR (left) or RTL (right)
		echo $renderer->alignRTL, ":", $cX, "pt;";
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
		$cXT = $renderer->GetX();
		$cYT = $renderer->GetY();
		$renderer->SetXY(0, 0);

		// Print the text elements
		foreach ($this->elements as $element) {
			if (is_object($element)) {
				$element->render($renderer, $cX, false);
			} elseif (is_string($element) && $element == "footnotetexts") {
				$renderer->Footnotes();
			} elseif (is_string($element) && $element == "addpage") {
				$renderer->AddPage();
			}
		}
		echo "</div>\n";

		// Reset "margins"
		$renderer->SetXY($cXT, $cYT);
		// This will be mostly used to trick the multiple images last height
		if ($this->reseth) {
			$cH = 0;
		}
		// New line and some clean up
		if (!$this->newline) {
			$renderer->SetXY($cX + $this->width, $this->top);
			$renderer->lastCellHeight = $cH;
		} else {
			$renderer->SetXY(0, $this->top + $cH + ($cP * 2));
			$renderer->lastCellHeight = 0;
		}
	}
}
