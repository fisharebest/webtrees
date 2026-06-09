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
 * Operations specific to the HTML report backend.
 *
 * Element renderers that target the HTML backend (HtmlCell, HtmlText,
 * HtmlTextBox, HtmlImage, HtmlLine, HtmlFootnote) require these
 * cursor- and text-measurement helpers.
 */
interface HtmlRendererInterface
{
    // Cursor positioning
    public function getX(): float;

    public function getY(): float;

    public function setX(float $x): void;

    public function setY(float $y): void;

    public function setXY(float $x, float $y): void;

    // Text measurement
    public function getStringWidth(string $text): float;

    public function addMaxY(float $y): void;

    public function getRemainingWidth(): float;

    public function getTextCellHeight(string $str): float;

    public function textWrap(string $str, float $width): string;

    public function write(string $text, string $color = '', bool $useclass = true): void;
}
