<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Report;

use function str_contains;

/**
 * Class ReportBaseText
 */
class ReportBaseText extends ReportBaseElement
{
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
     * @var float User unit (points)
     */
    public $wrapWidthRemaining;
    /**
     * Original width of a cell
     *
     * @var float User unit (points)
     */
    public $wrapWidthCell;

    /**
     * Create a Text class - Base
     *
     * @param string $style The name of the text style
     * @param string $color HTML color code
     */
    public function __construct(string $style, string $color)
    {
        $this->text               = '';
        $this->color              = $color;
        $this->wrapWidthRemaining = 0;
        $this->styleName          = $style;
    }

    /**
     * Set the width for word-wrapping.
     *
     * @param float $wrapwidth
     * @param float $cellwidth
     *
     * @return float
     */
    public function setWrapWidth(float $wrapwidth, float $cellwidth): float
    {
        $this->wrapWidthCell = $cellwidth;
        if (str_contains($this->text, "\n")) {
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
    public function getStyleName(): string
    {
        return $this->styleName;
    }
}
