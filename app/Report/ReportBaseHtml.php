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
 * Class ReportBaseHtml
 */
class ReportBaseHtml extends ReportBaseElement {
	/** @var string The XML tag. */
	public $tag;

	/** @var string[] Attributes of the XML tag. */
	public $attrs;

	/** @var ReportBaseElement[] A list of elements. */
	public $elements = array();

	/**
	 * Create an element.
	 *
	 * @param $tag
	 * @param $attrs
	 */
	public function __construct($tag, $attrs) {
		$this->tag   = $tag;
		$this->attrs = $attrs;

		return 0;
	}

	/**
	 * Get the start tag.
	 *
	 * @return string
	 */
	public function getStart() {
		$str = "<" . $this->tag . " ";
		foreach ($this->attrs as $key => $value) {
			$str .= $key . "=\"" . $value . "\" ";
		}
		$str .= ">";

		return $str;
	}

	/**
	 * Get the end tag.
	 *
	 * @return string
	 */
	public function getEnd() {
		return "</" . $this->tag . ">";
	}

	/**
	 * Add an element.
	 *
	 * @param ReportBaseElement $element
	 */
	public function addElement($element) {
		$this->elements[] = $element;
	}
}
