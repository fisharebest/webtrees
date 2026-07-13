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

use function count;
use function htmlspecialchars;
use function max;
use function str_contains;
use function strtolower;
use function strtr;
use function uasort;

use const ENT_HTML5;
use const ENT_QUOTES;

/**
 * Renders a list of LayoutBlocks as HTML.
 *
 * Produces clean, semantic HTML that relies on CSS for layout rather than
 * the old TCPDF-style absolute positioning. Body content uses relative
 * positioning within a container of known width; the browser handles
 * word-wrapping and page printing.
 */
final class HtmlWriter
{
    private bool $rtl = false;

    /**
     * Render a complete report as HTML.
     *
     * @param Config        $config         Page/report configuration
     * @param array<string,Style> $styles         Named styles from the report XML
     * @param list<LayoutBlock>   $header_blocks  Positioned blocks for the header
     * @param list<LayoutBlock>   $body_blocks    Positioned blocks for the body
     * @param list<LayoutBlock>   $footer_blocks  Positioned blocks for the footer
     * @param list<LayoutBlock>   $footnote_blocks Footnote body blocks
     */
    public function render(
        Config $config,
        array $styles,
        array $header_blocks,
        array $body_blocks,
        array $footer_blocks,
        array $footnote_blocks = [],
    ): string {
        return $this->renderPaged(
            $config,
            $styles,
            new LayoutPages([0 => $header_blocks]),
            new LayoutPages([0 => $body_blocks]),
            new LayoutPages([0 => $footer_blocks]),
            new LayoutPages([0 => $footnote_blocks]),
        );
    }

    /**
     * Render a complete report from paged layout results.
     *
     * @param Config        $config          Page/report configuration
     * @param array<string,Style> $styles          Named styles from the report XML
     * @param LayoutPages         $header_pages    Header blocks grouped by page
     * @param LayoutPages         $body_pages      Body blocks grouped by page
     * @param LayoutPages         $footer_pages    Footer blocks grouped by page
     * @param LayoutPages|null    $footnote_pages  Footnote body blocks grouped by page
     */
    public function renderPaged(
        Config $config,
        array $styles,
        LayoutPages $header_pages,
        LayoutPages $body_pages,
        LayoutPages $footer_pages,
        LayoutPages|null $footnote_pages = null,
    ): string {
        $this->rtl = $config->rtl;

        $header_blocks = $header_pages->flatten();
        $body_blocks = $body_pages->flatten();
        $footer_blocks = $footer_pages->flatten();
        $footnote_blocks = $footnote_pages?->flatten() ?? [];

        $effective_page_width = $config->orientation === PageOrientation::Landscape
            ? $config->paper_height
            : $config->paper_width;

        $content_width = $effective_page_width - $config->left_margin - $config->right_margin;

        $html = $this->renderStyles($styles);
        $html .= '<div class="report-container" style="width: ' . $content_width . 'pt;">';
        $html .= $this->renderSection('report-header', $header_blocks, $content_width);
        $html .= $this->renderSection('report-body', $body_blocks, $content_width);

        if ($footnote_blocks !== []) {
            $html .= $this->renderSection('report-footnotes', $footnote_blocks, $content_width);
        }

        $html .= $this->renderSection('report-footer', $footer_blocks, $content_width);
        $html .= '</div>';

        return $html;
    }

    /**
     * Emit the <style> block for report-specific CSS classes.
     *
     * @param array<string,Style> $styles
     */
    private function renderStyles(array $styles): string
    {
        $css = '<style>';

        foreach ($styles as $name => $style) {
            $style_flags = strtolower($style->style);

            $css .= '.' . $name . ' { ';
            $css .= 'font-size: ' . $style->size . 'pt; ';

            if (str_contains($style_flags, 'b')) {
                $css .= 'font-weight: bold; ';
            }
            if (str_contains($style_flags, 'i')) {
                $css .= 'font-style: italic; ';
            }
            if (str_contains($style_flags, 'u')) {
                $css .= 'text-decoration: underline; ';
            }
            if (str_contains($style_flags, 'd')) {
                $css .= 'text-decoration: line-through; ';
            }

            $css .= '}';
        }

        $css .= '</style>';

        return $css;
    }

