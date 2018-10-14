<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Report;

use TCPDF;

/**
 * WT Report PDF Class
 *
 * This class inherits from the TCPDF class and is used to generate the PDF document
 */
class ReportTcpdf extends TCPDF
{
    /** @var ReportBaseElement[] Array of elements in the header */
    public $headerElements = [];

    /** @var ReportBaseElement[] Array of elements in the page header */
    public $pageHeaderElements = [];

    /** @var ReportBaseElement[] Array of elements in the footer */
    public $footerElements = [];

    /** @var ReportBaseElement[] Array of elements in the body */
    public $bodyElements = [];

    /** @var ReportPdfFootnote[] Array of elements in the footer notes */
    public $printedfootnotes = [];

    /** @var string Currently used style name */
    public $currentStyle = '';

    /** @var float The last cell height */
    public $lastCellHeight = 0;

    /** @var float The largest font size within a TextBox to calculate the height */
    public $largestFontHeight = 0;

    /** @var int The last pictures page number */
    public $lastpicpage = 0;

    /** @var ReportPdf The current report. */
    public $wt_report;

    /**
     * PDF Header -PDF
     *
     * @return void
     */
    public function header()
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

        foreach ($this->pageHeaderElements as $element) {
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
    public function body()
    {
        $this->AddPage();

        foreach ($this->bodyElements as $key => $element) {
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
     * PDF Footnotes -PDF
     *
     * @return void
     */
    public function footnotes()
    {
        foreach ($this->printedfootnotes as $element) {
            if (($this->GetY() + $element->getFootnoteHeight($this)) > $this->getPageHeight()) {
                $this->AddPage();
            }

            $element->renderFootnote($this);

            if ($this->GetY() > $this->getPageHeight()) {
                $this->AddPage();
            }
        }
    }

    /**
     * PDF Footer -PDF
     *
     * @return void
     */
    public function footer()
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
     * Add an element to the Header -PDF
     *
     * @param ReportBaseElement|string $element
     *
     * @return void
     */
    public function addHeader($element)
    {
        $this->headerElements[] = $element;
    }

    /**
     * Add an element to the Page Header -PDF
     *
     * @param ReportBaseElement|string $element
     *
     * @return void
     */
    public function addPageHeader($element)
    {
        $this->pageHeaderElements[] = $element;
    }

    /**
     * Add an element to the Body -PDF
     *
     * @param ReportBaseElement|string $element
     *
     * @return void
     */
    public function addBody($element)
    {
        $this->bodyElements[] = $element;
    }

    /**
     * Add an element to the Footer -PDF
     *
     * @param ReportBaseElement|string $element
     *
     * @return void
     */
    public function addFooter($element)
    {
        $this->footerElements[] = $element;
    }

    /**
     * Remove the header.
     *
     * @param int $index
     *
     * @return void
     */
    public function removeHeader(int $index)
    {
        unset($this->headerElements[$index]);
    }

    /**
     * Remove the page header.
     *
     * @param int $index
     *
     * @return void
     */
    public function removePageHeader(int $index)
    {
        unset($this->pageHeaderElements[$index]);
    }

    /**
     * Remove the body.
     *
     * @param int $index
     *
     * @return void
     */
    public function removeBody(int $index)
    {
        unset($this->bodyElements[$index]);
    }

    /**
     * Remove the footer.
     *
     * @param int $index
     *
     * @return void
     */
    public function removeFooter(int $index)
    {
        unset($this->footerElements[$index]);
    }

    /**
     * Clear the Header -PDF
     *
     * @return void
     */
    public function clearHeader()
    {
        unset($this->headerElements);
        $this->headerElements = [];
    }

    /**
     * Clear the Page Header -PDF
     *
     * @return void
     */
    public function clearPageHeader()
    {
        unset($this->pageHeaderElements);
        $this->pageHeaderElements = [];
    }

    /**
     * Set the report.
     *
     * @param ReportPdf $report
     *
     * @return void
     */
    public function setReport(ReportPdf $report)
    {
        $this->wt_report = $report;
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
    public function setCurrentStyle(string $s)
    {
        $this->currentStyle = $s;
        $style              = $this->wt_report->getStyle($s);
        $this->SetFont($style['font'], $style['style'], $style['size']);
    }

    /**
     * Get the style -PDF
     *
     * @param string $s Style name
     *
     * @return array
     */
    public function getStyle(string $s): array
    {
        if (!isset($this->wt_report->styles[$s])) {
            $s                           = $this->getCurrentStyle();
            $this->wt_report->styles[$s] = $s;
        }

        return $this->wt_report->styles[$s];
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
        $m = $this->getMargins();
        if ($this->getRTL()) {
            $x += $m['right'];
        } else {
            $x += $m['left'];
        }
        $this->SetX($x);

        return $x;
    }

    /**
     * Get the maximum line width to draw from the curren position -PDF
     * RTL supported
     *
     * @return float
     */
    public function getMaxLineWidth()
    {
        $m = $this->getMargins();
        if ($this->getRTL()) {
            return ($this->getRemainingWidth() + $m['right']);
        }

        return ($this->getRemainingWidth() + $m['left']);
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
        if (empty($this->currentStyle)) {
            return $this->wt_report->default_font_size;
        }
        $style = $this->wt_report->getStyle($this->currentStyle);

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
            if ($this->printedfootnotes[$i]->getValue() == $val) {
                // If this footnote already exist then set up the numbers for this object
                $footnote->setNum($i + 1);
                $footnote->setAddlink((string) ($i + 1));

                return $this->printedfootnotes[$i];
            }
            $i++;
        }
        // If this Footnote has not been set up yet
        $footnote->setNum($ct + 1);
        $footnote->setAddlink((string) $this->AddLink());
        $this->printedfootnotes[] = $footnote;

        return false;
    }

    /**
     * Used this function instead of AddPage()
     * This function will make sure that images will not be overwritten
     *
     * @return void
     */
    public function newPage()
    {
        if ($this->lastpicpage > $this->getPage()) {
            $this->setPage($this->lastpicpage);
        }
        $this->AddPage();
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
        return $this->checkPageBreak($height);
    }

    /**
     * Returns the remaining width between the current position and margins -PDF
     *
     * @return float Remaining width
     */
    public function getRemainingWidthPDF(): float
    {
        return $this->getRemainingWidth();
    }
}
