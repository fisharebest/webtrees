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

use TCPDF;

/**
 * WT Report PDF Class
 *
 * This class inherits from the TCPDF class and is used to generate the PDF document
 */
class ReportTcpdf extends TCPDF {
	/** @var ReportBaseElement[] Array of elements in the header */
	public $headerElements = array();

	/** @var ReportBaseElement[] Array of elements in the page header */
	public $pageHeaderElements = array();

	/** @var ReportBaseElement[] Array of elements in the footer */
	public $footerElements = array();

	/** @var ReportBaseElement[] Array of elements in the body */
	public $bodyElements = array();

	/** @var ReportBaseFootnote[] Array of elements in the footer notes */
	public $printedfootnotes = array();

	/** @var string Currently used style name */
	public $currentStyle;

	/** @var int The last cell height */
	public $lastCellHeight = 0;

	/** @var int The largest font size within a TextBox to calculate the height */
	public $largestFontHeight = 0;

	/** @var int The last pictures page number */
	public $lastpicpage = 0;

	/** @var ReportBase The current report. */
	public $wt_report;

	/**
	 * PDF Header -PDF
	 */
	public function header() {
		foreach ($this->headerElements as $element) {
			if (is_object($element)) {
				$element->render($this);
			} elseif (is_string($element) && $element == "footnotetexts") {
				$this->footnotes();
			} elseif (is_string($element) && $element == "addpage") {
				$this->newPage();
			}
		}
		foreach ($this->pageHeaderElements as $element) {
			if (is_object($element)) {
				$element->render($this);
			} elseif (is_string($element) && $element == "footnotetexts") {
				$this->footnotes();
			} elseif (is_string($element) && $element == "addpage") {
				$this->newPage();
			}
		}
	}

	/**
	 * PDF Body -PDF
	 */
	public function body() {
		$this->AddPage();
		foreach ($this->bodyElements as $key => $element) {
			if (is_object($element)) {
				$element->render($this);
			} elseif (is_string($element) && $element == "footnotetexts") {
				$this->footnotes();
			} elseif (is_string($element) && $element == "addpage") {
				$this->newPage();
			}
			// Delete used elements in hope to reduce 'some' memory usage
			unset($this->bodyElements[$key]);
		}
	}

	/**
	 * PDF Footnotes -PDF
	 */
	public function footnotes() {
		foreach ($this->printedfootnotes as $element) {
			if (($this->GetY() + $element->getFootnoteHeight($this)) > $this->getPageHeight()) {
				$this->AddPage();
			}
			$element->renderFootnote($this);
			if ($this->GetY() > $this->getPageHeight()) {
				$this->AddPage();
			}
		}
	}

	/**
	 * PDF Footer -PDF
	 */
	public function footer() {
		foreach ($this->footerElements as $element) {
			if (is_object($element)) {
				$element->render($this);
			} elseif (is_string($element) && $element == "footnotetexts") {
				$this->footnotes();
			} elseif (is_string($element) && $element == "addpage") {
				$this->newPage();
			}
		}
	}

	/**
	 * Add an element to the Header -PDF
	 *
	 * @param object|string $element
	 *
	 * @return int The number of the Header elements
	 */
	public function addHeader($element) {
		$this->headerElements[] = $element;

		return count($this->headerElements) - 1;
	}

	/**
	 * Add an element to the Page Header -PDF
	 *
	 * @param object|string $element
	 *
	 * @return int The number of the Page Header elements
	 */
	public function addPageHeader($element) {
		$this->pageHeaderElements[] = $element;

		return count($this->pageHeaderElements) - 1;
	}

	/**
	 * Add an element to the Body -PDF
	 *
	 * @param object|string $element
	 *
	 * @return int The number of the Body elements
	 */
	public function addBody($element) {
		$this->bodyElements[] = $element;

		return count($this->bodyElements) - 1;
	}

	/**
	 * Add an element to the Footer -PDF
	 *
	 * @param object|string $element
	 *
	 * @return int The number of the Footer elements
	 */
	public function addFooter($element) {
		$this->footerElements[] = $element;

		return count($this->footerElements) - 1;
	}

	/**
	 * Remove the header.
	 *
	 * @param $index
	 */
	public function removeHeader($index) {
		unset($this->headerElements[$index]);
	}

	/**
	 * Remove the page header.
	 *
	 * @param $index
	 */
	public function removePageHeader($index) {
		unset($this->pageHeaderElements[$index]);
	}

