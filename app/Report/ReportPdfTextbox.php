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
 * Class ReportPdfTextbox
 */
class ReportPdfTextbox extends ReportBaseTextbox {
	/**
	 * PDF Text Box renderer
	 *
	 * @param ReportTcpdf $renderer
	 *
	 * @return bool|int
	 */
	public function render($renderer) {

		$newelements      = array();
		$lastelement      = "";
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

		// Used with line breaks and cell height calculation within this box
		$renderer->largestFontHeight = 0;

		// If current position (left)
		if ($this->left == ".") {
			$cX = $renderer->GetX();
		} else {
			// For static position add margin (returns and updates X)
			$cX = $renderer->addMarginX($this->left);
		}

		// If current position (top)
		if ($this->top == ".") {
			$cY = $renderer->GetY();
		} else {
			$cY = $this->top;
			$renderer->SetY($cY);
		}

		// Check the width if set to page wide OR set by xml to larger then page width (margin)
		if ($this->width == 0 || $this->width > $renderer->getRemainingWidthPDF()) {
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
			$cWT = $cW - ($cM['cell'] * 2);
		}
		// Element height (exept text)
		$eH = 0;
		$w  = 0;
		// Temp Height
		$cHT = 0;
		//-- $lw is an array
		// 0 => last line width
		// 1 => 1 if text was wrapped, 0 if text did not wrap
		// 2 => number of LF
		$lw = array();
		// Element counter
		$cE = count($this->elements);
		//-- calculate the text box height + width
		for ($i = 0; $i < $cE; $i++) {
			if (is_object($this->elements[$i])) {
				$ew = $this->elements[$i]->setWrapWidth($cWT - $w, $cWT);
				if ($ew == $cWT) {
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
			if ($eH == 0) {
				// This is text elements. Number of LF but at least one line
				$cHT = ($cHT + 1) * $renderer->getCellHeightRatio();
				// Calculate the cell hight with the largest font size used within this Box
				$cHT = $cHT * $renderer->largestFontHeight;
				// Add cell padding
				if ($this->padding) {
					if (is_array($cM['cell'])) {
						$cHT += ($cM['padding_bottom'] + $cM['padding_top']);
					} else {
						$cHT += ($cM['cell'] * 2);
					}
				}
				if ($cH < $cHT) {
					$cH = $cHT;
				}
			} // This is any other element
			elseif ($cH < $eH) {
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
				$cY = $renderer->GetY();
			}
		}

		// Setup the border and background color
		$cS = ""; // Class Style
		if ($this->border) {
			$cS = "D";
		} // D or empty string: Draw (default)
		$match = array();
		// Fill the background
		if ($this->fill) {
			if (!empty($this->bgcolor)) {
				if (preg_match("/#?(..)(..)(..)/", $this->bgcolor, $match)) {
					$cS .= "F"; // F: Fill the background
					$r = hexdec($match[1]);
					$g = hexdec($match[2]);
					$b = hexdec($match[3]);
					$renderer->SetFillColor($r, $g, $b);
				}
			}
		}
		// Clean up a bit
		unset($lw, $w, $match, $cE, $eH);
		// Draw the border
		if (!empty($cS)) {
			if (!$renderer->getRTL()) {
				$cXM = $cX;
			} else {
				$cXM = ($renderer->getPageWidth()) - $cX - $cW;
			}
			$renderer->Rect($cXM, $cY, $cW, $cH, $cS);
		}
		// Add cell padding if set and if any text (element) exist
		if ($this->padding) {
			if ($cHT > 0) {
				if (is_array($cM['cell'])) {
					$renderer->SetY($cY + $cM['padding_top']);
				} else {
					$renderer->SetY($cY + $cM['cell']);
				}
			}
		}
		// Change the margins X, Width
		if (!$renderer->getRTL()) {
			if ($this->padding) {
				if (is_array($cM['cell'])) {
					$renderer->SetLeftMargin($cX + $cM['padding_left']);
				} else {
					$renderer->SetLeftMargin($cX + $cM['cell']);
				}
				$renderer->SetRightMargin($renderer->getRemainingWidthPDF() - $cW + $cM['right']);
			} else {
				$renderer->SetLeftMargin($cX);
				$renderer->SetRightMargin($renderer->getRemainingWidthPDF() - $cW + $cM['right']);
			}
		} else {
			if ($this->padding) {
				if (is_array($cM['cell'])) {
					$renderer->SetRightMargin($cX + $cM['padding_right']);
				} else {
					$renderer->SetRightMargin($cX + $cM['cell']);
				}
				$renderer->SetLeftMargin($renderer->getRemainingWidthPDF() - $cW + $cM['left']);
			} else {
				$renderer->SetRightMargin($cX);
				$renderer->SetLeftMargin($renderer->getRemainingWidthPDF() - $cW + $cM['left']);
			}
		}
		// Save the current page number
		$cPN = $renderer->getPage();

		// Render the elements (write text, print picture...)
		foreach ($this->elements as $element) {
			if (is_object($element)) {
				$element->render($renderer);
			} elseif (is_string($element) && $element == 'footnotetexts') {
				$renderer->footnotes();
			} elseif (is_string($element) && $element == 'addpage') {
				$renderer->newPage();
			}
		}
		// Restore the margins
		$renderer->SetLeftMargin($cM['left']);
		$renderer->SetRightMargin($cM['right']);

		// This will be mostly used to trick the multiple images last height
		if ($this->reseth) {
			$cH = 0;
			// This can only happen with multiple images and with pagebreak
			if ($cPN != $renderer->getPage()) {
				$renderer->setPage($cPN);
			}
		}
		// New line and some clean up
		if (!$this->newline) {
			$renderer->SetXY(($cX + $cW), $cY);
			$renderer->lastCellHeight = $cH;
		} else {
			// addMarginX() also updates X
			$renderer->addMarginX(0);
			$renderer->SetY($cY + $cH);
			$renderer->lastCellHeight = 0;
		}

		return true;
	}
}
