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

/**
 * Tracks current PDF page index and total number of created pages.
 */
final class PdfPageState
{
    private int $current_page = -1;

    private int $page_count = 0;

    public function incrementPage(): void
    {
        $this->current_page++;
        $this->page_count++;
    }

    public function hasCurrentPage(): bool
    {
        return $this->current_page >= 0;
    }

    public function currentPageIndex(): int
    {
        return $this->current_page;
    }

    public function currentPageNumber(): int
    {
        return $this->current_page + 1;
    }

    public function pageCount(): int
    {
        return $this->page_count;
    }
}
