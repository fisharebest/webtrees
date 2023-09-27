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

use function str_contains;
use function str_replace;

/**
 * Class ReportHtmlCell
 */
class ReportHtmlCell extends ReportBaseCell
{
    /**
     * HTML Cell renderer
     *
     * @param HtmlRenderer $renderer
     *
     * @return void
     */
    public function render($renderer): void
    {
        if (str_contains($this->text, '{{:ptp:}}')) {
            return;
        }
        $temptext = str_replace('#PAGENUM#', (string) $renderer->pageNo(), $this->text);
        // underline «title» part of Source item
        $temptext = str_replace([
            '«',
            '»',
        ], [
            '<u>',
            '</u>',
        ], $temptext);

        // Set up the text style
        if ($renderer->getCurrentStyle() !== $this->styleName) {
            $renderer->setCurrentStyle($this->styleName);
        }

        // If (Future-feature-enable/disable cell padding)
        $cP = $renderer->cPadding;

        // Adjust the positions
        if ($this->left === ReportBaseElement::CURRENT_POSITION) {
            $this->left = $renderer->getX();
        } else {
            $renderer->setX($this->left);
        }

        if ($this->top === ReportBaseElement::CURRENT_POSITION) {
            $this->top = $renderer->getY();
        } else {
            $renderer->setY($this->top);
        }

        // Start collecting the HTML code
        echo '<div class="', $this->styleName, '" style="position:absolute;top:', $this->top, 'pt;';
        // Use Cell around padding to support RTL also
        echo 'padding:', $cP, 'pt;';
        // LTR (left) or RTL (right)
        echo $renderer->alignRTL, ':', $this->left, 'pt;';

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
        $cW = $this->width - $cP * 2.0;

        // If there is any text
        if (!empty($temptext)) {
            // Wrap the text
            $temptext = $renderer->textWrap($temptext, $cW);
            $tmph     = $renderer->getTextCellHeight($temptext);
            // Add some cell padding
            $this->height += $cP;
            if ($tmph > $this->height) {
                $this->height = $tmph;
            }
        }
        // Check the last cell height and adjust the current cell height if needed
        if ($renderer->lastCellHeight > $this->height) {
            $this->height = $renderer->lastCellHeight;
        }
        echo ' width:', $cW - $bpixX, 'pt;height:', $this->height - $bpixY, 'pt;';

        // Text alignment
        switch ($this->align) {
            case 'C':
                echo ' text-align:center;';
                break;
            case 'L':
                echo ' text-align:left;';
                break;
            case 'R':
                echo ' text-align:right;';
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
        if ($this->newline === 0) {
            // -> Next to this cell in the same line
            $renderer->setXy($this->left + $this->width, $this->top);
            $renderer->lastCellHeight = $this->height;
        } elseif ($this->newline === 1) {
            // -> On a new line at the margin - Default
            $renderer->setXy(0, $renderer->getY() + $this->height + $cP * 2);
            // Reset the last cell height for the next line
            $renderer->lastCellHeight = 0;
        } elseif ($this->newline === 2) {
            // -> On a new line at the end of this cell
            $renderer->setXy($renderer->getX() + $this->width, $renderer->getY() + $this->height + $cP * 2);
            // Reset the last cell height for the next line
            $renderer->lastCellHeight = 0;
        }
    }
}
