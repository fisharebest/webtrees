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

/**
 * Operations specific to the PDF report backend.
 *
 * Element renderers that target the PDF backend (PdfCell, PdfText,
 * PdfTextBox, PdfImage, PdfLine, PdfFootnote) require these page-break
 * and margin helpers, which delegate to TCPDF.
 */
interface PdfRendererInterface
{
    public function addMarginX(float $x): float;

    public function checkPageBreakPDF(float $height): bool;

    public function getMaxLineWidth(): float;

    public function getRemainingWidthPDF(): float;
}
