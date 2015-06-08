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

/**
 * class ReportHtmlFootnote
 */
class ReportHtmlFootnote extends ReportBaseFootnote {
	/**
	 * HTML Footnotes number renderer
	 *
	 * @param ReportHtml $renderer
	 */
	public function render($renderer) {
		$renderer->setCurrentStyle("footnotenum");
		echo "<a href=\"#footnote", $this->num, "\"><sup>";
		$renderer->write($renderer->entityRTL . $this->num);
		echo "</sup></a>\n";
	}

	/**
	 * Write the Footnote text
	 * Uses style name "footnote" by default
	 *
	 * @param ReportHtml $html
	 */
	public function renderFootnote($html) {

		if ($html->getCurrentStyle() != $this->styleName) {
			$html->setCurrentStyle($this->styleName);
		}

		$temptext = str_replace("#PAGENUM#", $html->pageNo(), $this->text);
		// underline «title» part of Source item
		$temptext = str_replace(array('«', '»'), array('<u>', '</u>'), $temptext);
		echo "\n<div><a name=\"footnote", $this->num, "\"></a>";
		$html->write($this->num . ". " . $temptext);
		echo "</div>";

		$html->setXy(0, $html->getY() + $this->getFootnoteHeight($html));
	}

	/**
	 * Calculates the Footnotes height
	 *
	 * @param ReportHtml $html
	 * @param int        $cellWidth The width of the cell to use it for text wraping
	 *
	 * @return int       Footnote height in points
	 */
	public function getFootnoteHeight($html, $cellWidth = 0) {
		if ($html->getCurrentStyle() != $this->styleName) {
			$html->setCurrentStyle($this->styleName);
		}

		if ($cellWidth > 0) {
			$this->text = $html->textWrap($this->text, $cellWidth);
		}
		$this->text = $this->text . "\n\n";
		$ct         = substr_count($this->text, "\n");
		$fsize      = $html->getCurrentStyleHeight();

		return ($fsize * $ct) * $html->cellHeightRatio;
	}

	/**
	 * Get the width of text
	 * Breaks up a text into lines if needed
	 *
	 * @param ReportHtml $html
	 *
	 * @return array
	 */
	public function getWidth($html) {
		// Setup the style name
		$html->setCurrentStyle("footnotenum");

		// Check for the largest font size in the box
		$fsize = $html->getCurrentStyleHeight();
		if ($fsize > $html->largestFontHeight) {
			$html->largestFontHeight = $fsize;
		}

		// Returns the Object if already numbered else false
		if (empty($this->num)) {
			$html->checkFootnote($this);
		}

		// Get the line width for the text in points + a little margin
		$lw = $html->GetStringWidth($this->numText);
		// Line Feed counter - Number of lines in the text
		$lfct = $html->countLines($this->numText);
		// If there is still remaining wrap width...
		if ($this->wrapWidthRemaining > 0) {
			// Check with line counter too!
			if ($lw >= $this->wrapWidthRemaining || $lfct > 1) {
				$newtext            = "";
				$wrapWidthRemaining = $this->wrapWidthRemaining;
				$lines              = explode("\n", $this->numText);
				// Go throught the text line by line
				foreach ($lines as $line) {
					// Line width in points + a little margin
					$lw = $html->GetStringWidth($line);
					// If the line has to be wraped
					if ($lw > $wrapWidthRemaining) {
						$words    = explode(" ", $line);
						$addspace = count($words);
						$lw       = 0;
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
				$this->numText = $newtext;
				$lfct          = substr_count($this->numText, "\n");

				return array($lw, 1, $lfct);
			}
		}
		$l    = 0;
		$lfct = substr_count($this->numText, "\n");
		if ($lfct > 0) {
			$l = 2;
		}

		return array($lw, $l, $lfct);
	}
}
