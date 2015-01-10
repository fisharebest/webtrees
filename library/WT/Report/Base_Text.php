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
 * Class WT_Report_Base_Text - Base Report Generator, used by the SAX
 * parser to generate reports from the XML report file.
 */
class WT_Report_Base_Text extends WT_Report_Base_Element {
	/**
	 * Text color in HTML code
	 *
	 * @var string
	 */
	public $color;
	/**
	 * Style name
	 *
	 * @var string
	 */
	public $styleName;
	/**
	 * Remaining width of a cel
	 *
	 * @var integer User unit (points)
	 */
	public $wrapWidthRemaining;
	/**
	 * Original width of a cell
	 *
	 * @var integer User unit (points)
	 */
	public $wrapWidthCell;

	/**
	 * Create a Text class - Base
	 *
	 * @param string $style The name of the text style
	 * @param string $color HTML color code
	 */
	function __construct($style, $color) {
		$this->text = '';
		$this->color = $color;
		$this->wrapWidthRemaining = 0;
		$this->styleName = $style;

		return 0;
	}

	/**
	 * @param $wrapwidth
	 * @param $cellwidth
	 *
	 * @return mixed
	 */
	function setWrapWidth($wrapwidth, $cellwidth) {
		$this->wrapWidthCell = $cellwidth;
		if (strpos($this->text, "\n") !== false) {
			$this->wrapWidthRemaining = $cellwidth;
		} else {
			$this->wrapWidthRemaining = $wrapwidth;
		}

		return $this->wrapWidthRemaining;
	}

	/**
	 * @return string
	 */
	function getStyleName() {
		return $this->styleName;
	}
}
