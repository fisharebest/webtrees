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

abstract class ReportBaseCell extends ReportBaseElement
{
    protected string $url = '';

    public function __construct(
        protected float $width, // 0 uses available space to the right margin
        protected float $height,
        protected string $border, // '': none, 1: all, [LRTB]: combination of left, right, top, bottom
        protected string $align, // left/center/right/justify
        protected string $bgcolor,
        protected string $styleName,
        protected int $newline, // 0: to the right (default) 1: to the beginning of the next line 2: below
        protected float $top,
        protected float $left,
        protected bool $fill, // transparent background, 1: solid background
        protected int $stretch, // 0: disabled (default), 1: horizontal scaling if necessary 2: horizontal scaling 3: character spacing if necessary 4 = character spacing
        protected string $bocolor, // Border color
        protected string $tcolor, // Text color
        protected bool $reseth
    ) {
    }

    /**
     * @param HtmlRenderer|PdfRenderer $renderer
     */
    public function getHeight($renderer): float
    {
        return $this->height;
    }

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
