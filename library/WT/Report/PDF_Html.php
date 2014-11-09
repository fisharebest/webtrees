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
 * Class WT_Report_PDF_Html - Base Report Generator, used by the SAX
 * parser to generate reports from the XML report file.
 */
class WT_Report_PDF_Html extends WT_Report_Base_Html {
	/**
	 * @param         $renderer
	 * @param boolean $sub
	 *
	 * @return integer|string
	 */
	function render($renderer, $sub = false) {
		if (!empty($this->attrs['style'])) {
			$renderer->setCurrentStyle($this->attrs['style']);
		}
		if (!empty($this->attrs['width'])) {
			$this->attrs['width'] *= 3.9;
		}

		$this->text = $this->getStart() . $this->text;
		foreach ($this->elements as $element) {
			if (is_string($element) && $element == "footnotetexts") {
				$renderer->Footnotes();
			} elseif (is_string($element) && $element == "addpage") {
				$renderer->newPage();
			} elseif ($element instanceof WT_Report_Base_Html) {
				$this->text .= $element->render($renderer, true);
			} else {
				$element->render($renderer);
			}
		}
		$this->text .= $this->getEnd();
		if ($sub) {
			return $this->text;
		}
		$renderer->writeHTML($this->text); //prints 2 empty cells in the Expanded Relatives report
		return 0;
	}
}
