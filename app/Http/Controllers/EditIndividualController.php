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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for edit forms and responses.
 */
class EditIndividualController extends AbstractBaseController {
	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function reorderMedia(Request $request): Response {
		/** @var Tree $tree */
		$tree       = $request->attributes->get('tree');
		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		$title = $individual->getFullName() . ' — ' . I18N::translate('Re-order media');

		return $this->viewResponse('edit/reorder-media', [
			'title'      => $title,
			'individual' => $individual,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function reorderMediaAction(Request $request): Response {
		/** @var Tree $tree */
		$tree       = $request->attributes->get('tree');
		$xref       = $request->get('xref');
		$order      = (array) $request->get('order', []);
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		$dummy_facts = ['0 @' . $individual->getXref() . '@ INDI'];
		$sort_facts  = [];
		$keep_facts  = [];

		// Split facts into OBJE and other
		foreach ($individual->getFacts() as $fact) {
			if ($fact->getTag() === 'OBJE') {
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

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function reorderNames(Request $request): Response {
		/** @var Tree $tree */
		$tree       = $request->attributes->get('tree');
		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		$title = $individual->getFullName() . ' — ' . I18N::translate('Re-order names');

		return $this->viewResponse('edit/reorder-names', [
			'title'      => $title,
			'individual' => $individual,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function reorderNamesAction(Request $request): Response {
		/** @var Tree $tree */
		$tree       = $request->attributes->get('tree');
		$xref       = $request->get('xref');
		$order      = (array) $request->get('order', []);
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		$dummy_facts = ['0 @' . $individual->getXref() . '@ INDI'];
		$sort_facts  = [];
		$keep_facts  = [];

		// Split facts into NAME and other
		foreach ($individual->getFacts() as $fact) {
			if ($fact->getTag() === 'NAME') {
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

		$this->checkIndividualAccess($individual, true);

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

		$this->checkIndividualAccess($individual, true);

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

	/**
	 * Add a child to an existing individual (creating a one-parent family).
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function addChild(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref   = $request->get('xref', '');

		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		$title = $individual->getFullName() . ' - ' . I18N::translate('Add a child to create a one-parent family');

		return $this->viewResponse('edit/new-individual', [
			'tree'       => $tree,
			'title'      => $title,
			'nextaction' => 'add_child_to_individual_action',
			'individual' => $individual,
			'family'     => null,
			'name_fact'  => null,
			'famtag'     => 'CHIL',
			'gender'     => 'U',
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function addChildAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref = $request->get('xref', '');

		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		// @TODO move edit_interface.php code here

		return new RedirectResponse($individual->url());
	}

	/**
	 * Add a parent to an existing individual (creating a one-parent family).
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function addParent(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref   = $request->get('xref', '');
		$gender = $request->get('gender', 'U');

		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		if ($gender === 'F') {
			$title = $individual->getFullName() . ' - ' . I18N::translate('Add a mother');
			$famtag = 'WIFE';
		} else {
			$title = $individual->getFullName() . ' - ' . I18N::translate('Add a father');
			$famtag = 'HUSB';
		}

		return $this->viewResponse('edit/new-individual', [
			'tree'       => $tree,
			'title'      => $title,
			'nextaction' => 'add_parent_to_individual_action',
			'individual' => $individual,
			'family'     => null,
			'name_fact'  => null,
			'famtag'     => $famtag,
			'gender'     => $gender,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function addParentAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref = $request->get('xref', '');

		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		// @TODO move edit_interface.php code here

		return new RedirectResponse($individual->url());
	}

	/**
	 * Add a spouse to an existing individual (creating a new family).
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function addSpouse(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$gender = $request->get('gender', 'F');
		$xref   = $request->get('xref', '');

		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		if ($gender === 'F') {
			$title = $individual->getFullName() . ' - ' . I18N::translate('Add a wife');
			$famtag = 'WIFE';
		} else {
			$title = $individual->getFullName() . ' - ' . I18N::translate('Add a husband');
			$famtag = 'HUSB';
		}

		return $this->viewResponse('edit/new-individual', [
			'tree'       => $tree,
			'title'      => $title,
			'nextaction' => 'add_spouse_to_individual_action',
			'individual' => $individual,
			'family'     => null,
			'name_fact'  => null,
			'famtag'     => $famtag,
			'gender'     => $gender,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function addSpouseAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref = $request->get('xref', '');

		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		// @TODO move edit_interface.php code here

		return new RedirectResponse($individual->url());
	}

	/**
	 * Add an unlinked individual
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function addUnlinked(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		return $this->viewResponse('edit/new-individual', [
			'tree'       => $tree,
			'title'      => I18N::translate('Create an individual'),
			'nextaction' => 'add_unlinked_indi_action',
			'individual' => null,
			'family'     => null,
			'name_fact'  => null,
			'famtag'     => null,
			'gender'     => 'U',
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function addUnlinkedAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref = $request->get('xref', '');

		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		// @TODO move edit_interface.php code here

		return new RedirectResponse($individual->url());
	}

	/**
	 * Edit a name record.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function editName(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$fact_id = $request->get('fact_id', '');
		$xref    = $request->get('xref', '');

		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		// Find the fact to edit
		$name_fact = null;
		foreach ($individual->getFacts() as $fact) {
			if ($fact->getFactId() === $fact_id && $fact->canEdit()) {
				return $this->viewResponse('edit/new-individual', [
					'tree'       => $tree,
					'title'      => I18N::translate('Edit the name'),
					'nextaction' => 'update',
					'individual' => $individual,
					'family'     => null,
					'name_fact'  => $name_fact,
					'famtag'     => '',
					'gender'     => $individual->getSex(),
				]);
			}
		}

		throw new NotFoundHttpException;
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function editNameAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref = $request->get('xref', '');

		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		// @TODO move edit_interface.php code here

		return new RedirectResponse($individual->url());
	}

	/**
	 * Add a new name record.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function addName(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref = $request->get('xref', '');

		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		$title = $individual->getFullName() . ' — ' . I18N::translate('Add a name');

		return $this->viewResponse('edit/new-individual', [
			'tree'       => $tree,
			'title'      => $title,
			'nextaction' => 'update',
			'individual' => $individual,
			'family'     => null,
			'name_fact'  => null,
			'famtag'     => '',
			'gender'     => $individual->getSex(),
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function addNameAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref = $request->get('xref', '');

		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, true);

		// @TODO move edit_interface.php code here

		return new RedirectResponse($individual->url());
	}
}
