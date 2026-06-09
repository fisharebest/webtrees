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

use function str_replace;

/**
 * @extends AbstractText<AbstractRenderer&PdfRendererInterface>
 */
class PdfText extends AbstractText
{
    public function render(AbstractRenderer $renderer, bool $layout = true): void
    {
        $renderer->setCurrentStyle($this->style);
        $temptext = $this->resolvedText($renderer);

        if ($this->color === '') {
            $renderer->setTextColor(0, 0, 0);
        } else {
            $hex = new HexColor($this->color);
            $renderer->setTextColor($hex->red, $hex->green, $hex->blue);
        }

        $temptext = (new RightToLeftFormatter())->format($temptext);
        $temptext = str_replace(
            [
                '<br><span dir="rtl">',
                '<br><span dir="ltr">',
            ],
            [
                '<span dir="rtl" ><br>',
                '<span dir="ltr" ><br>',
            ],
            $temptext
        );
        $renderer->writeHTML($temptext, false, false, true);
        // Reset the text color to black, or it will be inherited
        $renderer->setTextColor(0, 0, 0);
    }

    public function getHeight(AbstractRenderer $renderer): float
    {
        return 0;
    }
}
