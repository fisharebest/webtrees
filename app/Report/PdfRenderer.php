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
use Fisharebest\Webtrees\Webtrees;

use function count;

class PdfRenderer extends AbstractRenderer implements PdfRendererInterface
{
    /**
     * PDF compression - Zlib extension is required
     */
    private const bool COMPRESSION = true;

    /**
     * If true reduce the RAM memory usage by caching temporary data on filesystem (slower).
     */
    private const bool DISK_CACHE = false;

    /**
     * true means that the input text is unicode (PDF)
     *
     * @var bool const
     */
    private const bool UNICODE = true;

    // Font sub-setting in TCPDF is slow.
    private const bool SUBSETTING = false;

    // Tracks the page an image was last rendered on, for page-break recovery.
    private int $lastpicpage = 0;

    private TcpdfWrapper $tcpdf;

    public function header(): void
    {
        // Save the current style — TCPDF restores its own graphics state
        // after Header(), but we must keep our style tracker in sync.
        $saved_style = $this->currentStyle;

        foreach ($this->headerElements as $element) {
            $element->render($this);
        }

        $this->currentStyle = $saved_style;
    }

    public function body(): void
    {
        $this->tcpdf->AddPage();

        foreach ($this->bodyElements as $element) {
            $element->render($this);
        }
    }

    public function footnotes(): void
    {
        foreach ($this->printedfootnotes as $element) {
            if ($this->tcpdf->GetY() + $element->getFootnoteHeight($this) > $this->getPageHeight()) {
                $this->tcpdf->AddPage();
            }

            $element->renderFootnote($this);

            if ($this->tcpdf->GetY() > $this->getPageHeight()) {
                $this->tcpdf->AddPage();
            }
        }
    }

    public function footer(): void
    {
        $saved_style = $this->currentStyle;

        foreach ($this->footerElements as $element) {
            $element->render($this);
        }

        $this->currentStyle = $saved_style;
    }

    public function setCurrentStyle(Style $style): void
    {
        if ($this->currentStyle !== $style) {
            $this->currentStyle = $style;
            $this->tcpdf->setFont($this->config->font, $style->style, $style->size);
        }
    }

    /**
     * Return the current PDF page number, delegating to TCPDF.
     *
     * This override allows element renderers to obtain the current page
     * number through the same {@see AbstractRenderer::pageNo()} interface
     * used by the HTML backend, so that
     * {@see AbstractElement::resolvedText()} can stay backend-agnostic.
     */
    public function pageNo(): int
    {
        return $this->tcpdf->PageNo();
    }

    public function addMarginX(float $x): float
    {
        $m = $this->tcpdf->getMargins();
        if ($this->tcpdf->getRTL()) {
            $x += $m['right'];
        } else {
            $x += $m['left'];
        }
        $this->tcpdf->setX($x);

        return $x;
    }

    public function getMaxLineWidth(): float
    {
        $m = $this->tcpdf->getMargins();
        if ($this->tcpdf->getRTL()) {
            return $this->tcpdf->getRemainingWidth() + $m['right'];
        }

        return $this->tcpdf->getRemainingWidth() + $m['left'];
    }

    public function checkFootnote(AbstractFootnote $footnote): void
    {
        $val = $footnote->getValue();

        foreach ($this->printedfootnotes as $i => $printed_footnote) {
            if ($printed_footnote->getValue() === $val) {
                // Previously seen footnote
                $footnote->setNumAndLink($i + 1, (string) ($i + 1));

                return;
            }
        }

        // New footnote
        $num = count($this->printedfootnotes) + 1;
        $footnote->setNumAndLink($num, (string) $this->createLink());
        $this->printedfootnotes[] = $footnote;
    }

    /**
     * Add a new page, ensuring images on later pages are not overwritten.
     */
    public function newPage(): void
    {
        if ($this->getLastPicPage() > $this->tcpdf->getPage()) {
            $this->tcpdf->setPage($this->getLastPicPage());
        }
        $this->tcpdf->AddPage();
    }

    public function checkPageBreakPDF(float $height): bool
    {
        return $this->tcpdf->checkPageBreak($height);
    }

    public function getRemainingWidthPDF(): float
    {
        return $this->tcpdf->getRemainingWidth();
    }

