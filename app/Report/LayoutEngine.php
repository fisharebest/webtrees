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

use LogicException;

use function array_splice;
use function count;
use function str_replace;

/**
 * Computes positioned layout blocks from report elements and page configuration.
 *
 * The layout engine performs a single forward pass over the element tree,
 * tracking cursor position, handling page breaks, text wrapping, and
 * nested text-box containers. It produces a flat list of LayoutBlock values
 * that renderers can walk without any further layout computation.
 *
 * This replaces the cursor-based layout logic previously duplicated across
 * the HTML and PDF renderer-specific element implementations.
 */
final class LayoutEngine
{
    private const string PAGE_NUMBER_TOKEN = '#PAGENUM#';

    private const float CELL_PADDING = 2.0;

    private const float LINE_HEIGHT_RATIO = 1.25;

    /** Footnote reference numbers are rendered at this fraction of the surrounding text size. */
    public const float FOOTNOTE_REF_SIZE_RATIO = 0.75;


    private float $cursor_x = 0.0;

    private float $cursor_y = 0.0;

    private int $current_page = 0;

    private float $last_cell_height = 0.0;

    /** Bottom edge of the most recent float image (explicit left + SameLine). */
    private float $float_bottom = 0.0;

    /** Max height of the current adjacent Cell row. */
    private float $cell_row_height = 0.0;

    /** Baseline Y position of the current adjacent Cell row. */
    private float $cell_row_y = 0.0;

    /** @var list<int> Indexes of row TextCell blocks to normalize when row height grows. */
    private array $cell_row_block_indexes = [];

    /** Monotonic row identifier for grouped row output. */
    private int $next_row_id = 1;

    /** Current row identifier for adjacent Cell blocks. */
    private int|null $cell_row_id = null;

    /** Current row identifier for adjacent TextBox blocks. */
    private int|null $textbox_row_id = null;

    /** Monotonic column identifier for grouped row output. */
    private int $next_column_id = 1;

    /** Max height of the current adjacent TextBox row. */
    private float $textbox_row_height = 0.0;

    /** Baseline Y position of the current adjacent TextBox row. */
    private float $textbox_row_y = 0.0;

    /** @var list<int> Indexes of row rect blocks to normalize when row height grows. */
    private array $textbox_row_rect_indexes = [];

    /** Printable content width (page width minus margins). */
    private float $content_width;

    /** Printable content height (page height minus margins). */
    private readonly float $content_height;

    private readonly bool $rtl;

    /** @var array<int, LayoutBlock> */
    private array $blocks = [];

    /** @var list<array{text: string, style: Style}> Collected unique footnotes */
    private array $footnotes = [];

    private readonly TextWrapper $wrapper;

    public function __construct(
        private readonly TextMeasurerInterface $measurer,
        Config $config,
        private readonly bool $skip_total_pages_token = true,
    ) {
        $effective_page_width = $config->paper_width;
        $effective_page_height = $config->paper_height;

        // Landscape swaps physical width/height for paged rendering.
        // Preserve non-positive heights (HTML infinite mode) as-is.
        if ($config->orientation === PageOrientation::Landscape && $config->paper_height > 0.0) {
            $effective_page_width = $config->paper_height;
            $effective_page_height = $config->paper_width;
        }

        $this->content_width = $effective_page_width - $config->left_margin - $config->right_margin;
        $this->content_height = $effective_page_height - $config->top_margin - $config->bottom_margin;
        $this->rtl = $config->rtl;
        $this->wrapper = new TextWrapper($measurer);
    }

    /**
     * Lay out a list of body elements and return positioned blocks.
     *
     * @param list<Element> $elements
     * @param int                   $start_page Zero-based page index for the first laid-out page
     *
     * @return list<LayoutBlock>
     */
    public function layout(array $elements, int $start_page = 0): array
    {
        return $this->layoutPaged($elements, $start_page)->flatten();
    }

