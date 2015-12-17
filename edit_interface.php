<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\SimpleController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\GedcomCode\GedcomCodePedi;

define('WT_SCRIPT_NAME', 'edit_interface.php');
require './includes/session.php';

$action = Filter::post('action', null, Filter::get('action'));

$controller = new SimpleController;
$controller
	->restrictAccess(Auth::isEditor($WT_TREE))
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();')
	->addInlineJavascript('var locale_date_format="' . preg_replace('/[^DMY]/', '', str_replace(array('j', 'F'), array('D', 'M'), I18N::dateFormat())) . '";');

switch ($action) {
////////////////////////////////////////////////////////////////////////////////
case 'editraw':
	$xref = Filter::get('xref', WT_REGEX_XREF);

	$record = GedcomRecord::getInstance($xref, $WT_TREE);
	check_record_access($record);

	$controller
		->setPageTitle($record->getFullName() . ' - ' . I18N::translate('Edit raw GEDCOM'))
		->pageHeader()
		->addInlineJavascript('jQuery("#raw-gedcom-list").sortable({opacity: 0.7, cursor: "move", axis: "y"});');

	?>
	<div id="edit_interface-page">
		<h4>
			<?php echo $controller->getPageTitle(); ?>
			<?php echo FunctionsPrint::helpLink('edit_edit_raw'); ?>
		</h4>
		<pre>     <?php echo '0 @' . $record->getXref() . '@ ' . $record::RECORD_TYPE; ?></pre>
		<form method="post" action="edit_interface.php">
			<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
			<input type="hidden" name="action" value="updateraw">
			<input type="hidden" name="xref" value="<?php echo $xref; ?>">
			<?php echo Filter::getCsrf(); ?>
			<ul id="raw-gedcom-list">
				<?php foreach ($record->getFacts() as $fact) { ?>
					<?php if (!$fact->isPendingDeletion()) { ?>
					<li>
						<div style="cursor:move;">
							<?php echo $fact->summary(); ?>
						</div>
						<input type="hidden" name="fact_id[]" value="<?php echo $fact->getFactId(); ?>">
						<textarea name="fact[]" dir="ltr" rows="<?php echo preg_match_all('/\n/', $fact->getGedcom(), $dummy_parameter_for_php53); ?>" style="width:100%;"><?php echo Filter::escapeHtml($fact->getGedcom()); ?></textarea>
					</li>
					<?php } ?>
				<?php } ?>
				<li>
					<div style="cursor:move;">
						<b><i><?php echo I18N::translate('Add a fact'); ?><i></b>
					</div>
					<input type="hidden" name="fact_id[]" value="">
					<textarea name="fact[]" dir="ltr" rows="2" style="width:100%;"></textarea>
				</li>
			</ul>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div>
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'updateraw':
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$facts     = Filter::postArray('fact');
	$fact_ids  = Filter::postArray('fact_id');

	if (!Filter::checkCsrf()) {
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=editraw&xref=' . $xref);

		return;
	}

	$record = GedcomRecord::getInstance($xref, $WT_TREE);
	check_record_access($record);

	$controller
		->setPageTitle($record->getFullName() . ' - ' . I18N::translate('Edit raw GEDCOM'))
		->pageHeader();

	$gedcom = '0 @' . $record->getXref() . '@ ' . $record::RECORD_TYPE;

	// Retain any private facts
	foreach ($record->getFacts(null, false, Auth::PRIV_HIDE) as $fact) {
		if (!in_array($fact->getFactId(), $fact_ids) && !$fact->isPendingDeletion()) {
			$gedcom .= "\n" . $fact->getGedcom();
		}
	}
	// Append the new facts
	foreach ($facts as $fact) {
		$gedcom .= "\n" . $fact;
	}

	// Cleanup the client’s bad editing?
	$gedcom = preg_replace('/[\r\n]+/', "\n", $gedcom); // Empty lines
	$gedcom = trim($gedcom); // Leading/trailing spaces

	$record->updateRecord($gedcom, false);

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'editrawfact':
	$xref    = Filter::get('xref', WT_REGEX_XREF);
	$fact_id = Filter::get('fact_id');

	$record = GedcomRecord::getInstance($xref, $WT_TREE);
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
		$controller
			->pageHeader()
			->addInlineJavascript('closePopupAndReloadParent();');

		return;
	}

	$controller
		->setPageTitle($record->getFullName() . ' - ' . I18N::translate('Edit raw GEDCOM'))
		->pageHeader();

	?>
	<div id="edit_interface-page">
		<h4>
			<?php echo $controller->getPageTitle(); ?>
			<?php echo FunctionsPrint::helpLink('edit_edit_raw'); ?>
			<?php FunctionsPrint::printSpecialCharacterLink('gedcom'); ?>
		</h4>
		<form method="post" action="edit_interface.php">
			<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
			<input type="hidden" name="action" value="updaterawfact">
			<input type="hidden" name="xref" value="<?php echo $xref; ?>">
			<input type="hidden" name="fact_id" value="<?php echo $fact_id; ?>">
			<?php echo Filter::getCsrf(); ?>
			<textarea name="gedcom" id="gedcom" dir="ltr"><?php echo Filter::escapeHtml($edit_fact->getGedcom()); ?></textarea>
			<table class="facts_table">
				<?php echo keep_chan($record); ?>
			</table>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div>
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'updaterawfact':
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$fact_id   = Filter::post('fact_id');
	$gedcom    = Filter::post('gedcom');
	$keep_chan = Filter::postBool('keep_chan');

	if (!Filter::checkCsrf()) {
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=editrawfact&xref=' . $xref . '&fact_id=' . $fact_id);

		return;
	}

	$record = GedcomRecord::getInstance($xref, $WT_TREE);
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
		$controller
			->pageHeader()
			->addInlineJavascript('closePopupAndReloadParent();');

		return;
	}

	$controller
		->setPageTitle($record->getFullName() . ' - ' . I18N::translate('Edit raw GEDCOM'))
		->pageHeader();

	// Cleanup the client’s bad editing?
	$gedcom = preg_replace('/[\r\n]+/', "\n", $gedcom); // Empty lines
	$gedcom = trim($gedcom); // Leading/trailing spaces

	$record->updateFact($fact_id, $gedcom, !$keep_chan);

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'edit':
	$xref    = Filter::get('xref', WT_REGEX_XREF);
	$fact_id = Filter::get('fact_id');

	$record = GedcomRecord::getInstance($xref, $WT_TREE);
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
		$controller
			->pageHeader()
			->addInlineJavascript('closePopupAndReloadParent();');

		return;
	}

	$controller
		->setPageTitle($record->getFullName() . ' - ' . I18N::translate('Edit'))
		->pageHeader();

	echo '<div id="edit_interface-page">';
	echo '<h4>', $controller->getPageTitle(), '</h4>';
	FunctionsPrint::initializeCalendarPopup();
	echo '<form name="editform" method="post" action="edit_interface.php" enctype="multipart/form-data">';
	echo '<input type="hidden" name="ged" value="', $WT_TREE->getNameHtml(), '">';
	echo '<input type="hidden" name="action" value="update">';
	echo '<input type="hidden" name="fact_id" value="', $fact_id, '">';
	echo '<input type="hidden" name="xref" value="', $xref, '">';
	echo '<input type="hidden" name="prev_action" value="edit">';
	echo Filter::getCsrf();
	echo '<table class="facts_table">';
	FunctionsEdit::createEditForm($record, $edit_fact);
	echo keep_chan($record);
	echo '</table>';

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
		// FAM and INDI records have real facts.  They can take NOTE/SOUR/OBJE/etc.
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
			if ($level1type === 'CHR' || $level1type === 'MARR') {
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
	if (Auth::isAdmin() || $WT_TREE->getPreference('SHOW_GEDCOM_RECORD')) {
		echo
			'<br><br><a href="edit_interface.php?action=editrawfact&amp;xref=', $xref, '&amp;fact_id=', $fact_id, '&amp;ged=', $WT_TREE->getNameUrl(), '">',
			I18N::translate('Edit raw GEDCOM'),
			'</a>';
	}
	?>
		<p id="save-cancel">
			<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
			<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
		</p>
	</form>
	</div>
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'add':
	$xref = Filter::get('xref', WT_REGEX_XREF);
	$fact = Filter::get('fact', WT_REGEX_TAG);

	$record = GedcomRecord::getInstance($xref, $WT_TREE);
	check_record_access($record);

	$controller
		->setPageTitle($record->getFullName() . ' - ' . GedcomTag::getLabel($fact, $record))
		->pageHeader();

	$level0type = $record::RECORD_TYPE;

	echo '<div id="edit_interface-page">';
	echo '<h4>', $controller->getPageTitle(), '</h4>';

	FunctionsPrint::initializeCalendarPopup();
	echo '<form name="addform" method="post" action="edit_interface.php" enctype="multipart/form-data">';
	echo '<input type="hidden" name="ged" value="', $WT_TREE->getNameHtml(), '">';
	echo '<input type="hidden" name="action" value="update">';
	echo '<input type="hidden" name="xref" value="', $xref, '">';
	echo '<input type="hidden" name="prev_action" value="add">';
	echo '<input type="hidden" name="fact_type" value="' . $fact . '">';
	echo Filter::getCsrf();
	echo '<table class="facts_table">';

	FunctionsEdit::createAddForm($fact);

	echo keep_chan($record);
	echo '</table>';

	// Genealogical facts (e.g. for INDI and FAM records) can have 2 SOUR/NOTE/OBJE/ASSO/RESN ...
	if ($level0type === 'INDI' || $level0type === 'FAM') {
		// ... but not facts which are simply links to other records
		if ($fact !== 'OBJE' && $fact !== 'NOTE' && $fact !== 'SHARED_NOTE' && $fact !== 'REPO' && $fact !== 'SOUR' && $fact !== 'ASSO' && $fact !== 'ALIA') {
			FunctionsEdit::printAddLayer('SOUR');
			FunctionsEdit::printAddLayer('OBJE');
			// Don’t add notes to notes!
			if ($fact !== 'NOTE') {
				FunctionsEdit::printAddLayer('NOTE');
				FunctionsEdit::printAddLayer('SHARED_NOTE', 2, $fact);
			}
			FunctionsEdit::printAddLayer('ASSO');
			// allow to add godfather and godmother for CHR fact or best man and bridesmaid  for MARR fact in one window
			if ($fact === 'CHR' || $fact === 'MARR') {
				FunctionsEdit::printAddLayer('ASSO2');
			}
			FunctionsEdit::printAddLayer('RESN');
		}
	}
	?>
		<p id="save-cancel">
			<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
			<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
		</p>
	</form>
	</div>
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'update':
	// Update a fact
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$fact_id   = Filter::post('fact_id');
	$keep_chan = Filter::postBool('keep_chan');

	if (!Filter::checkCsrf()) {
		$prev_action = Filter::post('prev_action', 'add|edit|addname|editname');
		$fact_type   = Filter::post('fact_type', WT_REGEX_TAG);
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=' . $prev_action . '&xref=' . $xref . '&fact_id=' . $fact_id . '&fact=' . $fact_type);

		return;
	}

	$record = GedcomRecord::getInstance($xref, $WT_TREE);
	check_record_access($record);

	// Arrays for each GEDCOM line
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	$controller
		->setPageTitle(I18N::translate('Edit'))
		->pageHeader();

	// If the fact has a DATE or PLAC, then delete any value of Y
	if ($text[0] === 'Y') {
		for ($n = 1; $n < count($tag); ++$n) {
			if ($glevels[$n] == 2 && ($tag[$n] === 'DATE' || $tag[$n] === 'PLAC') && $text[$n]) {
				$text[0] = '';
				break;
			}
		}
	}

	$newged = '';
	if (!empty($_POST['NAME'])) {
		$newged .= "\n1 NAME " . $_POST['NAME'];
	}
	if (!empty($_POST['TYPE'])) {
		$newged .= "\n2 TYPE " . $_POST['TYPE'];
	}
	if (!empty($_POST['NPFX'])) {
		$newged .= "\n2 NPFX " . $_POST['NPFX'];
	}
	if (!empty($_POST['GIVN'])) {
		$newged .= "\n2 GIVN " . $_POST['GIVN'];
	}
	if (!empty($_POST['NICK'])) {
		$newged .= "\n2 NICK " . $_POST['NICK'];
	}
	if (!empty($_POST['SPFX'])) {
		$newged .= "\n2 SPFX " . $_POST['SPFX'];
	}
	if (!empty($_POST['SURN'])) {
		$newged .= "\n2 SURN " . $_POST['SURN'];
	}
	if (!empty($_POST['NSFX'])) {
		$newged .= "\n2 NSFX " . $_POST['NSFX'];
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
	if (!empty($_POST['_MARNM'])) {
		$newged .= "\n2 _MARNM " . $_POST['_MARNM'];
	}
	if (!empty($_POST['ROMN'])) {
		$newged .= "\n2 ROMN " . $_POST['ROMN'];
	}
	if (!empty($_POST['FONE'])) {
		$newged .= "\n2 FONE " . $_POST['FONE'];
	}
	if (!empty($_POST['_HEB'])) {
		$newged .= "\n2 _HEB " . $_POST['_HEB'];
	}
	if (!empty($_POST['_AKA'])) {
		$newged .= "\n2 _AKA " . $_POST['_AKA'];
	}

	$newged = substr($newged, 1); // Remove leading newline
	$record->updateFact($fact_id, $newged, !$keep_chan);

	// For the GEDFact_assistant module
	$pid_array = Filter::post('pid_array');
	if ($pid_array) {
		foreach (explode(',', $pid_array) as $pid) {
			if ($pid !== $xref) {
				$indi = Individual::getInstance($pid, $WT_TREE);
				if ($indi && $indi->canEdit()) {
					$indi->updateFact($fact_id, $newged, !$keep_chan);
				}
			}
		}
	}

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
// Add a new child to an existing family
////////////////////////////////////////////////////////////////////////////////
case 'add_child_to_family':
	$xref   = Filter::get('xref', WT_REGEX_XREF);
	$gender = Filter::get('gender', '[MFU]', 'U');

	$family = Family::getInstance($xref, $WT_TREE);
	check_record_access($family);

	$controller
		->setPageTitle($family->getFullName() . ' - ' . I18N::translate('Add a new child'))
		->pageHeader();

	print_indi_form('add_child_to_family_action', null, $family, null, 'CHIL', $gender);
	break;

case 'add_child_to_family_action':
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$PEDI      = Filter::post('PEDI');
	$keep_chan = Filter::postBool('keep_chan');
	$glevels   = Filter::postArray('glevels', '[0-9]');
	$tag       = Filter::postArray('tag', WT_REGEX_TAG);
	$text      = Filter::postArray('text');
	$islink    = Filter::postArray('islink', '[01]');

	if (!Filter::checkCsrf()) {
		$gender = Filter::get('gender', '[MFU]', 'U');
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=add_child_to_family&xref=' . $xref . '&gender=' . $gender);

		return;
	}

	$family = Family::getInstance($xref, $WT_TREE);
	check_record_access($family);

	$controller->pageHeader();

	FunctionsEdit::splitSource();
	$gedrec = '0 @REF@ INDI';
	$gedrec .= FunctionsEdit::addNewName();
	$gedrec .= FunctionsEdit::addNewSex();
	if (preg_match_all('/([A-Z0-9_]+)/', $WT_TREE->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
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
		$controller->addInlineJavascript('closePopupAndReloadParent("' . $new_child->getRawUrl() . '");');
	} else {
		$controller->addInlineJavascript('closePopupAndReloadParent();');
	}
	break;

////////////////////////////////////////////////////////////////////////////////
// Add a new child to an existing individual (creating a one-parent family)
////////////////////////////////////////////////////////////////////////////////
case 'add_child_to_individual':
	$xref = Filter::get('xref', WT_REGEX_XREF);

	$person = Individual::getInstance($xref, $WT_TREE);
	check_record_access($person);

	$controller
		->setPageTitle($person->getFullName() . ' - ' . I18N::translate('Add a child to create a one-parent family'))
		->pageHeader();

	print_indi_form('add_child_to_individual_action', $person, null, null, 'CHIL', $person->getSex());
	break;

case 'add_child_to_individual_action':
	$xref    = Filter::post('xref', WT_REGEX_XREF);
	$PEDI    = Filter::post('PEDI');
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	if (!Filter::checkCsrf()) {
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=add_child_to_individual&xref=' . $xref);

		return;
	}

	$person = Individual::getInstance($xref, $WT_TREE);
	check_record_access($person);

	$controller->pageHeader();

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
	if (preg_match_all('/([A-Z0-9_]+)/', $WT_TREE->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
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
		$controller->addInlineJavascript('closePopupAndReloadParent("' . $child->getRawUrl() . '");');
	} else {
		$controller->addInlineJavascript('closePopupAndReloadParent();');
	}
	break;

////////////////////////////////////////////////////////////////////////////////
// Add a new parent to an existing individual (creating a one-parent family)
////////////////////////////////////////////////////////////////////////////////
case 'add_parent_to_individual':
	$xref   = Filter::get('xref', WT_REGEX_XREF);
	$gender = Filter::get('gender', '[MF]', 'U');

	$individual = Individual::getInstance($xref, $WT_TREE);
	check_record_access($individual);

	if ($gender === 'F') {
		$controller->setPageTitle(I18N::translate('Add a new mother'));
		$famtag = 'WIFE';
	} else {
		$controller->setPageTitle(I18N::translate('Add a new father'));
		$famtag = 'HUSB';
	}
	$controller->pageHeader();

	print_indi_form('add_parent_to_individual_action', $individual, null, null, $famtag, $gender);
	break;

case 'add_parent_to_individual_action':
	$xref    = Filter::post('xref', WT_REGEX_XREF);
	$PEDI    = Filter::post('PEDI');
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	if (!Filter::checkCsrf()) {
		$gender = Filter::get('gender', '[MFU]', 'U');
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=add_parent_to_individual&xref=' . $xref . '&gender=' . $gender);

		return;
	}

	$person = Individual::getInstance($xref, $WT_TREE);
	check_record_access($person);

	$controller->pageHeader();

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
	if (preg_match_all('/([A-Z0-9_]+)/', $WT_TREE->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
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
		$controller->addInlineJavascript('closePopupAndReloadParent("' . $parent->getRawUrl() . '");');
	} else {
		$controller->addInlineJavascript('closePopupAndReloadParent();');
	}
	break;

////////////////////////////////////////////////////////////////////////////////
// Add a new, unlinked individual
////////////////////////////////////////////////////////////////////////////////
case 'add_unlinked_indi':
	$controller
		->restrictAccess(Auth::isManager($WT_TREE))
		->setPageTitle(I18N::translate('Create a new individual'))
		->pageHeader();

	print_indi_form('add_unlinked_indi_action', null, null, null, null, null);
	break;

case 'add_unlinked_indi_action':
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	if (!Filter::checkCsrf()) {
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=add_unlinked_indi');

		return;
	}

	$controller
		->restrictAccess(Auth::isManager($WT_TREE))
		->pageHeader();

	FunctionsEdit::splitSource();
	$gedrec = '0 @REF@ INDI';
	$gedrec .= FunctionsEdit::addNewName();
	$gedrec .= FunctionsEdit::addNewSex();
	if (preg_match_all('/([A-Z0-9_]+)/', $WT_TREE->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
		foreach ($matches[1] as $match) {
			$gedrec .= FunctionsEdit::addNewFact($match);
		}
	}
	if (Filter::postBool('SOUR_INDI')) {
		$gedrec = FunctionsEdit::handleUpdates($gedrec);
	} else {
		$gedrec = FunctionsEdit::updateRest($gedrec);
	}

	$new_indi = $WT_TREE->createRecord($gedrec);

	if (Filter::post('goto') === 'new') {
		$controller->addInlineJavascript('closePopupAndReloadParent("' . $new_indi->getRawUrl() . '");');
	} else {
		$controller->addInlineJavascript('closePopupAndReloadParent();');
	}
	break;

////////////////////////////////////////////////////////////////////////////////
// Add a new spouse to an existing individual (creating a new family)
////////////////////////////////////////////////////////////////////////////////
case 'add_spouse_to_individual':
	$famtag = Filter::get('famtag', 'HUSB|WIFE');
	$xref   = Filter::get('xref', WT_REGEX_XREF);

	$individual = Individual::getInstance($xref, $WT_TREE);
	check_record_access($individual);

	if ($famtag === 'WIFE') {
		$controller->setPageTitle(I18N::translate('Add a new wife'));
		$sex = 'F';
	} else {
		$controller->setPageTitle(I18N::translate('Add a new husband'));
		$sex = 'M';
	}
	$controller->pageHeader();

	print_indi_form('add_spouse_to_individual_action', $individual, null, null, $famtag, $sex);
	break;

case 'add_spouse_to_individual_action':
	$xref    = Filter::post('xref'); // Add a spouse to this individual
	$sex     = Filter::post('SEX', '[MFU]', 'U');
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	if (!Filter::checkCsrf()) {
		$famtag = Filter::get('famtag', 'HUSB|WIFE');
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=add_spouse_to_individual&xref=' . $xref . '&famtag=' . $famtag);

		return;
	}

	$person = Individual::getInstance($xref, $WT_TREE);
	check_record_access($person);

	$controller
		->setPageTitle(I18N::translate('Add a new spouse'))
		->pageHeader();

	FunctionsEdit::splitSource();
	$indi_gedcom = '0 @REF@ INDI';
	$indi_gedcom .= FunctionsEdit::addNewName();
	$indi_gedcom .= FunctionsEdit::addNewSex();
	if (preg_match_all('/([A-Z0-9_]+)/', $WT_TREE->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
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
	if (preg_match_all('/([A-Z0-9_]+)/', $WT_TREE->getPreference('QUICK_REQUIRED_FAMFACTS'), $matches)) {
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
		$controller->addInlineJavascript('closePopupAndReloadParent("' . $spouse->getRawUrl() . '");');
	} else {
		$controller->addInlineJavascript('closePopupAndReloadParent();');
	}
	break;

////////////////////////////////////////////////////////////////////////////////
// Add a new spouse to an existing family
////////////////////////////////////////////////////////////////////////////////
case 'add_spouse_to_family':
	$xref   = Filter::get('xref', WT_REGEX_XREF);
	$famtag = Filter::get('famtag', 'HUSB|WIFE');

	$family = Family::getInstance($xref, $WT_TREE);
	check_record_access($family);

	if ($famtag === 'WIFE') {
		$controller->setPageTitle(I18N::translate('Add a new wife'));
		$sex = 'F';
	} else {
		$controller->setPageTitle(I18N::translate('Add a new husband'));
		$sex = 'M';
	}
	$controller->pageHeader();

	print_indi_form('add_spouse_to_family_action', null, $family, null, $famtag, $sex);
	break;

case 'add_spouse_to_family_action':
	$xref    = Filter::post('xref', WT_REGEX_XREF);
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	$family  = Family::getInstance($xref, $WT_TREE);
	check_record_access($family);

	if (!Filter::checkCsrf()) {
		$famtag = Filter::get('famtag', 'HUSB|WIFE');
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=add_spouse_to_family&xref=' . $xref . '&famtag=' . $famtag);

		return;
	}

	$controller->pageHeader();

	// Create the new spouse
	FunctionsEdit::splitSource(); // separate SOUR record from the rest

	$gedrec = '0 @REF@ INDI';
	$gedrec .= FunctionsEdit::addNewName();
	$gedrec .= FunctionsEdit::addNewSex();
	if (preg_match_all('/([A-Z0-9_]+)/', $WT_TREE->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
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
	if (preg_match_all('/([A-Z0-9_]+)/', $WT_TREE->getPreference('QUICK_REQUIRED_FAMFACTS'), $matches)) {
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
		$controller->addInlineJavascript('closePopupAndReloadParent("' . $spouse->getRawUrl() . '");');
	} else {
		$controller->addInlineJavascript('closePopupAndReloadParent();');
	}
	break;

////////////////////////////////////////////////////////////////////////////////
// Link an individual to an existing family, as a child
////////////////////////////////////////////////////////////////////////////////
case 'addfamlink':
	$xref = Filter::get('xref', WT_REGEX_XREF);

	$person = Individual::getInstance($xref, $WT_TREE);
	check_record_access($person);

	$controller
		->setPageTitle($person->getFullName() . ' - ' . I18N::translate('Link this individual to an existing family as a child'))
		->pageHeader();

	?>
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>
		<form method="post" name="addchildform" action="edit_interface.php">
			<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
			<input type="hidden" name="action" value="linkfamaction">
			<input type="hidden" name="xref" value="<?php echo $person->getXref(); ?>">
			<?php echo Filter::getCsrf(); ?>
			<table class="facts_table">
				<tr>
					<td class="facts_label">
						<?php echo I18N::translate('Family'); ?>
					</td>
					<td class="facts_value">
						<input data-autocomplete-type="FAM" type="text" id="famid" name="famid" size="8">
						<?php echo FunctionsPrint::printFindFamilyLink('famid'); ?>
					</td>
				</tr>
				<tr>
					<td class="facts_label">
						<?php echo GedcomTag::getLabel('PEDI'); ?>
					</td>
					<td class="facts_value">
						<?php echo FunctionsEdit::editFieldPedigree('PEDI', '', '', $person); ?>
						<p class="small text-muted">
							<?php echo I18N::translate('A child may have more than one set of parents.  The relationship between the child and the parents can be biological, legal, or based on local culture and tradition.  If no pedigree is specified, then a biological relationship will be assumed.'); ?>
						</p>
					</td>
				</tr>
				<?php echo keep_chan($person); ?>
			</table>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div>
	<?php
	break;

case 'linkfamaction':
	$xref   = Filter::post('xref', WT_REGEX_XREF);
	$famid  = Filter::post('famid', WT_REGEX_XREF);
	$PEDI   = Filter::post('PEDI');

	if (!Filter::checkCsrf()) {
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=addfamlink&xref=' . $xref);

		return;
	}

	$person = Individual::getInstance($xref, $WT_TREE);
	$family = Family::getInstance($famid, $WT_TREE);
	check_record_access($person);
	check_record_access($family);

	$controller
		->setPageTitle($person->getFullName() . ' - ' . I18N::translate('Link this individual to an existing family as a child'))
		->pageHeader();

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

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
// Link and individual to an existing individual as a spouse
////////////////////////////////////////////////////////////////////////////////
case 'linkspouse':
	$famtag = Filter::get('famtag', 'HUSB|WIFE');
	$xref   = Filter::get('xref', WT_REGEX_XREF);

	$person = Individual::getInstance($xref, $WT_TREE);
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
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>
		<form method="post" name="addchildform" action="edit_interface.php">
			<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
			<input type="hidden" name="action" value="linkspouseaction">
			<input type="hidden" name="xref" value="<?php echo $person->getXref(); ?>">
			<input type="hidden" name="famtag" value="<?php echo $famtag; ?>">
			<?php echo Filter::getCsrf(); ?>
			<table class="facts_table">
				<tr>
					<td class="facts_label">
						<?php echo $label; ?>
					</td>
					<td class="facts_value">
						<input data-autocomplete-type="INDI" id="spouseid" type="text" name="spid" size="8">
						<?php echo FunctionsPrint::printFindIndividualLink('spouseid'); ?>
					</td>
				</tr>
				<?php FunctionsEdit::addSimpleTag('0 MARR Y'); ?>
				<?php FunctionsEdit::addSimpleTag('0 DATE', 'MARR'); ?>
				<?php FunctionsEdit::addSimpleTag('0 PLAC', 'MARR'); ?>
				<?php echo keep_chan($person); ?>
			</table>
			<?php FunctionsEdit::printAddLayer('SOUR'); ?>
			<?php FunctionsEdit::printAddLayer('OBJE'); ?>
			<?php FunctionsEdit::printAddLayer('NOTE'); ?>
			<?php FunctionsEdit::printAddLayer('SHARED_NOTE'); ?>
			<?php FunctionsEdit::printAddLayer('ASSO'); ?>
			<?php FunctionsEdit::printAddLayer('ASSO2'); ?>
			<?php FunctionsEdit::printAddLayer('RESN'); ?>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div>
	<?php
	break;

case 'linkspouseaction':
	$xref    = Filter::post('xref', WT_REGEX_XREF);
	$spid    = Filter::post('spid', WT_REGEX_XREF);
	$famtag  = Filter::post('famtag', 'HUSB|WIFE');
	$glevels = Filter::postArray('glevels', '[0-9]');
	$tag     = Filter::postArray('tag', WT_REGEX_TAG);
	$text    = Filter::postArray('text');
	$islink  = Filter::postArray('islink', '[01]');

	if (!Filter::checkCsrf()) {
		$famtag = Filter::get('famtag', 'HUSB|WIFE');
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=linkspouse&xref=' . $xref . '&famtag=' . $famtag);

		return;
	}

	$person  = Individual::getInstance($xref, $WT_TREE);
	$spouse  = Individual::getInstance($spid, $WT_TREE);
	check_record_access($person);
	check_record_access($spouse);

	if ($person->getSex() === 'F') {
		$controller->setPageTitle($person->getFullName() . ' - ' . I18N::translate('Add a husband using an existing individual'));
	} else {
		$controller->setPageTitle($person->getFullName() . ' - ' . I18N::translate('Add a wife using an existing individual'));
	}
	$controller->pageHeader();

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

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
// Create a new source record
////////////////////////////////////////////////////////////////////////////////
case 'addnewsource':
	$controller
		->setPageTitle(I18N::translate('Create a new source'))
		->pageHeader();

	?>
	<script>
		function check_form(frm) {
			if (frm.TITL.value=="") {
				alert('<?php echo I18N::translate('You must provide a source title'); ?>');
				frm.TITL.focus();
				return false;
			}
			return true;
		}
	</script>
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>
		<form method="post" action="edit_interface.php" onsubmit="return check_form(this);">
			<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
			<input type="hidden" name="action" value="addsourceaction">
			<input type="hidden" name="xref" value="newsour">
			<?php echo Filter::getCsrf(); ?>
			<table class="facts_table">
				<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('TITL'); ?></td>
				<td class="optionbox wrap"><input type="text" data-autocomplete-type="SOUR_TITL" name="TITL" id="TITL" value="" size="60"> <?php echo FunctionsPrint::printSpecialCharacterLink('TITL'); ?></td></tr>
				<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('ABBR'); ?></td>
				<td class="optionbox wrap"><input type="text" name="ABBR" id="ABBR" value="" size="40" maxlength="255"> <?php echo FunctionsPrint::printSpecialCharacterLink('ABBR'); ?></td></tr>
				<?php if (strstr($WT_TREE->getPreference('ADVANCED_NAME_FACTS'), "_HEB") !== false) { ?>
				<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('_HEB'); ?></td>
				<td class="optionbox wrap"><input type="text" name="_HEB" id="_HEB" value="" size="60"> <?php echo FunctionsPrint::printSpecialCharacterLink('_HEB'); ?></td></tr>
				<?php } ?>
				<?php if (strstr($WT_TREE->getPreference('ADVANCED_NAME_FACTS'), "ROMN") !== false) { ?>
				<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('ROMN'); ?></td>
				<td class="optionbox wrap"><input  type="text" name="ROMN" id="ROMN" value="" size="60"> <?php echo FunctionsPrint::printSpecialCharacterLink('ROMN'); ?></td></tr>
				<?php } ?>
				<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('AUTH'); ?></td>
				<td class="optionbox wrap"><input type="text" name="AUTH" id="AUTH" value="" size="40" maxlength="255"> <?php echo FunctionsPrint::printSpecialCharacterLink('AUTH'); ?></td></tr>
				<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('PUBL'); ?></td>
				<td class="optionbox wrap"><textarea name="PUBL" id="PUBL" rows="5" cols="60"></textarea><br><?php echo FunctionsPrint::printSpecialCharacterLink('PUBL'); ?></td></tr>
				<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('REPO'); ?></td>
				<td class="optionbox wrap"><input type="text" data-autocomplete-type="REPO" name="REPO" id="REPO" value="" size="10"> <?php echo FunctionsPrint::printFindRepositoryLink('REPO'), ' ', FunctionsEdit::printAddNewRepositoryLink('REPO'); ?></td></tr>
				<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('CALN'); ?></td>
				<td class="optionbox wrap"><input type="text" name="CALN" id="CALN" value=""></td></tr>
				<?php echo keep_chan(); ?>
			</table>
				<a href="#"  onclick="return expand_layer('events');"><i id="events_img" class="icon-plus"></i>
				<?php echo I18N::translate('Associate events with this source'); ?></a>
				<div id="events" style="display: none;">
				<table class="facts_table">
				<tr>
					<td class="descriptionbox wrap width25"><?php echo I18N::translate('Select events'), FunctionsPrint::helpLink('edit_SOUR_EVEN'); ?></td>
					<td class="optionbox wrap"><select name="EVEN[]" multiple="multiple" size="5">
						<?php
						$parts = explode(',', $WT_TREE->getPreference('INDI_FACTS_ADD'));
						foreach ($parts as $key) {
							?><option value="<?php echo $key; ?>"><?php echo GedcomTag::getLabel($key); ?></option>
						<?php
						}
						$parts = explode(',', $WT_TREE->getPreference('FAM_FACTS_ADD'));
						foreach ($parts as $key) {
							?><option value="<?php echo $key; ?>"><?php echo GedcomTag::getLabel($key); ?></option>
						<?php
						}
						?>
					</select></td>
				</tr>
				<?php
				FunctionsEdit::addSimpleTag('0 DATE', 'EVEN');
				FunctionsEdit::addSimpleTag('0 PLAC', 'EVEN');
				FunctionsEdit::addSimpleTag('0 AGNC');
				?>
				</table>
			</div>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div>
	<?php
	break;

case 'addsourceaction':
	$controller
		->setPageTitle(I18N::translate('Create a new source'))
		->pageHeader();

	if (!Filter::checkCsrf()) {
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=addnewsource');

		return;
	}

	$newgedrec = '0 @XREF@ SOUR';
	$ABBR      = Filter::post('ABBR');
	if ($ABBR) {
		$newgedrec .= "\n1 ABBR " . $ABBR;
	}
	$TITL = Filter::post('TITL');
	if ($TITL) {
		$newgedrec .= "\n1 TITL " . $TITL;
		$_HEB = Filter::post('_HEB');
		if ($_HEB) {
			$newgedrec .= "\n2 _HEB " . $_HEB;
		}
		$ROMN = Filter::post('ROMN');
		if ($ROMN) {
			$newgedrec .= "\n2 ROMN " . $ROMN;
		}
	}
	$AUTH = Filter::post('AUTH');
	if ($AUTH) {
		$newgedrec .= "\n1 AUTH " . $AUTH;
	}
	$PUBL = Filter::post('PUBL');
	if ($PUBL) {
		$newgedrec .= "\n1 PUBL " . preg_replace('/\r?\n/', "\n2 CONT ", $PUBL);
	}
	$REPO = Filter::post('REPO', WT_REGEX_XREF);
	if ($REPO) {
		$newgedrec .= "\n1 REPO @" . $REPO . '@';
		$CALN = Filter::post('CALN');
		if ($CALN) {
			$newgedrec .= "\n1 CALN " . $CALN;
		}
	}
	$EVEN = Filter::postArray('EVEN', WT_REGEX_TAG);
	if ($EVEN) {
		$newgedrec .= "\n1 DATA";
		$newgedrec .= "\n2 EVEN " . implode(',', $EVEN);
		$EVEN_DATE = Filter::post('EVEN_DATE');
		if ($EVEN_DATE) {
			$newgedrec .= "\n3 EVEN_DATE " . $EVEN_DATE;
		}
		$EVEN_PLAC = Filter::post('EVEN_PLAC');
		if ($EVEN_PLAC) {
			$newgedrec .= "\n3 EVEN_PLAC " . $EVEN_PLAC;
		}
		$AGNC = Filter::post('AGNC');
		if ($AGNC) {
			$newgedrec .= "\n2 AGNC " . $AGNC;
		}
	}

	$record = $WT_TREE->createRecord($newgedrec);
	$controller->addInlineJavascript('openerpasteid("' . $record->getXref() . '");');
	break;

////////////////////////////////////////////////////////////////////////////////
// Create a new note record
////////////////////////////////////////////////////////////////////////////////
case 'addnewnote':
	$controller
		->setPageTitle(I18N::translate('Create a new shared note'))
		->pageHeader();

	?>
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>

		<form method="post" action="edit_interface.php" onsubmit="return check_form(this);">
			<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
			<input type="hidden" name="action" value="addnoteaction">
			<input type="hidden" name="noteid" value="newnote">
			<?php echo Filter::getCsrf(); ?>
			<?php
			echo '<table class="facts_table">';
			echo '<tr>';
			echo '<td class="descriptionbox nowrap">';
			echo I18N::translate('Shared note');
			echo '</td>';
			echo '<td class="optionbox wrap"><textarea name="NOTE" id="NOTE" rows="15" cols="87"></textarea>';
			echo FunctionsPrint::printSpecialCharacterLink('NOTE');
			echo '</td>';
			echo '</tr>';
			echo keep_chan();
			echo '</table>';
			?>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div>
	<?php
	break;

case 'addnoteaction':
	$controller
		->setPageTitle(I18N::translate('Create a new shared note'))
		->pageHeader();

	if (!Filter::checkCsrf()) {
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=addnewnote');

		return;
	}

	$gedrec = '0 @XREF@ NOTE ' . preg_replace("/\r?\n/", "\n1 CONT ", Filter::post('NOTE'));

	$record = $WT_TREE->createRecord($gedrec);
	$controller->addInlineJavascript('openerpasteid("' . $record->getXref() . '");');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addnewnote_assisted':
	require WT_ROOT . WT_MODULES_DIR . 'GEDFact_assistant/census/census-edit.php';
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addnoteaction_assisted':
	require WT_ROOT . WT_MODULES_DIR . 'GEDFact_assistant/census/census-save.php';
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addmedia_links':
	$pid = Filter::get('pid', WT_REGEX_XREF);

	$person = Individual::getInstance($pid, $WT_TREE);
	check_record_access($person);

	$controller
		->setPageTitle(I18N::translate('Family navigator') . ' — ' . $person->getFullName())
		->pageHeader();

	?>
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>
		<form method="post" action="edit_interface.php?xref=<?php echo $person->getXref(); ?>" onsubmit="findindi()">
			<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
			<input type="hidden" name="action" value="addmedia_links">
			<input type="hidden" name="noteid" value="newnote">
			<?php echo Filter::getCsrf(); ?>
			<?php require WT_ROOT . WT_MODULES_DIR . 'GEDFact_assistant/MEDIA_ctrl.php'; ?>
		</form>
	</div>
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
// Edit a note record
////////////////////////////////////////////////////////////////////////////////
case 'editnote':
	$xref = Filter::get('xref', WT_REGEX_XREF);

	$note = Note::getInstance($xref, $WT_TREE);
	check_record_access($note);

	$controller
		->setPageTitle(I18N::translate('Edit shared note'))
		->pageHeader();

	?>
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>
		<form method="post" action="edit_interface.php">
			<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
			<input type="hidden" name="action" value="editnoteaction">
			<input type="hidden" name="xref" value="<?php echo $xref; ?>">
			<?php echo Filter::getCsrf(); ?>
			<table class="facts_table">
				<tr>
					<td class="descriptionbox wrap width25"><?php echo I18N::translate('Shared note'); ?></td>
					<td class="optionbox wrap">
						<textarea name="NOTE" id="NOTE" rows="15" cols="90"><?php echo Filter::escapeHtml($note->getNote()); ?></textarea>
						<br>
						<?php echo FunctionsPrint::printSpecialCharacterLink('NOTE'); ?>
					</td>
				</tr>
				<?php echo keep_chan($note); ?>
			</table>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div>
	<?php
	break;

case 'editnoteaction':
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$keep_chan = Filter::postBool('keep_chan');
	$note      = Filter::post('NOTE');

	if (!Filter::checkCsrf()) {
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=editnote&xref=' . $xref);

		return;
	}

	$record = Note::getInstance($xref, $WT_TREE);
	check_record_access($record);

	$controller
		->setPageTitle(I18N::translate('Edit shared note'))
		->pageHeader();

	// We have user-supplied data in a replacement string - escape it against backreferences
	$note = str_replace(array('\\', '$'), array('\\\\', '\\$'), $note);

	$gedrec = preg_replace(
		'/^0 @' . $record->getXref() . '@ NOTE.*(\n1 CONT.*)*/',
		'0 @' . $record->getXref() . '@ NOTE ' . preg_replace("/\r?\n/", "\n1 CONT ", $note),
		$record->getGedcom()
	);

	$record->updateRecord($gedrec, !$keep_chan);

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
// Create a new repository
////////////////////////////////////////////////////////////////////////////////
case 'addnewrepository':
	$controller
		->setPageTitle(I18N::translate('Create a new repository'))
		->pageHeader();

	echo '<div id="edit_interface-page">';
	echo '<h4>', $controller->getPageTitle(), '</h4>';

	echo '<script>';
	?>
		function check_form(frm) {
			if (frm.NAME.value=="") {
				alert('<?php echo I18N::translate('You must provide a repository name.'); ?>');
				frm.NAME.focus();
				return false;
			}
			return true;
		}
	<?php
	echo '</script>';
	?>
	<form method="post" action="edit_interface.php" onsubmit="return check_form(this);">
		<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
		<input type="hidden" name="action" value="addrepoaction">
		<input type="hidden" name="xref" value="newrepo">
		<?php echo Filter::getCsrf(); ?>
		<table class="facts_table">
			<tr><td class="descriptionbox wrap width25"><?php echo I18N::translate('Repository name'); ?></td>
			<td class="optionbox wrap"><input type="text" name="REPO_NAME" id="REPO_NAME" value="" size="40" maxlength="255"> <?php echo FunctionsPrint::printSpecialCharacterLink('REPO_NAME'); ?></td></tr>
			<?php if (strstr($WT_TREE->getPreference('ADVANCED_NAME_FACTS'), "_HEB") !== false) { ?>
			<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('_HEB'); ?></td>
			<td class="optionbox wrap"><input type="text" name="_HEB" id="_HEB" value="" size="40" maxlength="255"> <?php echo FunctionsPrint::printSpecialCharacterLink('_HEB'); ?></td></tr>
			<?php } ?>
			<?php if (strstr($WT_TREE->getPreference('ADVANCED_NAME_FACTS'), "ROMN") !== false) { ?>
			<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('ROMN'); ?></td>
			<td class="optionbox wrap"><input type="text" name="ROMN" id="ROMN" value="" size="40" maxlength="255"> <?php echo FunctionsPrint::printSpecialCharacterLink('ROMN'); ?></td></tr>
			<?php } ?>
			<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('ADDR'); ?></td>
			<td class="optionbox wrap"><textarea name="ADDR" id="ADDR" rows="5" cols="60"></textarea><?php echo FunctionsPrint::printSpecialCharacterLink('ADDR'); ?> </td></tr>
			<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('PHON'); ?></td>
			<td class="optionbox wrap"><input type="text" name="PHON" id="PHON" value="" size="40" maxlength="255"> </td></tr>
			<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('EMAIL'); ?></td>
			<td class="optionbox wrap"><input type="text" name="EMAIL" id="EMAIL" value="" size="40" maxlength="255"></td></tr>
			<tr><td class="descriptionbox wrap width25"><?php echo GedcomTag::getLabel('WWW'); ?></td>
			<td class="optionbox wrap"><input type="text" name="WWW" id="WWW" value="" size="40" maxlength="255"> </td></tr>
			<?php echo keep_chan(); ?>
		</table>
		<p id="save-cancel">
			<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
			<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
		</p>
	</form>
	</div>
	<?php
	break;

case 'addrepoaction':

	if (!Filter::checkCsrf()) {
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=addnewrepository');

		return;
	}

	$controller
		->setPageTitle(I18N::translate('Create a new repository'))
		->pageHeader();

	$gedrec    = '0 @XREF@ REPO';
	$REPO_NAME = Filter::post('REPO_NAME');
	if ($REPO_NAME) {
		$gedrec .= "\n1 NAME " . $REPO_NAME;
		$_HEB = Filter::post('_HEB');
		if ($_HEB) {
			$gedrec .= "\n2 _HEB " . $_HEB;
		}
		$ROMN = Filter::post('ROMN');
		if ($ROMN) {
			$gedrec .= "\n2 ROMN " . $ROMN;
		}
	}
	$ADDR = Filter::post('ADDR');
	if ($ADDR) {
		$gedrec .= "\n1 ADDR " . preg_replace('/\r?\n/', "\n2 CONT ", $ADDR);
	}
	$PHON = Filter::post('PHON');
	if ($PHON) {
		$gedrec .= "\n1 PHON " . $PHON;
	}
	$FAX = Filter::post('FAX');
	if ($FAX) {
		$gedrec .= "\n1 FAX " . $FAX;
	}
	$EMAIL = Filter::post('EMAIL');
	if ($EMAIL) {
		$gedrec .= "\n1 EMAIL " . $EMAIL;
	}
	$WWW = Filter::post('WWW');
	if ($WWW) {
		$gedrec .= "\n1 WWW " . $WWW;
	}

	$record = $WT_TREE->createRecord($gedrec);
	$controller->addInlineJavascript('openerpasteid("' . $record->getXref() . '");');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'editname':
	$xref    = Filter::get('xref', WT_REGEX_XREF);
	$fact_id = Filter::get('fact_id');

	$person = Individual::getInstance($xref, $WT_TREE);
	check_record_access($person);

	// Find the fact to edit
	$name_fact = null;
	foreach ($person->getFacts() as $fact) {
		if ($fact->getFactId() === $fact_id && $fact->canEdit()) {
			$name_fact = $fact;
		}
	}
	if (!$name_fact) {
		$controller
			->pageHeader()
			->addInlineJavascript('closePopupAndReloadParent();');

		return;
	}

	$controller
		->setPageTitle(I18N::translate('Edit name'))
		->pageHeader();

	print_indi_form('update', $person, null, $name_fact, '', $person->getSex());

	break;

////////////////////////////////////////////////////////////////////////////////
case 'addname':
	$xref = Filter::get('xref', WT_REGEX_XREF);

	$person = Individual::getInstance($xref, $WT_TREE);
	check_record_access($person);

	$controller
		->setPageTitle(I18N::translate('Add a new name'))
		->pageHeader();

	print_indi_form('update', $person, null, null, '', $person->getSex());
	break;

////////////////////////////////////////////////////////////////////////////////
// Change the order of media objects
////////////////////////////////////////////////////////////////////////////////
case 'reorder_media':
	$xref = Filter::get('xref', WT_REGEX_XREF);

	$person = Individual::getInstance($xref, $WT_TREE);
	check_record_access($person);

	$controller
		->setPageTitle(I18N::translate('Re-order media'))
		->pageHeader()
		->addInlineJavascript('
			jQuery("#reorder_media_list").sortable({forceHelperSize: true, forcePlaceholderSize: true, opacity: 0.7, cursor: "move", axis: "y"});

			//-- update the order numbers after drag-n-drop sorting is complete
			jQuery("#reorder_media_list").bind("sortupdate", function(event, ui) {
					jQuery("#"+jQuery(this).attr("id")+" input").each(
						function (index, value) {
							value.value = index+1;
						}
					);
				});
		');

	// Get the current sort order
	$sort_obje = array();
	foreach ($person->getFacts('_WT_OBJE_SORT') as $fact) {
		$media = $fact->getTarget();
		if ($media && $media->canShow()) {
			$sort_obje[] = $media;
		}
	}

	// Add other media objects from the individual and any spouse-families
	$record_list = array($person);
	foreach ($person->getSpouseFamilies() as $family) {
		$record_list[] = $family;
	}
	foreach ($record_list as $record) {
		if ($record->canShow()) {
			foreach ($record->getFacts() as $fact) {
				if (!$fact->isPendingDeletion()) {
					preg_match_all('/(?:^1|\n\d) OBJE @(' . WT_REGEX_XREF . ')@/', $fact->getGedcom(), $matches);
					foreach ($matches[1] as $match) {
						$media = Media::getInstance($match, $WT_TREE);
						if (!in_array($media, $sort_obje)) {
							$sort_obje[] = $media;
						}
					}
				}
			}
		}
	}

	?>
	<div id="edit_interface-page">
		<h4><?php echo I18N::translate('Click a row, then drag-and-drop to re-order media'); ?></h4>
		<form name="reorder_form" method="post" action="edit_interface.php">
			<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
			<input type="hidden" name="action" value="reorder_media_update">
			<input type="hidden" name="xref" value="<?php echo $xref; ?>">
			<?php echo Filter::getCsrf(); ?>
			<ul id="reorder_media_list">
			<?php foreach ($sort_obje as $n => $obje) { ?>
				<li class="facts_value" style="list-style:none;cursor:move;margin-bottom:2px;" id="li_<?php echo $obje->getXref(); ?>">
					<table class="pic">
						<tr>
							<td width="80" valign="top" align="center">
								<?php echo $obje->displayImage(); ?>
							</td>
							<td>
								<?php echo $obje->getFullName(); ?>
							</td>
						</tr>
					</table>
					<input type="hidden" name="order1[<?php echo $obje->getXref(); ?>]" value="<?php echo $n; ?>">
				</li>
			<?php } ?>
			</ul>
			<table class="facts_table">
				<?php echo keep_chan($record); ?>
			</table>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div>
	<?php
	break;

case 'reorder_media_update':
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$order1    = Filter::post('order1');
	$keep_chan = Filter::postBool('keep_chan');

	if (!Filter::checkCsrf()) {
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=reorder_media_&xref=' . $xref);

		return;
	}

	$person = Individual::getInstance($xref, $WT_TREE);
	check_record_access($person);

	$controller
		->setPageTitle(I18N::translate('Re-order media'))
		->pageHeader();

	// Delete any existing _WT_OBJE_SORT records
	$facts = array('0 @' . $person->getXref() . '@ INDI');
	foreach ($person->getFacts() as $fact) {
		if ($fact->getTag() !== '_WT_OBJE_SORT') {
			$facts[] = $fact->getGedcom();
		}
	}
	if (is_array($order1)) {
		// Add new _WT_OBJE_SORT records
		foreach ($order1 as $xref => $n) {
			$facts[] = '1 _WT_OBJE_SORT @' . $xref . '@';
		}
	}

	$person->updateRecord(implode("\n", $facts), !$keep_chan);

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
// Change the order of children within a family record
////////////////////////////////////////////////////////////////////////////////
case 'reorder_children':
	$xref   = Filter::post('xref', WT_REGEX_XREF, Filter::get('xref', WT_REGEX_XREF));
	$option = Filter::post('option');

	$family = Family::getInstance($xref, $WT_TREE);
	check_record_access($family);

	$controller
		->addInlineJavascript('jQuery("#reorder_list").sortable({forceHelperSize: true, forcePlaceholderSize: true, opacity: 0.7, cursor: "move", axis: "y"});')
		->addInlineJavascript('jQuery("#reorder_list").bind("sortupdate", function(event, ui) { jQuery("#"+jQuery(this).attr("id")+" input").each( function (index, value) { value.value = index+1; }); });')
		->setPageTitle(I18N::translate('Re-order children'))
		->pageHeader();

	?>
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>
		<form name="reorder_form" method="post" action="edit_interface.php">
			<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
			<input type="hidden" name="action" value="reorder_update">
			<input type="hidden" name="xref" value="<?php echo $xref; ?>">
			<input type="hidden" name="option" value="bybirth">
			<?php echo Filter::getCsrf(); ?>
			<ul id="reorder_list">
				<?php
				// reorder children in modified families
				$ids = array();
				foreach ($family->getChildren() as $child) {
					$ids[] = $child->getXref();
				}
				$children = array();
				foreach ($family->getChildren() as $k => $child) {
					$bdate = $child->getEstimatedBirthDate();
					if ($bdate->isOK()) {
						$sortkey = $bdate->julianDay();
					} else {
						$sortkey = 1e8; // birth date missing => sort last
					}
					$children[$child->getXref()] = $sortkey;
				}
				if ($option === 'bybirth') {
					asort($children);
				}
				$i = 0;
				foreach ($children as $id => $child) {
					echo '<li style="cursor:move; margin-bottom:2px; position:relative;"';
					if (!in_array($id, $ids)) {
						echo ' class="facts_value new"';
					}
					echo ' id="li_', $id, '">';
					echo Theme::theme()->individualBoxLarge(Individual::getInstance($id, $WT_TREE));
					echo '<input type="hidden" name="order[', $id, ']" value="', $i, '">';
					echo '</li>';
					$i++;
				}
			echo '</ul>';
			?>
			<table>
				<?php echo keep_chan($family); ?>
			</table>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
				<input type="submit" class="save" onclick="document.reorder_form.action.value='reorder_children'; document.reorder_form.submit();" value="<?php echo I18N::translate('sort by date of birth'); ?>">
				<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div>
	<?php
	break;

case 'reorder_update':
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$order     = Filter::post('order');
	$keep_chan = Filter::postBool('keep_chan');

	if (!Filter::checkCsrf()) {
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=reorder_children&xref=' . $xref);

		return;
	}

	$family = Family::getInstance($xref, $WT_TREE);
	check_record_access($family);

	$controller
		->setPageTitle(I18N::translate('Re-order children'))
		->pageHeader();

	if (is_array($order)) {
		$gedcom = array('0 @' . $family->getXref() . '@ FAM');
		$facts  = $family->getFacts();

		// Move children to the end of the record
		foreach ($order as $child => $num) {
			foreach ($facts as $n => $fact) {
				if ($fact->getValue() === '@' . $child . '@') {
					$facts[] = $fact;
					unset($facts[$n]);
					break;
				}
			}
		}
		foreach ($facts as $fact) {
			$gedcom[] = $fact->getGedcom();
		}

		$family->updateRecord(implode("\n", $gedcom), !$keep_chan);
	}

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
// Change the members of a family record
////////////////////////////////////////////////////////////////////////////////
case 'changefamily':
	$xref = Filter::get('xref', WT_REGEX_XREF);

	$family = Family::getInstance($xref, $WT_TREE);
	check_record_access($family);

	$controller
		->setPageTitle(I18N::translate('Change family members') . ' – ' . $family->getFullName())
		->pageHeader();

	$father   = $family->getHusband();
	$mother   = $family->getWife();
	$children = $family->getChildren();
	?>
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>
		<div id="changefam">
			<form name="changefamform" method="post" action="edit_interface.php">
				<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
				<input type="hidden" name="action" value="changefamily_update">
				<input type="hidden" name="xref" value="<?php echo $xref; ?>">
				<?php echo Filter::getCsrf(); ?>
				<table>
					<tr>
					<?php if ($father) { ?>
						<td class="descriptionbox">
							<b>
								<?php
								switch ($father->getSex()) {
								case 'M': echo I18N::translate('husband'); break;
								case 'F': echo I18N::translate('wife'); break;
								default:  echo I18N::translate('spouse'); break;
								}
								?>
							</b>
							<input type="hidden" name="HUSB" value="<?php echo $father->getXref(); ?>">
						</td>
						<td id="HUSBName" class="optionbox"><?php echo $father->getFullName(); ?>
						</td>
					<?php } else { ?>
						<td class="descriptionbox">
							<b><?php echo I18N::translate('spouse'); ?></b>
							<input type="hidden" name="HUSB" value="">
						</td>
						<td id="HUSBName" class="optionbox">
						</td>
					<?php } ?>
						<td class="optionbox">
							<a href="#" id="husbrem" style="display: <?php echo is_null($father) ? 'none' : 'block'; ?>;" onclick="document.changefamform.HUSB.value=''; document.getElementById('HUSBName').innerHTML=''; this.style.display='none'; return false;">
								<?php echo I18N::translate('Remove'); ?>
							</a>
						</td>
						<td class="optionbox">
							<a href="#" onclick="return findIndi(document.changefamform.HUSB, document.getElementById('HUSBName'));">
								<?php echo I18N::translate('Change'); ?>
							</a>
						</td>
					</tr>
					<tr>
					<?php if ($mother) { ?>
						<td class="descriptionbox">
							<b>
								<?php
								switch ($mother->getSex()) {
								case 'M': echo I18N::translate('husband'); break;
								case 'F': echo I18N::translate('wife'); break;
								default:  echo I18N::translate('spouse'); break;
								}
								?>
							</b>
							<input type="hidden" name="WIFE" value="<?php echo $mother->getXref(); ?>">
						</td>
						<td id="WIFEName" class="optionbox">
							<?php echo $mother->getFullName(); ?>
						</td>
					<?php } else { ?>
						<td class="descriptionbox">
							<b><?php echo I18N::translate('spouse'); ?></b>
							<input type="hidden" name="WIFE" value="">
						</td>
						<td id="WIFEName" class="optionbox">
						</td>
					<?php } ?>
						<td class="optionbox">
							<a href="#" id="wiferem" style="display: <?php echo is_null($mother) ? 'none' : 'block'; ?>;" onclick="document.changefamform.WIFE.value=''; document.getElementById('WIFEName').innerHTML=''; this.style.display='none'; return false;">
								<?php echo I18N::translate('Remove'); ?>
							</a>
						</td>
						<td class="optionbox">
							<a href="#" onclick="return findIndi(document.changefamform.WIFE, document.getElementById('WIFEName'));">
								<?php echo I18N::translate('Change'); ?>
							</a>
						</td>
					</tr>
					<?php $i = 0; foreach ($children as $child) { ?>
					<tr>
						<td class="descriptionbox">
							<b>
								<?php
								switch ($child->getSex()) {
								case 'M': echo I18N::translate('son'); break;
								case 'F': echo I18N::translate('daughter'); break;
								default:  echo I18N::translate('child'); break;
								}
								?>
							</b>
							<input type="hidden" name="CHIL<?php echo $i; ?>" value="<?php echo $child->getXref(); ?>">
						</td>
						<td id="CHILName<?php echo $i; ?>" class="optionbox"><?php echo $child->getFullName(); ?>
						</td>
						<td class="optionbox">
							<a href="#" id="childrem<?php echo $i; ?>" style="display: block;" onclick="document.changefamform.CHIL<?php echo $i; ?>.value=''; document.getElementById('CHILName<?php echo $i; ?>').innerHTML=''; this.style.display='none'; return false;">
								<?php echo I18N::translate('Remove'); ?>
							</a>
						</td>
						<td class="optionbox">
							<a href="#" onclick="return findIndi(document.changefamform.CHIL<?php echo $i; ?>, document.getElementById('CHILName<?php echo $i; ?>'));">
								<?php echo I18N::translate('Change'); ?>
							</a>
						</td>
					</tr>
					<?php $i++; } ?>
					<tr>
						<td class="descriptionbox">
							<b><?php echo I18N::translate('child'); ?></b>
							<input type="hidden" name="CHIL<?php echo $i; ?>" value="">
						</td>
						<td id="CHILName<?php echo $i; ?>" class="optionbox">
						</td>
						<td colspan="2" class="optionbox child">
							<a href="#" id="childrem<?php echo $i; ?>" style="display: none;" onclick="document.changefamform.CHIL<?php echo $i; ?>.value=''; document.getElementById('CHILName<?php echo $i; ?>').innerHTML=''; this.style.display='none'; return false;">
								<?php echo I18N::translate('Remove'); ?>
							</a>
							<a href="#" onclick="remElement = document.getElementById('childrem<?php echo $i; ?>'); return findIndi(document.changefamform.CHIL<?php echo $i; ?>, document.getElementById('CHILName<?php echo $i; ?>'));">
								<?php echo I18N::translate('Add'); ?>
							</a>
						</td>
					</tr>
				</table>
				<p id="save-cancel">
					<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
					<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
				</p>
			</form>
		</div>
	</div>
	<?php
	break;

case 'changefamily_update':
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$HUSB      = Filter::post('HUSB', WT_REGEX_XREF);
	$WIFE      = Filter::post('WIFE', WT_REGEX_XREF);
	$keep_chan = Filter::postBool('keep_chan');

	if (!Filter::checkCsrf()) {
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=changefamily&xref=' . $xref);

		return;
	}

	$CHIL = array();
	for ($i = 0;; ++$i) {
		if (isset($_POST['CHIL' . $i])) {
			$CHIL[] = Filter::post('CHIL' . $i, WT_REGEX_XREF);
		} else {
			break;
		}
	}

	$family = Family::getInstance($xref, $WT_TREE);
	check_record_access($family);

	$controller
		->setPageTitle(I18N::translate('Change family members') . ' – ' . $family->getFullName())
		->pageHeader();

	// Current family members
	$old_father   = $family->getHusband();
	$old_mother   = $family->getWife();
	$old_children = $family->getChildren();

	// New family members
	$new_father   = Individual::getInstance($HUSB, $WT_TREE);
	$new_mother   = Individual::getInstance($WIFE, $WT_TREE);
	$new_children = array();
	if (is_array($CHIL)) {
		foreach ($CHIL as $child) {
			$new_children[] = Individual::getInstance($child, $WT_TREE);
		}
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

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
// Change the order of FAMS records within an INDI record
////////////////////////////////////////////////////////////////////////////////
case 'reorder_fams':
	$xref   = Filter::post('xref', WT_REGEX_XREF, Filter::get('xref', WT_REGEX_XREF));
	$option = Filter::post('option');

	$person = Individual::getInstance($xref, $WT_TREE);
	check_record_access($person);

	$controller
		->addInlineJavascript('jQuery("#reorder_list").sortable({forceHelperSize: true, forcePlaceholderSize: true, opacity: 0.7, cursor: "move", axis: "y"});')
		//-- update the order numbers after drag-n-drop sorting is complete
		->addInlineJavascript('jQuery("#reorder_list").bind("sortupdate", function(event, ui) { jQuery("#"+jQuery(this).attr("id")+" input").each( function (index, value) { value.value = index+1; }); });')
		->setPageTitle(I18N::translate('Re-order families'))
		->pageHeader();

	$fams = $person->getSpouseFamilies();
	if ($option === 'bymarriage') {
		usort($fams, '\Fisharebest\Webtrees\Family::compareMarrDate');
	}

	?>
	<div id="edit_interface-page">
	<h4><?php echo $controller->getPageTitle(); ?></h4>
	<form name="reorder_form" method="post" action="edit_interface.php">
		<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
		<input type="hidden" name="action" value="reorder_fams_update">
		<input type="hidden" name="xref" value="<?php echo $xref; ?>">
		<input type="hidden" name="option" value="bymarriage">
		<?php echo Filter::getCsrf(); ?>
		<ul id="reorder_list">
		<?php foreach ($fams as $n => $family) { ?>
			<li class="facts_value" style="cursor:move;margin-bottom:2px;" id="li_<?php echo $family->getXref(); ?>">
				<div class="name2"><?php echo $family->getFullName(); ?></div>
				<?php echo $family->formatFirstMajorFact(WT_EVENTS_MARR, 2); ?>
				<input type="hidden" name="order[<?php echo $family->getXref(); ?>]" value="<?php echo $n; ?>">
			</li>
		<?php } ?>
		</ul>
		<p id="save-cancel">
			<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>">
			<input type="submit" class="save" onclick="document.reorder_form.action.value='reorder_fams'; document.reorder_form.submit();" value="<?php echo I18N::translate('sort by date of marriage'); ?>">
			<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
		</p>
	</form>
	</div>
	<?php
	break;

case 'reorder_fams_update':
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$order     = Filter::post('order');
	$keep_chan = Filter::postBool('keep_chan');

	if (!Filter::checkCsrf()) {
		header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=reorder_fams&xref=' . $xref);

		return;
	}

	$person = Individual::getInstance($xref, $WT_TREE);
	check_record_access($person);

	$controller
		->setPageTitle(I18N::translate('Re-order families'))
		->pageHeader();

	if (is_array($order)) {
		$gedcom = array('0 @' . $person->getXref() . '@ INDI');
		$facts  = $person->getFacts();

		// Move families to the end of the record
		foreach ($order as $family => $num) {
			foreach ($facts as $n => $fact) {
				if ($fact->getValue() === '@' . $family . '@') {
					$facts[] = $fact;
					unset($facts[$n]);
					break;
				}
			}
		}
		foreach ($facts as $fact) {
			$gedcom[] = $fact->getGedcom();
		}

		$person->updateRecord(implode("\n", $gedcom), !$keep_chan);
	}

	$controller->addInlineJavascript('closePopupAndReloadParent();');
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
	global $WT_TREE;

	if (Auth::isAdmin()) {
		if ($record) {
			$details =
				GedcomTag::getLabelValue('DATE', $record->lastChangeTimestamp()) .
				GedcomTag::getLabelValue('_WT_USER', Filter::escapeHtml($record->lastChangeUser()));
		} else {
			$details = '';
		}

		return
			'<tr><td class="descriptionbox wrap width25">' .
			GedcomTag::getLabel('CHAN') .
			'</td><td class="optionbox wrap">' .
			'<input type="checkbox" name="keep_chan" value="1" ' . ($WT_TREE->getPreference('NO_UPDATE_CHAN') ? 'checked' : '') . '>' .
			I18N::translate('Keep the existing “last change” information') .
			$details .
			'</td></tr>';
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
	global $WT_TREE, $bdm, $controller;

	if ($person) {
		$xref = $person->getXref();
	} elseif ($family) {
		$xref = $family->getXref();
	} else {
		$xref = 'new';
	}

	// Different cultures do surnames differently
	$surname_tradition = SurnameTradition::create($WT_TREE->getPreference('SURNAME_TRADITION'));

	$name_fields = array();
	if ($name_fact) {
		// Editing an existing name
		$name_fact_id = $name_fact->getFactId();
		$name_type    = $name_fact->getAttribute('TYPE');
		$namerec      = $name_fact->getGedcom();
		foreach (Config::standardNameFacts() as $tag) {
			if ($tag === 'NAME') {
				$name_fields[$tag] = $name_fact->getValue();
			} else {
				$name_fields[$tag] = $name_fact->getAttribute($tag);
			}
		}
		// Populate any missing 2 XXXX fields from the 1 NAME field
		$npfx_accept = implode('|', Config::namePrefixes());
		if (preg_match('/(((' . $npfx_accept . ')\.? +)*)([^\n\/"]*)("(.*)")? *\/(([a-z]{2,3} +)*)(.*)\/ *(.*)/i', $name_fields['NAME'], $name_bits)) {
			if (empty($name_fields['NPFX'])) {
				$name_fields['NPFX'] = $name_bits[1];
			}
			if (empty($name_fields['SPFX']) && empty($name_fields['SURN'])) {
				$name_fields['SPFX'] = trim($name_bits[7]);
				// For names with two surnames, there will be four slashes.
				// Turn them into a list
				$name_fields['SURN'] = preg_replace('~/[^/]*/~', ',', $name_bits[9]);
			}
			if (empty($name_fields['GIVN'])) {
				$name_fields['GIVN'] = $name_bits[4];
			}
			if (empty($name_fields['NICK']) && !empty($name_bits[6]) && !preg_match('/^2 NICK/m', $namerec)) {
				$name_fields['NICK'] = $name_bits[6];
			}
		}

	} else {
		// Creating a new name
		$name_fact_id = null;
		$name_type    = null;
		$namerec      = null;
		// Populate the standard NAME field and subfields
		foreach (Config::standardNameFacts() as $tag) {
			$name_fields[$tag] = '';
		}
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

	echo '<div id="edit_interface-page">';
	echo '<h4>', $controller->getPageTitle(), '</h4>';
	FunctionsPrint::initializeCalendarPopup();
	echo '<form method="post" name="addchildform" onsubmit="return checkform();">';
	echo '<input type="hidden" name="ged" value="', $WT_TREE->getNameHtml(), '">';
	echo '<input type="hidden" name="action" value="', $nextaction, '">';
	echo '<input type="hidden" name="fact_id" value="', $name_fact_id, '">';
	echo '<input type="hidden" name="xref" value="', $xref, '">';
	echo '<input type="hidden" name="famtag" value="', $famtag, '">';
	echo '<input type="hidden" name="gender" value="', $gender, '">';
	echo '<input type="hidden" name="goto" value="">'; // set by javascript
	echo Filter::getCsrf();
	echo '<table class="facts_table">';

	switch ($nextaction) {
	case 'add_child_to_family_action':
	case 'add_child_to_individual_action':
		// When adding a new child, specify the pedigree
		FunctionsEdit::addSimpleTag('0 PEDI');
		break;
	case 'update':
		// When adding/editing a name, specify the type
		FunctionsEdit::addSimpleTag('0 TYPE ' . $name_type, '', '', null, $person);
		break;
	}

	// First - new/existing standard name fields
	foreach ($name_fields as $tag => $value) {
		if (substr_compare($tag, '_', 0, 1) !== 0) {
			FunctionsEdit::addSimpleTag('0 ' . $tag . ' ' . $value);
		}
	}

	// Second - new/existing advanced name fields
	if ($surname_tradition->hasMarriedNames() || preg_match('/\n2 _MARNM /', $namerec)) {
		$adv_name_fields = array('_MARNM' => '');
	} else {
		$adv_name_fields = array();
	}
	if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('ADVANCED_NAME_FACTS'), $match)) {
		foreach ($match[1] as $tag) {
			$adv_name_fields[$tag] = '';
		}
	}

	foreach (array_keys($adv_name_fields) as $tag) {
		// Edit existing tags, grouped together
		if (preg_match_all('/2 ' . $tag . ' (.+)/', $namerec, $match)) {
			foreach ($match[1] as $value) {
				FunctionsEdit::addSimpleTag('2 ' . $tag . ' ' . $value, '', GedcomTag::getLabel('NAME:' . $tag, $person));
				if ($tag === '_MARNM') {
					preg_match_all('/\/([^\/]*)\//', $value, $matches);
					FunctionsEdit::addSimpleTag('2 _MARNM_SURN ' . implode(',', $matches[1]));
				}
			}
		}
		// Allow a new tag to be entered
		if (!array_key_exists($tag, $name_fields)) {
			FunctionsEdit::addSimpleTag('0 ' . $tag, '', GedcomTag::getLabel('NAME:' . $tag, $person));
			if ($tag === '_MARNM') {
				FunctionsEdit::addSimpleTag('0 _MARNM_SURN');
			}
		}
	}

	// Third - new/existing custom name fields
	foreach ($name_fields as $tag => $value) {
		if (substr_compare($tag, '_', 0, 1) === 0) {
			FunctionsEdit::addSimpleTag('0 ' . $tag . ' ' . $value);
			if ($tag === '_MARNM') {
				preg_match_all('/\/([^\/]*)\//', $value, $matches);
				FunctionsEdit::addSimpleTag('2 _MARNM_SURN ' . implode(',', $matches[1]));
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
		$tags     = array();
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
				FunctionsEdit::addSimpleTag($level . ' ' . $type . ' ' . $text);
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
		echo '</table><br><table class="facts_table">';
		// 1 SEX
		if ($famtag === 'HUSB' || $gender === 'M') {
			FunctionsEdit::addSimpleTag("0 SEX M");
		} elseif ($famtag === 'WIFE' || $gender === 'F') {
			FunctionsEdit::addSimpleTag('0 SEX F');
		} else {
			FunctionsEdit::addSimpleTag('0 SEX');
		}
		$bdm = 'BD';
		if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
			foreach ($matches[1] as $match) {
				if (!in_array($match, explode('|', WT_EVENTS_DEAT))) {
					FunctionsEdit::addSimpleTags($match);
				}
			}
		}
		//-- if adding a spouse add the option to add a marriage fact to the new family
		if ($nextaction === 'add_spouse_to_individual_action' || $nextaction === 'add_spouse_to_family_action') {
			$bdm .= 'M';
			if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('QUICK_REQUIRED_FAMFACTS'), $matches)) {
				foreach ($matches[1] as $match) {
					FunctionsEdit::addSimpleTags($match);
				}
			}
		}
		if (preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
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

	// If we are editing an existing name, allow raw GEDCOM editing
	if ($name_fact && (Auth::isAdmin() || $WT_TREE->getPreference('SHOW_GEDCOM_RECORD'))) {
		echo
			'<br><br><a href="edit_interface.php?action=editrawfact&amp;xref=', $xref, '&amp;fact_id=', $name_fact->getFactId(), '&amp;ged=', $WT_TREE->getNameUrl(), '">',
			I18N::translate('Edit raw GEDCOM'),
			'</a>';
	}

	echo '<p id="save-cancel">';
	echo '<input type="submit" class="save" value="', /* I18N: button label */ I18N::translate('save'), '">';
	if (preg_match('/^add_(child|spouse|parent|unlinked_indi)/', $nextaction)) {
		echo '<input type="submit" class="save" value="', /* I18N: button label */ I18N::translate('go to new individual'), '" onclick="document.addchildform.goto.value=\'new\';">';
	}
	echo '<input type="button" class="cancel" value="', /* I18N: button label */ I18N::translate('close'), '" onclick="window.close();">';
	echo '</p>';
	echo '</form>';
	$controller->addInlineJavascript('
	SURNAME_TRADITION="' . $WT_TREE->getPreference('SURNAME_TRADITION') . '";
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
		var npfx = jQuery("#NPFX").val();
		var givn = jQuery("#GIVN").val();
		var spfx = jQuery("#SPFX").val();
		var surn = jQuery("#SURN").val();
		var nsfx = jQuery("#NSFX").val();
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
		if (WT_LOCALE === "vi" || WT_LOCALE === "hu") {
			// Default format: /SURN/ GIVN
			return trim(npfx+" /"+trim(spfx+" "+surn).replace(/ *, */g, " ")+"/ "+givn.replace(/ *, */g, " ")+" "+nsfx);
		} else if (WT_LOCALE === "zh-Hans" || WT_LOCALE === "zh-Hant") {
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
		var npfx = jQuery("#NPFX").val();
		var givn = jQuery("#GIVN").val();
		var spfx = jQuery("#SPFX").val();
		var surn = jQuery("#SURN").val();
		var nsfx = jQuery("#NSFX").val();
		var name = generate_name();
		jQuery("#NAME").val(name);
		jQuery("#NAME_display").text(name);
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
	// <input type="text">   <span style="display:hidden">
	var oldName = "";

	// Calls to generate_name() trigger an update - hence need to
	// set the manual change to true first.  We are probably
	// listening to the wrong events on the input fields...
	var manualChange = true;
	manualChange = generate_name() !== jQuery("#NAME").val();

	function convertHidden(eid) {
		var input1 = jQuery("#" + eid);
		var input2 = jQuery("#" + eid + "_display");
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
	echo '</div>';
}

/**
 * Can we edit a GedcomRecord object
 *
 * @param GedcomRecord $object
 */
function check_record_access(GedcomRecord $object = null) {
	global $controller;

	if (!$object || !$object->canShow() || !$object->canEdit()) {
		$controller
			->pageHeader()
			->addInlineJavascript('closePopupAndReloadParent();');
		exit;
	}
}
