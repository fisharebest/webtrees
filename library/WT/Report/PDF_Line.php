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
use WT\Auth;

class WT_Report_PDF_Line extends WT_Report_Base_Line {
	/**
	 * PDF line renderer
	 *
	 * @param PDF $pdf
	 *
	 * @return void
	 */
	function render($pdf) {
		if ($this->x1 == ".") {
			$this->x1 = $pdf->GetX();
		}
		if ($this->y1 == ".") {
			$this->y1 = $pdf->GetY();
		}
		if ($this->x2 == ".") {
			$this->x2 = $pdf->getMaxLineWidth();
		}
		if ($this->y2 == ".") {
			$this->y2 = $pdf->GetY();
		}
		if ($pdf->getRTL()) {
			$pdf->Line($pdf->getPageWidth() - $this->x1, $this->y1, $pdf->getPageWidth() - $this->x2, $this->y2);
		} else {
			$pdf->Line($this->x1, $this->y1, $this->x2, $this->y2);
		}
	}
}
