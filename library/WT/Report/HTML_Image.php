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

/**
 * Image element
 */
class WT_Report_HTML_Image extends WT_Report_Base_Image {
	/**
	 * Image renderer
	 *
	 * @param WT_Report_HTML $html
	 *
	 * @return void
	 */
	function render($html) {
		global $lastpicbottom, $lastpicpage, $lastpicleft, $lastpicright;

		// Get the current positions
		if ($this->x == ".") {
			$this->x = $html->getX();
		}
		if ($this->y == ".") {
			//-- first check for a collision with the last picture
			if (isset($lastpicbottom)) {
				if (($html->pageNo() == $lastpicpage) && ($lastpicbottom >= $html->getY()) && ($this->x >= $lastpicleft) && ($this->x <= $lastpicright)) {
					$html->setY($lastpicbottom + ($html->cPadding * 2));
				}
			}
			$this->y = $html->getY();
		}

		// Image alignment
		switch ($this->align) {
		case "L":
			echo "<div style=\"position:absolute;top:", $this->y, "pt;left:0pt;width:", $html->getRemainingWidth(), "pt;text-align:left;\">\n";
			echo "<img src=\"", $this->file, "\" style=\"width:", $this->width, "pt;height:", $this->height, "pt;\" alt=\"\">\n</div>\n";
			break;
		case "C":
			echo "<div style=\"position:absolute;top:", $this->y, "pt;left:0pt;width:", $html->getRemainingWidth(), "pt;text-align:center;\">\n";
			echo "<img src=\"", $this->file, "\" style=\"width:", $this->width, "pt;height:", $this->height, "pt;\" alt=\"\">\n</div>\n";
			break;
		case "R":
			echo "<div style=\"position:absolute;top:", $this->y, "pt;left:0pt;width:", $html->getRemainingWidth(), "pt;text-align:right;\">\n";
			echo "<img src=\"", $this->file, "\" style=\"width:", $this->width, "pt;height:", $this->height, "pt;\" alt=\"\">\n</div>\n";
			break;
		default:
			echo "<img src=\"", $this->file, "\" style=\"position:absolute;", $html->alignRTL, ":", $this->x, "pt;top:", $this->y, "pt;width:", $this->width, "pt;height:", $this->height, "pt;\" alt=\"\">\n";
		}

		$lastpicpage = $html->pageNo();
		$lastpicleft = $this->x;
		$lastpicright = $this->x + $this->width;
		$lastpicbottom = $this->y + $this->height;
		// Setup for the next line
		if ($this->line == "N") {
			$html->setY($lastpicbottom);
		}
		// Keep max Y updated
		$html->addMaxY($lastpicbottom);
	}

	/**
	 * Get the image height
	 *
	 * This would be called from the TextBox only for multiple images
	 * so we add a bit bottom space between the images
	 *
	 * @param WT_Report_HTML $html
	 *
	 * @return float
	 */
	function getHeight($html) {
		return $this->height + ($html->cPadding * 2);
	}

}
