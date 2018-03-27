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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Module\OpenStreetMapModule;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PedigreeMapChartController extends AbstractChartController {

	// Needs openstreetmap module to provide assets and respond to AJAX calls
	const SERVING_MODULE = 'openstreetmap';

	/**
	 * A form to request the chart parameters.
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
	public function page(Request $request): Response {
		/** @var Tree $tree */
		$tree       = $request->attributes->get('tree');
		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkModuleIsActive($tree, self::SERVING_MODULE);
		$this->checkIndividualAccess($individual);

		$maxgenerations = $tree->getPreference('MAX_PEDIGREE_GENERATIONS');
		$generations    = $request->get('generations', $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS'));
		$osm_module     = Module::getModuleByName(self::SERVING_MODULE);

		return $this->viewResponse(
			'modules/openstreetmap/pedigreemap-page',
			[
				'assets'         =>$osm_module->assets(),
				'title'          => /* I18N: %s is an individual’s name */ I18N::translate('Pedigree map of %s', $individual->getFullName()),
				'individual'     => $individual,
				'tree'           => $tree,
				'generations'    => $generations,
				'maxgenerations' => $maxgenerations,
			]
		);
	}

	/**
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
	public function chart(Request $request): Response {
		$tree = $request->attributes->get('tree');
		$this->checkModuleIsActive($tree, self::SERVING_MODULE);
		$xref        = $request->get('xref');
		$individual  = Individual::getInstance($xref, $tree);
		$generations = (int)$request->get('generations');

		$html = view(
			'modules/openstreetmap/pedigreemap-chart',
			[
				'module'      => self::SERVING_MODULE,
				'ref'         => $individual->getXref(),
				'tree'        => $tree,
				'generations' => $generations,
			]
		);

		return new Response($html);
	}
}
