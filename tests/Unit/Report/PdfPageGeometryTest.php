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

use Fisharebest\Webtrees\Report\PdfPageGeometry;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PdfPageGeometry::class)]
class PdfPageGeometryTest extends TestCase
{
    public function testOriginsAndMargins(): void
    {
        $pdf_page_geometry = new PdfPageGeometry(400.0, 600.0, 30.0, 20.0, 35.0, 25.0);

        self::assertSame(25.0, $pdf_page_geometry->header_margin);
        self::assertSame(600.0, $pdf_page_geometry->page_height);
        self::assertSame(30.0, $pdf_page_geometry->left_margin);
        self::assertSame(565.0, $pdf_page_geometry->page_height - $pdf_page_geometry->bottom_margin);
    }

    public function testStoresDimensionsFromConstructor(): void
    {
        $pdf_page_geometry = new PdfPageGeometry(500.0, 700.0, 40.0, 15.0, 30.0, 20.0);

        self::assertSame(500.0, $pdf_page_geometry->page_width);
        self::assertSame(700.0, $pdf_page_geometry->page_height);
        self::assertSame(40.0, $pdf_page_geometry->left_margin);
        self::assertSame(15.0, $pdf_page_geometry->right_margin);
        self::assertSame(30.0, $pdf_page_geometry->bottom_margin);
        self::assertSame(20.0, $pdf_page_geometry->header_margin);
    }
}
