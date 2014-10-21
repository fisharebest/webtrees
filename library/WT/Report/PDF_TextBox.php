<?php
// PDF Report Generator
//
// used by the SAX parser to generate PDF reports from the XML report file.
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

class WT_Report_PDF_TextBox extends WT_Report_Base_TextBox {
	/**
	 * PDF Text Box renderer
	 *
	 * @param PDF $pdf
	 *
	 * @return boolean|integer
	 */
	function render($pdf) {

		$newelements = array();
		$lastelement = "";
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
					$pdf->checkFootnote($element);
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

		// Used with line breaks and cell height calculation within this box
		$pdf->largestFontHeight = 0;

		// If current position (left)
		if ($this->left == ".") {
			$cX = $pdf->GetX();
		} else {
			// For static position add margin (returns and updates X)
			$cX = $pdf->addMarginX($this->left);
		}

		// If current position (top)
		if ($this->top == ".") {
			$cY = $pdf->GetY();
		} else {
			$cY = $this->top;
			$pdf->SetY($cY);
		}

		// Check the width if set to page wide OR set by xml to larger then page width (margin)
		if ($this->width == 0 || $this->width > $pdf->getRemainingWidthPDF()) {
			$cW = $pdf->getRemainingWidthPDF();
		} else {
			$cW = $this->width;
		}

		// Save the original margins
		$cM = $pdf->getMargins();
		// Use cell padding to wrap the width
		// Temp Width with cell padding
		if (is_array($cM['cell'])) {
			$cWT = $cW - ($cM['padding_left'] + $cM['padding_right']);
		} else {
			$cWT = $cW - ($cM['cell'] * 2);
		}
		// Element height (exept text)
		$eH = 0;
		$w = 0;
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
				$lw = $this->elements[$i]->getWidth($pdf);
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
				$eH += $this->elements[$i]->getHeight($pdf);
			}
			//else {
			//$h += $pdf->getFootnotesHeight();
			//}
		}

		// Add up what’s the final height
		$cH = $this->height;
		// If any element exist
		if ($cE > 0) {
			// Check if this is text or some other element, like images
			if ($eH == 0) {
				// This is text elements. Number of LF but at least one line
				$cHT = ($cHT + 1) * $pdf->getCellHeightRatio();
				// Calculate the cell hight with the largest font size used within this Box
				$cHT = $cHT * $pdf->largestFontHeight;
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
		if ($cH < $pdf->lastCellHeight) {
			$cH = $pdf->lastCellHeight;
		}
		// Add a new page if needed
		if ($this->pagecheck) {
			// Reset last cell height or Header/Footer will inherit it, in case of pagebreak
			$pdf->lastCellHeight = 0;
			if ($pdf->checkPageBreakPDF($cH)) {
				$cY = $pdf->GetY();
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
					$pdf->SetFillColor($r, $g, $b);
				}
			}
		}
		// Clean up a bit
		unset($lw, $w, $match, $cE, $eH);
		// Draw the border
		if (!empty($cS)) {
			if (!$pdf->getRTL()) {
				$cXM = $cX;
			} else {
				$cXM = ($pdf->getPageWidth()) - $cX - $cW;
			}
			//echo "<br>cX=".$cX."  cXM=".$cXM."  cW=".$cW."  LW=".$pdf->getPageWidth()."  RW=".$pdf->getRemainingWidthPDF()."  MLW=".$pdf->getMaxLineWidth();
			$pdf->Rect($cXM, $cY, $cW, $cH, $cS);
		}
		// Add cell padding if set and if any text (element) exist
		if ($this->padding) {
			if ($cHT > 0) {
				if (is_array($cM['cell'])) {
					$pdf->SetY($cY + $cM['padding_top']);
				} else {
					$pdf->SetY($cY + $cM['cell']);
				}
			}
		}
		// Change the margins X, Width
		if (!$pdf->getRTL()) {
			if ($this->padding) {
				if (is_array($cM['cell'])) {
					$pdf->SetLeftMargin($cX + $cM['padding_left']);
				} else {
					$pdf->SetLeftMargin($cX + $cM['cell']);
				}
				$pdf->SetRightMargin($pdf->getRemainingWidthPDF() - $cW + $cM['right']);
			} else {
				$pdf->SetLeftMargin($cX);
				$pdf->SetRightMargin($pdf->getRemainingWidthPDF() - $cW + $cM['right']);
			}
		} else {
			if ($this->padding) {
				if (is_array($cM['cell'])) {
					$pdf->SetRightMargin($cX + $cM['padding_right']);
				} else {
					$pdf->SetRightMargin($cX + $cM['cell']);
				}
				$pdf->SetLeftMargin($pdf->getRemainingWidthPDF() - $cW + $cM['left']);
			} else {
				$pdf->SetRightMargin($cX);
				$pdf->SetLeftMargin($pdf->getRemainingWidthPDF() - $cW + $cM['left']);
			}
		}
		// Save the current page number
		$cPN = $pdf->getPage();

		// Render the elements (write text, print picture...)
		foreach ($this->elements as $element) {
			if (is_object($element)) {
				$element->render($pdf);
			} elseif (is_string($element) && $element == 'footnotetexts') {
				$pdf->footnotes();
			} elseif (is_string($element) && $element == 'addpage') {
				$pdf->newPage();
			}
		}
		// Restore the margins
		$pdf->SetLeftMargin($cM['left']);
		$pdf->SetRightMargin($cM['right']);

		// This will be mostly used to trick the multiple images last height
		if ($this->reseth) {
			$cH = 0;
			// This can only happen with multiple images and with pagebreak
			if ($cPN != $pdf->getPage()) {
				$pdf->setPage($cPN);
			}
		}
		// New line and some clean up
		if (!$this->newline) {
			$pdf->SetXY(($cX + $cW), $cY);
			$pdf->lastCellHeight = $cH;
		} else {
			// addMarginX() also updates X
			$pdf->addMarginX(0);
			$pdf->SetY($cY + $cH);
			$pdf->lastCellHeight = 0;
		}

		return true;
	}
}
