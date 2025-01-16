<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
 * Class ReportBaseImage
 */
class ReportBaseImage extends ReportBaseElement
{
    // Filename of the image
    public string $file;

    // Height of the image
    public float $height;

    // Width of the image
    public float $width;

    // X-position (left) of the image
    public float $x;

    // Y-position (top) of the image
    public float $y;

    // Placement of the image. L: left, C:center, R:right (or empty for x/y)
    public string $align;

    // T:same line, N:next line
    public string $line;

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
     * @return array{0:float,1:int,2:float}
     */
    public function getWidth($renderer): array
    {
        return [$this->width, 1, $this->height];
    }
}
