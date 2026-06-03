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

use function explode;
use function substr_count;

/**
 * @extends AbstractText<AbstractRenderer&HtmlRendererInterface>
 */
class HtmlText extends AbstractText
{
    public function render(AbstractRenderer $renderer, bool $layout = true): void
    {
        // Set up the style
        $renderer->setCurrentStyle($this->style);
        $temptext = $this->resolvedText($renderer);

        // If any text at all
        if ($temptext !== '') {
            // If called by an other element
            if (!$layout) {
                $renderer->write($temptext, $this->color);
            } else {
                // Save the start positions
                $startX = $renderer->getX();
                $startY = $renderer->getY();
                $width  = $renderer->getRemainingWidth();
                // If text is wider than the page then wrap it
                if ($renderer->getStringWidth($temptext) > $width) {
                    $lines = explode("\n", $temptext);
                    foreach ($lines as $line) {
                        echo '<div style="position:absolute;top:', $startY, 'pt;', $renderer->config->align_rtl, ':', $startX, 'pt;width:', $width, 'pt;">';
                        $line = $renderer->textWrap($line, $width);
                        $startY += $renderer->getTextCellHeight($line);
                        $renderer->setY($startY);
                        $renderer->write($line, $this->color);
                        echo "</div>\n";
                    }
                } else {
                    echo '<div style="position:absolute;top:', $startY, 'pt;', $renderer->config->align_rtl, ':', $startX, 'pt;width:', $width, 'pt;">';
                    $renderer->write($temptext, $this->color);
                    echo "</div>\n";
                    $renderer->setX($startX + $renderer->getStringWidth($temptext));
                    if ($renderer->countLines($temptext) !== 1) {
                        $renderer->setXY(0, $startY + $renderer->getTextCellHeight($temptext));
                    }
                }
            }
        }
    }

    public function getHeight(AbstractRenderer $renderer): float
    {
        $ct = substr_count($this->text, "\n");
        if ($ct > 0) {
            $ct += 1;
        }
        $style = $this->style;

        return $style->size * $ct * $renderer::LINE_HEIGHT_RATIO;
    }
}
