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
 * Class WT_Report_PDF_Footnote - Base Report Generator, used by the SAX
 * parser to generate reports from the XML report file.
 */
class WT_Report_PDF_Footnote extends WT_Report_Base_Footnote {
	/**
	 * PDF Footnotes number renderer
	 *
	 * @param PDF $renderer
	 *
	 * @return void
	 */
	function render($renderer) {
		$renderer->setCurrentStyle("footnotenum");
		$renderer->Write($renderer->getCurrentStyleHeight(), $this->numText, $this->addlink); //source link numbers after name
	}

	/**
	 * Write the Footnote text
	 * Uses style name "footnote" by default
	 *
	 * @param PDF $pdf
	 *
	 * @return void
	 */
	function renderFootnote($pdf) {
		if ($pdf->getCurrentStyle() != $this->styleName) {
			$pdf->setCurrentStyle($this->styleName);
		}
		$temptext = str_replace("#PAGENUM#", $pdf->PageNo(), $this->text);
		// Set the link to this y/page position
		$pdf->SetLink($this->addlink, -1, -1);
		// Print first the source number
		// working
		if ($pdf->getRTL()) {
			$pdf->writeHTML("<span> ." . $this->num . "</span>", false, false, false, false, "");
		} else {
			$temptext = "<span>" . $this->num . ". </span>" . $temptext;
		}
		// underline «title» part of Source item
		$temptext = str_replace(array('«', '»'), array('<u>', '</u>'), $temptext);
		$pdf->writeHTML($temptext, true, false, true, false, '');
	}

	/**
	 * Returns the height in points of the Footnote element
	 *
	 * @param PDF $renderer
	 *
	 * @return float $h
	 */
	function getFootnoteHeight($renderer) {
		return 0;
	}

	/**
	 * Splits the text into lines to fit into a giving cell
	 * and returns the last lines width
	 *
	 * @param PDF $pdf
	 *
	 * @return array
	 */
	function getWidth($pdf) {
		// Setup the style name, a font must be selected to calculate the width
		$pdf->setCurrentStyle("footnotenum");

		// Check for the largest font size in the box
		$fsize = $pdf->getCurrentStyleHeight();
		if ($fsize > $pdf->largestFontHeight) {
			$pdf->largestFontHeight = $fsize;
		}

		// Returns the Object if already numbered else false
		if (empty($this->num)) {
			$pdf->checkFootnote($this);
		}

		// Get the line width
		$lw = ceil($pdf->GetStringWidth($this->numText));
		// Line Feed counter - Number of lines in the text
		$lfct = substr_count($this->numText, "\n") + 1;
		// If there is still remaining wrap width...
		if ($this->wrapWidthRemaining > 0) {
			// Check with line counter too!
			// but floor the $wrapWidthRemaining first to keep it bugfree!
			$wrapWidthRemaining = (int)($this->wrapWidthRemaining);
			if (($lw >= $wrapWidthRemaining) or ($lfct > 1)) {
				$newtext = "";
				$lines = explode("\n", $this->numText);
				// Go throught the text line by line
				foreach ($lines as $line) {
					// Line width in points
					$lw = ceil($pdf->GetStringWidth($line));
					// If the line has to be wraped
					if ($lw >= $wrapWidthRemaining) {
						$words = explode(" ", $line);
						$addspace = count($words);
						$lw = 0;
						foreach ($words as $word) {
							$addspace--;
							$lw += ceil($pdf->GetStringWidth($word . " "));
							if ($lw < $wrapWidthRemaining) {
								$newtext .= $word;
								if ($addspace != 0) {
									$newtext .= " ";
								}
							} else {
								$lw = $pdf->GetStringWidth($word . " ");
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
				$this->numText = $newtext;
				$lfct = substr_count($this->numText, "\n");

				return array($lw, 1, $lfct);
			}
		}
		$l = 0;
		$lfct = substr_count($this->numText, "\n");
		if ($lfct > 0) {
			$l = 2;
		}

		return array($lw, $l, $lfct);
	}
}
