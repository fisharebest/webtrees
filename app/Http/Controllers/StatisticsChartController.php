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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Stats;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * A chart showing the various statistics about the family tree.
 */
class StatisticsChartController extends AbstractChartController {
	const MONTHS = [
		'JAN',
		'FEB',
		'MAR',
		'APR',
		'MAY',
		'JUN',
		'JUL',
		'AUG',
		'SEP',
		'OCT',
		'NOV',
		'DEC',
	];

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

		$this->checkModuleIsActive($tree, 'statistics_chart');

		$title = I18N::translate('Statistics');

		return $this->viewResponse('statistics-page', [
			'title' => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function chartIndividuals(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'statistics_chart');

		$html = view('statistics-chart-individuals', [
			'show_oldest_living' => Auth::check(),
			'stats'              => new Stats($tree),
		]);

		return new Response($html);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function chartFamilies(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'statistics_chart');

		$html = view('statistics-chart-families', [
			'stats' => new Stats($tree),
		]);

		return new Response($html);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function chartOther(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'statistics_chart');

		$html = view('statistics-chart-other', [
			'stats' => new Stats($tree),
		]);

		return new Response($html);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function chartCustomOptions(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'statistics_chart');

		$html = view('statistics-chart-custom', [
		]);

		return new Response($html);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function chartCustomChart(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'statistics_chart');

		// @TODO - convert to views and remove globals
		ob_start();
		global $legend, $xdata, $ydata, $xmax, $zmax, $z_boundaries;

		$x_axis       = (int) $request->get('x-as');
		$y_axis       = (int) $request->get('y-as');
		$z_axis       = (int) $request->get('z-as');
		$stats        = new Stats($tree);
		$z_boundaries = [];
		$legend       = [];

		switch ($x_axis) {
			case '1':
				return new Response($stats->chartDistribution([
					$request->get('chart_shows'),
					$request->get('chart_type'),
					$request->get('SURN'),
				]));

			case '2':
				return new Response($stats->chartDistribution([
					$request->get('chart_shows'),
					'birth_distribution_chart',
				]));

			case '3':
				return new Response($stats->chartDistribution([
					$request->get('chart_shows'),
					'death_distribution_chart',
				]));

			case '4':
				return new Response($stats->chartDistribution([
					$request->get('chart_shows'),
					'marriage_distribution_chart',
				]));

			case '11':
				$monthdata = [];
				for ($i = 0; $i < 12; ++$i) {
					$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
				}
				$xgiven            = true;
				$zgiven            = false;
				$title             = I18N::translate('Month of birth');
				$xtitle            = I18N::translate('Month');
				$ytitle            = I18N::translate('numbers');
				$boundaries_z_axis = $request->get('z-axis-boundaries-periods');
				$xdata             = $monthdata;
				$xmax              = 12;
				if ($z_axis !== 300 && $z_axis !== 301) {
					$this->calculateLegend($boundaries_z_axis);
				}
				$percentage = false;
				if ($y_axis === 201) {
					$percentage = false;
					$ytitle     = I18N::translate('Individuals');
				} elseif ($y_axis === 202) {
					$percentage = true;
					$ytitle     = I18N::translate('percentage');
				}
				$male_female = false;
				if ($z_axis === 300) {
					$zgiven          = false;
					$legend[0]       = 'all';
					$zmax            = 1;
					$z_boundaries[0] = 100000;
				} elseif ($z_axis === 301) {
					$male_female = true;
					$zgiven      = true;
					$legend[0]   = I18N::translate('Male');
					$legend[1]   = I18N::translate('Female');
					$zmax        = 2;
				}
				//-- reset the data array
				for ($i = 0; $i < $zmax; $i++) {
					for ($j = 0; $j < $xmax; $j++) {
						$ydata[$i][$j] = 0;
					}
				}
				$this->monthOfBirth($z_axis, $z_boundaries, $stats, $xgiven, $zgiven);

				return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $male_female, $percentage));

			case '12':
				$monthdata = [];
				for ($i = 0; $i < 12; ++$i) {
					$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
				}
				$xgiven            = true;
				$zgiven            = false;
				$title             = I18N::translate('Month of death');
				$xtitle            = I18N::translate('Month');
				$ytitle            = I18N::translate('numbers');
				$boundaries_z_axis = $request->get('z-axis-boundaries-periods');
				$xdata             = $monthdata;
				$xmax              = 12;
				if ($z_axis !== 300 && $z_axis !== 301) {
					$this->calculateLegend($boundaries_z_axis);
				}
				$percentage = false;
				if ($y_axis === 201) {
					$percentage = false;
					$ytitle     = I18N::translate('Individuals');
				} elseif ($y_axis === 202) {
					$percentage = true;
					$ytitle     = I18N::translate('percentage');
				}
				$male_female = false;
				if ($z_axis === 300) {
					$zgiven          = false;
					$legend[0]       = 'all';
					$zmax            = 1;
					$z_boundaries[0] = 100000;
				} elseif ($z_axis === 301) {
					$male_female = true;
					$zgiven      = true;
					$legend[0]   = I18N::translate('Male');
					$legend[1]   = I18N::translate('Female');
					$zmax        = 2;
				}
				//-- reset the data array
				for ($i = 0; $i < $zmax; $i++) {
					for ($j = 0; $j < $xmax; $j++) {
						$ydata[$i][$j] = 0;
					}
				}
				$this->monthOfDeath($z_axis, $z_boundaries, $stats, $xgiven, $zgiven);

				return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $male_female, $percentage));

			case '13':
				$monthdata = [];
				for ($i = 0; $i < 12; ++$i) {
					$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
				}

				if ($z_axis === 301) {
					$z_axis = 300;
				}
				$xgiven            = true;
				$zgiven            = false;
				$title             = I18N::translate('Month of marriage');
				$xtitle            = I18N::translate('Month');
				$ytitle            = I18N::translate('numbers');
				$boundaries_z_axis = $request->get('z-axis-boundaries-periods');
				$xdata             = $monthdata;
				$xmax              = 12;
				if ($z_axis !== 300 && $z_axis !== 301) {
					$this->calculateLegend($boundaries_z_axis);
				}
				$percentage = false;
				if ($y_axis === 201) {
					$percentage = false;
					$ytitle     = I18N::translate('Families');
				} elseif ($y_axis === 202) {
					$percentage = true;
					$ytitle     = I18N::translate('percentage');
				}
				$male_female = false;
				if ($z_axis === 300) {
					$zgiven          = false;
					$legend[0]       = 'all';
					$zmax            = 1;
					$z_boundaries[0] = 100000;
				} elseif ($z_axis === 301) {
					$male_female = true;
					$zgiven      = true;
					$legend[0]   = I18N::translate('Male');
					$legend[1]   = I18N::translate('Female');
					$zmax        = 2;
				}
				//-- reset the data array
				for ($i = 0; $i < $zmax; $i++) {
					for ($j = 0; $j < $xmax; $j++) {
						$ydata[$i][$j] = 0;
					}
				}
				$this->monthOfMarriage($z_axis, $z_boundaries, $stats, $xgiven, $zgiven);

				return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $male_female, $percentage));

			case '14':
				$monthdata = [];
				for ($i = 0; $i < 12; ++$i) {
					$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
				}
				$xgiven            = true;
				$zgiven            = false;
				$title             = I18N::translate('Month of birth of first child in a relation');
				$xtitle            = I18N::translate('Month');
				$ytitle            = I18N::translate('numbers');
				$boundaries_z_axis = $request->get('z-axis-boundaries-periods');
				$xdata             = $monthdata;
				$xmax              = 12;
				if ($z_axis !== 300 && $z_axis !== 301) {
					$this->calculateLegend($boundaries_z_axis);
				}
				$percentage = false;
				if ($y_axis === 201) {
					$percentage = false;
					$ytitle     = I18N::translate('Children');
				} elseif ($y_axis === 202) {
					$percentage = true;
					$ytitle     = I18N::translate('percentage');
				}
				$male_female = false;
				if ($z_axis === 300) {
					$zgiven          = false;
					$legend[0]       = 'all';
					$zmax            = 1;
					$z_boundaries[0] = 100000;
				} elseif ($z_axis === 301) {
					$male_female = true;
					$zgiven      = true;
					$legend[0]   = I18N::translate('Male');
					$legend[1]   = I18N::translate('Female');
					$zmax        = 2;
				}
				//-- reset the data array
				for ($i = 0; $i < $zmax; $i++) {
					for ($j = 0; $j < $xmax; $j++) {
						$ydata[$i][$j] = 0;
					}
				}
				$this->monthOfBirthOfFirstChild($z_axis, $z_boundaries, $stats, $xgiven, $zgiven);

				return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $male_female, $percentage));

			case '15':
				$monthdata = [];
				for ($i = 0; $i < 12; ++$i) {
					$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
				}

				if ($z_axis === 301) {
					$z_axis = 300;
				}
				$xgiven            = true;
				$zgiven            = false;
				$title             = I18N::translate('Month of first marriage');
				$xtitle            = I18N::translate('Month');
				$ytitle            = I18N::translate('numbers');
				$boundaries_z_axis = $request->get('z-axis-boundaries-periods');
				$xdata             = $monthdata;
				$xmax              = 12;
				if ($z_axis !== 300 && $z_axis !== 301) {
					$this->calculateLegend($boundaries_z_axis);
				}
				$percentage = false;
				if ($y_axis === 201) {
					$percentage = false;
					$ytitle     = I18N::translate('Families');
				} elseif ($y_axis === 202) {
					$percentage = true;
					$ytitle     = I18N::translate('percentage');
				}
				$male_female = false;
				if ($z_axis === 300) {
					$zgiven          = false;
					$legend[0]       = 'all';
					$zmax            = 1;
					$z_boundaries[0] = 100000;
				} elseif ($z_axis === 301) {
					$male_female = true;
					$zgiven      = true;
					$legend[0]   = I18N::translate('Male');
					$legend[1]   = I18N::translate('Female');
					$zmax        = 2;
				}
				//-- reset the data array
				for ($i = 0; $i < $zmax; $i++) {
					for ($j = 0; $j < $xmax; $j++) {
						$ydata[$i][$j] = 0;
					}
				}
				$this->monthOfFirstMarriage($z_axis, $z_boundaries, $stats, $xgiven, $zgiven);

				return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $male_female, $percentage));

			case '18':
				$xgiven            = false;
				$zgiven            = false;
				$title             = /* I18N: Two axes of a graph */
					I18N::translate('Longevity versus time');
				$xtitle            = I18N::translate('age');
				$ytitle            = I18N::translate('numbers');
				$boundaries_x_axis = $request->get('x-axis-boundaries-ages');
				$boundaries_z_axis = $request->get('z-axis-boundaries-periods');
				$this->calculateAxis($boundaries_x_axis);
				if ($z_axis !== 300 && $z_axis !== 301) {
					$this->calculateLegend($boundaries_z_axis);
				}
				$percentage = false;
				if ($y_axis === 201) {
					$percentage = false;
					$ytitle     = I18N::translate('Individuals');
				} elseif ($y_axis === 202) {
					$percentage = true;
					$ytitle     = I18N::translate('percentage');
				}
				$male_female = false;
				if ($z_axis === 300) {
					$zgiven          = false;
					$legend[0]       = 'all';
					$zmax            = 1;
					$z_boundaries[0] = 100000;
				} elseif ($z_axis === 301) {
					$male_female = true;
					$zgiven      = true;
					$legend[0]   = I18N::translate('Male');
					$legend[1]   = I18N::translate('Female');
					$zmax        = 2;
				}
				//-- reset the data array
				for ($i = 0; $i < $zmax; $i++) {
					for ($j = 0; $j < $xmax; $j++) {
						$ydata[$i][$j] = 0;
					}
				}
				$this->longevityVersusTime($z_axis, $z_boundaries, $stats, $xgiven, $zgiven);

				return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $male_female, $percentage));

			case '19':
				$xgiven            = false;
				$zgiven            = false;
				$title             = I18N::translate('Age in year of marriage');
				$xtitle            = I18N::translate('age');
				$ytitle            = I18N::translate('numbers');
				$boundaries_x_axis = $request->get('x-axis-boundaries-ages_m');
				$boundaries_z_axis = $request->get('z-axis-boundaries-periods');
				$this->calculateAxis($boundaries_x_axis);
				if ($z_axis !== 300 && $z_axis !== 301) {
					$this->calculateLegend($boundaries_z_axis);
				}
				$percentage = false;
				if ($y_axis === 201) {
					$percentage = false;
					$ytitle     = I18N::translate('Individuals');
				} elseif ($y_axis === 202) {
					$percentage = true;
					$ytitle     = I18N::translate('percentage');
				}
				$male_female     = false;
				$z_boundaries[0] = 100000;
				if ($z_axis === 300) {
					$zgiven    = false;
					$legend[0] = 'all';
					$zmax      = 1;
				} elseif ($z_axis === 301) {
					$male_female = true;
					$zgiven      = true;
					$legend[0]   = I18N::translate('Male');
					$legend[1]   = I18N::translate('Female');
					$zmax        = 2;
				}
				//-- reset the data array
				for ($i = 0; $i < $zmax; $i++) {
					for ($j = 0; $j < $xmax; $j++) {
						$ydata[$i][$j] = 0;
					}
				}
				$this->ageAtMarriage($z_axis, $z_boundaries, $stats, $xgiven, $zgiven);

				return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $male_female, $percentage));

			case '20':
				$xgiven            = false;
				$zgiven            = false;
				$title             = I18N::translate('Age in year of first marriage');
				$xtitle            = I18N::translate('age');
				$ytitle            = I18N::translate('numbers');
				$boundaries_x_axis = $request->get('x-axis-boundaries-ages_m');
				$boundaries_z_axis = $request->get('z-axis-boundaries-periods');
				$this->calculateAxis($boundaries_x_axis);
				if ($z_axis !== 300 && $z_axis !== 301) {
					$this->calculateLegend($boundaries_z_axis);
				}
				$percentage = false;
				if ($y_axis === 201) {
					$percentage = false;
					$ytitle     = I18N::translate('Individuals');
				} elseif ($y_axis === 202) {
					$percentage = true;
					$ytitle     = I18N::translate('percentage');
				}
				$male_female = false;
				if ($z_axis === 300) {
					$zgiven          = false;
					$legend[0]       = 'all';
					$zmax            = 1;
					$z_boundaries[0] = 100000;
				} elseif ($z_axis === 301) {
					$male_female = true;
					$zgiven      = true;
					$legend[0]   = I18N::translate('Male');
					$legend[1]   = I18N::translate('Female');
					$zmax        = 2;
				}
				//-- reset the data array
				for ($i = 0; $i < $zmax; $i++) {
					for ($j = 0; $j < $xmax; $j++) {
						$ydata[$i][$j] = 0;
					}
				}
				$this->ageAtFirstMarriage($z_axis, $z_boundaries, $stats, $xgiven, $zgiven);

				return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $male_female, $percentage));

			case '21':
				$xgiven            = false;
				$zgiven            = false;
				$title             = I18N::translate('Number of children');
				$xtitle            = I18N::translate('children');
				$ytitle            = I18N::translate('numbers');
				$boundaries_x_axis = $request->get('x-axis-boundaries-numbers');
				$boundaries_z_axis = $request->get('z-axis-boundaries-periods');
				$this->calculateAxis($boundaries_x_axis);
				if ($z_axis !== 300 && $z_axis !== 301) {
					$this->calculateLegend($boundaries_z_axis);
				}
				$percentage = false;
				if ($y_axis === 201) {
					$percentage = false;
					$ytitle     = I18N::translate('Families');
				} elseif ($y_axis === 202) {
					$percentage = true;
					$ytitle     = I18N::translate('percentage');
				}
				$male_female = false;
				if ($z_axis === 300) {
					$zgiven          = false;
					$legend[0]       = 'all';
					$zmax            = 1;
					$z_boundaries[0] = 100000;
				} elseif ($z_axis === 301) {
					$male_female = true;
					$zgiven      = true;
					$legend[0]   = I18N::translate('Male');
					$legend[1]   = I18N::translate('Female');
					$zmax        = 2;
				}
				//-- reset the data array
				for ($i = 0; $i < $zmax; $i++) {
					for ($j = 0; $j < $xmax; $j++) {
						$ydata[$i][$j] = 0;
					}
				}
				$this->numberOfChildren($z_axis, $z_boundaries, $stats, $xgiven, $zgiven);

				return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $male_female, $percentage));

			default:
				throw new NotFoundHttpException;
				break;
		}
	}

	/**
	 * Month of birth
	 *
	 * @param int   $z_axis
	 * @param int[] $z_boundaries
	 * @param Stats $stats
	 * @param bool  $xgiven
	 * @param bool  $zgiven
	 */
	private function monthOfBirth($z_axis, array $z_boundaries, Stats $stats, bool $xgiven, bool $zgiven) {
		if ($z_axis === 300) {
			$num = $stats->statsBirthQuery(false);
			foreach ($num as $values) {
				foreach (self::MONTHS as $key => $month) {
					if ($month === $values['d_month']) {
						$this->fillYData(0, $key, $values['total'], $xgiven, $zgiven);
					}
				}
			}
		} elseif ($z_axis === 301) {
			$num = $stats->statsBirthQuery(false, true);
			foreach ($num as $values) {
				foreach (self::MONTHS as $key => $month) {
					if ($month === $values['d_month']) {
						if ($values['i_sex'] === 'M') {
							$this->fillYData(0, $key, $values['total'], $xgiven, $zgiven);
						} elseif ($values['i_sex'] === 'F') {
							$this->fillYData(1, $key, $values['total'], $xgiven, $zgiven);
						}
					}
				}
			}
		} else {
			$zstart = 0;
			foreach ($z_boundaries as $boundary) {
				$num = $stats->statsBirthQuery(false, false, $zstart, $boundary);
				foreach ($num as $values) {
					foreach (self::MONTHS as $key => $month) {
						if ($month === $values['d_month']) {
							$this->fillYData($boundary, $key, $values['total'], $xgiven, $zgiven);
						}
					}
				}
				$zstart = $boundary + 1;
			}
		}
	}

	/**
	 * Month of birth of first child in a relation
	 *
	 * @param int   $z_axis
	 * @param int[] $z_boundaries
	 * @param Stats $stats
	 * @param bool  $xgiven
	 * @param bool  $zgiven
	 */
	private function monthOfBirthOfFirstChild($z_axis, array $z_boundaries, Stats $stats, bool $xgiven, bool $zgiven) {
		if ($z_axis === 300) {
			$num = $stats->monthFirstChildQuery(false);
			foreach ($num as $values) {
				foreach (self::MONTHS as $key => $month) {
					if ($month === $values['d_month']) {
						$this->fillYData(0, $key, $values['total'], $xgiven, $zgiven);
					}
				}
			}
		} elseif ($z_axis === 301) {
			$num = $stats->monthFirstChildQuery(false, true);
			foreach ($num as $values) {
				foreach (self::MONTHS as $key => $month) {
					if ($month === $values['d_month']) {
						if ($values['i_sex'] === 'M') {
							$this->fillYData(0, $key, $values['total'], $xgiven, $zgiven);
						} elseif ($values['i_sex'] === 'F') {
							$this->fillYData(1, $key, $values['total'], $xgiven, $zgiven);
						}
					}
				}
			}
		} else {
			$zstart = 0;
			foreach ($z_boundaries as $boundary) {
				$num = $stats->monthFirstChildQuery(false, false, $zstart, $boundary);
				foreach ($num as $values) {
					foreach (self::MONTHS as $key => $month) {
						if ($month === $values['d_month']) {
							$this->fillYData($boundary, $key, $values['total'], $xgiven, $zgiven);
						}
					}
				}
				$zstart = $boundary + 1;
			}
		}
	}

	/**
	 * Month of death
	 *
	 * @param int   $z_axis
	 * @param int[] $z_boundaries
	 * @param Stats $stats
	 * @param bool  $xgiven
	 * @param bool  $zgiven
	 */
	private function monthOfDeath($z_axis, array $z_boundaries, Stats $stats, bool $xgiven, bool $zgiven) {
		if ($z_axis === 300) {
			$num = $stats->statsDeathQuery(false);
			foreach ($num as $values) {
				foreach (self::MONTHS as $key => $month) {
					if ($month === $values['d_month']) {
						$this->fillYData(0, $key, $values['total'], $xgiven, $zgiven);
					}
				}
			}
		} elseif ($z_axis === 301) {
			$num = $stats->statsDeathQuery(false, true);
			foreach ($num as $values) {
				foreach (self::MONTHS as $key => $month) {
					if ($month === $values['d_month']) {
						if ($values['i_sex'] === 'M') {
							$this->fillYData(0, $key, $values['total'], $xgiven, $zgiven);
						} elseif ($values['i_sex'] === 'F') {
							$this->fillYData(1, $key, $values['total'], $xgiven, $zgiven);
						}
					}
				}
			}
		} else {
			$zstart = 0;
			foreach ($z_boundaries as $boundary) {
				$num = $stats->statsDeathQuery(false, false, $zstart, $boundary);
				foreach ($num as $values) {
					foreach (self::MONTHS as $key => $month) {
						if ($month === $values['d_month']) {
							$this->fillYData($boundary, $key, $values['total'], $xgiven, $zgiven);
						}
					}
				}
				$zstart = $boundary + 1;
			}
		}
	}

	/**
	 * Month of marriage
	 *
	 * @param int   $z_axis
	 * @param int[] $z_boundaries
	 * @param Stats $stats
	 * @param bool  $xgiven
	 * @param bool  $zgiven
	 */
	private function monthOfMarriage($z_axis, array $z_boundaries, Stats $stats, bool $xgiven, bool $zgiven) {
		if ($z_axis === 300) {
			$num = $stats->statsMarrQuery(false, false);
			foreach ($num as $values) {
				foreach (self::MONTHS as $key => $month) {
					if ($month === $values['d_month']) {
						$this->fillYData(0, $key, $values['total'], $xgiven, $zgiven);
					}
				}
			}
		} else {
			$zstart = 0;
			foreach ($z_boundaries as $boundary) {
				$num = $stats->statsMarrQuery(false, false, $zstart, $boundary);
				foreach ($num as $values) {
					foreach (self::MONTHS as $key => $month) {
						if ($month === $values['d_month']) {
							$this->fillYData($boundary, $key, $values['total'], $xgiven, $zgiven);
						}
					}
				}
				$zstart = $boundary + 1;
			}
		}
	}

	/**
	 * Month of first marriage
	 *
	 * @param int   $z_axis
	 * @param int[] $z_boundaries
	 * @param Stats $stats
	 * @param bool  $xgiven
	 * @param bool  $zgiven
	 */
	private function monthOfFirstMarriage($z_axis, array $z_boundaries, Stats $stats, bool $xgiven, bool $zgiven) {
		if ($z_axis === 300) {
			$num  = $stats->statsMarrQuery(false, true);
			$indi = [];
			$fam  = [];
			foreach ($num as $values) {
				if (!in_array($values['indi'], $indi) && !in_array($values['fams'], $fam)) {
					foreach (self::MONTHS as $key => $month) {
						if ($month === $values['month']) {
							$this->fillYData(0, $key, 1, $xgiven, $zgiven);
						}
					}
					$indi[] = $values['indi'];
					$fam[]  = $values['fams'];
				}
			}
		} else {
			$zstart = 0;
			$indi   = [];
			$fam    = [];
			foreach ($z_boundaries as $boundary) {
				$num = $stats->statsMarrQuery(false, true, $zstart, $boundary);
				foreach ($num as $values) {
					if (!in_array($values['indi'], $indi) && !in_array($values['fams'], $fam)) {
						foreach (self::MONTHS as $key => $month) {
							if ($month === $values['month']) {
								$this->fillYData($boundary, $key, 1, $xgiven, $zgiven);
							}
						}
						$indi[] = $values['indi'];
						$fam[]  = $values['fams'];
					}
				}
				$zstart = $boundary + 1;
			}
		}
	}

	/**
	 * Longevity versus time
	 *
	 * @param int   $z_axis
	 * @param int[] $z_boundaries
	 * @param Stats $stats
	 * @param bool  $xgiven
	 * @param bool  $zgiven
	 */
	private function longevityVersusTime($z_axis, array $z_boundaries, Stats $stats, bool $xgiven, bool $zgiven) {
		if ($z_axis === 300) {
			$num = $stats->statsAgeQuery(false, 'DEAT');
			foreach ($num as $values) {
				foreach ($values as $age_value) {
					$this->fillYData(0, (int) ($age_value / 365.25), 1, $xgiven, $zgiven);
				}
			}
		} elseif ($z_axis === 301) {
			$num = $stats->statsAgeQuery(false, 'DEAT', 'M');
			foreach ($num as $values) {
				foreach ($values as $age_value) {
					$this->fillYData(0, (int) ($age_value / 365.25), 1, $xgiven, $zgiven);
				}
			}
			$num = $stats->statsAgeQuery(false, 'DEAT', 'F');
			foreach ($num as $values) {
				foreach ($values as $age_value) {
					$this->fillYData(1, (int) ($age_value / 365.25), 1, $xgiven, $zgiven);
				}
			}
		} else {
			$zstart = 0;
			foreach ($z_boundaries as $boundary) {
				$num = $stats->statsAgeQuery(false, 'DEAT', 'BOTH', $zstart, $boundary);
				foreach ($num as $values) {
					foreach ($values as $age_value) {
						$this->fillYData($boundary, (int) ($age_value / 365.25), 1, $xgiven, $zgiven);
					}
				}
				$zstart = $boundary + 1;
			}
		}
	}

	/**
	 * Age in year of marriage
	 *
	 * @param int   $z_axis
	 * @param int[] $z_boundaries
	 * @param Stats $stats
	 * @param bool  $xgiven
	 * @param bool  $zgiven
	 */
	private function ageAtMarriage($z_axis, array $z_boundaries, Stats $stats, bool $xgiven, bool $zgiven) {
		if ($z_axis === 300) {
			$num = $stats->statsMarrAgeQuery(false, 'M');
			foreach ($num as $values) {
				$this->fillYData(0, (int) ($values['age'] / 365.25), 1, $xgiven, $zgiven);
			}
			$num = $stats->statsMarrAgeQuery(false, 'F');
			foreach ($num as $values) {
				$this->fillYData(0, (int) ($values['age'] / 365.25), 1, $xgiven, $zgiven);
			}
		} elseif ($z_axis === 301) {
			$num = $stats->statsMarrAgeQuery(false, 'M');
			foreach ($num as $values) {
				$this->fillYData(0, (int) ($values['age'] / 365.25), 1, $xgiven, $zgiven);
			}
			$num = $stats->statsMarrAgeQuery(false, 'F');
			foreach ($num as $values) {
				$this->fillYData(1, (int) ($values['age'] / 365.25), 1, $xgiven, $zgiven);
			}
		} else {
			$zstart = 0;
			foreach ($z_boundaries as $boundary) {
				$num = $stats->statsMarrAgeQuery(false, 'M', $zstart, $boundary);
				foreach ($num as $values) {
					$this->fillYData($boundary, (int) ($values['age'] / 365.25), 1, $xgiven, $zgiven);
				}
				$num = $stats->statsMarrAgeQuery(false, 'F', $zstart, $boundary);
				foreach ($num as $values) {
					$this->fillYData($boundary, (int) ($values['age'] / 365.25), 1, $xgiven, $zgiven);
				}
				$zstart = $boundary + 1;
			}
		}
	}

	/**
	 * Age in year of first marriage
	 *
	 * @param int   $z_axis
	 * @param int[] $z_boundaries
	 * @param Stats $stats
	 * @param bool  $xgiven
	 * @param bool  $zgiven
	 */
	private function ageAtFirstMarriage($z_axis, array $z_boundaries, Stats $stats, bool $xgiven, bool $zgiven) {
		if ($z_axis === 300) {
			$num  = $stats->statsMarrAgeQuery(false, 'M');
			$indi = [];
			foreach ($num as $values) {
				if (!in_array($values['d_gid'], $indi)) {
					$this->fillYData(0, (int) ($values['age'] / 365.25), 1, $xgiven, $zgiven);
					$indi[] = $values['d_gid'];
				}
			}
			$num  = $stats->statsMarrAgeQuery(false, 'F');
			$indi = [];
			foreach ($num as $values) {
				if (!in_array($values['d_gid'], $indi)) {
					$this->fillYData(0, (int) ($values['age'] / 365.25), 1, $xgiven, $zgiven);
					$indi[] = $values['d_gid'];
				}
			}
		} elseif ($z_axis === 301) {
			$num  = $stats->statsMarrAgeQuery(false, 'M');
			$indi = [];
			foreach ($num as $values) {
				if (!in_array($values['d_gid'], $indi)) {
					$this->fillYData(0, (int) ($values['age'] / 365.25), 1, $xgiven, $zgiven);
					$indi[] = $values['d_gid'];
				}
			}
			$num  = $stats->statsMarrAgeQuery(false, 'F');
			$indi = [];
			foreach ($num as $values) {
				if (!in_array($values['d_gid'], $indi)) {
					$this->fillYData(1, (int) ($values['age'] / 365.25), 1, $xgiven, $zgiven);
					$indi[] = $values['d_gid'];
				}
			}
		} else {
			$zstart = 0;
			$indi   = [];
			foreach ($z_boundaries as $boundary) {
				$num = $stats->statsMarrAgeQuery(false, 'M', $zstart, $boundary);
				foreach ($num as $values) {
					if (!in_array($values['d_gid'], $indi)) {
						$this->fillYData($boundary, (int) ($values['age'] / 365.25), 1, $xgiven, $zgiven);
						$indi[] = $values['d_gid'];
					}
				}
				$num = $stats->statsMarrAgeQuery(false, 'F', $zstart, $boundary);
				foreach ($num as $values) {
					if (!in_array($values['d_gid'], $indi)) {
						$this->fillYData($boundary, (int) ($values['age'] / 365.25), 1, $xgiven, $zgiven);
						$indi[] = $values['d_gid'];
					}
				}
				$zstart = $boundary + 1;
			}
		}
	}

	/**
	 * Number of children
	 *
	 * @param int   $z_axis
	 * @param int[] $z_boundaries
	 * @param Stats $stats
	 * @param bool  $xgiven
	 * @param bool  $zgiven
	 */
	private function numberOfChildren($z_axis, array $z_boundaries, Stats $stats, bool $xgiven, bool $zgiven) {
		if ($z_axis === 300) {
			$num = $stats->statsChildrenQuery(false);
			foreach ($num as $values) {
				$this->fillYData(0, $values['f_numchil'], $values['total'], $xgiven, $zgiven);
			}
		} elseif ($z_axis === 301) {
			$num = $stats->statsChildrenQuery(false, 'M');
			foreach ($num as $values) {
				$this->fillYData(0, $values['num'], $values['total'], $xgiven, $zgiven);
			}
			$num = $stats->statsChildrenQuery(false, 'F');
			foreach ($num as $values) {
				$this->fillYData(1, $values['num'], $values['total'], $xgiven, $zgiven);
			}
		} else {
			$zstart = 0;
			foreach ($z_boundaries as $boundary) {
				$num = $stats->statsChildrenQuery(false, 'BOTH', $zstart, $boundary);
				foreach ($num as $values) {
					$this->fillYData($boundary, $values['f_numchil'], $values['total'], $xgiven, $zgiven);
				}
				$zstart = $boundary + 1;
			}
		}
	}

	/**
	 * Calculate the Y axis.
	 *
	 * @param int  $z
	 * @param int  $x
	 * @param int  $val
	 * @param bool $xgiven
	 * @param bool $zgiven
	 */
	private function fillYData($z, $x, $val, bool $xgiven, bool $zgiven) {
		global $ydata, $xmax, $x_boundaries, $zmax, $z_boundaries;
		//-- calculate index $i out of given z value
		//-- calculate index $j out of given x value
		if ($xgiven) {
			$j = $x;
		} else {
			$j = 0;
			while (($x > $x_boundaries[$j]) && ($j < $xmax)) {
				$j++;
			}
		}
		if ($zgiven) {
			$i = $z;
		} else {
			$i = 0;
			while (($z > $z_boundaries[$i]) && ($i < $zmax)) {
				$i++;
			}
		}
		if (isset($ydata[$i][$j])) {
			$ydata[$i][$j] += $val;
		} else {
			$ydata[$i][$j] = $val;
		}
	}

	/**
	 * Plot the data.
	 *
	 * @param string   $chart_title
	 * @param int[][]  $xdata
	 * @param string   $xtitle
	 * @param int[][]  $ydata
	 * @param string   $ytitle
	 * @param string[] $legend
	 * @param bool     $male_female
	 * @param bool     $percentage
	 *
	 * @return string
	 */
	private function myPlot(string $chart_title, array $xdata, string $xtitle, array $ydata, string $ytitle, array $legend, bool $male_female, bool $percentage): string {

		// Google Chart API only allows text encoding for numbers less than 100
		// and it does not allow adjusting the y-axis range, so we must find the maximum y-value
		// in order to adjust beforehand by changing the numbers

		if ($male_female) {
			$stop = 2;
		} else {
			$stop = count($ydata);
		}
		if ($percentage) {
			$ypercentmax = 0;
			$yt          = [];
			for ($i = 0; $i < $stop; $i++) {
				if (isset($ydata[$i])) {
					$ymax   = max($ydata[$i]);
					$yt[$i] = array_sum($ydata[$i]);
					if ($yt[$i] > 0) {
						$ypercent    = round($ymax / $yt[$i] * 100, 1);
						$ypercentmax = max($ypercentmax, $ypercent);
					}
				}
			}
			$ymax = $ypercentmax;
			if ($ymax > 0) {
				$scalefactor = 100.0 / $ymax;
			} else {
				$scalefactor = 0;
			}
			$datastring = 'chd=t:';
			for ($i = 0; $i < $stop; $i++) {
				if (isset($ydata[$i])) {
					foreach ($ydata[$i] as $j => $data) {
						if ($j > 0) {
							$datastring .= ',';
						}
						if ($yt[$i] > 0) {
							$datastring .= round($data / $yt[$i] * 100 * $scalefactor, 1);
						} else {
							$datastring .= '0';
						}
					}
					if ($i !== $stop - 1) {
						$datastring .= '|';
					}
				}
			}
		} else {
			$ymax = 0;
			for ($i = 0; $i < $stop; $i++) {
				$ymax = max($ymax, max($ydata[$i]));
			}
			if ($ymax > 0) {
				$scalefactor = 100.0 / $ymax;
			} else {
				$scalefactor = 0;
			}
			$datastring = 'chd=t:';
			for ($i = 0; $i < $stop; $i++) {
				foreach ($ydata[$i] as $j => $data) {
					if ($j > 0) {
						$datastring .= ',';
					}
					$datastring .= round($data * $scalefactor, 1);
				}
				if ($i !== $stop - 1) {
					$datastring .= '|';
				}
			}
		}
		$colors      = [
			'0000FF',
			'FFA0CB',
			'9F00FF',
			'FF7000',
			'905030',
			'FF0000',
			'00FF00',
			'F0F000',
		];
		$colorstring = 'chco=';
		for ($i = 0; $i < $stop; $i++) {
			if (isset($colors[$i])) {
				$colorstring .= $colors[$i];
				if ($i !== ($stop - 1)) {
					$colorstring .= ',';
				}
			}
		}

		$imgurl = 'https://chart.googleapis.com/chart?cht=bvg&amp;chs=950x300&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt=' . rawurlencode($chart_title) . '&amp;' . $datastring . '&amp;' . $colorstring . '&amp;chbh=';
		if (count($ydata) > 3) {
			$imgurl .= '5,1';
		} elseif (count($ydata) < 2) {
			$imgurl .= '45,1';
		} else {
			$imgurl .= '20,3';
		}
		$imgurl .= '&amp;chxt=x,x,y,y&amp;chxl=0:|';
		foreach ($xdata as $data) {
			$imgurl .= rawurlencode($data) . '|';
		}

		$imgurl .= '1:||||' . rawurlencode($xtitle) . '|2:|';
		$imgurl .= '0|';
		if ($percentage) {
			for ($i = 1; $i < 11; $i++) {
				if ($ymax < 11) {
					$imgurl .= round($ymax * $i / 10, 1) . '|';
				} else {
					$imgurl .= round($ymax * $i / 10, 0) . '|';
				}
			}
			$imgurl .= '3:||%|';
		} else {
			if ($ymax < 11) {
				for ($i = 1; $i < $ymax + 1; $i++) {
					$imgurl .= round($ymax * $i / ($ymax), 0) . '|';
				}
			} else {
				for ($i = 1; $i < 11; $i++) {
					$imgurl .= round($ymax * $i / 10, 0) . '|';
				}
			}
			$imgurl .= '3:||' . rawurlencode($ytitle) . '|';
		}
		// Only show legend if y-data is non-2-dimensional
		if (count($ydata) > 1) {
			$imgurl .= '&amp;chdl=';
			foreach ($legend as $i => $data) {
				if ($i > 0) {
					$imgurl .= '|';
				}
				$imgurl .= rawurlencode($data);
			}
		}

		return '<img src="' . $imgurl . '" width="950" height="300" alt="' . e($chart_title) . '">';
	}

	/**
	 * Create the X axis.
	 *
	 * @param string $x_axis_boundaries
	 */
	private function calculateAxis($x_axis_boundaries) {
		global $x_axis, $xdata, $xmax, $x_boundaries;

		// Calculate xdata and zdata elements out of chart values
		$hulpar = explode(',', $x_axis_boundaries);
		$i      = 1;
		if ($x_axis === 21 && $hulpar[0] == 1) {
			$xdata[0] = 0;
		} else {
			$xdata[0] = $this->formatRangeOfNumbers(0, $hulpar[0]);
		}
		$x_boundaries[0] = $hulpar[0] - 1;
		while (isset($hulpar[$i])) {
			$i1 = $i - 1;
			if (($hulpar[$i] - $hulpar[$i1]) === 1) {
				$xdata[$i]        = $hulpar[$i1];
				$x_boundaries[$i] = $hulpar[$i1];
			} elseif ($hulpar[$i1] === $hulpar[0]) {
				$xdata[$i]        = $this->formatRangeOfNumbers($hulpar[$i1], $hulpar[$i]);
				$x_boundaries[$i] = $hulpar[$i];
			} else {
				$xdata[$i]        = $this->formatRangeOfNumbers($hulpar[$i1] + 1, $hulpar[$i]);
				$x_boundaries[$i] = $hulpar[$i];
			}
			$i++;
		}
		$xdata[$i]        = $hulpar[$i - 1];
		$x_boundaries[$i] = $hulpar[$i - 1];
		if ($hulpar[$i - 1] === $i) {
			$xmax = $i + 1;
		} else {
			$xmax = $i;
		}
		$xdata[$xmax]        = /* I18N: Label on a graph; 40+ means 40 or more */
			I18N::translate('%s+', I18N::number($hulpar[$i - 1]));
		$x_boundaries[$xmax] = 10000;
		$xmax                = $xmax + 1;
		if ($xmax > 20) {
			$xmax = 20;
		}
	}

	/**
	 * A range of integers.
	 *
	 * @param int $x
	 * @param int $y
	 *
	 * @return string
	 */
	private function formatRangeOfNumbers($x, $y): string {
		return /* I18N: A range of numbers */
			I18N::translate(
				'%1$s–%2$s',
				I18N::number($x),
				I18N::number($y)
			);
	}

	/**
	 * Calculate the Z axis.
	 *
	 * @param string $boundaries_z_axis
	 */
	private function calculateLegend($boundaries_z_axis) {
		global $legend, $zmax, $z_boundaries;

		// calculate the legend values
		$hulpar          = explode(',', $boundaries_z_axis);
		$i               = 1;
		$date            = new Date('BEF ' . $hulpar[0]);
		$legend[0]       = strip_tags($date->display());
		$z_boundaries[0] = $hulpar[0] - 1;
		while (isset($hulpar[$i])) {
			$i1               = $i - 1;
			$date             = new Date('BET ' . $hulpar[$i1] . ' AND ' . ($hulpar[$i] - 1));
			$legend[$i]       = strip_tags($date->display());
			$z_boundaries[$i] = $hulpar[$i] - 1;
			$i++;
		}
		$zmax                = $i;
		$zmax1               = $zmax - 1;
		$date                = new Date('AFT ' . $hulpar[$zmax1]);
		$legend[$zmax]       = strip_tags($date->display());
		$z_boundaries[$zmax] = 10000;
		$zmax                = $zmax + 1;
		if ($zmax > 8) {
			$zmax = 8;
		}
	}
}
