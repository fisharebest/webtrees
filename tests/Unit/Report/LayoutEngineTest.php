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
use Fisharebest\Webtrees\Report\Cell;
use Fisharebest\Webtrees\Report\Element;
use Fisharebest\Webtrees\Report\Footnote;
use Fisharebest\Webtrees\Report\FootnoteTextsElement;
use Fisharebest\Webtrees\Report\Image;
use Fisharebest\Webtrees\Report\Line;
use Fisharebest\Webtrees\Report\Text;
use Fisharebest\Webtrees\Report\TextBox;
use Fisharebest\Webtrees\Report\CellAlign;
use Fisharebest\Webtrees\Report\CellNewline;
use Fisharebest\Webtrees\Report\HtmlTextMeasurer;
use Fisharebest\Webtrees\Report\ImageContinuation;
use Fisharebest\Webtrees\Report\FootnoteBodyData;
use Fisharebest\Webtrees\Report\FootnoteRefData;
use Fisharebest\Webtrees\Report\ImageData;
use Fisharebest\Webtrees\Report\LayoutBlock;
use Fisharebest\Webtrees\Report\LayoutEngine;
use Fisharebest\Webtrees\Report\LayoutPages;
use Fisharebest\Webtrees\Report\LineData;
use Fisharebest\Webtrees\Report\NewPageElement;
use Fisharebest\Webtrees\Report\PageBreakData;
use Fisharebest\Webtrees\Report\PageOrientation;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Report\RectData;
use Fisharebest\Webtrees\Report\Config;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Report\TextCellData;
use Fisharebest\Webtrees\Report\TextData;
use Fisharebest\Webtrees\Report\TextFlowData;
use Fisharebest\Webtrees\Report\TextWrapper;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function count;

#[CoversClass(LayoutEngine::class)]
#[CoversClass(LayoutPages::class)]
#[CoversClass(TextWrapper::class)]
#[CoversClass(HtmlTextMeasurer::class)]
class LayoutEngineTest extends TestCase
{
    /** Content width: 612 - 36 - 36 = 540pt */
    private const float CONTENT_WIDTH = 540.0;

    /** Content height: 792 - 36 - 36 = 720pt */
    private const float CONTENT_HEIGHT = 720.0;

    private function createConfig(
        bool $rtl = false,
        float $page_height = 792.0,
        PageOrientation $orientation = PageOrientation::Portrait,
    ): Config {
        return new Config(
            paper_width: 612.0,
            paper_height: $page_height,
            left_margin: 36.0,
            right_margin: 36.0,
            top_margin: 36.0,
            bottom_margin: 36.0,
            header_margin: 10.0,
            footer_margin: 10.0,
            orientation: $orientation,
            paper_size: PaperSize::USLetter,
            rtl: $rtl,
            generated_by: 'test',
            author: 'test',
            title: 'test',
            description: 'test',
            align_rtl: $rtl ? 'right' : 'left',
            entity_rtl: $rtl ? '&rlm;' : '&lrm;',
            font: 'dejavusans',
            timestamp: self::createStub(TimestampInterface::class),
        );
    }

    private function createEngine(
        bool $rtl = false,
        float $page_height = 792.0,
        bool $skip_total_pages_token = true,
        PageOrientation $orientation = PageOrientation::Portrait,
    ): LayoutEngine {
        $config = $this->createConfig($rtl, $page_height, $orientation);

        return new LayoutEngine(new HtmlTextMeasurer(), $config, $skip_total_pages_token);
    }

    private function makeStyle(): Style
    {
        return new Style(name: 'body', style: '', size: 10.0);
    }

    // --- TextWrapper tests ---

    public function testEmptyElementListProducesNoBlocks(): void
    {
        $engine = $this->createEngine();
        $blocks = $engine->layout([]);

        self::assertSame([], $blocks);
    }

