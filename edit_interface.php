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
case 'edit':
	//////////////////////////////////////////////////////////////////////////////
	// Edit a fact
	//////////////////////////////////////////////////////////////////////////////
	$xref    = Filter::get('xref', WT_REGEX_XREF);
	$fact_id = Filter::get('fact_id');

	$record = GedcomRecord::getInstance($xref, $controller->tree());
	check_record_access($record);

	// Find the fact to edit
	$edit_fact = null;
	foreach ($record->getFacts() as $fact) {
		if ($fact->getFactId() === $fact_id && $fact->canEdit()) {
			$edit_fact = $fact;
			break;
		}
	}
	if (!$edit_fact) {
		header('Location: ' . $record->url());
		break;
	}

	$controller
		->setPageTitle($record->getFullName() . ' - ' . GedcomTag::getLabel($edit_fact->getTag()))
		->pageHeader();

	echo '<h2>', $controller->getPageTitle(), '</h2>';
	FunctionsPrint::initializeCalendarPopup();
	echo '<form name="editform" method="post" enctype="multipart/form-data">';
	echo '<input type="hidden" name="ged" value="', e($controller->tree()->getName()), '">';
	echo '<input type="hidden" name="action" value="update">';
	echo '<input type="hidden" name="fact_id" value="', $fact_id, '">';
	echo '<input type="hidden" name="xref" value="', $xref, '">';
	echo '<input type="hidden" name="prev_action" value="edit">';
	echo Filter::getCsrf();
	FunctionsEdit::createEditForm($edit_fact);
	echo keep_chan($record);

	$level1type = $edit_fact->getTag();
	switch ($record::RECORD_TYPE) {
		case 'REPO':
			// REPO:NAME facts may take a NOTE (but the REPO record may not).
			if ($level1type === 'NAME') {
				echo view('cards/add-note', [
					'level' => 2,
				]);
				echo view('cards/add-shared-note', [
					'level' => 2,
				]);
			}
			break;
		case 'FAM':
		case 'INDI':
			// FAM and INDI records have real facts. They can take NOTE/SOUR/OBJE/etc.
			if ($level1type !== 'SEX' && $level1type !== 'NOTE' && $level1type !== 'ALIA') {
				if ($level1type !== 'SOUR') {
					echo view('cards/add-source-citation', [
						'level'          => 2,
						'full_citations' => $controller->tree()->getPreference('FULL_SOURCES'),
					]);				}
				if ($level1type !== 'OBJE') {
					if ($controller->tree()->getPreference('MEDIA_UPLOAD') >= Auth::accessLevel($controller->tree())) {
						echo view('cards/add-media-object', [
							'level' => 2,
						]);
					}
				}
				echo view('cards/add-note', [
					'level' => 2,
				]);
				echo view('cards/add-shared-note', [
					'level' => 2,
				]);
				if ($level1type !== 'ASSO' && $level1type !== 'NOTE' && $level1type !== 'SOUR') {
					echo view('cards/add-associate', [
						'id'    => Uuid::uuid4()->toString(),
						'level' => 2,
					]);
				}
				// allow to add godfather and godmother for CHR fact or best man and bridesmaid  for MARR fact in one window
				if (in_array($level1type, Config::twoAssociates())) {
					echo view('cards/add-associate', [
						'id'    => Uuid::uuid4()->toString(),
						'level' => 2,
					]);
				}
				if ($level1type !== 'SOUR') {
					echo view('cards/add-restriction', [
						'level' => 2,
					]);
				}
			}
			break;
		default:
			// Other types of record do not have these lower-level records
			break;
	}

	?>
	<div class="row form-group">
		<div class="col-sm-9 offset-sm-3">
			<button class="btn btn-primary" type="submit">
				<?= FontAwesome::decorativeIcon('save') ?>
				<?= /* I18N: A button label. */
				I18N::translate('save') ?>
			</button>
			<a class="btn btn-secondary" href="<?= e($record->url()) ?>">
				<?= FontAwesome::decorativeIcon('cancel') ?>
				<?= /* I18N: A button label. */
				I18N::translate('cancel') ?>
			</a>
			<?php if (Auth::isAdmin() || $controller->tree()->getPreference('SHOW_GEDCOM_RECORD')): ?>
				<a class="btn btn-link" href="<?= e(route('edit-raw-fact', ['xref' => $xref, 'fact_id' => $fact_id, 'ged' => $controller->tree()->getName()])) ?>">
					<?= I18N::translate('Edit the raw GEDCOM') ?>
				</a>
			<?php endif; ?>
		</div>
	</div>

	</form>
	<?php
	echo view('modals/on-screen-keyboard');
	echo view('modals/ajax');
	break;

