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

use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\Webtrees\ColorGenerator;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Tree;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A chart showing the lifespans of individuals.
 */
class LifespansChartController extends AbstractChartController {
	// Parameters for generating colors
	const RANGE      = 120; // degrees
	const SATURATION = 100; // percent
	const LIGHTNESS  = 30; // percent
	const ALPHA      = 0.25;

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

		$this->checkModuleIsActive($tree, 'lifespans_chart');

		$xrefs     = (array) $request->get('xrefs', []);
		$addxref   = $request->get('addxref', '');
		$addfam    = (bool) $request->get('addfam', false);
		$placename = $request->get('placename', '');
		$start     = $request->get('start', '');
		$end       = $request->get('end', '');

		$place      = new Place($placename, $tree);
		$start_date = new Date($start);
		$end_date   = new Date($end);

		// Add an individual, and family members
		$individual = Individual::getInstance($addxref, $tree);
		if ($individual !== null) {
			$xrefs[] = $addxref;
			if ($addfam) {
				$xrefs = array_merge($xrefs,  $this->closeFamily($individual));
			}
		}

		// Select by date and/or place.
		if ($start_date->isOK() && $end_date->isOK() && $placename !== '') {
			$date_xrefs  = $this->findIndividualsByDate($start_date, $end_date, $tree);
			$place_xrefs = $this->findIndividualsByPlace($place, $tree);
			$xrefs       = array_intersect($date_xrefs, $place_xrefs);
		} elseif ($start_date->isOK() && $end_date->isOK()) {
			$xrefs = $this->findIndividualsByDate($start_date, $end_date, $tree);
		} elseif ($placename !== '') {
			$xrefs = $this->findIndividualsByPlace($place, $tree);
		}

		// Filter duplicates and private individuals.
		$xrefs = array_unique($xrefs);
		$xrefs = array_filter($xrefs, function (string $xref) use ($tree) {
			$individual = Individual::getInstance($xref, $tree);

			return $individual !== null && $individual->canShow();
		});

		$title    = I18N::translate('Lifespans');
		$subtitle = $this->subtitle(count($xrefs), $start_date, $end_date, $placename);

