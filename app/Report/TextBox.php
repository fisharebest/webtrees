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

final class TextBox extends Element
{
    /** @var array<Element> */
    public array $elements = [];

    public function __construct(
        public readonly float $width,
        public readonly float $height,
        public readonly bool $border,
        public readonly string $background_color,
        public readonly bool $newline, // Does following text start on new line
        public readonly float $left,
        public readonly float $top,
        public readonly bool $check_page_break,
        public readonly bool $padding,
        public readonly bool $reset_height, // Treat this box as a float (do not advance Y cursor)
    ) {
    }

    public function addElement(Element $element): void
    {
        $this->elements[] = $element;
    }
}
