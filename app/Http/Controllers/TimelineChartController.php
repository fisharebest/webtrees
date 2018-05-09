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

use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A chart showing the events of individuals on a time line.
 */
class TimelineChartController extends AbstractChartController {
	// The user can alter the vertical scale
	const SCALE_MIN     = 1;
	const SCALE_MAX     = 200;
	const SCALE_DEFAULT = 10;

	// GEDCOM events that may have DATE data, but should not be displayed
	const NON_FACTS = [
		'BAPL',
		'ENDL',
		'SLGC',
		'SLGS',
		'_TODO',
		'CHAN',
	];

	// Box height
	const BHEIGHT = 30;

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

		$this->checkModuleIsActive($tree, 'timeline_chart');

		$scale = (int) $request->get('scale', self::SCALE_DEFAULT);
		$scale = min($scale, self::SCALE_MAX);
		$scale = max($scale, self::SCALE_MIN);

		$xrefs = (array) $request->get('xrefs', []);
		$xrefs = array_unique($xrefs);
		$xrefs = array_filter($xrefs, function (string $xref) use ($tree) {
			$individual = Individual::getInstance($xref, $tree);

			return $individual !== null && $individual->canShow();
		});

		// Generate URLs omitting each xref.
		$remove_urls = [];
		foreach ($xrefs as $xref) {
			$tmp = array_filter($xrefs, function ($x) use ($xref) {
				return $x !== $xref;
			});

			$remove_urls[$xref] = route('timeline', [
				'ged'   => $tree->getName(),
				'scale' => $scale,
				'xrefs' => $tmp,
			]);
		}

		$individuals = array_map(function (string $xref) use ($tree) {
			return Individual::getInstance($xref, $tree);
		}, $xrefs);

		$title = I18N::translate('Timeline');

		$chart_url = route('timeline-chart', [
			'ged'   => $tree->getName(),
			'scale' => $scale,
			'xrefs' => $xrefs,
		]);

		$zoom_in_url = route('timeline', [
			'ged'   => $tree->getName(),
			'scale' => min(self::SCALE_MAX, $scale + (int) ($scale * 0.2 + 1)),
			'xrefs' => $xrefs,
		]);

		$zoom_out_url = route('timeline', [
			'route' => 'timeline',
			'ged'   => $tree->getName(),
			'scale' => max(self::SCALE_MIN, $scale - (int) ($scale * 0.2 + 1)),
			'xrefs' => $xrefs,
		]);

		return $this->viewResponse('timeline-page', [
			'chart_url'    => $chart_url,
			'individuals'  => $individuals,
			'remove_urls'  => $remove_urls,
			'title'        => $title,
			'scale'        => $scale,
			'zoom_in_url'  => $zoom_in_url,
			'zoom_out_url' => $zoom_out_url,
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

		$this->checkModuleIsActive($tree, 'timeline_chart');

		$scale = (int) $request->get('scale', self::SCALE_DEFAULT);
		$scale = min($scale, self::SCALE_MAX);
		$scale = max($scale, self::SCALE_MIN);

		$xrefs = (array) $request->get('xrefs', []);
		$xrefs = array_unique($xrefs);

		/** @var Individual[] $individuals */
		$individuals = array_map(function (string $xref) use ($tree) {
			return Individual::getInstance($xref, $tree);
		}, $xrefs);

		$individuals = array_filter($individuals, function (Individual $individual = null) {
			return $individual !== null && $individual->canShow();
		});

		$baseyear    = (int) date('Y');
		$topyear     = 0;
		$indifacts   = [];
		$birthyears  = [];
		$birthmonths = [];
		$birthdays   = [];

		foreach ($individuals as $individual) {
			$bdate = $individual->getBirthDate();
			if ($bdate->isOK()) {
				$date = new GregorianDate($bdate->minimumJulianDay());

				$birthyears [$individual->getXref()] = $date->y;
				$birthmonths[$individual->getXref()] = max(1, $date->m);
				$birthdays  [$individual->getXref()] = max(1, $date->d);
			}
			// find all the fact information
			$facts = $individual->getFacts();
			foreach ($individual->getSpouseFamilies() as $family) {
				foreach ($family->getFacts() as $fact) {
					$facts[] = $fact;
				}
			}
			foreach ($facts as $event) {
				// get the fact type
				$fact = $event->getTag();
				if (!in_array($fact, self::NON_FACTS)) {
					// check for a date
					$date = $event->getDate();
					if ($date->isOK()) {
						$date     = new GregorianDate($date->minimumJulianDay());
						$baseyear = min($baseyear, $date->y);
						$topyear  = max($topyear, $date->y);

						if (!$individual->isDead()) {
							$topyear = max($topyear, (int) date('Y'));
						}

						// do not add the same fact twice (prevents marriages from being added multiple times)
						if (!in_array($event, $indifacts, true)) {
							$indifacts[] = $event;
						}
					}
				}
			}
		}

		if ($scale === 0) {
			$scale = (int) (($topyear - $baseyear) / 20 * count($indifacts) / 4);
			if ($scale < 6) {
				$scale = 6;
			}
		}
		if ($scale < 2) {
			$scale = 2;
		}
		$baseyear -= 5;
		$topyear  += 5;

		Functions::sortFacts($indifacts);

		$html = view('timeline-chart', [
			'baseyear'    => $baseyear,
			'bheight'     => self::BHEIGHT,
			'birthdays'   => $birthdays,
			'birthmonths' => $birthmonths,
			'birthyears'  => $birthyears,
			'indifacts'   => $indifacts,
			'individuals' => $individuals,
			'placements'  => [],
			'scale'       => $scale,
			'topyear'     => $topyear,
		]);

		return new Response($html);
	}
}
