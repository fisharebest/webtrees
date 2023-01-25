<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

/**
 * Class ReportBaseElement
 */
class ReportBaseElement
{
    // Special value for X or Y position, to indicate the current position.
    public const CURRENT_POSITION = -1.0;

    public string $text = '';

    /**
     * Element renderer
     *
     * @param HtmlRenderer|PdfRenderer $renderer
     *
     * @return void
     */
    public function render($renderer): void
    {
        //-- to be implemented in inherited classes
    }

    /**
     * Get the height.
     *
     * @param HtmlRenderer|PdfRenderer $renderer
     *
     * @return float
     */
    public function getHeight($renderer): float
    {
        return 0.0;
    }

    /**
     * Get the width.
     *
     * @param HtmlRenderer|PdfRenderer $renderer
     *
     * @return array{0:float,1:int,2:float}
     */
    public function getWidth($renderer): array
    {
        return [0.0, 1, 0.0];
    }

    /**
     * Add text.
     *
     * @param string $t
     *
     * @return void
     */
    public function addText(string $t): void
    {
        $t = trim($t, "\r\n\t");
        $t = strtr($t, ['<br>' => "\n", '&nbsp;' => ' ']);

        $this->text .= strip_tags($t);
    }

    /**
     * Add an end-of-line.
     *
     * @return void
     */
    public function addNewline(): void
    {
        $this->text .= "\n";
    }

    /**
     * Get the current text.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->text;
    }

    /**
     * Set the width to wrap text.
     *
     * @param float $wrapwidth
     * @param float $cellwidth
     *
     * @return float
     */
    public function setWrapWidth(float $wrapwidth, float $cellwidth): float
    {
        return 0;
    }

    /**
     * Render the footnotes.
     *
     * @param HtmlRenderer|PdfRenderer $renderer
     *
     * @return void
     */
    public function renderFootnote($renderer): void
    {
    }

    /**
     * Set the text.
     *
     * @param string $text
     *
     * @return void
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }
}
