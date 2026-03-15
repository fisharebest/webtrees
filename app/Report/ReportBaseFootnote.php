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

use function str_contains;

abstract class ReportBaseFootnote extends ReportBaseElement
{
    // The name of the style for this element
    protected string $styleName = '';

    // Numbers for the links
    public int $num;

    // The text that will be printed with the number
    protected string $numText = '';

    // Remaining width of a cell
    protected float $wrapWidthRemaining;

    // Original width of a cell (points)
    protected float $wrapWidthCell;

    // A link
    protected string $addlink;

    public function __construct(string $styleName)
    {
        if ($styleName !== '') {
            $this->styleName = $styleName;
        } else {
            $this->styleName = 'footnote';
        }
    }

    public function setWrapWidth(float $wrapwidth, float $cellwidth): float
    {
        $this->wrapWidthCell = $cellwidth;
        if (str_contains($this->numText, "\n")) {
            $this->wrapWidthRemaining = $cellwidth;
        } else {
            $this->wrapWidthRemaining = $wrapwidth;
        }

        return $this->wrapWidthRemaining;
    }

    public function setNum(int $n): void
    {
        $this->num     = $n;
        $this->numText = $n . ' ';
    }

    public function setAddlink(string $a): void
    {
        $this->addlink = $a;
    }
}
