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
use RuntimeException;

use function explode;
use function ltrim;
use function mb_strlen;
use function mb_substr;
use function sprintf;

/**
 * Renders layout blocks to the PDF backend.
 *
 * This class is a Phase-3 migration target: move PDF output from element-level
 * render() methods to direct block rendering, mirroring HtmlWriter.
 */
final class PdfBlockWriter
{
    private const float LINE_HEIGHT_RATIO = 1.25;

    /** @var array<string, int> */
    private array $footnote_links = [];

    /**
     * @param list<LayoutBlock> $blocks
     */
    public function render(PdfRenderTargetInterface $renderer, array $blocks, float $origin_x = 0.0, float $origin_y = 0.0): void
    {
        $this->footnote_links = [];

        foreach ($blocks as $block) {
            $this->ensurePage($renderer, $block->page);
            $this->renderBlock($renderer, $block, $origin_x, $origin_y);
        }
    }

    /**
     * Render blocks directly on the already-selected page.
     *
     * @param list<LayoutBlock> $blocks
     */
    public function renderCurrentPage(PdfRenderTargetInterface $renderer, array $blocks, float $origin_x = 0.0, float $origin_y = 0.0): void
    {
        $this->footnote_links = [];

        foreach ($blocks as $block) {
            $this->renderBlock($renderer, $block, $origin_x, $origin_y);
        }
    }

    private function ensurePage(PdfRenderTargetInterface $renderer, int $target_page): void
    {
        $current_page = $renderer->getPageIndex();

        if ($target_page < $current_page) {
            throw new RuntimeException(sprintf('Layout blocks must be rendered in page order. Current page: %d, target page: %d', $current_page, $target_page));
        }

        // Layout blocks and PdfRenderer both use zero-based page indexes.
        while ($renderer->getPageIndex() < $target_page) {
            $renderer->newPage();
        }
    }

    private function renderBlock(PdfRenderTargetInterface $renderer, LayoutBlock $block, float $origin_x, float $origin_y): void
    {
        match ($block->data::class) {
            TextCellData::class => $this->renderTextCell($renderer, $block, $origin_x, $origin_y),
            TextData::class => $this->renderText($renderer, $block, $origin_x, $origin_y),
            TextFlowData::class => $this->renderTextFlow($renderer, $block, $origin_x, $origin_y),
            RectData::class => $this->renderRect($renderer, $block, $origin_x, $origin_y),
            LineData::class => $this->renderLine($renderer, $block, $origin_x, $origin_y),
            ImageData::class => $this->renderImage($renderer, $block, $origin_x, $origin_y),
            FootnoteRefData::class => $this->renderFootnoteRef($renderer, $block, $origin_x, $origin_y),
            FootnoteBodyData::class => $this->renderFootnoteBody($renderer, $block, $origin_x, $origin_y),
            PageBreakData::class => null,
            default => throw new LogicException('Unsupported layout block data class: ' . $block->data::class),
        };
    }

    private function renderTextCell(PdfRenderTargetInterface $renderer, LayoutBlock $block, float $origin_x, float $origin_y): void
    {
        $data = $block->data;
        assert($data instanceof TextCellData);
        $x = $origin_x + $block->x;
        $y = $origin_y + $block->y;

        $renderer->setCurrentStyle($data->style);

        if ($data->text_color !== '') {
            $this->applyHexTextColor($renderer, $data->text_color);
        }

        $rect_style = '';
        if ($data->border !== '') {
            if ($data->border_color !== '') {
                $this->applyHexDrawColor($renderer, $data->border_color);
            }
            $rect_style .= 'D';
        }
        if ($data->background_color !== '') {
            $this->applyHexFillColor($renderer, $data->background_color);
            $rect_style .= 'F';
        }
        if ($rect_style !== '') {
            $renderer->drawRect($x, $y, $block->width, $block->height, $rect_style);
        }

        $renderer->drawTextBlock(
            text: $data->text,
            x: $x,
            y: $y,
            width: $block->width,
            height: $block->height,
            align: $this->alignToPdf($data->align),
            line_height: $data->style->size * self::LINE_HEIGHT_RATIO,
        );

        if ($data->url !== '') {
            $renderer->addLinkArea($x, $y, $block->width, $block->height, $data->url);
        }

        $renderer->resetColors();
    }

