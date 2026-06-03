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
 * @extends AbstractLine<AbstractRenderer&PdfRendererInterface>
 */
class PdfLine extends AbstractLine
{
    public function render(AbstractRenderer $renderer, bool $attrib = true): void
    {
        if ($this->x1 === AbstractElement::CURRENT_POSITION) {
            $this->x1 = $renderer->getX();
        }
        if ($this->y1 === AbstractElement::CURRENT_POSITION) {
            $this->y1 = $renderer->getY();
        }
        if ($this->x2 === AbstractElement::CURRENT_POSITION) {
            $this->x2 = $renderer->getMaxLineWidth();
        }
        if ($this->y2 === AbstractElement::CURRENT_POSITION) {
            $this->y2 = $renderer->getY();
        }
        if ($renderer->isRTL()) {
            $renderer->drawLine($renderer->getPageWidth() - $this->x1, $this->y1, $renderer->getPageWidth() - $this->x2, $this->y2);
        } else {
            $renderer->drawLine($this->x1, $this->y1, $this->x2, $this->y2);
        }
    }
}
