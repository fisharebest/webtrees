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

class WT_Report_HTML_Text extends WT_Report_Base_Text {
	/**
	 * @param WT_Report_HTML $html
	 * @param integer        $curx
	 * @param boolean        $attrib Is is called from a different element?
	 *
	 * @return void
	 */
	function render($html, $curx = 0, $attrib = true) {

		// Setup the style name
		if ($html->getCurrentStyle() != $this->styleName) {
			$html->setCurrentStyle($this->styleName);
		}
		$temptext = str_replace("#PAGENUM#", $html->pageNo(), $this->text);
		// underline «title» part of Source item
		$temptext = str_replace(array('«', '»'), array('<u>', '</u>'), $temptext);

		// If any text at all
		if (!empty($temptext)) {
			// If called by an other element
			if (!$attrib) {
				$html->write($temptext, $this->color);
			} else {
				// Save the start positions
				$startX = $html->getX();
				$startY = $html->getY();
				$width = $html->getRemainingWidth();
				// If text is wider then page width then wrap it
				if ($html->GetStringWidth($temptext) > $width) {
					$lines = explode("\n", $temptext);
					foreach ($lines as $line) {
						echo "<div style=\"position:absolute;top:", $startY, "pt;", $html->alignRTL, ":", $startX, "pt;width:", $width, "pt;\">";
						$line = $html->textWrap($line, $width);
						$startY += $html->getTextCellHeight($line);
						$html->setY($startY);
						$html->write($line, $this->color);
						echo "</div>\n";
					}
				} else {
					echo "<div style=\"position:absolute;top:", $startY, "pt;", $html->alignRTL, ":", $startX, "pt;width:", $width, "pt;\">";
					$html->write($temptext, $this->color);
					echo "</div>\n";
					$html->setX($startX + $html->GetStringWidth($temptext));
					if ($html->countLines($temptext) != 1) {
						$html->setXy(0, ($startY + $html->getTextCellHeight($temptext)));
					}
				}
			}
		}
	}

	/**
	 * Returns the height in points of the text element
	 *
	 * The height is already calculated in getWidth()
	 *
	 * @param WT_Report_HTML $html
	 *
	 * @return float
	 */
	function getHeight($html) {
		$ct = substr_count($this->text, "\n");
		if ($ct > 0) {
			$ct += 1;
		}
		$style = $html->getStyle($this->styleName);
		return ($style["size"] * $ct) * $html->cellHeightRatio;
	}

	/**
	 * Get the width of text and wrap it too
	 *
	 * @param WT_Report_HTML $html
	 *
	 * @return array
	 */
	function getWidth($html) {
		// Setup the style name
		if ($html->getCurrentStyle() != $this->styleName) {
			$html->setCurrentStyle($this->styleName);
		}

		// Check for the largest font size in the box
		$fsize = $html->getCurrentStyleHeight();
		if ($fsize > $html->largestFontHeight) {
			$html->largestFontHeight = $fsize;
		}

		// Get the line width for the text in points
		$lw = $html->GetStringWidth($this->text);
		// Line Feed counter - Number of lines in the text
		$lfct = $html->countLines($this->text);
		// If there is still remaining wrap width...
		if ($this->wrapWidthRemaining > 0) {
			// Check with line counter too!
			if (($lw >= $this->wrapWidthRemaining) or ($lfct > 1)) {
				$newtext = "";
				$wrapWidthRemaining = $this->wrapWidthRemaining;
				$lines = explode("\n", $this->text);
				// Go throught the text line by line
				foreach ($lines as $line) {
					// Line width in points + a little margin
					$lw = $html->GetStringWidth($line);
					// If the line has to be wraped
					if ($lw > $wrapWidthRemaining) {
						$words = explode(" ", $line);
						$addspace = count($words);
						$lw = 0;
						foreach ($words as $word) {
							$addspace--;
							$lw += $html->GetStringWidth($word . " ");
							if ($lw <= $wrapWidthRemaining) {
								$newtext .= $word;
								if ($addspace != 0) {
									$newtext .= " ";
								}
							} else {
								$lw = $html->GetStringWidth($word . " ");
								$newtext .= "\n$word";
								if ($addspace != 0) {
									$newtext .= " ";
								}
								// Reset the wrap width to the cell width
								$wrapWidthRemaining = $this->wrapWidthCell;
							}
						}
					} else {
						$newtext .= $line;
					}
					// Check the Line Feed counter
					if ($lfct > 1) {
						// Add a new line feed as long as it’s not the last line
						$newtext .= "\n";
						// Reset the line width
						$lw = 0;
						// Reset the wrap width to the cell width
						$wrapWidthRemaining = $this->wrapWidthCell;
					}
					$lfct--;
				}
				$this->text = $newtext;
				$lfct = substr_count($this->text, "\n");
				return array($lw, 1, $lfct);
			}
		}
		$l = 0;
		$lfct = substr_count($this->text, "\n");
		if ($lfct > 0) {
			$l = 2;
		}
		return array($lw, $l, $lfct);
	}
}
