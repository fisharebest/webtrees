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
 * @extends AbstractFootnote<AbstractRenderer&PdfRendererInterface>
 */
class PdfFootnote extends AbstractFootnote
{
    public function render(AbstractRenderer $renderer, bool $layout = true): void
    {
        $renderer->setCurrentStyle($renderer->getStyle('footnotenum'));
        $renderer->writeText($renderer->getCurrentStyleHeight(), $this->numText, $this->addlink);
    }

    public function renderFootnote(AbstractRenderer $renderer): void
    {
        $temptext = $this->resolvedFootnoteText($renderer);
        // Set the link to this y/page position
        $renderer->setLinkDestination($this->addlink, -1, -1);
        // Print first the source number
        if ($renderer->isRTL()) {
            $renderer->writeHTML('<span> .' . $this->num . '</span>', false);
        } else {
            $temptext = '<span>' . $this->num . '. </span>' . $temptext;
        }
        $renderer->writeHTML($temptext, true, false, true);
    }

    public function getFootnoteHeight(AbstractRenderer $renderer, float $cellWidth = 0): float
    {
        return 0;
    }
}