    /**
     * Lay out a list of body elements and return blocks grouped by page.
     *
     * @param list<Element> $elements
     */
    public function layoutPaged(array $elements, int $start_page = 0): LayoutPages
    {
        $this->blocks = [];
        $this->cursor_x = 0.0;
        $this->cursor_y = 0.0;
        $this->current_page = $start_page;
        $this->last_cell_height = 0.0;
        $this->float_bottom = 0.0;
        $this->cell_row_height = 0.0;
        $this->cell_row_y = 0.0;
        $this->cell_row_block_indexes = [];
        $this->next_row_id = 1;
        $this->cell_row_id = null;
        $this->textbox_row_id = null;
        $this->next_column_id = 1;
        $this->textbox_row_height = 0.0;
        $this->textbox_row_y = 0.0;
        $this->textbox_row_rect_indexes = [];
        $this->footnotes = [];

        foreach ($elements as $element) {
            $this->layoutElement($element);
        }

        /** @var array<int, list<LayoutBlock>> $pages */
        $pages = [];
        foreach ($this->blocks as $block) {
            $pages[$block->page] ??= [];
            $pages[$block->page][] = $block;
        }

        return new LayoutPages($pages);
    }

    /**
     * Dispatch a single element to the appropriate layout method.
     */
    private function layoutElement(Element $element): void
    {
        if ($element instanceof Cell) {
            $this->layoutCell($element);
        } elseif ($element instanceof TextBox) {
            $this->layoutTextBox($element);
        } elseif ($element instanceof Text) {
            $this->layoutText($element);
        } elseif ($element instanceof Image) {
            $this->layoutImage($element);
        } elseif ($element instanceof Line) {
            $this->layoutLine($element);
        } elseif ($element instanceof NewPageElement) {
            $this->newPage();
        } elseif ($element instanceof FootnoteTextsElement) {
            $this->layoutFootnoteBodies();
        } elseif ($element instanceof Footnote) {
            $this->layoutFootnoteRef($element);
        }
    }

    /**
     * Resolve placeholder tokens in element text.
     *
     * Replaces the page-number token with the current page number.
     * Strips the underline delimiters «/» which are handled by inline
     * markup in the writer.
     */
    private function resolveText(string $text): string
    {
        return str_replace(
            [self::PAGE_NUMBER_TOKEN, '«', '»'],
            [(string) ($this->current_page + 1), '', ''],
            $text
        );
    }

    /**
     * Layout a Cell element: a text block with border, alignment, and cursor behavior.
     */
    private function layoutCell(Cell $cell): void
    {
        // HTML output does not have a page count and must drop this token.
        if ($this->shouldSkipTotalPagesToken($cell)) {
            return;
        }

        $width = $cell->width;
        $height = $cell->height;

        // Resolve position
        $x = $cell->left === Element::CURRENT_POSITION ? $this->cursor_x : $cell->left;
        $y = $cell->top === Element::CURRENT_POSITION ? $this->cursor_y : $cell->top;

        if ($this->cursor_x === 0.0 || $y !== $this->cell_row_y) {
            $this->cell_row_y = $y;
            $this->cell_row_height = 0.0;
            $this->cell_row_block_indexes = [];
            $this->cell_row_id = $this->next_row_id++;
        }

        // Resolve width
        $remaining = $this->content_width - $this->cursor_x;
        if ($width === 0.0 || $width > $remaining) {
            $width = $remaining;
        }

        // Get text with tokens resolved
        $text = $this->resolveText($cell->getValue());
        $content_width = $width - self::CELL_PADDING * 2;

        if ($text !== '') {
            $text_height = $this->wrapper->textHeight($text, $content_width, $cell->style, self::LINE_HEIGHT_RATIO);
            $text_height += self::CELL_PADDING * 2;
            if ($text_height > $height) {
                $height = $text_height;
            }
        }
        if ($height < $this->cell_row_height) {
            $height = $this->cell_row_height;
        }

        // Check page break
        if ($this->needsPageBreak($height)) {
            $this->newPage();
            $y = $this->cursor_y;
        }

        // Emit the block
        $this->blocks[] = LayoutBlock::textCell(
            page: $this->current_page,
            x: $this->rtl ? $this->content_width - $x - $width : $x,
            y: $y,
            width: $width,
            height: $height,
            text: $text,
            style: $cell->style,
            align: $cell->align,
            background_color: $cell->background_color,
            border: $cell->border,
            border_color: $cell->border_color,
            text_color: $cell->text_color,
            url: $cell->url,
        )->withRowColumn($this->cell_row_id, $this->next_column_id++);

        $row_block_index = count($this->blocks) - 1;

        if ($height > $this->cell_row_height) {
            $this->cell_row_height = $height;
            foreach ($this->cell_row_block_indexes as $block_index) {
                $this->blocks[$block_index] = $this->blocks[$block_index]->withHeight($this->cell_row_height);
            }
        }

        $this->cell_row_block_indexes[] = $row_block_index;

        // Advance cursor based on newline behavior.
        switch ($cell->newline) {
            case CellNewline::Right:
                $this->cursor_x = $x + $width;
                $this->cursor_y = $y;
                $this->last_cell_height = $this->cell_row_height;
                break;

            case CellNewline::NextLine:
                $this->cursor_x = 0.0;
                $this->cursor_y = $y + $this->cell_row_height;
                $this->last_cell_height = 0.0;
                $this->cell_row_height = 0.0;
                $this->cell_row_block_indexes = [];
                $this->cell_row_id = null;
                break;

            case CellNewline::Below:
                $this->cursor_x = $x + $width;
                $this->cursor_y = $y + $this->cell_row_height;
                $this->last_cell_height = 0.0;
                $this->cell_row_height = 0.0;
                $this->cell_row_block_indexes = [];
                $this->cell_row_id = null;
                break;
        }
    }

