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

use function hexdec;
use function is_array;
use function preg_match;
use function str_replace;

/**
 * Class ReportPdfCell
 */
class ReportPdfCell extends ReportBaseCell
{
    /**
     * PDF Cell renderer
     *
     * @param PdfRenderer $renderer
     *
     * @return void
     */
    public function render($renderer): void
    {
        $temptext = str_replace('#PAGENUM#', (string) $renderer->tcpdf->PageNo(), $this->text);
        // underline «title» part of Source item
        $temptext = str_replace([
            '«',
            '»',
        ], [
            '<u>',
            '</u>',
        ], $temptext);

        // Set up the text style
        if ($renderer->getCurrentStyle() !== $this->styleName) {
            $renderer->setCurrentStyle($this->styleName);
        }

        // Background color
        $match = [];
        // Indicates if the cell background must be painted (1) or transparent (0)
        if ($this->fill === 1) {
            if (!empty($this->bgcolor)) {
                // HTML color to RGB
                if (preg_match('/#?(..)(..)(..)/', $this->bgcolor, $match)) {
                    $r = hexdec($match[1]);
                    $g = hexdec($match[2]);
                    $b = hexdec($match[3]);
                    $renderer->tcpdf->SetFillColor($r, $g, $b);
                }
            } else {
                // If no color set then don't fill
                $this->fill = 0;
            }
        }

        // Borders
        // HTML color to RGB
        if (preg_match('/#?(..)(..)(..)/', $this->bocolor, $match)) {
            $r = hexdec($match[1]);
            $g = hexdec($match[2]);
            $b = hexdec($match[3]);
            $renderer->tcpdf->SetDrawColor($r, $g, $b);
        }

        // Paint the text color or they might use inherited colors by the previous function
        if (preg_match('/#?(..)(..)(..)/', $this->tcolor, $match)) {
            $r = hexdec($match[1]);
            $g = hexdec($match[2]);
            $b = hexdec($match[3]);
            $renderer->tcpdf->SetTextColor($r, $g, $b);
        } else {
            $renderer->tcpdf->SetTextColor(0, 0, 0);
        }

        // If current position (left)
        if ($this->left === ReportBaseElement::CURRENT_POSITION) {
            $cX = $renderer->tcpdf->GetX();
        } else {
            // For static position add margin (also updates X)
            $cX = $renderer->addMarginX($this->left);
        }

        // Check the width if set to page wide OR set by xml to larger then page wide
        if ($this->width === 0.0 || $this->width > $renderer->getRemainingWidthPDF()) {
            $this->width = $renderer->getRemainingWidthPDF();
        }
        // For current position
        if ($this->top === ReportBaseElement::CURRENT_POSITION) {
            $this->top = $renderer->tcpdf->GetY();
        } else {
            $renderer->tcpdf->SetY($this->top);
        }

        // Check the last cell height and adjust the current cell height if needed
        if ($renderer->lastCellHeight > $this->height) {
            $this->height = $renderer->lastCellHeight;
        }
        // Check for pagebreak
        if (!empty($temptext)) {
            $cHT = $renderer->tcpdf->getNumLines($temptext, $this->width);
            $cHT = $cHT * $renderer->tcpdf->getCellHeightRatio() * $renderer->getCurrentStyleHeight();
            $cM  = $renderer->tcpdf->getMargins();
            // Add padding
            if (is_array($cM['cell'])) {
                $cHT += $cM['padding_bottom'] + $cM['padding_top'];
            } else {
                $cHT += $cM['cell'] * 2;
            }
            // Add a new page if needed
            if ($renderer->checkPageBreakPDF($cHT)) {
                $this->top = $renderer->tcpdf->GetY();
            }
            $temptext = RightToLeftSupport::spanLtrRtl($temptext);
        }
        // HTML ready - last value is true
        $renderer->tcpdf->MultiCell(
            $this->width,
            $this->height,
            $temptext,
            $this->border,
            $this->align,
            $this->fill,
            $this->newline,
            $cX,
            $this->top,
            $this->reseth,
            $this->stretch,
            true
        );
        // Reset the last cell height for the next line
        if ($this->newline >= 1) {
            $renderer->lastCellHeight = 0;
        } elseif ($renderer->lastCellHeight < $renderer->tcpdf->getLastH()) {
            // OR save the last height if higher then before
            $renderer->lastCellHeight = $renderer->tcpdf->getLastH();
        }

        // Set up the url link if exists ontop of the cell
        if (!empty($this->url)) {
            $renderer->tcpdf->Link($cX, $this->top, $this->width, $this->height, $this->url);
        }
        // Reset the border and the text color to black or they will be inherited
        $renderer->tcpdf->SetDrawColor(0, 0, 0);
        $renderer->tcpdf->SetTextColor(0, 0, 0);
    }
}
