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

class WT_Report_HTML_Cell extends WT_Report_Base_Cell {
	/**
	 * HTML Cell renderer
	 *
	 * @param WT_Report_HTML $html
	 *
	 * @return void
	 */
	function render($html) {
		if (strpos($this->text, "{{:ptp:}}") !== false) {
			return;
		}
		$temptext = str_replace("#PAGENUM#", $html->PageNo(), $this->text);
		// underline «title» part of Source item
		$temptext = str_replace(array('«', '»'), array('<u>', '</u>'), $temptext);

		// Setup the style name
		if ($html->getCurrentStyle() != $this->styleName) {
			$html->setCurrentStyle($this->styleName);
		}

		/**
		 * Keep the original values, use these local variables
		 */
		$cW = 0; // Class Width
		$cP = 0; // Class Padding

		// If (Future-feature-enable/disable cell padding)
		$cP = $html->cPadding;

		// Adjust the positions
		if ($this->left == ".") {
			$this->left = $html->GetX();
		} else {
			$html->SetX($this->left);
		}

		if ($this->top == ".") {
			$this->top = $html->GetY();
		} else {
			$html->SetY($this->top);
		}

		// Start collecting the HTML code
		echo "<div class=\"", $this->styleName, "\" style=\"position:absolute;top:", $this->top, "pt;";
		// Use Cell around padding to support RTL also
		echo "padding:", $cP, "pt;";
		// LTR (left) or RTL (right)
		echo $html->alignRTL, ":", $this->left, "pt;";
		// Background color
		if (!empty($this->bgcolor)) {
			echo "background-color:", $this->bgcolor, ";";
		}
		// Border setup
		$bpixX = 0;
		$bpixY = 0;
		if (!empty($this->border)) {
			// Border all around
			if ($this->border == 1) {
				echo " border:solid ";
				if (!empty($this->bocolor)) {
					echo $this->bocolor;
				} else {
					echo "black";
				}
				echo " 1pt;";
				$bpixX = 1;
				$bpixY = 1;
			} else {
				if (stripos($this->border, "T") !== false) {
					echo " border-top:solid ";
					if (!empty($this->bocolor)) {
						echo $this->bocolor;
					} else {
						echo "black";
					}
					echo " 1pt;";
					$bpixY = 1;
				}
				if (stripos($this->border, "B") !== false) {
					echo " border-bottom:solid ";
					if (!empty($this->bocolor)) {
						echo $this->bocolor;
					} else {
						echo "black";
					}
					echo " 1pt;";
					$bpixY = 1;
				}
				if (stripos($this->border, "R") !== false) {
					echo " border-right:solid ";
					if (!empty($this->bocolor)) {
						echo $this->bocolor;
					} else {
						echo "black";
					}
					echo " 1pt;";
					$bpixX = 1;
				}
				if (stripos($this->border, "L") !== false) {
					echo " border-left:solid ";
					if (!empty($this->bocolor)) {
						echo $this->bocolor;
					} else {
						echo "black";
					}
					echo " 1pt;";
					$bpixX = 1;
				}
			}
		}
		// Check the width if set to page wide OR set by xml to larger then page wide
		if ($this->width == 0 || $this->width > $html->getRemainingWidth()) {
			$this->width = $html->getRemainingWidth();
		}
		// We have to calculate a different width for the padding, counting on both side
		$cW = $this->width - ($cP * 2);

		// If there is any text
		if (!empty($temptext)) {
			// Wrap the text
			$temptext = $html->textWrap($temptext, $cW);
			$tmph = $html->getTextCellHeight($temptext);
			// Add some cell padding
			$this->height += $cP;
			if ($tmph > $this->height) {
				$this->height = $tmph;
			}
		}
		// Check the last cell height and ajust with the current cell height
		if ($html->lastCellHeight > $this->height) {
			$this->height = $html->lastCellHeight;
		}
		echo " width:", $cW - $bpixX, "pt;height:", $this->height - $bpixY, "pt;";

		// Text alignment
		switch ($this->align) {
		case "C":
			echo " text-align:center;";
			break;
		case "L":
			echo " text-align:left;";
			break;
		case "R":
			echo " text-align:right;";
			break;
		}

		// Print the collected HTML code
		echo "\">";

		// Print URL
		if (!empty($this->url)) {
			echo "<a href=\"", $this->url, "\">";
		}
		// Print any text if exists
		if (!empty($temptext)) {
			$html->write($temptext, $this->tcolor, false);
		}
		if (!empty($this->url)) {
			echo "</a>";
		}
		// Finish the cell printing and start to clean up
		echo "</div>\n";
		// Where to place the next position
		// -> Next to this cell in the same line
		if ($this->newline == 0) {
			$html->SetXY($this->left + $this->width, $this->top);
			$html->lastCellHeight = $this->height;
		} // -> On a new line at the margin - Default
		elseif ($this->newline == 1) {
			$html->SetXY(0, $html->GetY() + $this->height + ($cP * 2));
			// Reset the last cell height for the next line
			$html->lastCellHeight = 0;
		} // -> On a new line at the end of this cell
		elseif ($this->newline == 2) {
			$html->SetXY($html->GetX() + $this->width, $html->GetY() + $this->height + ($cP * 2));
			// Reset the last cell height for the next line
			$html->lastCellHeight = 0;
		}
	}
}