case 'add':
	//////////////////////////////////////////////////////////////////////////////
	// Add a new fact
	//////////////////////////////////////////////////////////////////////////////
	$xref = Filter::get('xref', WT_REGEX_XREF);
	$fact = Filter::get('fact', WT_REGEX_TAG);

	$record = GedcomRecord::getInstance($xref, $controller->tree());
	check_record_access($record);

	$controller
		->setPageTitle($record->getFullName() . ' - ' . GedcomTag::getLabel($fact, $record))
		->pageHeader();

	$level0type = $record::RECORD_TYPE;

	echo '<h2>', $controller->getPageTitle(), '</h2>';

	FunctionsPrint::initializeCalendarPopup();
	echo '<form name="addform" method="post" enctype="multipart/form-data">';
	echo '<input type="hidden" name="ged" value="', e($controller->tree()->getName()), '">';
	echo '<input type="hidden" name="action" value="update">';
	echo '<input type="hidden" name="xref" value="', $xref, '">';
	echo '<input type="hidden" name="prev_action" value="add">';
	echo '<input type="hidden" name="fact_type" value="' . $fact . '">';
	echo Filter::getCsrf();
	FunctionsEdit::createAddForm($fact);
	echo keep_chan($record);

	// Genealogical facts (e.g. for INDI and FAM records) can have 2 SOUR/NOTE/OBJE/ASSO/RESN ...
	if ($level0type === 'INDI' || $level0type === 'FAM') {
		// ... but not facts which are simply links to other records
		if ($fact !== 'OBJE' && $fact !== 'NOTE' && $fact !== 'SHARED_NOTE' && $fact !== 'REPO' && $fact !== 'SOUR' && $fact !== 'SUBM' && $fact !== 'ASSO' && $fact !== 'ALIA' && $fact !== 'SEX') {
			echo view('cards/add-source-citation', [
				'level'          => 2,
				'full_citations' => $controller->tree()->getPreference('FULL_SOURCES'),
			]);
			if ($controller->tree()->getPreference('MEDIA_UPLOAD') >= Auth::accessLevel($controller->tree())) {
				echo view('cards/add-media-object', [
					'level' => 2,
				]);
			}
			// Don’t add notes to notes!
			if ($fact !== 'NOTE') {
				echo view('cards/add-note', [
					'level' => 2,
				]);
				echo view('cards/add-shared-note', [
					'level' => 2,
				]);
			}
			echo view('cards/add-associate', [
				'id'    => Uuid::uuid4()->toString(),
				'level' => 2,
			]);
			// allow to add godfather and godmother for CHR fact or best man and bridesmaid  for MARR fact in one window
			if (in_array($fact, Config::twoAssociates())) {
				echo view('cards/add-associate', [
					'id'    => Uuid::uuid4()->toString(),
					'level' => 2,
				]);
			}
			echo view('cards/add-restriction', [
				'level' => 2,
			]);
		}
	}
	?>
	<div class="row form-group">
		<div class="col-sm-9 offset-sm-3">
			<button class="btn btn-primary" type="submit">
				<?= FontAwesome::decorativeIcon('save') ?>
				<?= /* I18N: A button label. */
				I18N::translate('save') ?>
			</button>
			<a class="btn btn-secondary" href="<?= e($record->url()) ?>">
				<?= FontAwesome::decorativeIcon('cancel') ?>
				<?= /* I18N: A button label. */
				I18N::translate('cancel') ?>
			</a>
		</div>
	</div>
	</form>
	<?php
	echo view('modals/on-screen-keyboard');
	echo view('modals/ajax');

	break;

case 'update':
	//////////////////////////////////////////////////////////////////////////////
	// Save a new/updated fact
	//////////////////////////////////////////////////////////////////////////////
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$fact_id   = Filter::post('fact_id');
	$keep_chan = Filter::postBool('keep_chan');

	$record = GedcomRecord::getInstance($xref, $controller->tree());
	check_record_access($record);

	// Arrays for each GEDCOM line
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	// If the fact has a DATE or PLAC, then delete any value of Y
	if ($text[0] === 'Y') {
		foreach ($tag as $n => $value) {
			if ($glevels[$n] == 2 && ($value === 'DATE' || $value === 'PLAC') && $text[$n] !== '') {
				$text[0] = '';
				break;
			}
		}
	}

	$newged = '';
	if (!empty($_POST['NAME'])) {
		$newged .= "\n1 NAME " . $_POST['NAME'];
		$name_facts = ['TYPE', 'NPFX', 'GIVN', 'NICK', 'SPFX', 'SURN', 'NSFX'];
		foreach ($name_facts as $name_fact) {
			if (!empty($_POST[$name_fact])) {
				$newged .= "\n2 " . $name_fact . ' ' . $_POST[$name_fact];
			}
		}
	}

	if (isset($_POST['NOTE'])) {
		$NOTE = $_POST['NOTE'];
	}
	if (!empty($NOTE)) {
		$tempnote = preg_split('/\r?\n/', trim($NOTE) . "\n"); // make sure only one line ending on the end
		$title[]  = '0 @' . $xref . '@ NOTE ' . array_shift($tempnote);
		foreach ($tempnote as &$line) {
			$line = trim('1 CONT ' . $line, ' ');
		}
	}

	$newged = FunctionsEdit::handleUpdates($newged);

	// Add new names after existing names
	if (!empty($_POST['NAME'])) {
		preg_match_all('/[_0-9A-Z]+/', $controller->tree()->getPreference('ADVANCED_NAME_FACTS'), $match);
		$name_facts = array_unique(array_merge(['_MARNM'], $match[0]));
		foreach ($name_facts as $name_fact) {
			// Ignore advanced facts that duplicate standard facts.
			if (!in_array($name_fact, ['TYPE', 'NPFX', 'GIVN', 'NICK', 'SPFX', 'SURN', 'NSFX']) && !empty($_POST[$name_fact])) {
				$newged .= "\n2 " . $name_fact . ' ' . $_POST[$name_fact];
			}
		}
	}

	$newged = substr($newged, 1); // Remove leading newline

	/** @var CensusAssistantModule $census_assistant */
	$census_assistant = Module::getModuleByName('GEDFact_assistant');
	if ($census_assistant !== null && $record instanceof Individual) {
		$newged = $census_assistant->updateCensusAssistant($record, $fact_id, $newged, $keep_chan);
	}

	$record->updateFact($fact_id, $newged, !$keep_chan);

	// For the GEDFact_assistant module
	$pid_array = Filter::post('pid_array');
	if ($pid_array) {
		foreach (explode(',', $pid_array) as $pid) {
			if ($pid !== $xref) {
				$indi = Individual::getInstance($pid, $controller->tree());
				if ($indi && $indi->canEdit()) {
					$indi->updateFact($fact_id, $newged, !$keep_chan);
				}
			}
		}
	}

	header('Location: ' . $record->url());
	break;