		return $this->viewResponse('lifespans-page', [
			'xrefs'    => $xrefs,
			'subtitle' => $subtitle,
			'title'    => $title,
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

		$this->checkModuleIsActive($tree, 'lifespans_chart');

		$xrefs = (array) $request->get('xrefs', []);
		$xrefs = array_unique($xrefs);

		/** @var Individual[] $individuals */
		$individuals = array_map(function (string $xref) use ($tree) {
			return Individual::getInstance($xref, $tree);
		}, $xrefs);

		$individuals = array_filter($individuals, function (Individual $individual = null) {
			return $individual !== null && $individual->canShow();
		});

		// Sort the array in order of birth year
		usort($individuals, function (Individual $a, Individual $b) {
			return Date::compare($a->getEstimatedBirthDate(), $b->getEstimatedBirthDate());
		});

		$subtitle = $request->get('subtitle');

		// Round to whole decades
		$start_year = (int) floor($this->minYear($individuals) / 10) * 10;
		$end_year   = (int) ceil($this->maxYear($individuals) / 10) * 10;

		$lifespans = $this->layoutIndividuals($individuals);

		$max_rows = array_reduce($lifespans, function ($carry, stdClass $item) {
			return max($carry, $item->row);
		}, 0);

		$html = view('lifespans-chart', [
			'dir'        => I18N::direction(),
			'end_year'   => $end_year,
			'lifespans'  => $lifespans,
			'max_rows'   => $max_rows,
			'start_year' => $start_year,
			'subtitle'   => $subtitle,
		]);

		return new Response($html);
	}

	/**
	 *
	 *
	 * @param Individual[] $individuals
	 *
	 * @return stdClass[]
	 */
	private function layoutIndividuals(array $individuals): array {
		$colors = [
			'M' => new ColorGenerator(240, self::SATURATION, self::LIGHTNESS, self::ALPHA, self::RANGE * -1),
			'F' => new ColorGenerator(000, self::SATURATION, self::LIGHTNESS, self::ALPHA, self::RANGE),
			'U' => new ColorGenerator(120, self::SATURATION, self::LIGHTNESS, self::ALPHA, self::RANGE),
		];

		$current_year = (int) date('Y');

		// Latest year used in each row
		$rows = [];

		$lifespans = [];

		foreach ($individuals as $individual) {
			$birth_jd   = $individual->getEstimatedBirthDate()->minimumJulianDay();
			$birth_year = $this->jdToYear($birth_jd);
			$death_jd   = $individual->getEstimatedDeathDate()->maximumJulianDay();
			$death_year = $this->jdToYear($death_jd);

			// Don't show death dates in the future.
			$death_year = min($death_year, $current_year);

			// Add this individual to the next row in the chart...
			$next_row = count($rows);
			// ...unless we can find an existing row where it fits.
			foreach ($rows as $row => $year) {
				if ($year < $birth_year) {
					$next_row = $row;
					break;
				}
			}

			// Fill the row up to the year (leaving a small gap)
			$rows[$next_row] = $death_year;

			$lifespans[] = (object) [
				'background' => $colors[$individual->getSex()]->getNextColor(),
				'birth_year' => $birth_year,
				'death_year' => $death_year,
				'id'         => 'individual-' . md5($individual->getXref()),
				'individual' => $individual,
				'row'        => $next_row,
			];
		}

		return $lifespans;
	}

	/**
	 * Find the latest event year for individuals
	 *
	 * @param array $individuals
	 *
	 * @return int
	 */
	private function maxYear(array $individuals): int {
		$jd = array_reduce($individuals, function ($carry, Individual $item) {
			return max($carry, $item->getEstimatedDeathDate()->maximumJulianDay());
		}, 0);

		$year = $this->jdToYear($jd);

		// Don't show future dates
		return min($year, (int) date('Y'));

	}

	/**
	 * Find the earliest event year for individuals
	 *
	 * @param array $individuals
	 *
	 * @return int
	 */
	private function minYear(array $individuals): int {
		$jd = array_reduce($individuals, function ($carry, Individual $item) {
			return min($carry, $item->getEstimatedBirthDate()->minimumJulianDay());
		}, PHP_INT_MAX);

		return $this->jdToYear($jd);
	}

	/**
	 * Convert a julian day to a gregorian year
	 *
	 * @param int $jd
	 *
	 * @return int
	 */
	private function jdToYear(int $jd): int {
		if ($jd === 0) {
			return 0;
		} else {
			$gregorian = new GregorianCalendar();
			list($y) = $gregorian->jdToYmd($jd);
		}

		return $y;
	}

	/**
	 * @param Date $start
	 * @param Date $end
	 * @param Tree $tree
	 *
	 * @return string[]
	 */
	private function findIndividualsByDate(Date $start, Date $end, Tree $tree): array {
		return Database::prepare(
			"SELECT d_gid" .
			" FROM `##dates`" .
			" WHERE d_file = :tree_id" .
			" AND d_julianday1 <= :max_jd" .
			" AND d_julianday2 >= :min_jd" .
			" AND d_fact NOT IN ('BAPL', 'ENDL', 'SLGC', 'SLGS', '_TODO', 'CHAN')"
		)->execute([
			'tree_id' => $tree->getTreeId(),
			'max_jd'  => $end->maximumJulianDay(),
			'min_jd'  => $start->minimumJulianDay(),
		])->fetchOneColumn();
	}

	/**
	 * @param Place $place
	 * @param Tree  $tree
	 *
	 * @return string[]
	 */
	private function findIndividualsByPlace(Place $place, Tree $tree): array {
		return Database::prepare(
			"SELECT DISTINCT `i_id` FROM `##placelinks`" .
			" JOIN `##individuals` ON `pl_gid`=`i_id` AND `pl_file`=`i_file`" .
			" WHERE `i_file`=:tree_id" .
			" AND `pl_p_id`=:place_id" .
			" UNION" .
			" SELECT DISTINCT `f_id` FROM `##placelinks`" .
			" JOIN `##families` ON `pl_gid`=`f_id` AND `pl_file`=`f_file`" .
			" WHERE `f_file`=:tree_id" .
			" AND `pl_p_id`=:place_id"
		)->execute([
			'tree_id'  => $tree->getTreeId(),
			'place_id' => $place->getPlaceId(),
		])->fetchOneColumn();
	}

	/**
	 * Find the close family members of an individual.
	 *
	 * @param Individual $individual
	 *
	 * @return string[]
	 */
	private function closeFamily(Individual $individual): array {
		$xrefs = [];

		foreach ($individual->getSpouseFamilies() as $family) {
			foreach ($family->getChildren() as $child) {
				$xrefs[] = $child->getXref();
			}

			foreach ($family->getSpouses() as $spouse) {
				$xrefs[] = $spouse->getXref();
			}
		}

		foreach ($individual->getChildFamilies() as $family) {
			foreach ($family->getChildren() as $child) {
				$xrefs[] = $child->getXref();
			}

			foreach ($family->getSpouses() as $spouse) {
				$xrefs[] = $spouse->getXref();
			}
		}

		return $xrefs;
	}

	/**
	 * Generate a subtitle, based on filter parameters
	 *
	 * @param int    $count
	 * @param Date   $start
	 * @param Date   $end
	 * @param string $placename
	 *
	 * @return string
	 */
	private function subtitle(int $count, Date $start, Date $end, string $placename): string {
		if ($start->isOK() && $end->isOK() && $placename !== '') {
			return I18N::plural(
				'%s individual with events in %s between %s and %s',
				'%s individuals with events in %s between %s and %s',
				$count, I18N::number($count),
				$placename, $start->display(false, '%Y'), $end->display(false, '%Y')
			);
		} elseif ($placename !== '') {
			return I18N::plural(
				'%s individual with events in %s',
				'%s individuals with events in %s',
				$count, I18N::number($count),
				$placename
			);
		} elseif ($start->isOK() && $end->isOK()) {
			return I18N::plural(
				'%s individual with events between %s and %s',
				'%s individuals with events between %s and %s',
				$count, I18N::number($count),
				$start->display(false, '%Y'), $end->display(false, '%Y')
			);
		} else {
			return I18N::plural('%s individual', '%s individuals', $count, I18N::number($count));
		}
	}
}