    /**
     * Layout a TextBox element: a container with nested elements.
     *
     * Text and FootnoteRef elements are collected into a single TextFlow block
     * so the browser can handle word-wrapping. Non-text elements (images, nested
     * text-boxes) interrupt the flow and are laid out individually.
     */
    private function layoutTextBox(TextBox $text_box): void
    {
        $box_width = $text_box->width;

        // A "float TextBox" has an explicit left position, non-zero width, and
        // reset_height=true. It renders at a fixed position (e.g., photos column)
        // without advancing Y. Clear any previous float before placing a new one.
        $is_float_box = $text_box->reset_height
            && $text_box->left !== Element::CURRENT_POSITION
            && $text_box->width > 0.0;

        if ($is_float_box && $this->cursor_y < $this->float_bottom) {
            $this->cursor_y = $this->float_bottom;
        }

        // Resolve position
        $box_x = $text_box->left === Element::CURRENT_POSITION ? $this->cursor_x : $text_box->left;
        $box_y = $text_box->top === Element::CURRENT_POSITION ? $this->cursor_y : $text_box->top;
        $box_start_page = $this->current_page;

        if ($this->cursor_x === 0.0 || $box_y !== $this->textbox_row_y) {
            $this->textbox_row_y = $box_y;
            $this->textbox_row_height = 0.0;
            $this->textbox_row_rect_indexes = [];
            $this->textbox_row_id = $this->next_row_id++;
        }

        $textbox_column_id = $this->next_column_id++;

        // Resolve width
        $remaining = $this->content_width - ($text_box->left === Element::CURRENT_POSITION ? $this->cursor_x : $box_x);
        if ($box_width === 0.0 || $box_width > $remaining) {
            $box_width = $remaining;
        }

        $padding = $text_box->padding ? self::CELL_PADDING : 0.0;
        $inner_width = $box_width - $padding * 2;

        // Save cursor state
        $saved_x = $this->cursor_x;
        $saved_y = $this->cursor_y;
        $saved_content_width = $this->content_width;
        $saved_last_cell_height = $this->last_cell_height;

        // Set up nested layout context
        $this->cursor_x = 0.0;
        $this->cursor_y = 0.0;
        $this->content_width = $inner_width;
        $this->last_cell_height = 0.0;

        // Collect inline elements (text + footnotes) into text runs.
        // Non-text elements flush the current flow and are laid out individually.
        $child_blocks_start = count($this->blocks);
        /** @var list<TextRun|FootnoteRefData> $runs */
        $runs = [];

        foreach ($text_box->elements as $child) {
            if ($child instanceof Text) {
                if ($this->shouldSkipTotalPagesToken($child)) {
                    continue;
                }

                $child_text = $this->resolveText($child->getValue());

                if ($child_text === '') {
                    continue;
                }

                if ($child->truncate > 0.0) {
                    $child_text = $this->measurer->truncate($child_text, $child->truncate, $child->style);
                }

                $runs[] = new TextRun($child_text, $child->style, $child->color, $child->url);
            } elseif ($child instanceof Footnote) {
                // Register the footnote and add an inline reference to the flow
                $footnote_text = $this->resolveText($child->getValue());

                $number = 0;
                foreach ($this->footnotes as $index => $existing) {
                    if ($existing['text'] === $footnote_text) {
                        $number = $index + 1;
                        break;
                    }
                }
                if ($number === 0) {
                    $this->footnotes[] = ['text' => $footnote_text, 'style' => $child->style];
                    $number = count($this->footnotes);
                }
                $child->setNumber($number);

                // Derive ref size from the most recent text run's font size
                $base_size = 0.0;
                for ($i = count($runs) - 1; $i >= 0; $i--) {
                    if ($runs[$i] instanceof TextRun) {
                        $base_size = $runs[$i]->style->size;
                        break;
                    }
                }
                if ($base_size === 0.0) {
                    $base_size = $child->style->size;
                }
                $ref_size = $base_size * self::FOOTNOTE_REF_SIZE_RATIO;
                $ref_style = new Style(name: 'footnoteref', style: '', size: $ref_size);
                $runs[] = new FootnoteRefData(
                    number: $number,
                    link_target: (string) $number,
                    style: $ref_style,
                );
            } else {
                // Flush any collected runs before a non-inline element
                if ($runs !== []) {
                    $this->emitTextFlow($runs);
                    $runs = [];
                }
                $this->layoutElement($child);
            }
        }

        // Flush remaining runs
        if ($runs !== []) {
            $this->emitTextFlow($runs);
        }

        // Compute box height from children
        $child_end_page = $this->current_page;
        $child_end_cursor_y = $this->cursor_y;
        $inner_height = $this->cursor_y;
        if ($inner_height < $this->last_cell_height) {
            $inner_height = $this->last_cell_height;
        }
        $box_height = $text_box->height;
        if ($inner_height + $padding * 2 > $box_height) {
            $box_height = $inner_height + $padding * 2;
        }

        // Keep all adjacent boxes in a row at the same height.
        if ($box_height < $this->textbox_row_height) {
            $box_height = $this->textbox_row_height;
        }

        // Restore parent context
        $this->content_width = $saved_content_width;
        $this->cursor_x = $saved_x;
        $this->cursor_y = $saved_y;
        $this->last_cell_height = $saved_last_cell_height;

        // Check page break for the whole box
        // Child layout may have already paginated (for example via FootnoteTexts).
        // In that case, do not apply an additional container-level page break.
        if ($text_box->check_page_break && $child_end_page === $box_start_page && $this->needsPageBreak($box_height)) {
            $this->newPage();
            $box_y = $this->cursor_y;

            // Child blocks were laid out before the container-level break check.
            // When the box is moved to the next page, move those child blocks too.
            $block_count = count($this->blocks);
            for ($i = $child_blocks_start; $i < $block_count; $i++) {
                $this->blocks[$i] = $this->blocks[$i]->withPage($this->current_page);
            }
        }

        // Offset child blocks to their absolute position within the page
        $offset_x = ($this->rtl ? $this->content_width - $box_x - $box_width : $box_x) + $padding;
        $block_count = count($this->blocks);
        for ($i = $child_blocks_start; $i < $block_count; $i++) {
            $offset_y = $this->blocks[$i]->page === $box_start_page
                ? $box_y + $padding
                : $padding;
            $this->blocks[$i] = $this->offsetBlock($this->blocks[$i], $offset_x, $offset_y);
            $this->blocks[$i] = $this->blocks[$i]->withRowColumn($this->textbox_row_id, $textbox_column_id);
        }

        // Insert background/border rect before the children so it renders behind them
        $row_rect_index = null;
        if ($text_box->background_color !== '' || $text_box->border) {
            $rect_x = $this->rtl ? $this->content_width - $box_x - $box_width : $box_x;
            $rect_block = LayoutBlock::rect(
                page: $this->current_page,
                x: $rect_x,
                y: $box_y,
                width: $box_width,
                height: $box_height,
                background_color: $text_box->background_color,
                border: $text_box->border,
            );
            array_splice($this->blocks, $child_blocks_start, 0, [$rect_block]);
            $this->blocks[$child_blocks_start] = $this->blocks[$child_blocks_start]->withRowColumn($this->textbox_row_id, $textbox_column_id);
            $row_rect_index = $child_blocks_start;
        }

        if ($box_height > $this->textbox_row_height) {
            $this->textbox_row_height = $box_height;
            foreach ($this->textbox_row_rect_indexes as $rect_index) {
                $this->blocks[$rect_index] = $this->blocks[$rect_index]->withHeight($this->textbox_row_height);
            }
        }

        if ($row_rect_index !== null) {
            $this->textbox_row_rect_indexes[] = $row_rect_index;
        }


        // Advance cursor after the text box.
        // Box height already includes padding, so no extra padding is added.
        if ($is_float_box) {
            // Float behavior: the box renders but does not advance Y.
            // Record float bottom so subsequent floats will clear past it.
            $this->float_bottom = max($this->float_bottom, $box_y + $box_height);
            $this->cursor_x = 0.0;
            $this->cursor_y = $box_y;
            $this->last_cell_height = 0.0;
            $this->textbox_row_height = 0.0;
            $this->textbox_row_rect_indexes = [];
            $this->textbox_row_id = null;
        } elseif ($text_box->reset_height) {
            // Cursor-reset behavior: zero-width reset TextBox moves cursor to
            // left margin without advancing Y (used after HighlightedImage).
            $this->cursor_x = 0.0;
            $this->cursor_y = $box_y;
            $this->last_cell_height = 0.0;
            $this->textbox_row_height = 0.0;
            $this->textbox_row_rect_indexes = [];
            $this->textbox_row_id = null;
        } elseif ($child_end_page > $box_start_page) {
            $this->cursor_x = 0.0;
            $this->cursor_y = $child_end_cursor_y;
            $this->last_cell_height = 0.0;
            $this->textbox_row_height = 0.0;
            $this->textbox_row_rect_indexes = [];
            $this->textbox_row_id = null;
        } elseif ($text_box->newline) {
            $this->cursor_x = 0.0;
            $this->cursor_y = $box_y + $this->textbox_row_height;
            $this->last_cell_height = 0.0;
            $this->textbox_row_height = 0.0;
            $this->textbox_row_rect_indexes = [];
            $this->textbox_row_id = null;
        } else {
            $this->cursor_x = $box_x + $box_width;
            $this->cursor_y = $box_y;
            $this->last_cell_height = $this->textbox_row_height;
        }
    }

