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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Registry;

use function base64_encode;
use function mb_strlen;

/**
 * HTML report renderer.
 *
 * Parses the report element tree and produces HTML output using the
 * LayoutEngine + HtmlWriter pipeline.  Element creation methods build
 * a document model; run() performs layout and writes the final HTML.
 */
final class HtmlRenderer extends AbstractRenderer implements ElementFactoryInterface
{
    public function output(): string
    {
        $config = $this->reportConfig();

        $layout_page_width = $config->orientation === PageOrientation::Landscape
            ? $config->paper_height
            : $config->paper_width;

        // HTML uses infinite page height (no automatic page breaks).
        $layout_config = new Config(
            paper_width: $layout_page_width,
            paper_height: 0.0,
            left_margin: $config->left_margin,
            right_margin: $config->right_margin,
            top_margin: $config->top_margin,
            bottom_margin: $config->bottom_margin,
            header_margin: $config->header_margin,
            footer_margin: $config->footer_margin,
            orientation: $config->orientation,
            paper_size: $config->paper_size,
            rtl: $config->rtl,
            generated_by: $config->generated_by,
            author: $config->author,
            title: $config->title,
            description: $config->description,
            align_rtl: $config->align_rtl,
            entity_rtl: $config->entity_rtl,
            font: $config->font,
            timestamp: $config->timestamp,
            font_subsetting: $config->font_subsetting,
            compression: $config->compression,
        );

        $measurer = new HtmlTextMeasurer();
        $engine = new LayoutEngine($measurer, $layout_config);

        $header_pages = $engine->layoutPaged($this->headerElements());
        $body_pages = $engine->layoutPaged($this->bodyElements());
        $footer_pages = $engine->layoutPaged($this->footerElements());

        $writer = new HtmlWriter();
        return $writer->renderPaged(
            $config,
            $this->stylesMap(),
            $header_pages,
            $body_pages,
            $footer_pages,
        );
    }

    public function createCell(float $width, float $height, string $border, CellAlign $align, string $background_color, Style $style, CellNewline $newline, float $top, float $left, string $border_color, string $text_color): Cell
    {
        return new Cell($width, $height, $border, $align, $background_color, $style, $newline, $top, $left, $border_color, $text_color);
    }

    public function createTextBox(
        float $width,
        float $height,
        bool $border,
        string $background_color,
        bool $newline,
        float $left,
        float $top,
        bool $check_page_break,
        bool $padding,
        bool $reset_height,
    ): TextBox {
        return new TextBox($width, $height, $border, $background_color, $newline, $left, $top, $check_page_break, $padding, $reset_height);
    }

    public function createText(Style $style, string $color, float $truncate): Text
    {
        return new Text($style, $color, $truncate);
    }

    public function createFootnote(Style $style): Footnote
    {
        return new Footnote($style);
    }

    public function createImage(
        string $mime_type,
        string $data,
        float $x,
        float $y,
        float $w,
        float $h,
        CellAlign $align,
        ImageContinuation $ln,
    ): Image {
        $src = 'data:' . $mime_type . ';base64,' . base64_encode($data);

        return new Image($src, $x, $y, $w, $h, $align, $ln);
    }

    public function createImageFromObject(
        MediaFile $media_file,
        float $x,
        float $y,
        float $w,
        float $h,
        CellAlign $align,
        ImageContinuation $ln,
    ): Image {
        $add_watermark = Registry::imageFactory()->fileNeedsWatermark($media_file, Auth::user());

        $data = Registry::imageFactory()->mediaFileThumbnail(
            $media_file,
            (int) ($w * 4),
            (int) ($h * 4),
            'crop',
            $add_watermark,
        );

        $src = 'data:' . $media_file->mimeType() . ';base64,' . base64_encode($data);

        return new Image($src, $x, $y, $w, $h, $align, $ln);
    }

    public function createLine(float $x1, float $y1, float $x2, float $y2): Line
    {
        return new Line($x1, $y1, $x2, $y2);
    }

    public function newPage(): void
    {
        // Page breaks are handled by the LayoutEngine pipeline.
    }

    public function setCurrentStyle(Style $style): void
    {
        $this->setCurrentStyleValue($style);
    }

    public function getStringWidth(string $text): float
    {
        $style = $this->currentStyleValue();

        $font_size = $style instanceof Style ? $style->size : StyleDefaults::DEFAULT_FONT_SIZE;

        return mb_strlen($text) * $font_size / 2;
    }


    public function pageNumber(): int
    {
        return 1;
    }
}
