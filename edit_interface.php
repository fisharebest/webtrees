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

require 'includes/session.php';

$action = Filter::post('action', null, Filter::get('action'));

$controller = new PageController;
$controller
	->restrictAccess(Auth::isEditor($controller->tree()))
	->addInlineJavascript('var locale_date_format="' . preg_replace('/[^DMY]/', '', str_replace(['j', 'F'], ['D', 'M'], I18N::dateFormat())) . '";');

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
				FunctionsEdit::printAddLayer('NOTE');
				FunctionsEdit::printAddLayer('SHARED_NOTE');
			}
			break;
		case 'FAM':
		case 'INDI':
			// FAM and INDI records have real facts. They can take NOTE/SOUR/OBJE/etc.
			if ($level1type !== 'SEX' && $level1type !== 'NOTE' && $level1type !== 'ALIA') {
				if ($level1type !== 'SOUR') {
					FunctionsEdit::printAddLayer('SOUR');
				}
				if ($level1type !== 'OBJE') {
					FunctionsEdit::printAddLayer('OBJE');
				}
				FunctionsEdit::printAddLayer('NOTE');
				FunctionsEdit::printAddLayer('SHARED_NOTE', 2, $level1type);
				if ($level1type !== 'ASSO' && $level1type !== 'NOTE' && $level1type !== 'SOUR') {
					FunctionsEdit::printAddLayer('ASSO');
				}
				// allow to add godfather and godmother for CHR fact or best man and bridesmaid  for MARR fact in one window
				if (in_array($level1type, Config::twoAssociates())) {
					FunctionsEdit::printAddLayer('ASSO2');
				}
				if ($level1type !== 'SOUR') {
					FunctionsEdit::printAddLayer('RESN');
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
			FunctionsEdit::printAddLayer('SOUR');
			FunctionsEdit::printAddLayer('OBJE');
			// Don’t add notes to notes!
			if ($fact !== 'NOTE') {
				FunctionsEdit::printAddLayer('NOTE');
				FunctionsEdit::printAddLayer('SHARED_NOTE', 2, $fact);
			}
			FunctionsEdit::printAddLayer('ASSO');
			// allow to add godfather and godmother for CHR fact or best man and bridesmaid  for MARR fact in one window
			if (in_array($fact, Config::twoAssociates())) {
				FunctionsEdit::printAddLayer('ASSO2');
			}
			FunctionsEdit::printAddLayer('RESN');
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

	if (!Filter::checkCsrf()) {
		$prev_action = Filter::post('prev_action', 'add|edit|addname|editname');
		$fact_type   = Filter::post('fact_type', WT_REGEX_TAG);
		header('Location: edit_interface.php?action=' . $prev_action . '&xref=' . $xref . '&fact_id=' . $fact_id . '&fact=' . $fact_type);
		break;
	}

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

case 'editnote':
	//////////////////////////////////////////////////////////////////////////////
	// Edit a note record
	//////////////////////////////////////////////////////////////////////////////
	$xref = Filter::get('xref', WT_REGEX_XREF);
	$note = Note::getInstance($xref, $controller->tree());
	check_record_access($note);
	$controller
		->setPageTitle(I18N::translate('Edit the shared note'))
		->pageHeader();
	?>
	<h2><?= $controller->getPageTitle() ?></h2>

	<form method="post">
		<input type="hidden" name="ged" value="<?= e($controller->tree()->getName()) ?>">
		<input type="hidden" name="action" value="editnoteaction">
		<input type="hidden" name="xref" value="<?= $xref ?>">
		<?= Filter::getCsrf() ?>
		<table class="table wt-facts-table">
			<tr>
				<th scope="row"><?= I18N::translate('Shared note') ?></th>
				<td>
					<textarea name="NOTE" id="NOTE" rows="15" cols="90"><?= e($note->getNote()) ?></textarea>
					<br>
					<?= FunctionsEdit::inputAddonKeyboard('NOTE') ?>
				</td>
			</tr>
		</table>
		<?= keep_chan($note) ?>
		<div class="row form-group">
			<div class="col-sm-9 offset-sm-3">
				<button class="btn btn-primary" type="submit">
					<?= FontAwesome::decorativeIcon('save') ?>
					<?= /* I18N: A button label. */
					I18N::translate('save') ?>
				</button>
				<a class="btn btn-secondary" href="<?= e($note->url()) ?>">
					<?= FontAwesome::decorativeIcon('cancel') ?>
					<?= /* I18N: A button label. */
					I18N::translate('cancel') ?>
				</a>
			</div>
		</div>
	</form>
	<?php
	echo view('modals/on-screen-keyboard');
	break;

case 'editnoteaction':
	//////////////////////////////////////////////////////////////////////////////
	// Edit a note record
	//////////////////////////////////////////////////////////////////////////////
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$keep_chan = Filter::postBool('keep_chan');
	$note      = Filter::post('NOTE');
	if (!Filter::checkCsrf()) {
		header('Location: edit_interface.php?action=editnote&xref=' . $xref);
		break;
	}
	$record = Note::getInstance($xref, $controller->tree());
	check_record_access($record);
	// We have user-supplied data in a replacement string - escape it against backreferences
	$note = str_replace(['\\', '$'], ['\\\\', '\\$'], $note);
	$gedrec = preg_replace(
		'/^0 @' . $record->getXref() . '@ NOTE.*(\n1 CONT.*)*/',
		'0 @' . $record->getXref() . '@ NOTE ' . preg_replace("/\r?\n/", "\n1 CONT ", $note),
		$record->getGedcom()
	);
	$record->updateRecord($gedrec, !$keep_chan);
	header('Location: ' . $record->url());
	break;

case 'add_child_to_family':
	//////////////////////////////////////////////////////////////////////////////
	// Add a child to an existing family
	//////////////////////////////////////////////////////////////////////////////
	$xref   = Filter::get('xref', WT_REGEX_XREF);
	$gender = Filter::get('gender', '[MFU]', 'U');

	$family = Family::getInstance($xref, $controller->tree());
	check_record_access($family);

	$controller
		->setPageTitle($family->getFullName() . ' - ' . I18N::translate('Add a child'))
		->pageHeader();

	print_indi_form('add_child_to_family_action', null, $family, null, 'CHIL', $gender);
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

	if (!Filter::checkCsrf()) {
		$gender = Filter::get('gender', '[MFU]', 'U');
		header('Location: edit_interface.php?action=add_child_to_family&xref=' . $xref . '&gender=' . $gender);
		break;
	}

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

case 'add_child_to_individual':
	//////////////////////////////////////////////////////////////////////////////
	// Add a child to an existing individual (creating a one-parent family)
	//////////////////////////////////////////////////////////////////////////////
	$xref = Filter::get('xref', WT_REGEX_XREF);

	$person = Individual::getInstance($xref, $controller->tree());
	check_record_access($person);

	$controller
		->setPageTitle($person->getFullName() . ' - ' . I18N::translate('Add a child to create a one-parent family'))
		->pageHeader();

	print_indi_form('add_child_to_individual_action', $person, null, null, 'CHIL', 'U');
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

	if (!Filter::checkCsrf()) {
		header('Location: edit_interface.php?action=add_child_to_individual&xref=' . $xref);
		break;
	}

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

case 'add_parent_to_individual':
	//////////////////////////////////////////////////////////////////////////////
	// Add a new parent to an existing individual (creating a one-parent family)
	//////////////////////////////////////////////////////////////////////////////
	$xref   = Filter::get('xref', WT_REGEX_XREF);
	$gender = Filter::get('gender', '[MF]', 'U');

	$individual = Individual::getInstance($xref, $controller->tree());
	check_record_access($individual);

	if ($gender === 'F') {
		$controller->setPageTitle(I18N::translate('Add a mother'));
		$famtag = 'WIFE';
	} else {
		$controller->setPageTitle(I18N::translate('Add a father'));
		$famtag = 'HUSB';
	}
	$controller->pageHeader();

	print_indi_form('add_parent_to_individual_action', $individual, null, null, $famtag, $gender);
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

	if (!Filter::checkCsrf()) {
		$gender = Filter::get('gender', '[MFU]', 'U');
		header('Location: edit_interface.php?action=add_parent_to_individual&xref=' . $xref . '&gender=' . $gender);
		break;
	}

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

case 'add_unlinked_indi':
	//////////////////////////////////////////////////////////////////////////////
	// Add a new, unlinked individual
	//////////////////////////////////////////////////////////////////////////////
	$controller
		->restrictAccess(Auth::isManager($controller->tree()))
		->setPageTitle(I18N::translate('Create an individual'))
		->pageHeader();

	print_indi_form('add_unlinked_indi_action', null, null, null, null, null);
	break;

case 'add_unlinked_indi_action':
	//////////////////////////////////////////////////////////////////////////////
	// Add a new, unlinked individual
	//////////////////////////////////////////////////////////////////////////////
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	if (!Filter::checkCsrf()) {
		header('Location: edit_interface.php?action=add_unlinked_indi');
		break;
	}

	$controller->restrictAccess(Auth::isManager($controller->tree()));

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

case 'add_spouse_to_individual':
	//////////////////////////////////////////////////////////////////////////////
	// Add a spouse to an existing individual (creating a new family)
	//////////////////////////////////////////////////////////////////////////////
	$sex  = Filter::get('sex', 'M|F', 'F');
	$xref = Filter::get('xref', WT_REGEX_XREF);

	$individual = Individual::getInstance($xref, $controller->tree());
	check_record_access($individual);

	if ($sex === 'F') {
		$controller->setPageTitle(I18N::translate('Add a wife'));
		$famtag = 'WIFE';
	} else {
		$controller->setPageTitle(I18N::translate('Add a husband'));
		$famtag = 'HUSB';
	}
	$controller->pageHeader();

	print_indi_form('add_spouse_to_individual_action', $individual, null, null, $famtag, $sex);
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

	if (!Filter::checkCsrf()) {
		header('Location: edit_interface.php?action=add_spouse_to_individual&xref=' . $xref . '&sex=' . $sex);

		break;
	}

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

case 'add_spouse_to_family':
	//////////////////////////////////////////////////////////////////////////////
	// Add a spouse to an existing family
	//////////////////////////////////////////////////////////////////////////////
	$xref   = Filter::get('xref', WT_REGEX_XREF);
	$famtag = Filter::get('famtag', 'HUSB|WIFE');

	$family = Family::getInstance($xref, $controller->tree());
	check_record_access($family);

	if ($famtag === 'WIFE') {
		$controller->setPageTitle(I18N::translate('Add a wife'));
		$sex = 'F';
	} else {
		$controller->setPageTitle(I18N::translate('Add a husband'));
		$sex = 'M';
	}
	$controller->pageHeader();

	print_indi_form('add_spouse_to_family_action', null, $family, null, $famtag, $sex);
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

	if (!Filter::checkCsrf()) {
		$famtag = Filter::get('famtag', 'HUSB|WIFE');
		header('Location: edit_interface.php?action=add_spouse_to_family&xref=' . $xref . '&famtag=' . $famtag);

		break;
	}

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
	<form method="post" name="addchildform">
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

	if (!Filter::checkCsrf()) {
		header('Location: edit_interface.php?action=addfamlink&xref=' . $xref);
		break;
	}

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

	<form method="post" name="addchildform">
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

	if (!Filter::checkCsrf()) {
		$famtag = Filter::get('famtag', 'HUSB|WIFE');
		header('Location: edit_interface.php?action=linkspouse&xref=' . $xref . '&famtag=' . $famtag);

		break;
	}

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
	if (!Filter::checkCsrf()) {
		header('Location: edit_interface.php?action=addnewrepository&ged=' . $controller->tree()->getNameUrl());
		break;
	}
	$xref       = Filter::post('xref', WT_REGEX_XREF);
	$media_xref = Filter::post('media-xref', WT_REGEX_XREF);
	$record     = GedcomRecord::getInstance($xref, $controller->tree());
	check_record_access($record);

	$gedcom = '1 OBJE @' . $media_xref . '@';

	$record->createFact($gedcom, true);

	header('Location: ' . $record->url());
	break;

case 'editname':
	//////////////////////////////////////////////////////////////////////////////
	//
	//////////////////////////////////////////////////////////////////////////////
	$xref    = Filter::get('xref', WT_REGEX_XREF);
	$fact_id = Filter::get('fact_id');

	$person = Individual::getInstance($xref, $controller->tree());
	check_record_access($person);

	// Find the fact to edit
	$name_fact = null;
	foreach ($person->getFacts() as $fact) {
		if ($fact->getFactId() === $fact_id && $fact->canEdit()) {
			$name_fact = $fact;
		}
	}
	if (!$name_fact) {
		header('Location: ' . $person->url());
		break;
	}

	$controller
		->setPageTitle(I18N::translate('Edit the name'))
		->pageHeader();

	print_indi_form('update', $person, null, $name_fact, '', $person->getSex());
	echo view('modals/ajax');
	break;

case 'addname':
	//////////////////////////////////////////////////////////////////////////////
	//
	//////////////////////////////////////////////////////////////////////////////
	$xref = Filter::get('xref', WT_REGEX_XREF);

	$individual = Individual::getInstance($xref, $controller->tree());
	check_record_access($individual);

	$controller
		->setPageTitle($individual->getFullName() . ' — ' . I18N::translate('Add a name'))
		->pageHeader();

	print_indi_form('update', $individual, null, null, '', $individual->getSex());
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

	if (!Filter::checkCsrf()) {
		header('Location: edit_interface.php?action=changefamily&xref=' . $xref);
		break;
	}

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
 * Print a form to add an individual or edit an individual’s name
 *
 * @param string     $nextaction
 * @param Individual $person
 * @param Family     $family
 * @param Fact       $name_fact
 * @param string     $famtag
 * @param string     $gender
 */
function print_indi_form($nextaction, Individual $person = null, Family $family = null, Fact $name_fact = null, $famtag = 'CHIL', $gender = 'U') {
	global $bdm, $controller;

	if ($person) {
		$xref = $person->getXref();
		$cancel_url = $person->url();
	} elseif ($family) {
		$xref = $family->getXref();
		$cancel_url = $family->url();
	} else {
		$cancel_url = route('admin-trees');
		$xref = 'new';
	}

	// Different cultures do surnames differently
	$surname_tradition = SurnameTradition::create($controller->tree()->getPreference('SURNAME_TRADITION'));

	if ($name_fact !== null) {
		// Editing an existing name
		$name_fact_id = $name_fact->getFactId();
		$namerec      = $name_fact->getGedcom();
		$name_fields  = [
			'NAME' => $name_fact->getValue(),
			'TYPE' => $name_fact->getAttribute('TYPE'),
			'NPFX' => $name_fact->getAttribute('NPFX'),
			'GIVN' => $name_fact->getAttribute('GIVN'),
			'NICK' => $name_fact->getAttribute('NICK'),
			'SPFX' => $name_fact->getAttribute('SPFX'),
			'SURN' => $name_fact->getAttribute('SURN'),
			'NSFX' => $name_fact->getAttribute('NSFX'),
		];

		// Populate any missing subfields from the NAME field
		$npfx_accept = implode('|', Config::namePrefixes());
		if (preg_match('/(((' . $npfx_accept . ')\.? +)*)([^\n\/"]*)("(.*)")? *\/(([a-z]{2,3} +)*)(.*)\/ *(.*)/i', $name_fields['NAME'], $name_bits)) {
			$name_fields['NPFX'] = $name_fields['NPFX'] ?: $name_bits[1];
			$name_fields['GIVN'] = $name_fields['GIVN'] ?: $name_bits[4];
			$name_fields['NICK'] = $name_fields['NICK'] ?: $name_bits[6];
			$name_fields['SPFX'] = $name_fields['SPFX'] ?: trim($name_bits[7]);
			$name_fields['SURN'] = $name_fields['SURN'] ?: preg_replace('~/[^/]*/~', ',', $name_bits[9]);
			$name_fields['NSFX'] = $name_fields['NSFX'] ?: $name_bits[10];
		}
	} else {
		// Creating a new name
		$name_fact_id = null;
		$namerec      = null;
		$name_fields  = [
			'NAME' => '',
			'TYPE' => '',
			'NPFX' => '',
			'GIVN' => '',
			'NICK' => '',
			'SPFX' => '',
			'SURN' => '',
			'NSFX' => '',
		];

		// Inherit surname from parents, spouse or child
		if ($family) {
			$father = $family->getHusband();
			if ($father && $father->getFirstFact('NAME')) {
				$father_name = $father->getFirstFact('NAME')->getValue();
			} else {
				$father_name = '';
			}
			$mother = $family->getWife();
			if ($mother && $mother->getFirstFact('NAME')) {
				$mother_name = $mother->getFirstFact('NAME')->getValue();
			} else {
				$mother_name = '';
			}
		} else {
			$father      = null;
			$mother      = null;
			$father_name = '';
			$mother_name = '';
		}
		if ($person && $person->getFirstFact('NAME')) {
			$indi_name = $person->getFirstFact('NAME')->getValue();
		} else {
			$indi_name = '';
		}

		switch ($nextaction) {
			case 'add_child_to_family_action':
				$name_fields = array_merge($name_fields, $surname_tradition->newChildNames($father_name, $mother_name, $gender));
				break;
			case 'add_child_to_individual_action':
				if ($person->getSex() === 'F') {
					$name_fields = array_merge($name_fields, $surname_tradition->newChildNames('', $indi_name, $gender));
				} else {
					$name_fields = array_merge($name_fields, $surname_tradition->newChildNames($indi_name, '', $gender));
				}
				break;
			case 'add_parent_to_individual_action':
				$name_fields = array_merge($name_fields, $surname_tradition->newParentNames($indi_name, $gender));
				break;
			case 'add_spouse_to_family_action':
				if ($father) {
					$name_fields = array_merge($name_fields, $surname_tradition->newSpouseNames($father_name, $gender));
				} else {
					$name_fields = array_merge($name_fields, $surname_tradition->newSpouseNames($mother_name, $gender));
				}
				break;
			case 'add_spouse_to_individual_action':
				$name_fields = array_merge($name_fields, $surname_tradition->newSpouseNames($indi_name, $gender));
				break;
			case 'add_unlinked_indi_action':
			case 'update':
				if ($surname_tradition->hasSurnames()) {
					$name_fields['NAME'] = '//';
				}
				break;
		}
	}

	$bdm = ''; // used to copy '1 SOUR' to '2 SOUR' for BIRT DEAT MARR

	echo '<h2>', $controller->getPageTitle(), '</h2>';

	FunctionsPrint::initializeCalendarPopup();
	echo '<form method="post" name="addchildform" onsubmit="return checkform();">';
	echo '<input type="hidden" name="ged" value="', e($controller->tree()->getName()), '">';
	echo '<input type="hidden" name="action" value="', $nextaction, '">';
	echo '<input type="hidden" name="fact_id" value="', $name_fact_id, '">';
	echo '<input type="hidden" name="xref" value="', $xref, '">';
	echo '<input type="hidden" name="famtag" value="', $famtag, '">';
	echo '<input type="hidden" name="gender" value="', $gender, '">';
	echo Filter::getCsrf();
	echo '<table class="table wt-facts-table">';

	switch ($nextaction) {
		case 'add_child_to_family_action':
		case 'add_child_to_individual_action':
			// When adding a new child, specify the pedigree
			echo FunctionsEdit::addSimpleTag('0 PEDI');
			break;
	}
	// First - standard name fields
	foreach ($name_fields as $tag => $value) {
		if (substr_compare($tag, '_', 0, 1) !== 0) {
			echo FunctionsEdit::addSimpleTag('0 ' . $tag . ' ' . $value, '', '', null, $person);
		}
	}

	// Second - advanced name fields
	if ($surname_tradition->hasMarriedNames() || preg_match('/\n2 _MARNM /', $namerec)) {
		$adv_name_fields = ['_MARNM' => ''];
	} else {
		$adv_name_fields = [];
	}
	if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $controller->tree()->getPreference('ADVANCED_NAME_FACTS'), $match)) {
		foreach ($match[1] as $tag) {
			// Ignore advanced facts that duplicate standard facts
			if (!in_array($tag, ['TYPE', 'NPFX', 'GIVN', 'NICK', 'SPFX', 'SURN', 'NSFX'])) {
				$adv_name_fields[$tag] = '';
			}
		}
	}

	foreach (array_keys($adv_name_fields) as $tag) {
		// Edit existing tags, grouped together
		if (preg_match_all('/2 ' . $tag . ' (.+)/', $namerec, $match)) {
			foreach ($match[1] as $value) {
				echo FunctionsEdit::addSimpleTag('2 ' . $tag . ' ' . $value, '', GedcomTag::getLabel('NAME:' . $tag, $person));
				if ($tag === '_MARNM') {
					preg_match_all('/\/([^\/]*)\//', $value, $matches);
					echo FunctionsEdit::addSimpleTag('2 _MARNM_SURN ' . implode(',', $matches[1]));
				}
			}
		}
		// Allow a new tag to be entered
		if (!array_key_exists($tag, $name_fields)) {
			echo FunctionsEdit::addSimpleTag('0 ' . $tag, '', GedcomTag::getLabel('NAME:' . $tag, $person));
			if ($tag === '_MARNM') {
				echo FunctionsEdit::addSimpleTag('0 _MARNM_SURN');
			}
		}
	}

	// Third - new/existing custom name fields
	foreach ($name_fields as $tag => $value) {
		if (substr_compare($tag, '_', 0, 1) === 0) {
			echo FunctionsEdit::addSimpleTag('0 ' . $tag . ' ' . $value);
			if ($tag === '_MARNM') {
				preg_match_all('/\/([^\/]*)\//', $value, $matches);
				echo FunctionsEdit::addSimpleTag('2 _MARNM_SURN ' . implode(',', $matches[1]));
			}
		}
	}

	// Fourth - SOUR, NOTE, _CUSTOM, etc.
	if ($namerec) {
		$gedlines = explode("\n", $namerec); // -- find the number of lines in the record
		$fields   = explode(' ', $gedlines[0]);
		$glevel   = $fields[0];
		$level    = $glevel;
		$type     = $fields[1];
		$tags     = [];
		$i        = 0;
		do {
			if ($type !== 'TYPE' && !array_key_exists($type, $name_fields) && !array_key_exists($type, $adv_name_fields)) {
				$text = '';
				for ($j = 2; $j < count($fields); $j++) {
					if ($j > 2) {
						$text .= ' ';
					}
					$text .= $fields[$j];
				}
				while (($i + 1 < count($gedlines)) && (preg_match('/' . ($level + 1) . ' CONT ?(.*)/', $gedlines[$i + 1], $cmatch) > 0)) {
					$text .= "\n" . $cmatch[1];
					$i++;
				}
				echo FunctionsEdit::addSimpleTag($level . ' ' . $type . ' ' . $text);
			}
			$tags[] = $type;
			$i++;
			if (isset($gedlines[$i])) {
				$fields = explode(' ', $gedlines[$i]);
				$level  = $fields[0];
				if (isset($fields[1])) {
					$type = $fields[1];
				}
			}
		} while (($level > $glevel) && ($i < count($gedlines)));
	}

	// If we are adding a new individual, add the basic details
	if ($nextaction !== 'update') {
		echo '</table><br><table class="table wt-facts-table">';
		// 1 SEX
		if ($famtag === 'HUSB' || $gender === 'M') {
			echo FunctionsEdit::addSimpleTag('0 SEX M');
		} elseif ($famtag === 'WIFE' || $gender === 'F') {
			echo FunctionsEdit::addSimpleTag('0 SEX F');
		} else {
			echo FunctionsEdit::addSimpleTag('0 SEX U');
		}
		$bdm = 'BD';
		if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $controller->tree()->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
			foreach ($matches[1] as $match) {
				if (!in_array($match, explode('|', WT_EVENTS_DEAT))) {
					FunctionsEdit::addSimpleTags($match);
				}
			}
		}
		//-- if adding a spouse add the option to add a marriage fact to the new family
		if ($nextaction === 'add_spouse_to_individual_action' || $nextaction === 'add_spouse_to_family_action') {
			$bdm .= 'M';
			if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $controller->tree()->getPreference('QUICK_REQUIRED_FAMFACTS'), $matches)) {
				foreach ($matches[1] as $match) {
					FunctionsEdit::addSimpleTags($match);
				}
			}
		}
		if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $controller->tree()->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
			foreach ($matches[1] as $match) {
				if (in_array($match, explode('|', WT_EVENTS_DEAT))) {
					FunctionsEdit::addSimpleTags($match);
				}
			}
		}
	}

	echo keep_chan($person);
	echo '</table>';
	if ($nextaction === 'update') {
		// GEDCOM 5.5.1 spec says NAME doesn’t get a OBJE
		FunctionsEdit::printAddLayer('SOUR');
		FunctionsEdit::printAddLayer('NOTE');
		FunctionsEdit::printAddLayer('SHARED_NOTE');
		FunctionsEdit::printAddLayer('RESN');
	} else {
		FunctionsEdit::printAddLayer('SOUR', 1);
		FunctionsEdit::printAddLayer('NOTE', 1);
		FunctionsEdit::printAddLayer('SHARED_NOTE', 1);
		FunctionsEdit::printAddLayer('RESN', 1);
	}

	?>
	<div class="row form-group">
		<div class="col-sm-9 offset-sm-3">
			<button class="btn btn-primary" type="submit">
				<?= FontAwesome::decorativeIcon('save') ?>
				<?= /* I18N: A button label. */ I18N::translate('save') ?>
			</button>
			<?php if (preg_match('/^add_(child|spouse|parent|unlinked_indi)/', $nextaction)): ?>

				<button class="btn btn-primary" type="submit" name="goto" value="<?= $xref ?>">
					<?= FontAwesome::decorativeIcon('save') ?>
					<?= /* I18N: A button label. */ I18N::translate('go to new individual') ?>
				</button>
			<?php endif ?>
			<a class="btn btn-secondary" href="<?= e($cancel_url) ?>">
				<?= FontAwesome::decorativeIcon('cancel') ?>
				<?= /* I18N: A button label. */ I18N::translate('cancel') ?>
			</a>
			<?php if ($name_fact !== null && (Auth::isAdmin() || $controller->tree()->getPreference('SHOW_GEDCOM_RECORD'))): ?>
				<a class="btn btn-link" href="<?= e(route('edit-raw-fact', ['xref' => $xref, 'fact_id' => $name_fact->getFactId(), 'ged' => $controller->tree()->getName()])) ?>">
					<?= I18N::translate('Edit the raw GEDCOM') ?>
				</a>
			<?php endif ?>
		</div>
	</div>
	</form>
	<?= view('modals/ajax') ?>

	<?php
	$controller->addInlineJavascript('
	SURNAME_TRADITION="' . $controller->tree()->getPreference('SURNAME_TRADITION') . '";
	gender="' . $gender . '";
	famtag="' . $famtag . '";
	function trim(str) {
		str=str.replace(/\s\s+/g, " ");
		return str.replace(/(^\s+)|(\s+$)/g, "");
	}

	function lang_class(str) {
		if (str.match(/[\u0370-\u03FF]/)) return "greek";
		if (str.match(/[\u0400-\u04FF]/)) return "cyrillic";
		if (str.match(/[\u0590-\u05FF]/)) return "hebrew";
		if (str.match(/[\u0600-\u06FF]/)) return "arabic";
		return "latin"; // No matched text implies latin :-)
	}

	// Generate a full name from the name components
	function generate_name() {
		var npfx = $("#NPFX").val();
		var givn = $("#GIVN").val();
		var spfx = $("#SPFX").val();
		var surn = $("#SURN").val();
		var nsfx = $("#NSFX").val();
		if (SURNAME_TRADITION === "polish" && (gender === "F" || famtag === "WIFE")) {
			surn = surn.replace(/ski$/, "ska");
			surn = surn.replace(/cki$/, "cka");
			surn = surn.replace(/dzki$/, "dzka");
			surn = surn.replace(/żki$/, "żka");
		}
		// Commas are used in the GIVN and SURN field to separate lists of surnames.
		// For example, to differentiate the two Spanish surnames from an English
		// double-barred name.
		// Commas *may* be used in other fields, and will form part of the NAME.
		var locale = document.documentElement.lang;
		if (locale === "vi" || locale === "hu") {
			// Default format: /SURN/ GIVN
			return trim(npfx+" /"+trim(spfx+" "+surn).replace(/ *, */g, " ")+"/ "+givn.replace(/ *, */g, " ")+" "+nsfx);
		} else if (locale === "zh-Hans" || locale === "zh-Hant") {
			// Default format: /SURN/GIVN
			return npfx+"/"+spfx+surn+"/"+givn+nsfx;
		} else {
			// Default format: GIVN /SURN/
			return trim(npfx+" "+givn.replace(/ *, */g, " ")+" /"+trim(spfx+" "+surn).replace(/ *, */g, " ")+"/ "+nsfx);
		}
	}

	// Update the NAME and _MARNM fields from the name components
	// and also display the value in read-only "gedcom" format.
	function updatewholename() {
		// Don’t update the name if the user manually changed it
		if (manualChange) {
			return;
		}
		var npfx = $("#NPFX").val();
		var givn = $("#GIVN").val();
		var spfx = $("#SPFX").val();
		var surn = $("#SURN").val();
		var nsfx = $("#NSFX").val();
		var name = generate_name();
		$("#NAME").val(name);
		$("#NAME_display").text(name);
		// Married names inherit some NSFX values, but not these
		nsfx = nsfx.replace(/^(I|II|III|IV|V|VI|Junior|Jr\.?|Senior|Sr\.?)$/i, "");
		// Update _MARNM field from _MARNM_SURN field and display it
		// Be careful of mixing latin/hebrew/etc. character sets.
		var ip = document.getElementsByTagName("input");
		var marnm_id = "";
		var romn = "";
		var heb = "";
		for (var i = 0; i < ip.length; i++) {
			var val = trim(ip[i].value);
			if (ip[i].id.indexOf("_HEB") === 0)
				heb = val;
			if (ip[i].id.indexOf("ROMN") === 0)
				romn = val;
			if (ip[i].id.indexOf("_MARNM") === 0) {
				if (ip[i].id.indexOf("_MARNM_SURN") === 0) {
					var msurn = "";
					if (val !== "") {
						var lc = lang_class(document.getElementById(ip[i].id).value);
						if (lang_class(name) === lc)
							msurn = trim(npfx + " " + givn + " /" + val + "/ " + nsfx);
						else if (lc === "hebrew")
							msurn = heb.replace(/\/.*\//, "/" + val + "/");
						else if (lang_class(romn) === lc)
							msurn = romn.replace(/\/.*\//, "/" + val + "/");
					}
					document.getElementById(marnm_id).value = msurn;
					document.getElementById(marnm_id+"_display").innerHTML = msurn;
				} else {
					marnm_id = ip[i].id;
				}
			}
		}
	}

	// Toggle the name editor fields between
	// <input type="hidden"> <span style="display:inline">
	// <input type="text">   <span style="display:none">
	var oldName = "";

	// Calls to generate_name() trigger an update - hence need to
	// set the manual change to true first. We are probably
	// listening to the wrong events on the input fields...
	var manualChange = true;
	manualChange = generate_name() !== $("#NAME").val();

	function convertHidden(eid) {
		var input1 = $("#" + eid);
		var input2 = $("#" + eid + "_display");
		// Note that IE does not allow us to change the type of an input, so we must create a new one.
		if (input1.attr("type")=="hidden") {
			input1.replaceWith(input1.clone().attr("type", "text"));
			input2.hide();
		} else {
			input1.replaceWith(input1.clone().attr("type", "hidden"));
			input2.show();
		}
	}

	/**
	 * if the user manually changed the NAME field, then update the textual
	 * HTML representation of it
	 * If the value changed set manualChange to true so that changing
	 * the other fields doesn’t change the NAME line
	 */
	function updateTextName(eid) {
		var element = document.getElementById(eid);
		if (element) {
			if (element.value!=oldName) manualChange = true;
			var delement = document.getElementById(eid+"_display");
			if (delement) {
				delement.innerHTML = element.value;
			}
		}
	}

	function checkform() {
		var ip=document.getElementsByTagName("input");
		for (var i=0; i<ip.length; i++) {
			// ADD slashes to _HEB and _AKA names
			if (ip[i].id.indexOf("_AKA")==0 || ip[i].id.indexOf("_HEB")==0 || ip[i].id.indexOf("ROMN")==0)
				if (ip[i].value.indexOf("/")<0 && ip[i].value!="")
					ip[i].value=ip[i].value.replace(/([^\s]+)\s*$/, "/$1/");
			// Blank out temporary _MARNM_SURN
			if (ip[i].id.indexOf("_MARNM_SURN")==0)
					ip[i].value="";
			// Convert "xxx yyy" and "xxx y yyy" surnames to "xxx,yyy"
			if ((SURNAME_TRADITION=="spanish" || "SURNAME_TRADITION"=="portuguese") && ip[i].id.indexOf("SURN")==0) {
				ip[i].value=document.forms[0].SURN.value.replace(/^\s*([^\s,]{2,})\s+([iIyY] +)?([^\s,]{2,})\s*$/, "$1,$3");
			}
		}
		return true;
	}

	// If the name isn’t initially formed from the components in a standard way,
	// then don’t automatically update it.
	if (document.getElementById("NAME").value!=generate_name() && document.getElementById("NAME").value!="//") {
		convertHidden("NAME");
	}
	');
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
