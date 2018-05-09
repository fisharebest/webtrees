<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\FunctionsCharts;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A chart of direct-line descendants.
 */
class DescendantsChartController extends AbstractChartController {
	// Chart styles
	const CHART_STYLE_LIST        = 0;
	const CHART_STYLE_BOOKLET     = 1;
	const CHART_STYLE_INDIVIDUALS = 2;
	const CHART_STYLE_FAMILIES    = 3;

	// Defaults
	const DEFAULT_STYLE               = self::CHART_STYLE_LIST;
	const DEFAULT_GENERATIONS         = 3;
	const DEFAULT_MAXIMUM_GENERATIONS = 9;

	/**
	 * A form to request the chart parameters.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function page(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'descendancy_chart');

		$xref         = $request->get('xref');
		$individual   = Individual::getInstance($xref, $tree);

		$minimum_generations = 2;
		$maximum_generations = (int) $tree->getPreference('MAX_DESCENDANCY_GENERATIONS', self::DEFAULT_MAXIMUM_GENERATIONS);
		$default_generations = (int) $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS', self::DEFAULT_GENERATIONS);

		$chart_style  = (int) $request->get('chart_style', self::DEFAULT_STYLE);
		$generations  = (int) $request->get('generations', $default_generations);

		$generations = min($generations, $maximum_generations);
		$generations = max($generations, $minimum_generations);

		if ($individual !== null && $individual->canShowName()) {
			$title = /* I18N: %s is an individual’s name */ I18N::translate('Descendants of %s', $individual->getFullName());
		} else {
			$title = I18N::translate('Descendants');
		}

		return $this->viewResponse('descendants-page', [
			'chart_style'         => $chart_style,
			'chart_styles'        => $this->chartStyles(),
			'default_generations' => $default_generations,
			'generations'         => $generations,
			'individual'          => $individual,
			'maximum_generations' => $maximum_generations,
			'minimum_generations' => $minimum_generations,
			'title'               => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function chart(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'descendancy_chart');

		$xref         = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual);

		$minimum_generations = 2;
		$maximum_generations = (int) $tree->getPreference('MAX_PEDIGREE_GENERATIONS', self::DEFAULT_MAXIMUM_GENERATIONS);
		$default_generations = (int) $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS', self::DEFAULT_GENERATIONS);

		$chart_style  = (int) $request->get('chart_style', self::DEFAULT_STYLE);
		$generations  = (int) $request->get('generations', $default_generations);

		$generations = min($generations, $maximum_generations);
		$generations = max($generations, $minimum_generations);

		$descendants = $this->descendants($individual, $generations, []);

		switch($chart_style) {
			case self::CHART_STYLE_LIST:
			default:
				return $this->descendantsList($individual, $generations);
			case self::CHART_STYLE_BOOKLET:
				return $this->descendantsBooklet($individual, $generations);
			case self::CHART_STYLE_INDIVIDUALS:
				return $this->descendantsIndividuals($descendants);
			case self::CHART_STYLE_FAMILIES:
				return $this->descendantsFamilies($descendants);
		}
	}

	/**
	 * Show a hierarchical list of descendants
	 *
	 * @TODO replace ob_start() with views.
	 *
	 * @param Individual $individual
	 * @param int        $generations
	 *
	 * @return Response
	 */
	private function descendantsList(Individual $individual, int $generations): Response {
		ob_start();

		echo '<ul class="chart_common">';
		$this->printChildDescendancy($individual, $generations, $generations);
		echo '</ul>';

		$html = ob_get_clean();

		return new Response($html);

	}

	/**
	 * print a child descendancy
	 *
	 * @param Individual $person
	 * @param int        $depth the descendancy depth to show
	 * @param int        $generations
	 */
	private function printChildDescendancy(Individual $person, $depth, int $generations) {
		echo '<li>';
		echo '<table><tr><td>';
		if ($depth == $generations) {
			echo '<img src="' . Theme::theme()->parameter('image-spacer') . '" height="3" width="15"></td><td>';
		} else {
			echo '<img src="' . Theme::theme()->parameter('image-spacer') . '" height="3" width="3">';
			echo '<img src="' . Theme::theme()->parameter('image-hline') . '" height="3" width="', 12, '"></td><td>';
		}
		FunctionsPrint::printPedigreePerson($person);
		echo '</td>';

		// check if child has parents and add an arrow
		echo '<td></td>';
		echo '<td>';
		foreach ($person->getChildFamilies() as $cfamily) {
			foreach ($cfamily->getSpouses() as $parent) {
				echo FontAwesome::linkIcon('arrow-up', I18N::translate('Start at parents'), ['href' => route('descendants', ['ged' => $parent->getTree()->getName(), 'xref' => $parent->getXref(), 'generations' => $generations])]);
				// only show the arrow for one of the parents
				break;
			}
		}

		// d'Aboville child number
		$level = $generations - $depth;
		echo '<br><br>&nbsp;';
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
			echo '<span class="person_box' . $isf . '">&nbsp;' . $this->dabo_num[$i] . '&nbsp;</span>';
			if ($i < $level) {
				echo '.';
			}
		}
		echo '</span>';
		echo '</td></tr>';
		echo '</table>';
		echo '</li>';

		// loop for each spouse
		foreach ($person->getSpouseFamilies() as $family) {
			$this->printFamilyDescendancy($person, $family, $depth, $generations);
		}
	}

	/**
	 * print a family descendancy
	 *
	 * @param Individual $person
	 * @param Family     $family
	 * @param int        $depth the descendancy depth to show
	 * @param int        $generations
	 */
	private function printFamilyDescendancy(Individual $person, Family $family, int $depth, int $generations) {
		$uid = Uuid::uuid4()->toString(); // create a unique ID
		// print marriage info
		echo '<li>';
		echo '<img src="', Theme::theme()->parameter('image-spacer'), '" height="2" width="', 19, '">';
		echo '<span class="details1">';
		echo '<a href="#" onclick="expand_layer(\'' . $uid . '\'); return false;" class="top"><i id="' . $uid . '_img" class="icon-minus" title="' . I18N::translate('View this family') . '"></i></a>';
		if ($family->canShow()) {
			foreach ($family->getFacts(WT_EVENTS_MARR) as $fact) {
				echo ' <a href="', e($family->url()), '" class="details1">', $fact->summary(), '</a>';
			}
		}
		echo '</span>';

		// print spouse
		$spouse = $family->getSpouse($person);
		echo '<ul class="generations" id="' . $uid . '">';
		echo '<li>';
		echo '<table><tr><td>';
		FunctionsPrint::printPedigreePerson($spouse);
		echo '</td>';

		// check if spouse has parents and add an arrow
		echo '<td></td>';
		echo '<td>';
		if ($spouse) {
			foreach ($spouse->getChildFamilies() as $cfamily) {
				foreach ($cfamily->getSpouses() as $parent) {
					echo FontAwesome::linkIcon('arrow-up', I18N::translate('Start at parents'), ['href' => route('descendants', ['ged' => $parent->getTree()->getName(), 'xref' => $parent->getXref(), 'generations' => $generations])]);
					// only show the arrow for one of the parents
					break;
				}
			}
		}
		echo '<br><br>&nbsp;';
		echo '</td></tr>';

		// children
		$children = $family->getChildren();
		echo '<tr><td colspan="3" class="details1" >&nbsp;&nbsp;';
		if (!empty($children)) {
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
				$this->printChildDescendancy($child, $depth - 1, $generations);
			}
		}
		echo '</ul>';
		echo '</li>';
	}

	/**
	 * Show a tabular list of individual descendants.
	 *
	 * @param Individual[] $descendants
	 *
	 * @return Response
	 */
	private function descendantsIndividuals(array $descendants): Response {
		$html = FunctionsPrintLists::individualTable($descendants);

		return new Response($html);
	}

	/**
	 * Show a tabular list of individual descendants.
	 *
	 * @param Individual[] $descendants
	 *
	 * @return Response
	 */
	private function descendantsFamilies(array $descendants): Response {
		$families  = [];
		foreach ($descendants as $individual) {
			foreach ($individual->getChildFamilies() as $family) {
				$families[$family->getXref()] = $family;
			}
		}

		$html = FunctionsPrintLists::familyTable($families);

		return new Response($html);
	}

	/**
	 * Show a booklet view of descendants
	 *
	 * @TODO replace ob_start() with views.
	 *
	 * @param Individual $individual
	 * @param int        $generations
	 *
	 * @return Response
	 */
	private function descendantsBooklet(Individual $individual, int $generations): Response {
		ob_start();

		$this->printChildFamily($individual, $generations);

		$html = ob_get_clean();

		return new Response($html);
	}


	/**
	 * Print a child family
	 *
	 * @param Individual $person
	 * @param int        $depth the descendancy depth to show
	 * @param string     $label
	 * @param string     $gpid
	 */
	private function printChildFamily(Individual $person, $depth, $label = '1.', $gpid = '') {
		if ($depth < 2) {
			return;
		}
		foreach ($person->getSpouseFamilies() as $family) {
			FunctionsCharts::printSosaFamily($family->getXref(), '', -1, $label, $person->getXref(), $gpid, 0);
			$i = 1;
			foreach ($family->getChildren() as $child) {
				$this->printChildFamily($child, $depth - 1, $label . ($i++) . '.', $person->getXref());
			}
		}
	}

	/**
	 * This chart can display its output in a number of styles
	 *
	 * @return array
	 */
	private function chartStyles(): array {
		return [
			self::CHART_STYLE_LIST        => I18N::translate('List'),
			self::CHART_STYLE_BOOKLET     => I18N::translate('Booklet'),
			self::CHART_STYLE_INDIVIDUALS => I18N::translate('Individuals'),
			self::CHART_STYLE_FAMILIES    => I18N::translate('Families'),
		];
	}
}
