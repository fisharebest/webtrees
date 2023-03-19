<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Webtrees;

use function count;

/**
 * Class PdfRenderer
 */
class PdfRenderer extends AbstractRenderer
{
    /**
     * PDF compression - Zlib extension is required
     *
     * @var bool const
     */
    private const COMPRESSION = true;

    /**
     * If true reduce the RAM memory usage by caching temporary data on filesystem (slower).
     *
     * @var bool const
     */
    private const DISK_CACHE = false;

    /**
     * true means that the input text is unicode (PDF)
     *
     * @var bool const
     */
    private const UNICODE = true;

    // Font sub-setting in TCPDF is slow.
    private const SUBSETTING = false;

    public TcpdfWrapper $tcpdf;

    /** @var array<ReportPdfFootnote> Array of elements in the footer notes */
    public array $printedfootnotes = [];

    // The last cell height
    public float $lastCellHeight = 0.0;

    // The largest font size within a TextBox to calculate the height
    public float $largestFontHeight = 0.0;

    // The last pictures page number
    public int $lastpicpage = 0;

    /**
     * PDF Header -PDF
     *
     * @return void
     */
    public function header(): void
    {
        foreach ($this->headerElements as $element) {
            if ($element instanceof ReportBaseElement) {
                $element->render($this);
            } elseif ($element === 'footnotetexts') {
                $this->footnotes();
            } elseif ($element === 'addpage') {
                $this->newPage();
            }
        }
    }

    /**
     * PDF Body -PDF
     *
     * @return void
     */
    public function body(): void
    {
        $this->tcpdf->AddPage();

        foreach ($this->bodyElements as $element) {
            if ($element instanceof ReportBaseElement) {
                $element->render($this);
            } elseif ($element === 'footnotetexts') {
                $this->footnotes();
            } elseif ($element === 'addpage') {
                $this->newPage();
            }
        }
    }

    /**
     * Generate footnotes
     *
     * @return void
     */
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

    /**
     * PDF Footer -PDF
     *
     * @return void
     */
    public function footer(): void
    {
        foreach ($this->footerElements as $element) {
            if ($element instanceof ReportBaseElement) {
                $element->render($this);
            } elseif ($element === 'footnotetexts') {
                $this->footnotes();
            } elseif ($element === 'addpage') {
                $this->newPage();
            }
        }
    }

    /**
     * Remove the header.
     *
     * @param int $index
     *
     * @return void
     */
    public function removeHeader(int $index): void
    {
        unset($this->headerElements[$index]);
    }

    /**
     * Remove the body.
     *
     * @param int $index
     *
     * @return void
     */
    public function removeBody(int $index): void
    {
        unset($this->bodyElements[$index]);
    }

    /**
     * Clear the Header -PDF
     *
     * @return void
     */
    public function clearHeader(): void
    {
        unset($this->headerElements);
        $this->headerElements = [];
    }

    /**
     * Get the currently used style name -PDF
     *
     * @return string
     */
    public function getCurrentStyle(): string
    {
        return $this->currentStyle;
    }

    /**
     * Setup a style for usage -PDF
     *
     * @param string $s Style name
     *
     * @return void
     */
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
     *
     * @return float
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

    /**
     * Get the maximum line width to draw from the curren position -PDF
     * RTL supported
     *
     * @return float
     */
    public function getMaxLineWidth(): float
    {
        $m = $this->tcpdf->getMargins();
        if ($this->tcpdf->getRTL()) {
            return $this->tcpdf->getRemainingWidth() + $m['right'];
        }

        return $this->tcpdf->getRemainingWidth() + $m['left'];
    }

    /**
     * Get the height of the footnote.
     *
     * @return float
     */
    public function getFootnotesHeight(): float
    {
        $h = 0;
        foreach ($this->printedfootnotes as $element) {
            $h += $element->getHeight($this);
        }

        return $h;
    }

    /**
     * Returns the the current font size height -PDF
     *
     * @return float
     */
    public function getCurrentStyleHeight(): float
    {
        if ($this->currentStyle === '') {
            return $this->default_font_size;
        }
        $style = $this->getStyle($this->currentStyle);

        return $style['size'];
    }

    /**
     * Checks the Footnote and numbers them
     *
     * @param ReportPdfFootnote $footnote
     *
     * @return ReportPdfFootnote|bool object if already numbered, false otherwise
     */
    public function checkFootnote(ReportPdfFootnote $footnote)
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
     * Used this function instead of AddPage()
     * This function will make sure that images will not be overwritten
     *
     * @return void
     */
    public function newPage(): void
    {
        if ($this->lastpicpage > $this->tcpdf->getPage()) {
            $this->tcpdf->setPage($this->lastpicpage);
        }
        $this->tcpdf->AddPage();
    }

