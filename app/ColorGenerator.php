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
namespace Fisharebest\Webtrees;

/**
 * Generate a range of colurs for the lifespan chart.
 */
class ColorGenerator {
	/** @var int Current hue */
	private $hue;

	/** @var int Initial hue*/
	private $basehue;

	/** @var int Saturation */
	private $saturation;

	/** @var int Lightness */
	private $lightness;

	/** @var int Initial lightness*/
	private $baselightness;

	/** @var int Alpha transparancy */
	private $alpha;

	/** @var int Clockwise or anticlockwise color wheel */
	private $range;

	/**
	 * Create a color generator.
	 *
	 * @param int $hue (0Deg = Red, 120Deg = green, 240Deg = blue)
	 * @param int $saturation
	 * @param int $lightness
	 * @param int $alpha
	 * @param int $range (sign determines direction. positive = clockwise, negative = anticlockwise)
	 */
	public function __construct($hue, $saturation, $lightness, $alpha, $range) {
		$this->hue           = $hue;
		$this->basehue       = $hue;
		$this->saturation    = $saturation;
		$this->lightness     = $lightness;
		$this->baselightness = $lightness;
		$this->alpha         = $alpha;
		$this->range         = $range;
	}

	/**
	 * Function getNextColor
	 *
	 * $lightness cycles between $baselightness and 100% in $lightnessStep steps
	 * $hue cycles on each complete $lightness cycle
	 * between $basehue and $basehue + $range degrees in $hueStep degrees
	 *
	 * @param int $lightnessStep
	 * @param int $hueStep
	 *
	 * @return string
	 */
	public function getNextColor($lightnessStep = 10, $hueStep = 15) {
		$lightness = $this->lightness + $lightnessStep;
		$hue       = $this->hue;

		if ($lightness >= 100) {
			$lightness = $this->baselightness;
			$hue += $hueStep * (abs($this->range) / $this->range);
			if (($hue - $this->basehue) * ($hue - ($this->basehue + $this->range)) >= 0) {
				$hue = $this->basehue;
			}
			$this->hue = $hue;
		}
		$this->lightness = $lightness;

		return sprintf("hsla(%s, %s%%, %s%%, %s)",
			$this->hue,
			$this->saturation,
			$this->lightness,
			$this->alpha);
	}

}
