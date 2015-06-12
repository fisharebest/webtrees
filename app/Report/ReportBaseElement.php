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
 * Class ReportBaseElement
 */
class ReportBaseElement {
	/** @var string Text*/
	public $text = '';

	/**
	 * Element renderer
	 *
	 * @param ReportHtml|ReportTcpdf $renderer
	 */
	public function render($renderer) {
		//-- to be implemented in inherited classes
	}

	/**
	 * Get the height.
	 *
	 * @param ReportHtml|ReportTcpdf $renderer
	 *
	 * @return float
	 */
	public function getHeight($renderer) {
		return 0.0;
	}

	/**
	 * Get the width.
	 *
	 * @param ReportHtml|ReportTcpdf $renderer
	 *
	 * @return float
	 */
	public function getWidth($renderer) {
		return 0.0;
	}

	/**
	 * Add text.
	 *
	 * @param string $t
	 *
	 * @return int
	 */
	public function addText($t) {
		$t = trim($t, "\r\n\t");
		$t = str_replace(array("<br>", "&nbsp;"), array("\n", " "), $t);
		$t = strip_tags($t);
		$t = htmlspecialchars_decode($t);
		$this->text .= $t;

		return 0;
	}

	/**
	 * Add an end-of-line.
	 *
	 * @return int
	 */
	public function addNewline() {
		$this->text .= "\n";

		return 0;
	}

	/**
	 * Get the current text.
	 *
	 * @return string
	 */
	public function getValue() {
		return $this->text;
	}

	/**
	 * Set the width to wrap text.
	 *
	 * @param $wrapwidth
	 * @param $cellwidth
	 *
	 * @return int
	 */
	public function setWrapWidth($wrapwidth, $cellwidth) {
		return 0;
	}

	/**
	 * Render the footnotes.
	 *
	 * @param $renderer
	 */
	public function renderFootnote($renderer) {
	}

	/**
	 * Set the text.
	 *
	 * @param $text
	 */
	public function setText($text) {
		$this->text = $text;
	}
}
