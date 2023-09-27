<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees;

/**
 * Generate a range of colurs for the lifespan chart.
 */
class ColorGenerator
{
    private int $hue;

    private int $basehue;

    private int $saturation;

    private int $lightness;

    private int $baselightness;

    private float $alpha;

    private int $range;

    /**
     * Create a color generator.
     *
     * @param int   $hue        0Deg = Red, 120Deg = green, 240Deg = blue)
     * @param int   $saturation
     * @param int   $lightness
     * @param float $alpha
     * @param int   $range      sign determines direction. positive = clockwise, negative = anticlockwise
     */
    public function __construct(int $hue, int $saturation, int $lightness, float $alpha, int $range)
    {
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
    public function getNextColor(int $lightnessStep = 10, int $hueStep = 15): string
    {
        $lightness = $this->lightness + $lightnessStep;
        $hue       = $this->hue;

        if ($lightness >= 100) {
            $lightness = $this->baselightness;
            if ($this->range > 0) {
                $hue += $hueStep;
            } else {
                $hue -= $hueStep;
            }
            if (($hue - $this->basehue) * ($hue - ($this->basehue + $this->range)) >= 0) {
                $hue = $this->basehue;
            }
            $this->hue = $hue;
        }
        $this->lightness = $lightness;

        return sprintf('hsla(%d, %d%%, %d%%, %0.2f)', $this->hue, $this->saturation, $this->lightness, $this->alpha);
    }
}
