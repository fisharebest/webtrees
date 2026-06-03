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
 * PdfTextBox, PdfImage, PdfLine, PdfFootnote) require these methods,
 * which delegate to TCPDF internally.
 */
interface PdfRendererInterface
{
    // Margin and layout helpers
    public function addMarginX(float $x): float;

    public function checkPageBreakPDF(float $height): bool;

    public function getMaxLineWidth(): float;

    public function getRemainingWidthPDF(): float;

    // Cursor positioning
    public function getX(): float;

    public function getY(): float;

    public function setX(float $x): void;

    public function setY(float $y): void;

    public function setXY(float $x, float $y): void;

    // Text measurement
    public function getStringWidth(string $text): float;

    public function getNumLines(string $text, float $width): int;

    public function getCellHeightRatio(): float;

    public function getLastRenderedHeight(): float;

    // Margins
    /**
     * @return array<string, mixed>
     */
    public function getMargins(): array;

    public function setLeftMargin(float $margin): void;

    public function setRightMargin(float $margin): void;

    // Page operations
    public function getPageWidth(): float;

    public function getPageHeight(): float;

    public function getPageIndex(): int;

    public function setPageIndex(int $page): void;

    public function isRTL(): bool;

    // Color operations
    public function setFillColor(int $red, int $green, int $blue): void;

    public function setDrawColor(int $red, int $green, int $blue): void;

    public function setTextColor(int $red, int $green, int $blue): void;

    public function resetColors(): void;

    // Rendering operations
    public function multiCell(
        float $width,
        float $height,
        string $text,
        string $border,
        string $align,
        bool $fill,
        int $newline,
        float $x,
        float $y,
        bool $reseth,
        int $stretch,
        bool $is_html,
    ): void;

    public function writeHTML(string $html, bool $newline = true, bool $fill = false, bool $reseth = true): void;

    public function writeText(float $height, string $text, string $link = ''): void;

    public function drawImage(
        string $file,
        float $x,
        float $y,
        float $width,
        float $height,
        string $type,
        string $link,
        string $ln,
        bool $fitonpage,
        int $dpi,
        string $align,
    ): void;

    public function drawLine(float $x1, float $y1, float $x2, float $y2): void;

    public function drawRect(float $x, float $y, float $width, float $height, string $style): void;

    public function addLinkArea(float $x, float $y, float $width, float $height, string $url): void;

    // Link management
    public function createLink(): int;

    public function setLinkDestination(string $link, float $y = -1, int $page = -1): void;

    // Image page tracking
    public function getLastPicPage(): int;

    public function setLastPicPage(int $page): void;
}
