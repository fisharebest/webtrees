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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\GedcomCode\GedcomCodePedi;
use Fisharebest\Webtrees\Module\CensusAssistantModule;
use Ramsey\Uuid\Uuid;

require 'includes/session.php';

$action = Filter::post('action', null, Filter::get('action'));

$controller = new PageController;

switch ($action) {
case 'add_child_to_family_action':
	//////////////////////////////////////////////////////////////////////////////
	// Add a child to an existing family
	//////////////////////////////////////////////////////////////////////////////
	$tree      = $controller->tree();
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$PEDI      = Filter::post('PEDI');
	$keep_chan = Filter::postBool('keep_chan');
	$glevels   = Filter::postArray('glevels', '[0-9]');
	$tag       = Filter::postArray('tag', WT_REGEX_TAG);
	$text      = Filter::postArray('text');
	$islink    = Filter::postArray('islink', '[01]');

	$family = Family::getInstance($xref, $tree);
	check_record_access($family);

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
	if (Filter::postBool('SOUR_INDI')) {
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

	if (Filter::post('goto') === 'new') {
		header('Location: ' . $new_child->url());
	} else {
		header('Location: ' . $family->url());
	}
	break;

case 'add_child_to_individual_action':
	//////////////////////////////////////////////////////////////////////////////
	// Add a child to an existing individual (creating a one-parent family)
	//////////////////////////////////////////////////////////////////////////////
	$tree      = $controller->tree();
	$xref    = Filter::post('xref', WT_REGEX_XREF);
	$PEDI    = Filter::post('PEDI');
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	$person = Individual::getInstance($xref, $tree);
	check_record_access($person);

	// Create a family
	if ($person->getSex() === 'F') {
		$gedcom = "0 @NEW@ FAM\n1 WIFE @" . $person->getXref() . '@';
	} else {
		$gedcom = "0 @NEW@ FAM\n1 HUSB @" . $person->getXref() . '@';
	}
	$family = $tree->createRecord($gedcom);

	// Link the parent to the family
	$person->createFact('1 FAMS @' . $family->getXref() . '@', true);

	// Create a child
	FunctionsEdit::splitSource(); // separate SOUR record from the rest

	$gedcom = '0 @NEW@ INDI';
	$gedcom .= FunctionsEdit::addNewName($tree);
	$gedcom .= FunctionsEdit::addNewSex();
	$gedcom .= "\n" . GedcomCodePedi::createNewFamcPedi($PEDI, $family->getXref());
	if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$gedcom .= FunctionsEdit::addNewFact($tree, $match);
		}
	}
	if (Filter::postBool('SOUR_INDI')) {
		$gedcom = FunctionsEdit::handleUpdates($gedcom);
	} else {
		$gedcom = FunctionsEdit::updateRest($gedcom);
	}

	$child = $tree->createRecord($gedcom);

	// Link the family to the child
	$family->createFact('1 CHIL @' . $child->getXref() . '@', true);

	if (Filter::post('goto') === 'new') {
		header('Location: ' . $child->url());
	} else {
		header('Location: ' . $person->url());
	}
	break;

case 'add_parent_to_individual_action':
	//////////////////////////////////////////////////////////////////////////////
	// Add a new parent to an existing individual (creating a one-parent family)
	//////////////////////////////////////////////////////////////////////////////
	$tree    = $controller->tree();
	$xref    = Filter::post('xref', WT_REGEX_XREF);
	$PEDI    = Filter::post('PEDI');
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	$person = Individual::getInstance($xref, $tree);
	check_record_access($person);

	// Create a new family
	$gedcom = "0 @NEW@ FAM\n1 CHIL @" . $person->getXref() . '@';
	$family = $tree->createRecord($gedcom);

	// Link the child to the family
	$person->createFact('1 FAMC @' . $family->getXref() . '@', true);

	// Create a child
	FunctionsEdit::splitSource(); // separate SOUR record from the rest

	$gedcom = '0 @NEW@ INDI';
	$gedcom .= FunctionsEdit::addNewName($tree);
	$gedcom .= FunctionsEdit::addNewSex();
	if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$gedcom .= FunctionsEdit::addNewFact($tree, $match);
		}
	}
	if (Filter::postBool('SOUR_INDI')) {
		$gedcom = FunctionsEdit::handleUpdates($gedcom);
	} else {
		$gedcom = FunctionsEdit::updateRest($gedcom);
	}
	$gedcom .= "\n1 FAMS @" . $family->getXref() . '@';

	$parent = $tree->createRecord($gedcom);

	// Link the family to the child
	if ($parent->getSex() === 'F') {
		$family->createFact('1 WIFE @' . $parent->getXref() . '@', true);
	} else {
		$family->createFact('1 HUSB @' . $parent->getXref() . '@', true);
	}

	if (Filter::post('goto') === 'new') {
		header('Location: ' . $parent->url());
	} else {
		header('Location: ' . $person->url());
	}
	break;

