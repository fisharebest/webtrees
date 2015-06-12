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
 * Class ReportBaseText
 */
class ReportBaseText extends ReportBaseElement {
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
	 * @var int User unit (points)
	 */
	public $wrapWidthRemaining;
	/**
	 * Original width of a cell
	 *
	 * @var int User unit (points)
	 */
	public $wrapWidthCell;

	/**
	 * Create a Text class - Base
	 *
	 * @param string $style The name of the text style
	 * @param string $color HTML color code
	 */
	public function __construct($style, $color) {
		$this->text               = '';
		$this->color              = $color;
		$this->wrapWidthRemaining = 0;
		$this->styleName          = $style;

		return 0;
	}

	/**
	 * Set the width for word-wrapping.
	 *
	 * @param $wrapwidth
	 * @param $cellwidth
	 *
	 * @return mixed
	 */
	public function setWrapWidth($wrapwidth, $cellwidth) {
		$this->wrapWidthCell = $cellwidth;
		if (strpos($this->text, "\n") !== false) {
			$this->wrapWidthRemaining = $cellwidth;
		} else {
			$this->wrapWidthRemaining = $wrapwidth;
		}

		return $this->wrapWidthRemaining;
	}

	/**
	 * Get the style name.
	 *
	 * @return string
	 */
	public function getStyleName() {
		return $this->styleName;
	}
}
