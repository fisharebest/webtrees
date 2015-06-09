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
 * Class ReportBaseCell
 */
class ReportBaseCell extends ReportBaseElement {
	/**
	 * Allows to center or align the text. Possible values are:<ul><li>left or empty string: left align</li><li>center: center align</li><li>right: right align</li><li>justify: justification (default value when $ishtml=false)</li></ul>
	 *
	 * @var string
	 */
	public $align = "";
	/**
	 * Whether or not a border should be printed around this box. 0 = no border, 1 = border. Default is 0.
	 * Or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
	 *
	 * @var mixed
	 */
	public $border;
	/**
	 * Border color in HTML code
	 *
	 * @var string
	 */
	public $bocolor;
	/**
	 * The HTML color code to fill the background of this cell.
	 *
	 * @var string
	 */
	public $bgcolor;
	/**
	 * Indicates if the cell background must be painted (1) or transparent (0). Default value: 1.
	 * If no background color is set then it will not be painted
	 *
	 * @var int
	 */
	public $fill;
	/**
	 * Cell height DEFAULT 0 (expressed in points)
	 * The starting height of this cell. If the text wraps the height will automatically be adjusted.
	 *
	 * @var int
	 */
	public $height;
	/**
	 * Left position in user units (X-position). Default is the current position
	 *
	 * @var mixed
	 */
	public $left;
	/**
	 * Indicates where the current position should go after the call.  Possible values are:<ul><li>0: to the right [DEFAULT]</li><li>1: to the beginning of the next line</li><li>2: below</li></ul>
	 *
	 * @var int
	 */
	public $newline;
	/**
	 * The name of the Style that should be used to render the text.
	 *
	 * @var string
	 */
	public $styleName;
	/**
	 * Stretch carachter mode: <ul><li>0 = disabled (default)</li><li>1 = horizontal scaling only if necessary</li><li>2 = forced horizontal scaling</li><li>3 = character spacing only if necessary</li><li>4 = forced character spacing</li></ul>
	 *
	 * @var int
	 */
	public $stretch;
	/**
	 * Text color in HTML code
	 *
	 * @var string
	 */
	public $tcolor;
	/**
	 * Top position in user units (Y-position). Default is the current position
	 *
	 * @var mixed
	 */
	public $top;
	/**
	 * URL address
	 *
	 * @var string
	 */
	public $url;
	/**
	 * Cell width DEFAULT 0 (expressed in points)
	 * Setting the width to 0 will make it the width from the current location to the right margin.
	 *
	 * @var int
	 */
	public $width;

	/** @var int Unknown */
	public $reseth;

	/**
	 * CELL - Element
	 *
	 * @param int    $width   cell width (expressed in points)
	 * @param int    $height  cell height (expressed in points)
	 * @param mixed  $border  Border style
	 * @param string $align   Text alignement
	 * @param string $bgcolor Background color code
	 * @param string $style   The name of the text style
	 * @param int    $ln      Indicates where the current position should go after the call
	 * @param mixed  $top     Y-position
	 * @param mixed  $left    X-position
	 * @param int    $fill    Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
	 * @param int $stretch Stretch carachter mode
	 * @param string $bocolor Border color
	 * @param string $tcolor  Text color
	 * @param        $reseth
	 */
	public function __construct(
		$width, $height, $border, $align, $bgcolor, $style, $ln, $top, $left, $fill, $stretch, $bocolor, $tcolor, $reseth
	) {
		$this->align     = $align;
		$this->border    = $border;
		$this->bgcolor   = $bgcolor;
		$this->bocolor   = $bocolor;
		$this->fill      = $fill;
		$this->height    = $height;
		$this->left      = $left;
		$this->newline   = $ln;
		$this->styleName = $style;
		$this->text      = "";
		$this->tcolor    = $tcolor;
		$this->top       = $top;
		$this->url       = "";
		$this->stretch   = $stretch;
		$this->width     = $width;
		$this->reseth    = $reseth;

		return 0;
	}

	/**
	 * Get the cell height
	 *
	 * @param $renderer
	 *
	 * @return float
	 */
	public function getHeight($renderer) {
		return $this->height;
	}

	/**
	 * Sets the current cells URL
	 *
	 * @param string $url The URL address to save
	 *
	 * @return int
	 */
	public function setUrl($url) {
		$this->url = $url;

		return 0;
	}

	/**
	 * Get the cell width
	 *
	 * @param $renderer
	 *
	 * @return float
	 */
	public function getWidth($renderer) {
		return $this->width;
	}
}
