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

use function abs;

class ReportBaseLine extends ReportBaseElement
{
    // Start horizontal position, current position (default)
    public float $x1 = ReportBaseElement::CURRENT_POSITION;

    // Start vertical position, current position (default)
    public float $y1 = ReportBaseElement::CURRENT_POSITION;

    // End horizontal position, maximum width (default)
    public float $x2 = ReportBaseElement::CURRENT_POSITION;

    // End vertical position
    public float $y2 = ReportBaseElement::CURRENT_POSITION;

    /**
     * Create a line class - Base
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     */
    public function __construct(float $x1, float $y1, float $x2, float $y2)
    {
        $this->x1 = $x1;
        $this->y1 = $y1;
        $this->x2 = $x2;
        $this->y2 = $y2;
    }

    /**
     * Get the height of the line.
     *
     * @param HtmlRenderer|PdfRenderer $renderer
     *
     * @return float
     */
    public function getHeight($renderer): float
    {
        return abs($this->y2 - $this->y1);
    }

    /**
     * Get the width of the line.
     *
     * @param HtmlRenderer|PdfRenderer $renderer
     *
     * @return array{0:float,1:int,2:float}
     */
    public function getWidth($renderer): array
    {
        return [abs($this->x2 - $this->x1), 1, $this->getHeight($renderer)];
    }
}
