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

class WT_Report_PDF_Cell extends WT_Report_Base_Cell {
	/**
	 * PDF Cell renderer
	 *
	 * @param PDF $pdf
	 *
	 * @return void
	 */
	function render($pdf) {

		// Set up the text style
		if (($pdf->getCurrentStyle()) != ($this->styleName)) {
			$pdf->setCurrentStyle($this->styleName);
		}
		$temptext = str_replace("#PAGENUM#", $pdf->PageNo(), $this->text);
		// underline «title» part of Source item
		$temptext = str_replace(array('«', '»'), array('<u>', '</u>'), $temptext);
		$match = array();
		// Indicates if the cell background must be painted (1) or transparent (0)
		if ($this->fill == 1) {
			if (!empty($this->bgcolor)) {
				// HTML color to RGB
				if (preg_match("/#?(..)(..)(..)/", $this->bgcolor, $match)) {
					$r = hexdec($match[1]);
					$g = hexdec($match[2]);
					$b = hexdec($match[3]);
					$pdf->SetFillColor($r, $g, $b);
				}
			} // If no color set then don't fill
			else {
				$this->fill = 0;
			}
		}
		// Paint the Border color if set
		if (!empty($this->bocolor)) {
			// HTML color to RGB
			if (preg_match("/#?(..)(..)(..)/", $this->bocolor, $match)) {
				$r = hexdec($match[1]);
				$g = hexdec($match[2]);
				$b = hexdec($match[3]);
				$pdf->SetDrawColor($r, $g, $b);
			}
		}
		// Paint the text color or they might use inherited colors by the previous function
		if (preg_match("/#?(..)(..)(..)/", $this->tcolor, $match)) {
			$r = hexdec($match[1]);
			$g = hexdec($match[2]);
			$b = hexdec($match[3]);
			$pdf->SetTextColor($r, $g, $b);
		} else {
			$pdf->SetTextColor(0, 0, 0);
		}

		// If current position (left)
		if ($this->left == ".") {
			$cX = $pdf->GetX();
		} // For static position add margin (also updates X)
		else {
			$cX = $pdf->addMarginX($this->left);
		}

		// Check the width if set to page wide OR set by xml to larger then page wide
		if ($this->width == 0 || $this->width > $pdf->getRemainingWidthPDF()) {
			$this->width = $pdf->getRemainingWidthPDF();
		}
		// For current position
		if ($this->top == ".") {
			$this->top = $pdf->GetY();
		} else {
			$pdf->SetY($this->top);
		}

		// Check the last cell height and adjust the current cell height if needed
		if ($pdf->lastCellHeight > $this->height) {
			$this->height = $pdf->lastCellHeight;
		}
		// Check for pagebreak
		if (!empty($temptext)) {
			$cHT = $pdf->getNumLines($temptext, $this->width);
			$cHT = $cHT * $pdf->getCellHeightRatio() * $pdf->getCurrentStyleHeight();
			$cM = $pdf->getMargins();
			// Add padding
			if (is_array($cM['cell'])) {
				$cHT += ($cM['padding_bottom'] + $cM['padding_top']);
			} else {
				$cHT += ($cM['cell'] * 2);
			}
			// Add a new page if needed
			if ($pdf->checkPageBreakPDF($cHT)) {
				$this->top = $pdf->GetY();
			}
			$temptext = spanLTRRTL($temptext, "BOTH");
		}
		// HTML ready - last value is true
		$pdf->MultiCell(
			$this->width,
			$this->height,
			$temptext,
			$this->border,
			$this->align,
			$this->fill,
			$this->newline,
			$cX,
			$this->top,
			$this->reseth,
			$this->stretch,
			true
		);
		// Reset the last cell height for the next line
		if ($this->newline >= 1) {
			$pdf->lastCellHeight = 0;
		} // OR save the last height if heigher then before
		elseif ($pdf->lastCellHeight < $pdf->getLastH()) {
			$pdf->lastCellHeight = $pdf->getLastH();
		}

		// Set up the url link if exists ontop of the cell
		if (!empty($this->url)) {
			$pdf->Link($cX, $this->top, $this->width, $this->height, $this->url);
		}
		// Reset the border and the text color to black or they will be inherited
		$pdf->SetDrawColor(0, 0, 0);
		$pdf->SetTextColor(0, 0, 0);
	}
}
