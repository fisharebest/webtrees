<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use TCPDF;
use Fisharebest\Webtrees\Carbon;

/**
 * Class TcpdfWrapper
 */
class TcpdfWrapper extends TCPDF
{
    /**
     * Expose protected method in base class.
     *
     * @return float Return the remaining width
     */
    public function getRemainingWidth(): float
    {
        return parent::getRemainingWidth();
    }

    /**
     * Expose protected method in base class.
     *
     * @param mixed $h       Cell height. Default value: 0.
     * @param mixed $y       Starting y position, leave empty for current position.
     * @param bool  $add_page If true add a page, otherwise only return the true/false state
     *
     * @return boolean true in case of page break, false otherwise.
     */
    public function checkPageBreak($h = 0, $y = '', $add_page = true): bool
    {
        return parent::checkPageBreak($h, $y, $add_page);
    }


    //Page header
    public function Header()
    {
        $this->setHeaderTemplateAutoreset(true);
        $f = $this->getHeaderFont();
        $f[2] = 12;
        $this->setHeaderFont($f);
        $this->header_line_color = array(255,255,255);  // the line cuts through the pedigree chart in landscape mode
    // and no line gives a nicer look

        if ($this->header_xobjid === false) {
            // start a new XObject Template
            $this->header_xobjid = $this->startTemplate($this->w, $this->tMargin);
            $headerfont = $this->getHeaderFont();
            $headerdata = $this->getHeaderData();
            $this->y = $this->header_margin;
            if ($this->rtl) {
                $this->x = $this->w - $this->original_rMargin;
            } else {
                $this->x = $this->original_lMargin;
            }
            if (($headerdata['logo']) and ($headerdata['logo'] != K_BLANK_IMAGE)) {
                $imgtype = TCPDF_IMAGES::getImageFileType(K_PATH_IMAGES . $headerdata['logo']);
                if (($imgtype == 'eps') or ($imgtype == 'ai')) {
                    $this->ImageEps(K_PATH_IMAGES . $headerdata['logo'], '', '', $headerdata['logo_width']);
                } elseif ($imgtype == 'svg') {
                    $this->ImageSVG(K_PATH_IMAGES . $headerdata['logo'], '', '', $headerdata['logo_width']);
                } else {
                    $this->Image(K_PATH_IMAGES . $headerdata['logo'], '', '', $headerdata['logo_width']);
                }
                $imgy = $this->getImageRBY();
            } else {
                $imgy = $this->y;
            }
            $cell_height = $this->getCellHeight($headerfont[2] / $this->k);
            // set starting margin for text data cell
            if ($this->getRTL()) {
                $header_x = $this->original_rMargin + ($headerdata['logo_width'] * 1.1);
            } else {
                $header_x = $this->original_lMargin + ($headerdata['logo_width'] * 1.1);
            }
            $cw = $this->w - $this->original_lMargin - $this->original_rMargin - ($headerdata['logo_width'] * 1.1);
            $this->SetTextColorArray($this->header_text_color);
            // header title
            $this->SetFont($headerfont[0], 'B', $headerfont[2] + 1);
            $this->SetX($header_x);
            $this->Cell($cw, $cell_height, $headerdata['title'], 0, 1, 'C', 0, '', 0);
            if ($this->page == 1) {   // allows for a second line, not used (yet)
                // header string
                $this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
                $this->SetX($header_x);
                $this->MultiCell($cw, $cell_height, $headerdata['string'], 0, '', 0, 1, '', '', true, 0, false, true, 0, 'T', false);
                $this->endTemplate();
            }
        }

        // print header template
        $xx = 0;
        if ($this->original_lMargin < 15) {
            $xx = 15;   // approx 5mm (15/72*25.4)  // my Lexmark printer masks ~2 mm at the paper edge
                    // only applicable on pedigree charts(?)
        }
        $dx = 0;
        if (!$this->header_xobj_autoreset and $this->booklet and (($this->page % 2) == 0)) {
            // adjust margins for booklet mode
            $dx = ($this->original_lMargin - $this->original_rMargin);
        }
        if ($this->rtl) {
            $x = $this->w + $dx;
        } else {
            $x = $xx + $dx;
        }
        $this->printTemplate($this->header_xobjid, $x, 0, 0, 0, '', '', false);
        if ($this->header_xobj_autoreset) {
            // reset header xobject template at each page
            $this->header_xobjid = false;
        }
    }


    // Page footer
    public function Footer()
    {
        // Set font
        $this->SetFont('helvetica', '', 8);
        // Position at 30 pt ~ 10 mm from bottom and 5 mm from left paper edge
        $this->SetXY(15, -30);
        $this->Cell(0, 11, $this->creator, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->SetXY(15, -30);
        $this->Cell(0, 11, Carbon::now()->local()->toDateTimeString(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->SetFont('helvetica', '', 10);
        $this->SetXY(15, -30);
        $this->Cell(0, 11, $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}
