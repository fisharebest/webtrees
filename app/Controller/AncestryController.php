<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\FunctionsCharts;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;

/**
 * Controller for the ancestry chart
 */
class AncestryController extends ChartController {
	/** @var int Show boxes for cousins */
	public $show_cousins;

	/** @var string Determines style of chart  '0', '1', '2' or '3' */
	public $chart_style;

	/** @var int Number of generations to display */
	public $generations;

	/**
	 * Startup activity
	 */
	public function __construct() {
		parent::__construct();

		// Request details
		$this->show_cousins = Filter::getInteger('show_cousins', 0, 1);
		$this->chart_style  = Filter::get('chart_style', '[0123]', '0');
		$this->generations  = Filter::getInteger('PEDIGREE_GENERATIONS', 2, $this->tree()->getPreference('MAX_PEDIGREE_GENERATIONS'), $this->tree()->getPreference('DEFAULT_PEDIGREE_GENERATIONS'));
	}

	/**
	 * print a child ascendancy
	 *
	 * @param Individual $individual
	 * @param int        $sosa
	 * @param int        $generations
	 */
	private function printChildAscendancy(Individual $individual, $sosa, $generations) {
		echo '<li class="wt-ancestors-chart-list-item">';
		echo '<table><tbody><tr><td>';
		if ($sosa === 1) {
			echo '<img src="', Theme::theme()->parameter('image-spacer'), '" height="3" width="15"></td><td>';
		} else {
			echo '<img src="', Theme::theme()->parameter('image-spacer'), '" height="3" width="2">';
			echo '<img src="', Theme::theme()->parameter('image-hline'), '" height="3" width="13"></td><td>';
		}
		FunctionsPrint::printPedigreePerson($individual);
		echo '</td><td>';
		if ($sosa > 1) {
			echo FontAwesome::linkIcon('arrow-down', I18N::translate('Ancestors of %s', $individual->getFullName()), ['href' => '?rootid=' . $individual->getXref() . '&amp;PEDIGREE_GENERATIONS=' . $this->generations . '&amp;chart_style=' . $this->chart_style . '&amp;ged=' . $individual->getTree()->getNameUrl()]);
		}
		echo '</td><td class="details1">&nbsp;<span class="person_box' . ($sosa === 1 ? 'NN' : ($sosa % 2 ? 'F' : '')) . '">', I18N::number($sosa), '</span> ';
		echo '</td><td class="details1">&nbsp;', FunctionsCharts::getSosaName($sosa), '</td>';
		echo '</tr></tbody></table>';

		// Parents
		$family = $individual->getPrimaryChildFamily();
		if ($family && $generations > 0) {
			// Marriage details
			echo '<span class="details1">';
			echo '<img src="', Theme::theme()->parameter('image-spacer'), '" height="2" width="15"><a href="#" onclick="return expand_layer(\'sosa_', $sosa, '\');" class="top"><i id="sosa_', $sosa, '_img" class="icon-minus" title="', I18N::translate('View this family'), '"></i></a>';
			echo ' <span class="person_box">', I18N::number($sosa * 2), '</span> ', I18N::translate('and');
			echo ' <span class="person_boxF">', I18N::number($sosa * 2 + 1), '</span>';
			if ($family->canShow()) {
				foreach ($family->getFacts(WT_EVENTS_MARR) as $fact) {
					echo ' <a href="', $family->getHtmlUrl(), '" class="details1">', $fact->summary(), '</a>';
				}
			}
			echo '</span>';
			echo '<ul class="wt-ancestors-chart-list" id="sosa_', $sosa, '">';
			if ($family->getHusband()) {
				$this->printChildAscendancy($family->getHusband(), $sosa * 2, $generations - 1);
			}
			if ($family->getWife()) {
				$this->printChildAscendancy($family->getWife(), $sosa * 2 + 1, $generations - 1);
			}
			echo '</ul>';
		}
		echo '</li>';
	}

	/**
	 * Get the title of this chart
	 *
	 * @return string
	 */
	public function getPageTitle() {
		if ($this->root !== null && $this->root->canShowName()) {
			return /* I18N: %s is an individual’s name */ I18N::translate('Ancestors of %s', $this->root->getFullName());
		} else {
			return I18N::translate('Ancestors');
		}
	}

	/**
	 * Get the content of this chart
	 *
	 * @return string
	 */
	public function getChart() {
		if ($this->root === null || !$this->root->canShowName()) {
			return '<p>' . I18N::translate('This individual does not exist or you do not have permission to view it.') . '</p>';
		}

		switch ($this->chart_style) {
		case 0:
			// List
			return
				'<ul class="chart_common">' .
				$this->printChildAscendancy($this->root, 1, $this->generations - 1) .
				'</ul>';

		case 1:
			// Booklet
			// first page : show indi facts
			FunctionsPrint::printPedigreePerson($this->root);
			// process the tree
			$ancestors = $this->sosaAncestors($this->generations - 1);
			$ancestors = array_filter($ancestors); // The SOSA array includes empty placeholders

			foreach ($ancestors as $sosa => $individual) {
				foreach ($individual->getChildFamilies() as $family) {
					FunctionsCharts::printSosaFamily($family->getXref(), $individual->getXref(), $sosa, '', '', '', $this->show_cousins);
				}
			}
			break;

		case 2:
			// Individual list
			$ancestors = $this->sosaAncestors($this->generations);
			$ancestors = array_filter($ancestors); // The SOSA array includes empty placeholders

			return FunctionsPrintLists::individualTable($ancestors, 'sosa');

		case 3:
			// Family list
			$ancestors = $this->sosaAncestors($this->generations - 1);
			$ancestors = array_filter($ancestors); // The SOSA array includes empty placeholders
			$families  = [];
			foreach ($ancestors as $individual) {
				foreach ($individual->getChildFamilies() as $family) {
					$families[$family->getXref()] = $family;
				}
			}

			return FunctionsPrintLists::familyTable($families);
		}

		return '';
	}
}