	/**
	 * Remove the body.
	 *
	 * @param $index
	 */
	public function removeBody($index) {
		unset($this->bodyElements[$index]);
	}

	/**
	 * Remove the footer.
	 *
	 * @param $index
	 */
	public function removeFooter($index) {
		unset($this->footerElements[$index]);
	}

	/**
	 * Clear the Header -PDF
	 */
	public function clearHeader() {
		unset($this->headerElements);
		$this->headerElements = array();
	}

	/**
	 * Clear the Page Header -PDF
	 */
	public function clearPageHeader() {
		unset($this->pageHeaderElements);
		$this->pageHeaderElements = array();
	}

	/**
	 * Set the report.
	 *
	 * @param $r
	 */
	public function setReport($r) {
		$this->wt_report = $r;
	}

	/**
	 * Get the currently used style name -PDF
	 *
	 * @return string
	 */
	public function getCurrentStyle() {
		return $this->currentStyle;
	}

	/**
	 * Setup a style for usage -PDF
	 *
	 * @param string $s Style name
	 */
	public function setCurrentStyle($s) {
		$this->currentStyle = $s;
		$style              = $this->wt_report->getStyle($s);
		$this->SetFont($style['font'], $style['style'], $style['size']);
	}

	/**
	 * Get the style -PDF
	 *
	 * @param string $s Style name
	 *
	 * @return array
	 */
	public function getStyle($s) {
		if (!isset($this->wt_report->Styles[$s])) {
			$s                           = $this->getCurrentStyle();
			$this->wt_report->Styles[$s] = $s;
		}

		return $this->wt_report->Styles[$s];
	}

	/**
	 * Add margin when static horizontal position is used -PDF
	 * RTL supported
	 *
	 * @param float $x Static position
	 *
	 * @return float
	 */
	public function addMarginX($x) {
		$m = $this->getMargins();
		if ($this->getRTL()) {
			$x += $m['right'];
		} else {
			$x += $m['left'];
		}
		$this->SetX($x);

		return $x;
	}

	/**
	 * Get the maximum line width to draw from the curren position -PDF
	 * RTL supported
	 *
	 * @return float
	 */
	public function getMaxLineWidth() {
		$m = $this->getMargins();
		if ($this->getRTL()) {
			return ($this->getRemainingWidth() + $m['right']);
		} else {
			return ($this->getRemainingWidth() + $m['left']);
		}
	}

	/**
	 * Get the height of the footnote.
	 *
	 * @return int
	 */
	public function getFootnotesHeight() {
		$h = 0;
		foreach ($this->printedfootnotes as $element) {
			$h += $element->getHeight($this);
		}

		return $h;
	}

	/**
	 * Returns the the current font size height -PDF
	 *
	 * @return int
	 */
	public function getCurrentStyleHeight() {
		if (empty($this->currentStyle)) {
			return $this->wt_report->defaultFontSize;
		}
		$style = $this->wt_report->getStyle($this->currentStyle);

		return $style['size'];
	}

	/**
	 * Checks the Footnote and numbers them
	 *
	 * @param object $footnote
	 *
	 * @return bool false if not numbered befor | object if already numbered
	 */
	public function checkFootnote($footnote) {
		$ct  = count($this->printedfootnotes);
		$val = $footnote->getValue();
		$i   = 0;
		while ($i < $ct) {
			if ($this->printedfootnotes[$i]->getValue() == $val) {
				// If this footnote already exist then set up the numbers for this object
				$footnote->setNum($i + 1);
				$footnote->setAddlink($i + 1);

				return $this->printedfootnotes[$i];
			}
			$i++;
		}
		// If this Footnote has not been set up yet
		$footnote->setNum($ct + 1);
		$footnote->setAddlink($this->AddLink());
		$this->printedfootnotes[] = $footnote;

		return false;
	}

	/**
	 * Used this function instead of AddPage()
	 * This function will make sure that images will not be overwritten
	 */
	public function newPage() {
		if ($this->lastpicpage > $this->getPage()) {
			$this->setPage($this->lastpicpage);
		}
		$this->AddPage();
	}

	/**
	 * Add a page if needed -PDF
	 *
	 * @param int $height Cell height
	 *
	 * @return bool true in case of page break, false otherwise
	 */
	public function checkPageBreakPDF($height) {
		return $this->checkPageBreak($height);
	}

	/**
	 * Returns the remaining width between the current position and margins -PDF
	 *
	 * @return float Remaining width
	 */
	public function getRemainingWidthPDF() {
		return $this->getRemainingWidth();
	}
}
