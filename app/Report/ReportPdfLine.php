<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

/**
 * Class ReportPdfLine
 */
class ReportPdfLine extends ReportBaseLine
{
    /**
     * PDF line renderer
     *
     * @param PdfRenderer $renderer
     *
     * @return void
     */
    public function render($renderer)
    {
        if ($this->x1 === ReportBaseElement::CURRENT_POSITION) {
            $this->x1 = $renderer->tcpdf->GetX();
        }
        if ($this->y1 === ReportBaseElement::CURRENT_POSITION) {
            $this->y1 = $renderer->tcpdf->GetY();
        }
        if ($this->x2 === ReportBaseElement::CURRENT_POSITION) {
            $this->x2 = $renderer->getMaxLineWidth();
        }
        if ($this->y2 === ReportBaseElement::CURRENT_POSITION) {
            $this->y2 = $renderer->tcpdf->GetY();
        }
        if ($renderer->tcpdf->getRTL()) {
            $renderer->tcpdf->Line($renderer->tcpdf->getPageWidth() - $this->x1, $this->y1, $renderer->tcpdf->getPageWidth() - $this->x2, $this->y2);
        } else {
            $renderer->tcpdf->Line($this->x1, $this->y1, $this->x2, $this->y2);
        }
    }
}