    private function renderText(PdfRenderTargetInterface $renderer, LayoutBlock $block, float $origin_x, float $origin_y): void
    {
        $data = $block->data;
        assert($data instanceof TextData);
        $x = $origin_x + $block->x;
        $y = $origin_y + $block->y;

        $renderer->setCurrentStyle($data->style);
        if ($data->color !== '') {
            $this->applyHexTextColor($renderer, $data->color);
        }
        $renderer->drawTextBlock(
            text: $data->text,
            x: $x,
            y: $y,
            width: $block->width,
            height: $block->height,
            align: $renderer->isRTL() ? 'R' : 'L',
            line_height: $block->height,
            with_padding: false,
        );
        $renderer->resetColors();
    }

    private function renderTextFlow(PdfRenderTargetInterface $renderer, LayoutBlock $block, float $origin_x, float $origin_y): void
    {
        $data = $block->data;
        assert($data instanceof TextFlowData);
        $x = $origin_x + $block->x;
        $y = $origin_y + $block->y;

        $cursor_x = 0.0;
        $cursor_y = 0.0;
        $line_height = $this->dominantLineHeight($data);

        foreach ($data->runs as $run) {
            if ($run instanceof TextRun) {
                $cursor_y = $this->renderTextFlowRun($renderer, $run, $x, $y, $block->width, $block->height, $line_height, $cursor_x, $cursor_y);
            } elseif ($run instanceof FootnoteRefData) {
                $cursor_y = $this->renderTextFlowFootnoteRef($renderer, $run, $x, $y, $block->width, $block->height, $line_height, $cursor_x, $cursor_y);
            }
        }
    }

    private function dominantLineHeight(TextFlowData $data): float
    {
        // Keep PDF flow line height aligned with LayoutEngine::emitTextFlow(),
        // which estimates TextFlow height from the first available run style.
        $first_run = $data->runs[0] ?? null;

        if ($first_run === null) {
            throw new LogicException('TextFlowData must contain at least one run.');
        }

        return $first_run->style->size * self::LINE_HEIGHT_RATIO;
    }

    private function renderTextFlowRun(
        PdfRenderTargetInterface $renderer,
        TextRun $run,
        float $base_x,
        float $base_y,
        float $block_width,
        float $block_height,
        float $line_height,
        float &$cursor_x,
        float $cursor_y,
    ): float {
        $renderer->setCurrentStyle($run->style);
        if ($run->color !== '') {
            $this->applyHexTextColor($renderer, $run->color);
        }

        $text = $run->text;
        if ($text === '') {
            $renderer->resetColors();
            return $cursor_y;
        }

        // Track position before rendering so we can overlay a link area.
        $start_x = $cursor_x;
        $start_y = $cursor_y;

        // Split on explicit newlines (from <br/>) so cursor tracking stays
        // synchronized with the visual output.  Each segment between newlines
        // is rendered as a separate inline block; newlines advance to a new line.
        $segments = explode("\n", $text);

        foreach ($segments as $index => $segment) {
            if ($index > 0) {
                // Newline: advance to the next line.
                $cursor_x = 0.0;
                $cursor_y += $line_height;
            }

            if ($segment === '') {
                continue;
            }

            $cursor_y = $this->renderTextFlowSegmentInline(
                $renderer,
                $segment,
                $run->style,
                $base_x,
                $base_y,
                $block_width,
                $block_height,
                $line_height,
                $cursor_x,
                $cursor_y,
            );
        }

        // Add a clickable link area over the rendered text when a URL is set.
        if ($run->url !== '') {
            $link_width = $renderer->getStringWidth($text);
            $renderer->addLinkArea($base_x + $start_x, $base_y + $start_y, $link_width, $line_height, $run->url);
        }

        $renderer->resetColors();

        return $cursor_y;
    }

