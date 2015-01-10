<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

/**
 * Class WT_Report_Base_Line - Base Report Generator, used by the SAX
 * parser to generate reports from the XML report file.
 */
class WT_Report_Base_Line extends WT_Report_Base_Element {
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
