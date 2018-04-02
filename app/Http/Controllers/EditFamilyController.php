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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for edit forms and responses.
 */
class EditFamilyController extends AbstractBaseController {
	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function reorderChildren(Request $request): Response {
		/** @var Tree $tree */
		$tree   = $request->attributes->get('tree');
		$xref   = $request->get('xref');
		$family = Family::getInstance($xref, $tree);

		$this->checkFamilyAccess($family, true);

		$title = $family->getFullName() . ' — ' . I18N::translate('Re-order children');

		return $this->viewResponse('edit/reorder-children', [
			'title'  => $title,
			'family' => $family,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function reorderChildrenAction(Request $request): Response {
		/** @var Tree $tree */
		$tree   = $request->attributes->get('tree');
		$xref   = $request->get('xref');
		$order  = (array) $request->get('order', []);
		$family = Family::getInstance($xref, $tree);

		$this->checkFamilyAccess($family, true);

		$dummy_facts = ['0 @' . $family->getXref() . '@ FAM'];
		$sort_facts  = [];
		$keep_facts  = [];

		// Split facts into FAMS and other
		foreach ($family->getFacts() as $fact) {
			if ($fact->getTag() === 'CHIL') {
				$sort_facts[$fact->getFactId()] = $fact->getGedcom();
			} else {
				$keep_facts[] = $fact->getGedcom();
			}
		}

		// Sort the facts
		uksort($sort_facts, function ($x, $y) use ($order) {
			return array_search($x, $order) - array_search($y, $order);
		});

		// Merge the facts
		$gedcom = implode("\n", array_merge($dummy_facts, $sort_facts, $keep_facts));

		$family->updateRecord($gedcom, false);

		return new RedirectResponse($family->url());
	}
}
