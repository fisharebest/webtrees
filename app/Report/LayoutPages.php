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

use function ksort;

/**
 * Paged layout result grouped by zero-based page index.
 */
final readonly class LayoutPages
{
    /**
     * @param array<int, list<LayoutBlock>> $pages
     */
    public function __construct(
        public array $pages,
    ) {
    }

    /**
     * @return list<LayoutBlock>
     */
    public function flatten(): array
    {
        $pages = $this->pages;
        ksort($pages);

        $blocks = [];
        foreach ($pages as $page_blocks) {
            foreach ($page_blocks as $block) {
                $blocks[] = $block;
            }
        }

        return $blocks;
    }
}