    public function testTextWrapperWrapsLongText(): void
    {
        $measurer = new HtmlTextMeasurer();
        $adaptor = new TextWrapper($measurer);
        $style = new Style(name: 'body', style: '', size: 12.0);

        // 12pt font → each char is ~6pt wide
        // Width of 60pt fits ~10 characters
        $lines = $adaptor->wrapText('Hello World This Is A Test', $style, 60.0);

        self::assertGreaterThan(1, count($lines));
        $first_line_width = $measurer->getStringWidth($lines[0], $style);
        self::assertLessThanOrEqual(60.0, $first_line_width);
    }

    public function testTextWrapperPreservesExplicitLineBreaks(): void
    {
        $measurer = new HtmlTextMeasurer();
        $adaptor = new TextWrapper($measurer);
        $style = new Style(name: 'body', style: '', size: 10.0);

        $lines = $adaptor->wrapText("Line One\nLine Two\nLine Three", $style, 500.0);

        self::assertCount(3, $lines);
        self::assertSame('Line One', $lines[0]);
        self::assertSame('Line Two', $lines[1]);
        self::assertSame('Line Three', $lines[2]);
    }

    public function testTextWrapperCountsLines(): void
    {
        $measurer = new HtmlTextMeasurer();
        $adaptor = new TextWrapper($measurer);
        $style = new Style(name: 'body', style: '', size: 10.0);

        $count = $adaptor->countLines("Short text", 500.0, $style);
        self::assertSame(1, $count);

        $count = $adaptor->countLines("Line\nBreak", 500.0, $style);
        self::assertSame(2, $count);
    }

    public function testTextWrapperComputesHeight(): void
    {
        $measurer = new HtmlTextMeasurer();
        $adaptor = new TextWrapper($measurer);
        $style = new Style(name: 'body', style: '', size: 10.0);

        $height = $adaptor->textHeight("One line", 500.0, $style, 1.25);

        // 1 line × 10pt × 1.25 = 12.5pt
        self::assertEqualsWithDelta(12.5, $height, 0.01);
    }

    // --- Cell layout tests ---

