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

use function strip_tags;
use function trim;

abstract class AbstractElement
{
    // Special value for X or Y position, to indicate the current position.
    public const float CURRENT_POSITION = -1.0;

    protected string $text = '';

    /**
     * @param bool $attrib - true (element handles position/layout) false (parent TextBox handles position/layout)
     */
    abstract public function render(AbstractRenderer $renderer, bool $attrib = true): void;

    public function getHeight(AbstractRenderer $renderer): float
    {
        return 0.0;
    }

    /**
     * @return array{0:float,1:int,2:float}
     */
    public function getWidth(AbstractRenderer $renderer): array
    {
        return [0.0, 1, 0.0];
    }

    public function addText(string $t): void
    {
        $t = trim($t, "\r\n\t");
        $t = strtr($t, ['<br>' => "\n", '&nbsp;' => "\u{A0}"]);

        $this->text .= strip_tags($t);
    }

    public function addNewline(): void
    {
        $this->text .= "\n";
    }

    public function getValue(): string
    {
        return $this->text;
    }

    public function setWrapWidth(float $wrapwidth, float $cellwidth): float
    {
        return 0;
    }

    public function renderFootnote(AbstractRenderer $renderer): void
    {
    }
}
