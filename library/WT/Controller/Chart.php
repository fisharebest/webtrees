<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
 * Class WT_Controller_Chart - Base controller for all chart pages
 */
class WT_Controller_Chart extends WT_Controller_Page {
	/** @var WT_Individual Who is chart about? */
	public $root;

	/** @var string An error message, in case we cannot construct the chart */
	public $error_message;

	/**
	 * Create the chart controller
	 */
	public function __construct() {
		parent::__construct();

		$rootid = WT_Filter::get('rootid', WT_REGEX_XREF);
		$this->root = WT_Individual::getInstance($rootid);
		if (!$this->root) {
			// Missing root individual?  Show the chart for someone.
			$this->root   = $this->getSignificantIndividual();
		}

		if (!$this->root || !$this->root->canShowName()) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			$this->error_message = WT_I18N::translate('This individual does not exist or you do not have permission to view it.');
		}
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return WT_Individual
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
	 * @param integer $generations
	 *
	 * @return WT_Individual[]
	 */
	public function sosaAncestors($generations) {
		$ancestors = array(
			1 => $this->root
		);

		// Subtract one generation, as this algorithm includes parents.
		$max = pow(2, $generations - 1);

		for ($i = 1; $i < $max; $i++) {
			$ancestors[$i * 2] = null;
			$ancestors[$i * 2 + 1] = null;
			$person = $ancestors[$i];
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
}