    /**
     * Add a page if needed -PDF
     *
     * @param float $height Cell height
     *
     * @return bool true in case of page break, false otherwise
     */
    public function checkPageBreakPDF(float $height): bool
    {
        return $this->tcpdf->checkPageBreak($height);
    }

    /**
     * Returns the remaining width between the current position and margins -PDF
     *
     * @return float Remaining width
     */
    public function getRemainingWidthPDF(): float
    {
        return $this->tcpdf->getRemainingWidth();
    }
    /**
     * PDF Setup - ReportPdf
     *
     * @return void
     */
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
            $element = new ReportPdfCell(0.0, 10.0, '', 'C', '', 'genby', 1, ReportBaseElement::CURRENT_POSITION, ReportBaseElement::CURRENT_POSITION, false, 0, '', '', true);
            $element->addText($this->generated_by);
            $element->setUrl(Webtrees::URL);
            $this->addElementToFooter($element);
        }
    }

    /**
     * Run the report.
     *
     * @return void
     */
    public function run(): void
    {
        $this->body();
        echo $this->tcpdf->Output('doc.pdf', 'S');
    }

    /**
     * Create a new Cell object.
     *
     * @param float  $width   cell width (expressed in points)
     * @param float  $height  cell height (expressed in points)
     * @param string $border  Border style
     * @param string $align   Text alignment
     * @param string $bgcolor Background color code
     * @param string $style   The name of the text style
     * @param int    $ln      Indicates where the current position should go after the call
     * @param float  $top     Y-position
     * @param float  $left    X-position
     * @param bool   $fill    Indicates if the cell background must be painted (1) or transparent (0). Default value: 1
     * @param int    $stretch Stretch carachter mode
     * @param string $bocolor Border color
     * @param string $tcolor  Text color
     * @param bool   $reseth
     *
     * @return ReportBaseCell
     */
    public function createCell(float $width, float $height, string $border, string $align, string $bgcolor, string $style, int $ln, float $top, float $left, bool $fill, int $stretch, string $bocolor, string $tcolor, bool $reseth): ReportBaseCell
    {
        return new ReportPdfCell($width, $height, $border, $align, $bgcolor, $style, $ln, $top, $left, $fill, $stretch, $bocolor, $tcolor, $reseth);
    }

    /**
     * Create a new TextBox object.
     *
     * @param float  $width   Text box width
     * @param float  $height  Text box height
     * @param bool   $border
     * @param string $bgcolor Background color code in HTML
     * @param bool   $newline
     * @param float  $left
     * @param float  $top
     * @param bool   $pagecheck
     * @param string $style
     * @param bool   $fill
     * @param bool   $padding
     * @param bool   $reseth
     *
     * @return ReportBaseTextbox
     */
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
    ): ReportBaseTextbox {
        return new ReportPdfTextBox($width, $height, $border, $bgcolor, $newline, $left, $top, $pagecheck, $style, $fill, $padding, $reseth);
    }

    /**
     * Create a text element.
     *
     * @param string $style
     * @param string $color
     *
     * @return ReportBaseText
     */
    public function createText(string $style, string $color): ReportBaseText
    {
        return new ReportPdfText($style, $color);
    }

    /**
     * Create a new Footnote object.
     *
     * @param string $style Style name
     *
     * @return ReportBaseFootnote
     */
    public function createFootnote(string $style): ReportBaseFootnote
    {
        return new ReportPdfFootnote($style);
    }

    /**
     * Create a new image object.
     *
     * @param string $file  Filename
     * @param float  $x
     * @param float  $y
     * @param float  $w     Image width
     * @param float  $h     Image height
     * @param string $align L:left, C:center, R:right or empty to use x/y
     * @param string $ln    T:same line, N:next line
     *
     * @return ReportBaseImage
     */
    public function createImage(string $file, float $x, float $y, float $w, float $h, string $align, string $ln): ReportBaseImage
    {
        return new ReportPdfImage($file, $x, $y, $w, $h, $align, $ln);
    }

    /**
     * Create a new image object from Media Object.
     *
     * @param MediaFile $media_file
     * @param float     $x
     * @param float     $y
     * @param float     $w     Image width
     * @param float     $h     Image height
     * @param string    $align L:left, C:center, R:right or empty to use x/y
     * @param string    $ln    T:same line, N:next line
     *
     * @return ReportBaseImage
     */
    public function createImageFromObject(
        MediaFile $media_file,
        float $x,
        float $y,
        float $w,
        float $h,
        string $align,
        string $ln
    ): ReportBaseImage {
        return new ReportPdfImage('@' . $media_file->fileContents(), $x, $y, $w, $h, $align, $ln);
    }

    /**
     * Create a line.
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     *
     * @return ReportBaseLine
     */
    public function createLine(float $x1, float $y1, float $x2, float $y2): ReportBaseLine
    {
        return new ReportPdfLine($x1, $y1, $x2, $y2);
    }
}
