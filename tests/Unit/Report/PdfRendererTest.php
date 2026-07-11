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

use Fisharebest\Webtrees\Contracts\TimestampInterface;
use Fisharebest\Webtrees\Report\Config;
use Fisharebest\Webtrees\Report\PageOrientation;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Report\PdfRenderer;
use Fisharebest\Webtrees\Report\PdfWriter;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;

#[CoversClass(PdfRenderer::class)]
class PdfRendererTest extends TestCase
{
    use ElementTestTrait;

    private function createConfig(): Config
    {
        return new Config(
            paper_width: 400.0,
            paper_height: 600.0,
            left_margin: 30.0,
            right_margin: 20.0,
            top_margin: 0.0,
            bottom_margin: 35.0,
            header_margin: 25.0,
            footer_margin: 0.0,
            orientation: PageOrientation::Portrait,
            paper_size: PaperSize::USLetter,
            rtl: false,
            generated_by: '',
            author: 'test',
            title: 'test',
            description: 'test',
            align_rtl: 'left',
            entity_rtl: '&lrm;',
            primary_font: 'dejavusans',
            fallback_fonts: [],
            timestamp: $this->createStub(TimestampInterface::class),
        );
    }

    public function testClass(): void
    {
        self::assertTrue(class_exists(PdfRenderer::class));
    }

    public function testInitializesReadonlyPdfWriterAndRendersSections(): void
    {
        $renderer = new PdfRenderer();
        $renderer->setup($this->createConfig());

        $reflection_property = new ReflectionProperty($renderer, 'pdf_writer');
        $pdf_writer = $reflection_property->getValue($renderer);

        self::assertTrue($reflection_property->isReadOnly());
        self::assertInstanceOf(PdfWriter::class, $pdf_writer);

        $renderer->header();
        $renderer->body();
        $renderer->footer();

        self::assertGreaterThanOrEqual(1, $renderer->pageNumber());
    }
}
