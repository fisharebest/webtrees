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

use Com\Tecnick\Pdf\Tcpdf;
use Fisharebest\Webtrees\Contracts\TimestampInterface;
use Fisharebest\Webtrees\Report\PageOrientation;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Report\PdfRenderTargetInterface;
use Fisharebest\Webtrees\Report\Config;
use Fisharebest\Webtrees\Report\TcLibPdfAdaptor;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use RuntimeException;

#[CoversClass(TcLibPdfAdaptor::class)]
class TcLibPdfAdaptorTest extends TestCase
{
    private function createConfig(bool $rtl = false): Config
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
            rtl: $rtl,
            generated_by: 'test',
            author: 'test',
            title: 'test',
            description: 'test',
            align_rtl: $rtl ? 'right' : 'left',
            entity_rtl: $rtl ? '&rlm;' : '&lrm;',
            font: 'dejavusans',
            timestamp: $this->createStub(TimestampInterface::class),
        );
    }

    public function testAddPageRendersHeaderFooterAtFixedOrigins(): void
    {
        $header_calls = [];
        $footer_calls = [];

        $renderer = $this->createStub(PdfRenderTargetInterface::class);
        $renderer->method('header')
            ->willReturnCallback(static function (float $origin_x, float $origin_y) use (&$header_calls): void {
                $header_calls[] = [$origin_x, $origin_y];
            });
        $renderer->method('footer')
            ->willReturnCallback(static function (float $origin_x, float $origin_y) use (&$footer_calls): void {
                $footer_calls[] = [$origin_x, $origin_y];
            });

        $adaptor = new TcLibPdfAdaptor(new Tcpdf('pt', true, false, true), $renderer, $this->createConfig());

        $adaptor->addPage();
        $adaptor->addPage();

        self::assertSame([
            [30.0, 25.0],
            [30.0, 25.0],
        ], $header_calls);

        self::assertSame([
            [30.0, 565.0],
        ], $footer_calls);
    }

    public function testAddPageRendersHeaderFooterAtFixedOriginsForRtl(): void
    {
        $header_calls = [];
        $footer_calls = [];

        $renderer = $this->createStub(PdfRenderTargetInterface::class);
        $renderer->method('header')
            ->willReturnCallback(static function (float $origin_x, float $origin_y) use (&$header_calls): void {
                $header_calls[] = [$origin_x, $origin_y];
            });
        $renderer->method('footer')
            ->willReturnCallback(static function (float $origin_x, float $origin_y) use (&$footer_calls): void {
                $footer_calls[] = [$origin_x, $origin_y];
            });

        $adaptor = new TcLibPdfAdaptor(new Tcpdf('pt', true, false, true), $renderer, $this->createConfig(rtl: true));

        $adaptor->addPage();
        $adaptor->addPage();

        self::assertSame([
            [30.0, 25.0],
            [30.0, 25.0],
        ], $header_calls);

        self::assertSame([
            [30.0, 565.0],
        ], $footer_calls);
    }

    public function testRenderGuardResetsAfterHeaderException(): void
    {
        $header_calls = 0;

        $renderer = $this->createStub(PdfRenderTargetInterface::class);
        $renderer->method('header')
            ->willReturnCallback(static function (float $origin_x, float $origin_y) use (&$header_calls): void {
                $header_calls++;
                if ($header_calls === 1) {
                    throw new RuntimeException('header failure');
                }
            });

        $adaptor = new TcLibPdfAdaptor(new Tcpdf('pt', true, false, true), $renderer, $this->createConfig());

        try {
            $adaptor->addPage();
            self::fail('Expected header exception was not thrown.');
        } catch (RuntimeException $runtime_exception) {
            self::assertSame('header failure', $runtime_exception->getMessage());
        }

        // The second page render proves the internal guard state was reset.
        $adaptor->addPage();

        self::assertSame(2, $header_calls);
    }
}
