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
 * Class WT_Report_Base_Image - Base Report Generator, used by the SAX
 * parser to generate reports from the XML report file.
 */
class WT_Report_Base_Image extends WT_Report_Base_Element {
	/**
	 * Filename of the image
	 *
	 * @var string
	 */
	public $file;
	/**
	 * Height of the image
	 *
	 * @var float
	 */
	public $height;
	/**
	 * Width of the image
	 *
	 * @var float
	 */
	public $width;
	/**
	 * X-position (left) of the image
	 *
	 * @var float
	 */
	public $x;
	/**
	 * Y-position (top) of the image
	 *
	 * @var float
	 */
	public $y;
	/**
	 * Placement fo the image. L: left, C:center, R:right
	 *
	 * @var string
	 */
	public $align = "";
	/**
	 * T:same line, N:next line
	 *
	 * @var string
	 */
	public $line = "";

	/**
	 * Image class function - Base
	 *
	 * @param string $file  Filename of the image
	 * @param float  $x     X-position (left) of the image
	 * @param float  $y     Y-position (top) of the image
	 * @param float  $w     Width of the image
	 * @param float  $h     Height of the image
	 * @param string $align Placement of the image. L: left, C:center, R:right
	 * @param string $ln    T:same line, N:next line
	 */
	function __construct($file, $x, $y, $w, $h, $align, $ln) {
		$this->file = $file;
		$this->width = $w;
		$this->height = $h;
		$this->x = $x;
		$this->y = $y;
		$this->align = $align;
		$this->line = $ln;

		return 0;
	}

	/**
	 * @param $renderer
	 *
	 * @return float
	 */
	function getHeight($renderer) {
		return $this->height;
	}

	/**
	 * @param $renderer
	 *
	 * @return float
	 */
	function getWidth($renderer) {
		return $this->width;
	}
}
