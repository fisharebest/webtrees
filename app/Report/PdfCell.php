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

use function is_array;

/**
 * @extends AbstractCell<AbstractRenderer&PdfRendererInterface>
 */
class PdfCell extends AbstractCell
{
    public function render(AbstractRenderer $renderer, bool $layout = true): void
    {
        $temptext = $this->resolvedText($renderer);

        $renderer->setCurrentStyle($this->style);

        $fill = $this->bgcolor !== '';
        if ($fill) {
            $hex = new HexColor($this->bgcolor);
            $renderer->setFillColor($hex->red, $hex->green, $hex->blue);
        }

        // Border color
        if ($this->bocolor !== '') {
            $hex = new HexColor($this->bocolor);
            $renderer->setDrawColor($hex->red, $hex->green, $hex->blue);
        }

        // Text color - falls back to black, so a missing tcolor does
        // not inherit the previous cell's color.
        if ($this->tcolor === '') {
            $renderer->setTextColor(0, 0, 0);
        } else {
            $hex = new HexColor($this->tcolor);
            $renderer->setTextColor($hex->red, $hex->green, $hex->blue);
        }

        // If current position (left)
        if ($this->left === AbstractElement::CURRENT_POSITION) {
            $cX = $renderer->getX();
        } else {
            // For static position add margin (also updates X)
            $cX = $renderer->addMarginX($this->left);
        }

        // Check the width if set to page wide OR set by xml to larger then page wide
        if ($this->width === 0.0 || $this->width > $renderer->getRemainingWidthPDF()) {
            $this->width = $renderer->getRemainingWidthPDF();
        }
        // For current position
        if ($this->top === AbstractElement::CURRENT_POSITION) {
            $this->top = $renderer->getY();
        } else {
            $renderer->setY($this->top);
        }

        // Check the last cell height and adjust the current cell height if needed
        if ($renderer->getLastCellHeight() > $this->height) {
            $this->height = $renderer->getLastCellHeight();
        }
        // Check for pagebreak
        if ($temptext !== '') {
            $cHT = $renderer->getNumLines($temptext, $this->width);
            $cHT = $cHT * $renderer->getCellHeightRatio() * $renderer->getCurrentStyleHeight();
            $cM  = $renderer->getMargins();
            // Add padding
            if (is_array($cM['cell'])) {
                $cHT += $cM['padding_bottom'] + $cM['padding_top'];
            } else {
                $cHT += $cM['cell'] * 2;
            }
            // Add a new page if needed
            if ($renderer->checkPageBreakPDF($cHT)) {
                $this->top = $renderer->getY();
            }
            $temptext = (new RightToLeftFormatter())->format($temptext);
        }
        // HTML ready - last value is true
        $renderer->multiCell(
            $this->width,
            $this->height,
            $temptext,
            $this->border,
            $this->align->value,
            $fill,
            $this->newline->value,
            $cX,
            $this->top,
            $this->reseth,
            $this->stretch,
            true
        );
        // Reset the last cell height for the next line
        if ($this->newline !== CellNewline::Right) {
            $renderer->resetLastCellHeight();
        } elseif ($renderer->getLastCellHeight() < $renderer->getLastRenderedHeight()) {
            // OR save the last height if higher then before
            $renderer->setLastCellHeight($renderer->getLastRenderedHeight());
        }

        // Set up the url link if exists on top of the cell
        if ($this->url !== '') {
            $renderer->addLinkArea($cX, $this->top, $this->width, $this->height, $this->url);
        }
        // Reset the border and the text color to black or they will be inherited
        $renderer->resetColors();
    }
}
