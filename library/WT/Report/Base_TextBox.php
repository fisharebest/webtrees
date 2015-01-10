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
 * Class WT_Report_Base_TextBox - Base Report Generator, used by the SAX
 * parser to generate reports from the XML report file.
 */
class WT_Report_Base_TextBox extends WT_Report_Base_Element {
	/**
	 * Array of elements in the TextBox
	 *
	 * @var array
	 */
	public $elements = array();

	/**
	 *  Background color in HTML code
	 *
	 * @var string
	 */
	public $bgcolor;
	/**
	 * Whether or not paint the background
	 *
	 * @var boolean
	 */
	public $fill;

	/**
	 * Position the left corner of this box on the page(expressed in points). The default is the current position.
	 *
	 * @var mixed
	 */
	public $left;
	/**
	 * Position the top corner of this box on the page(expressed in points). the default is the current position
	 *
	 * @var mixed
	 */
	public $top;
	/**
	 * After this box is finished rendering, should the next section of text start immediately after the this box or should it start on a new line under this box. 0 = no new line, 1 = force new line. Default is 0
	 *
	 * @var boolean
	 */
	public $newline;

	/**
	 * @var boolean
	 */
	public $pagecheck;

	/**
	 * Whether or not a border should be printed around this box. 0 = no border, 1 = border. Default is 0
	 *
	 * @var boolean
	 */
	public $border;
	/**
	 * Style of rendering
	 *
	 * <ul>
	 * <li>D or empty string: Draw (default).</li>
	 * <li>F: Fill.</li>
	 * <li>DF or FD: Draw and fill.</li>
	 * <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
	 *<li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
	 * </ul>
	 *
	 * @var string
	 */
	public $style;

	/**
	 * @var array $borderstyle Border style of rectangle. Array with keys among the following:
	 * <ul>
	 * <li>all: Line style of all borders. Array like for {@link SetLineStyle SetLineStyle}.</li>
	 * <li>L, T, R, B or combinations: Line style of left, top, right or bottom border. Array like for {@link SetLineStyle SetLineStyle}.</li>
	 * </ul>
	 * Not yet in use
	 * var $borderstyle;
	 */

	/**
	 * The starting height of this cell. If the text wraps the height will automatically be adjusted
	 *
	 * @var float
	 */
	public $height;
	/**
	 * Setting the width to 0 will make it the width from the current location to the right margin
	 *
	 * @var float
	 */
	public $width;
	/**
	 * Use cell padding or not
	 *
	 * @var boolean $padding
	 */
	public $padding;
	/**
	 * Resets this box last height after it’s done
	 */
	public $reseth;

	/**
	 * TextBox - Element - Base
	 *
	 * @param float   $width   Text box width
	 * @param float   $height  Text box height
	 * @param boolean $border
	 * @param string  $bgcolor Background color code in HTML
	 * @param boolean $newline
	 * @param mixed   $left
	 * @param mixed   $top
	 * @param boolean $pagecheck
	 * @param string  $style
	 * @param boolean $fill
	 * @param boolean $padding
	 * @param boolean $reseth
	 */
	function __construct(
		$width, $height, $border, $bgcolor, $newline, $left, $top, $pagecheck, $style, $fill, $padding, $reseth
	) {
		$this->border = $border;
		$this->bgcolor = $bgcolor;
		$this->fill = $fill;
		$this->height = $height;
		$this->left = $left;
		$this->newline = $newline;
		$this->pagecheck = $pagecheck;
		$this->style = $style;
		$this->top = $top;
		$this->width = $width;
		$this->padding = $padding;
		$this->reseth = $reseth;

		return 0;
	}

	/**
	 * Add an element to the TextBox
	 *
	 * @param object|string $element
	 *
	 * @return integer
	 */
	function addElement($element) {
		$this->elements[] = $element;

		return 0;
	}
}
