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
use Fisharebest\Webtrees\Tree;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the family page.
 */
class FamilyController extends AbstractBaseController {
	/**
	 * Show a family's page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function show(Request $request): Response {
		/** @var Tree $tree */
		$tree   = $request->attributes->get('tree');
		$xref   = $request->get('xref');
		$family = Family::getInstance($xref, $tree);

		$this->checkFamilyAccess($family, false);

		return $this->viewResponse('family-page', [
			'facts'       => $family->getFacts(null, true),
			'meta_robots' => 'index,follow',
			'record'      => $family,
			'significant' => $this->significant($family),
			'title'       => $family->getFullName(),
		]);
	}

	/**
	 * What are the significant elements of this page?
	 * The layout will need them to generate URLs for charts and reports.
	 *
	 * @param Family $family
	 *
	 * @return stdClass
	 */
	private function significant(Family $family) {
		$significant = (object) [
			'family'     => $family,
			'individual' => null,
			'surname'    => '',
		];

		foreach ($family->getSpouses() + $family->getChildren() as $individual) {
			$significant->individual = $individual;
			list($significant->surname) = explode(',', $individual->getSortName());
			break;
		}

		return $significant;
	}
}
