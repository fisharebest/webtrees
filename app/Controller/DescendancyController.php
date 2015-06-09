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

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsCharts;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;
use Rhumsaa\Uuid\Uuid;

/**
 * Controller for the descendancy chart
 */
class DescendancyController extends ChartController {
	/** @var int Show boxes for cousins */
	public $show_cousins;

	/** @var int Determines style of chart */
	public $chart_style;

	/** @var int Number of generations to display */
	public $generations;

	/** @var array d'Aboville numbering system <http://www.saintclair.org/numbers/numdob.html> */
	public $dabo_num = array();

	/** @var array d'Aboville numbering system <http://www.saintclair.org/numbers/numdob.html> */
	public $dabo_sex = array();

	/**
	 * Create the descendancy controller
	 */
	public function __construct() {
		global $WT_TREE;

		parent::__construct();

		// Extract parameters from form
		$this->chart_style = Filter::getInteger('chart_style', 0, 3, 0);
		$this->generations = Filter::getInteger('generations', 2, $WT_TREE->getPreference('MAX_DESCENDANCY_GENERATIONS'), $WT_TREE->getPreference('DEFAULT_PEDIGREE_GENERATIONS'));

		if ($this->root && $this->root->canShowName()) {
			$this->setPageTitle(
				/* I18N: %s is an individual’s name */
				I18N::translate('Descendants of %s', $this->root->getFullName())
			);
		} else {
			$this->setPageTitle(I18N::translate('Descendants'));
		}
	}

	/**
	 * Print a child family
	 *
	 * @param Individual $person
	 * @param int        $depth the descendancy depth to show
	 * @param string     $label
	 * @param string     $gpid
	 */
	public function printChildFamily(Individual $person, $depth, $label = '1.', $gpid = '') {

		if ($depth < 2) {
			return;
		}
		foreach ($person->getSpouseFamilies() as $family) {
			FunctionsCharts::printSosaFamily($family->getXref(), '', -1, $label, $person->getXref(), $gpid, 0, $this->showFull());
			$i = 1;
			foreach ($family->getChildren() as $child) {
				$this->printChildFamily($child, $depth - 1, $label . ($i++) . '.', $person->getXref());
			}
		}
	}

	/**
	 * print a child descendancy
	 *
	 * @param Individual $person
	 * @param int        $depth the descendancy depth to show
	 */
	public function printChildDescendancy(Individual $person, $depth) {
		echo "<li>";
		echo "<table><tr><td>";
		if ($depth == $this->generations) {
			echo "<img src=\"" . Theme::theme()->parameter('image-spacer') . "\" height=\"3\" width=\"", Theme::theme()->parameter('chart-descendancy-indent'), "\" alt=\"\"></td><td>";
		} else {
			echo "<img src=\"" . Theme::theme()->parameter('image-spacer') . "\" height=\"3\" width=\"3\" alt=\"\">";
			echo "<img src=\"" . Theme::theme()->parameter('image-hline') . "\" height=\"3\" width=\"", Theme::theme()->parameter('chart-descendancy-indent') - 3, "\" alt=\"\"></td><td>";
		}
		FunctionsPrint::printPedigreePerson($person, $this->showFull());
		echo '</td>';

		// check if child has parents and add an arrow
		echo '<td></td>';
		echo '<td>';
		foreach ($person->getChildFamilies() as $cfamily) {
			foreach ($cfamily->getSpouses() as $parent) {
				FunctionsCharts::printUrlArrow('?rootid=' . $parent->getXref() . '&amp;generations=' . $this->generations . '&amp;chart_style=' . $this->chart_style . '&amp;show_full=' . $this->showFull() . '&amp;ged=' . $parent->getTree()->getNameUrl(), I18N::translate('Start at parents'), 2);
				// only show the arrow for one of the parents
				break;
			}
		}

		// d'Aboville child number
		$level = $this->generations - $depth;
		if ($this->showFull()) {
			echo '<br><br>&nbsp;';
		}
		echo '<span dir="ltr">'; //needed so that RTL languages will display this properly
		if (!isset($this->dabo_num[$level])) {
			$this->dabo_num[$level] = 0;
		}
		$this->dabo_num[$level]++;
		$this->dabo_num[$level + 1] = 0;
		$this->dabo_sex[$level]     = $person->getSex();
		for ($i = 0; $i <= $level; $i++) {
			$isf = $this->dabo_sex[$i];
			if ($isf === 'M') {
				$isf = '';
			}
			if ($isf === 'U') {
				$isf = 'NN';
			}
			echo "<span class=\"person_box" . $isf . "\">&nbsp;" . $this->dabo_num[$i] . "&nbsp;</span>";
			if ($i < $level) {
				echo '.';
			}
		}
		echo "</span>";
		echo "</td></tr>";
		echo "</table>";
		echo "</li>";

		// loop for each spouse
		foreach ($person->getSpouseFamilies() as $family) {
			$this->printFamilyDescendancy($person, $family, $depth);
		}
	}

