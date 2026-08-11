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

namespace Fisharebest\Webtrees\Tests\Unit\Report;

use Fisharebest\Webtrees\Report\PdfPageState;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PdfPageState::class)]
class PdfPageStateTest extends TestCase
{
    public function testInitialState(): void
    {
        $pdf_page_state = new PdfPageState();

        self::assertFalse($pdf_page_state->hasCurrentPage());
        self::assertSame(-1, $pdf_page_state->currentPageIndex());
        self::assertSame(0, $pdf_page_state->currentPageNumber());
        self::assertSame(0, $pdf_page_state->pageCount());
    }

    public function testIncrementPageUpdatesIndexNumberAndCount(): void
    {
        $pdf_page_state = new PdfPageState();

        $pdf_page_state->incrementPage();
        $pdf_page_state->incrementPage();

        self::assertTrue($pdf_page_state->hasCurrentPage());
        self::assertSame(1, $pdf_page_state->currentPageIndex());
        self::assertSame(2, $pdf_page_state->currentPageNumber());
        self::assertSame(2, $pdf_page_state->pageCount());
    }
}