    /**
     * Render a section (header, body, or footer) as positioned blocks
     * within a relative container.
     *
     * @param list<LayoutBlock> $blocks
     */
    private function renderSection(string $class, array $blocks, float $content_width): string
    {
        if ($blocks === []) {
            return '';
        }

        if ($class === 'report-body') {
            return $this->renderBodySection($class, $blocks, $content_width);
        }


        // Calculate the total height needed for the section
        $max_y = 0.0;
        foreach ($blocks as $block) {
            $bottom = $block->y + $block->height;
            if ($bottom > $max_y) {
                $max_y = $bottom;
            }
        }

        $tag = match ($class) {
            'report-header' => 'header',
            'report-footer' => 'footer',
            default => 'div',
        };

        $html = '<' . $tag;

        if ($tag === 'div') {
            $html .= ' class="' . $class . '"';
        }

        $html .= ' style="';
        $html .= 'position: relative; ';
        $html .= 'width: ' . $content_width . 'pt; ';
        $html .= 'height: ' . $max_y . 'pt;';
        $html .= '">';

        foreach ($blocks as $block) {
            $html .= $this->renderBlock($block);
        }

        $html .= '</' . $tag . '>';

        return $html;
    }

    /**
     * Render body blocks, using flow-based row containers for grouped TextCell rows.
     *
     * @param list<LayoutBlock> $blocks
     */
    private function renderBodySection(string $class, array $blocks, float $content_width): string
    {
        // Keep the same section-height contract as the absolute renderer.
        $max_y = 0.0;
        foreach ($blocks as $block) {
            $bottom = $block->y + $block->height;
            if ($bottom > $max_y) {
                $max_y = $bottom;
            }
        }

        $html = '<main style="position: relative; width: ' . $content_width . 'pt; min-height: ' . $max_y . 'pt;">';

        $index = 0;
        $count = count($blocks);

        while ($index < $count) {
            $block = $blocks[$index];

            if ($block->row_id !== null && $block->column_id !== null) {
                $row_blocks = [];
                $row_id = $block->row_id;

                while ($index < $count) {
                    $candidate = $blocks[$index];
                    if ($candidate->row_id !== $row_id || $candidate->column_id === null) {
                        break;
                    }
                    $row_blocks[] = $candidate;
                    $index++;
                }

                if ($this->canRenderFlowRow($row_blocks)) {
                    $html .= $this->renderFlowRow($row_blocks);
                } else {
                    foreach ($row_blocks as $row_block) {
                        $html .= $this->renderBlock($row_block);
                    }
                }
                continue;
            }

            $html .= $this->renderBlock($block);
            $index++;
        }

        $html .= '</main>';

        return $html;
    }

    /**
     * @param list<LayoutBlock> $row_blocks
     */
    private function canRenderFlowRow(array $row_blocks): bool
    {
        $column_ids = [];
        $column_bounds = [];

        foreach ($row_blocks as $row_block) {
            if ($row_block->column_id === null) {
                return false;
            }
            $column_ids[$row_block->column_id] = true;
            $column_bounds[$row_block->column_id] ??= ['left' => $row_block->x, 'right' => $row_block->x + $row_block->width];

            if ($row_block->x < $column_bounds[$row_block->column_id]['left']) {
                $column_bounds[$row_block->column_id]['left'] = $row_block->x;
            }

            $column_right = $row_block->x + $row_block->width;
            if ($column_right > $column_bounds[$row_block->column_id]['right']) {
                $column_bounds[$row_block->column_id]['right'] = $column_right;
            }

            if (
                !$row_block->data instanceof RectData
                && !$row_block->data instanceof TextFlowData
                && !$row_block->data instanceof TextCellData
            ) {
                return false;
            }
        }

        // Flex rows are only valid for non-overlapping columns.
        // Layered blocks (for example number + name overlays) must keep absolute positioning.
        uasort(
            $column_bounds,
            static fn (array $left, array $right): int => $left['left'] <=> $right['left'],
        );

        $previous_right = null;
        foreach ($column_bounds as $bounds) {
            if ($previous_right !== null && $bounds['left'] < $previous_right) {
                return false;
            }

            $previous_right = $bounds['right'];
        }

        // Only treat true multi-column rows as flow rows.
        return count($column_ids) > 1;
    }

