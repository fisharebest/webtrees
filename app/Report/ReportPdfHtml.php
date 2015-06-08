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
 * Class ReportPdfHtml
 */
class ReportPdfHtml extends ReportBaseHtml {
	/**
	 * Render the output.
	 *
	 * @param      $renderer
	 * @param bool $sub
	 *
	 * @return int|string
	 */
	public function render($renderer, $sub = false) {
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
			} elseif ($element instanceof ReportBaseHtml) {
				$element->render($renderer, true);
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
