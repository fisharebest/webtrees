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
 * Class ReportBaseCell
 */
class ReportBaseCell extends ReportBaseElement
{
    // Center or align the text. Possible values are:
    // left or empty string: left align
    // center: center align
    // right: right align
    // justify: justification (default value when $ishtml=false)
    public string $align = '';

    // Whether a border should be printed around this box.
    // ''  = no border (default)
    // '1' = border
    // a string containing some or all of the following characters (in any order):
    // L: left
    // T: top
    // R: right
    // B: bottom
    public string $border;

    // Border color in HTML code
    public string $bocolor;

    // The HTML color code to fill the background of this cell.
    public string $bgcolor;

    // Indicates if the cell background must be painted (1) or transparent (0). Default value: 1.
    // If no background color is set then it will not be painted
    public int $fill;

    // Cell height DEFAULT 0 (expressed in points)
    // The starting height of this cell. If the text wraps the height will automatically be adjusted.
    public float $height;

    /**
     * Left position in user units (X-position). Default is the current position
     *
     * @var mixed
     */

    public $left;

    // Indicates where the current position should go after the call. Possible values are:
    // 0: to the right [DEFAULT]
    // 1: to the beginning of the next line
    // 2: below
    public int $newline;

    // The name of the Style that should be used to render the text.
    public string $styleName;

    // Stretch character mode:
    // 0 = disabled (default)
    // 1 = horizontal scaling only if necessary
    // 2 = forced horizontal scaling
    // 3 = character spacing only if necessary
    // 4 = forced character spacing
    public int $stretch;

    // Text color in HTML code
    public string $tcolor;

    /**
     * Top position in user units (Y-position). Default is the current position
     *
     * @var mixed
     */

    public $top;

    // URL address
    public string $url = '';

    // Cell width DEFAULT 0 (expressed in points)
    // Setting the width to 0 will make it the width from the current location to the right margin.
    public float $width;

    public bool $reseth;

    /**
     * CELL - Element
     *
     * @param float  $width   cell width (expressed in points)
     * @param float  $height  cell height (expressed in points)
     * @param string $border  Border style
     * @param string $align   Text alignment
     * @param string $bgcolor Background color code
     * @param string $style   The name of the text style
     * @param int    $ln      Indicates where the current position should go after the call
     * @param mixed  $top     Y-position
     * @param mixed  $left    X-position
     * @param int    $fill    Indicates if the cell background must be painted (1) or transparent (0).
     * @param int    $stretch Stretch carachter mode
     * @param string $bocolor Border color
     * @param string $tcolor  Text color
     * @param bool   $reseth
     */
    public function __construct(
        float $width,
        float $height,
        string $border,
        string $align,
        string $bgcolor,
        string $style,
        int $ln,
        $top,
        $left,
        int $fill,
        int $stretch,
        string $bocolor,
        string $tcolor,
        bool $reseth
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
        $this->tcolor    = $tcolor;
        $this->top       = $top;
        $this->stretch   = $stretch;
        $this->width     = $width;
        $this->reseth    = $reseth;
    }

    /**
     * Get the cell height
     *
     * @param HtmlRenderer|PdfRenderer $renderer
     *
     * @return float
     */
    public function getHeight($renderer): float
    {
        return $this->height;
    }

    /**
     * Sets the current cells URL
     *
     * @param string $url The URL address to save
     *
     * @return void
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * Get the cell width
     *
     * @param HtmlRenderer|PdfRenderer $renderer
     *
     * @return array{0:float,1:int,2:float}
     */
    public function getWidth($renderer): array
    {
        return [$this->width, 1, $this->height];
    }
}
