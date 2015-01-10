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
 * Class WT_Report_HTML_Line - Base Report Generator, used by the SAX
 * parser to generate reports from the XML report file.
 */
class WT_Report_HTML_Line extends WT_Report_Base_Line {
	/**
	 * HTML line renderer
	 *
	 * @param WT_Report_HTML $renderer
	 *
	 * @return void
	 */
	function render($renderer) {
		if ($this->x1 == ".") $this->x1 = $renderer->getX();
		if ($this->y1 == ".") $this->y1 = $renderer->getY();
		if ($this->x2 == ".") {
			$this->x2 = $renderer->getRemainingWidth();
		}
		if ($this->y2 == ".") $this->y2 = $renderer->getY();
		// TODO Non verticle or horizontal lines can use a series of divs absolutely positioned
		// Vertical line
		if ($this->x1 == $this->x2) {
			echo "<div style=\"position:absolute;overflow:hidden;border-", $renderer->alignRTL, ":solid black 1pt;", $renderer->alignRTL, ":", $this->x1, "pt;top:", $this->y1 + 1, "pt;width:1pt;height:", $this->y2 - $this->y1, "pt;\"> </div>\n";
		}
		// Horizontal line
		if ($this->y1 == $this->y2) {
			echo "<div style=\"position:absolute;overflow:hidden;border-top:solid black 1pt;", $renderer->alignRTL, ":", $this->x1, "pt;top:", $this->y1 + 1, "pt;width:", $this->x2 - $this->x1, "pt;height:1pt;\"> </div>\n";
		}
		// Keep max Y updated
		// One or the other will be higher... lasy mans way...
		$renderer->addMaxY($this->y1);
		$renderer->addMaxY($this->y2);
	}
}