    public function setup(ReportConfig $config): void
    {
        parent::setup($config);

        $this->tcpdf = new TcpdfWrapper(
            $this->config->orientation->value,
            self::UNITS,
            [$this->config->page_width, $this->config->page_height],
            self::UNICODE,
            'UTF-8',
            self::DISK_CACHE
        );

        $this->tcpdf->setRenderer($this);
        $this->tcpdf->setMargins($this->config->left_margin, $this->config->top_margin, $this->config->right_margin);
        $this->tcpdf->setHeaderMargin($this->config->header_margin);
        $this->tcpdf->setFooterMargin($this->config->footer_margin);
        $this->tcpdf->setAutoPageBreak(true, $this->config->bottom_margin);
        $this->tcpdf->setFontSubsetting(self::SUBSETTING);
        $this->tcpdf->setCompression(self::COMPRESSION);
        $this->tcpdf->setRTL($this->config->rtl);
        $this->tcpdf->setCreator(Webtrees::NAME . ' ' . Webtrees::VERSION);
        $this->tcpdf->setAuthor($this->config->author);
        $this->tcpdf->setTitle($this->config->title);
        $this->tcpdf->setSubject($this->config->description);

        if ($this->config->show_generated_by) {
            // The default style name for Generated by.... is 'genby'
            $element = new PdfCell(0.0, 10.0, '', CellAlign::Center, '', $this->getStyle('genby'), CellNewline::NextLine, AbstractElement::CURRENT_POSITION, AbstractElement::CURRENT_POSITION, false, 0, '', '', true);
            $element->addText($this->config->generated_by);
            $element->setUrl(Webtrees::URL);
            $this->addElementToFooter($element);
        }
    }

    public function run(): void
    {
        $this->body();
        echo $this->tcpdf->Output('doc.pdf', 'S');
    }

    public function createCell(float $width, float $height, string $border, CellAlign $align, string $bgcolor, Style $style, CellNewline $ln, float $top, float $left, bool $fill, int $stretch, string $bocolor, string $tcolor, bool $reseth): PdfCell
    {
        return new PdfCell($width, $height, $border, $align, $bgcolor, $style, $ln, $top, $left, $fill, $stretch, $bocolor, $tcolor, $reseth);
    }

    public function createTextBox(
        float $width,
        float $height,
        bool $border,
        string $bgcolor,
        bool $newline,
        float $left,
        float $top,
        bool $pagecheck,
        string $style,
        bool $fill,
        bool $padding,
        bool $reseth
    ): PdfTextBox {
        return new PdfTextBox($width, $height, $border, $bgcolor, $newline, $left, $top, $pagecheck, $style, $fill, $padding, $reseth);
    }

    public function createText(Style $style, string $color): PdfText
    {
        return new PdfText($style, $color);
    }

    public function createFootnote(Style $style): PdfFootnote
    {
        return new PdfFootnote($style);
    }

    public function createImage(
        string $file,
        float $x,
        float $y,
        float $w,
        float $h,
        CellAlign $align,
        ImageContinuation $ln,
    ): PdfImage {
        $src = '@' . file_get_contents($file);

        return new PdfImage($src, $x, $y, $w, $h, $align, $ln);
    }

    public function createImageFromObject(
        MediaFile $media_file,
        float $x,
        float $y,
        float $w,
        float $h,
        CellAlign $align,
        ImageContinuation $ln,
    ): PdfImage {
        // Send higher-resolution image at the same aspect ratio.
        $add_watermark = Registry::imageFactory()->fileNeedsWatermark($media_file, Auth::user());

        $data = Registry::imageFactory()->mediaFileThumbnail(
            $media_file,
            (int) ($w * 4),
            (int) ($h * 4),
            'crop',
            $add_watermark,
        );

        $src = '@' . $data;

        return new PdfImage($src, $x, $y, $w, $h, $align, $ln);
    }

    public function createLine(float $x1, float $y1, float $x2, float $y2): PdfLine
    {
        return new PdfLine($x1, $y1, $x2, $y2);
    }

    // =========================================================================
    // Cursor positioning
    // =========================================================================

    public function getX(): float
    {
        return $this->tcpdf->GetX();
    }

    public function getY(): float
    {
        return $this->tcpdf->GetY();
    }

    public function setX(float $x): void
    {
        $this->tcpdf->setX($x);
    }

    public function setY(float $y): void
    {
        $this->tcpdf->setY($y);
    }

    public function setXY(float $x, float $y): void
    {
        $this->tcpdf->setXY($x, $y);
    }

    // =========================================================================
    // Text measurement
    // =========================================================================

    public function getStringWidth(string $text): float
    {
        return $this->tcpdf->GetStringWidth($text);
    }

    public function getNumLines(string $text, float $width): int
    {
        return $this->tcpdf->getNumLines($text, $width);
    }

    public function getCellHeightRatio(): float
    {
        return $this->tcpdf->getCellHeightRatio();
    }

