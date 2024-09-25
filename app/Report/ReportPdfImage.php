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

/**
 * Class ReportPdfImage
 */
class ReportPdfImage extends ReportBaseImage
{
    /**
     * PDF image renderer
     *
     * @param PdfRenderer $renderer
     *
     * @return void
     */
    public function render($renderer,bool $headerorfoot=false): void //sfqas
    {
        static $lastpicbottom, $lastpicpage, $lastpicleft, $lastpicright;

        // Check for a pagebreak first
        if ($renderer->tcpdf->checkPageBreak($this->height + 5,null,false) and !$headerorfoot){  //sfqas
            $renderer->checkPageBreakPDF($this->height + 5);
			$this->y = $renderer->tcpdf->GetY();
        }

        $curx = $renderer->tcpdf->GetX();

        // Get the current positions
        if ($this->x === ReportBaseElement::CURRENT_POSITION) {
            $this->x = $renderer->tcpdf->GetX();
        } else {
            // For static position add margin
            $this->x = $renderer->addMarginX($this->x);
            $renderer->tcpdf->setX($curx);
        }
        if ($this->y === ReportBaseElement::CURRENT_POSITION) {
            //-- first check for a collision with the last picture
            if ($lastpicbottom !== null && $renderer->tcpdf->PageNo() === $lastpicpage && $lastpicbottom >= $renderer->tcpdf->GetY() && $this->x >= $lastpicleft && $this->x <= $lastpicright) {
                $renderer->tcpdf->setY($lastpicbottom + 5);
            }
            $this->y = $renderer->tcpdf->GetY();
        } else {
            $renderer->tcpdf->setY($this->y);
        }
        if ($renderer->tcpdf->getRTL()) {
            $renderer->tcpdf->Image(
                $this->file,
                $renderer->tcpdf->getPageWidth() - $this->x,
                $this->y,
                $this->width,
                $this->height,
                '',
                '',
                $this->line,
                false,
                72,
                $this->align
            );
        } else {
            $renderer->tcpdf->Image(
                $this->file,
                $this->x,
                $this->y,
                $this->width,
                $this->height,
                '',
                '',
                $this->line,
                false,
                72,
                $this->align
            );
        }
        $lastpicpage           = $renderer->tcpdf->PageNo();
        $renderer->lastpicpage = $renderer->tcpdf->getPage();
        $lastpicleft           = $this->x;
        $lastpicright          = $this->x + $this->width;
        $lastpicbottom         = $this->y + $this->height;
        // Setup for the next line
        if ($this->line === 'N') {
            $renderer->tcpdf->setY($lastpicbottom);
        }
    }
}