    /**
     * Layout inline flowing text.
     */
    private function layoutText(Text $text_element): void
    {
        if ($this->shouldSkipTotalPagesToken($text_element)) {
            return;
        }

        $text = $this->resolveText($text_element->getValue());
        if ($text === '') {
            return;
        }


        if ($text_element->truncate > 0.0) {
            $text = $this->measurer->truncate($text, $text_element->truncate, $text_element->style);
        }

        $available_width = $this->content_width - $this->cursor_x;

        $text_width = $this->measurer->getStringWidth($text, $text_element->style);
        $line_height = $text_element->style->size * self::LINE_HEIGHT_RATIO;

        if ($text_width <= $available_width) {
            // Fits on current line
            $this->blocks[] = LayoutBlock::text(
                page: $this->current_page,
                x: $this->rtl ? $this->content_width - $this->cursor_x - $text_width : $this->cursor_x,
                y: $this->cursor_y,
                width: $text_width,
                height: $line_height,
                text: $text,
                style: $text_element->style,
                color: $text_element->color,
            );
            $this->cursor_x += $text_width;
        } else {
            // Needs wrapping
            $lines = $this->wrapper->wrapText($text, $text_element->style, $available_width);
            $x = $this->cursor_x;
            $y = $this->cursor_y;

            foreach ($lines as $index => $line) {
                if ($line === '') {
                    $y += $line_height;
                    $x = 0.0;
                    continue;
                }

                $line_width = $this->measurer->getStringWidth($line, $text_element->style);
                $this->blocks[] = LayoutBlock::text(
                    page: $this->current_page,
                    x: $this->rtl ? $this->content_width - $x - $line_width : $x,
                    y: $y,
                    width: $line_width,
                    height: $line_height,
                    text: $line,
                    style: $text_element->style,
                    color: $text_element->color,
                );

                if ($index < count($lines) - 1) {
                    $y += $line_height;
                    $x = 0.0;
                } else {
                    $x += $line_width;
                }
            }

            $this->cursor_x = $x;
            $this->cursor_y = $y;
        }
    }

