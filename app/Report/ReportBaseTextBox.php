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

abstract class ReportBaseTextBox extends ReportBaseElement
{
    /** @var array<ReportBaseElement|string> */
    protected array $elements = [];

    public function __construct(
        protected float $width,
        protected float $height,
        protected bool $border,
        protected string $bgcolor,
        protected bool $newline, // Does following text start on new line
        protected float $left,
        protected float $top,
        protected bool $pagecheck,
        protected string $style, // D or empty string: Draw (default), F: Fill, DF/FD: Draw and fill, CEO: Clip odd/even, CNZ: Clip non-zero winding
        protected bool $fill,
        protected bool $padding,
        protected bool $reseth, // Resets this box last height after it’s done
    ) {
    }

    public function addElement(ReportBaseElement|string $element): void
    {
        $this->elements[] = $element;
    }
}
