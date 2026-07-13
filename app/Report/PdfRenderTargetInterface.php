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

interface PdfRenderTargetInterface
{
    public function reportConfig(): Config;

    public function getPageIndex(): int;

    public function newPage(): void;

    public function header(float $origin_x = 0.0, float $origin_y = 0.0): void;

    public function footer(float $origin_x = 0.0, float $origin_y = 0.0): void;

    public function setCurrentStyle(Style $style): void;

    public function getStringWidth(string $text): float;

    public function isRTL(): bool;

    public function setFillColor(HexColor $color): void;

    public function setDrawColor(HexColor $color): void;

    public function setTextColor(HexColor $color): void;

    public function resetColors(): void;

    public function drawImage(string $file, float $x, float $y, float $width, float $height): void;

    public function drawLine(float $x1, float $y1, float $x2, float $y2): void;

    public function drawRect(float $x, float $y, float $width, float $height, string $style): void;

    public function drawTextBlock(string $text, float $x, float $y, float $width, float $height, string $align, float $line_height, bool $with_padding = true): void;

    public function addLinkArea(float $x, float $y, float $width, float $height, string $url): void;

    public function createLink(): int;

    public function setLinkDestination(string $link, float $y, int $page = -1): void;
}