    /**
     * Emit a TextFlow block containing multiple styled runs.
     *
     * The block is positioned at the current cursor and spans the full
     * content width. The browser handles all word-wrapping within the block.
     * Height is estimated for layout purposes (container sizing).
     *
     * @param list<TextRun|FootnoteRefData> $runs
     */
    private function emitTextFlow(array $runs): void
    {
        // Concatenate all text to estimate total height
        $total_text = '';
        $dominant_style = null;

        foreach ($runs as $run) {
            if ($run instanceof TextRun) {
                $total_text .= $run->text;
            } else {
                // Footnote ref contributes a small amount of text
                $total_text .= $run->number;
            }

            $dominant_style ??= $run->style;
        }

        if ($total_text === '' || $dominant_style === null) {
            return;
        }

        // Estimate height using the wrapper
        $flow_width = $this->content_width;
        $text_height = $this->wrapper->textHeight($total_text, $flow_width, $dominant_style, self::LINE_HEIGHT_RATIO);

        $this->blocks[] = LayoutBlock::textFlow(
            page: $this->current_page,
            x: 0.0,
            y: $this->cursor_y,
            width: $flow_width,
            height: $text_height,
            runs: $runs,
        );

        $this->cursor_y += $text_height;
        $this->cursor_x = 0.0;
        $this->last_cell_height = $text_height;
    }

