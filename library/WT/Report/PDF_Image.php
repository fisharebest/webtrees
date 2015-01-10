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

/**
 * ImagePDF class element
 */
class WT_Report_PDF_Image extends WT_Report_Base_Image {
	/**
	 * PDF image renderer
	 *
	 * @param PDF $renderer
	 *
	 * @return void
	 */
	function render($renderer) {
		global $lastpicbottom, $lastpicpage, $lastpicleft, $lastpicright;

		// Check for a pagebreak first
		if ($renderer->checkPageBreakPDF($this->height + 5)) {
			$this->y = $renderer->GetY();
		}

		$curx = $renderer->GetX();
		// If current position (left)set "."
		if ($this->x == ".") {
			$this->x = $renderer->GetX();
		} // For static position add margin
		else {
			$this->x = $renderer->addMarginX($this->x);
			$renderer->SetX($curx);
		}
		if ($this->y == ".") {
			//-- first check for a collision with the last picture
			if (isset($lastpicbottom)) {
				if (($renderer->PageNo() == $lastpicpage) && ($lastpicbottom >= $renderer->GetY()) && ($this->x >= $lastpicleft) && ($this->x <= $lastpicright)
				) {
					$renderer->SetY($lastpicbottom + 5);
				}
			}
			$this->y = $renderer->GetY();
		} else {
			$renderer->SetY($this->y);
		}
		if ($renderer->getRTL()) {
			$renderer->Image(
				$this->file,
				$renderer->getPageWidth() - $this->x,
				$this->y,
				$this->width,
				$this->height,
				"",
				"",
				$this->line,
				false,
				72,
				$this->align
			);
		} else {
			$renderer->Image(
				$this->file,
				$this->x,
				$this->y,
				$this->width,
				$this->height,
				"",
				"",
				$this->line,
				false,
				72,
				$this->align
			);
		}
		$lastpicpage = $renderer->PageNo();
		$renderer->lastpicpage = $renderer->getPage();
		$lastpicleft = $this->x;
		$lastpicright = $this->x + $this->width;
		$lastpicbottom = $this->y + $this->height;
		// Setup for the next line
		if ($this->line == "N") {
			$renderer->SetY($lastpicbottom);
		}
	}

	/**
	 * Get the image height
	 *
	 * @param PDF $pdf
	 *
	 * @return float
	 */
	function getHeight($pdf) {
		return $this->height;
	}

	/**
	 * @param $pdf
	 *
	 * @return float
	 */
	function getWidth($pdf) {
		return $this->width;
	}
}
