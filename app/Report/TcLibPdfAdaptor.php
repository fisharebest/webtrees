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
use RuntimeException;

use function array_values;
use function count;
use function in_array;
use function is_array;
use function max;
use function min;
use function preg_split;
use function sprintf;
use function strtolower;

use const PREG_SPLIT_NO_EMPTY;

final class TcLibPdfAdaptor
{
    private readonly TextWrapper $text_wrapper;


    private readonly PdfPageState $pdf_page_state;

    private readonly PdfPageGeometry $pdf_page_geometry;

    private readonly PdfInternalLinkService $pdf_internal_link_service;

    private float $font_size = 10.0;

    private string $font_family = '';

    private readonly string $primary_font;

    /** @var list<string> */
    private readonly array $fallback_fonts;

    private readonly FontCoverageIndex $font_coverage_index;

    /** @var array<int,int> */
    private array $unsupported_codepoints = [];


    // Consistent cell padding applied to all text cells (left and right).
    // This insets text from the cell edge, improves readability, and ensures
    // that our splitTextIntoLines() width calculation matches the effective
    // text width used by tc-lib-pdf's addTextCell() during rendering.
    private const float CELL_PADDING = 1.0;

    private readonly bool $is_rtl;

    // Active colors for draw, fill, and text operations.
    private HexColor $draw_color;
    private HexColor $fill_color;
    private HexColor $text_color;


