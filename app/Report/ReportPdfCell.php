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

use Fisharebest\Webtrees\Functions\FunctionsRtl;

/**
 * Class ReportPdfCell
 */
class ReportPdfCell extends ReportBaseCell {
	/**
	 * PDF Cell renderer
	 *
	 * @param ReportTcpdf $renderer
	 */
	public function render($renderer) {

		// Set up the text style
		if (($renderer->getCurrentStyle()) != ($this->styleName)) {
			$renderer->setCurrentStyle($this->styleName);
		}
		$temptext = str_replace("#PAGENUM#", $renderer->PageNo(), $this->text);
		// underline «title» part of Source item
		$temptext = str_replace(array('«', '»'), array('<u>', '</u>'), $temptext);
		$match    = array();
		// Indicates if the cell background must be painted (1) or transparent (0)
		if ($this->fill == 1) {
			if (!empty($this->bgcolor)) {
				// HTML color to RGB
				if (preg_match("/#?(..)(..)(..)/", $this->bgcolor, $match)) {
					$r = hexdec($match[1]);
					$g = hexdec($match[2]);
					$b = hexdec($match[3]);
					$renderer->SetFillColor($r, $g, $b);
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
				$renderer->SetDrawColor($r, $g, $b);
			}
		}
		// Paint the text color or they might use inherited colors by the previous function
		if (preg_match("/#?(..)(..)(..)/", $this->tcolor, $match)) {
			$r = hexdec($match[1]);
			$g = hexdec($match[2]);
			$b = hexdec($match[3]);
			$renderer->SetTextColor($r, $g, $b);
		} else {
			$renderer->SetTextColor(0, 0, 0);
		}

		// If current position (left)
		if ($this->left == ".") {
			$cX = $renderer->GetX();
		} // For static position add margin (also updates X)
		else {
			$cX = $renderer->addMarginX($this->left);
		}

		// Check the width if set to page wide OR set by xml to larger then page wide
		if ($this->width == 0 || $this->width > $renderer->getRemainingWidthPDF()) {
			$this->width = $renderer->getRemainingWidthPDF();
		}
		// For current position
		if ($this->top == ".") {
			$this->top = $renderer->GetY();
		} else {
			$renderer->SetY($this->top);
		}

		// Check the last cell height and adjust the current cell height if needed
		if ($renderer->lastCellHeight > $this->height) {
			$this->height = $renderer->lastCellHeight;
		}
		// Check for pagebreak
		if (!empty($temptext)) {
			$cHT = $renderer->getNumLines($temptext, $this->width);
			$cHT = $cHT * $renderer->getCellHeightRatio() * $renderer->getCurrentStyleHeight();
			$cM  = $renderer->getMargins();
			// Add padding
			if (is_array($cM['cell'])) {
				$cHT += ($cM['padding_bottom'] + $cM['padding_top']);
			} else {
				$cHT += ($cM['cell'] * 2);
			}
			// Add a new page if needed
			if ($renderer->checkPageBreakPDF($cHT)) {
				$this->top = $renderer->GetY();
			}
			$temptext = FunctionsRtl::spanLtrRtl($temptext, "BOTH");
		}
		// HTML ready - last value is true
		$renderer->MultiCell(
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
			$renderer->lastCellHeight = 0;
		} // OR save the last height if heigher then before
		elseif ($renderer->lastCellHeight < $renderer->getLastH()) {
			$renderer->lastCellHeight = $renderer->getLastH();
		}

		// Set up the url link if exists ontop of the cell
		if (!empty($this->url)) {
			$renderer->Link($cX, $this->top, $this->width, $this->height, $this->url);
		}
		// Reset the border and the text color to black or they will be inherited
		$renderer->SetDrawColor(0, 0, 0);
		$renderer->SetTextColor(0, 0, 0);
	}
}
