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
 * Class ReportBaseLine
 */
class ReportBaseLine extends ReportBaseElement {
	/**
	 * Start horizontal position, current position (default)
	 *
	 * @var mixed
	 */
	public $x1 = ".";
	/**
	 * Start vertical position, current position (default)
	 *
	 * @var mixed
	 */
	public $y1 = ".";
	/**
	 * End horizontal position, maximum width (default)
	 *
	 * @var mixed
	 */
	public $x2 = ".";
	/**
	 * End vertical position
	 *
	 * @var mixed
	 */
	public $y2 = ".";

	/**
	 * Create a line class - Base
	 *
	 * @param mixed $x1
	 * @param mixed $y1
	 * @param mixed $x2
	 * @param mixed $y2
	 */
	function __construct($x1, $y1, $x2, $y2) {
		$this->x1 = $x1;
		$this->y1 = $y1;
		$this->x2 = $x2;
		$this->y2 = $y2;

		return 0;
	}

	/**
	 * @param $renderer
	 *
	 * @return number
	 */
	function getHeight($renderer) {
		return abs($this->y2 - $this->y1);
	}

	/**
	 * @param $renderer
	 *
	 * @return number
	 */
	function getWidth($renderer) {
		return abs($this->x2 - $this->x1);
	}
}
