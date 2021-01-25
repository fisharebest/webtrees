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

/**
 * Class ReportBaseTextbox
 */
class ReportBaseTextbox extends ReportBaseElement
{
    /**
     * Array of elements in the TextBox
     *
     * @var ReportBaseElement[]|string[]
     */
    public $elements = [];

    /**
     *  Background color in HTML code
     *
     * @var string
     */
    public $bgcolor;
    /**
     * Whether or not paint the background
     *
     * @var bool
     */
    public $fill;

    /**
     * Position the left corner of this box on the page(expressed in points). The default is the current position.
     *
     * @var float
     */
    public $left;
    /**
     * Position the top corner of this box on the page(expressed in points). the default is the current position
     *
     * @var float
     */
    public $top;
    /**
     * After this box is finished rendering, should the next section of text start immediately after the this box or should it start on a new line under this box. 0 = no new line, 1 = force new line. Default is 0
     *
     * @var bool
     */
    public $newline;

    /** @var bool Unused? */
    public $pagecheck;

    /** @var bool Whether to print a border */
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
     * @var bool
     */
    public $padding;
    /**
     * Resets this box last height after it’s done
     *
     * @var bool
     */
    public $reseth;

    /**
     * TextBox - Element - Base
     *
     * @param float  $width   Text box width
     * @param float  $height  Text box height
     * @param bool   $border
     * @param string $bgcolor Background color code in HTML
     * @param bool   $newline
     * @param float  $left
     * @param float  $top
     * @param bool   $pagecheck
     * @param string $style
     * @param bool   $fill
     * @param bool   $padding
     * @param bool   $reseth
     */
    public function __construct(
        float $width,
        float $height,
        bool $border,
        string $bgcolor,
        bool $newline,
        float $left,
        float $top,
        bool $pagecheck,
        string $style,
        bool $fill,
        bool $padding,
        bool $reseth
    ) {
        $this->border    = $border;
        $this->bgcolor   = $bgcolor;
        $this->fill      = $fill;
        $this->height    = $height;
        $this->left      = $left;
        $this->newline   = $newline;
        $this->pagecheck = $pagecheck;
        $this->style     = $style;
        $this->top       = $top;
        $this->width     = $width;
        $this->padding   = $padding;
        $this->reseth    = $reseth;
    }

    /**
     * Add an element to the TextBox
     *
     * @param ReportBaseElement|string $element
     *
     * @return void
     */
    public function addElement($element): void
    {
        $this->elements[] = $element;
    }
}
