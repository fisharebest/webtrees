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

/**
 * The output of the layout engine: a positioned, ready-to-render instruction.
 *
 * Each LayoutBlock represents one visual primitive on the page — a text cell,
 * a rectangle, a line, an image, or a page break. Renderers walk a list of
 * these blocks and emit backend-specific output without any further layout
 * computation.
 *
 * All coordinates are in points (1pt = 1/72 inch), measured from the
 * top-left corner of the page content area (inside margins).
 */
final readonly class LayoutBlock
{
    private function __construct(
        public int $page,
        public float $x,
        public float $y,
        public float $width,
        public float $height,
        public LayoutBlockData $data,
        public int|null $row_id = null,
        public int|null $column_id = null,
    ) {
    }

    /**
     * Return a copy of this block with an updated height.
     */
    public function withHeight(float $height): self
    {
        return new self(
            page: $this->page,
            x: $this->x,
            y: $this->y,
            width: $this->width,
            height: $height,
            data: $this->data,
            row_id: $this->row_id,
            column_id: $this->column_id,
        );
    }

    /**
     * Return a copy of this block with an updated page index.
     */
    public function withPage(int $page): self
    {
        return new self(
            page: $page,
            x: $this->x,
            y: $this->y,
            width: $this->width,
            height: $this->height,
            data: $this->data,
            row_id: $this->row_id,
            column_id: $this->column_id,
        );
    }

    /**
     * Return a copy of this block with row/column grouping metadata.
     */
    public function withRowColumn(int|null $row_id, int|null $column_id): self
    {
        return new self(
            page: $this->page,
            x: $this->x,
            y: $this->y,
            width: $this->width,
            height: $this->height,
            data: $this->data,
            row_id: $row_id,
            column_id: $column_id,
        );
    }

    /**
     * Create a text cell block.
     */
    public static function textCell(
        int $page,
        float $x,
        float $y,
        float $width,
        float $height,
        string $text,
        Style $style,
        CellAlign $align,
        string $background_color,
        string $border,
        string $border_color,
        string $text_color,
        string $url,
    ): self {
        return new self(
            page: $page,
            x: $x,
            y: $y,
            width: $width,
            height: $height,
            data: new TextCellData(
                text: $text,
                style: $style,
                align: $align,
                background_color: $background_color,
                border: $border,
                border_color: $border_color,
                text_color: $text_color,
                url: $url,
            ),
        );
    }

    /**
     * Create an inline text block (flowing text without explicit cell border).
     */
    public static function text(
        int $page,
        float $x,
        float $y,
        float $width,
        float $height,
        string $text,
        Style $style,
        string $color,
    ): self {
        return new self(
            page: $page,
            x: $x,
            y: $y,
            width: $width,
            height: $height,
            data: new TextData(
                text: $text,
                style: $style,
                color: $color,
            ),
        );
    }

    /**
     * Create a text flow block: a positioned container with multiple styled runs.
     *
     * The browser handles word-wrapping; no per-line positioning is needed.
     *
     * @param list<TextRun|FootnoteRefData> $runs
     */
    public static function textFlow(
        int $page,
        float $x,
        float $y,
        float $width,
        float $height,
        array $runs,
    ): self {
        return new self(
            page: $page,
            x: $x,
            y: $y,
            width: $width,
            height: $height,
            data: new TextFlowData(runs: $runs),
        );
    }

    /**
     * Create an image block.
     */
    public static function image(
        int $page,
        float $x,
        float $y,
        float $width,
        float $height,
        string $src,
        CellAlign $align,
        string $link = '',
    ): self {
        return new self(
            page: $page,
            x: $x,
            y: $y,
            width: $width,
            height: $height,
            data: new ImageData(
                src: $src,
                align: $align,
                link: $link,
            ),
        );
    }

    /**
     * Create a line block.
     */
    public static function line(
        int $page,
        float $x1,
        float $y1,
        float $x2,
        float $y2,
    ): self {
        return new self(
            page: $page,
            x: $x1,
            y: $y1,
            width: $x2 - $x1,
            height: $y2 - $y1,
            data: new LineData(
                x1: $x1,
                y1: $y1,
                x2: $x2,
                y2: $y2,
            ),
        );
    }

    /**
     * Create a rectangle block (used for text-box borders/backgrounds).
     */
    public static function rect(
        int $page,
        float $x,
        float $y,
        float $width,
        float $height,
        string $background_color,
        bool $border,
    ): self {
        return new self(
            page: $page,
            x: $x,
            y: $y,
            width: $width,
            height: $height,
            data: new RectData(
                background_color: $background_color,
                border: $border,
            ),
        );
    }

    /**
     * Create a page-break marker.
     */
    public static function pageBreak(int $page): self
    {
        return new self(
            page: $page,
            x: 0.0,
            y: 0.0,
            width: 0.0,
            height: 0.0,
            data: new PageBreakData(),
        );
    }

    /**
     * Create a footnote reference (superscript number inline).
     */
    public static function footnoteRef(
        int $page,
        float $x,
        float $y,
        float $width,
        float $height,
        int $number,
        string $link_target,
        Style $style,
    ): self {
        return new self(
            page: $page,
            x: $x,
            y: $y,
            width: $width,
            height: $height,
            data: new FootnoteRefData(
                number: $number,
                link_target: $link_target,
                style: $style,
            ),
        );
    }

    /**
     * Create a footnote body (rendered at page bottom).
     */
    public static function footnoteBody(
        int $page,
        float $x,
        float $y,
        float $width,
        float $height,
        int $number,
        string $text,
        string $link_target,
        Style $style,
    ): self {
        return new self(
            page: $page,
            x: $x,
            y: $y,
            width: $width,
            height: $height,
            data: new FootnoteBodyData(
                number: $number,
                text: $text,
                link_target: $link_target,
                style: $style,
            ),
        );
    }
}
