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

use Fisharebest\Webtrees\Contracts\TimestampInterface;

/**
 * Immutable page-layout configuration for a report, built by the parser
 * from the <Doc> element's attributes and passed to the renderer's setup().
 *
 * All dimensions are in points (1pt = 1/72 inch).
 *
 * For PDF reports, we can disable compression and font-subsetting during testing.
 */
final readonly class Config
{
    public function __construct(
        public float $paper_width,
        public float $paper_height,
        public float $left_margin,
        public float $right_margin,
        public float $top_margin,
        public float $bottom_margin,
        public float $header_margin,
        public float $footer_margin,
        public PageOrientation $orientation,
        public PaperSize $paper_size,
        public bool $rtl,
        public string $generated_by,
        public string $author,
        public string $title,
        public string $description,
        public string $align_rtl,
        public string $entity_rtl,
        public string $font,
        public TimestampInterface $timestamp,
        public bool $font_subsetting = true,
        public bool $compression = true,
    ) {
    }
}
