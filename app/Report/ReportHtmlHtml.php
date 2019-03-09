<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees\Report;

/**
 * Class ReportHtmlHtml
 */
class ReportHtmlHtml extends ReportBaseHtml
{
    /**
     * Render the elements.
     *
     * @param ReportHtml $renderer
     * @param bool       $sub
     * @param bool       $inat
     *
     * @return string
     */
    public function render($renderer, $sub = false, $inat = true)
    {
        if (!empty($this->attrs["wt_style"])) {
            $renderer->setCurrentStyle($this->attrs["wt_style"]);
        }

        $this->text = $this->getStart() . $this->text;
        foreach ($this->elements as $element) {
            if (is_string($element) && $element == "footnotetexts") {
                $renderer->footnotes();
            } elseif (is_string($element) && $element == "addpage") {
                $renderer->addPage();
            } elseif ($element instanceof ReportBaseHtml) {
                $element->render($renderer, true, false);
            } else {
                $element->render($renderer);
            }
        }
        $this->text .= $this->getEnd();
        if ($sub) {
            return $this->text;
        }

        // If not called by an other attribute
        if ($inat) {
            $startX = $renderer->getX();
            $startY = $renderer->getY();
            $width  = $renderer->getRemainingWidth();
            echo "<div style=\"position: absolute;top: ", $startY, "pt;", $renderer->alignRTL, ": ", $startX, "pt;width: ", $width, "pt;\">";
            $startY += $renderer->getCurrentStyleHeight() + 2;
            $renderer->setY($startY);
        }

        echo $this->text;

        if ($inat) {
            echo "</div>\n";
        }
    }
}
