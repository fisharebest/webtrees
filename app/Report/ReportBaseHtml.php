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
 * Class ReportBaseHtml
 */
class ReportBaseHtml extends ReportBaseElement {
	public $tag;
	public $attrs;
	public $elements = array();

	/**
	 * @param $tag
	 * @param $attrs
	 */
	function __construct($tag, $attrs) {
		$this->tag = $tag;
		$this->attrs = $attrs;

		return 0;
	}

	/**
	 * @return string
	 */
	function getStart() {
		$str = "<" . $this->tag . " ";
		foreach ($this->attrs as $key => $value) {
			$str .= $key . "=\"" . $value . "\" ";
		}
		$str .= ">";

		return $str;
	}

	/**
	 * @return string
	 */
	function getEnd() {
		return "</" . $this->tag . ">";
	}

	/**
	 * @param $element
	 *
	 * @return integer
	 */
	function addElement($element) {
		$this->elements[] = $element;

		return 0;
	}
}