case 'add_unlinked_indi_action':
	//////////////////////////////////////////////////////////////////////////////
	// Add a new, unlinked individual
	//////////////////////////////////////////////////////////////////////////////
	$tree    = $controller->tree();
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	FunctionsEdit::splitSource();
	$gedrec = '0 @REF@ INDI';
	$gedrec .= FunctionsEdit::addNewName($tree);
	$gedrec .= FunctionsEdit::addNewSex();
	if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$gedrec .= FunctionsEdit::addNewFact($tree, $match);
		}
	}
	if (Filter::postBool('SOUR_INDI')) {
		$gedrec = FunctionsEdit::handleUpdates($gedrec);
	} else {
		$gedrec = FunctionsEdit::updateRest($gedrec);
	}

	$new_indi = $tree->createRecord($gedrec);

	if (Filter::post('goto') === 'new') {
		header('Location: ' . $new_indi->url());
	} else {
		header('Location: ' . route('admin-trees'));
	}
	break;

case 'add_spouse_to_individual_action':
	//////////////////////////////////////////////////////////////////////////////
	// Add a spouse to an existing individual (creating a new family)
	//////////////////////////////////////////////////////////////////////////////
	$tree    = $controller->tree();
	$xref    = Filter::post('xref'); // Add a spouse to this individual
	$sex     = Filter::post('SEX', '[MFU]', 'U');
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	$person = Individual::getInstance($xref, $tree);
	check_record_access($person);

	FunctionsEdit::splitSource();
	$indi_gedcom = '0 @REF@ INDI';
	$indi_gedcom .= FunctionsEdit::addNewName($tree);
	$indi_gedcom .= FunctionsEdit::addNewSex();
	if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$indi_gedcom .= FunctionsEdit::addNewFact($tree, $match);
		}
	}
	if (Filter::postBool('SOUR_INDI')) {
		$indi_gedcom = FunctionsEdit::handleUpdates($indi_gedcom);
	} else {
		$indi_gedcom = FunctionsEdit::updateRest($indi_gedcom);
	}

	$fam_gedcom = '';
	if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FAMFACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$fam_gedcom .= FunctionsEdit::addNewFact($tree, $match);
		}
	}
	if (Filter::postBool('SOUR_FAM')) {
		$fam_gedcom = FunctionsEdit::handleUpdates($fam_gedcom);
	} else {
		$fam_gedcom = FunctionsEdit::updateRest($fam_gedcom);
	}

	// Create the new spouse
	$spouse = $tree->createRecord($indi_gedcom);
	// Create a new family
	if ($sex === 'F') {
		$family = $tree->createRecord("0 @NEW@ FAM\n1 WIFE @" . $spouse->getXref() . "@\n1 HUSB @" . $person->getXref() . '@' . $fam_gedcom);
	} else {
		$family = $tree->createRecord("0 @NEW@ FAM\n1 HUSB @" . $spouse->getXref() . "@\n1 WIFE @" . $person->getXref() . '@' . $fam_gedcom);
	}
	// Link the spouses to the family
	$spouse->createFact('1 FAMS @' . $family->getXref() . '@', true);
	$person->createFact('1 FAMS @' . $family->getXref() . '@', true);

	if (Filter::post('goto') === 'new') {
		header('Location: ' . $spouse->url());
	} else {
		header('Location: ' . $person->url());
	}
	break;

case 'add_spouse_to_family_action':
	//////////////////////////////////////////////////////////////////////////////
	// Add a spouse to an existing family
	//////////////////////////////////////////////////////////////////////////////
	$tree    = $controller->tree();
	$xref    = Filter::post('xref', WT_REGEX_XREF);
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	$family = Family::getInstance($xref, $tree);
	check_record_access($family);

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

	if (Filter::postBool('SOUR_INDI')) {
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
	if (Filter::postBool('SOUR_FAM')) {
		$famrec = FunctionsEdit::handleUpdates($famrec);
	} else {
		$famrec = FunctionsEdit::updateRest($famrec);
	}
	$family->createFact(trim($famrec), true); // trim leading \n

	if (Filter::post('goto') === 'new') {
		header('Location: ' . $spouse->url());
	} else {
		header('Location: ' . $family->url());
	}
	break;
}

/**
 * Show an option to preserve the existing CHAN record when editing.
 *
 * @param GedcomRecord $record
 *
 * @return string
 */
function keep_chan(GedcomRecord $record = null) {
	global $controller;

	if (Auth::isAdmin()) {
		if ($record) {
			$details
				= GedcomTag::getLabelValue('DATE', $record->lastChangeTimestamp()) .
				GedcomTag::getLabelValue('_WT_USER', e($record->lastChangeUser()));
		} else {
			$details = '';
		}

		return
			'<div class="form-group row"><label class="col-sm-3 col-form-label" for="keep_chan">' .
			I18N::translate('Last change') .
			'</label><div class="col-sm-9">' .
			Bootstrap4::checkbox(I18N::translate('Keep the existing “last change” information'), true, ['name' => 'keep_chan', 'checked' => (bool) $controller->tree()->getPreference('NO_UPDATE_CHAN')]) .
			$details .
			'</div></div>';
	} else {
		return '';
	}
}

/**
 * Can we edit a GedcomRecord object
 *
 * @param GedcomRecord $record
 */
function check_record_access(GedcomRecord $record = null) {
	if (!$record || !$record->canShow() || !$record->canEdit()) {
		header('Location: ' . $record->url());

		exit;
	}
}
