<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

abstract class ReportBaseImage extends ReportBaseElement
{
    public function __construct(
        protected string $src,
        protected float $x,
        protected float $y,
        protected float $width,
        protected float $height,
        protected string $align, // L: left, C:center, R:right
        protected string $line,  // T:same line, N:next line
    ) {
    }

    /**
     * @param HtmlRenderer|PdfRenderer $renderer
     */
    public function getHeight($renderer): float
    {
        return $this->height;
    }

    /**
     * @param HtmlRenderer|PdfRenderer $renderer
     *
     * @return array{0:float,1:int,2:float}
     */
    public function getWidth($renderer): array
    {
        return [$this->width, 1, $this->height];
    }
}
