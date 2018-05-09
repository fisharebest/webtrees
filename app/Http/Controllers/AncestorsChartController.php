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

use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\FunctionsCharts;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A chart of direct-line ancestors.
 */
class AncestorsChartController extends AbstractChartController {
	// Chart styles
	const CHART_STYLE_LIST        = 0;
	const CHART_STYLE_BOOKLET     = 1;
	const CHART_STYLE_INDIVIDUALS = 2;
	const CHART_STYLE_FAMILIES    = 3;

	// Defaults
	const DEFAULT_COUSINS             = false;
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

		$this->checkModuleIsActive($tree, 'ancestors_chart');

		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual);

		$minimum_generations = 2;
		$maximum_generations = (int) $tree->getPreference('MAX_PEDIGREE_GENERATIONS', self::DEFAULT_MAXIMUM_GENERATIONS);
		$default_generations = (int) $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS', self::DEFAULT_GENERATIONS);

		$show_cousins = (bool) $request->get('show_cousins', self::DEFAULT_COUSINS);
		$chart_style  = (int) $request->get('chart_style', self::DEFAULT_STYLE);
		$generations  = (int) $request->get('generations', $default_generations);

		$generations = min($generations, $maximum_generations);
		$generations = max($generations, $minimum_generations);

		if ($individual !== null && $individual->canShowName()) {
			$title = /* I18N: %s is an individual’s name */ I18N::translate('Ancestors of %s', $individual->getFullName());
		} else {
			$title = I18N::translate('Ancestors');
		}

		return $this->viewResponse('ancestors-page', [
			'chart_style'         => $chart_style,
			'chart_styles'        => $this->chartStyles(),
			'default_generations' => $default_generations,
			'generations'         => $generations,
			'individual'          => $individual,
			'maximum_generations' => $maximum_generations,
			'minimum_generations' => $minimum_generations,
			'show_cousins'        => $show_cousins,
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

		$this->checkModuleIsActive($tree, 'ancestors_chart');

		$xref         = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual);

		$minimum_generations = 2;
		$maximum_generations = (int) $tree->getPreference('MAX_PEDIGREE_GENERATIONS', self::DEFAULT_MAXIMUM_GENERATIONS);
		$default_generations = (int) $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS', self::DEFAULT_GENERATIONS);

		$show_cousins = (bool) $request->get('show_cousins', self::DEFAULT_COUSINS);
		$chart_style  = (int) $request->get('chart_style', self::DEFAULT_STYLE);
		$generations  = (int) $request->get('generations', $default_generations);

		$generations = min($generations, $maximum_generations);
		$generations = max($generations, $minimum_generations);

		$ancestors = $this->sosaStradonitzAncestors($individual, $generations);

		switch($chart_style) {
			case self::CHART_STYLE_LIST:
			default:
				return $this->ancestorsList($individual, $generations);
			case self::CHART_STYLE_BOOKLET:
				return $this->ancestorsBooklet($ancestors, $show_cousins);
			case self::CHART_STYLE_INDIVIDUALS:
				return $this->ancestorsIndividuals($ancestors);
			case self::CHART_STYLE_FAMILIES:
				return $this->ancestorsFamilies($ancestors);
		}
	}

	/**
	 * Show a hierarchical list of ancestors
	 *
	 * @TODO replace ob_start() with views.
	 *
	 * @param Individual $individual
	 * @param int        $generations
	 *
	 * @return Response
	 */
	private function ancestorsList(Individual $individual, int $generations): Response {
		ob_start();

		echo
		'<ul class="chart_common">' .
		$this->printChildAscendancy($individual, 1, $generations - 1) .
		'</ul>';

		$html = ob_get_clean();

		return new Response($html);
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
			echo FontAwesome::linkIcon('arrow-down', I18N::translate('Ancestors of %s', $individual->getFullName()), ['href' => route('ancestors', ['xref' => $individual->getXref(), 'ged' => $individual->getTree()->getName(), 'generations' => $generations, 'chart_style' => self::CHART_STYLE_LIST])]);
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
					echo ' <a href="', e($family->url()), '" class="details1">', $fact->summary(), '</a>';
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
	 * Show a tabular list of individual ancestors.
	 *
	 * @param Individual[] $ancestors
	 *
	 * @return Response
	 */
	private function ancestorsIndividuals(array $ancestors): Response {
		$ancestors = array_filter($ancestors);
		$html      = FunctionsPrintLists::individualTable($ancestors, 'sosa');

		return new Response($html);
	}

	/**
	 * Show a tabular list of individual ancestors.
	 *
	 * @param Individual[] $ancestors
	 *
	 * @return Response
	 */
	private function ancestorsFamilies(array $ancestors): Response {
		$ancestors = array_filter($ancestors);
		$families  = [];
		foreach ($ancestors as $individual) {
			foreach ($individual->getChildFamilies() as $family) {
				$families[$family->getXref()] = $family;
			}
		}

		$html =  FunctionsPrintLists::familyTable($families);

		return new Response($html);
	}

	/**
	 * Show a booklet view of ancestors
	 *
	 * @TODO replace ob_start() with views.
	 *
	 * @param Individual[] $ancestors
	 * @param bool         $show_cousins
	 *
	 * @return Response
	 */
	private function ancestorsBooklet(array $ancestors, bool $show_cousins): Response {
		$ancestors = array_filter($ancestors);

		ob_start();

		FunctionsPrint::printPedigreePerson($ancestors[1]);
		foreach ($ancestors as $sosa => $individual) {
			foreach ($individual->getChildFamilies() as $family) {
				FunctionsCharts::printSosaFamily($family->getXref(), $individual->getXref(), $sosa, '', '', '', $show_cousins);
			}
		}

		$html = ob_get_clean();

		return new Response($html);
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