    /**
     * Return the height of the last rendered MultiCell.
     */
    public function getLastRenderedHeight(): float
    {
        return $this->tcpdf->getLastH();
    }

    // =========================================================================
    // Margins
    // =========================================================================

    /**
     * @return array<string, mixed>
     */
    public function getMargins(): array
    {
        return $this->tcpdf->getMargins();
    }

    public function setLeftMargin(float $margin): void
    {
        $this->tcpdf->setLeftMargin($margin);
    }

    public function setRightMargin(float $margin): void
    {
        $this->tcpdf->setRightMargin($margin);
    }

    // =========================================================================
    // Page operations
    // =========================================================================

    public function getPageWidth(): float
    {
        return $this->tcpdf->getPageWidth();
    }

    public function getPageHeight(): float
    {
        return $this->tcpdf->getPageHeight();
    }

    public function getPageIndex(): int
    {
        return $this->tcpdf->getPage();
    }

    public function setPageIndex(int $page): void
    {
        $this->tcpdf->setPage($page);
    }

    public function isRTL(): bool
    {
        return $this->tcpdf->getRTL();
    }

    // =========================================================================
    // Color operations
    // =========================================================================

    public function setFillColor(int $red, int $green, int $blue): void
    {
        $this->tcpdf->setFillColor($red, $green, $blue);
    }

    public function setDrawColor(int $red, int $green, int $blue): void
    {
        $this->tcpdf->setDrawColor($red, $green, $blue);
    }

    public function setTextColor(int $red, int $green, int $blue): void
    {
        $this->tcpdf->setTextColor($red, $green, $blue);
    }

    public function resetColors(): void
    {
        $this->tcpdf->resetColors();
    }

    // =========================================================================
    // Rendering operations
    // =========================================================================

    /**
     * Write a multi-line text cell with wrapping, border, and alignment.
     */
    public function multiCell(
        float $width,
        float $height,
        string $text,
        string $border,
        string $align,
        bool $fill,
        int $newline,
        float $x,
        float $y,
        bool $reseth,
        int $stretch,
        bool $is_html,
    ): void {
        $this->tcpdf->MultiCell(
            $width,
            $height,
            $text,
            $border,
            $align,
            $fill,
            $newline,
            $x,
            $y,
            $reseth,
            $stretch,
            $is_html
        );
    }

    /**
     * Write HTML content at the current position.
     */
    public function writeHTML(string $html, bool $newline = true, bool $fill = false, bool $reseth = true): void
    {
        $this->tcpdf->writeHTML($html, $newline, $fill, $reseth);
    }

    /**
     * Write text at the current position with a given line height and optional link.
     */
    public function writeText(float $height, string $text, string $link = ''): void
    {
        $this->tcpdf->Write($height, $text, $link);
    }

    /**
     * Embed an image on the page.
     */
    public function drawImage(
        string $file,
        float $x,
        float $y,
        float $width,
        float $height,
        string $type,
        string $link,
        string $ln,
        bool $fitonpage,
        int $dpi,
        string $align,
    ): void {
        $this->tcpdf->Image(
            $file,
            $x,
            $y,
            $width,
            $height,
            $type,
            $link,
            $ln,
            $fitonpage,
            $dpi,
            $align
        );
    }

    /**
     * Draw a line between two points.
     */
    public function drawLine(float $x1, float $y1, float $x2, float $y2): void
    {
        $this->tcpdf->Line($x1, $y1, $x2, $y2);
    }

    /**
     * Draw a rectangle.
     */
    public function drawRect(float $x, float $y, float $width, float $height, string $style): void
    {
        $this->tcpdf->Rect($x, $y, $width, $height, $style);
    }

    /**
     * Add a clickable link area on the current page.
     */
    public function addLinkArea(float $x, float $y, float $width, float $height, string $url): void
    {
        $this->tcpdf->Link($x, $y, $width, $height, $url);
    }

    // =========================================================================
    // Link management
    // =========================================================================

    /**
     * Create a new internal link reference and return its ID.
     */
    public function createLink(): int
    {
        return $this->tcpdf->AddLink();
    }

    /**
     * Set the destination of a previously created link.
     */
    public function setLinkDestination(string $link, float $y = -1, int $page = -1): void
    {
        $this->tcpdf->setLink($link, $y, $page);
    }

    // =========================================================================
    // Image page tracking
    // =========================================================================

    public function getLastPicPage(): int
    {
        return $this->lastpicpage;
    }

    public function setLastPicPage(int $page): void
    {
        $this->lastpicpage = $page;
    }
}