case 'add_child_to_family_action':
	//////////////////////////////////////////////////////////////////////////////
	// Add a child to an existing family
	//////////////////////////////////////////////////////////////////////////////
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$PEDI      = Filter::post('PEDI');
	$keep_chan = Filter::postBool('keep_chan');
	$glevels   = Filter::postArray('glevels', '[0-9]');
	$tag       = Filter::postArray('tag', WT_REGEX_TAG);
	$text      = Filter::postArray('text');
	$islink    = Filter::postArray('islink', '[01]');

	$family = Family::getInstance($xref, $controller->tree());
	check_record_access($family);

	FunctionsEdit::splitSource();
	$gedrec = '0 @REF@ INDI';
	$gedrec .= FunctionsEdit::addNewName();
	$gedrec .= FunctionsEdit::addNewSex();
	if (preg_match_all('/([A-Z0-9_]+)/', $controller->tree()->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$gedrec .= FunctionsEdit::addNewFact($match);
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
	$xref    = Filter::post('xref', WT_REGEX_XREF);
	$PEDI    = Filter::post('PEDI');
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	$person = Individual::getInstance($xref, $controller->tree());
	check_record_access($person);

	// Create a family
	if ($person->getSex() === 'F') {
		$gedcom = "0 @NEW@ FAM\n1 WIFE @" . $person->getXref() . '@';
	} else {
		$gedcom = "0 @NEW@ FAM\n1 HUSB @" . $person->getXref() . '@';
	}
	$family = $person->getTree()->createRecord($gedcom);

	// Link the parent to the family
	$person->createFact('1 FAMS @' . $family->getXref() . '@', true);

	// Create a child
	FunctionsEdit::splitSource(); // separate SOUR record from the rest

	$gedcom = '0 @NEW@ INDI';
	$gedcom .= FunctionsEdit::addNewName();
	$gedcom .= FunctionsEdit::addNewSex();
	$gedcom .= "\n" . GedcomCodePedi::createNewFamcPedi($PEDI, $family->getXref());
	if (preg_match_all('/([A-Z0-9_]+)/', $controller->tree()->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$gedcom .= FunctionsEdit::addNewFact($match);
		}
	}
	if (Filter::postBool('SOUR_INDI')) {
		$gedcom = FunctionsEdit::handleUpdates($gedcom);
	} else {
		$gedcom = FunctionsEdit::updateRest($gedcom);
	}

	$child = $person->getTree()->createRecord($gedcom);

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
	$xref    = Filter::post('xref', WT_REGEX_XREF);
	$PEDI    = Filter::post('PEDI');
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	$person = Individual::getInstance($xref, $controller->tree());
	check_record_access($person);

	// Create a new family
	$gedcom = "0 @NEW@ FAM\n1 CHIL @" . $person->getXref() . '@';
	$family = $person->getTree()->createRecord($gedcom);

	// Link the child to the family
	$person->createFact('1 FAMC @' . $family->getXref() . '@', true);

	// Create a child
	FunctionsEdit::splitSource(); // separate SOUR record from the rest

	$gedcom = '0 @NEW@ INDI';
	$gedcom .= FunctionsEdit::addNewName();
	$gedcom .= FunctionsEdit::addNewSex();
	if (preg_match_all('/([A-Z0-9_]+)/', $controller->tree()->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$gedcom .= FunctionsEdit::addNewFact($match);
		}
	}
	if (Filter::postBool('SOUR_INDI')) {
		$gedcom = FunctionsEdit::handleUpdates($gedcom);
	} else {
		$gedcom = FunctionsEdit::updateRest($gedcom);
	}
	$gedcom .= "\n1 FAMS @" . $family->getXref() . '@';

	$parent = $person->getTree()->createRecord($gedcom);

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
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	FunctionsEdit::splitSource();
	$gedrec = '0 @REF@ INDI';
	$gedrec .= FunctionsEdit::addNewName();
	$gedrec .= FunctionsEdit::addNewSex();
	if (preg_match_all('/([A-Z0-9_]+)/', $controller->tree()->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$gedrec .= FunctionsEdit::addNewFact($match);
		}
	}
	if (Filter::postBool('SOUR_INDI')) {
		$gedrec = FunctionsEdit::handleUpdates($gedrec);
	} else {
		$gedrec = FunctionsEdit::updateRest($gedrec);
	}

	$new_indi = $controller->tree()->createRecord($gedrec);

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
	$xref    = Filter::post('xref'); // Add a spouse to this individual
	$sex     = Filter::post('SEX', '[MFU]', 'U');
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	$person = Individual::getInstance($xref, $controller->tree());
	check_record_access($person);

	FunctionsEdit::splitSource();
	$indi_gedcom = '0 @REF@ INDI';
	$indi_gedcom .= FunctionsEdit::addNewName();
	$indi_gedcom .= FunctionsEdit::addNewSex();
	if (preg_match_all('/([A-Z0-9_]+)/', $controller->tree()->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$indi_gedcom .= FunctionsEdit::addNewFact($match);
		}
	}
	if (Filter::postBool('SOUR_INDI')) {
		$indi_gedcom = FunctionsEdit::handleUpdates($indi_gedcom);
	} else {
		$indi_gedcom = FunctionsEdit::updateRest($indi_gedcom);
	}

	$fam_gedcom = '';
	if (preg_match_all('/([A-Z0-9_]+)/', $controller->tree()->getPreference('QUICK_REQUIRED_FAMFACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$fam_gedcom .= FunctionsEdit::addNewFact($match);
		}
	}
	if (Filter::postBool('SOUR_FAM')) {
		$fam_gedcom = FunctionsEdit::handleUpdates($fam_gedcom);
	} else {
		$fam_gedcom = FunctionsEdit::updateRest($fam_gedcom);
	}

	// Create the new spouse
	$spouse = $person->getTree()->createRecord($indi_gedcom);
	// Create a new family
	if ($sex === 'F') {
		$family = $spouse->getTree()->createRecord("0 @NEW@ FAM\n1 WIFE @" . $spouse->getXref() . "@\n1 HUSB @" . $person->getXref() . '@' . $fam_gedcom);
	} else {
		$family = $spouse->getTree()->createRecord("0 @NEW@ FAM\n1 HUSB @" . $spouse->getXref() . "@\n1 WIFE @" . $person->getXref() . '@' . $fam_gedcom);
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
	$xref    = Filter::post('xref', WT_REGEX_XREF);
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	$family = Family::getInstance($xref, $controller->tree());
	check_record_access($family);

	// Create the new spouse
	FunctionsEdit::splitSource(); // separate SOUR record from the rest

	$gedrec = '0 @REF@ INDI';
	$gedrec .= FunctionsEdit::addNewName();
	$gedrec .= FunctionsEdit::addNewSex();
	if (preg_match_all('/([A-Z0-9_]+)/', $controller->tree()->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$gedrec .= FunctionsEdit::addNewFact($match);
		}
	}

	if (Filter::postBool('SOUR_INDI')) {
		$gedrec = FunctionsEdit::handleUpdates($gedrec);
	} else {
		$gedrec = FunctionsEdit::updateRest($gedrec);
	}
	$gedrec .= "\n1 FAMS @" . $family->getXref() . '@';
	$spouse = $family->getTree()->createRecord($gedrec);

	// Update the existing family - add marriage, etc
	if ($family->getFirstFact('HUSB')) {
		$family->createFact('1 WIFE @' . $spouse->getXref() . '@', true);
	} else {
		$family->createFact('1 HUSB @' . $spouse->getXref() . '@', true);
	}
	$famrec = '';
	if (preg_match_all('/([A-Z0-9_]+)/', $controller->tree()->getPreference('QUICK_REQUIRED_FAMFACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$famrec .= FunctionsEdit::addNewFact($match);
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

case 'addfamlink':
	//////////////////////////////////////////////////////////////////////////////
	// Link an individual to an existing family, as a child
	//////////////////////////////////////////////////////////////////////////////
	$xref = Filter::get('xref', WT_REGEX_XREF);

	$person = Individual::getInstance($xref, $controller->tree());
	check_record_access($person);

	$controller
		->setPageTitle($person->getFullName() . ' - ' . I18N::translate('Link this individual to an existing family as a child'))
		->pageHeader();

	?>
	<h2><?= $controller->getPageTitle() ?></h2>
	<form method="post">
		<input type="hidden" name="ged" value="<?= e($controller->tree()->getName()) ?>">
		<input type="hidden" name="action" value="linkfamaction">
		<input type="hidden" name="xref" value="<?= $person->getXref() ?>">
		<?= Filter::getCsrf() ?>

		<div class="row form-group">
			<label class="col-sm-3 col-form-label" for="famid">
				<?= I18N::translate('Family') ?>
			</label>
			<div class="col-sm-9">
				<?= FunctionsEdit::formControlFamily($controller->tree(), null, ['id' => 'famid', 'name' => 'famid']) ?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-sm-3 col-form-label" for="PEDI">
				<?= I18N::translate('Pedigree') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::select(GedcomCodePedi::getValues($person), '', ['id' => 'PEDI', 'name' => 'PEDI']) ?>
				<p class="small text-muted">
					<?= I18N::translate('A child may have more than one set of parents. The relationship between the child and the parents can be biological, legal, or based on local culture and tradition. If no pedigree is specified, then a biological relationship will be assumed.') ?>
				</p>
			</div>
		</div>

		<?= keep_chan($person) ?>

		<div class="row form-group">
			<div class="col-sm-9 offset-sm-3">
				<button class="btn btn-primary" type="submit">
					<?= FontAwesome::decorativeIcon('save') ?>
					<?= /* I18N: A button label. */
					I18N::translate('save') ?>
				</button>
				<a class="btn btn-secondary" href="<?= e($person->url()) ?>">
					<?= FontAwesome::decorativeIcon('cancel') ?>
					<?= /* I18N: A button label. */
					I18N::translate('cancel') ?>
				</a>
			</div>
		</div>
	</form>
	<?php
	break;

case 'linkfamaction':
	//////////////////////////////////////////////////////////////////////////////
	// Link an individual to an existing family, as a child
	//////////////////////////////////////////////////////////////////////////////
	$xref  = Filter::post('xref', WT_REGEX_XREF);
	$famid = Filter::post('famid', WT_REGEX_XREF);
	$PEDI  = Filter::post('PEDI');

	$person = Individual::getInstance($xref, $controller->tree());
	$family = Family::getInstance($famid, $controller->tree());
	check_record_access($person);
	check_record_access($family);

	// Replace any existing child->family link (we may be changing the PEDI);
	$fact_id = null;
	foreach ($person->getFacts('FAMC') as $fact) {
		if ($family === $fact->getTarget()) {
			$fact_id = $fact->getFactId();
			break;
		}
	}

	$gedcom = GedcomCodePedi::createNewFamcPedi($PEDI, $famid);
	$person->updateFact($fact_id, $gedcom, true);

	// Only set the family->child link if it does not already exist
	$edit_fact = null;
	foreach ($family->getFacts('CHIL') as $fact) {
		if ($person === $fact->getTarget()) {
			$edit_fact = $fact;
			break;
		}
	}
	if (!$edit_fact) {
		$family->createFact('1 CHIL @' . $person->getXref() . '@', true);
	}

	header('Location: ' . $person->url());
	break;

case 'linkspouse':
	//////////////////////////////////////////////////////////////////////////////
	// Link and individual to an existing individual as a spouse
	//////////////////////////////////////////////////////////////////////////////
	$famtag = Filter::get('famtag', 'HUSB|WIFE');
	$xref   = Filter::get('xref', WT_REGEX_XREF);

	$person = Individual::getInstance($xref, $controller->tree());
	check_record_access($person);

	if ($person->getSex() === 'F') {
		$controller->setPageTitle($person->getFullName() . ' - ' . I18N::translate('Add a husband using an existing individual'));
		$label = I18N::translate('Husband');
	} else {
		$controller->setPageTitle($person->getFullName() . ' - ' . I18N::translate('Add a wife using an existing individual'));
		$label = I18N::translate('Wife');
	}

	$controller->pageHeader();
	FunctionsPrint::initializeCalendarPopup();

	?>
	<h2><?= $controller->getPageTitle() ?></h2>

	<form method="post">
		<input type="hidden" name="ged" value="<?= e($controller->tree()->getName()) ?>">
		<input type="hidden" name="action" value="linkspouseaction">
		<input type="hidden" name="xref" value="<?= $person->getXref() ?>">
		<input type="hidden" name="famtag" value="<?= $famtag ?>">
		<?= Filter::getCsrf() ?>
		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="spouse">
				<?= $label ?>
			</label>
			<div class="col-sm-9">
				<?= FunctionsEdit::formControlIndividual($controller->tree(), null, ['id' => 'spouse', 'name' => 'spid']) ?>
			</div>
		</div>

		<?= FunctionsEdit::addSimpleTag('0 MARR Y') ?>
		<?= FunctionsEdit::addSimpleTag('0 DATE', 'MARR') ?>
		<?= FunctionsEdit::addSimpleTag('0 PLAC', 'MARR') ?>

		<div class="row form-group">
			<div class="col-sm-9 offset-sm-3">
				<button class="btn btn-primary" type="submit">
					<?= FontAwesome::decorativeIcon('save') ?>
					<?= /* I18N: A button label. */ I18N::translate('save') ?>
				</button>
				<a class="btn btn-secondary" href="<?= e($person->url()) ?>">
					<?= FontAwesome::decorativeIcon('cancel') ?>
					<?= /* I18N: A button label. */ I18N::translate('cancel') ?>
				</a>
			</div>
		</div>
	</form>
	<?php
	break;

case 'linkspouseaction':
	//////////////////////////////////////////////////////////////////////////////
	// Link and individual to an existing individual as a spouse
	//////////////////////////////////////////////////////////////////////////////
	$xref    = Filter::post('xref', WT_REGEX_XREF);
	$spid    = Filter::post('spid', WT_REGEX_XREF);
	$famtag  = Filter::post('famtag', 'HUSB|WIFE');
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	$person = Individual::getInstance($xref, $controller->tree());
	$spouse = Individual::getInstance($spid, $controller->tree());
	check_record_access($person);
	check_record_access($spouse);

	if ($person->getSex() === 'F') {
		$controller->setPageTitle($person->getFullName() . ' - ' . I18N::translate('Add a husband using an existing individual'));
	} else {
		$controller->setPageTitle($person->getFullName() . ' - ' . I18N::translate('Add a wife using an existing individual'));
	}

	if ($person->getSex() === 'M') {
		$gedcom = "0 @new@ FAM\n1 HUSB @" . $person->getXref() . "@\n1 WIFE @" . $spouse->getXref() . '@';
	} else {
		$gedcom = "0 @new@ FAM\n1 HUSB @" . $spouse->getXref() . "@\n1 WIFE @" . $person->getXref() . '@';
	}
	FunctionsEdit::splitSource();
	$gedcom .= FunctionsEdit::addNewFact('MARR');

	if (Filter::postBool('SOUR_FAM') || count($tagSOUR) > 0) {
		// before adding 2 SOUR it needs to add 1 MARR Y first
		if (FunctionsEdit::addNewFact('MARR') === '') {
			$gedcom .= "\n1 MARR Y";
		}
		$gedcom = FunctionsEdit::handleUpdates($gedcom);
	} else {
		// before adding level 2 facts it needs to add 1 MARR Y first
		if (FunctionsEdit::addNewFact('MARR') === '') {
			$gedcom .= "\n1 MARR Y";
		}
		$gedcom = FunctionsEdit::updateRest($gedcom);
	}

	$family = $person->getTree()->createRecord($gedcom);
	$person->createFact('1 FAMS @' . $family->getXref() . '@', true);
	$spouse->createFact('1 FAMS @' . $family->getXref() . '@', true);

	header('Location: ' . $person->url());
	break;

case 'addmedia_links':
	//////////////////////////////////////////////////////////////////////////////
	//
	//////////////////////////////////////////////////////////////////////////////
	$pid = Filter::get('pid', WT_REGEX_XREF);

	$person = Individual::getInstance($pid, $controller->tree());
	check_record_access($person);

	$controller
		->setPageTitle(I18N::translate('Family navigator') . ' — ' . $person->getFullName())
		->pageHeader();

	?>
	<h2><?= $controller->getPageTitle() ?></h2>

	<form method="post" action="edit_interface.php?xref=<?= $person->getXref() ?>" onsubmit="findindi()">
		<input type="hidden" name="ged" value="<?= e($controller->tree()->getName()) ?>">
		<input type="hidden" name="action" value="addmedia_links">
		<input type="hidden" name="noteid" value="newnote">
		<?= Filter::getCsrf() ?>
		<?php require WT_ROOT . WT_MODULES_DIR . 'GEDFact_assistant/MEDIA_ctrl.php' ?>
	</form>
	<?php
	break;

case 'add-media-link':
	//////////////////////////////////////////////////////////////////////////////
	// Link a media object to a record.
	//////////////////////////////////////////////////////////////////////////////
	$xref   = Filter::get('xref', WT_REGEX_XREF);
	$record = GedcomRecord::getInstance($xref, $controller->tree());
	check_record_access($record);

	$controller
		->setPageTitle($record->getFullName() . ' — ' . I18N::translate('Add a media object'))
		->pageHeader();

	?>
	<h2><?= $controller->getPageTitle() ?></h2>

	<form method="post">
		<input type="hidden" name="ged" value="<?= e($record->getTree()->getName()) ?>">
		<input type="hidden" name="xref" value="<?= e($record->getXref()) ?>">
		<input type="hidden" name="action" value="save-media-link">
		<?= Filter::getCsrf() ?>

		<div class="row form-group">
			<label class="col-sm-3 col-form-label" for="media-xref">
				<?= I18N::translate('Media object') ?>
			</label>
			<div class="col-sm-9">
				<div class="input-group">
					<?php if ($record->getTree()->getPreference('MEDIA_UPLOAD') >= Auth::accessLevel($record->getTree())): ?>
						<span class="input-group-btn">
							<button class="btn btn-secondary" type="button" data-toggle="modal" data-href="<?= e(route('create-media-object', ['tree' => $record->getTree()->getName()])) ?>" data-target="#wt-ajax-modal" data-select-id="media-xref" title="<?= I18N::translate('Create a media object') ?>">
								<i class="fas fa-plus" aria-hidden="true" title="<?= I18N::translate('add') ?>"></i>
								<span class="sr-only"><?= I18N::translate('add') ?></span>
							</button>
						</span>
					<?php endif ?>
					<?= FunctionsEdit::formControlMediaObject($controller->tree(), null, ['id' => 'media-xref', 'name' => 'media-xref', 'data-element-id' => 'media-xref']) ?>
				</div>
			</div>
		</div>

		<div class="row form-group">
			<div class="col-sm-9 offset-sm-3">
				<button class="btn btn-primary" type="submit">
					<?= FontAwesome::decorativeIcon('save') ?>
					<?= /* I18N: A button label. */
					I18N::translate('save') ?>
				</button>
				<a class="btn btn-secondary" href="<?= e($record->url()) ?>">
					<?= FontAwesome::decorativeIcon('cancel') ?>
					<?= /* I18N: A button label. */
					I18N::translate('cancel') ?>
				</a>
			</div>
		</div>
	</form>
	<?= view('modals/ajax') ?>
	<?php
	break;

case 'save-media-link':
	//////////////////////////////////////////////////////////////////////////////
	// Link a media object to a record.
	//////////////////////////////////////////////////////////////////////////////
	$xref       = Filter::post('xref', WT_REGEX_XREF);
	$media_xref = Filter::post('media-xref', WT_REGEX_XREF);
	$record     = GedcomRecord::getInstance($xref, $controller->tree());
	check_record_access($record);

	$gedcom = '1 OBJE @' . $media_xref . '@';

	$record->createFact($gedcom, true);

	header('Location: ' . $record->url());
	break;

case 'changefamily':
	//////////////////////////////////////////////////////////////////////////////
	// Change the members of a family record
	//////////////////////////////////////////////////////////////////////////////
	$xref = Filter::get('xref', WT_REGEX_XREF);

	$family = Family::getInstance($xref, $controller->tree());
	check_record_access($family);

	$controller
		->setPageTitle(I18N::translate('Change family members') . ' – ' . $family->getFullName())
		->pageHeader();

	$father   = $family->getHusband();
	$mother   = $family->getWife();
	$children = $family->getChildren();
	?>
	<h2><?= $controller->getPageTitle() ?></h2>

	<div id="changefam">
		<form name="changefamform" method="post">
			<input type="hidden" name="ged" value="<?= e($controller->tree()->getName()) ?>">
			<input type="hidden" name="action" value="changefamily_update">
			<input type="hidden" name="xref" value="<?= $xref ?>">
			<?= Filter::getCsrf() ?>
			<table>
				<tr>
					<?php if ($father) { ?>
						<td class="descriptionbox">
							<b>
								<?php
								switch ($father->getSex()) {
									case 'M':
										echo I18N::translate('husband');
										break;
									case 'F':
										echo I18N::translate('wife');
										break;
									default:
										echo I18N::translate('spouse');
										break;
								}
								?>
							</b>
							<input type="hidden" name="HUSB" value="<?= $father->getXref() ?>">
						</td>
						<td id="HUSBName" class="optionbox"><?= $father->getFullName() ?>
						</td>
					<?php } else { ?>
						<td class="descriptionbox">
							<b><?= I18N::translate('spouse') ?></b>
							<input type="hidden" name="HUSB" value="">
						</td>
						<td id="HUSBName" class="optionbox">
						</td>
					<?php } ?>
					<td class="optionbox">
						<a href="#" id="husbrem" style="display: <?= is_null($father) ? 'none' : 'block' ?>;"
						   onclick="document.changefamform.HUSB.value=''; document.getElementById('HUSBName').innerHTML=''; this.style.display='none'; return false;">
							<?= I18N::translate('Remove') ?>
						</a>
					</td>
					<td class="optionbox">
					</td>
				</tr>
				<tr>
					<?php if ($mother) { ?>
						<td class="descriptionbox">
							<b>
								<?php
								switch ($mother->getSex()) {
									case 'M':
										echo I18N::translate('husband');
										break;
									case 'F':
										echo I18N::translate('wife');
										break;
									default:
										echo I18N::translate('spouse');
										break;
								}
								?>
							</b>
							<input type="hidden" name="WIFE" value="<?= $mother->getXref() ?>">
						</td>
						<td id="WIFEName" class="optionbox">
							<?= $mother->getFullName() ?>
						</td>
					<?php } else { ?>
						<td class="descriptionbox">
							<b><?= I18N::translate('spouse') ?></b>
							<input type="hidden" name="WIFE" value="">
						</td>
						<td id="WIFEName" class="optionbox">
						</td>
					<?php } ?>
					<td class="optionbox">
						<a href="#" id="wiferem" style="display: <?= is_null($mother) ? 'none' : 'block' ?>;"
						   onclick="document.changefamform.WIFE.value=''; document.getElementById('WIFEName').innerHTML=''; this.style.display='none'; return false;">
							<?= I18N::translate('Remove') ?>
						</a>
					</td>
					<td class="optionbox">
					</td>
				</tr>
				<?php $i = 0;
				foreach ($children as $child) { ?>
					<tr>
						<td class="descriptionbox">
							<b>
								<?php
								switch ($child->getSex()) {
									case 'M':
										echo I18N::translate('son');
										break;
									case 'F':
										echo I18N::translate('daughter');
										break;
									default:
										echo I18N::translate('child');
										break;
								}
								?>
							</b>
							<input type="hidden" name="CHIL<?= $i ?>" value="<?= $child->getXref() ?>">
						</td>
						<td id="CHILName<?= $i ?>" class="optionbox"><?= $child->getFullName() ?>
						</td>
						<td class="optionbox">
							<a href="#" id="childrem<?= $i ?>" style="display: block;"
							   onclick="document.changefamform.CHIL<?= $i ?>.value=''; document.getElementById('CHILName<?= $i ?>').innerHTML=''; this.style.display='none'; return false;">
								<?= I18N::translate('Remove') ?>
							</a>
						</td>
						<td class="optionbox">
						</td>
					</tr>
					<?php $i++;
				} ?>
				<tr>
					<td class="descriptionbox">
						<b><?= I18N::translate('child') ?></b>
						<input type="hidden" name="CHIL<?= $i ?>" value="">
					</td>
					<td id="CHILName<?= $i ?>" class="optionbox">
					</td>
					<td colspan="2" class="optionbox child">
						<a href="#" id="childrem<?= $i ?>" style="display: none;"
						   onclick="document.changefamform.CHIL<?= $i ?>.value=''; document.getElementById('CHILName<?= $i ?>').innerHTML=''; this.style.display='none'; return false;">
							<?= I18N::translate('Remove') ?>
						</a>
					</td>
				</tr>
			</table>
			<div class="row form-group">
				<div class="col-sm-9 offset-sm-3">
					<button class="btn btn-primary" type="submit">
						<?= FontAwesome::decorativeIcon('save') ?>
						<?= /* I18N: A button label. */
						I18N::translate('save') ?>
					</button>
					<a class="btn btn-secondary" href="<?= e($family->url()) ?>">
						<?= FontAwesome::decorativeIcon('cancel') ?>
						<?= /* I18N: A button label. */
						I18N::translate('cancel') ?>
					</a>
				</div>
			</div>
		</form>
	</div>
	<?php
	break;

case 'changefamily_update':
	//////////////////////////////////////////////////////////////////////////////
	// Change the members of a family record
	//////////////////////////////////////////////////////////////////////////////
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$HUSB      = Filter::post('HUSB', WT_REGEX_XREF);
	$WIFE      = Filter::post('WIFE', WT_REGEX_XREF);
	$keep_chan = Filter::postBool('keep_chan');

	$CHIL = [];
	for ($i = 0; isset($_POST['CHIL' . $i]); ++$i) {
		$CHIL[] = Filter::post('CHIL' . $i, WT_REGEX_XREF);
	}

	$family = Family::getInstance($xref, $controller->tree());
	check_record_access($family);

	// Current family members
	$old_father   = $family->getHusband();
	$old_mother   = $family->getWife();
	$old_children = $family->getChildren();

	// New family members
	$new_father   = Individual::getInstance($HUSB, $controller->tree());
	$new_mother   = Individual::getInstance($WIFE, $controller->tree());
	$new_children = [];
	foreach ($CHIL as $child) {
		$new_children[] = Individual::getInstance($child, $controller->tree());
	}

	if ($old_father !== $new_father) {
		if ($old_father) {
			// Remove old FAMS link
			foreach ($old_father->getFacts('FAMS') as $fact) {
				if ($fact->getTarget() === $family) {
					$old_father->deleteFact($fact->getFactId(), !$keep_chan);
				}
			}
			// Remove old HUSB link
			foreach ($family->getFacts('HUSB|WIFE') as $fact) {
				if ($fact->getTarget() === $old_father) {
					$family->deleteFact($fact->getFactId(), !$keep_chan);
				}
			}
		}
		if ($new_father) {
			// Add new FAMS link
			$new_father->createFact('1 FAMS @' . $family->getXref() . '@', !$keep_chan);
			// Add new HUSB link
			$family->createFact('1 HUSB @' . $new_father->getXref() . '@', !$keep_chan);
		}
	}

	if ($old_mother !== $new_mother) {
		if ($old_mother) {
			// Remove old FAMS link
			foreach ($old_mother->getFacts('FAMS') as $fact) {
				if ($fact->getTarget() === $family) {
					$old_mother->deleteFact($fact->getFactId(), !$keep_chan);
				}
			}
			// Remove old WIFE link
			foreach ($family->getFacts('HUSB|WIFE') as $fact) {
				if ($fact->getTarget() === $old_mother) {
					$family->deleteFact($fact->getFactId(), !$keep_chan);
				}
			}
		}
		if ($new_mother) {
			// Add new FAMS link
			$new_mother->createFact('1 FAMS @' . $family->getXref() . '@', !$keep_chan);
			// Add new WIFE link
			$family->createFact('1 WIFE @' . $new_mother->getXref() . '@', !$keep_chan);
		}
	}

	foreach ($old_children as $old_child) {
		if ($old_child && !in_array($old_child, $new_children)) {
			// Remove old FAMC link
			foreach ($old_child->getFacts('FAMC') as $fact) {
				if ($fact->getTarget() === $family) {
					$old_child->deleteFact($fact->getFactId(), !$keep_chan);
				}
			}
			// Remove old CHIL link
			foreach ($family->getFacts('CHIL') as $fact) {
				if ($fact->getTarget() === $old_child) {
					$family->deleteFact($fact->getFactId(), !$keep_chan);
				}
			}
		}
	}

	foreach ($new_children as $new_child) {
		if ($new_child && !in_array($new_child, $old_children)) {
			// Add new FAMC link
			$new_child->createFact('1 FAMC @' . $family->getXref() . '@', !$keep_chan);
			// Add new CHIL link
			$family->createFact('1 CHIL @' . $new_child->getXref() . '@', !$keep_chan);
		}
	}

	header('Location: ' . $family->url());
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
