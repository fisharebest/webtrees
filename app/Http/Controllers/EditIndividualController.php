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
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for edit forms and responses.
 */
class EditIndividualController extends BaseController {
	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function reorderSpouses(Request $request): Response {
		/** @var Tree $tree */
		$tree       = $request->attributes->get('tree');
		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		if ($individual === null) {
			return $this->individualNotFoundResponse();
		} elseif (!$individual->canEdit()) {
			return $this->individualNotAllowedResponse();
		}

		$title = $individual->getFullName() . ' — ' . I18N::translate('Re-order families');

		return $this->viewResponse('edit/reorder-spouses', [
			'title'      => $title,
			'individual' => $individual,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function reorderSpousesAction(Request $request): Response {
		/** @var Tree $tree */
		$tree       = $request->attributes->get('tree');
		$xref       = $request->get('xref');
		$order      = (array) $request->get('order', []);
		$individual = Individual::getInstance($xref, $tree);

		if ($individual === null) {
			return $this->individualNotFoundResponse();
		} elseif (!$individual->canEdit()) {
			return $this->individualNotAllowedResponse();
		}

		$dummy_facts = ['0 @' . $individual->getXref() . '@ INDI'];
		$sort_facts  = [];
		$keep_facts  = [];

		// Split facts into FAMS and other
		foreach ($individual->getFacts() as $fact) {
			if ($fact->getTag() === 'FAMS') {
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

		$individual->updateRecord($gedcom, false);

		return new RedirectResponse($individual->url());
	}
}