    public function __construct(
        private readonly Tcpdf $tcpdf,
        private readonly PdfRenderTargetInterface $renderer,
        Config $config,
    ) {
        $this->draw_color = new HexColor('#000000');
        $this->fill_color = new HexColor('#FFFFFF');
        $this->text_color = new HexColor('#000000');
        $this->font_coverage_index = new FontCoverageIndex();
        $this->primary_font = $config->primary_font;
        $this->fallback_fonts = $config->fallback_fonts;

        $this->pdf_page_state = new PdfPageState();
        $this->pdf_internal_link_service = new PdfInternalLinkService(new InternalLinkRegistry());

        $this->tcpdf->setDefaultCellPadding(self::CELL_PADDING, self::CELL_PADDING, self::CELL_PADDING, self::CELL_PADDING);
        $this->text_wrapper = new TextWrapper(new PdfTextMeasurer($renderer));

        // Apply page layout from the report configuration.
        $is_landscape = $config->orientation === PageOrientation::Landscape;
        $this->pdf_page_geometry = new PdfPageGeometry(
            page_width: $is_landscape ? $config->paper_height : $config->paper_width,
            page_height: $is_landscape ? $config->paper_width : $config->paper_height,
            left_margin: $config->left_margin,
            right_margin: $config->right_margin,
            bottom_margin: $config->bottom_margin,
            header_margin: $config->header_margin,
        );

        $this->is_rtl = $config->rtl;
        $this->tcpdf->setRTL($config->rtl);
        $this->tcpdf->setCreator($config->author);
        $this->tcpdf->setAuthor($config->author);
        $this->tcpdf->setTitle($config->title);
        $this->tcpdf->setSubject($config->description);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function addPage(array $data = []): array
    {
        // Add a footer to the previous page (if there was a previous page).
        if ($this->pdf_page_state->hasCurrentPage()) {
            $this->renderFooter();
        }

        if ($data === []) {
            // Keep tc-lib-pdf margins at zero for the backing page region.
            // LayoutEngine/PdfBlockWriter already apply report margins through
            // absolute coordinates, and non-zero region offsets can trigger early
            // addTextCell pagination in tc-lib-pdf.
            $data = [
                'orientation' => $this->is_rtl ? 'R' : '',
                'width' => $this->pdf_page_geometry->page_width,
                'height' => $this->pdf_page_geometry->page_height,
                'margin' => [
                    'PL' => 0.0,
                    'PR' => 0.0,
                    'PT' => 0.0,
                    'HB' => 0.0,
                    'PB' => 0.0,
                    'FT' => 0.0,
                ],
            ];
        }

        $page = $this->tcpdf->addPage($data);

        $this->pdf_page_state->incrementPage();
        $this->renderHeader();

        return $page;
    }

    public function pageNumber(): int
    {
        return $this->pdf_page_state->currentPageNumber();
    }

    public function getPage(): int
    {
        return $this->pdf_page_state->currentPageIndex();
    }

    public function getRTL(): bool
    {
        return $this->is_rtl;
    }

    public function output(): string
    {
        $this->renderFooter();

        return $this->tcpdf->getOutPDFString();
    }

    /**
     * @return list<string>
     */
    public function unsupportedGlyphDiagnostics(): array
    {
        $diagnostics = [];

        foreach ($this->unsupported_codepoints as $codepoint => $count) {
            $diagnostics[] = sprintf('U+%04X x%d', $codepoint, $count);
        }

        return array_values($diagnostics);
    }

    // Public wrapper API: drawing primitives and text/image output.

    public function setFont(string $family, string $style = '', float $size = 0): void
    {
        $this->switchFont($family, $style, $size, true);
    }

    public function getStringWidth(string $text): float
    {
        if ($text === '') {
            return 0.0;
        }

        // If report defaults ever switch back to DejaVu Sans, this is the
        // place to add an FSI/PDI substitution for fonts that cannot keep
        // U+2068/U+2069 non-visible.

        $saved_font_style = (string) $this->tcpdf->font->getCurrentFont()['style'];
        $saved_font_size = $this->font_size;
        $saved_font_family = $this->font_family;
        $font_runs = $this->splitTextIntoFontRuns($text);
        $text_width = 0.0;

        foreach ($font_runs as $font_run) {
            $this->switchFont($font_run['font'], $saved_font_style, $saved_font_size, false);
            $text_width += $this->measureCurrentFontTextWidth($font_run['text']);
        }

        $this->switchFont($saved_font_family, $saved_font_style, $saved_font_size, false);

        return $text_width;
    }

    public function setDrawColor(HexColor $color): void
    {
        $this->draw_color = $color;
    }

    public function setFillColor(HexColor $color): void
    {
        $this->fill_color = $color;
    }

    public function setTextColor(HexColor $color): void
    {
        $this->text_color = $color;
    }

    public function resetColors(): void
    {
        $black = new HexColor('#000000');
        $this->setDrawColor($black);
        $this->setTextColor($black);
    }

    public function drawTextBlock(
        string $text,
        float $x,
        float $y,
        float $width,
        float $height,
        string $align,
        float $line_height,
        bool $with_padding = true,
    ): void {
        if ($text === '' || $width <= 0.0 || $height <= 0.0 || $line_height <= 0.0) {
            return;
        }

        // If report defaults ever switch back to DejaVu Sans, this is the
        // place to add an FSI/PDI substitution for fonts that cannot keep
        // U+2068/U+2069 non-visible.


        $text_width = $with_padding ? $width - self::CELL_PADDING * 2 : $width;
        if ($text_width <= 0.0) {
            return;
        }

        $halign = match ($align) {
            'L' => 'L',
            'C' => 'C',
            'R' => 'R',
            'J' => 'J',
            default => $this->is_rtl ? 'R' : 'L',
        };

        // Width measurements are based on current PDF font state.
        // Save the caller's font style/size so we can restore it after
        // wrapText() — the PdfTextMeasurer changes the renderer's current
        // style during measurement which would corrupt the rendering font.
        $saved_font_style = (string) $this->tcpdf->font->getCurrentFont()['style'];
        // Style expects lowercase flags, while TCPDF uses uppercase.
        $saved_style_flags = strtolower($saved_font_style);

        $saved_font_size = $this->font_size;

        $style = new Style(name: 'pdf-current', style: $saved_style_flags, size: $saved_font_size);
        $lines = $this->text_wrapper->wrapText($text, $style, $text_width);
        $block_bottom = $y + $height;

        // Restore the font to the caller's style before rendering lines.
        // PdfTextMeasurer may have changed it during word-by-word measurement.
        $this->setFont('', $saved_font_style, $saved_font_size);

        $this->emitTextColor();

        foreach ($lines as $line_index => $line_text) {
            if ($line_text === '') {
                continue;
            }

            $line_y = $y + $line_index * $line_height;
            if ($line_y >= $block_bottom) {
                break;
            }

            $cell_height = min($line_height, $block_bottom - $line_y);

            $this->drawTextLineWithFallbackRuns($line_text, $x, $line_y, $width, $cell_height, $halign, $with_padding);
        }
    }

    public function drawImage(string $source, float $x, float $y, float $width, float $height): void
    {
        $image_id = $this->tcpdf->image->add($source);

        $image_key = $this->tcpdf->image->getKey($source, 0, 0, 100);
        $image_data = $this->tcpdf->image->getImageDataByKey($image_key);
        $source_width = (float) $image_data['width'];
        $source_height = (float) $image_data['height'];

        // Resolve missing dimensions proportionally from the source aspect ratio.
        if ($width <= 0.0 && $height <= 0.0) {
            $width = max(1.0, $this->tcpdf->toUnit($source_width));
            $height = max(1.0, $this->tcpdf->toUnit($source_height));
        } elseif ($width <= 0.0) {
            $ratio = $source_height > 0.0 ? $source_width / $source_height : 1.0;
            $width = max(1.0, $height * $ratio);
        } elseif ($height <= 0.0) {
            $ratio = $source_width > 0.0 ? $source_height / $source_width : 1.0;
            $height = max(1.0, $width * $ratio);
        }

        $this->addCurrentPageContent(
            $this->tcpdf->image->getSetImage($image_id, $x, $y, $width, $height, $this->pdf_page_geometry->page_height)
        );
    }

    public function drawLine(float $x1, float $y1, float $x2, float $y2): void
    {
        $this->emitDrawColor();
        $this->addCurrentPageContent($this->tcpdf->graph->getLine($x1, $y1, $x2, $y2));
    }

    public function drawRect(float $x, float $y, float $width, float $height, string $style = 'S'): void
    {
        $mode = match ($style) {
            'F'        => 'F',
            'DF', 'FD' => 'B',
            default    => 'S',
        };

        if ($mode === 'F' || $mode === 'B') {
            $this->emitFillColor();
        }

        if ($mode === 'S' || $mode === 'B') {
            $this->emitDrawColor();
        }

        $this->addCurrentPageContent($this->tcpdf->graph->getRect($x, $y, $width, $height, $mode));
    }

    // Public wrapper API: link management.

    public function createInternalLink(): int
    {
        return $this->pdf_internal_link_service->createLink($this->pdf_page_state->currentPageIndex());
    }

    public function addLinkArea(float $x, float $y, float $width, float $height, string $url): void
    {
        $destination = $this->pdf_internal_link_service->resolveDestination(
            $url,
            fn (int $page, float $link_y): string => $this->tcpdf->addInternalLink($page, $link_y),
        );

        $annotation_id = $this->tcpdf->setLink($x, $y, $width, $height, $destination);
        $this->tcpdf->page->addAnnotRef($annotation_id);
    }

    public function setLinkDestination(int|string $link, float $y, int $page = -1): void
    {
        $this->pdf_internal_link_service->setDestination(
            $link,
            $y,
            $this->pdf_page_state->currentPageIndex(),
            $page,
        );
    }

    // Internal helpers.

    private function addCurrentPageContent(string $content): void
    {
        $this->tcpdf->page->addContent($content);
    }

    private function renderHeader(): void
    {
        $this->renderer->header(
            // The LayoutEngine already mirrors x positions for RTL pages, so
            // report sections always start at the left margin.
            $this->pdf_page_geometry->left_margin,
            $this->pdf_page_geometry->header_margin,
        );
    }

    private function renderFooter(): void
    {

        $this->renderer->footer(
            // The LayoutEngine already mirrors x positions for RTL pages, so
            // report sections always start at the left margin.
            $this->pdf_page_geometry->left_margin,
            $this->pdf_page_geometry->page_height - $this->pdf_page_geometry->bottom_margin,
        );
    }

    private function addTextCellAt(
        string $line_text,
        float $cell_x,
        float $line_y,
        float $cell_width,
        float $cell_height,
        string $horizontal_align,
        bool $with_padding = true,
    ): void {
        if (!$with_padding) {
            // tc-lib-pdf applies global default cell padding to addTextCell().
            // Compensate so unpadded flows are rendered at exact block coordinates.
            $cell_x -= self::CELL_PADDING;
            $line_y -= self::CELL_PADDING;
            $cell_width += self::CELL_PADDING * 2;
            $cell_height += self::CELL_PADDING * 2;
        }

        $this->tcpdf->addTextCell(
            $line_text,
            -1,
            $cell_x,
            $line_y,
            $cell_width,
            $cell_height,
            0,
            0,
            'T',
            $horizontal_align,
            null,
            [],
            0,
            0,
            0,
            0,
            true,
            true,
            false,
            false,
            false,
            false,
            false,
            false,
        );
    }

    private function emitDrawColor(): void
    {
        $this->addCurrentPageContent(
            $this->tcpdf->color->getPdfColor($this->draw_color->hex(), true)
        );
    }

    private function emitFillColor(): void
    {
        $this->addCurrentPageContent(
            $this->tcpdf->color->getPdfColor($this->fill_color->hex(), false)
        );
    }

    private function emitTextColor(): void
    {
        $this->addCurrentPageContent(
            $this->tcpdf->color->getPdfColor($this->text_color->hex(), false)
        );
    }

    private function switchFont(string $family, string $style, float $size, bool $emit_font_operation): void
    {
        if ($size > 0.0) {
            $this->font_size = $size;
        }

        if ($family !== '') {
            $this->font_family = $family;
        }

        $this->tcpdf->font->insert($this->tcpdf->pon, $this->font_family, $style, $this->font_size);

        if ($emit_font_operation && $this->pdf_page_state->hasCurrentPage()) {
            $this->addCurrentPageContent($this->tcpdf->font->getOutCurrentFont());
        }
    }

    private function drawTextLineWithFallbackRuns(
        string $line_text,
        float $cell_x,
        float $line_y,
        float $cell_width,
        float $cell_height,
        string $horizontal_align,
        bool $with_padding,
    ): void {
        $font_runs = $this->splitTextIntoFontRuns($line_text);

        if (count($font_runs) <= 1) {
            $this->addTextCellAt($line_text, $cell_x, $line_y, $cell_width, $cell_height, $horizontal_align, $with_padding);

            return;
        }

        $saved_font_style = (string) $this->tcpdf->font->getCurrentFont()['style'];
        $saved_font_size = $this->font_size;
        $saved_font_family = $this->font_family;

        $line_width = 0.0;
        foreach ($font_runs as $font_run) {
            $this->switchFont($font_run['font'], $saved_font_style, $saved_font_size, false);
            $line_width += $this->measureCurrentFontTextWidth($font_run['text']);
        }

        $available_width = $with_padding
            ? $cell_width - self::CELL_PADDING * 2
            : $cell_width;

        if ($available_width <= 0.0) {
            $this->switchFont($saved_font_family, $saved_font_style, $saved_font_size, true);

            return;
        }

        $text_origin_x = $with_padding
            ? $cell_x + self::CELL_PADDING
            : $cell_x;

        $line_start_x = match ($horizontal_align) {
            'C' => $text_origin_x + max(0.0, ($available_width - $line_width) / 2),
            'R' => $text_origin_x + max(0.0, $available_width - $line_width),
            default => $text_origin_x,
        };

        foreach ($font_runs as $font_run) {
            $this->switchFont($font_run['font'], $saved_font_style, $saved_font_size, true);
            $run_width = $this->measureCurrentFontTextWidth($font_run['text']);

            if ($run_width <= 0.0) {
                continue;
            }

            $this->addTextCellAt(
                $font_run['text'],
                $line_start_x,
                $line_y,
                $run_width,
                $cell_height,
                'L',
                false,
            );

            $line_start_x += $run_width;
        }

        $this->switchFont($saved_font_family, $saved_font_style, $saved_font_size, true);
    }

    private function measureCurrentFontTextWidth(string $text): float
    {
        if ($text === '') {
            return 0.0;
        }

        $ord_array = $this->tcpdf->uniconv->strToOrdArr($text);
        $width_points = $this->tcpdf->font->getOrdArrWidth($ord_array);

        return $this->tcpdf->toUnit($width_points);
    }

    /**
     * @return list<string>
     */
    private function fallbackFontOrder(): array
    {
        $active_font = $this->font_family !== '' ? $this->font_family : $this->primary_font;
        $font_order = [$active_font];

        foreach ($this->fallback_fonts as $fallback_font) {
            if ($fallback_font !== '' && !in_array($fallback_font, $font_order, true)) {
                $font_order[] = $fallback_font;
            }
        }

        return array_values($font_order);
    }

    /**
     * @return list<array{font:string,text:string}>
     */
    private function splitTextIntoFontRuns(string $text): array
    {
        if ($text === '') {
            return [];
        }

        $font_order = $this->fallbackFontOrder();
        $characters = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);

        if ($characters === false || !is_array($characters)) {
            throw new RuntimeException('Unable to split text into Unicode characters.');
        }

        $font_runs = [];

        foreach ($characters as $character) {
            $ord_array = $this->tcpdf->uniconv->strToOrdArr($character);
            $codepoint = $ord_array[0] ?? null;

            if ($codepoint === null) {
                throw new RuntimeException('Unable to determine Unicode codepoint for text run segmentation.');
            }

            $selected_font = $this->font_coverage_index->firstSupportingFont($font_order, $codepoint);
            $output_character = $character;

            if ($selected_font === null) {
                $this->unsupported_codepoints[$codepoint] = ($this->unsupported_codepoints[$codepoint] ?? 0) + 1;
                $selected_font = $font_order[0];
            }

            $last_run_index = count($font_runs) - 1;

            if ($last_run_index >= 0 && $font_runs[$last_run_index]['font'] === $selected_font) {
                $font_runs[$last_run_index]['text'] .= $output_character;
            } else {
                $font_runs[] = [
                    'font' => $selected_font,
                    'text' => $output_character,
                ];
            }
        }

        return $font_runs;
    }
}