    public function testSingleCellAtOrigin(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $cell = new Cell(
            width: 200.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell->addText('Hello');

        $blocks = $engine->layout([$cell]);

        self::assertCount(1, $blocks);
        self::assertInstanceOf(TextCellData::class, $blocks[0]->data);
        // Cell placed at origin
        self::assertEqualsWithDelta(0.0, $blocks[0]->x, 0.01);
        self::assertEqualsWithDelta(0.0, $blocks[0]->y, 0.01);
        self::assertEqualsWithDelta(200.0, $blocks[0]->width, 0.01);
    }

    public function testTwoCellsSideBySide(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $cell1 = new Cell(
            width: 200.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::Right,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell1->addText('First');

        $cell2 = new Cell(
            width: 150.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell2->addText('Second');

        $blocks = $engine->layout([$cell1, $cell2]);

        self::assertCount(2, $blocks);
        // First cell at x=0
        self::assertEqualsWithDelta(0.0, $blocks[0]->x, 0.01);
        self::assertEqualsWithDelta(0.0, $blocks[0]->y, 0.01);
        // Second cell starts at x=200 (right of first)
        self::assertEqualsWithDelta(200.0, $blocks[1]->x, 0.01);
        self::assertEqualsWithDelta(0.0, $blocks[1]->y, 0.01);
    }

    public function testCellNextLineAdvancesY(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $cell1 = new Cell(
            width: 200.0,
            height: 25.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell1->addText('Row 1');

        $cell2 = new Cell(
            width: 200.0,
            height: 25.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell2->addText('Row 2');

        $blocks = $engine->layout([$cell1, $cell2]);

        self::assertCount(2, $blocks);
        // First cell at y=0
        self::assertEqualsWithDelta(0.0, $blocks[0]->y, 0.01);
        // Second cell at y=25 (height of first cell)
        self::assertEqualsWithDelta(25.0, $blocks[1]->y, 0.01);
    }

    public function testCellZeroWidthExpandsToRemainingSpace(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $cell = new Cell(
            width: 0.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell->addText('Full width');

        $blocks = $engine->layout([$cell]);

        self::assertCount(1, $blocks);
        // Should expand to content width (540pt)
        self::assertEqualsWithDelta(self::CONTENT_WIDTH, $blocks[0]->width, 0.01);
    }

    public function testCellContainingTotalPagesTokenIsSkippedByDefault(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $cell = new Cell(
            width: 200.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell->addText('Page 1 of ');
        $cell->addTotalPages();

        $blocks = $engine->layout([$cell]);

        self::assertSame([], $blocks);
    }

    public function testCellContainingTotalPagesTokenCanBeKeptForPdfFlow(): void
    {
        $engine = $this->createEngine(skip_total_pages_token: false);
        $style = $this->makeStyle();

        $cell = new Cell(
            width: 200.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell->addText('Page 1 of ');
        $cell->addTotalPages();

        $blocks = $engine->layout([$cell]);

        self::assertCount(1, $blocks);
        self::assertInstanceOf(TextCellData::class, $blocks[0]->data);
        self::assertStringContainsString(Element::TOTAL_PAGES_TOKEN, $blocks[0]->data->text);
    }

    public function testAdjacentCellsShareRowMetadataAndUniqueColumns(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $cell1 = new Cell(
            width: 200.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::Right,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell1->addText('First');

        $cell2 = new Cell(
            width: 150.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell2->addText('Second');

        $blocks = $engine->layout([$cell1, $cell2]);

        self::assertCount(2, $blocks);
        self::assertNotNull($blocks[0]->row_id);
        self::assertSame($blocks[0]->row_id, $blocks[1]->row_id);
        self::assertNotNull($blocks[0]->column_id);
        self::assertNotNull($blocks[1]->column_id);
        self::assertNotSame($blocks[0]->column_id, $blocks[1]->column_id);
    }

    public function testAdjacentCellsInRowNormalizeToTallestHeight(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $cell1 = new Cell(
            width: 180.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::Right,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell1->addText('Short');

        $cell2 = new Cell(
            width: 180.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell2->addText('This cell contains enough text to wrap onto multiple lines and force a taller row height.');

        $blocks = $engine->layout([$cell1, $cell2]);

        self::assertCount(2, $blocks);
        self::assertGreaterThan(20.0, $blocks[1]->height);
        self::assertEqualsWithDelta($blocks[1]->height, $blocks[0]->height, 0.01);
    }

    public function testAdjacentTextBoxesShareRowMetadataOnRectAndTextFlow(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $box1 = new TextBox(
            width: 200.0,
            height: 0.0,
            border: true,
            background_color: '#CCCCCC',
            newline: false,
            left: Element::CURRENT_POSITION,
            top: Element::CURRENT_POSITION,
            check_page_break: true,
            padding: true,
            reset_height: false,
        );
        $text1 = new Text($style, '', 0.0);
        $text1->addText('Column one');
        $box1->addElement($text1);

        $box2 = new TextBox(
            width: 200.0,
            height: 0.0,
            border: true,
            background_color: '#CCCCCC',
            newline: true,
            left: Element::CURRENT_POSITION,
            top: Element::CURRENT_POSITION,
            check_page_break: false,
            padding: true,
            reset_height: false,
        );
        $text2 = new Text($style, '', 0.0);
        $text2->addText('Column two');
        $box2->addElement($text2);

        $blocks = $engine->layout([$box1, $box2]);

        $first_rect = null;
        $first_flow = null;
        $second_rect = null;
        $second_flow = null;

        foreach ($blocks as $block) {
            if ($block->data instanceof RectData && $first_rect === null) {
                $first_rect = $block;
                continue;
            }
            if ($block->data instanceof TextFlowData && $first_flow === null) {
                $first_flow = $block;
                continue;
            }
            if ($block->data instanceof RectData && $second_rect === null) {
                $second_rect = $block;
                continue;
            }
            if ($block->data instanceof TextFlowData) {
                $second_flow = $block;
                break;
            }
        }

        self::assertInstanceOf(LayoutBlock::class, $first_rect);
        self::assertInstanceOf(LayoutBlock::class, $first_flow);
        self::assertInstanceOf(LayoutBlock::class, $second_rect);
        self::assertInstanceOf(LayoutBlock::class, $second_flow);

        self::assertNotNull($first_rect->row_id);
        self::assertSame($first_rect->row_id, $first_flow->row_id);
        self::assertSame($first_rect->row_id, $second_rect->row_id);
        self::assertSame($first_rect->row_id, $second_flow->row_id);

        self::assertNotNull($first_rect->column_id);
        self::assertSame($first_rect->column_id, $first_flow->column_id);
        self::assertNotNull($second_rect->column_id);
        self::assertSame($second_rect->column_id, $second_flow->column_id);
        self::assertNotSame($first_rect->column_id, $second_rect->column_id);
    }

    public function testAdjacentTextBoxesNormalizeRectHeightToTallestRowBox(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $box1 = new TextBox(
            width: 180.0,
            height: 20.0,
            border: true,
            background_color: '#CCCCCC',
            newline: false,
            left: Element::CURRENT_POSITION,
            top: Element::CURRENT_POSITION,
            check_page_break: false,
            padding: true,
            reset_height: false,
        );
        $text1 = new Text($style, '', 0.0);
        $text1->addText('Short');
        $box1->addElement($text1);

        $box2 = new TextBox(
            width: 180.0,
            height: 20.0,
            border: true,
            background_color: '#CCCCCC',
            newline: true,
            left: Element::CURRENT_POSITION,
            top: Element::CURRENT_POSITION,
            check_page_break: false,
            padding: true,
            reset_height: false,
        );
        $text2 = new Text($style, '', 0.0);
        $text2->addText('This text flow is intentionally long so the second text box grows taller than its minimum height.');
        $box2->addElement($text2);

        $blocks = $engine->layout([$box1, $box2]);

        $rects = [];
        foreach ($blocks as $block) {
            if ($block->data instanceof RectData) {
                $rects[] = $block;
            }
        }

        self::assertCount(2, $rects);
        self::assertGreaterThan(20.0, $rects[1]->height);
        self::assertEqualsWithDelta($rects[1]->height, $rects[0]->height, 0.01);
    }

    // --- Page break tests ---

    public function testNewPageElementCreatesPageBreak(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $cell1 = new Cell(
            width: 200.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell1->addText('Page 1');

        $page_break = new NewPageElement();

        $cell2 = new Cell(
            width: 200.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell2->addText('Page 2');

        $blocks = $engine->layout([$cell1, $page_break, $cell2]);

        self::assertCount(3, $blocks);
        // First cell on page 0
        self::assertSame(0, $blocks[0]->page);
        // Page break marker
        self::assertInstanceOf(PageBreakData::class, $blocks[1]->data);
        self::assertSame(0, $blocks[1]->page);
        // Second cell on page 1, at y=0
        self::assertSame(1, $blocks[2]->page);
        self::assertEqualsWithDelta(0.0, $blocks[2]->y, 0.01);
    }

    public function testLeadingNewPageElementIsIgnoredOnEmptyPage(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $cell = new Cell(
            width: 200.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell->addText('First page content');

        $blocks = $engine->layout([new NewPageElement(), $cell]);

        self::assertCount(1, $blocks);
        self::assertInstanceOf(TextCellData::class, $blocks[0]->data);
        self::assertSame(0, $blocks[0]->page);
        self::assertEqualsWithDelta(0.0, $blocks[0]->y, 0.01);
    }

    public function testAutoPageBreakWhenCellExceedsPageHeight(): void
    {
        // Create a short page (content height = 100pt)
        $engine = $this->createEngine(page_height: 172.0); // 172 - 36 - 36 = 100pt content
        $style = $this->makeStyle();

        $cell1 = new Cell(
            width: 200.0,
            height: 60.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell1->addText('First');

        $cell2 = new Cell(
            width: 200.0,
            height: 60.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell2->addText('Second');

        $blocks = $engine->layout([$cell1, $cell2]);

        // First cell on page 0 at y=0
        self::assertSame(0, $blocks[0]->page);
        self::assertEqualsWithDelta(0.0, $blocks[0]->y, 0.01);

        // Second cell should trigger a page break (60 + 60 = 120 > 100)
        // Find the second TextCell block
        $text_cells = array_values(array_filter($blocks, fn ($b) => $b->data instanceof TextCellData));
        self::assertCount(2, $text_cells);
        self::assertSame(1, $text_cells[1]->page);
        self::assertEqualsWithDelta(0.0, $text_cells[1]->y, 0.01);
    }

    public function testTextBoxContainerPageBreakMovesNestedBlocksToNextPage(): void
    {
        $engine = $this->createEngine(page_height: 172.0);
        $style = $this->makeStyle();

        $preface = new Cell(
            width: 200.0,
            height: 90.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $preface->addText('Preface');

        $text_box = new TextBox(
            width: 300.0,
            height: 20.0,
            border: false,
            background_color: '',
            newline: true,
            left: Element::CURRENT_POSITION,
            top: Element::CURRENT_POSITION,
            check_page_break: true,
            padding: true,
            reset_height: false,
        );

        $text = new Text($style, '', 0.0);
        $text->addText('This text box should start on page 2.');
        $text_box->addElement($text);

        $blocks = $engine->layout([$preface, $text_box]);

        $flow_blocks = array_values(array_filter($blocks, fn ($block) => $block->data instanceof TextFlowData));

        self::assertCount(1, $flow_blocks);
        self::assertSame(1, $flow_blocks[0]->page);
        self::assertLessThan(10.0, $flow_blocks[0]->y);
    }

    public function testLandscapeUsesSwappedPrintableWidth(): void
    {
        $engine = $this->createEngine(orientation: PageOrientation::Landscape);
        $style = $this->makeStyle();

        $cell = new Cell(
            width: 0.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell->addText('Landscape width');

        $blocks = $engine->layout([$cell]);

        self::assertCount(1, $blocks);
        self::assertInstanceOf(TextCellData::class, $blocks[0]->data);
        self::assertEqualsWithDelta(720.0, $blocks[0]->width, 0.01);
    }

    public function testFootnoteBodiesFlowAfterHeadingInPagedMode(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $cell = new Cell(
            width: 200.0,
            height: 100.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell->addText('Body content');

        $footnote = new Footnote($style);
        $footnote->addText('Bottom footnote');

        $footnote_texts = new FootnoteTextsElement();

        $blocks = $engine->layout([$cell, $footnote, $footnote_texts]);

        $footnote_bodies = array_values(array_filter($blocks, fn ($block) => $block->data instanceof FootnoteBodyData));
        self::assertCount(1, $footnote_bodies);

        $body = $footnote_bodies[0];
        self::assertSame(0, $body->page);
        self::assertGreaterThan(100.0, $body->y);
        self::assertLessThan(self::CONTENT_HEIGHT, $body->y);
    }

    public function testFootnoteBodiesRemainInFlowInInfiniteHeightMode(): void
    {
        $engine = $this->createEngine(page_height: 0.0);
        $style = $this->makeStyle();

        $footnote = new Footnote($style);
        $footnote->addText('Flow footnote');

        $footnote_texts = new FootnoteTextsElement();

        $blocks = $engine->layout([$footnote, $footnote_texts]);

        $footnote_refs = array_values(array_filter($blocks, fn ($block) => $block->data instanceof FootnoteRefData));
        $footnote_bodies = array_values(array_filter($blocks, fn ($block) => $block->data instanceof FootnoteBodyData));

        self::assertCount(1, $footnote_refs);
        self::assertCount(1, $footnote_bodies);
        self::assertGreaterThan($footnote_refs[0]->y, $footnote_bodies[0]->y);
        self::assertLessThan(100.0, $footnote_bodies[0]->y);
    }

    public function testTextBoxFootnoteContinuationUsesTopPageOffsetOnNextPage(): void
    {
        $engine = $this->createEngine(page_height: 172.0);
        $style = $this->makeStyle();

        $preface = new Cell(
            width: 200.0,
            height: 80.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $preface->addText('Preface');

        $text_box = new TextBox(
            width: 400.0,
            height: 0.0,
            border: false,
            background_color: '',
            newline: true,
            left: Element::CURRENT_POSITION,
            top: Element::CURRENT_POSITION,
            check_page_break: false,
            padding: true,
            reset_height: false,
        );

        $heading = new Text($style, '', 0.0);
        $heading->addText('Sources');
        $text_box->addElement($heading);

        for ($index = 0; $index < 20; $index++) {
            $footnote = new Footnote($style);
            $footnote->addText('This is long footnote line ' . $index . ' that forces pagination in a nested text box context with enough additional text to span multiple wrapped lines on a small page.');
            $text_box->addElement($footnote);
        }
        $text_box->addElement(new FootnoteTextsElement());

        $blocks = $engine->layout([$preface, $text_box]);

        $continuation_body = null;
        foreach ($blocks as $block) {
            if ($block->data instanceof FootnoteBodyData && $block->page > 0) {
                $continuation_body = $block;
                break;
            }
        }

        self::assertInstanceOf(LayoutBlock::class, $continuation_body);
        self::assertLessThan(20.0, $continuation_body->y);
    }

    // --- TextBox tests ---

    public function testTextBoxWithNestedCells(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $text_box = new TextBox(
            width: 300.0,
            height: 0.0,
            border: false,
            background_color: '',
            newline: true,
            left: Element::CURRENT_POSITION,
            top: Element::CURRENT_POSITION,
            check_page_break: false,
            padding: true,
            reset_height: false,
        );

        // Add a cell inside the text box
        $inner_cell = new Cell(
            width: 0.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $inner_cell->addText('Inside box');
        $text_box->addElement($inner_cell);

        $blocks = $engine->layout([$text_box]);

        // Should have at least one TextCell block (the inner cell)
        $text_cells = array_values(array_filter($blocks, fn ($b) => $b->data instanceof TextCellData));
        self::assertCount(1, $text_cells);

        // Inner cell should be offset by padding (2pt) from the box origin
        self::assertEqualsWithDelta(2.0, $text_cells[0]->x, 0.01);
        self::assertEqualsWithDelta(2.0, $text_cells[0]->y, 0.01);
    }

    public function testTextBoxWithBorderEmitsRect(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $text_box = new TextBox(
            width: 300.0,
            height: 50.0,
            border: true,
            background_color: '#CCCCCC',
            newline: true,
            left: Element::CURRENT_POSITION,
            top: Element::CURRENT_POSITION,
            check_page_break: false,
            padding: false,
            reset_height: false,
        );

        $blocks = $engine->layout([$text_box]);

        // Should emit a Rect block for the border/background
        $rects = array_values(array_filter($blocks, fn ($b) => $b->data instanceof RectData));
        self::assertCount(1, $rects);
        self::assertEqualsWithDelta(300.0, $rects[0]->width, 0.01);
        self::assertEqualsWithDelta(50.0, $rects[0]->height, 0.01);
    }

    // --- Image tests ---

    public function testImageAtCurrentPosition(): void
    {
        $engine = $this->createEngine();

        $image = new Image(
            src: 'data:image/png;base64,test',
            x: Element::CURRENT_POSITION,
            y: Element::CURRENT_POSITION,
            width: 100.0,
            height: 80.0,
            align: CellAlign::None,
            line: ImageContinuation::NextLine,
        );

        $blocks = $engine->layout([$image]);

        self::assertCount(1, $blocks);
        self::assertInstanceOf(ImageData::class, $blocks[0]->data);
        self::assertEqualsWithDelta(0.0, $blocks[0]->x, 0.01);
        self::assertEqualsWithDelta(0.0, $blocks[0]->y, 0.01);
        self::assertEqualsWithDelta(100.0, $blocks[0]->width, 0.01);
        self::assertEqualsWithDelta(80.0, $blocks[0]->height, 0.01);
    }

    public function testImageNextLineAdvancesY(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $image = new Image(
            src: 'data:image/png;base64,test',
            x: Element::CURRENT_POSITION,
            y: Element::CURRENT_POSITION,
            width: 100.0,
            height: 80.0,
            align: CellAlign::None,
            line: ImageContinuation::NextLine,
        );

        $cell = new Cell(
            width: 200.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell->addText('After image');

        $blocks = $engine->layout([$image, $cell]);

        $text_cells = array_values(array_filter($blocks, fn ($b) => $b->data instanceof TextCellData));
        self::assertCount(1, $text_cells);
        // Cell should appear at y=80 (below the image)
        self::assertEqualsWithDelta(80.0, $text_cells[0]->y, 0.01);
    }

    // --- Line tests ---

    public function testLineFromCurrentPosition(): void
    {
        $engine = $this->createEngine();

        $line = new Line(
            x1: Element::CURRENT_POSITION,
            y1: Element::CURRENT_POSITION,
            x2: Element::CURRENT_POSITION,
            y2: Element::CURRENT_POSITION,
        );

        $blocks = $engine->layout([$line]);

        self::assertCount(1, $blocks);
        self::assertInstanceOf(LineData::class, $blocks[0]->data);
        // x1=0 (cursor), y1=0 (cursor), x2=content_width, y2=0
        self::assertEqualsWithDelta(0.0, $blocks[0]->x, 0.01);
        self::assertEqualsWithDelta(0.0, $blocks[0]->y, 0.01);
        // Width = x2 - x1 = content_width - 0
        self::assertEqualsWithDelta(self::CONTENT_WIDTH, $blocks[0]->width, 0.01);
    }

    // --- RTL tests ---

    public function testRtlMirrorsCellPosition(): void
    {
        $engine = $this->createEngine(rtl: true);
        $style = $this->makeStyle();

        $cell = new Cell(
            width: 200.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell->addText('RTL cell');

        $blocks = $engine->layout([$cell]);

        self::assertCount(1, $blocks);
        // In RTL mode, x should be mirrored: content_width - 0 - 200 = 340
        self::assertEqualsWithDelta(self::CONTENT_WIDTH - 200.0, $blocks[0]->x, 0.01);
    }

    // --- Inline text tests ---

    public function testInlineTextFitsOnOneLine(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $text = new Text($style, '', 0.0);
        $text->addText('Short');

        $blocks = $engine->layout([$text]);

        self::assertCount(1, $blocks);
        self::assertInstanceOf(TextData::class, $blocks[0]->data);
        self::assertEqualsWithDelta(0.0, $blocks[0]->x, 0.01);
        self::assertEqualsWithDelta(0.0, $blocks[0]->y, 0.01);
        // Width should be sum of each character's width
        self::assertEqualsWithDelta(22.6, $blocks[0]->width, 0.01);
    }

    public function testLayoutPagedGroupsBlocksByPage(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $cell1 = new Cell(
            width: 200.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell1->addText('Page 1');

        $cell2 = new Cell(
            width: 200.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell2->addText('Page 2');

        $pages = $engine->layoutPaged([$cell1, new NewPageElement(), $cell2]);

        self::assertArrayHasKey(0, $pages->pages);
        self::assertArrayHasKey(1, $pages->pages);
        self::assertCount(2, $pages->pages[0]);
        self::assertCount(1, $pages->pages[1]);
        self::assertInstanceOf(PageBreakData::class, $pages->pages[0][1]->data);
    }

    public function testLayoutMatchesFlattenedLayoutPaged(): void
    {
        $engine = $this->createEngine();
        $style = $this->makeStyle();

        $cell = new Cell(
            width: 0.0,
            height: 20.0,
            border: '',
            align: CellAlign::Left,
            background_color: '',
            style: $style,
            newline: CellNewline::NextLine,
            top: Element::CURRENT_POSITION,
            left: Element::CURRENT_POSITION,
            border_color: '',
            text_color: '',
        );
        $cell->addText('Consistency');

        $layout_blocks = $engine->layout([$cell, new NewPageElement(), $cell]);
        $paged_blocks = $engine->layoutPaged([$cell, new NewPageElement(), $cell])->flatten();

        self::assertEquals($layout_blocks, $paged_blocks);
    }
}
