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

/**
 * Class ReportBaseElement
 */
class ReportBaseElement {
	/**
	 * @var string
	 */
	public $text = "";

	/**
	 * Element renderer
	 *
	  *@param ReportHtml|ReportPdf $renderer
	 *
	 * @return void
	 */
	function render($renderer) {
		//-- to be implemented in inherited classes
	}

	/**
	 * @param ReportHtml|ReportPdf $renderer



*
*@return float
	 */
	function getHeight($renderer) {
		return 0.0;
	}

	/**
	 * @param ReportHtml|ReportPdf $renderer



*
*@return float
	 */
	function getWidth($renderer) {
		return 0.0;
	}

	/**
	 * @param string $t
	 *
	 * @return integer
	 */
	function addText($t) {
		global $wt_report, $reportTitle, $reportDescription;

		$t = trim($t, "\r\n\t");
		$t = str_replace(array("<br>", "&nbsp;"), array("\n", " "), $t);
		$t = strip_tags($t);
		$t = htmlspecialchars_decode($t);
		$this->text .= $t;

		// Adding the title and description to the Document Properties
		if ($reportTitle) {
			$wt_report->addTitle($t);
		} elseif ($reportDescription) {
			$wt_report->addDescription($t);
		}

		return 0;
	}

	/**
	 * @return integer
	 */
	function addNewline() {
		$this->text .= "\n";

		return 0;
	}

	/**
	 * @return string
	 */
	function getValue() {
		return $this->text;
	}

	/**
	 * @param $wrapwidth
	 * @param $cellwidth
	 *
	 * @return integer
	 */
	function setWrapWidth($wrapwidth, $cellwidth) {
		return 0;
	}

	/**
	 * @param $renderer
	 *
	 * @return void
	 */
	function renderFootnote($renderer) {
		// To be implemented in inherited classes
	}

	/**
	 * @param $text
	 *
	 * @return void
	 */
	function setText($text) {
		$this->text = $text;
	}
}
