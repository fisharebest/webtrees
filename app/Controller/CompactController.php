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
namespace Fisharebest\Webtrees\Controller;

use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;

/**
 * Controller for the compact chart
 */
class CompactController extends ChartController {
	/** @var bool Data for the view .*/
	public $show_thumbs = false;

	/** int[] Data for the controller. */
	private $treeid = array();

	/**
	 * Startup activity
	 */
	public function __construct() {
		parent::__construct();

		// Extract the request parameters
		$this->show_thumbs = Filter::getBool('show_thumbs');

		if ($this->root && $this->root->canShowName()) {
			$this->setPageTitle(
				/* I18N: %s is an individual’s name */
			I18N::translate('Compact tree of %s', $this->root->getFullName())
		);
		} else {
			$this->setPageTitle(I18N::translate('Compact tree'));
		}
		$this->treeid = $this->sosaAncestors(5);
	}

	/**
	 * Get an individual by their SOSA number.
	 *
	 * @param int $n
	 *
	 * @return string
	 */
	public function sosaIndividual($n) {
		$indi = $this->treeid[$n];

		if ($indi && $indi->canShowName()) {
			$name    = $indi->getFullName();
			$addname = $indi->getAddName();

			if ($this->show_thumbs && $indi->getTree()->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
				$html = $indi->displayImage();
			} else {
				$html = '';
			}

			$html .= '<a class="name1" href="' . $indi->getHtmlUrl() . '">';
			$html .= $name;
			if ($addname) {
				$html .= '<br>' . $addname;
			}
			$html .= '</a>';
			$html .= '<br>';
			if ($indi->canShow()) {
				$html .= '<div class="details1">' . $indi->getLifeSpan() . '</div>';
			}
		} else {
			// Empty box
			$html = '&nbsp;';
		}

		// -- box color
		$isF = '';
		if ($n == 1) {
			if ($indi && $indi->getSex() == 'F') {
				$isF = 'F';
			}
		} elseif ($n % 2) {
			$isF = 'F';
		}

		// -- box size
		if ($n == 1) {
			return '<td class="person_box' . $isF . ' person_box_template" style="text-align:center; vertical-align:top;">' . $html . '</td>';
		} else {
			return '<td class="person_box' . $isF . ' person_box_template" style="text-align:center; vertical-align:top;" width="15%">' . $html . '</td>';
		}
	}

	/**
	 * Get an arrow, pointing to other generations.
	 *
	 * @param int    $n
	 * @param string $arrow_dir
	 *
	 * @return string
	 */
	public function sosaArrow($n, $arrow_dir) {
		$indi = $this->treeid[$n];

		$arrow_dir = substr($arrow_dir, 0, 1);
		if (I18N::direction() === 'rtl') {
			if ($arrow_dir === 'l') {
				$arrow_dir = 'r';
			} elseif ($arrow_dir === 'r') {
				$arrow_dir = 'l';
			}
		}

		if ($indi) {
			$title = I18N::translate('Compact tree of %s', $indi->getFullName());
			$text  = '<a class="icon-' . $arrow_dir . 'arrow" title="' . strip_tags($title) . '" href="?rootid=' . $indi->getXref();
			if ($this->show_thumbs) {
				$text .= "&amp;show_thumbs=" . $this->show_thumbs;
			}
			$text .= "\"></a>";
		} else {
			$text = '<i class="icon-' . $arrow_dir . 'arrow"></i>';
		}

		return $text;
	}
}