    /**
     * Layout an image element.
     */
    private function layoutImage(Image $image): void
    {
        // Clear any previous float before placing a new float image.
        // A float is an image with an explicit left position and SameLine continuation,
        // meaning it does not advance the Y cursor. Without clearing, consecutive
        // float images (e.g., photos for successive individuals) would overlap.
        if ($image->x !== Element::CURRENT_POSITION && $image->line === ImageContinuation::SameLine) {
            if ($this->cursor_y < $this->float_bottom) {
                $this->cursor_y = $this->float_bottom;
            }
        }

        $x = $image->x === Element::CURRENT_POSITION ? $this->cursor_x : $image->x;
        $y = $image->y === Element::CURRENT_POSITION ? $this->cursor_y : $image->y;

        if ($this->needsPageBreak($image->height)) {
            $this->newPage();
            $y = $this->cursor_y;
        }

        $this->blocks[] = LayoutBlock::image(
            page: $this->current_page,
            x: $this->rtl ? $this->content_width - $x - $image->width : $x,
            y: $y,
            width: $image->width,
            height: $image->height,
            src: $image->src,
            align: $image->align,
            link: $image->link,
        );

        if ($image->line === ImageContinuation::NextLine) {
            $this->cursor_x = 0.0;
            $this->cursor_y = $y + $image->height;
        } else {
            $this->cursor_x = $x + $image->width;
            // Record the float bottom so subsequent floats will clear past it.
            if ($image->x !== Element::CURRENT_POSITION) {
                $this->float_bottom = max($this->float_bottom, $y + $image->height);
            }
        }
    }

    /**
     * Layout a line element.
     */
    private function layoutLine(Line $line_element): void
    {
        $x1 = $line_element->x1 === Element::CURRENT_POSITION ? $this->cursor_x : $line_element->x1;
        $y1 = $line_element->y1 === Element::CURRENT_POSITION ? $this->cursor_y : $line_element->y1;
        $x2 = $line_element->x2 === Element::CURRENT_POSITION ? $this->content_width : $line_element->x2;
        $y2 = $line_element->y2 === Element::CURRENT_POSITION ? $this->cursor_y : $line_element->y2;

        if ($this->rtl) {
            $x1 = $this->content_width - $x1;
            $x2 = $this->content_width - $x2;
        }

        $this->blocks[] = LayoutBlock::line(
            page: $this->current_page,
            x1: $x1,
            y1: $y1,
            x2: $x2,
            y2: $y2,
        );
    }

