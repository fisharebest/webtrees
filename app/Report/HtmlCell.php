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

use function str_contains;

/**
 * @extends AbstractCell<AbstractRenderer&HtmlRendererInterface>
 */
class HtmlCell extends AbstractCell
{
    public function render(AbstractRenderer $renderer, bool $attrib = true): void
    {
        if ($this->containsTotalPages()) {
            return;
        }
        $temptext = $this->resolvedText($renderer);

        // Set up the text style
        $renderer->setCurrentStyle($this->style);

        // Adjust the positions
        if ($this->left === AbstractElement::CURRENT_POSITION) {
            $this->left = $renderer->getX();
        } else {
            $renderer->setX($this->left);
        }

        if ($this->top === AbstractElement::CURRENT_POSITION) {
            $this->top = $renderer->getY();
        } else {
            $renderer->setY($this->top);
        }

        // Start collecting the HTML code
        echo '<div class="', $this->style->name, '" style="position:absolute;top:', $this->top, 'pt;';
        // Use Cell around padding to support RTL also
        echo 'padding:', $renderer::CELL_PADDING, 'pt;';
        // LTR (left) or RTL (right)
        echo $renderer->config->align_rtl, ':', $this->left, 'pt;';

        // Background color
        if (!empty($this->bgcolor)) {
            echo 'background-color:', $this->bgcolor, ';';
        }

        // Borders
        $bpixX = 0;
        $bpixY = 0;
        if (!empty($this->border)) {
            // Border all around
            if ($this->border === '1') {
                echo ' border:solid ', $this->bocolor ?: 'black', ' 1pt;';
                $bpixX = 1;
                $bpixY = 1;
            } else {
                if (str_contains($this->border, 'T')) {
                    echo ' border-top:solid ', $this->bocolor ?: 'black', ' 1pt;';
                    $bpixY = 1;
                }
                if (str_contains($this->border, 'B')) {
                    echo ' border-bottom:solid ', $this->bocolor ?: 'black', ' 1pt;';
                    $bpixY = 1;
                }
                if (str_contains($this->border, 'R')) {
                    echo ' border-right:solid ', $this->bocolor ?: 'black', ' 1pt;';
                    $bpixX = 1;
                }
                if (str_contains($this->border, 'L')) {
                    echo ' border-left:solid ', $this->bocolor ?: 'black', ' 1pt;';
                    $bpixX = 1;
                }
            }
        }
        // Check the width if set to page wide OR set by xml to larger then page wide
        if ($this->width === 0.0 || $this->width > $renderer->getRemainingWidth()) {
            $this->width = $renderer->getRemainingWidth();
        }
        // We have to calculate a different width for the padding, counting on both side
        $cW = $this->width - $renderer::CELL_PADDING * 2.0;

        // If there is any text
        if (!empty($temptext)) {
            // Wrap the text
            $temptext = $renderer->textWrap($temptext, $cW);
            $tmph     = $renderer->getTextCellHeight($temptext);
            // Add some cell padding
            $this->height += $renderer::CELL_PADDING;
            if ($tmph > $this->height) {
                $this->height = $tmph;
            }
        }
        // Check the last cell height and adjust the current cell height if needed
        if ($renderer->getLastCellHeight() > $this->height) {
            $this->height = $renderer->getLastCellHeight();
        }
        echo ' width:', $cW - $bpixX, 'pt;height:', $this->height - $bpixY, 'pt;';

        // Text alignment
        switch ($this->align) {
            case CellAlign::Center:
                echo ' text-align:center;';
                break;
            case CellAlign::Left:
                echo ' text-align:left;';
                break;
            case CellAlign::Right:
                echo ' text-align:right;';
                break;
            default:
                // CellAlign::None and CellAlign::Justify intentionally
                // produce no text-align style, matching the historical
                // behaviour of the HTML renderer.
                break;
        }

        // Print the collected HTML code
        echo '">';

        // Print URL
        if (!empty($this->url)) {
            echo '<a href="', $this->url, '">';
        }
        // Print any text if exists
        if (!empty($temptext)) {
            $renderer->write($temptext, $this->tcolor, false);
        }
        if (!empty($this->url)) {
            echo '</a>';
        }
        // Finish the cell printing and start to clean up
        echo "</div>\n";

        // Where to place the next position
        switch ($this->newline) {
            case CellNewline::Right:
                // -> Next to this cell in the same line
                $renderer->setXy($this->left + $this->width, $this->top);
                $renderer->setLastCellHeight($this->height);
                break;
            case CellNewline::NextLine:
                // -> On a new line at the margin - Default
                $renderer->setXy(0, $renderer->getY() + $this->height + $renderer::CELL_PADDING * 2);
                // Reset the last cell height for the next line
                $renderer->resetLastCellHeight();
                break;
            case CellNewline::Below:
                // -> On a new line at the end of this cell
                $renderer->setXy($renderer->getX() + $this->width, $renderer->getY() + $this->height + $renderer::CELL_PADDING * 2);
                // Reset the last cell height for the next line
                $renderer->resetLastCellHeight();
                break;
        }
    }
}
