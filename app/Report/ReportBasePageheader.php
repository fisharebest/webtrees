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
 * Class ReportBasePageheader
 */
class ReportBasePageheader extends ReportBaseElement {
	/** @var ReportBaseElement[] Elements */
	public $elements = array();

	/**
	 * Create a page header
	 */
	public function __construct() {
		$this->elements = array();

		return 0;
	}

	/**
	 * Unknown?
	 *
	 * @return int
	 */
	public function textBox() {
		$this->elements = array();

		return 0;
	}

	/**
	 * Add element - PageHeader
	 *
	 * @param ReportBaseElement $element
	 */
	public function addElement($element) {
		$this->elements[] = $element;
	}
}
