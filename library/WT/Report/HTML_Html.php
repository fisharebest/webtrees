<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

/**
 * Class WT_Report_HTML_Html - Base Report Generator, used by the SAX
 * parser to generate reports from the XML report file.
 */
class WT_Report_HTML_Html extends WT_Report_Base_Html {
	/**
	 * @param WT_Report_HTML $renderer
	 * @param boolean        $sub
	 * @param boolean        $inat
	 *
	 * @return string
	 */
	function render($renderer, $sub = false, $inat = true) {
		if (!empty($this->attrs["wt_style"])) $renderer->setCurrentStyle($this->attrs["wt_style"]);

		$this->text = $this->getStart() . $this->text;
		foreach ($this->elements as $element) {
			if (is_string($element) && $element == "footnotetexts") {
				$renderer->Footnotes();
			} elseif (is_string($element) && $element == "addpage") {
				$renderer->AddPage();
			} elseif ($element instanceof WT_Report_Base_Html) {
				$this->text .= $element->render($renderer, true, false);
			} else {
				$element->render($renderer);
			}
		}
		$this->text .= $this->getEnd();
		if ($sub) return $this->text;

		// If not called by an other attribute
		if ($inat) {
			$startX = $renderer->GetX();
			$startY = $renderer->GetY();
			$width = $renderer->getRemainingWidth();
			echo "<div style=\"position: absolute;top: ", $startY, "pt;", $renderer->alignRTL, ": ", $startX, "pt;width: ", $width, "pt;\">";
			$startY += $renderer->getCurrentStyleHeight() + 2;
			$renderer->SetY($startY);
		}

		echo $this->text;

		if ($inat) {
			echo "</div>\n";
		}
	}
}
