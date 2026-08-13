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

final class Image extends Element
{
    public function __construct(
        public readonly string $src,
        public readonly float $x,
        public readonly float $y,
        public readonly float $width,
        public readonly float $height,
        public readonly CellAlign $align,
        public readonly ImageContinuation $line,
        public readonly string $link = '',
    ) {
    }

    /**
     * Return a copy of this image with a clickable link URL.
     */
    public function withLink(string $link): self
    {
        return new self($this->src, $this->x, $this->y, $this->width, $this->height, $this->align, $this->line, $link);
    }
}