    /**
     * Render a single text segment (no newlines) inline in the text flow.
     *
     * Passes the complete segment to drawTextBlock so that tc-lib-pdf's Bidi
     * algorithm operates on full phrases.  The cursor is advanced by the
     * actual text width consumed.
     */
    private function renderTextFlowSegmentInline(
        PdfRenderTargetInterface $renderer,
        string $text,
        Style $style,
        float $base_x,
        float $base_y,
        float $block_width,
        float $block_height,
        float $line_height,
        float &$cursor_x,
        float $cursor_y,
    ): float {
        $available_width = $block_width - $cursor_x;

        $text_width = $renderer->getStringWidth($text);

        // If the segment fits on the current line, render it inline.
        if ($text_width <= $available_width || $cursor_x === 0.0) {
            if ($cursor_y + $line_height <= $block_height + 0.0001) {
                // Use the full available width for the text cell so that
                // TextWrapper does not spuriously wrap due to floating-point
                // differences between whole-string and word-by-word measurement.
                $cell_width = $text_width <= $available_width ? $available_width : $block_width;

                // Position the run's cell according to page direction.
                if ($renderer->isRTL()) {
                    $draw_x = $base_x + $block_width - $cursor_x - $cell_width;
                } else {
                    $draw_x = $base_x + $cursor_x;
                }

                $renderer->drawTextBlock(
                    text: $text,
                    x: $draw_x,
                    y: $base_y + $cursor_y,
                    width: $cell_width,
                    height: $block_height - $cursor_y,
                    align: $renderer->isRTL() ? 'R' : 'L',
                    line_height: $line_height,
                    with_padding: false,
                );
            }

            // Advance cursor by actual text width for subsequent inline content.
            if ($text_width <= $available_width) {
                $cursor_x += $text_width;
            } else {
                // Keep cursor position aligned with the wrapped output.
                [$line_count, $last_line_width] = $this->wrappedTextMetrics($renderer, $text, $block_width, $style);
                $cursor_y += ($line_count - 1) * $line_height;
                $cursor_x = $last_line_width;
            }
        } else {
            // The segment does not fit on the remainder of the current line.
            // Use two-width wrapping: the first line wraps at available_width,
            // subsequent lines at full block_width. This ensures words that do
            // not fit in the remaining space are pushed intact to the next line
            // (where they have full width) rather than being broken mid-word.
            $wrapper = new TextWrapper(new PdfTextMeasurer($renderer));
            $lines = $wrapper->wrapText($text, $style, $available_width, $block_width);
            $first_line = $lines[0];

            if ($first_line !== '') {
                // Render the words that fit on the current line.
                if ($renderer->isRTL()) {
                    $draw_x = $base_x + $block_width - $cursor_x - $available_width;
                } else {
                    $draw_x = $base_x + $cursor_x;
                }

                $renderer->drawTextBlock(
                    text: $first_line,
                    x: $draw_x,
                    y: $base_y + $cursor_y,
                    width: $available_width,
                    height: $line_height,
                    align: $renderer->isRTL() ? 'R' : 'L',
                    line_height: $line_height,
                    with_padding: false,
                );

                // Extract the remaining text after the first line.
                $remaining_text = ltrim(mb_substr($text, mb_strlen($first_line)));
            } else {
                // No complete word fits in the remaining space.
                $remaining_text = $text;
            }

            $cursor_x = 0.0;
            $cursor_y += $line_height;

            // Render remaining text at full block width.
            if ($remaining_text !== '' && $cursor_y + $line_height <= $block_height + 0.0001) {
                $renderer->drawTextBlock(
                    text: $remaining_text,
                    x: $base_x,
                    y: $base_y + $cursor_y,
                    width: $block_width,
                    height: $block_height - $cursor_y,
                    align: $renderer->isRTL() ? 'R' : 'L',
                    line_height: $line_height,
                    with_padding: false,
                );

                [$line_count, $last_line_width] = $this->wrappedTextMetrics($renderer, $remaining_text, $block_width, $style);
                $cursor_y += ($line_count - 1) * $line_height;
                $cursor_x = $last_line_width;
            }
        }


        return $cursor_y;
    }

    /**
     * @return array{int, float}
     */
    private function wrappedTextMetrics(PdfRenderTargetInterface $renderer, string $text, float $width, Style $style): array
    {
        if ($text === '' || $width <= 0.0) {
            return [1, 0.0];
        }

        $wrapper = new TextWrapper(new PdfTextMeasurer($renderer));
        $line_count = $wrapper->countLines($text, $width, $style);
        $last_line_width = $wrapper->lastLineWidth($text, $width, $style);

        return [$line_count, $last_line_width];
    }


    private function renderTextFlowFootnoteRef(
        PdfRenderTargetInterface $renderer,
        FootnoteRefData $run,
        float $base_x,
        float $base_y,
        float $block_width,
        float $block_height,
        float $line_height,
        float &$cursor_x,
        float $cursor_y,
    ): float {
        $renderer->setCurrentStyle($run->style);
        $number_text = (string) $run->number;
        $number_width = $renderer->getStringWidth($number_text);

        if ($cursor_x + $number_width > $block_width && $cursor_x > 0.0) {
            $cursor_x = 0.0;
            $cursor_y += $line_height;
        }

        if ($cursor_y + $line_height > $block_height + 0.0001) {
            return $cursor_y;
        }

        // Position footnote markers using the same directional cursor as text runs.
        if ($renderer->isRTL()) {
            $draw_x = $base_x + $block_width - $cursor_x - $number_width;
        } else {
            $draw_x = $base_x + $cursor_x;
        }

        $draw_y = $base_y + $cursor_y;

        $renderer->drawTextBlock(
            text: $number_text,
            x: $draw_x,
            y: $draw_y,
            width: $number_width,
            height: $line_height,
            align: 'L',
            line_height: $line_height,
            with_padding: false,
        );

        $link_target = $run->link_target !== '' ? $run->link_target : (string) $run->number;
        $link_id = $this->footnoteLinkId($renderer, $link_target);
        $renderer->addLinkArea($draw_x, $draw_y, $number_width, $line_height, (string) $link_id);

        $cursor_x += $number_width;

        return $cursor_y;
    }

