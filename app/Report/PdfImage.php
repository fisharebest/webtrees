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
 * @extends AbstractImage<AbstractRenderer&PdfRendererInterface>
 */
class PdfImage extends AbstractImage
{
    public function render(AbstractRenderer $renderer, bool $layout = true): void
    {
        static $lastpicbottom, $lastpicpage, $lastpicleft, $lastpicright;

        // Check for a pagebreak first
        if ($renderer->checkPageBreakPDF($this->height + 5)) {
            $this->y = $renderer->getY();
        }

        $curx = $renderer->getX();

        // Track whether the image has an explicit X position so we can
        // restore the cursor afterward — explicitly placed images should
        // not shift the horizontal flow for subsequent elements.
        $explicit_position = ($this->x !== AbstractElement::CURRENT_POSITION);

        // Get the current positions
        if (!$explicit_position) {
            $this->x = $renderer->getX();
        } else {
            // For static position add margin
            $this->x = $renderer->addMarginX($this->x);
            $renderer->setX($curx);
        }
        if ($this->y === AbstractElement::CURRENT_POSITION) {
            //-- first check for a collision with the last picture
            if ($lastpicbottom !== null && $renderer->pageNo() === $lastpicpage && $lastpicbottom >= $renderer->getY() && $this->x >= $lastpicleft && $this->x <= $lastpicright) {
                $renderer->setY($lastpicbottom + 5);
            }
            $this->y = $renderer->getY();
        } else {
            $renderer->setY($this->y);
        }
        if ($renderer->isRTL()) {
            $renderer->drawImage(
                $this->src,
                $renderer->getPageWidth() - $this->x,
                $this->y,
                $this->width,
                $this->height,
                '',
                '',
                $this->line->value,
                false,
                72,
                $this->align->value
            );
        } else {
            $renderer->drawImage(
                $this->src,
                $this->x,
                $this->y,
                $this->width,
                $this->height,
                '',
                '',
                $this->line->value,
                false,
                72,
                $this->align->value
            );
        }
        $lastpicpage = $renderer->pageNo();
        $renderer->setLastPicPage($renderer->getPageIndex());
        $lastpicleft           = $this->x;
        $lastpicright          = $this->x + $this->width;
        $lastpicbottom         = $this->y + $this->height;
        // Setup for the next line
        if ($this->line === ImageContinuation::NextLine) {
            $renderer->setY($lastpicbottom);
        }
        // When the image was placed at an explicit X position, restore
        // the cursor so subsequent elements flow from the original position
        // rather than from the right edge of this image.
        if ($explicit_position) {
            $renderer->setX($curx);
        }
    }
}
