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

class PdfRenderer extends AbstractRenderer
{
    /**
     * PDF compression - Zlib extension is required
     *
     * @var bool const
     */
    private const bool COMPRESSION = true;

    /**
     * If true reduce the RAM memory usage by caching temporary data on filesystem (slower).
     *
     * @var bool const
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

    public function header(): void
    {
        foreach ($this->headerElements as $element) {
            $element->render($this);
        }
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
            if ($this->tcpdf->GetY() + $element->getFootnoteHeight($this) > $this->tcpdf->getPageHeight()) {
                $this->tcpdf->AddPage();
            }

            $element->renderFootnote($this);

            if ($this->tcpdf->GetY() > $this->tcpdf->getPageHeight()) {
                $this->tcpdf->AddPage();
            }
        }
    }

    public function footer(): void
    {
        foreach ($this->footerElements as $element) {
            $element->render($this);
        }
    }

    public function setCurrentStyle(string $s): void
    {
        $this->currentStyle = $s;
        $style              = $this->getStyle($s);
        $this->tcpdf->setFont($style['font'], $style['style'], $style['size']);
    }

    /**
     * Get the style -PDF
     *
     * @param string $s Style name
     *
     * @return array{'name': string, 'font': string, 'style': string, 'size': float}
     */
    public function getStyle(string $s): array
    {
        return $this->styles[$s] ?? $this->styles[$this->getCurrentStyle()];
    }

    /**
     * Add margin when static horizontal position is used -PDF
     * RTL supported
     *
     * @param float $x Static position
     */
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

    public function getFootnotesHeight(float $cellWidth = 0): float
    {
        $h = 0;
        foreach ($this->printedfootnotes as $element) {
            $h += $element->getHeight($this);
        }

        return $h;
    }

    public function checkFootnote(AbstractFootnote $footnote): AbstractFootnote|false
    {
        $ct  = count($this->printedfootnotes);
        $val = $footnote->getValue();
        $i   = 0;
        while ($i < $ct) {
            if ($this->printedfootnotes[$i]->getValue() === $val) {
                // If this footnote already exist then set up the numbers for this object
                $footnote->setNum($i + 1);
                $footnote->setAddlink((string) ($i + 1));

                return $this->printedfootnotes[$i];
            }
            $i++;
        }
        // If this Footnote has not been set up yet
        $footnote->setNum($ct + 1);
        $footnote->setAddlink((string) $this->tcpdf->AddLink());
        $this->printedfootnotes[] = $footnote;

        return false;
    }

    /**
     * Add a new page, ensuring images on later pages are not overwritten.
     */
    public function newPage(): void
    {
        if ($this->lastpicpage > $this->tcpdf->getPage()) {
            $this->tcpdf->setPage($this->lastpicpage);
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

    public function setup(): void
    {
        parent::setup();

        $this->tcpdf = new TcpdfWrapper(
            $this->orientation,
            self::UNITS,
            [$this->page_width, $this->page_height],
            self::UNICODE,
            'UTF-8',
            self::DISK_CACHE
        );

        $this->tcpdf->setMargins($this->left_margin, $this->top_margin, $this->right_margin);
        $this->tcpdf->setHeaderMargin($this->header_margin);
        $this->tcpdf->setFooterMargin($this->footer_margin);
        $this->tcpdf->setAutoPageBreak(true, $this->bottom_margin);
        $this->tcpdf->setFontSubsetting(self::SUBSETTING);
        $this->tcpdf->setCompression(self::COMPRESSION);
        $this->tcpdf->setRTL($this->rtl);
        $this->tcpdf->setCreator(Webtrees::NAME . ' ' . Webtrees::VERSION);
        $this->tcpdf->setAuthor($this->rauthor);
        $this->tcpdf->setTitle($this->title);
        $this->tcpdf->setSubject($this->rsubject);
        $this->tcpdf->setKeywords($this->rkeywords);
        $this->tcpdf->setHeaderData('', 0, $this->title);
        $this->tcpdf->setHeaderFont([$this->default_font, '', $this->default_font_size]);

        if ($this->show_generated_by) {
            // The default style name for Generated by.... is 'genby'
            $element = new PdfCell(0.0, 10.0, '', 'C', '', 'genby', 1, AbstractElement::CURRENT_POSITION, AbstractElement::CURRENT_POSITION, false, 0, '', '', true);
            $element->addText($this->generated_by);
            $element->setUrl(Webtrees::URL);
            $this->addElementToFooter($element);
        }
    }

    public function run(): void
    {
        $this->body();
        echo $this->tcpdf->Output('doc.pdf', 'S');
    }

    public function createCell(float $width, float $height, string $border, string $align, string $bgcolor, string $style, int $ln, float $top, float $left, bool $fill, int $stretch, string $bocolor, string $tcolor, bool $reseth): PdfCell
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

    public function createText(string $style, string $color): PdfText
    {
        return new PdfText($style, $color);
    }

    public function createFootnote(string $style): PdfFootnote
    {
        return new PdfFootnote($style);
    }

    public function createImage(
        string $file,
        float $x,
        float $y,
        float $w,
        float $h,
        string $align,
        string $ln,
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
        string $align,
        string $ln
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
}