    private function renderRect(PdfRenderTargetInterface $renderer, LayoutBlock $block, float $origin_x, float $origin_y): void
    {
        $data = $block->data;
        assert($data instanceof RectData);
        $x = $origin_x + $block->x;
        $y = $origin_y + $block->y;

        $style = '';
        if ($data->border) {
            $style .= 'D';
        }
        if ($data->background_color !== '') {
            $this->applyHexFillColor($renderer, $data->background_color);
            $style .= 'F';
        }
        if ($style === '') {
            $style = 'D';
        }

        $renderer->drawRect($x, $y, $block->width, $block->height, $style);
        $renderer->resetColors();
    }

    private function renderLine(PdfRenderTargetInterface $renderer, LayoutBlock $block, float $origin_x, float $origin_y): void
    {
        $data = $block->data;
        assert($data instanceof LineData);

        $renderer->drawLine($origin_x + $data->x1, $origin_y + $data->y1, $origin_x + $data->x2, $origin_y + $data->y2);
    }

    private function renderImage(PdfRenderTargetInterface $renderer, LayoutBlock $block, float $origin_x, float $origin_y): void
    {
        $data = $block->data;
        assert($data instanceof ImageData);
        $x = $origin_x + $block->x;
        $y = $origin_y + $block->y;

        $renderer->drawImage($data->src, $x, $y, $block->width, $block->height);

        if ($data->link !== '') {
            $renderer->addLinkArea($x, $y, $block->width, $block->height, $data->link);
        }
    }

    private function renderFootnoteRef(PdfRenderTargetInterface $renderer, LayoutBlock $block, float $origin_x, float $origin_y): void
    {
        $data = $block->data;
        assert($data instanceof FootnoteRefData);
        $x = $origin_x + $block->x;
        $y = $origin_y + $block->y;

        $link_target = $data->link_target !== '' ? $data->link_target : (string) $data->number;
        $link_id = $this->footnoteLinkId($renderer, $link_target);

        $renderer->setCurrentStyle($data->style);
        $renderer->drawTextBlock(
            text: (string) $data->number,
            x: $x,
            y: $y,
            width: $block->width,
            height: $block->height,
            align: 'L',
            line_height: $block->height,
            with_padding: false,
        );
        $renderer->addLinkArea($x, $y, $block->width, $block->height, (string) $link_id);
    }

    private function renderFootnoteBody(PdfRenderTargetInterface $renderer, LayoutBlock $block, float $origin_x, float $origin_y): void
    {
        $data = $block->data;
        assert($data instanceof FootnoteBodyData);
        $x = $origin_x + $block->x;
        $y = $origin_y + $block->y;

        $link_target = $data->link_target !== '' ? $data->link_target : (string) $data->number;
        $link_id = $this->footnoteLinkId($renderer, $link_target);

        $renderer->setCurrentStyle($data->style);
        $renderer->setLinkDestination((string) $link_id, $y, -1);
        $renderer->drawTextBlock(
            text: sprintf('%d. %s', $data->number, $data->text),
            x: $x,
            y: $y,
            width: $block->width,
            height: $block->height,
            align: 'L',
            line_height: $data->style->size * self::LINE_HEIGHT_RATIO,
        );
    }

    private function footnoteLinkId(PdfRenderTargetInterface $renderer, string $link_target): int
    {
        if (!isset($this->footnote_links[$link_target])) {
            $this->footnote_links[$link_target] = $renderer->createLink();
        }

        return $this->footnote_links[$link_target];
    }

    private function alignToPdf(CellAlign $align): string
    {
        return match ($align) {
            CellAlign::Center => 'C',
            CellAlign::Right => 'R',
            default => 'L',
        };
    }

    private function applyHexTextColor(PdfRenderTargetInterface $renderer, string $color): void
    {
        $renderer->setTextColor(new HexColor($color));
    }

    private function applyHexFillColor(PdfRenderTargetInterface $renderer, string $color): void
    {
        $renderer->setFillColor(new HexColor($color));
    }

    private function applyHexDrawColor(PdfRenderTargetInterface $renderer, string $color): void
    {
        $renderer->setDrawColor(new HexColor($color));
    }
}
