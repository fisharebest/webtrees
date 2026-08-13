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

final class Cell extends Element
{
    public function __construct(
        public readonly float $width, // 0 uses available space to the right margin
        public readonly float $height,
        public readonly string $border, // '': none, 1: all, [LRTB]: combination of left, right, top, bottom
        public readonly CellAlign $align,
        public readonly string $background_color,
        public readonly Style $style,
        public readonly CellNewline $newline,
        public readonly float $top,
        public readonly float $left,
        public readonly string $border_color,
        public readonly string $text_color,
        public string $url = '', // Optional hyperlink URL for the cell
    ) {
    }
}
