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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Webtrees;

abstract class AbstractRenderer implements ElementContainerInterface
{
    // Reports layouts are measured in points.
    protected const string UNITS = 'pt';

    // A point is 1/72 of an inch
    private const float INCH_TO_POINTS = 72.0;
    private const float MM_TO_POINTS   = 72.0 / 25.4;

    protected const array PAPER_SIZES = [
        // ISO 216
        'A0'         => [841.0 * self::MM_TO_POINTS, 1189.0 * self::MM_TO_POINTS],
        'A1'         => [594.0 * self::MM_TO_POINTS, 841.0 * self::MM_TO_POINTS],
        'A2'         => [420.0 * self::MM_TO_POINTS, 594.0 * self::MM_TO_POINTS],
        'A3'         => [297.0 * self::MM_TO_POINTS, 420.0 * self::MM_TO_POINTS],
        'A4'         => [210.0 * self::MM_TO_POINTS, 297.0 * self::MM_TO_POINTS],
        // US
        'US-Letter'  => [8.5 * self::INCH_TO_POINTS, 11.0 * self::INCH_TO_POINTS],
        'US-Legal'   => [8.5 * self::INCH_TO_POINTS, 14.0 * self::INCH_TO_POINTS],
        'US-Tabloid' => [11.0 * self::INCH_TO_POINTS, 17.0 * self::INCH_TO_POINTS],
    ];

    public float $left_margin = 18.0 * self::MM_TO_POINTS;

    public float $right_margin = 9.9 * self::MM_TO_POINTS;

    public float $top_margin = 26.8 * self::MM_TO_POINTS;

    public float $bottom_margin = 21.6 * self::MM_TO_POINTS;

    public float $header_margin = 4.9 * self::MM_TO_POINTS;

    public float $footer_margin = 9.9 * self::MM_TO_POINTS;

    public string $orientation = 'portrait';

    public string $page_format = 'A4';

    public float $page_height = 0.0;

    public float $page_width = 0.0;

    /** @var array<string, Style> Style elements found in the document, keyed by name */
    public array $styles = [];

    public string $default_font = 'dejavusans';

    public float $default_font_size = 12.0;

    /** Which logical section of the report is currently being assembled. */
    protected ReportSection $processing = ReportSection::Header;

    public bool $rtl = false;

    public bool $show_generated_by = true;

    public string $generated_by = '';

    public string $title = '';

    public string $rauthor = Webtrees::NAME . ' ' . Webtrees::VERSION;

    public string $rkeywords = '';

    public string $rsubject = '';

    /** @var array<AbstractElement> */
    public array $headerElements = [];

    /** @var array<AbstractElement> */
    public array $footerElements = [];

    /** @var array<AbstractElement> */
    public array $bodyElements = [];

    public Style|null $currentStyle = null;

    // The largest font size within a TextBox, used to calculate text height
    public float $largestFontHeight = 0.0;

    // The last cell height
    public float $lastCellHeight = 0.0;

    /** @var array<AbstractFootnote> Footnotes that have been rendered or queued for rendering */
    public array $printedfootnotes = [];

    // Properties used by HTML element renderers
    public float $cPadding = 0.0;
    public float $cellHeightRatio = 1.0;
    public string $alignRTL = 'left';
    public string $entityRTL = '&lrm;';

    // Properties used by PDF element renderers
    public TcpdfWrapper $tcpdf;
    public int $lastpicpage = 0;

    public function addElement(AbstractElement $element): void
    {
        match ($this->processing) {
            ReportSection::Body   => $this->addElementToBody($element),
            ReportSection::Header => $this->addElementToHeader($element),
            ReportSection::Footer => $this->addElementToFooter($element),
        };
    }

    public function addElementToHeader(AbstractElement $element): void
    {
        $this->headerElements[] = $element;
    }

    public function addElementToBody(AbstractElement $element): void
    {
        $this->bodyElements[] = $element;
    }

    public function addElementToFooter(AbstractElement $element): void
    {
        $this->footerElements[] = $element;
    }

    abstract public function run(): void;

    abstract public function setCurrentStyle(Style $style): void;

    /**
     * @param float       $width   cell width (expressed in points)
     * @param float       $height  cell height (expressed in points)
     * @param string      $border  Border style
     * @param CellAlign   $align   Text alignment
     * @param string      $bgcolor Background color code
     * @param Style       $style   The text style
     * @param CellNewline $ln      Where the cursor should go after the call
     * @param float       $top     Y-position
     * @param float       $left    X-position
     * @param bool        $fill    Indicates if the cell background must be painted (1) or transparent (0). Default value: 1
     * @param int         $stretch Stretch character mode
     * @param string      $bocolor Border color
     * @param string      $tcolor  Text color
     */
    abstract public function createCell(
        float $width,
        float $height,
        string $border,
        CellAlign $align,
        string $bgcolor,
        Style $style,
        CellNewline $ln,
        float $top,
        float $left,
        bool $fill,
        int $stretch,
        string $bocolor,
        string $tcolor,
        bool $reseth
    ): AbstractCell;

    /**
     * @param float  $width   Text box width
     * @param float  $height  Text box height
     * @param string $bgcolor Background color code in HTML
     */
    abstract public function createTextBox(
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
    ): AbstractTextBox;

    abstract public function createText(Style $style, string $color): AbstractText;

    abstract public function createLine(float $x1, float $y1, float $x2, float $y2): AbstractLine;

    abstract public function createImage(
        string $file,
        float $x,
        float $y,
        float $w,
        float $h,
        CellAlign $align,
        ImageContinuation $ln,
    ): AbstractImage;

    abstract public function createImageFromObject(
        MediaFile $media_file,
        float $x,
        float $y,
        float $w,
        float $h,
        CellAlign $align,
        ImageContinuation $ln,
    ): AbstractImage;

    abstract public function createFootnote(Style $style): AbstractFootnote;

    public function setup(): void
    {
        $this->rtl = I18N::direction() === 'rtl';

        $this->rkeywords = '';

        // I18N: This is a report footer. %s is the name of the application.
        $this->generated_by = I18N::translate('Generated by %s', Webtrees::NAME . ' ' . Webtrees::VERSION);

        // Paper size - defaults to A4 if the report fails to define a size.
        [$this->page_width, $this->page_height] = self::PAPER_SIZES[$this->page_format] ?? self::PAPER_SIZES['A4'];
    }

    public function setProcessing(ReportSection $section): void
    {
        $this->processing = $section;
    }

    public function addTitle(string $data): void
    {
        $this->title .= $data;
    }

    public function addDescription(string $data): void
    {
        $this->rsubject .= $data;
    }

    public function addStyle(Style $style): void
    {
        $this->styles[$style->name] = $style;
    }

    public function getStyle(string $style): Style
    {
        return $this->styles[$style];
    }

    public function getCurrentStyleHeight(): float
    {
        return $this->currentStyle?->size ?? $this->default_font_size;
    }

    // =========================================================================
    // Methods used by both HTML and PDF element renderers
    // =========================================================================

    abstract public function footnotes(): void;

    abstract public function newPage(): void;

    abstract public function checkFootnote(AbstractFootnote $footnote): AbstractFootnote|false;

    /**
     * The current page number.  HTML output is unpaginated and returns the
     * single page it is assembling; the PDF backend delegates to TCPDF.
     */
    abstract public function pageNo(): int;

    /**
     * Combined height of all footnotes queued for rendering at the bottom
     * of the current page or text box.
     */
    abstract public function getFootnotesHeight(float $cellWidth = 0): float;
}
