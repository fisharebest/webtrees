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
use Fisharebest\Webtrees\Report\CellAlign;
use Fisharebest\Webtrees\Report\HtmlWriter;
use Fisharebest\Webtrees\Report\LayoutBlock;
use Fisharebest\Webtrees\Report\LayoutPages;
use Fisharebest\Webtrees\Report\PageOrientation;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Report\Config;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Report\TextRun;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(HtmlWriter::class)]
class HtmlWriterTest extends TestCase
{
    private function createConfig(): Config
    {
        return new Config(
            paper_width: 612.0,
            paper_height: 792.0,
            left_margin: 36.0,
            right_margin: 36.0,
            top_margin: 36.0,
            bottom_margin: 36.0,
            header_margin: 10.0,
            footer_margin: 10.0,
            orientation: PageOrientation::Portrait,
            paper_size: PaperSize::USLetter,
            rtl: false,
            generated_by: '',
            author: 'test',
            title: 'test',
            description: 'test',
            align_rtl: 'left',
            entity_rtl: '&lrm;',
            font: 'dejavusans',
            timestamp: $this->createStub(TimestampInterface::class),
        );
    }

    public function testBodyUsesFlowRowForEligibleGroupedBlocks(): void
    {
        $writer = new HtmlWriter();
        $style = new Style(name: 'body', style: '', size: 10.0);

        $body_blocks = [
            LayoutBlock::rect(
                page: 0,
                x: 0.0,
                y: 0.0,
                width: 200.0,
                height: 40.0,
                background_color: '#EFEFEF',
                border: true,
            )->withRowColumn(1, 1),
            LayoutBlock::textFlow(
                page: 0,
                x: 2.0,
                y: 2.0,
                width: 196.0,
                height: 36.0,
                runs: [new TextRun('Column 1', $style, '')],
            )->withRowColumn(1, 1),
            LayoutBlock::rect(
                page: 0,
                x: 200.0,
                y: 0.0,
                width: 340.0,
                height: 40.0,
                background_color: '#EFEFEF',
                border: true,
            )->withRowColumn(1, 2),
            LayoutBlock::textFlow(
                page: 0,
                x: 202.0,
                y: 2.0,
                width: 336.0,
                height: 36.0,
                runs: [new TextRun('Column 2', $style, '')],
            )->withRowColumn(1, 2),
        ];

        $html = $writer->render(
            config: $this->createConfig(),
            styles: ['body' => $style],
            header_blocks: [],
            body_blocks: $body_blocks,
            footer_blocks: [],
        );

        self::assertStringContainsString('<div class="report-row"', $html);
        self::assertStringContainsString('<div class="report-col"', $html);
        self::assertStringContainsString('position:absolute; left:0pt; top:0pt; width:540pt;', $html);
    }

    public function testBodyUsesFlowRowForGroupedTextCells(): void
    {
        $writer = new HtmlWriter();
        $style = new Style(name: 'body', style: '', size: 10.0);

        $body_blocks = [
            LayoutBlock::textCell(
                page: 0,
                x: 0.0,
                y: 0.0,
                width: 270.0,
                height: 20.0,
                text: 'Left',
                style: $style,
                align: CellAlign::Left,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            )->withRowColumn(1, 1),
            LayoutBlock::textCell(
                page: 0,
                x: 270.0,
                y: 0.0,
                width: 270.0,
                height: 20.0,
                text: 'Right',
                style: $style,
                align: CellAlign::Left,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            )->withRowColumn(1, 2),
        ];

        $html = $writer->render(
            config: $this->createConfig(),
            styles: ['body' => $style],
            header_blocks: [],
            body_blocks: $body_blocks,
            footer_blocks: [],
        );

        self::assertStringContainsString('<div class="report-row"', $html);
        self::assertStringContainsString('<div class="report-col"', $html);
        self::assertStringNotContainsString('report-block report-text body', $html);
    }

    public function testFlowRowsPreserveAbsoluteTopPositions(): void
    {
        $writer = new HtmlWriter();
        $style = new Style(name: 'body', style: '', size: 10.0);

        $body_blocks = [
            LayoutBlock::textCell(
                page: 0,
                x: 0.0,
                y: 0.0,
                width: 270.0,
                height: 20.0,
                text: 'First left',
                style: $style,
                align: CellAlign::Left,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            )->withRowColumn(1, 1),
            LayoutBlock::textCell(
                page: 0,
                x: 270.0,
                y: 0.0,
                width: 270.0,
                height: 20.0,
                text: 'First right',
                style: $style,
                align: CellAlign::Left,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            )->withRowColumn(1, 2),
            LayoutBlock::textCell(
                page: 0,
                x: 0.0,
                y: 30.0,
                width: 270.0,
                height: 20.0,
                text: 'Second left',
                style: $style,
                align: CellAlign::Left,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            )->withRowColumn(2, 1),
            LayoutBlock::textCell(
                page: 0,
                x: 270.0,
                y: 30.0,
                width: 270.0,
                height: 20.0,
                text: 'Second right',
                style: $style,
                align: CellAlign::Left,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            )->withRowColumn(2, 2),
        ];

        $html = $writer->render(
            config: $this->createConfig(),
            styles: ['body' => $style],
            header_blocks: [],
            body_blocks: $body_blocks,
            footer_blocks: [],
        );

        self::assertStringContainsString('position:absolute; left:0pt; top:30pt; width:540pt;', $html);
        self::assertStringContainsString('min-height: 50pt', $html);
    }

