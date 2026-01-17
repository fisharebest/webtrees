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

class ReportHtmlImage extends ReportBaseImage
{
    /**
     * Image renderer
     *
     * @param HtmlRenderer $renderer
     *
     * @return void
     */
    public function render($renderer): void
    {
        static $lastpicbottom, $lastpicpage, $lastpicleft, $lastpicright;

        // Get the current positions
        if ($this->x === ReportBaseElement::CURRENT_POSITION) {
            $this->x = $renderer->getX();
        }
        if ($this->y === ReportBaseElement::CURRENT_POSITION) {
            //-- first check for a collision with the last picture
            if ($lastpicbottom !== null && $renderer->pageNo() === $lastpicpage && $lastpicbottom >= $renderer->getY() && $this->x >= $lastpicleft && $this->x <= $lastpicright) {
                $renderer->setY($lastpicbottom + $renderer->cPadding * 2);
            }
            $this->y = $renderer->getY();
        }

        // Image alignment
        switch ($this->align) {
            case 'L':
                echo '<div style="position:absolute;top:', $this->y, 'pt;left:0pt;width:', $renderer->getRemainingWidth(), "pt;text-align:left;\">\n";
                echo '<img src="', $this->file, '" style="width:', $this->width, 'pt;height:', $this->height, "pt;\" alt=\"\">\n</div>\n";
                break;
            case 'C':
                echo '<div style="position:absolute;top:', $this->y, 'pt;left:0pt;width:', $renderer->getRemainingWidth(), "pt;text-align:center;\">\n";
                echo '<img src="', $this->file, '" style="width:', $this->width, 'pt;height:', $this->height, "pt;\" alt=\"\">\n</div>\n";
                break;
            case 'R':
                echo '<div style="position:absolute;top:', $this->y, 'pt;left:0pt;width:', $renderer->getRemainingWidth(), "pt;text-align:right;\">\n";
                echo '<img src="', $this->file, '" style="width:', $this->width, 'pt;height:', $this->height, "pt;\" alt=\"\">\n</div>\n";
                break;
            default:
                echo '<img src="', $this->file, '" style="position:absolute;', $renderer->alignRTL, ':', $this->x, 'pt;top:', $this->y, 'pt;width:', $this->width, 'pt;height:', $this->height, "pt;\" alt=\"\">\n";
        }

        $lastpicpage   = $renderer->pageNo();
        $lastpicleft   = $this->x;
        $lastpicright  = $this->x + $this->width;
        $lastpicbottom = $this->y + $this->height;
        // Setup for the next line
        if ($this->line === 'N') {
            $renderer->setY($lastpicbottom);
        }
        // Keep max Y updated
        $renderer->addMaxY($lastpicbottom);
    }

    /**
     * Get the image height
     * This would be called from the TextBox only for multiple images
     * so we add a bit bottom space between the images
     *
     * @param HtmlRenderer $renderer
     *
     * @return float
     */
    public function getHeight($renderer): float
    {
        return $this->height + $renderer->cPadding * 2;
    }
}
