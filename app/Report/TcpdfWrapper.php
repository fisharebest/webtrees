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

use TCPDF;

/**
 * Thin facade around TCPDF.
 *
 * Exposes a handful of TCPDF protected helpers that the Pdf* element
 * renderers need, plus a `resetColors()` shortcut used at the tail of
 * each cell render to keep color state from leaking from one element
 * into the next.  Color parsing itself lives in HexColor.
 *
 * Overrides TCPDF's Header() and Footer() so that the report XML's
 * header/footer elements are rendered on every page instead of TCPDF's
 * default title-only header.
 */
class TcpdfWrapper extends TCPDF
{
    private PdfRenderer|null $renderer = null;

    /**
     * Connect this TCPDF instance to the PdfRenderer that owns it.
     * Must be called after construction and before AddPage().
     */
    public function setRenderer(PdfRenderer $renderer): void
    {
        $this->renderer = $renderer;
    }

    /**
     * Called by TCPDF on every AddPage().  Renders the report XML's
     * header elements (if any) into the header area of the page.
     */
    public function header(): void
    {
        if ($this->renderer !== null) {
            $this->renderer->header();
        }
    }

    /**
     * Called by TCPDF when a page ends.  Renders the report XML's
     * footer elements (if any) into the footer area of the page.
     */
    public function footer(): void
    {
        if ($this->renderer !== null) {
            $this->renderer->footer();
        }
    }

    public function getRemainingWidth(): float
    {
        return parent::getRemainingWidth();
    }

    public function checkPageBreak($h = 0, $y = null, $addpage = true): bool
    {
        return parent::checkPageBreak($h, $y, $addpage);
    }


    public function resetColors(): void
    {
        $this->setDrawColor(0, 0, 0);
        $this->setTextColor(0, 0, 0);
    }
}
