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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A chart showing the lifespans of individuals.
 */
class LifespansChartController extends AbstractChartController {
	// GEDCOM events that may have DATE data, but should not be displayed
	const NON_FACTS
		= [
			'BAPL',
			'ENDL',
			'SLGC',
			'SLGS',
			'_TODO',
			'CHAN',
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

		$this->checkModuleIsActive($tree, 'lifespans_chart');

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

			$remove_urls[$xref] = route('lifespans', [
				'ged'   => $tree->getName(),
				'xrefs' => $tmp,
			]);
		}

		$individuals = array_map(function (string $xref) use ($tree) {
			return Individual::getInstance($xref, $tree);
		}, $xrefs);

		$title = I18N::translate('Timeline');

		$chart_url = route('lifespans-chart', [
			'ged'   => $tree->getName(),
			'xrefs' => $xrefs,
		]);

		return $this->viewResponse('lifespans-page', [
			'chart_url'    => $chart_url,
			'individuals'  => $individuals,
			'remove_urls'  => $remove_urls,
			'title'        => $title,
			'tree'         => $tree,
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

		$html = view('lifespans-chart', [
			'individuals' => $individuals,
		]);

		return new Response($html);
	}
}
