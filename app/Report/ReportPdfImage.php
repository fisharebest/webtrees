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
 * Class ReportPdfImage
 */
class ReportPdfImage extends ReportBaseImage {
	/**
	 * PDF image renderer
	 *
	 * @param ReportTcpdf $renderer
	 */
	public function render($renderer) {
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
		$lastpicpage           = $renderer->PageNo();
		$renderer->lastpicpage = $renderer->getPage();
		$lastpicleft           = $this->x;
		$lastpicright          = $this->x + $this->width;
		$lastpicbottom         = $this->y + $this->height;
		// Setup for the next line
		if ($this->line == "N") {
			$renderer->SetY($lastpicbottom);
		}
	}

	/**
	 * Get the image height
	 *
	 * @param ReportTcpdf $pdf
	 *
	 * @return float
	 */
	public function getHeight($pdf) {
		return $this->height;
	}

	/**
	 * Get the image width.
	 *
	 * @param $pdf
	 *
	 * @return float
	 */
	public function getWidth($pdf) {
		return $this->width;
	}
}
