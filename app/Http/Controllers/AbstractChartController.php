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

use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Common logic for chart controllers.
 */
abstract class AbstractChartController extends BaseController {
	/**
	 * Check that a module is enabled for a tree.
	 *
	 * @param Tree   $tree
	 * @param string $module
	 */
	protected function checkModuleIsActive(Tree $tree, string $module) {
		$active_charts = Module::getActiveCharts($tree);

		if (!array_key_exists($module, $active_charts)) {
			throw new NotFoundHttpException;
		}
	}

	/**
	 * Find the ancestors of an individual, and generate an array indexed by
	 * Sosa-Stradonitz number.
	 *
	 * @param Individual $individual  Start with this individual
	 * @param int        $generations Fetch this number of generations
	 *
	 * @return Individual[]
	 */
	protected function sosaStradonitzAncestors(Individual $individual, int $generations): array {
		/** @var Individual[] $ancestors */
		$ancestors = [
			1 => $individual,
		];

		$max = 2 ** ($generations - 1);

		for ($i = 1; $i < $max; $i++) {
			$ancestors[$i * 2]     = null;
			$ancestors[$i * 2 + 1] = null;

			$individual = $ancestors[$i];

			if ($individual !== null) {
				$family = $individual->getPrimaryChildFamily();
				if ($family !== null) {
					if ($family->getHusband() !== null) {
						$ancestors[$i * 2] = $family->getHusband();
					}
					if ($family->getWife() !== null) {
						$ancestors[$i * 2 + 1] = $family->getWife();
					}
				}
			}
		}

		return $ancestors;
	}
}
