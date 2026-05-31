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
use LogicException;

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

    /** @var array<array{'name': string, 'font': string, 'style': string, 'size': float}> Style elements found in the document */
    public array $styles = [];

    public string $default_font = 'dejavusans';

    public float $default_font_size = 12.0;

    /** @var string Header (H), Body (B) or Footer (F) */
    public string $processing = 'H';

    public bool $rtl = false;

    public bool $show_generated_by = true;

    public string $generated_by = '';

    public string $title = '';

    public string $rauthor = Webtrees::NAME . ' ' . Webtrees::VERSION;

    public string $rkeywords = '';

    public string $rsubject = '';

    /** @var array<AbstractElement|string> */
    public array $headerElements = [];

    /** @var array<AbstractElement|string> */
    public array $footerElements = [];

    /** @var array<AbstractElement|string> */
    public array $bodyElements = [];

    public string $currentStyle = '';

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

    public function addElement(AbstractElement|string $element): void
    {
        if ($this->processing === 'B') {
            $this->addElementToBody($element);
        } elseif ($this->processing === 'H') {
            $this->addElementToHeader($element);
        } elseif ($this->processing === 'F') {
            $this->addElementToFooter($element);
        }
    }

    public function addElementToHeader(AbstractElement|string $element): void
    {
        $this->headerElements[] = $element;
    }

    public function addElementToBody(AbstractElement|string $element): void
    {
        $this->bodyElements[] = $element;
    }

    public function addElementToFooter(AbstractElement|string $element): void
    {
        $this->footerElements[] = $element;
    }

    abstract public function run(): void;

    abstract public function setCurrentStyle(string $s): void;

    /**
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
     * @param int    $stretch Stretch character mode
     * @param string $bocolor Border color
     * @param string $tcolor  Text color
     */
    abstract public function createCell(
        float $width,
        float $height,
        string $border,
        string $align,
        string $bgcolor,
        string $style,
        int $ln,
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

    abstract public function createText(string $style, string $color): AbstractText;

    abstract public function createLine(float $x1, float $y1, float $x2, float $y2): AbstractLine;

    abstract public function createImage(
        string $file,
        float $x,
        float $y,
        float $w,
        float $h,
        string $align, // L:left, C:center, R:right or empty to use x/y
        string $ln,    //  T:same line, N:next line
    ): AbstractImage;

    abstract public function createImageFromObject(
        MediaFile $media_file,
        float $x,
        float $y,
        float $w,
        float $h,
        string $align, // L:left, C:center, R:right or empty to use x/y
        string $ln,    // T:same line, N:next line
    ): AbstractImage;

    abstract public function createFootnote(string $style): AbstractFootnote;

    public function setup(): void
    {
        $this->rtl = I18N::direction() === 'rtl';

        $this->rkeywords = '';

        // I18N: This is a report footer. %s is the name of the application.
        $this->generated_by = I18N::translate('Generated by %s', Webtrees::NAME . ' ' . Webtrees::VERSION);

        // Paper size - defaults to A4 if the report fails to define a size.
        [$this->page_width, $this->page_height] = self::PAPER_SIZES[$this->page_format] ?? self::PAPER_SIZES['A4'];
    }

    public function setProcessing(string $p): void
    {
        $this->processing = $p;
    }

    public function addTitle(string $data): void
    {
        $this->title .= $data;
    }

    public function addDescription(string $data): void
    {
        $this->rsubject .= $data;
    }

    /**
     * @param array{'name': string, 'font': string, 'style': string, 'size': float} $style
     */
    public function addStyle(array $style): void
    {
        $this->styles[$style['name']] = $style;
    }

    /**
     * @return array{'name': string, 'font': string, 'style': string, 'size': float}
     */
    public function getStyle(string $style): array
    {
        return $this->styles[$style];
    }

    public function getCurrentStyle(): string
    {
        return $this->currentStyle;
    }

    public function getCurrentStyleHeight(): float
    {
        if ($this->currentStyle === '') {
            return $this->default_font_size;
        }

        $style = $this->getStyle($this->currentStyle);

        return $style['size'];
    }

    // =========================================================================
    // Methods used by both HTML and PDF element renderers
    // =========================================================================

    abstract public function footnotes(): void;

    abstract public function checkFootnote(AbstractFootnote $footnote): AbstractFootnote|false;

    // =========================================================================
    // Methods used by HTML element renderers
    // =========================================================================

    public function addMaxY(float $y): void
    {
        throw new LogicException('addMaxY() is not available in ' . static::class);
    }

    public function addPage(): void
    {
        throw new LogicException('addPage() is not available in ' . static::class);
    }

    public function countLines(string $str): int
    {
        throw new LogicException('countLines() is not available in ' . static::class);
    }

    public function getFootnotesHeight(float $cellWidth = 0): float
    {
        throw new LogicException('getFootnotesHeight() is not available in ' . static::class);
    }

    public function getRemainingWidth(): float
    {
        throw new LogicException('getRemainingWidth() is not available in ' . static::class);
    }

    public function getStringWidth(string $text): float
    {
        throw new LogicException('getStringWidth() is not available in ' . static::class);
    }

    public function getTextCellHeight(string $str): float
    {
        throw new LogicException('getTextCellHeight() is not available in ' . static::class);
    }

    public function getX(): float
    {
        throw new LogicException('getX() is not available in ' . static::class);
    }

    public function getY(): float
    {
        throw new LogicException('getY() is not available in ' . static::class);
    }

    public function pageNo(): int
    {
        throw new LogicException('pageNo() is not available in ' . static::class);
    }

    public function setX(float $x): void
    {
        throw new LogicException('setX() is not available in ' . static::class);
    }

    public function setXy(float $x, float $y): void
    {
        throw new LogicException('setXy() is not available in ' . static::class);
    }

    public function setY(float $y): void
    {
        throw new LogicException('setY() is not available in ' . static::class);
    }

    public function textWrap(string $str, float $width): string
    {
        throw new LogicException('textWrap() is not available in ' . static::class);
    }

    public function write(string $text, string $color = '', bool $useclass = true): void
    {
        throw new LogicException('write() is not available in ' . static::class);
    }

    // =========================================================================
    // Methods used by PDF element renderers
    // =========================================================================

    public function addMarginX(float $x): float
    {
        throw new LogicException('addMarginX() is not available in ' . static::class);
    }

    public function checkPageBreakPDF(float $height): bool
    {
        throw new LogicException('checkPageBreakPDF() is not available in ' . static::class);
    }

    public function getMaxLineWidth(): float
    {
        throw new LogicException('getMaxLineWidth() is not available in ' . static::class);
    }

    public function getRemainingWidthPDF(): float
    {
        throw new LogicException('getRemainingWidthPDF() is not available in ' . static::class);
    }

    public function newPage(): void
    {
        throw new LogicException('newPage() is not available in ' . static::class);
    }
}
