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
use Fisharebest\Webtrees\Report\FootnoteBodyData;
use Fisharebest\Webtrees\Report\FootnoteRefData;
use Fisharebest\Webtrees\Report\ImageData;
use Fisharebest\Webtrees\Report\LayoutBlock;
use Fisharebest\Webtrees\Report\LineData;
use Fisharebest\Webtrees\Report\PageBreakData;
use Fisharebest\Webtrees\Report\RectData;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Report\TextCellData;
use Fisharebest\Webtrees\Report\TextData;
use Fisharebest\Webtrees\Report\TextFlowData;
use Fisharebest\Webtrees\Report\TextRun;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LayoutBlock::class)]
class LayoutBlockTest extends TestCase
{
    public function testTextCellFactory(): void
    {
        $style = new Style('body', '', 12.0);
        $block = LayoutBlock::textCell(
            page: 0,
            x: 10.0,
            y: 20.0,
            width: 200.0,
            height: 14.0,
            text: 'Hello',
            style: $style,
            align: CellAlign::Left,
            background_color: '',
            border: '',
            border_color: '',
            text_color: '#000000',
            url: '',
        );

        self::assertSame(0, $block->page);
        self::assertSame(10.0, $block->x);
        self::assertSame(20.0, $block->y);
        self::assertSame(200.0, $block->width);
        self::assertSame(14.0, $block->height);
        self::assertInstanceOf(TextCellData::class, $block->data);
        self::assertSame('Hello', $block->data->text);
        self::assertNull($block->row_id);
        self::assertNull($block->column_id);
    }

    public function testTextFactory(): void
    {
        $style = new Style('body', '', 12.0);
        $block = LayoutBlock::text(
            page: 1,
            x: 5.0,
            y: 10.0,
            width: 100.0,
            height: 12.0,
            text: 'Inline',
            style: $style,
            color: '#333333',
        );

        self::assertSame(1, $block->page);
        self::assertInstanceOf(TextData::class, $block->data);
        self::assertSame('Inline', $block->data->text);
    }

    public function testTextFlowFactory(): void
    {
        $style = new Style('body', '', 12.0);
        $runs  = [new TextRun(text: 'word', style: $style, color: '')];
        $block = LayoutBlock::textFlow(
            page: 0,
            x: 0.0,
            y: 0.0,
            width: 100.0,
            height: 50.0,
            runs: $runs,
        );

        self::assertInstanceOf(TextFlowData::class, $block->data);
        self::assertCount(1, $block->data->runs);
    }

    public function testImageFactory(): void
    {
        $block = LayoutBlock::image(
            page: 0,
            x: 10.0,
            y: 10.0,
            width: 72.0,
            height: 72.0,
            src: '/photo.jpg',
            align: CellAlign::Center,
        );

        self::assertInstanceOf(ImageData::class, $block->data);
        self::assertSame('/photo.jpg', $block->data->src);
    }

    public function testLineFactory(): void
    {
        $block = LayoutBlock::line(
            page: 0,
            x1: 0.0,
            y1: 50.0,
            x2: 200.0,
            y2: 50.0,
        );

        self::assertInstanceOf(LineData::class, $block->data);
        self::assertSame(0.0, $block->data->x1);
        self::assertSame(200.0, $block->data->x2);
        self::assertSame(200.0, $block->width);
        self::assertSame(0.0, $block->height);
    }

    public function testRectFactory(): void
    {
        $block = LayoutBlock::rect(
            page: 0,
            x: 5.0,
            y: 5.0,
            width: 100.0,
            height: 50.0,
            background_color: '#CCCCCC',
            border: true,
        );

        self::assertInstanceOf(RectData::class, $block->data);
        self::assertSame('#CCCCCC', $block->data->background_color);
        self::assertTrue($block->data->border);
    }

    public function testPageBreakFactory(): void
    {
        $block = LayoutBlock::pageBreak(page: 2);

        self::assertSame(2, $block->page);
        self::assertSame(0.0, $block->x);
        self::assertSame(0.0, $block->y);
        self::assertInstanceOf(PageBreakData::class, $block->data);
    }

    public function testFootnoteRefFactory(): void
    {
        $style = new Style('body', '', 8.0);
        $block = LayoutBlock::footnoteRef(
            page: 0,
            x: 50.0,
            y: 30.0,
            width: 10.0,
            height: 8.0,
            number: 1,
            link_target: 'fn1',
            style: $style,
        );

        self::assertInstanceOf(FootnoteRefData::class, $block->data);
        self::assertSame(1, $block->data->number);
        self::assertSame('fn1', $block->data->link_target);
    }

    public function testFootnoteBodyFactory(): void
    {
        $style = new Style('body', '', 10.0);
        $block = LayoutBlock::footnoteBody(
            page: 0,
            x: 0.0,
            y: 700.0,
            width: 400.0,
            height: 12.0,
            number: 2,
            text: 'Citation details',
            link_target: 'fn2',
            style: $style,
        );

        self::assertInstanceOf(FootnoteBodyData::class, $block->data);
        self::assertSame(2, $block->data->number);
        self::assertSame('Citation details', $block->data->text);
    }

    public function testWithHeightReturnsNewInstance(): void
    {
        $block   = LayoutBlock::pageBreak(page: 0);
        $updated = $block->withHeight(25.0);

        self::assertSame(0.0, $block->height);
        self::assertSame(25.0, $updated->height);
        self::assertSame($block->page, $updated->page);
    }

    public function testWithPageReturnsNewInstance(): void
    {
        $style   = new Style('body', '', 12.0);
        $block   = LayoutBlock::text(0, 0.0, 0.0, 100.0, 12.0, 'text', $style, '');
        $updated = $block->withPage(3);

        self::assertSame(0, $block->page);
        self::assertSame(3, $updated->page);
    }

    public function testWithRowColumnReturnsNewInstance(): void
    {
        $block   = LayoutBlock::pageBreak(page: 0);
        $updated = $block->withRowColumn(5, 2);

        self::assertNull($block->row_id);
        self::assertNull($block->column_id);
        self::assertSame(5, $updated->row_id);
        self::assertSame(2, $updated->column_id);
    }
}