    /**
     * Layout a footnote reference (inline superscript number).
     *
     * Assigns a number to the footnote (deduplicating by text content)
     * and emits an inline superscript reference block.
     */
    private function layoutFootnoteRef(Footnote $footnote): void
    {
        $footnote_text = $this->resolveText($footnote->getValue());

        // Deduplicate: check if this footnote text already exists
        $number = 0;
        foreach ($this->footnotes as $index => $existing) {
            if ($existing['text'] === $footnote_text) {
                $number = $index + 1;
                break;
            }
        }

        // If not found, register as a new footnote
        if ($number === 0) {
            $this->footnotes[] = ['text' => $footnote_text, 'style' => $footnote->style];
            $number = count($this->footnotes);
        }

        // Assign the number back to the element (for potential old-style fallback)
        $footnote->setNumber($number);

        $number_text = (string) $number;
        $ref_size = $footnote->style->size * self::FOOTNOTE_REF_SIZE_RATIO;
        $style = new Style(name: 'footnoteref', style: '', size: $ref_size);
        $width = $this->measurer->getStringWidth($number_text, $style);
        $height = $style->size * self::LINE_HEIGHT_RATIO;

        $this->blocks[] = LayoutBlock::footnoteRef(
            page: $this->current_page,
            x: $this->rtl ? $this->content_width - $this->cursor_x - $width : $this->cursor_x,
            y: $this->cursor_y,
            width: $width,
            height: $height,
            number: $number,
            link_target: (string) $number,
            style: $style,
        );

        $this->cursor_x += $width;
    }

    /**
     * Emit all collected footnote bodies at the current cursor position.
     *
     * Called when a FootnoteTextsElement is encountered in the element stream.
     * Each footnote is rendered as a numbered text block.
     */
    private function layoutFootnoteBodies(): void
    {
        if ($this->footnotes === []) {
            return;
        }

        // Move to the next line if there is preceding inline content
        if ($this->cursor_x > 0.0) {
            $this->cursor_y += $this->last_cell_height > 0
                ? $this->last_cell_height
                : 10.0;
            $this->cursor_x = 0.0;
        }

        $footnote_style = new Style(name: 'footnote', style: '', size: 8.0);

        /** @var list<array{number:int,text:string,style:Style,height:float}> $footnote_entries */
        $footnote_entries = [];

        foreach ($this->footnotes as $index => $footnote) {
            $number = $index + 1;
            $text = $number . '. ' . $footnote['text'];
            $text_height = $this->wrapper->textHeight($text, $this->content_width, $footnote_style, self::LINE_HEIGHT_RATIO);

            $footnote_entries[] = [
                'number' => $number,
                'text' => $footnote['text'],
                'style' => $footnote['style'],
                'height' => $text_height,
            ];
        }

        foreach ($footnote_entries as $entry) {
            if ($this->needsPageBreak($entry['height'])) {
                $this->newPage();
            }

            $this->emitFootnoteBodyBlock($entry['number'], $entry['text'], $entry['style'], $entry['height']);
            $this->cursor_y += $entry['height'];
        }
    }

    private function emitFootnoteBodyBlock(int $number, string $text, Style $style, float $height): void
    {
        $this->blocks[] = LayoutBlock::footnoteBody(
            page: $this->current_page,
            x: 0.0,
            y: $this->cursor_y,
            width: $this->content_width,
            height: $height,
            number: $number,
            text: $text,
            link_target: (string) $number,
            style: $style,
        );
    }

    /**
     * Trigger a page break and reset cursor to top of new page.
     *
     * In infinite-height mode (HTML), the cursor advances with spacing
     * rather than resetting to zero, since all content shares a single
     * continuous coordinate space.
     */
    private function newPage(): void
    {
        // Legacy templates may start with <newpage/> to force creation of the
        // first page in cursor-based rendering. In block-based layout, page 0
        // already exists conceptually, so a break before any content is a no-op.
        if ($this->cursor_x === 0.0 && $this->cursor_y === 0.0 && !$this->hasContentOnCurrentPage()) {
            return;
        }

        $this->blocks[] = LayoutBlock::pageBreak($this->current_page);
        $this->current_page++;
        $this->cursor_x = 0.0;

        if ($this->content_height <= 0) {
            // HTML mode: keep advancing vertically with a visual break
            $this->cursor_y += 10.0;
        } else {
            // PDF mode: reset to top of the new page
            $this->cursor_y = 0.0;
        }

        $this->last_cell_height = 0.0;
        $this->float_bottom = 0.0;
    }

