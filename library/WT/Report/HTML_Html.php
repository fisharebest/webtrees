<?php
// HTML Report Generator
//
// used by the SAX parser to generate HTML reports from the XML report file.
//
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

class WT_Report_HTML_Html extends WT_Report_Base_Html {
	/**
	 * @param string  $html
	 * @param boolean $sub
	 * @param boolean $inat
	 *
	 * @return string
	 */
	function render($html, $sub = false, $inat = true) {

		if (!empty($this->attrs["wt_style"])) $html->setCurrentStyle($this->attrs["wt_style"]);

		$this->text = $this->getStart() . $this->text;
		foreach ($this->elements as $element) {
			if (is_string($element) && $element == "footnotetexts") {
				$html->Footnotes();
			} elseif (is_string($element) && $element == "addpage") {
				$html->AddPage();
			} elseif ($element instanceof WT_Report_Base_Html) {
				$this->text .= $element->render($html, true, false);
			} else {
				$element->render($html);
			}
		}
		$this->text .= $this->getEnd();
		if ($sub) return $this->text;

		// If not called by an other attribute
		if ($inat) {
			$startX = $html->GetX();
			$startY = $html->GetY();
			$width = $html->getRemainingWidth();
			echo "<div style=\"position: absolute;top: ", $startY, "pt;", $html->alignRTL, ": ", $startX, "pt;width: ", $width, "pt;\">";
			$startY += $html->getCurrentStyleHeight() + 2;
			$html->SetY($startY);
		}

		echo $this->text;

		if ($inat) {
			echo "</div>\n";
		}
	}
}
