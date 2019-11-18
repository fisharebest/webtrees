<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

declare(strict_types=1);

namespace Fisharebest\Webtrees\Report;

/**
 * Class ReportBaseImage
 */
class ReportBaseImage extends ReportBaseElement
{
    /** @var string Filename of the image */
    public $file;

    /** @var float Height of the image */
    public $height;

    /** @var float Width of the image */
    public $width;

    /** @var float X-position (left) of the image */
    public $x;

    /** @var float Y-position (top) of the image */
    public $y;

    /** @var string Placement fo the image. L: left, C:center, R:right (or empty for x/y) */
    public $align;

    /** @var string T:same line, N:next line */
    public $line;

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
    public function __construct(string $file, float $x, float $y, float $w, float $h, string $align, string $ln)
    {
        $this->file   = $file;
        $this->width  = $w;
        $this->height = $h;
        $this->x      = $x;
        $this->y      = $y;
        $this->align  = $align;
        $this->line   = $ln;
    }

    /**
     * Get the height.
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
     * Get the width.
     *
     * @param HtmlRenderer|PdfRenderer $renderer
     *
     * @return float|array
     */
    public function getWidth($renderer)
    {
        return $this->width;
    }
}