	/**
	 * print a family descendancy
	 *
	 * @param Individual $person
	 * @param Family     $family
	 * @param int        $depth the descendancy depth to show
	 */
	private function printFamilyDescendancy(Individual $person, Family $family, $depth) {
		$uid = Uuid::uuid4(); // create a unique ID
		// print marriage info
		echo '<li>';
		echo '<img src="', Theme::theme()->parameter('image-spacer'), '" height="2" width="', Theme::theme()->parameter('chart-descendancy-indent') + 4, '" alt="">';
		echo '<span class="details1">';
		echo "<a href=\"#\" onclick=\"expand_layer('" . $uid . "'); return false;\" class=\"top\"><i id=\"" . $uid . "_img\" class=\"icon-minus\" title=\"" . I18N::translate('View family') . "\"></i></a>";
		if ($family->canShow()) {
			foreach ($family->getFacts(WT_EVENTS_MARR) as $fact) {
				echo ' <a href="', $family->getHtmlUrl(), '" class="details1">', $fact->summary(), '</a>';
			}
		}
		echo '</span>';

		// print spouse
		$spouse = $family->getSpouse($person);
		echo '<ul id="' . $uid . '" class="generation">';
		echo '<li>';
		echo '<table><tr><td>';
		FunctionsPrint::printPedigreePerson($spouse, $this->showFull());
		echo '</td>';

		// check if spouse has parents and add an arrow
		echo '<td></td>';
		echo '<td>';
		if ($spouse) {
			foreach ($spouse->getChildFamilies() as $cfamily) {
				foreach ($cfamily->getSpouses() as $parent) {
					FunctionsCharts::printUrlArrow('?rootid=' . $parent->getXref() . '&amp;generations=' . $this->generations . '&amp;chart_style=' . $this->chart_style . '&amp;show_full=' . $this->showFull() . '&amp;ged=' . $parent->getTree()->getNameUrl(), I18N::translate('Start at parents'), 2);
					// only show the arrow for one of the parents
					break;
				}
			}
		}
		if ($this->showFull()) {
			echo '<br><br>&nbsp;';
		}
		echo '</td></tr>';

		// children
		$children = $family->getChildren();
		echo '<tr><td colspan="3" class="details1" >&nbsp;&nbsp;';
		if ($children) {
			echo GedcomTag::getLabel('NCHI') . ': ' . count($children);
		} else {
			// Distinguish between no children (NCHI 0) and no recorded
			// children (no CHIL records)
			if (strpos($family->getGedcom(), '\n1 NCHI 0')) {
				echo GedcomTag::getLabel('NCHI') . ': ' . count($children);
			} else {
				echo I18N::translate('No children');
			}
		}
		echo '</td></tr></table>';
		echo '</li>';
		if ($depth > 1) {
			foreach ($children as $child) {
				$this->printChildDescendancy($child, $depth - 1);
			}
		}
		echo '</ul>';
		echo '</li>';
	}

	/**
	 * Find all the individuals that are descended from an individual.
	 *
	 * @param Individual   $person
	 * @param int          $n
	 * @param Individual[] $array
	 *
	 * @return Individual[]
	 */
	public function individualDescendancy(Individual $person, $n, $array) {
		if ($n < 1) {
			return $array;
		}
		$array[$person->getXref()] = $person;
		foreach ($person->getSpouseFamilies() as $family) {
			$spouse = $family->getSpouse($person);
			if ($spouse) {
				$array[$spouse->getXref()] = $spouse;
			}
			foreach ($family->getChildren() as $child) {
				$array = $this->individualDescendancy($child, $n - 1, $array);
			}
		}

		return $array;
	}

	/**
	 * Find all the families that are descended from an individual.
	 *
	 * @param Individual $person
	 * @param int        $n
	 * @param Family[]   $array
	 *
	 * @return Family[]
	 */
	public function familyDescendancy($person, $n, $array) {
		if ($n < 1) {
			return $array;
		}
		foreach ($person->getSpouseFamilies() as $family) {
			$array[$family->getXref()] = $family;
			foreach ($family->getChildren() as $child) {
				$array = $this->familyDescendancy($child, $n - 1, $array);
			}
		}

		return $array;
	}
}
