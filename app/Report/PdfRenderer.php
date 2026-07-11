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

use Com\Tecnick\Pdf\Tcpdf;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Enums\ImageOperation;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Webtrees;
use RuntimeException;

use function define;
use function defined;
use function is_dir;
use function realpath;
use function strtoupper;

final class PdfRenderer extends AbstractRenderer implements ElementFactoryInterface, PdfRenderTargetInterface
{
    /**
     * true means that the input text is unicode (PDF)
     */
    private const bool UNICODE = true;


    private TcLibPdfAdaptor $adaptor;

    private readonly PdfWriter $pdf_writer;

    public function __construct()
    {
        parent::__construct();
        $this->pdf_writer = new PdfWriter();
    }


    public function header(float $origin_x = 0.0, float $origin_y = 0.0): void
    {
        $saved_style = $this->currentStyleValue();
        $this->pdf_writer->renderFixedSection($this, $this->headerElements(), $origin_x, $origin_y);
        $this->restoreCurrentStyle($saved_style);
    }

    public function body(): void
    {
        $this->pdf_writer->renderBody($this, $this->bodyElements());
    }
    public function footer(float $origin_x = 0.0, float $origin_y = 0.0): void
    {
        $saved_style = $this->currentStyleValue();
        $this->pdf_writer->renderFixedSection($this, $this->footerElements(), $origin_x, $origin_y);
        $this->restoreCurrentStyle($saved_style);
    }

    private function restoreCurrentStyle(Style|null $saved_style): void
    {
        $this->setCurrentStyleValue($saved_style);
        if ($saved_style !== null) {
            $this->adaptor->setFont($this->config->primary_font, strtoupper($saved_style->style), $saved_style->size);
        }
    }

    public function setCurrentStyle(Style $style): void
    {
        if ($this->currentStyleValue() !== $style) {
            $this->setCurrentStyleValue($style);
            $this->adaptor->setFont($this->config->primary_font, strtoupper($style->style), $style->size);
        }
    }

    /**
     * Return the current PDF page number, delegating to TCPDF.
     * This override allows element renderers to obtain the current page
     * number through the same {@see AbstractRenderer::pageNumber()} interface
     * used by the HTML backend.
     */
    public function pageNumber(): int
    {
        return $this->adaptor->pageNumber();
    }

    public function newPage(): void
    {
        $this->adaptor->addPage();
    }

    public function setup(Config $config): void
    {
        parent::setup($config);

        // Ensure tc-lib-pdf-font can find the converted JSON font definitions
        if (!defined('K_PATH_FONTS')) {
            $font_path = realpath(Webtrees::ROOT_DIR . 'resources/fonts');

            if ($font_path === false || !is_dir($font_path)) {
                throw new RuntimeException('Unable to resolve PDF font directory: ' . Webtrees::ROOT_DIR . 'resources/fonts');
            }

            define('K_PATH_FONTS', $font_path);
        }

        $tcpdf = new Tcpdf(
            self::UNITS,
            self::UNICODE,
            $config->font_subsetting,
            $config->compression,
            '',
            null,
            [
                // Keep remote resource loading disabled unless explicitly enabled.
                'allowedHosts' => [],
            ],
        );

        // Emit page transparency groups only when the page actually blends.
        $tcpdf->setPageTransparencyGroup('auto');

        $this->adaptor = new TcLibPdfAdaptor($tcpdf, $this, $this->config);
    }

    public function output(): string
    {
        $this->body();

        return $this->adaptor->output();
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
        $src = '@' . $data;

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
        // Send higher-resolution image at the same aspect ratio.

        $data = Registry::imageFactory()->mediaFileThumbnail(
            $media_file,
            (int) ($w * 4),
            (int) ($h * 4),
            ImageOperation::Crop,
            Auth::needsWatermark($media_file->media()->tree()),
        );

        $src = '@' . $data;

        return new Image($src, $x, $y, $w, $h, $align, $ln);
    }

    public function createLine(float $x1, float $y1, float $x2, float $y2): Line
    {
        return new Line($x1, $y1, $x2, $y2);
    }

    public function getStringWidth(string $text): float
    {
        return $this->adaptor->getStringWidth($text);
    }
    public function getPageIndex(): int
    {
        return $this->adaptor->getPage();
    }
    public function isRTL(): bool
    {
        return $this->adaptor->getRTL();
    }

    public function setFillColor(HexColor $color): void
    {
        $this->adaptor->setFillColor($color);
    }

    public function setDrawColor(HexColor $color): void
    {
        $this->adaptor->setDrawColor($color);
    }

    public function setTextColor(HexColor $color): void
    {
        $this->adaptor->setTextColor($color);
    }

    public function resetColors(): void
    {
        $this->adaptor->resetColors();
    }
    public function drawImage(
        string $file,
        float $x,
        float $y,
        float $width,
        float $height,
    ): void {
        $this->adaptor->drawImage(
            $file,
            $x,
            $y,
            $width,
            $height,
        );
    }

    public function drawLine(float $x1, float $y1, float $x2, float $y2): void
    {
        $this->adaptor->drawLine($x1, $y1, $x2, $y2);
    }

    public function drawRect(float $x, float $y, float $width, float $height, string $style): void
    {
        $this->adaptor->drawRect($x, $y, $width, $height, $style);
    }

    public function drawTextBlock(string $text, float $x, float $y, float $width, float $height, string $align, float $line_height, bool $with_padding = true): void
    {
        $this->adaptor->drawTextBlock($text, $x, $y, $width, $height, $align, $line_height, $with_padding);
    }

    public function addLinkArea(float $x, float $y, float $width, float $height, string $url): void
    {
        $this->adaptor->addLinkArea($x, $y, $width, $height, $url);
    }

    public function createLink(): int
    {
        return $this->adaptor->createInternalLink();
    }

    public function setLinkDestination(string $link, float $y, int $page = -1): void
    {
        $this->adaptor->setLinkDestination($link, $y, $page);
    }
}
