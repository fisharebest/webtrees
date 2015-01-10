<?php
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
 * Class WT_Report_PDF_Line - Base Report Generator, used by the SAX
 * parser to generate reports from the XML report file.
 */
class WT_Report_PDF_Line extends WT_Report_Base_Line {
	/**
	 * PDF line renderer
	 *
	 * @param PDF $renderer
	 *
	 * @return void
	 */
	function render($renderer) {
		if ($this->x1 == ".") {
			$this->x1 = $renderer->GetX();
		}
		if ($this->y1 == ".") {
			$this->y1 = $renderer->GetY();
		}
		if ($this->x2 == ".") {
			$this->x2 = $renderer->getMaxLineWidth();
		}
		if ($this->y2 == ".") {
			$this->y2 = $renderer->GetY();
		}
		if ($renderer->getRTL()) {
			$renderer->Line($renderer->getPageWidth() - $this->x1, $this->y1, $renderer->getPageWidth() - $this->x2, $this->y2);
		} else {
			$renderer->Line($this->x1, $this->y1, $this->x2, $this->y2);
		}
	}
}
