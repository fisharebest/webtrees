<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
 * Class ReportHtmlLine
 */
class ReportHtmlLine extends ReportBaseLine {
	/**
	 * HTML line renderer
	 *
	 * @param ReportHtml $renderer
	 */
	public function render($renderer) {
		if ($this->x1 == '.') {
			$this->x1 = $renderer->getX();
		}
		if ($this->y1 == '.') {
			$this->y1 = $renderer->getY();
		}
		if ($this->x2 == '.') {
			$this->x2 = $renderer->getRemainingWidth();
		}
		if ($this->y2 == '.') {
			$this->y2 = $renderer->getY();
		}
		// Vertical line
		if ($this->x1 == $this->x2) {
			echo "<div style=\"position:absolute;overflow:hidden;border-", $renderer->alignRTL, ":solid black 1pt;", $renderer->alignRTL, ":", $this->x1, "pt;top:", $this->y1 + 1, "pt;width:1pt;height:", $this->y2 - $this->y1, "pt;\"> </div>\n";
		}
		// Horizontal line
		if ($this->y1 == $this->y2) {
			echo "<div style=\"position:absolute;overflow:hidden;border-top:solid black 1pt;", $renderer->alignRTL, ":", $this->x1, "pt;top:", $this->y1 + 1, "pt;width:", $this->x2 - $this->x1, "pt;height:1pt;\"> </div>\n";
		}
		// Keep max Y updated
		// One or the other will be higher... lasy mans way...
		$renderer->addMaxY($this->y1);
		$renderer->addMaxY($this->y2);
	}
}
