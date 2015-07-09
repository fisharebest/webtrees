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
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;

/**
 * Base controller for all chart pages
 */
class ChartController extends PageController {
	/** @var Individual Who is chart about? */
	public $root;

	/** @var bool determines the detail shown in the personbox */
	private $show_full;

	/** @var string An error message, in case we cannot construct the chart */
	public $error_message;

	/** @var \stdClass personbox dimensions */
	private $box;
	/**
	 * Create the chart controller
	 *
	 * @param int $show_full needed for use by charts module
	 */
	public function __construct($show_full = 1) {
		global $WT_TREE;

		parent::__construct();

		$rootid     = Filter::get('rootid', WT_REGEX_XREF);
		$this->root = Individual::getInstance($rootid, $WT_TREE);
		if (!$this->root) {
			// Missing root individual?  Show the chart for someone.
			$this->root = $this->getSignificantIndividual();
		}

		if (!$this->root || !$this->root->canShowName()) {
			http_response_code(404);
			$this->error_message = I18N::translate('This individual does not exist or you do not have permission to view it.');
		}

		// Extract parameter from form
		if ($show_full) {
			$this->show_full = Filter::getInteger('show_full', 0, 1, $WT_TREE->getPreference('PEDIGREE_FULL_DETAILS'));
		} else {
			$this->show_full = 0;
		}

		$this->box = new \stdClass();
		if ($this->showFull()) {
			$this->box->width  = Theme::theme()->parameter('chart-box-x');
			$this->box->height = Theme::theme()->parameter('chart-box-y');
		} else {
			$this->box->width  = Theme::theme()->parameter('compact-chart-box-x');
			$this->box->height = Theme::theme()->parameter('compact-chart-box-y');
		}
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return Individual
	 */
	public function getSignificantIndividual() {
		if ($this->root) {
			return $this->root;
		} else {
			return parent::getSignificantIndividual();
		}
	}

	/**
	 * Find the direct-line ancestors of an individual.  Array indexes are SOSA numbers.
	 *
	 * @param int $generations
	 *
	 * @return Individual[]
	 */
	public function sosaAncestors($generations) {
		$ancestors = array(
			1 => $this->root,
		);

		// Subtract one generation, as this algorithm includes parents.
		$max = pow(2, $generations - 1);

		for ($i = 1; $i < $max; $i++) {
			$ancestors[$i * 2]     = null;
			$ancestors[$i * 2 + 1] = null;
			$person                = $ancestors[$i];
			if ($person) {
				$family = $person->getPrimaryChildFamily();
				if ($family) {
					if ($family->getHusband()) {
						$ancestors[$i * 2] = $family->getHusband();
					}
					if ($family->getWife()) {
						$ancestors[$i * 2 + 1] = $family->getWife();
					}
				}
			}
		}

		return $ancestors;
	}

	/**
	 * Function showFull
	 *
	 * @return bool
	 */
	public function showFull() {
		return $this->show_full;
	}

	/**
	 * Function boxDimensions
	 *
	 * @return \stdClass
	 */
	public function getBoxDimensions() {
		return $this->box;
	}
}
