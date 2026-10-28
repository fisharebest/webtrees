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

use Fisharebest\Webtrees\Report\CellAlign;
use Fisharebest\Webtrees\Report\HexColor;
use Fisharebest\Webtrees\Report\LayoutBlock;
use Fisharebest\Webtrees\Report\PdfBlockWriter;
use Fisharebest\Webtrees\Report\PdfRenderTargetInterface;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Report\TextRun;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use RuntimeException;

use function mb_strlen;

#[CoversClass(PdfBlockWriter::class)]
class PdfBlockWriterTest extends TestCase
{
    public function testRenderAppliesOriginOffsetToTextCell(): void
    {
        $style = new Style(name: 'body', style: '', size: 10.0);

        $block = LayoutBlock::textCell(
            page: 0,
            x: 4.0,
            y: 6.0,
            width: 120.0,
            height: 14.0,
            text: 'Body text',
            style: $style,
            align: CellAlign::Left,
            background_color: '',
            border: '',
            border_color: '',
            text_color: '',
            url: '',
        );

        $renderer = $this->createMock(PdfRenderTargetInterface::class);
        $renderer->expects($this->exactly(2))
            ->method('getPageIndex')
            ->willReturn(0);
        $renderer->expects($this->once())
            ->method('setCurrentStyle')
            ->with($style);
        $renderer->expects($this->once())
            ->method('drawTextBlock')
            ->with('Body text', 40.0, 56.0, 120.0, 14.0, 'L', 12.5);
        $renderer->expects($this->once())
            ->method('resetColors');
        $renderer->expects($this->never())
            ->method('newPage');
        $writer = new PdfBlockWriter();
        $writer->render($renderer, [$block], 36.0, 50.0);
    }

    public function testRenderTextDisablesPaddingForExactWidthBlocks(): void
    {
        $style = new Style(name: 'body', style: '', size: 10.0);

        $block = LayoutBlock::text(
            page: 0,
            x: 2.0,
            y: 3.0,
            width: 30.0,
            height: 12.5,
            text: 'Name',
            style: $style,
            color: '',
        );

        $renderer = $this->createMock(PdfRenderTargetInterface::class);
        $renderer->expects($this->exactly(2))
            ->method('getPageIndex')
            ->willReturn(0);
        $renderer->expects($this->once())
            ->method('setCurrentStyle')
            ->with($style);
        $renderer->expects($this->once())
            ->method('isRTL')
            ->willReturn(false);
        $renderer->expects($this->once())
            ->method('drawTextBlock')
            ->with('Name', 12.0, 23.0, 30.0, 12.5, 'L', 12.5, false);
        $renderer->expects($this->once())
            ->method('resetColors');
        $renderer->expects($this->never())
            ->method('newPage');
        $writer = new PdfBlockWriter();
        $writer->render($renderer, [$block], 10.0, 20.0);
    }

    public function testRenderTextFlowHandlesVeryNarrowColumnsWithoutRecursion(): void
    {
        $style = new Style(name: 'body', style: '', size: 10.0);

        $block = LayoutBlock::textFlow(
            page: 0,
            x: 0.0,
            y: 0.0,
            width: 1.0,
            height: 200.0,
            runs: [new TextRun('Name', $style, '')],
        );

        $renderer = $this->createMock(PdfRenderTargetInterface::class);
        $renderer->expects($this->exactly(2))
            ->method('getPageIndex')
            ->willReturn(0);
        $renderer->expects($this->atLeastOnce())
            ->method('setCurrentStyle')
            ->with($style);
        $renderer->expects($this->atLeastOnce())
            ->method('drawTextBlock');
        $renderer->expects($this->once())
            ->method('resetColors');
        $renderer->expects($this->never())
            ->method('newPage');
        $renderer->method('getStringWidth')
            ->willReturnCallback(static fn (string $text): float => 10.0 * mb_strlen($text));

        $writer = new PdfBlockWriter();
        $writer->render($renderer, [$block]);
    }

    public function testRenderTextFlowUsesWrappedLineCountForNextRunPosition(): void
    {
        $style = new Style(name: 'body', style: '', size: 10.0);

        $block = LayoutBlock::textFlow(
            page: 0,
            x: 0.0,
            y: 0.0,
            width: 10.0,
            height: 200.0,
            runs: [
                new TextRun('aaaaaa aaaaaa aaaaaa', $style, ''),
                new TextRun('NEXT', $style, ''),
            ],
        );

        $renderer = $this->createMock(PdfRenderTargetInterface::class);
        $renderer->expects($this->exactly(2))
            ->method('getPageIndex')
            ->willReturn(0);
        $renderer->expects($this->exactly(2))
            ->method('drawTextBlock')
            ->willReturnCallback(function (string $text, float $x, float $y, float $width, float $height, string $align, float $line_height, bool $with_padding = true): void {
                static $call_number = 0;
                $call_number++;

                if ($call_number === 1) {
                    self::assertSame('aaaaaa aaaaaa aaaaaa', $text);
                    self::assertSame(0.0, $x);
                    self::assertSame(0.0, $y);
                    self::assertSame(10.0, $width);
                    self::assertSame(200.0, $height);
                } elseif ($call_number === 2) {
                    self::assertSame('NEXT', $text);
                    self::assertSame(6.0, $x);
                    self::assertSame(25.0, $y);
                    self::assertSame(4.0, $width);
                    self::assertSame(175.0, $height);
                }

                self::assertSame('L', $align);
                self::assertSame(12.5, $line_height);
                self::assertFalse($with_padding);
            });
        $renderer->expects($this->exactly(2))
            ->method('resetColors');
        $renderer->method('setCurrentStyle');
        $renderer->method('isRTL')
            ->willReturn(false);
        $renderer->method('getStringWidth')
            ->willReturnCallback(static fn (string $text): float => (float) mb_strlen($text));

        $writer = new PdfBlockWriter();
        $writer->render($renderer, [$block]);
    }