    private function hasContentOnCurrentPage(): bool
    {
        for ($index = count($this->blocks) - 1; $index >= 0; $index--) {
            $block = $this->blocks[$index];

            if ($block->page !== $this->current_page) {
                break;
            }

            if (!$block->data instanceof PageBreakData) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether adding content of the given height would overflow the page.
     */
    private function needsPageBreak(float $height): bool
    {
        // Content height of 0 means infinite (HTML mode)
        if ($this->content_height <= 0) {
            return false;
        }

        return $this->cursor_y + $height > $this->content_height;
    }

    /**
     * Offset an already-emitted block by the given amounts (for text-box nesting).
     */
    private function offsetBlock(LayoutBlock $block, float $offset_x, float $offset_y): LayoutBlock
    {
        $data = $block->data;

        if ($data instanceof TextCellData) {
            return LayoutBlock::textCell(
                page: $block->page,
                x: $block->x + $offset_x,
                y: $block->y + $offset_y,
                width: $block->width,
                height: $block->height,
                text: $data->text,
                style: $data->style,
                align: $data->align,
                background_color: $data->background_color,
                border: $data->border,
                border_color: $data->border_color,
                text_color: $data->text_color,
                url: $data->url,
            );
        }

        if ($data instanceof TextData) {
            return LayoutBlock::text(
                page: $block->page,
                x: $block->x + $offset_x,
                y: $block->y + $offset_y,
                width: $block->width,
                height: $block->height,
                text: $data->text,
                style: $data->style,
                color: $data->color,
            );
        }

        if ($data instanceof TextFlowData) {
            return LayoutBlock::textFlow(
                page: $block->page,
                x: $block->x + $offset_x,
                y: $block->y + $offset_y,
                width: $block->width,
                height: $block->height,
                runs: $data->runs,
            );
        }

        if ($data instanceof ImageData) {
            return LayoutBlock::image(
                page: $block->page,
                x: $block->x + $offset_x,
                y: $block->y + $offset_y,
                width: $block->width,
                height: $block->height,
                src: $data->src,
                align: $data->align,
                link: $data->link,
            );
        }

        if ($data instanceof LineData) {
            return LayoutBlock::line(
                page: $block->page,
                x1: $data->x1 + $offset_x,
                y1: $data->y1 + $offset_y,
                x2: $data->x2 + $offset_x,
                y2: $data->y2 + $offset_y,
            );
        }

        if ($data instanceof RectData) {
            return LayoutBlock::rect(
                page: $block->page,
                x: $block->x + $offset_x,
                y: $block->y + $offset_y,
                width: $block->width,
                height: $block->height,
                background_color: $data->background_color,
                border: $data->border,
            );
        }

        if ($data instanceof FootnoteRefData) {
            return LayoutBlock::footnoteRef(
                page: $block->page,
                x: $block->x + $offset_x,
                y: $block->y + $offset_y,
                width: $block->width,
                height: $block->height,
                number: $data->number,
                link_target: $data->link_target,
                style: $data->style,
            );
        }

        if ($data instanceof FootnoteBodyData) {
            return LayoutBlock::footnoteBody(
                page: $block->page,
                x: $block->x + $offset_x,
                y: $block->y + $offset_y,
                width: $block->width,
                height: $block->height,
                number: $data->number,
                text: $data->text,
                link_target: $data->link_target,
                style: $data->style,
            );
        }

        if ($data instanceof PageBreakData) {
            return $block;
        }

        throw new LogicException('Unsupported layout block data class: ' . $data::class);
    }


    private function shouldSkipTotalPagesToken(Element $element): bool
    {
        return $this->skip_total_pages_token && $element->containsTotalPages();
    }
}
