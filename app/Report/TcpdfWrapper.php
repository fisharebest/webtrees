<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

class TcpdfWrapper extends TCPDF
{
    /**
     * Expose protected method in base class.
     *
     * @return float Return the remaining width
     */
    public function getRemainingWidth(): float
    {
        return parent::getRemainingWidth();
    }

    /**
     * Expose protected method in base class.
     *
     * @param float      $h       Cell height. Default value: 0.
     * @param float|null $y       Starting y position, leave empty for current position.
     * @param bool       $addpage If true add a page, otherwise only return the true/false state
     *
     * @return bool true in case of page break, false otherwise.
     */
    public function checkPageBreak($h = 0, $y = null, $addpage = true): bool
    {
        return parent::checkPageBreak($h, $y, $addpage);
    }
}
