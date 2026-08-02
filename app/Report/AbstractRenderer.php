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

abstract class AbstractRenderer
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

    /** @var array<ReportBaseElement|string> */
    public array $headerElements = [];

    /** @var array<ReportBaseElement|string> */
    public array $footerElements = [];

    /** @var array<ReportBaseElement|string> */
    public array $bodyElements = [];

    public string $currentStyle = '';

    public function addElement(ReportBaseElement|string $element): void
    {
        if ($this->processing === 'B') {
            $this->addElementToBody($element);
        } elseif ($this->processing === 'H') {
            $this->addElementToHeader($element);
        } elseif ($this->processing === 'F') {
            $this->addElementToFooter($element);
        }
    }

    public function addElementToHeader(ReportBaseElement|string $element): void
    {
        $this->headerElements[] = $element;
    }

    public function addElementToBody(ReportBaseElement|string $element): void
    {
        $this->bodyElements[] = $element;
    }

    public function addElementToFooter(ReportBaseElement|string $element): void
    {
        $this->footerElements[] = $element;
    }

    abstract public function run(): void;

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
     * @param bool   $reseth
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
    ): ReportBaseCell;

    /**
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
    ): ReportBaseTextBox;

    abstract public function createText(string $style, string $color): ReportBaseText;

    abstract public function createLine(float $x1, float $y1, float $x2, float $y2): ReportBaseLine;

    abstract public function createImage(
        string $file,
        float $x,
        float $y,
        float $w,
        float $h,
        string $align, // L:left, C:center, R:right or empty to use x/y
        string $ln,    //  T:same line, N:next line
    ): ReportBaseImage;

    abstract public function createImageFromObject(
        MediaFile $media_file,
        float $x,
        float $y,
        float $w,
        float $h,
        string $align, // L:left, C:center, R:right or empty to use x/y
        string $ln,    // T:same line, N:next line
    ): ReportBaseImage;

    abstract public function createFootnote(string $style): ReportBaseFootnote;

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
}
