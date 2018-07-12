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
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomCode\GedcomCodePedi;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
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

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function addChild(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref   = $request->get('xref', '');
		$gender = $request->get('gender', 'U');

		$family = Family::getInstance($xref, $tree);

		$this->checkFamilyAccess($family, true);

		$title = $family->getFullName() . ' - ' . I18N::translate('Add a child');

		return $this->viewResponse('edit/new-individual', [
			'tree'       => $tree,
			'title'      => $title,
			'nextaction' => 'add_child_to_family_action',
			'individual' => null,
			'family'     => $family,
			'name_fact'  => null,
			'famtag'     => 'CHIL',
			'gender'     => $gender,
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

		$family = Family::getInstance($xref, $tree);

		$this->checkFamilyAccess($family, true);

		$PEDI      = $request->get('PEDI', '');
		$keep_chan = (bool) $request->get('keep_chan');

		global $glevels, $tag, $text, $islink;
		$glevels   = $request->get('glevels', []);
		$tag       = $request->get('tag', []);
		$text      = $request->get('text', []);
		$islink    = $request->get('islink', []);

		FunctionsEdit::splitSource();
		$gedrec = '0 @REF@ INDI';
		$gedrec .= FunctionsEdit::addNewName($tree);
		$gedrec .= FunctionsEdit::addNewSex();
		if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
			foreach ($matches[1] as $match) {
				$gedrec .= FunctionsEdit::addNewFact($tree, $match);
			}
		}
		$gedrec .= "\n" . GedcomCodePedi::createNewFamcPedi($PEDI, $xref);
		if ((bool) $request->get('SOUR_INDI')) {
			$gedrec = FunctionsEdit::handleUpdates($gedrec);
		} else {
			$gedrec = FunctionsEdit::updateRest($gedrec);
		}

		// Create the new child
		$new_child = $family->getTree()->createRecord($gedrec);

		// Insert new child at the right place
		$done = false;
		foreach ($family->getFacts('CHIL') as $fact) {
			$old_child = $fact->getTarget();
			if ($old_child && Date::compare($new_child->getEstimatedBirthDate(), $old_child->getEstimatedBirthDate()) < 0) {
				// Insert before this child
				$family->updateFact($fact->getFactId(), '1 CHIL @' . $new_child->getXref() . "@\n" . $fact->getGedcom(), !$keep_chan);
				$done = true;
				break;
			}
		}
		if (!$done) {
			// Append child at end
			$family->createFact('1 CHIL @' . $new_child->getXref() . '@', !$keep_chan);
		}

		if ($request->get('goto') === 'new') {
			return new RedirectResponse($new_child->url());
		} else {
			return new RedirectResponse($family->url());
		}
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function addSpouse(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref   = $request->get('xref', '');
		$famtag = $request->get('famtag', '');

		$family = Family::getInstance($xref, $tree);

		$this->checkFamilyAccess($family, true);

		if ($famtag === 'WIFE') {
			$title = I18N::translate('Add a wife');
			$gender = 'F';
		} else {
			$title = I18N::translate('Add a husband');
			$gender = 'M';
		}

		return $this->viewResponse('edit/new-individual', [
			'tree'       => $tree,
			'title'      => $title,
			'nextaction' => 'add_spouse_to_family_action',
			'individual' => null,
			'family'     => $family,
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

		$family = Family::getInstance($xref, $tree);

		$this->checkFamilyAccess($family, true);

		global $glevels, $tag, $text, $islink;
		$glevels = $request->get('glevels', []);
		$tag     = $request->get('tag', []);
		$text    = $request->get('text', []);
		$islink  = $request->get('islink', []);

		// Create the new spouse
		FunctionsEdit::splitSource(); // separate SOUR record from the rest

		$gedrec = '0 @REF@ INDI';
		$gedrec .= FunctionsEdit::addNewName($tree);
		$gedrec .= FunctionsEdit::addNewSex();
		if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
			foreach ($matches[1] as $match) {
				$gedrec .= FunctionsEdit::addNewFact($tree, $match);
			}
		}

		if ((bool) $request->get('SOUR_INDI')) {
			$gedrec = FunctionsEdit::handleUpdates($gedrec);
		} else {
			$gedrec = FunctionsEdit::updateRest($gedrec);
		}
		$gedrec .= "\n1 FAMS @" . $family->getXref() . '@';
		$spouse = $tree->createRecord($gedrec);

		// Update the existing family - add marriage, etc
		if ($family->getFirstFact('HUSB')) {
			$family->createFact('1 WIFE @' . $spouse->getXref() . '@', true);
		} else {
			$family->createFact('1 HUSB @' . $spouse->getXref() . '@', true);
		}
		$famrec = '';
		if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FAMFACTS'), $matches)) {
			foreach ($matches[1] as $match) {
				$famrec .= FunctionsEdit::addNewFact($tree, $match);
			}
		}
		if ((bool) $request->get('SOUR_FAM')) {
			$famrec = FunctionsEdit::handleUpdates($famrec);
		} else {
			$famrec = FunctionsEdit::updateRest($famrec);
		}
		$family->createFact(trim($famrec), true); // trim leading \n

		if ($request->get('goto') === 'new') {
			return new RedirectResponse($spouse->url());
		} else {
			return new RedirectResponse($family->url());
		}
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function changeFamilyMembers(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref   = $request->get('xref', '');
		$family = Family::getInstance($xref, $tree);
		$this->checkFamilyAccess($family, true);

		$title = I18N::translate('Change family members') . ' – ' . $family->getFullName();

		return $this->viewResponse('edit/change-family-members', [
			'tree'     => $tree,
			'title'    => $title,
			'family'   => $family,
			'father'   => $family->getHusband(),
			'mother'   => $family->getWife(),
			'children' => $family->getChildren(),
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function changeFamilyMembersAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref   = $request->get('xref', '');
		$family = Family::getInstance($xref, $tree);
		$this->checkFamilyAccess($family, true);

		$HUSB = $request->get('HUSB', '');
		$WIFE = $request->get('WIFE', '');
		$CHIL = $request->get('CHIL', []);

		// Current family members
		$old_father   = $family->getHusband();
		$old_mother   = $family->getWife();
		$old_children = $family->getChildren();

		// New family members
		$new_father   = Individual::getInstance($HUSB, $tree);
		$new_mother   = Individual::getInstance($WIFE, $tree);
		$new_children = [];
		foreach ($CHIL as $child) {
			$new_children[] = Individual::getInstance($child, $tree);
		}

		if ($old_father !== $new_father) {
			if ($old_father) {
				// Remove old FAMS link
				foreach ($old_father->getFacts('FAMS') as $fact) {
					if ($fact->getTarget() === $family) {
						$old_father->deleteFact($fact->getFactId(), true);
					}
				}
				// Remove old HUSB link
				foreach ($family->getFacts('HUSB|WIFE') as $fact) {
					if ($fact->getTarget() === $old_father) {
						$family->deleteFact($fact->getFactId(), true);
					}
				}
			}
			if ($new_father) {
				// Add new FAMS link
				$new_father->createFact('1 FAMS @' . $family->getXref() . '@', true);
				// Add new HUSB link
				$family->createFact('1 HUSB @' . $new_father->getXref() . '@', true);
			}
		}

		if ($old_mother !== $new_mother) {
			if ($old_mother) {
				// Remove old FAMS link
				foreach ($old_mother->getFacts('FAMS') as $fact) {
					if ($fact->getTarget() === $family) {
						$old_mother->deleteFact($fact->getFactId(), true);
					}
				}
				// Remove old WIFE link
				foreach ($family->getFacts('HUSB|WIFE') as $fact) {
					if ($fact->getTarget() === $old_mother) {
						$family->deleteFact($fact->getFactId(), true);
					}
				}
			}
			if ($new_mother) {
				// Add new FAMS link
				$new_mother->createFact('1 FAMS @' . $family->getXref() . '@', true);
				// Add new WIFE link
				$family->createFact('1 WIFE @' . $new_mother->getXref() . '@', true);
			}
		}

		foreach ($old_children as $old_child) {
			if ($old_child && !in_array($old_child, $new_children)) {
				// Remove old FAMC link
				foreach ($old_child->getFacts('FAMC') as $fact) {
					if ($fact->getTarget() === $family) {
						$old_child->deleteFact($fact->getFactId(), true);
					}
				}
				// Remove old CHIL link
				foreach ($family->getFacts('CHIL') as $fact) {
					if ($fact->getTarget() === $old_child) {
						$family->deleteFact($fact->getFactId(), true);
					}
				}
			}
		}

		foreach ($new_children as $new_child) {
			if ($new_child && !in_array($new_child, $old_children)) {
				// Add new FAMC link
				$new_child->createFact('1 FAMC @' . $family->getXref() . '@', true);
				// Add new CHIL link
				$family->createFact('1 CHIL @' . $new_child->getXref() . '@', true);
			}
		}
		return new RedirectResponse($family->url());
	}
}