    public function testRenderThrowsForOutOfOrderPages(): void
    {
        $renderer = $this->createMock(PdfRenderTargetInterface::class);
        $current_page = 0;

        $renderer->method('getPageIndex')
            ->willReturnCallback(static function () use (&$current_page): int {
                return $current_page;
            });
        $renderer->expects($this->once())
            ->method('newPage')
            ->willReturnCallback(static function () use (&$current_page): void {
                $current_page++;
            });

        $writer = new PdfBlockWriter();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Layout blocks must be rendered in page order');

        $writer->render($renderer, [
            LayoutBlock::pageBreak(1),
            LayoutBlock::pageBreak(0),
        ]);
    }

    public function testRenderLineAppliesOriginOffset(): void
    {
        $block = LayoutBlock::line(
            page: 0,
            x1: 1.0,
            y1: 2.0,
            x2: 11.0,
            y2: 22.0,
        );

        $renderer = $this->createMock(PdfRenderTargetInterface::class);
        $renderer->expects($this->exactly(2))
            ->method('getPageIndex')
            ->willReturn(0);
        $renderer->expects($this->once())
            ->method('drawLine')
            ->with(11.0, 22.0, 21.0, 42.0);
        $renderer->expects($this->never())
            ->method('newPage');

        $writer = new PdfBlockWriter();
        $writer->render($renderer, [$block], 10.0, 20.0);
    }

    public function testRenderRectAppliesOriginOffsetAndBorderFillStyle(): void
    {
        $block = LayoutBlock::rect(
            page: 0,
            x: 2.0,
            y: 3.0,
            width: 40.0,
            height: 12.0,
            background_color: '#ffffff',
            border: true,
        );

        $renderer = $this->createMock(PdfRenderTargetInterface::class);
        $renderer->expects($this->exactly(2))
            ->method('getPageIndex')
            ->willReturn(0);
        $renderer->expects($this->once())
            ->method('setFillColor')
            ->with(self::callback(static fn (HexColor $color): bool => $color->hex() === '#FFFFFF'));
        $renderer->expects($this->once())
            ->method('drawRect')
            ->with(12.0, 23.0, 40.0, 12.0, 'DF');
        $renderer->expects($this->once())
            ->method('resetColors');
        $renderer->expects($this->never())
            ->method('newPage');

        $writer = new PdfBlockWriter();
        $writer->render($renderer, [$block], 10.0, 20.0);
    }

    public function testRenderImageAppliesOriginOffset(): void
    {
        $block = LayoutBlock::image(
            page: 0,
            x: 3.0,
            y: 4.0,
            width: 80.0,
            height: 50.0,
            src: '@binary-image-data',
            align: CellAlign::Left,
        );

        $renderer = $this->createMock(PdfRenderTargetInterface::class);
        $renderer->expects($this->exactly(2))
            ->method('getPageIndex')
            ->willReturn(0);
        $renderer->expects($this->once())
            ->method('drawImage')
            ->with('@binary-image-data', 13.0, 24.0, 80.0, 50.0);
        $renderer->expects($this->never())
            ->method('newPage');

        $writer = new PdfBlockWriter();
        $writer->render($renderer, [$block], 10.0, 20.0);
    }

    public function testRenderCreatesPagesForHigherPageIndexBlocks(): void
    {
        $current_page = 0;

        $renderer = $this->createMock(PdfRenderTargetInterface::class);
        $renderer->method('getPageIndex')
            ->willReturnCallback(static function () use (&$current_page): int {
                return $current_page;
            });
        $renderer->expects($this->exactly(2))
            ->method('newPage')
            ->willReturnCallback(static function () use (&$current_page): void {
                $current_page++;
            });
        $renderer->expects($this->once())
            ->method('drawLine')
            ->with(10.0, 20.0, 20.0, 30.0);

        $writer = new PdfBlockWriter();
        $writer->render($renderer, [
            LayoutBlock::pageBreak(2),
            LayoutBlock::line(page: 2, x1: 0.0, y1: 0.0, x2: 10.0, y2: 10.0),
        ], 10.0, 20.0);
    }
}
