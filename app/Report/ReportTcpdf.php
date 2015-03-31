<?php
namespace Fisharebest\Webtrees;

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

	/** @var integer The last cell height */
	public $lastCellHeight = 0;

	/** @var integer The largest font size within a TextBox to calculate the height */
	public $largestFontHeight = 0;

	/** @var integer The last pictures page number */
	public $lastpicpage = 0;

	public $wt_report;

	/**
	 * PDF Header -PDF
	 */
	function header() {
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
	function body() {
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
	function footnotes() {
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
	function footer() {
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
	 * @return integer The number of the Header elements
	 */
	function addHeader($element) {
		$this->headerElements[] = $element;

		return count($this->headerElements) - 1;
	}

	/**
	 * Add an element to the Page Header -PDF
	 *
	 * @param object|string $element
	 *
	 * @return integer The number of the Page Header elements
	 */
	function addPageHeader($element) {
		$this->pageHeaderElements[] = $element;

		return count($this->pageHeaderElements) - 1;
	}

	/**
	 * Add an element to the Body -PDF
	 *
	 * @param object|string $element
	 *
	 * @return integer The number of the Body elements
	 */
	function addBody($element) {
		$this->bodyElements[] = $element;

		return count($this->bodyElements) - 1;
	}

	/**
	 * Add an element to the Footer -PDF
	 *
	 * @param object|string $element
	 *
	 * @return integer The number of the Footer elements
	 */
	function addFooter($element) {
		$this->footerElements[] = $element;

		return count($this->footerElements) - 1;
	}

	/**
	 * @param $index
	 */
	function removeHeader($index) {
		unset($this->headerElements[$index]);
	}

	/**
	 * @param $index
	 */
	function removePageHeader($index) {
		unset($this->pageHeaderElements[$index]);
	}

	/**
	 * @param $index
	 */
	function removeBody($index) {
		unset($this->bodyElements[$index]);
	}

	/**
	 * @param $index
	 */
	function removeFooter($index) {
		unset($this->footerElements[$index]);
	}

	/**
	 * Clear the Header -PDF
	 */
	function clearHeader() {
		unset($this->headerElements);
		$this->headerElements = array();
	}

	/**
	 * Clear the Page Header -PDF
	 */
	function clearPageHeader() {
		unset($this->pageHeaderElements);
		$this->pageHeaderElements = array();
	}

	/**
	 * @param $r
	 */
	function setReport($r) {
		$this->wt_report = $r;
	}

	/**
	 * Get the currently used style name -PDF
	 *
	 * @return string
	 */
	function getCurrentStyle() {
		return $this->currentStyle;
	}

	/**
	 * Setup a style for usage -PDF
	 *
	 * @param string $s Style name
	 */
	function setCurrentStyle($s) {
		$this->currentStyle = $s;
		$style = $this->wt_report->getStyle($s);
		$this->SetFont($style['font'], $style['style'], $style['size']);
	}

	/**
	 * Get the style -PDF
	 *
	 * @param string $s Style name
	 *
	 * @return array
	 */
	function getStyle($s) {
		if (!isset($this->wt_report->Styles[$s])) {
			$s = $this->getCurrentStyle();
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
	function addMarginX($x) {
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
	function getMaxLineWidth() {
		$m = $this->getMargins();
		if ($this->getRTL()) {
			return ($this->getRemainingWidth() + $m['right']);
		} else {
			return ($this->getRemainingWidth() + $m['left']);
		}
	}

	/**
	 * @return integer
	 */
	function getFootnotesHeight() {
		$h = 0;
		foreach ($this->printedfootnotes as $element) {
			$h += $element->getHeight($this);
		}

		return $h;
	}

	/**
	 * Returns the the current font size height -PDF
	 *
	 * @return integer
	 */
	function getCurrentStyleHeight() {
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
	 * @return boolean false if not numbered befor | object if already numbered
	 */
	function checkFootnote($footnote) {
		$ct = count($this->printedfootnotes);
		$val = $footnote->getValue();
		$i = 0;
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
	function newPage() {
		if ($this->lastpicpage > $this->getPage()) {
			$this->setPage($this->lastpicpage);
		}
		$this->AddPage();
	}


	/*******************************************
	 * TCPDF protected functions
	 *******************************************/

	/**
	 * Add a page if needed -PDF
	 *
	 * @param integer $height Cell height
	 *
	 * @return boolean true in case of page break, false otherwise
	 */
	function checkPageBreakPDF($height) {
		return $this->checkPageBreak($height);
	}

	/**
	 * Returns the remaining width between the current position and margins -PDF
	 *
	 * @return float Remaining width
	 */
	function getRemainingWidthPDF() {
		return $this->getRemainingWidth();
	}
}
