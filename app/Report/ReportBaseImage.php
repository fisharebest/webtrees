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
 * Class ReportBaseImage
 */
class ReportBaseImage extends ReportBaseElement {
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
	public function __construct($file, $x, $y, $w, $h, $align, $ln) {
		$this->file   = $file;
		$this->width  = $w;
		$this->height = $h;
		$this->x      = $x;
		$this->y      = $y;
		$this->align  = $align;
		$this->line   = $ln;

		return 0;
	}

	/**
	 * Get the height.
	 *
	 * @param $renderer
	 *
	 * @return float
	 */
	public function getHeight($renderer) {
		return $this->height;
	}

	/**
	 * Get the width.
	 *
	 * @param $renderer
	 *
	 * @return float
	 */
	public function getWidth($renderer) {
		return $this->width;
	}
}