    /**
     * @param list<LayoutBlock> $row_blocks
     */
    private function renderFlowRow(array $row_blocks): string
    {
        $columns = [];
        $row_left = null;
        $row_top = null;
        $row_right = 0.0;

        foreach ($row_blocks as $row_block) {
            $column_id = $row_block->column_id;
            assert($column_id !== null);

            if (!isset($columns[$column_id])) {
                $columns[$column_id] = [
                    'left' => $row_block->x,
                    'top' => $row_block->y,
                    'width' => $row_block->width,
                    'rect' => null,
                    'flows' => [],
                    'cells' => [],
                ];
            }

            if ($row_block->x < $columns[$column_id]['left']) {
                $columns[$column_id]['left'] = $row_block->x;
            }
            if ($row_block->y < $columns[$column_id]['top']) {
                $columns[$column_id]['top'] = $row_block->y;
            }
            if ($row_block->width > $columns[$column_id]['width']) {
                $columns[$column_id]['width'] = $row_block->width;
            }

            if ($row_left === null || $row_block->x < $row_left) {
                $row_left = $row_block->x;
            }
            if ($row_top === null || $row_block->y < $row_top) {
                $row_top = $row_block->y;
            }

            $column_right = $columns[$column_id]['left'] + $columns[$column_id]['width'];
            if ($column_right > $row_right) {
                $row_right = $column_right;
            }

            if ($row_block->data instanceof RectData) {
                $columns[$column_id]['rect'] = $row_block;
            } elseif ($row_block->data instanceof TextFlowData) {
                $columns[$column_id]['flows'][] = $row_block;
            } elseif ($row_block->data instanceof TextCellData) {
                $columns[$column_id]['cells'][] = $row_block;
            }
        }

        // Render columns in visual order for the flex container.
        // In LTR mode, flex items flow left-to-right, so sort ascending by x.
        // In RTL mode, flex items flow right-to-left, so sort descending by x.
        if ($this->rtl) {
            uasort(
                $columns,
                static fn (array $left, array $right): int => $right['left'] <=> $left['left'],
            );
        } else {
            uasort(
                $columns,
                static fn (array $left, array $right): int => $left['left'] <=> $right['left'],
            );
        }

        $row_left ??= 0.0;
        $row_top ??= 0.0;
        $row_width = max(1.0, $row_right - $row_left);

        $html = '<div class="report-row" style="position:absolute; left:' . $row_left . 'pt; top:' . $row_top . 'pt; width:' . $row_width . 'pt;">';

        foreach ($columns as $column) {
            $style_parts = [];
            $style_parts[] = 'width:' . ($column['width'] * 100.0 / $row_width) . '%';

            $rect_block = $column['rect'];
            if ($rect_block instanceof LayoutBlock) {
                $rect_data = $rect_block->data;
                assert($rect_data instanceof RectData);
                if ($rect_data->background_color !== '') {
                    $style_parts[] = 'background-color:' . $rect_data->background_color;
                }
                if ($rect_data->border) {
                    $style_parts[] = 'border:solid black 1pt';
                }
            }

            foreach ($column['cells'] as $cell_block) {
                $cell_data = $cell_block->data;
                assert($cell_data instanceof TextCellData);

                if ($cell_data->background_color !== '') {
                    $style_parts[] = 'background-color:' . $cell_data->background_color;
                }

                $this->appendCellBorderStyles($style_parts, $cell_data->border, $cell_data->border_color);
            }

            $html .= '<div class="report-col" style="' . implode(';', $style_parts) . '">';
            foreach ($column['cells'] as $cell_block) {
                $html .= $this->renderTextCellInline($cell_block);
            }
            foreach ($column['flows'] as $flow_block) {
                $html .= $this->renderTextFlowInline($flow_block);
            }
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }


    /**
     * Render a single LayoutBlock as HTML.
     */
    private function renderBlock(LayoutBlock $block): string
    {
        return match ($block->data::class) {
            TextCellData::class => $this->renderTextCell($block),
            TextData::class => $this->renderText($block),
            TextFlowData::class => $this->renderTextFlow($block),
            ImageData::class => $this->renderImage($block),
            LineData::class => $this->renderLine($block),
            RectData::class => $this->renderRect($block),
            FootnoteRefData::class => $this->renderFootnoteRef($block),
            FootnoteBodyData::class => $this->renderFootnoteBody($block),
            PageBreakData::class => $this->renderPageBreak(),
            default => throw new LogicException('Unsupported layout block data class: ' . $block->data::class),
        };
    }

    /**
     * Render a text cell with optional border, background, and alignment.
     */
    private function renderTextCell(LayoutBlock $block): string
    {
        $data = $block->data;
        assert($data instanceof TextCellData);

        $style_parts = [];
        $style_parts[] = 'left:' . $block->x . 'pt';
        $style_parts[] = 'top:' . $block->y . 'pt';
        $style_parts[] = 'width:' . $block->width . 'pt';
        $style_parts[] = 'padding:2pt';

        if ($data->background_color !== '') {
            $style_parts[] = 'background-color:' . $data->background_color;
        }

        $this->appendCellBorderStyles($style_parts, $data->border, $data->border_color);

        $align = match ($data->align) {
            CellAlign::Center => 'text-align:center',
            CellAlign::Right => 'text-align:right',
            CellAlign::Left => 'text-align:left',
            CellAlign::None => '',
        };
        if ($align !== '') {
            $style_parts[] = $align;
        }

        $css_class = 'report-block report-text ' . $data->style->name;
        $inline_style = implode(';', $style_parts);

        $html = '<div class="' . $css_class . '" style="' . $inline_style . '">';

        if ($data->url !== '') {
            $html .= '<a href="' . $this->escape($data->url) . '">';
        }

        if ($data->text !== '') {
            $text_html = $this->escapeText($data->text);
            if ($data->text_color !== '') {
                $text_html = '<span style="color:' . $data->text_color . '">' . $text_html . '</span>';
            }
            $html .= $text_html;
        }

        if ($data->url !== '') {
            $html .= '</a>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render a text cell as in-flow HTML (no absolute positioning).
     */
    private function renderTextCellInline(LayoutBlock $block): string
    {
        $data = $block->data;
        assert($data instanceof TextCellData);

        $style_parts = [];
        $align = match ($data->align) {
            CellAlign::Center => 'text-align:center',
            CellAlign::Right => 'text-align:right',
            CellAlign::Left => 'text-align:left',
            CellAlign::None => '',
        };

        if ($align !== '') {
            $style_parts[] = $align;
        }

        $html = '<div class="report-text ' . $data->style->name . '"';
        if ($style_parts !== []) {
            $html .= ' style="' . implode(';', $style_parts) . '"';
        }
        $html .= '>';

        if ($data->url !== '') {
            $html .= '<a href="' . $this->escape($data->url) . '">';
        }

        if ($data->text !== '') {
            $text_html = $this->escapeText($data->text);
            if ($data->text_color !== '') {
                $text_html = '<span style="color:' . $data->text_color . '">' . $text_html . '</span>';
            }
            $html .= $text_html;
        }

        if ($data->url !== '') {
            $html .= '</a>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render inline flowing text.
     */
    private function renderText(LayoutBlock $block): string
    {
        $data = $block->data;
        assert($data instanceof TextData);

        $style_parts = [];
        $style_parts[] = 'left:' . $block->x . 'pt';
        $style_parts[] = 'top:' . $block->y . 'pt';
        $style_parts[] = 'width:' . $block->width . 'pt';

        $css_class = 'report-block report-text ' . $data->style->name;
        $inline_style = implode(';', $style_parts);

        $text_html = $this->escapeText($data->text);
        if ($data->color !== '') {
            $text_html = '<span style="color:' . $data->color . '">' . $text_html . '</span>';
        }

        return '<span class="' . $css_class . '" style="' . $inline_style . '">' . $text_html . '</span>';
    }

    /**
     * Render a text flow block: a positioned container with inline styled spans.
     *
     * The browser handles all word-wrapping within this container.
     */
    private function renderTextFlow(LayoutBlock $block): string
    {
        $data = $block->data;
        assert($data instanceof TextFlowData);

        $style_parts = [];
        $style_parts[] = 'left:' . $block->x . 'pt';
        $style_parts[] = 'top:' . $block->y . 'pt';
        $style_parts[] = 'width:' . $block->width . 'pt';

        $inline_style = implode(';', $style_parts);

        $html = '<div class="report-block report-text" style="' . $inline_style . '">';

        foreach ($data->runs as $run) {
            if ($run instanceof TextRun) {
                $text_html = $this->escapeText($run->text);
                $span_attrs = 'class="' . $run->style->name . '"';
                if ($run->color !== '') {
                    $span_attrs .= ' style="color:' . $run->color . '"';
                }
                $span = '<span ' . $span_attrs . '>' . $text_html . '</span>';
                if ($run->url !== '') {
                    $html .= '<a href="' . $this->escape($run->url) . '">' . $span . '</a>';
                } else {
                    $html .= $span;
                }
            } elseif ($run instanceof FootnoteRefData) {
                $html .= '<a href="#footnote' . $run->number . '"><sup>' . $run->number . '</sup></a>';
            }
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render a text flow block as in-flow HTML (no absolute positioning).
     */
    private function renderTextFlowInline(LayoutBlock $block): string
    {
        $data = $block->data;
        assert($data instanceof TextFlowData);

        $html = '<div class="report-text">';

        foreach ($data->runs as $run) {
            if ($run instanceof TextRun) {
                $text_html = $this->escapeText($run->text);
                $span_attrs = 'class="' . $run->style->name . '"';
                if ($run->color !== '') {
                    $span_attrs .= ' style="color:' . $run->color . '"';
                }
                $span = '<span ' . $span_attrs . '>' . $text_html . '</span>';
                if ($run->url !== '') {
                    $html .= '<a href="' . $this->escape($run->url) . '">' . $span . '</a>';
                } else {
                    $html .= $span;
                }
            } elseif ($run instanceof FootnoteRefData) {
                $html .= '<a href="#footnote' . $run->number . '"><sup>' . $run->number . '</sup></a>';
            }
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render an image.
     */
    private function renderImage(LayoutBlock $block): string
    {
        $data = $block->data;
        assert($data instanceof ImageData);

        $style_parts = [];
        $style_parts[] = 'left:' . $block->x . 'pt';
        $style_parts[] = 'top:' . $block->y . 'pt';
        $style_parts[] = 'width:' . $block->width . 'pt';
        $style_parts[] = 'height:' . $block->height . 'pt';

        $inline_style = implode(';', $style_parts);

        $img = '<img class="report-image" src="' . $this->escape($data->src) . '" style="' . $inline_style . '" alt="">';

        if ($data->link !== '') {
            return '<a href="' . $this->escape($data->link) . '" target="_blank" rel="noopener noreferrer">' . $img . '</a>';
        }

        return $img;
    }

    /**
     * Render a line between two points using a border trick.
     */
    private function renderLine(LayoutBlock $block): string
    {
        $data = $block->data;
        assert($data instanceof LineData);

        $is_horizontal = ($data->y1 === $data->y2);
        $is_vertical = ($data->x1 === $data->x2);

        if ($is_horizontal) {
            $left = min($data->x1, $data->x2);
            $width = abs($data->x2 - $data->x1);

            return '<div class="report-line" style="' .
                'left:' . $left . 'pt;' .
                'top:' . $data->y1 . 'pt;' .
                'width:' . $width . 'pt;' .
                'border-top:solid black 1pt;' .
                'height:0;' .
                '"></div>';
        }

        if ($is_vertical) {
            $top = min($data->y1, $data->y2);
            $height = abs($data->y2 - $data->y1);

            return '<div class="report-line" style="' .
                'left:' . $data->x1 . 'pt;' .
                'top:' . $top . 'pt;' .
                'height:' . $height . 'pt;' .
                'border-left:solid black 1pt;' .
                'width:0;' .
                '"></div>';
        }

        // Diagonal lines cannot be easily represented in CSS; use SVG
        $min_x = min($data->x1, $data->x2);
        $min_y = min($data->y1, $data->y2);
        $svg_width = abs($data->x2 - $data->x1);
        $svg_height = abs($data->y2 - $data->y1);

        return '<svg class="report-line" style="' .
            'left:' . $min_x . 'pt;top:' . $min_y . 'pt;' .
            'width:' . $svg_width . 'pt;height:' . $svg_height . 'pt;" ' .
            'viewBox="0 0 ' . $svg_width . ' ' . $svg_height . '">' .
            '<line x1="' . ($data->x1 - $min_x) . '" y1="' . ($data->y1 - $min_y) . '" ' .
            'x2="' . ($data->x2 - $min_x) . '" y2="' . ($data->y2 - $min_y) . '" ' .
            'stroke="black" stroke-width="1"/></svg>';
    }

    /**
     * Render a rectangle (background/border for text boxes).
     */
    private function renderRect(LayoutBlock $block): string
    {
        $data = $block->data;
        assert($data instanceof RectData);

        $style_parts = [];
        $style_parts[] = 'left:' . $block->x . 'pt';
        $style_parts[] = 'top:' . $block->y . 'pt';
        $style_parts[] = 'width:' . $block->width . 'pt';
        $style_parts[] = 'height:' . $block->height . 'pt';

        if ($data->background_color !== '') {
            $style_parts[] = 'background-color:' . $data->background_color;
        }

        if ($data->border) {
            $style_parts[] = 'border:solid black 1pt';
        }

        $inline_style = implode(';', $style_parts);

        return '<div class="report-block" style="' . $inline_style . '"></div>';
    }

    /**
     * Render a footnote reference (superscript number with anchor link).
     */
    private function renderFootnoteRef(LayoutBlock $block): string
    {
        $data = $block->data;
        assert($data instanceof FootnoteRefData);

        return '<a href="#footnote' . $data->number . '" style="' .
            'position:absolute;left:' . $block->x . 'pt;top:' . $block->y . 'pt;">' .
            '<sup>' . $data->number . '</sup></a>';
    }

    /**
     * Render a footnote body at the bottom of the page.
     */
    private function renderFootnoteBody(LayoutBlock $block): string
    {
        $data = $block->data;
        assert($data instanceof FootnoteBodyData);

        $style_class = $data->style->name;
        $text_html = $this->escapeText($data->text);

        return '<div class="report-block ' . $style_class . '" id="footnote' . $data->number . '" style="' .
            'left:' . $block->x . 'pt;top:' . $block->y . 'pt;width:' . $block->width . 'pt;">' .
            $data->number . '. ' . $text_html .
            '</div>';
    }

    /**
     * Render a page break marker for print CSS.
     */
    private function renderPageBreak(): string
    {
        return '<div class="report-page-break" aria-hidden="true"></div>';
    }

    /**
     * @param list<string> $style_parts
     */
    private function appendCellBorderStyles(array &$style_parts, string $border, string $border_color): void
    {
        if ($border === '') {
            return;
        }

        $resolved_border_color = $border_color !== '' ? $border_color : 'black';

        if ($border === '1') {
            $style_parts[] = 'border:solid ' . $resolved_border_color . ' 1pt';

            return;
        }

        if (str_contains($border, 'T')) {
            $style_parts[] = 'border-top:solid ' . $resolved_border_color . ' 1pt';
        }
        if (str_contains($border, 'B')) {
            $style_parts[] = 'border-bottom:solid ' . $resolved_border_color . ' 1pt';
        }
        if (str_contains($border, 'L')) {
            $style_parts[] = 'border-left:solid ' . $resolved_border_color . ' 1pt';
        }
        if (str_contains($border, 'R')) {
            $style_parts[] = 'border-right:solid ' . $resolved_border_color . ' 1pt';
        }
    }


    /**
     * Escape text for safe HTML output, converting newlines to <br>.
     */
    private function escapeText(string $text): string
    {
        $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return strtr($escaped, ["\n" => '<br>']);
    }

    /**
     * Escape an attribute value.
     */
    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
