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

use function substr_count;

/**
 * @extends AbstractFootnote<AbstractRenderer&HtmlRendererInterface>
 */
class HtmlFootnote extends AbstractFootnote
{
    public function render(AbstractRenderer $renderer, bool $layout = true): void
    {
        $renderer->setCurrentStyle($renderer->getStyle('footnotenum'));
        echo '<a href="#footnote', $this->num, '"><sup>';
        $renderer->write($renderer->config->entity_rtl . $this->num);
        echo "</sup></a>\n";
    }

    public function renderFootnote(AbstractRenderer $renderer): void
    {
        $temptext = $this->resolvedFootnoteText($renderer);
        echo '<div><a id="footnote', $this->num, '"></a>';
        $renderer->write($this->num . '. ' . $temptext);
        echo '</div>';

        $renderer->setXY(0, $renderer->getY() + $this->getFootnoteHeight($renderer));
    }

    /**
     * @param AbstractRenderer&HtmlRendererInterface $renderer
     */
    public function getFootnoteHeight(AbstractRenderer $renderer, float $cellWidth = 0): float
    {
        $renderer->setCurrentStyle($this->style);

        if ($cellWidth > 0) {
            $this->text = $renderer->textWrap($this->text, $cellWidth);
        }

        $this->text .= "\n\n";
        $ct         = substr_count($this->text, "\n");
        $fsize      = $renderer->getCurrentStyleHeight();

        return $fsize * $ct * $renderer::LINE_HEIGHT_RATIO;
    }
}
