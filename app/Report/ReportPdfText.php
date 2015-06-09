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
 * Class ReportPdfText
 */
class ReportPdfText extends ReportBaseText {
	/**
	 * PDF Text renderer
	 *
	 * @param ReportTcpdf $renderer
	 */
	public function render($renderer) {
		// Set up the style
		if ($renderer->getCurrentStyle() != $this->styleName) {
			$renderer->setCurrentStyle($this->styleName);
		}
		$temptext = str_replace("#PAGENUM#", $renderer->PageNo(), $this->text);
		// underline «title» part of Source item
		$temptext = str_replace(array('«', '»'), array('<u>', '</u>'), $temptext);

		// Paint the text color or they might use inherited colors by the previous function
		$match = array();
		if (preg_match("/#?(..)(..)(..)/", $this->color, $match)) {
			$r = hexdec($match[1]);
			$g = hexdec($match[2]);
			$b = hexdec($match[3]);
			$renderer->SetTextColor($r, $g, $b);
		} else {
			$renderer->SetTextColor(0, 0, 0);
		}
		$temptext = FunctionsRtl::spanLtrRtl($temptext, "BOTH");
		$temptext = str_replace(
			array('<br><span dir="rtl" >', '<br><span dir="ltr" >', '> ', ' <'),
			array('<span dir="rtl" ><br>', '<span dir="ltr" ><br>', '>&nbsp;', '&nbsp;<'),
			$temptext
		);
		$renderer->writeHTML(
			$temptext,
			false,
			false,
			true,
			false,
			""
		); //change height - line break etc. - the form is mirror on rtl pages
		// Reset the text color to black or it will be inherited
		$renderer->SetTextColor(0, 0, 0);
	}

	/**
	 * Returns the height in points of the text element
	 *
	 * The height is already calculated in getWidth()
	 *
	 * @param ReportTcpdf $pdf
	 *
	 * @return float 0
	 */
	public function getHeight($pdf) {
		return 0;
	}

	/**
	 * Splits the text into lines if necessary to fit into a giving cell
	 *
	 * @param ReportTcpdf $pdf
	 *
	 * @return array
	 */
	public function getWidth($pdf) {
		// Setup the style name, a font must be selected to calculate the width
		if ($pdf->getCurrentStyle() != $this->styleName) {
			$pdf->setCurrentStyle($this->styleName);
		}
		// Check for the largest font size in the box
		$fsize = $pdf->getCurrentStyleHeight();
		if ($fsize > $pdf->largestFontHeight) {
			$pdf->largestFontHeight = $fsize;
		}

		// Get the line width
		$lw = $pdf->GetStringWidth($this->text);
		// Line Feed counter - Number of lines in the text
		$lfct = substr_count($this->text, "\n") + 1;
		// If there is still remaining wrap width...
		if ($this->wrapWidthRemaining > 0) {
			// Check with line counter too!
			// but floor the $wrapWidthRemaining first to keep it bugfree!
			$wrapWidthRemaining = (int) ($this->wrapWidthRemaining);
			if ($lw >= $wrapWidthRemaining || $lfct > 1) {
				$newtext = "";
				$lines   = explode("\n", $this->text);
				// Go throught the text line by line
				foreach ($lines as $line) {
					// Line width in points + a little margin
					$lw = $pdf->GetStringWidth($line);
					// If the line has to be wraped
					if ($lw >= $wrapWidthRemaining) {
						$words    = explode(" ", $line);
						$addspace = count($words);
						$lw       = 0;
						foreach ($words as $word) {
							$addspace--;
							$lw += $pdf->GetStringWidth($word . " ");
							if ($lw <= $wrapWidthRemaining) {
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
						// Add a new line as long as it’s not the last line
						$newtext .= "\n";
						// Reset the line width
						$lw = 0;
						// Reset the wrap width to the cell width
						$wrapWidthRemaining = $this->wrapWidthCell;
					}
					$lfct--;
				}
				$this->text = $newtext;
				$lfct       = substr_count($this->text, "\n");

				return array($lw, 1, $lfct);
			}
		}
		$l    = 0;
		$lfct = substr_count($this->text, "\n");
		if ($lfct > 0) {
			$l = 2;
		}

		return array($lw, $l, $lfct);
	}
}