    public function testSingleColumnHeadingBetweenFlowRowsKeepsAbsoluteTop(): void
    {
        $writer = new HtmlWriter();
        $body_style = new Style(name: 'body', style: '', size: 10.0);
        $subheader_style = new Style(name: 'subheader', style: 'b', size: 12.0);

        $body_blocks = [
            LayoutBlock::textCell(
                page: 0,
                x: 0.0,
                y: 0.0,
                width: 270.0,
                height: 20.0,
                text: 'Row 1 left',
                style: $body_style,
                align: CellAlign::Left,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            )->withRowColumn(1, 1),
            LayoutBlock::textCell(
                page: 0,
                x: 270.0,
                y: 0.0,
                width: 270.0,
                height: 20.0,
                text: 'Row 1 right',
                style: $body_style,
                align: CellAlign::Left,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            )->withRowColumn(1, 2),
            LayoutBlock::textCell(
                page: 0,
                x: 0.0,
                y: 40.0,
                width: 540.0,
                height: 30.0,
                text: 'Generation 2',
                style: $subheader_style,
                align: CellAlign::Center,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            )->withRowColumn(2, 1),
            LayoutBlock::textCell(
                page: 0,
                x: 0.0,
                y: 80.0,
                width: 270.0,
                height: 20.0,
                text: 'Row 3 left',
                style: $body_style,
                align: CellAlign::Left,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            )->withRowColumn(3, 1),
            LayoutBlock::textCell(
                page: 0,
                x: 270.0,
                y: 80.0,
                width: 270.0,
                height: 20.0,
                text: 'Row 3 right',
                style: $body_style,
                align: CellAlign::Left,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            )->withRowColumn(3, 2),
        ];

        $html = $writer->render(
            config: $this->createConfig(),
            styles: ['body' => $body_style, 'subheader' => $subheader_style],
            header_blocks: [],
            body_blocks: $body_blocks,
            footer_blocks: [],
        );

        self::assertStringContainsString('report-block report-text subheader', $html);
        self::assertStringContainsString('left:0pt;top:40pt;width:540pt;padding:2pt;text-align:center', $html);
    }

    public function testRenderPagedMatchesRenderForEquivalentInputs(): void
    {
        $writer = new HtmlWriter();
        $style = new Style(name: 'body', style: '', size: 10.0);

        $body_blocks = [
            LayoutBlock::textCell(
                page: 0,
                x: 0.0,
                y: 0.0,
                width: 540.0,
                height: 20.0,
                text: 'Body',
                style: $style,
                align: CellAlign::Left,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            ),
        ];

        $flat_html = $writer->render(
            config: $this->createConfig(),
            styles: ['body' => $style],
            header_blocks: [],
            body_blocks: $body_blocks,
            footer_blocks: [],
        );

        $paged_html = $writer->renderPaged(
            config: $this->createConfig(),
            styles: ['body' => $style],
            header_pages: new LayoutPages([0 => []]),
            body_pages: new LayoutPages([0 => $body_blocks]),
            footer_pages: new LayoutPages([0 => []]),
        );

        self::assertSame($flat_html, $paged_html);
    }

    public function testRenderIncludesPageBreakMarkerClass(): void
    {
        $writer = new HtmlWriter();
        $style = new Style(name: 'body', style: '', size: 10.0);

        $body_blocks = [
            LayoutBlock::textCell(
                page: 0,
                x: 0.0,
                y: 0.0,
                width: 540.0,
                height: 20.0,
                text: 'Page 1',
                style: $style,
                align: CellAlign::Left,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            ),
            LayoutBlock::pageBreak(0),
            LayoutBlock::textCell(
                page: 1,
                x: 0.0,
                y: 0.0,
                width: 540.0,
                height: 20.0,
                text: 'Page 2',
                style: $style,
                align: CellAlign::Left,
                background_color: '',
                border: '',
                border_color: '',
                text_color: '',
                url: '',
            ),
        ];

        $html = $writer->renderPaged(
            config: $this->createConfig(),
            styles: ['body' => $style],
            header_pages: new LayoutPages([0 => []]),
            body_pages: new LayoutPages([0 => [$body_blocks[0], $body_blocks[1]], 1 => [$body_blocks[2]]]),
            footer_pages: new LayoutPages([0 => []]),
        );

        self::assertStringContainsString('<div class="report-page-break" aria-hidden="true"></div>', $html);
        self::assertStringNotContainsString('page-break-before:always', $html);
    }

    public function testBoldStyleFlagRendersBoldCss(): void
    {
        $writer = new HtmlWriter();
        $style = new Style(name: 'name', style: 'b', size: 10.0);

        $html = $writer->render(
            config: $this->createConfig(),
            styles: ['name' => $style],
            header_blocks: [],
            body_blocks: [],
            footer_blocks: [],
        );

        self::assertStringContainsString('.name { font-size: 10pt; font-weight: bold; }', $html);
    }
}
