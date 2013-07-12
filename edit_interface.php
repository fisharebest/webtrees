<?php
// PopUp Window to provide editing features.
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

define('WT_SCRIPT_NAME', 'edit_interface.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$action = safe_REQUEST($_REQUEST, 'action');

$controller=new WT_Controller_Simple();
$controller
	->requireEditorLogin()
	->addExternalJavascript(WT_STATIC_URL.'js/autocomplete.js')
	->addInlineJavascript('
	var locale_date_format="' . preg_replace('/[^DMY]/', '', str_replace(array('J', 'F'), array('D', 'M'), strtoupper($DATE_FORMAT))). '";
	function openerpasteid(id) {
		if (window.opener.paste_id) {
			window.opener.paste_id(id);
		}
		window.close();
	}
	function paste_id(value) {
		pastefield.value = value;
	}
	function paste_char(value) {
		if (document.selection) {
			// IE
			pastefield.focus();
			sel = document.selection.createRange();
			sel.text = value;
		} else if (pastefield.selectionStart || pastefield.selectionStart == 0) {
			// Mozilla/Chrome/Safari
			pastefield.value =
				pastefield.value.substring(0, pastefield.selectionStart) +
				value +
				pastefield.value.substring(pastefield.selectionEnd, pastefield.value.length);
			pastefield.selectionStart = pastefield.selectionEnd = pastefield.selectionStart + value.length;
		} else {
			// Fallback? - just append
			pastefield.value += value;
		}

		if (pastefield.id=="NPFX" || pastefield.id=="GIVN" || pastefield.id=="SPFX" || pastefield.id=="SURN" || pastefield.id=="NSFX") {
			updatewholename();
		}
	}
');

switch ($action) {
////////////////////////////////////////////////////////////////////////////////
case 'editraw':
	$xref    = safe_GET('xref', WT_REGEX_XREF);
	$fact_id = safe_GET('fact_id');

	$record = WT_GedcomRecord::getInstance($xref);
	check_record_access($record);

	// Find the fact to edit
	$edit_fact = null;
	foreach ($record->getFacts() as $fact) {
		if ($fact->getFactId() == $fact_id && $fact->canEdit()) {
			$edit_fact = $fact;
			break;
		}
	}
	if (!$edit_fact) {
		$controller
			->pageHeader()
			->addInlineJavascript('closePopupAndReloadParent();');
		exit;
	}

	$controller
		->setPageTitle($record->getFullName() . ' - ' . WT_I18N::translate('Edit raw GEDCOM record'))
		->pageHeader();

	?>
	<div id="edit_interface-page">
		<h4>
			<?php echo $controller->getPageTitle(); ?>
			<?php echo help_link('edit_edit_raw'); ?>
			<?php print_specialchar_link('gedcom'); ?>
		</h4>
		<form method="post" action="edit_interface.php">
			<input type="hidden" name="action" value="updateraw">
			<input type="hidden" name="xref" value="<?php echo $xref; ?>">
			<input type="hidden" name="fact_id" value="<?php echo $fact_id; ?>">
			<textarea name="gedcom" id="gedcom" dir="ltr"><?php echo htmlspecialchars($edit_fact->getGedcom()); ?></textarea>
			<table class="facts_table">
				<?php echo keep_chan($record); ?>
			</table>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'updateraw':
	$xref      = safe_POST('xref', WT_REGEX_XREF);
	$fact_id   = safe_POST('fact_id');
	$gedcom    = safe_POST('gedcom', WT_REGEX_UNSAFE);
	$keep_chan = safe_POST_bool('keep_chan');

	$record = WT_GedcomRecord::getInstance($xref);
	check_record_access($record);

	// Find the fact to edit
	$edit_fact = null;
	foreach ($record->getFacts() as $fact) {
		if ($fact->getFactId() == $fact_id && $fact->canEdit()) {
			$edit_fact = $fact;
			break;
		}
	}
	if (!$edit_fact) {
		$controller
			->pageHeader()
			->addInlineJavascript('closePopupAndReloadParent();');
		exit;
	}

	$controller
		->setPageTitle($record->getFullName() . ' - ' . WT_I18N::translate('Edit raw GEDCOM record'))
		->pageHeader();

	// Cleanup the client’s bad editing?
	$gedcom = preg_replace('/\n\n+/', "\n", $gedcom); // Empty lines
	$gedcom = trim($gedcom);                          // Leading/trailing spaces

	$record->updateFact($fact_id, $gedcom, !$keep_chan);

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'edit':
	$xref    = safe_GET('xref', WT_REGEX_XREF);
	$fact_id = safe_GET('fact_id');

	$record = WT_GedcomRecord::getInstance($xref);
	check_record_access($record);

	// Find the fact to edit
	$edit_fact = null;
	foreach ($record->getFacts() as $fact) {
		if ($fact->getFactId() == $fact_id  && $fact->canEdit()) {
			$edit_fact = $fact;
			break;
		}
	}
	if (!$edit_fact) {
		$controller
			->pageHeader()
			->addInlineJavascript('closePopupAndReloadParent();');
		exit;
	}

	$controller
		->setPageTitle($record->getFullName() . ' - ' . WT_I18N::translate('Edit'))
		->pageHeader();

	echo '<div id="edit_interface-page">';
	echo '<h4>', $controller->getPageTitle(), '</h4>';
	init_calendar_popup();
	echo '<form name="editform" method="post" action="edit_interface.php" enctype="multipart/form-data">';
	echo '<input type="hidden" name="action" value="update">';
	echo '<input type="hidden" name="fact_id" value="', $fact_id, '">';
	echo '<input type="hidden" name="xref" value="', $xref, '">';
	echo '<table class="facts_table">';
	create_edit_form($record, $edit_fact);
	echo keep_chan($record);
	echo '</table>';
	
	$level1type = $edit_fact->getTag();
	switch ($record::RECORD_TYPE) {
	case 'OBJE':
	case 'NOTE':
		// OBJE and NOTE "facts" are all special, and none can take lower-level links
		break;
	case 'SOUR':
	case 'REPO':
		// SOUR and REPO "facts" may only take a NOTE
		if ($level1type!='NOTE') {
			print_add_layer('NOTE');
		}
		break;
	case 'FAM':
	case 'INDI':
		// FAM and INDI records have "real facts".  They can take NOTE/SOUR/OBJE/etc.
		if ($level1type!='SEX') {
			if ($level1type!='SOUR' && $level1type!='REPO') {
				print_add_layer('SOUR');
			}
			if ($level1type!='OBJE' && $level1type!='REPO') {
				print_add_layer('OBJE');
			}
			if ($level1type!='NOTE') {
				print_add_layer('NOTE');
			}
			// Shared Note addition ------------
			if ($level1type!='SHARED_NOTE' && $level1type!='NOTE') {
				print_add_layer('SHARED_NOTE');
			}
			if ($level1type!='ASSO' && $level1type!='REPO' && $level1type!='NOTE') {
				print_add_layer('ASSO');
			}
			// allow to add godfather and godmother for CHR fact or best man and bridesmaid  for MARR fact in one window
			if ($level1type=='CHR' || $level1type=='MARR') {
				print_add_layer('ASSO2');
			}
			// RESN can be added to all level 1 tags
			print_add_layer('RESN');
		}
		break;
	}
	if (WT_USER_IS_ADMIN || $SHOW_GEDCOM_RECORD) {
		echo
			'<br><br><a href="edit_interface.php?action=editraw&amp;xref=', $xref, '&amp;fact_id=', $fact_id, '&amp;ged=', WT_GEDURL, '">',
			WT_I18N::translate('Edit raw GEDCOM record'),
			'</a>';
	}
	?>
		<p id="save-cancel">
			<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
			<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
		</p>
	</form>
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'add':
	$xref = safe_GET('xref', WT_REGEX_XREF);
	$fact = safe_GET('fact', WT_REGEX_TAG);
	
	$record = WT_GedcomRecord::getInstance($xref);
	check_record_access($record);

	$controller
		->setPageTitle($record->getFullName() . ' - ' . WT_Gedcom_Tag::getLabel($fact, $record))
		->pageHeader();

	$level0type = $record::RECORD_TYPE;

	echo '<div id="edit_interface-page">';
	echo '<h4>', $controller->getPageTitle(), '</h4>';

	init_calendar_popup();
	echo '<form name="addform" method="post" action="edit_interface.php" enctype="multipart/form-data">';
	echo '<input type="hidden" name="action" value="update">';
	echo '<input type="hidden" name="xref" value="', $xref, '">';
	echo '<table class="facts_table">';

	create_add_form($fact);

	echo keep_chan($record);
	echo '</table>';

	// Genealogical facts (e.g. for INDI and FAM records) can have 2 SOUR/NOTE/OBJE/ASSO/RESN ...
	if ($level0type=='INDI' || $level0type=='FAM') {
		// ... but not facts which are simply links to other records
		if ($fact!='OBJE' && $fact!='SHARED_NOTE' && $fact!='OBJE' && $fact!='REPO' && $fact!='SOUR' && $fact!='ASSO') {
			print_add_layer('SOUR');
			print_add_layer('OBJE');
			// Don't add notes to notes!
			if ($fact!='NOTE') {
				print_add_layer('NOTE');
				print_add_layer('SHARED_NOTE');
			}
			print_add_layer('ASSO');
			// allow to add godfather and godmother for CHR fact or best man and bridesmaid  for MARR fact in one window
			if ($fact=='CHR' || $fact=='MARR') {
				print_add_layer('ASSO2');
			}
			print_add_layer('RESN');
		}
	}
	?>
		<p id="save-cancel">
			<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
			<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
		</p>
	</form>
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'update':
	// Update a fact
	$xref      = safe_POST('xref',    WT_REGEX_XREF);
	$fact_id   = safe_POST('fact_id');
	$keep_chan = safe_POST_bool('keep_chan');
	
	$record = WT_GedcomRecord::getInstance($xref);
	check_record_access($record);

	// Arrays for each GEDCOM line
	$glevels = safe_POST('glevels');
	$tag     = safe_POST('tag',     WT_REGEX_TAG);
	$text    = safe_POST('text',    WT_REGEX_UNSAFE);
	$islink  = safe_POST('islink');

	$controller
		->setPageTitle(WT_I18N::translate('Edit'))
		->pageHeader();

	// If the fact has a DATE or PLAC, then delete any value of Y
	if ($text[0]=='Y') {
		for ($n=1; $n<count($tag); ++$n) {
			if ($glevels[$n]==2 && ($tag[$n]=='DATE' || $tag[$n]=='PLAC') && $text[$n]) {
				$text[0]='';
				break;
			}
		}
	}

	//-- check for photo update
	if (count($_FILES)>0) {
		if (isset($_REQUEST['folder'])) $folder = $_REQUEST['folder'];
		$uploaded_files = array();
		if (substr($folder, 0, 1) == "/") $folder = substr($folder, 1);
		if (substr($folder, -1, 1) != "/") $folder .= "/";
		foreach ($_FILES as $upload) {
			if (!empty($upload['tmp_name'])) {
				if (!move_uploaded_file($upload['tmp_name'], $MEDIA_DIRECTORY.$folder.basename($upload['name']))) {
					$error .= "<br>".WT_I18N::translate('There was an error uploading your file.')."<br>".file_upload_error_text($upload['error']);
					$uploaded_files[] = "";
				} else {
					$filename = $MEDIA_DIRECTORY.$folder.basename($upload['name']);
					$uploaded_files[] = $MEDIA_DIRECTORY.$folder.basename($upload['name']);
					if (!is_dir($MEDIA_DIRECTORY."thumbs/".$folder)) mkdir($MEDIA_DIRECTORY."thumbs/".$folder);
					$thumbnail = $MEDIA_DIRECTORY."thumbs/".$folder.basename($upload['name']);
					generate_thumbnail($filename, $thumbnail);
					if (!empty($error)) {
						echo "<span class=\"error\">", $error, "</span>";
					}
				}
			} else {
				$uploaded_files[] = "";
			}
		}
	}

	$newged = "";
	if (!empty($_POST['NAME']))   $newged .= "\n1 NAME "   . $_POST['NAME'];
	if (!empty($_POST['TYPE']))   $newged .= "\n2 TYPE "   . $_POST['TYPE'];
	if (!empty($_POST['NPFX']))   $newged .= "\n2 NPFX "   . $_POST['NPFX'];
	if (!empty($_POST['GIVN']))   $newged .= "\n2 GIVN "   . $_POST['GIVN'];
	if (!empty($_POST['NICK']))   $newged .= "\n2 NICK "   . $_POST['NICK'];
	if (!empty($_POST['SPFX']))   $newged .= "\n2 SPFX "   . $_POST['SPFX'];
	if (!empty($_POST['SURN']))   $newged .= "\n2 SURN "   . $_POST['SURN'];
	if (!empty($_POST['NSFX']))   $newged .= "\n2 NSFX "   . $_POST['NSFX'];
	if (!empty($_POST['ROMN']))   $newged .= "\n2 ROMN "   . $_POST['ROMN'];
	if (!empty($_POST['FONE']))   $newged .= "\n2 FONE "   . $_POST['FONE'];
	if (!empty($_POST['_HEB']))   $newged .= "\n2 _HEB "   . $_POST['_HEB'];
	if (!empty($_POST['_AKA']))   $newged .= "\n2 _AKA "   . $_POST['_AKA'];
	if (!empty($_POST['_MARNM'])) $newged .= "\n2 _MARNM " . $_POST['_MARNM'];
	
	if (isset($_POST['NOTE'])) $NOTE = $_POST['NOTE'];
	if (!empty($NOTE)) {			
		$tempnote = preg_split('/\r?\n/', trim($NOTE) . "\n"); // make sure only one line ending on the end
		$title[] = "0 @$xref@ NOTE " . array_shift($tempnote);
		foreach($tempnote as &$line) {
			$line = trim("1 CONT " . $line,' ');
		}
	}

	$newged = handle_updates($newged);
	$newged = substr($newged, 1); // Remove leading newline
	$record->updateFact($fact_id, $newged, !$keep_chan);

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addchild':
	$gender = safe_GET('gender', '[MF]', 'U');
	$famid  = safe_GET('famid',  WT_REGEX_XREF);

	$family = WT_Family::getInstance($famid);

	if ($family) {
		check_record_access($family);
		$controller->setPageTitle($family->getFullName() . ' - ' . WT_I18N::translate('Add a new child'));
	} else {
		$controller->setPageTitle(WT_I18N::translate('Add an unlinked person'));
	}
	$controller->pageHeader();

	print_indi_form('addchildaction', null, $family, null, 'CHIL', $gender);
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addspouse':
	$famtag = safe_GET('famtag', '(HUSB|WIFE)');
	$xref   = safe_GET('xref', WT_REGEX_XREF);

	$family = WT_Family::getInstance($xref);
	
	check_record_access($family);

	if ($famtag=='WIFE') {
		$controller->setPageTitle(WT_I18N::translate('Add a new wife'));
	} else {
		$controller->setPageTitle(WT_I18N::translate('Add a new husband'));
	}
	$controller->pageHeader();

	print_indi_form('addspouseaction', null, $family, null, $famtag);
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addnewparent':
	$xref   = safe_GET('xref',   WT_REGEX_XREF);
	$famtag = safe_GET('famtag', '(HUSB|WIFE)');
	$famid  = safe_GET('famid',  WT_REGEX_XREF);

	$person = WT_Individual::getInstance($xref);
	$family = WT_Family::getInstance($famid);
	check_record_access($person);
	if ($family) {
		check_record_access($family);
	}

	if ($person) {
		// Adding a parent to an individual
		$name=$person->getFullName() . ' - ';
	} else {
		// Adding a spouse to a family
		$name='';
	}

	if ($famtag=='WIFE') {
		$controller->setPageTitle($name . WT_I18N::translate('Add a new mother'));
	} else {
		$controller->setPageTitle($name . WT_I18N::translate('Add a new father'));
	}
	$controller->pageHeader();

	print_indi_form('addnewparentaction', $person, $family, null, $famtag, $person->getSex());
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addopfchild':
	$xref = safe_GET('xref', WT_REGEX_XREF);

	$person = WT_Individual::getInstance($xref);
	check_record_access($person);

	$controller
		->setPageTitle($person->getFullName() . ' - ' . WT_I18N::translate('Add a child to create a one-parent family'))
		->pageHeader();

	print_indi_form('addopfchildaction', $person, null, null, 'CHIL', $person->getSex());
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addfamlink':
	$xref = safe_GET('xref', WT_REGEX_XREF);

	$person = WT_Individual::getInstance($xref);
	check_record_access($person);
	
	$controller
		->setPageTitle($person->getFullName() . ' - ' . WT_I18N::translate('Link this person to an existing family as a child'))
		->pageHeader();

	echo '<div id="edit_interface-page">';
	echo '<h4>', $controller->getPageTitle(), '</h4>';

	echo '<form method="post" name="addchildform" action="edit_interface.php">';
	echo '<input type="hidden" name="action" value="linkfamaction">';
	echo '<input type="hidden" name="xref" value="', $person->getXref(), '">';
	echo '<table class="facts_table">';
	echo '<tr><td class="facts_label">', WT_I18N::translate('Family'), '</td>';
	echo '<td class="facts_value"><input type="text" id="famid" name="famid" size="8"> ';
	echo print_findfamily_link('famid');
	echo '</td></tr>';
	echo '<tr><td class="facts_label">', WT_Gedcom_Tag::getLabel('PEDI'), '</td><td class="facts_value">';
	echo edit_field_pedi('PEDI', '', '', $person);
	echo help_link('PEDI');
	echo '</td></tr>';
	echo keep_chan($person);
	echo '</table>';
	?>
		<p id="save-cancel">
			<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
			<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
		</p>
	</form>
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'linkspouse':
	$famtag = safe_GET('famtag', '(HUSB|WIFE)');
	$xref    = safe_GET('xref',   WT_REGEX_XREF);

	$person = WT_Individual::getInstance($xref);
	check_record_access($person);
	
	if ($person->getSex()=='F') {
		$controller->setPageTitle($person->getFullName() . ' - ' . WT_I18N::translate('Add a husband using an existing person'));
	} else {
		$controller->setPageTitle($person->getFullName() . ' - ' . WT_I18N::translate('Add a wife using an existing person'));
	}

	$controller->pageHeader();

	echo '<div id="edit_interface-page">';
	echo '<h4>', $controller->getPageTitle(), '</h4>';
	init_calendar_popup();
	echo '<form method="post" name="addchildform" action="edit_interface.php">';
	echo '<input type="hidden" name="action" value="linkspouseaction">';
	echo '<input type="hidden" name="xref" value="', $person->getXref(), '">';
	echo '<input type="hidden" name="famtag" value="', $famtag, '">';
	echo '<table class="facts_table">';
	echo '<tr><td class="facts_label">';
	if ($famtag=="WIFE") {
		echo WT_I18N::translate('Wife');
	} else {
		echo WT_I18N::translate('Husband');
	}
	echo '</td>';
	echo '<td class="facts_value"><input id="spouseid" type="text" name="spid" size="8"> ';
	echo print_findindi_link('spouseid');
	echo '</td></tr>';
	add_simple_tag("0 MARR Y");
	add_simple_tag("0 DATE", "MARR");
	add_simple_tag("0 PLAC", "MARR");
	echo keep_chan($person);
	echo '</table>';
	print_add_layer("SOUR");
	print_add_layer("OBJE");
	print_add_layer("NOTE");
	print_add_layer("SHARED_NOTE");
	print_add_layer("ASSO");
	// allow to add godfather and godmother for CHR fact or best man and bridesmaid  for MARR fact in one window
	print_add_layer("ASSO2");
	print_add_layer("RESN");
	?>
		<p id="save-cancel">
			<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
			<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
		</p>
	</form>
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'linkfamaction':
	$xref   = safe_POST('xref',  WT_REGEX_XREF);
	$famid  = safe_POST('famid', WT_REGEX_XREF);
	$PEDI   = safe_POST('PEDI');

	$person = WT_Individual::getInstance($xref);
	$family = WT_Family::getInstance($famid);
	check_record_access($person);
	check_record_access($family);
	
	$controller
		->setPageTitle($person->getFullName() . ' - ' . WT_I18N::translate('Link this person to an existing family as a child'))
		->pageHeader();

	// Replace any existing child->family link (we may be changing the PEDI);
	$fact_id = null;
	foreach ($person->getFacts('FAMC') as $fact) {
		if ($family->equals($fact->getTarget())) {
			$fact_id = $fact->getFactId();
			break;
		}
	}

	$gedcom = WT_Gedcom_Code_Pedi::createNewFamcPedi($PEDI, $famid);
	$person->updateFact($fact_id, $gedcom, true);

	// Only set the family->child link if it does not already exist
	$edit_fact = null;
	foreach ($family->getFacts('CHIL') as $fact) {
		if ($person->equals($fact->getTarget())) {
			$edit_fact = $fact;
			break;
		}
	}
	if (!$edit_fact) {
		$family->updateFact(null, '1 CHIL @' . $person->getXref() . '@', true);
	}

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addnewsource':
	$controller
		->setPageTitle(WT_I18N::translate('Create a new source'))
		->pageHeader();

	?>
	<script>
		function check_form(frm) {
			if (frm.TITL.value=="") {
				alert('<?php echo WT_I18N::translate('You must provide a source title'); ?>');
				frm.TITL.focus();
				return false;
			}
			return true;
		}
	</script>
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>
		<form method="post" action="edit_interface.php" onsubmit="return check_form(this);">
			<input type="hidden" name="action" value="addsourceaction">
			<input type="hidden" name="xref" value="newsour">
			<table class="facts_table">
				<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('TITL'); ?></td>
				<td class="optionbox wrap"><input type="text" name="TITL" id="TITL" value="" size="60"> <?php echo print_specialchar_link('TITL'); ?></td></tr>
				<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('ABBR'); ?></td>
				<td class="optionbox wrap"><input type="text" name="ABBR" id="ABBR" value="" size="40" maxlength="255"> <?php echo print_specialchar_link('ABBR'); ?></td></tr>
				<?php if (strstr($ADVANCED_NAME_FACTS, "_HEB")!==false) { ?>
				<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('_HEB'), help_link('_HEB'); ?></td>
				<td class="optionbox wrap"><input type="text" name="_HEB" id="_HEB" value="" size="60"> <?php echo print_specialchar_link('_HEB'); ?></td></tr>
				<?php } ?>
				<?php if (strstr($ADVANCED_NAME_FACTS, "ROMN")!==false) { ?>
				<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('ROMN'), help_link('ROMN'); ?></td>
				<td class="optionbox wrap"><input  type="text" name="ROMN" id="ROMN" value="" size="60"> <?php echo print_specialchar_link('ROMN'); ?></td></tr>
				<?php } ?>
				<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('AUTH'); ?></td>
				<td class="optionbox wrap"><input type="text" name="AUTH" id="AUTH" value="" size="40" maxlength="255"> <?php echo print_specialchar_link('AUTH'); ?></td></tr>
				<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('PUBL'); ?></td>
				<td class="optionbox wrap"><textarea name="PUBL" id="PUBL" rows="5" cols="60"></textarea><br><?php echo print_specialchar_link('PUBL'); ?></td></tr>
				<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('REPO'); ?></td>
				<td class="optionbox wrap"><input type="text" name="REPO" id="REPO" value="" size="10"> <?php echo print_findrepository_link('REPO'), ' ', print_addnewrepository_link('REPO'); ?></td></tr>
				<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('CALN'); ?></td>
				<td class="optionbox wrap"><input type="text" name="CALN" id="CALN" value=""></td></tr>
				<?php echo keep_chan(); ?>
			</table>
				<a href="#"  onclick="return expand_layer('events');"><i id="events_img" class="icon-plus"></i>
				<?php echo WT_I18N::translate('Associate events with this source'); ?></a><?php echo help_link('edit_SOUR_EVEN'); ?>
				<div id="events" style="display: none;">
				<table class="facts_table">
				<tr>
					<td class="descriptionbox wrap width25"><?php echo WT_I18N::translate('Select Events'), help_link('edit_SOUR_EVEN'); ?></td>
					<td class="optionbox wrap"><select name="EVEN[]" multiple="multiple" size="5">
						<?php
						$parts = explode(',', get_gedcom_setting(WT_GED_ID, 'INDI_FACTS_ADD'));
						foreach ($parts as $key) {
							?><option value="<?php echo $key; ?>"><?php echo WT_Gedcom_Tag::getLabel($key); ?></option>
						<?php
						}
						$parts = explode(',', get_gedcom_setting(WT_GED_ID, 'FAM_FACTS_ADD'));
						foreach ($parts as $key) {
							?><option value="<?php echo $key; ?>"><?php echo WT_Gedcom_Tag::getLabel($key); ?></option>
						<?php
						}
						?>
					</select></td>
				</tr>
				<?php
				add_simple_tag('0 DATE', 'EVEN');
				add_simple_tag('0 PLAC', 'EVEN');
				add_simple_tag('0 AGNC');
				?>
				</table>
			</div>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addsourceaction':
	$controller
		->setPageTitle(WT_I18N::translate('Create a new source'))
		->pageHeader();

	$newgedrec = "0 @XREF@ SOUR";
	if (isset($_REQUEST['EVEN'])) $EVEN = $_REQUEST['EVEN'];
	if (!empty($EVEN) && count($EVEN)>0) {
		$newgedrec .= "\n1 DATA";
		$newgedrec .= "\n2 EVEN ".implode(",", $EVEN);
		if (!empty($EVEN_DATE)) $newgedrec .= "\n3 DATE ".$EVEN_DATE;
		if (!empty($EVEN_PLAC)) $newgedrec .= "\n3 PLAC ".$EVEN_PLAC;
		if (!empty($AGNC))      $newgedrec .= "\n2 AGNC ".$AGNC;
	}
	if (isset($_REQUEST['ABBR'])) $ABBR = $_REQUEST['ABBR'];
	if (isset($_REQUEST['TITL'])) $TITL = $_REQUEST['TITL'];
	if (isset($_REQUEST['_HEB'])) $_HEB = $_REQUEST['_HEB'];
	if (isset($_REQUEST['ROMN'])) $ROMN = $_REQUEST['ROMN'];
	if (isset($_REQUEST['AUTH'])) $AUTH = $_REQUEST['AUTH'];
	if (isset($_REQUEST['PUBL'])) $PUBL = $_REQUEST['PUBL'];
	if (isset($_REQUEST['REPO'])) $REPO = $_REQUEST['REPO'];
	if (isset($_REQUEST['CALN'])) $CALN = $_REQUEST['CALN'];
	if (!empty($ABBR)) $newgedrec .= "\n1 ABBR $ABBR";
	if (!empty($TITL)) {
		$newgedrec .= "\n1 TITL $TITL";
		if (!empty($_HEB)) $newgedrec .= "\n2 _HEB $_HEB";
		if (!empty($ROMN)) $newgedrec .= "\n2 ROMN $ROMN";
	}
	if (!empty($AUTH)) $newgedrec .= "\n1 AUTH $AUTH";
	if (!empty($PUBL)) {
		foreach (preg_split("/\r?\n/", $PUBL) as $k=>$line) {
			if ($k==0) {
				$newgedrec .= "\n1 PUBL $line";
			} else {
				$newgedrec .= "\n2 CONT $line";
			}
		}
	}
	if (!empty($REPO)) {
		$newgedrec .= "\n1 REPO @$REPO@";
		if (!empty($CALN)) $newgedrec .= "\n2 CALN $CALN";
	}
	$record = WT_GedcomRecord::createRecord($newgedrec, WT_GED_ID);
	$controller->addInlineJavascript('openerpasteid("' . $record->getXref() . '");');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addnewnote':
	$controller
		->setPageTitle(WT_I18N::translate('Create a new Shared Note'))
		->pageHeader();

	?>
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>

		<form method="post" action="edit_interface.php" onsubmit="return check_form(this);">
			<input type="hidden" name="action" value="addnoteaction">
			<input type="hidden" name="noteid" value="newnote">
			<?php
			echo '<table class="facts_table">';
			echo '<tr>';
			echo '<td class="descriptionbox nowrap">';
			echo WT_I18N::translate('Shared note'), help_link('SHARED_NOTE');
			echo '</td>';
			echo '<td class="optionbox wrap" ><textarea name="NOTE" id="NOTE" rows="15" cols="87"></textarea>';
			echo print_specialchar_link('NOTE');
			echo '</td>';
			echo '</tr>';
			echo keep_chan();
			echo '</table>';
			?>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addnoteaction':
	$controller
		->setPageTitle(WT_I18N::translate('Create a new Shared Note'))
		->pageHeader();

	$newgedrec  = "0 @XREF@ NOTE";

	if (isset($_REQUEST['NOTE'])) $NOTE = $_REQUEST['NOTE'];

	if (!empty($NOTE)) {
		foreach (preg_split("/\r?\n/", $NOTE) as $k=>$line) {
			if ($k==0) {
				$newgedrec .= " {$line}";
			} else {
				$newgedrec .= "\n1 CONT {$line}";
			}
		}
	}

	$record = WT_GedcomRecord::createRecord($newgedrec, WT_GED_ID);
	$controller->addInlineJavascript('openerpasteid("' . $record->getXref() . '");');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addnewnote_assisted':
	require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/CENS_ctrl.php';
break;

////////////////////////////////////////////////////////////////////////////////
case 'addnoteaction_assisted':
	$controller
		->setPageTitle(WT_I18N::translate('Create a new Shared Note using Assistant'))
		->pageHeader();

	echo '<div id="edit_interface-page">';
	echo '<h4>', $controller->getPageTitle(), '</h4>';

	require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/_CENS/addnoteaction_assisted.php';

	echo 	'</div><!-- id="edit_interface-page" -->';
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addmedia_links':
	$xref = safe_GET('xref', WT_REGEX_XREF);

	$person = WT_Source::getInstance($xref);
	check_record_access($person);

	$controller
		->setPageTitle(WT_I18N::translate('Family navigator'))
		->pageHeader();

	?>
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>
		<form method="post" action="edit_interface.php?xref=<?php echo $person->getXref(); ?>" onsubmit="findindi()">
			<input type="hidden" name="action" value="addmedia_links">
			<input type="hidden" name="noteid" value="newnote">
			<?php require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/MEDIA_ctrl.php'; ?>
		</form>
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'editsource':
	$xref = safe_GET('xref', WT_REGEX_XREF);

	$source = WT_Source::getInstance($xref);
	check_record_access($source);

	$controller
		->setPageTitle($source->getFullName())
		->pageHeader();

	echo '<div id="edit_interface-page">';
	echo '<h4>', $controller->getPageTitle(), '</h4>';
	init_calendar_popup();
	echo '<form method="post" action="edit_interface.php" enctype="multipart/form-data">';
	echo '<input type="hidden" name="action" value="update">';
	echo '<input type="hidden" name="xref" value="', $xref, '">';
	echo '<table class="facts_table">';
	$gedlines = explode("\n", $gedrec); // -- find the number of lines in the record
	$uniquefacts = preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'SOUR_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
	$usedfacts = array();
	$lines = count($gedlines);
	if ($lines==1) {
		foreach ($uniquefacts as $fact) {
			$gedrec.="\n1 ".$fact;
		}
		$gedlines = explode("\n", $gedrec);
	}
	for ($i=$linenum; $i<$lines; $i++) {
		$fields = explode(' ', $gedlines[$i]);
		if ((substr($gedlines[$i], 0, 1)<2) && $fields[1]!="CHAN") {
			$level1type = create_edit_form($gedrec, $i, 'SOUR');
			echo '<input type="hidden" name="linenum[]" value="', $i, '">';
			$usedfacts[]=$fields[1];
			foreach ($uniquefacts as $key=>$fact) {
				if ($fact==$fields[1]) unset($uniquefacts[$key]);
			}
		}
	}
	foreach ($uniquefacts as $key=>$fact) {
		$gedrec.="\n1 ".$fact;
		$level1type = create_edit_form($gedrec, $lines++, 'SOUR');
		echo '<input type="hidden" name="linenum[]" value="', $i, '">';
	}

	echo keep_chan($source);
	echo '</table>';
	print_add_layer("OBJE");
	print_add_layer("NOTE");
	print_add_layer("SHARED_NOTE");
	print_add_layer("RESN");
	?>
		<p id="save-cancel">
			<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
			<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
		</p>
	</form>
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'editnote':
	$xref = safe_GET('xref', WT_REGEX_XREF);

	$note = WT_Note::getInstance($xref);
	check_record_access($note);

	$controller
		->setPageTitle(WT_I18N::translate('Edit Shared Note'))
		->pageHeader();

	?>
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>
		<form method="post" action="edit_interface.php" >
			<input type="hidden" name="action" value="update">
			<input type="hidden" name="xref" value="<?php echo $xref; ?>">
			<table class="facts_table">
				<tr>
					<td class="descriptionbox wrap width25"><?php echo WT_I18N::translate('Shared note'), help_link('SHARED_NOTE'); ?></td>
					<td class="optionbox wrap">
						<textarea name="NOTE" id="NOTE" rows="15" cols="90"><?php echo htmlspecialchars($note->getNote()); ?></textarea>
						<br>
						<?php echo print_specialchar_link('NOTE'); ?>
					</td>
				</tr>
				<?php echo keep_chan($note); ?>
			</table>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addnewrepository':
	$controller
		->setPageTitle(WT_I18N::translate('Create Repository'))
		->pageHeader();

	echo '<div id="edit_interface-page">';
	echo '<h4>', $controller->getPageTitle(), '</h4>';

	echo '<script>';
	?>
		function check_form(frm) {
			if (frm.NAME.value=="") {
				alert('<?php echo WT_I18N::translate('You must provide a repository name'); ?>');
				frm.NAME.focus();
				return false;
			}
			return true;
		}
	<?php
	echo '</script>';
	?>
	<form method="post" action="edit_interface.php" onsubmit="return check_form(this);">
		<input type="hidden" name="action" value="addrepoaction">
		<input type="hidden" name="xref" value="newrepo">
		<table class="facts_table">
			<tr><td class="descriptionbox wrap width25"><?php echo WT_I18N::translate('Repository name'); ?></td>
			<td class="optionbox wrap"><input type="text" name="REPO_NAME" id="REPO_NAME" value="" size="40" maxlength="255"> <?php echo print_specialchar_link('REPO_NAME'); ?></td></tr>
			<?php if (strstr($ADVANCED_NAME_FACTS, "_HEB")!==false) { ?>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('_HEB'), help_link('_HEB'); ?></td>
			<td class="optionbox wrap"><input type="text" name="_HEB" id="_HEB" value="" size="40" maxlength="255"> <?php echo print_specialchar_link('_HEB'); ?></td></tr>
			<?php } ?>
			<?php if (strstr($ADVANCED_NAME_FACTS, "ROMN")!==false) { ?>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('ROMN'), help_link('ROMN'); ?></td>
			<td class="optionbox wrap"><input type="text" name="ROMN" id="ROMN" value="" size="40" maxlength="255"> <?php echo print_specialchar_link('ROMN'); ?></td></tr>
			<?php } ?>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('ADDR'), help_link('ADDR'); ?></td>
			<td class="optionbox wrap"><textarea name="ADDR" id="ADDR" rows="5" cols="60"></textarea><?php echo print_specialchar_link('ADDR'); ?> </td></tr>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('PHON'), help_link('PHON'); ?></td>
			<td class="optionbox wrap"><input type="text" name="PHON" id="PHON" value="" size="40" maxlength="255"> </td></tr>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('FAX'), help_link('FAX'); ?></td>
			<td class="optionbox wrap"><input type="text" name="FAX" id="FAX" value="" size="40"></td></tr>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('EMAIL'), help_link('EMAIL'); ?></td>
			<td class="optionbox wrap"><input type="text" name="EMAIL" id="EMAIL" value="" size="40" maxlength="255"></td></tr>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('WWW'), help_link('URL'); ?></td>
			<td class="optionbox wrap"><input type="text" name="WWW" id="WWW" value="" size="40" maxlength="255"> </td></tr>
			<?php echo keep_chan(); ?>
		</table>
		<p id="save-cancel">
			<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
			<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
		</p>
	</form>
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addrepoaction':
	$controller
		->setPageTitle(WT_I18N::translate('Create Repository'))
		->pageHeader();

	$newgedrec = "0 @XREF@ REPO";
	if (isset($_REQUEST['REPO_NAME'])) $NAME = $_REQUEST['REPO_NAME'];
	if (isset($_REQUEST['_HEB'])) $_HEB = $_REQUEST['_HEB'];
	if (isset($_REQUEST['ROMN'])) $ROMN = $_REQUEST['ROMN'];
	if (isset($_REQUEST['ADDR'])) $ADDR = $_REQUEST['ADDR'];
	if (isset($_REQUEST['PHON'])) $PHON = $_REQUEST['PHON'];
	if (isset($_REQUEST['FAX'])) $FAX = $_REQUEST['FAX'];
	if (isset($_REQUEST['EMAIL'])) $EMAIL = $_REQUEST['EMAIL'];
	if (isset($_REQUEST['WWW'])) $WWW = $_REQUEST['WWW'];

	if (!empty($NAME)) {
		$newgedrec .= "\n1 NAME $NAME";
		if (!empty($_HEB)) $newgedrec .= "\n2 _HEB $_HEB";
		if (!empty($ROMN)) $newgedrec .= "\n2 ROMN $ROMN";
	}
	if (!empty($ADDR)) {
		foreach (preg_split("/\r?\n/", $ADDR) as $k=>$line) {
			if ($k==0) {
				$newgedrec .= "\n1 ADDR {$line}";
			} else {
				$newgedrec .= "\n2 CONT {$line}";
			}
		}
	}
	if (!empty($PHON)) $newgedrec .= "\n1 PHON $PHON";
	if (!empty($FAX)) $newgedrec .= "\n1 FAX $FAX";
	if (!empty($EMAIL)) $newgedrec .= "\n1 EMAIL $EMAIL";
	if (!empty($WWW)) $newgedrec .= "\n1 WWW $WWW";

	$record = WT_GedcomRecord::createRecord($newgedrec, WT_GED_ID);
	$controller->addInlineJavascript('openerpasteid("' . $record->getXref() . '");');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addchildaction':
	$famid     = safe_POST('famid', WT_REGEX_XREF); // Add a child to this family
	$keep_chan = safe_POST_bool('keep_chan');

	$family = WT_Family::getInstance($famid);
	check_record_access($family);

	$controller
		->setPageTitle(WT_I18N::translate('Add child'))
		->pageHeader();

	splitSOUR();
	$gedrec ="0 @REF@ INDI";
	$gedrec.=addNewName();
	$gedrec.=addNewSex ();
	if (preg_match_all('/([A-Z0-9_]+)/', $QUICK_REQUIRED_FACTS, $matches)) {
		foreach ($matches[1] as $match) {
			$gedrec.=addNewFact($match);
		}
	}
	if ($family) {
		// $family is not set when creating unlinked individuals
		if (isset($_REQUEST['PEDI'])) {
			$PEDI = $_REQUEST['PEDI'];
		} else {
			$PEDI = '';
		}
		$gedrec .= "\n".WT_Gedcom_Code_Pedi::createNewFamcPedi($PEDI, $famid);
	}
	if (safe_POST_bool('SOUR_INDI')) {
		$gedrec = handle_updates($gedrec);
	} else {
		$gedrec = updateRest($gedrec);
	}

	// Create the new child
	$new_child = WT_GedcomRecord::createRecord($gedrec, WT_GED_ID);

	if ($family) {
		// Insert new child at the right place
		foreach ($family->getFacts('CHIL') as $fact) {
			$old_child = $fact->getTarget();
			if ($old_child && WT_Date::Compare($new_child->getEstimatedBirthDate(), $old_child->getEstimatedBirthDate())<0) {
				// Insert before this child
				$family->updateFact($fact->getFactId(), "1 CHIL @" . $new_child->getXref()."@\n" . $fact->getGedcom(), !$keep_chan);
				$done = true;
				break;
			}
		}
		if (!$done) {
			// Append to end
			$family->updateFact(null, "1 CHIL @" . $new_child->getXref()."@\n", !$keep_chan);
		}
	}

	if (safe_POST('goto')=='new') {
		$controller->addInlineJavascript('closePopupAndReloadParent("' . $new_child->getRawUrl() . '");');
	} else {
		$controller->addInlineJavascript('closePopupAndReloadParent();');
	}
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addspouseaction':
	$famid = safe_POST('famid', WT_REGEX_XREF); // Add a spouse to this family
	$sex   = safe_POST('SEX', '[MFU]');

	$family = WT_Family::getInstance($famid);
	check_record_access($family);
	
	$controller
		->setPageTitle(WT_I18N::translate('Add a new spouse'))
		->pageHeader();

	splitSOUR();
	$indi_gedcom = '0 @REF@ INDI';
	$indi_gedcom.= addNewName();
	$indi_gedcom.= addNewSex ();
	if (preg_match_all('/([A-Z0-9_]+)/', $QUICK_REQUIRED_FACTS, $matches)) {
		foreach ($matches[1] as $match) {
			$indi_gedcom.=addNewFact($match);
		}
	}
	if (safe_POST_bool('SOUR_INDI')) {
		$indi_gedcom = handle_updates($indi_gedcom);
	} else {
		$indi_gedcom = updateRest($indi_gedcom);
	}

	$fam_gedcom = '';
	if (preg_match_all('/([A-Z0-9_]+)/', $QUICK_REQUIRED_FAMFACTS, $matches)) {
		foreach ($matches[1] as $match) {
			$fam_gedcom.=addNewFact($match);
		}
	}
	if (safe_POST_bool('SOUR_FAM')) {
		$fam_gedcom = handle_updates($fam_gedcom);
	} else {
		$fam_gedcom = updateRest($fam_gedcom);
	}

	if ($family) {
		// Append to an existing family
		$indi_gedcom .= "\n1 FAMS @" . $family->getXref() . '@';
		// Create the new spouse
		$spouse = WT_GedcomRecord::createRecord($indi_gedcom, WT_GED_ID);
		// Link the family
		if ($sex=='F') {
			$family->updateFact(null, '1 WIFE @' . $spouse->getXref() . '@' . $fam_gedcom, true);
		} else {
			$family->updateFact(null, '1 HUSB @' . $spouse->getXref() . '@' . $fam_gedcom, true);
		}
	} else {
		// Create the new spouse
		$spouse = WT_GedcomRecord::createRecord($indi_gedcom, WT_GED_ID);
		// Create a new family
		if ($sex == 'F') {
			$family = WT_GedcomRecord::createRecord("0 @NEW@ FAM\n1 WIFE @" . $spouse->getXref() . "@\n1 HUSB @" . $person->getXref() . "@" . $fam_gedcom, WT_GED_ID);
		} else {
			$family = WT_GedcomRecord::createRecord("0 @NEW@ FAM\n1 HUSB @" . $spouse->getXref() . "@\n1 WIFE @" . $person->getXref() . "@" . $fam_gedcom, WT_GED_ID);
		}
		// Link the spouses to the family
		$spouse->updateFact(null, '1 FAMS @' . $family->getXref() . '@', true);
		$person->updateFact(null, '1 FAMS @' . $family->getXref() . '@', true);
	}

	if (safe_POST('goto')=='new') {
		$controller->addInlineJavascript('closePopupAndReloadParent("' . $new_spouse->getRawUrl() . '");');
	} else {
		$controller->addInlineJavascript('closePopupAndReloadParent();');
	}
	break;

////////////////////////////////////////////////////////////////////////////////
case 'linkspouseaction':
	$xref   = safe_POST('xref',   WT_REGEX_XREF);
	$spid   = safe_POST('spid',   WT_REGEX_XREF);
	$famtag = safe_POST('famtag', '(HUSB|WIFE)');

	$person = WT_Individual::getInstance($xref);
	$spouse = WT_Individual::getInstance($spid);
	check_record_access($person);
	check_record_access($spouse);

	if ($person->getSex()=='F') {
		$controller->setPageTitle($person->getFullName() . ' - ' . WT_I18N::translate('Add a husband using an existing person'));
	} else {
		$controller->setPageTitle($person->getFullName() . ' - ' . WT_I18N::translate('Add a wife using an existing person'));
	}
	$controller->pageHeader();

	if ($person->getSex()=='M') {
		$gedcom = "0 @new@ FAM\n1 HUSB @" . $person->getXref() . "@\n1 WIFE @" . $spouse->getXref() . "@";
	} else {
		$gedcom = "0 @new@ FAM\n1 HUSB @" . $spouse->getXref() . "@\n1 WIFE @" . $person->getXref() . "@";
	}
	splitSOUR();
	$gedcom .= addNewFact('MARR');

	if (safe_POST_bool('SOUR_FAM') || count($tagSOUR)>0) {
		// before adding 2 SOUR it needs to add 1 MARR Y first
		if (addNewFact('MARR') == '') {
			$gedcom .= "\n1 MARR Y";
		}
		$gedcom = handle_updates($gedcom);
	} else {
		// before adding level 2 facts it needs to add 1 MARR Y first
		if (addNewFact('MARR')=='') {
			$gedcom .= "\n1 MARR Y";
		}
		$gedcom = updateRest($gedcom);
	}

	$family = WT_GedcomRecord::createRecord($gedcom, WT_GED_ID);
	$person->updateFact(null, '1 FAMS @' . $family->getXref() .'@', true);	
	$spouse->updateFact(null, '1 FAMS @' . $family->getXref() .'@', true);	

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addnewparentaction':
	$xref   = safe_POST('xref',   WT_REGEX_XREF);
	$famid  = safe_POST('famid',  WT_REGEX_XREF);
	$famtag = safe_POST('famtag', '(HUSB|WIFE)');

	$person = WT_Individual::getInstance($xref);
	$family = WT_Family::getInstance($famid);
	check_record_access($person);
	if ($family) {
		check_record_access($family);
	}

	$controller
		->setPageTitle(WT_I18N::translate('Add a new father'))
		->pageHeader();

	splitSOUR(); // separate SOUR record from the rest

	$gedrec ="0 @REF@ INDI";
	$gedrec.=addNewName();
	$gedrec.=addNewSex ();
	if (preg_match_all('/([A-Z0-9_]+)/', $QUICK_REQUIRED_FACTS, $matches)) {
		foreach ($matches[1] as $match) {
			$gedrec.=addNewFact($match);
		}
	}

	if (safe_POST_bool('SOUR_INDI')) {
		$gedrec = handle_updates($gedrec);
	} else {
		$gedrec = updateRest($gedrec);
	}

	$parent = WT_GedcomRecord::createRecord($gedrec, WT_GED_ID);
	if ($family) {
		// Link to an existing family
		$famrec = '1 ' . $famtag .' @' . $parent . '@';
		if (preg_match_all('/([A-Z0-9_]+)/', $QUICK_REQUIRED_FAMFACTS, $matches)) {
			foreach ($matches[1] as $match) {
				$famrec.=addNewFact($match);
			}
		}
		if (safe_POST_bool('SOUR_FAM')) {
			$famrec = handle_updates($famrec);
		} else {
			$famrec = updateRest($famrec);
		}
		$family->updateFact(null, $famrec, true);
		$parent->updateFact(null, '1 FAMS @' . $family->getXref() . '@', true);
	} else {
		// Create a new family
		$famrec = '0 @new@ FAM';
		if ($famtag == 'HUSB') {
			$famrec .= "\n1 HUSB @" . $parent->getXref() . '@';
			$famrec .= "\n1 CHIL @" . $person->getXref() . '@';
		} else {
			$famrec .= "\n1 WIFE @" . $parent->getXref() . '@';
			$famrec .= "\n1 CHIL @" . $person->getXref() . '@';
		}

		if (preg_match_all('/([A-Z0-9_]+)/', $QUICK_REQUIRED_FAMFACTS, $matches)) {
			foreach ($matches[1] as $match) {
				$famrec.=addNewFact($match);
			}
		}
		if (safe_POST_bool('SOUR_FAM')) {
			$famrec = handle_updates($famrec);
		} else {
			$famrec = updateRest($famrec);
		}

		$family = WT_GedcomRecord::createRecord($famrec, WT_GED_ID);
		$person->updateFact(null, '1 FAMC @' . $family->getXref() . '@', true);
		$parent->updateFact(null, '1 FAMS @' . $family->getXref() . '@', true);
	}

	if (safe_POST('goto')=='new') {
		$controller->addInlineJavascript('closePopupAndReloadParent("' . $parent->getRawUrl() . '");');
	} else {
		$controller->addInlineJavascript('closePopupAndReloadParent();');
	}
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addopfchildaction':
	$xref = safe_POST('xref',  WT_REGEX_XREF);
	$PEDI = safe_POST('PEDI');

	$person = WT_Individual::getInstance($xref);
	check_record_access($person);

	$controller
		->setPageTitle(WT_I18N::translate('Add child'))
		->pageHeader();

	// Create a family
	$gedcom='0 @NEW@ FAM';
	if ($person->getSex()=='F') {
		$gedcom.="\n1 WIFE @" . $person->getXref() . "@";
	} else {
		$gedcom.="\n1 HUSB @" . $person->getXref() . "@";
	}
	$family = WT_GedcomRecord::createRecord($gedcom, WT_GED_ID);

	// Link the parent to the family
	$person->updateFact(null, "1 FAMS @" . $family->getXref() . "@", true);

	// Create a child
	splitSOUR(); // separate SOUR record from the rest

	$gedcom = "0 @NEW@ INDI";
	$gedcom .= addNewName();
	$gedcom .= addNewSex ();
	$gedcom .= "\n".WT_Gedcom_Code_Pedi::createNewFamcPedi($PEDI, $newfamxref);
	if (preg_match_all('/([A-Z0-9_]+)/', $QUICK_REQUIRED_FACTS, $matches)) {
		foreach ($matches[1] as $match) {
			$gedcom.=addNewFact($match);
		}
	}
	if (safe_POST_bool('SOUR_INDI')) {
		$gedcom=handle_updates($gedcom);
	} else {
		$gedcom=updateRest($gedcom);
	}
	$gedcom .= "\n1 FAMC @" . $family->getXref() . "@";

	$child = WT_GedcomRecord::createRecord($gedcom, WT_GED_ID);

	// Link the family to the child
	$family->updateFact(null, '1 CHIL @' . $child->getXref() . '@', true);

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'editname':
	$xref    = safe_GET('xref', WT_REGEX_XREF);
	$fact_id = safe_GET('fact_id');

	$person = WT_Individual::getInstance($xref);
	check_record_access($person);

	// Find the fact to edit
	$name_fact = null;
	foreach ($person->getFacts() as $fact) {
		if ($fact->getFactId() == $fact_id && $fact->canEdit()) {
			$name_fact = $fact;
		}
	}
	if (!$name_fact) {
		$controller
			->pageHeader()
			->addInlineJavascript('closePopupAndReloadParent();');
		exit;
	}

	$controller
		->setPageTitle(WT_I18N::translate('Edit name'))
		->pageHeader();

	print_indi_form('update', $person, null, $name_fact, '', $person->getSex());

	if (WT_USER_IS_ADMIN || $SHOW_GEDCOM_RECORD) {
		echo
			'<br><br><a href="edit_interface.php?action=editraw&amp;xref=', $xref, '&amp;fact_id=', $fact_id, '&amp;ged=', WT_GEDURL, '">',
			WT_I18N::translate('Edit raw GEDCOM record'),
			'</a>';
	}
	
	break;

////////////////////////////////////////////////////////////////////////////////
case 'addname':
	$xref = safe_GET('xref', WT_REGEX_XREF);

	$person = WT_Individual::getInstance($xref);
	check_record_access($person);

	$controller
		->setPageTitle(WT_I18N::translate('Add new Name'))
		->pageHeader();

	print_indi_form('update', $person, null, null, '', $person->getSex());
	break;

////////////////////////////////////////////////////////////////////////////////
case 'paste':
	$xref = safe_REQUEST($_REQUEST, 'xref', WT_REGEX_XREF);
	$fact = safe_REQUEST($_REQUEST, 'fact', WT_REGEX_UNSAFE);

	$record = WT_GedcomRecord::getInstance($xref);
	check_record_access($record);

	$controller
		->setPageTitle(WT_I18N::translate('Add from clipboard'))
		->pageHeader();

	$record->updateFact(null, $WT_SESSION->clipboard[$fact]['factrec']);
	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'reorder_media': // Sort page using Popup
	$xref = safe_REQUEST($_REQUEST, 'xref',  WT_REGEX_XREF);

	$person = WT_Individual::getInstance($xref);
	check_record_access($person);

	$controller
		->setPageTitle(WT_I18N::translate('Re-order media'))
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
				if (!$fact->isOld()) {
					preg_match_all('/(?:^1|\n\d) OBJE @(' . WT_REGEX_XREF . ')@/', $fact->getGedcom(), $matches);
					foreach ($matches[1] as $match) {
						$media = WT_Media::getInstance($match);
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
		<h4><?php echo WT_I18N::translate('Click a row, then drag-and-drop to re-order media '); ?></h4>
		<form name="reorder_form" method="post" action="edit_interface.php">
			<input type="hidden" name="action" value="reorder_media_update">
			<input type="hidden" name="xref" value="<?php echo $xref; ?>">
			<ul id="reorder_media_list">
			<?php foreach ($sort_obje as $n=>$obje) { ?>
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
				<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div>
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'reorder_media_update': // Update sort using popup
	$xref      = safe_POST('xref',  WT_REGEX_XREF);
	$order1    = safe_POST('order1');
	$keep_chan = safe_POST_bool('keep_chan');

	$person = WT_Individual::getInstance($xref);
	check_record_access($person);

	$controller
		->setPageTitle(WT_I18N::translate('Re-order media'))
		->pageHeader();

	// Delete any existing _WT_OBJE_SORT records
	$facts = array('0 @' . $person->getXref() . '@ INDI');
	foreach ($person->getFacts() as $fact) {
		if ($fact->getTag() != '_WT_OBJE_SORT') {
			$facts[] = $fact->getGedcom();
		}
	}
	if (is_array($order1)) {
		// Add new _WT_OBJE_SORT records
		foreach ($order1 as $xref=>$n) {
			$facts[] = '1 _WT_OBJE_SORT @' . $xref . '@';
		}
	}

	$person->updateRecord(implode("\n", $facts), !$keep_chan);
	
	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'reorder_children':
	$xref   = safe_GET('xref',  WT_REGEX_XREF);
	$option = safe_GET('option');

	$family = WT_Family::getInstance($xref);
	check_record_access($family);

	$controller
		->addInlineJavascript('jQuery("#reorder_list").sortable({forceHelperSize: true, forcePlaceholderSize: true, opacity: 0.7, cursor: "move", axis: "y"});')
		->addInlineJavascript('jQuery("#reorder_list").bind("sortupdate", function(event, ui) { jQuery("#"+jQuery(this).attr("id")+" input").each( function (index, value) { value.value = index+1; }); });')
		->setPageTitle(WT_I18N::translate('Re-order children'))
		->pageHeader();

	?>
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>
		<form name="reorder_form" method="post" action="edit_interface.php">
			<input type="hidden" name="action" value="reorder_update">
			<input type="hidden" name="xref" value="<?php echo $xref; ?>">
			<input type="hidden" name="option" value="bybirth">
			<ul id="reorder_list">
				<?php
				// reorder children in modified families
				$ids = array();
				foreach ($family->getChildren() as $child) {
					$ids[]=$child->getXref();
				}
				$children = array();
				foreach ($family->getChildren() as $k=>$child) {
					$bdate = $child->getEstimatedBirthDate();
					if ($bdate->isOK()) {
						$sortkey = $bdate->JD();
					} else {
						$sortkey = 1e8; // birth date missing => sort last
					}
					$children[$child->getXref()] = $sortkey;
				}
				if ($option=='bybirth') {
					asort($children);
				}
				$i=0;
				$show_full = 1; // Force details to show for each child
				foreach ($children as $id=>$child) {
					echo '<li style="cursor:move; margin-bottom:2px; position:relative;"';
					if (!in_array($id, $ids)) echo ' class="facts_value new"';
					echo ' id="li_',$id,'" >';
					print_pedigree_person(WT_Individual::getInstance($id), 2);
					echo '<input type="hidden" name="order[',$id,']" value="',$i,'">';
					echo '</li>';
					$i++;
				}
			echo '</ul>';
			?>
			<table>
				<?php echo keep_chan($family); ?>
			</table>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
				<input type="submit" class="save" onclick="document.reorder_form.action.value='reorder_children'; document.reorder_form.submit();" value="<?php echo WT_I18N::translate('sort by date of birth'); ?>">
				<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'changefamily':
	$xref = safe_GET('xref', WT_REGEX_XREF);

	$family = WT_Family::getInstance($xref);
	check_record_access($family);
	
	$controller
		->setPageTitle(WT_I18N::translate('Change Family Members'))
		->pageHeader()
		->addInlineJavascript('
				function pastename(name) {
					if (nameElement) {
						nameElement.innerHTML = name;
					}
					if (remElement) {
						remElement.style.display = "block";
					}
				}
		');

	$father = $family->getHusband();
	$mother = $family->getWife();
	$children = $family->getChildren();
	if (count($children)>0) {
		if (!is_null($father)) {
			if ($father->getSex()=="F") {
				$father->setLabel(WT_I18N::translate('mother'));
			} else {
				$father->setLabel(WT_I18N::translate('father'));
			}
		}
		if (!is_null($mother)) {
			if ($mother->getSex()=="M") {
				$mother->setLabel(WT_I18N::translate('father'));
			} else {
				$mother->setLabel(WT_I18N::translate('mother'));
			}
		}
		for ($i=0; $i<count($children); $i++) {
			if (!is_null($children[$i])) {
				if ($children[$i]->getSex()=="M") {
					$children[$i]->setLabel(WT_I18N::translate('son'));
				} elseif ($children[$i]->getSex()=="F") {
					$children[$i]->setLabel(WT_I18N::translate('daughter'));
				} else {
					$children[$i]->setLabel(WT_I18N::translate('child'));
				}
			}
		}
	} else {
		if (!is_null($father)) {
			if ($father->getSex()=="F") {
				$father->setLabel(WT_I18N::translate('wife'));
			} elseif ($father->getSex()=="M") {
				$father->setLabel(WT_I18N::translate('husband'));
			} else {
				$father->setLabel(WT_I18N::translate('spouse'));
			}
		}
		if (!is_null($mother)) {
			if ($mother->getSex()=="F") {
				$mother->setLabel(WT_I18N::translate('wife'));
			} elseif ($mother->getSex()=="M") {
				$mother->setLabel(WT_I18N::translate('husband'));
			} else {
				$father->setLabel(WT_I18N::translate('spouse'));
			}
		}
	}
	?>
	<div id="edit_interface-page">
		<h4><?php echo $controller->getPageTitle(); ?></h4>
		<div id="changefam">
			<p>
				<?php echo WT_I18N::translate('Use this page to change or remove family members.<br /><br />For each member in the family, you can use the Change link to choose a different person to fill that role in the family.  You can also use the Remove link to remove that person from the family.<br /><br />When you have finished changing the family members, click the Save button to save the changes.'); ?>
			</p>
			<form name="changefamform" method="post" action="edit_interface.php">
				<input type="hidden" name="action" value="changefamily_update">
				<input type="hidden" name="xref" value="<?php echo $xref; ?>">
				<table>
					<tr>
					<?php if ($father) { ?>
						<td class="descriptionbox">
							<b><?php echo $father->getLabel(); ?></b>
							<input type="hidden" name="HUSB" value="<?php echo $father->getXref(); ?>">
						</td>
						<td id="HUSBName" class="optionbox"><?php echo $father->getFullName(); ?>
						</td>
					<?php } else { ?>
						<td class="descriptionbox">
							<b><?php echo WT_I18N::translate('spouse'); ?></b>
							<input type="hidden" name="HUSB" value="">
						</td>
						<td id="HUSBName" class="optionbox">
						</td>
					<?php } ?>
						<td class="optionbox">
							<a href="#" id="husbrem" style="display: <?php echo is_null($father) ? 'none':'block'; ?>;" onclick="document.changefamform.HUSB.value=''; document.getElementById('HUSBName').innerHTML=''; this.style.display='none'; return false;">
								<?php echo WT_I18N::translate('Remove'); ?>
							</a>
						</td>
						<td class="optionbox">
							<a href="#" onclick="return findIndi(document.changefamform.HUSB, document.getElementById('HUSBName'));">
								<?php echo WT_I18N::translate('Change'); ?>
							</a>
						</td>
					</tr>
					<tr>
					<?php if ($mother) { ?>
						<td class="descriptionbox">
							<b><?php echo $mother->getLabel(); ?></b>
							<input type="hidden" name="WIFE" value="<?php echo $mother->getXref(); ?>">
						</td>
						<td id="WIFEName" class="optionbox">
							<?php echo $mother->getFullName(); ?>
						</td>
					<?php } else { ?>
						<td class="descriptionbox">
							<b><?php echo WT_I18N::translate('spouse'); ?></b>
							<input type="hidden" name="WIFE" value="">
						</td>
						<td id="WIFEName" class="optionbox">
						</td>
					<?php } ?>
						<td class="optionbox">
							<a href="#" id="wiferem" style="display: <?php echo is_null($mother) ? 'none':'block'; ?>;" onclick="document.changefamform.WIFE.value=''; document.getElementById('WIFEName').innerHTML=''; this.style.display='none'; return false;">
								<?php echo WT_I18N::translate('Remove'); ?>
							</a>
						</td>
						<td class="optionbox">
							<a href="#" onclick="return findIndi(document.changefamform.WIFE, document.getElementById('WIFEName'));">
								<?php echo WT_I18N::translate('Change'); ?>
							</a>
						</td>
					</tr>
					<?php $i=0; foreach ($children as $child) { ?>
					<tr>
						<td class="descriptionbox">
							<b><?php echo $child->getLabel(); ?></b>
							<input type="hidden" name="CHIL<?php echo $i; ?>" value="<?php echo $child->getXref(); ?>">
						</td>
						<td id="CHILName<?php echo $i; ?>" class="optionbox"><?php echo $child->getFullName(); ?>
						</td>
						<td class="optionbox">
							<a href="#" id="childrem<?php echo $i; ?>" style="display: block;" onclick="document.changefamform.CHIL<?php echo $i; ?>.value=''; document.getElementById('CHILName<?php echo $i; ?>').innerHTML=''; this.style.display='none'; return false;">
								<?php echo WT_I18N::translate('Remove'); ?>
							</a>
						</td>
						<td class="optionbox">
							<a href="#" onclick="return findIndi(document.changefamform.CHIL<?php echo $i; ?>, document.getElementById('CHILName<?php echo $i; ?>'));">
								<?php echo WT_I18N::translate('Change'); ?>
							</a>
						</td>
					</tr>
					<?php $i++; } ?>
					<tr>
						<td class="descriptionbox">
							<b><?php echo WT_I18N::translate('child'); ?></b>
							<input type="hidden" name="CHIL<?php echo $i; ?>" value="">
						</td>
						<td id="CHILName<?php echo $i; ?>" class="optionbox">
						</td>
						<td colspan="2" class="optionbox child">
							<a href="#" id="childrem<?php echo $i; ?>" style="display: none;" onclick="document.changefamform.CHIL<?php echo $i; ?>.value=''; document.getElementById('CHILName<?php echo $i; ?>').innerHTML=''; this.style.display='none'; return false;">
								<?php echo WT_I18N::translate('Remove'); ?>
							</a>
							<a href="#" onclick="remElement = document.getElementById('childrem<?php echo $i; ?>'); return findIndi(document.changefamform.CHIL<?php echo $i; ?>, document.getElementById('CHILName<?php echo $i; ?>'));">
								<?php echo WT_I18N::translate('Add'); ?>
							</a>
						</td>
					</tr>
				</table>
				<p id="save-cancel">
					<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
					<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
				</p>
			</form>
		</div><!-- id="changefam" -->
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'changefamily_update':
	$xref      = safe_POST('xref', WT_REGEX_XREF);
	$HUSB      = safe_POST('HUSB', WT_REGEX_XREF);
	$WIFE      = safe_POST('WIFE', WT_REGEX_XREF);
	$CHIL      = safe_POST('CHIL', WT_REGEX_XREF);
	$keep_chan = safe_POST_bool('keep_chan');

	$family    = WT_Family::getInstance($xref);
	check_record_access($family);

	$controller
		->setPageTitle(WT_I18N::translate('Change Family Members'))
		->pageHeader();

	// Current family members
	$old_father   = $family->getHusband();
	$old_mother   = $family->getWife();
	$old_children = $family->getChildren();

	// New family members
	$new_father = WT_Individual::getInstance($HUSB);
	$new_mother = WT_Individual::getInstance($WIFE);
	$new_children = array();
	if (is_array($CHIL)) {
		foreach ($CHIL as $child) {
			$new_children[] = WT_Individaul::getInstance($child);
		}
	}

	if ($old_father != $new_father) {
		if ($old_father) {
			// Remove old FAMS link
			foreach ($old_father->getFacts('FAMS') as $fact) {
				if ($fact->getTarget() == $family) {
					$old_father->updateFact($fact->getFactId(), null, !$keep_chan);
				}
			}
			// Remove old HUSB link
			foreach ($family->getFacts('HUSB|WIFE') as $fact) {
				if ($fact->getTarget() == $old_father) {
					$family->updateFact($fact->getFactId(), null, !$keep_chan);
				}
			}
		}
		if ($new_father) {
			// Add new FAMS link
			$new_father->updateFact(null, '1 FAMS @' . $family->getXref() . '@', !$keep_chan);
			// Add new HUSB link
			$family->updateFact(null, '1 HUSB @' . $new_father->getXref() . '@', !$keep_chan);
		}
	}

	if ($old_mother != $new_mother) {
		if ($old_mother) {
			// Remove old FAMS link
			foreach ($old_mother->getFacts('FAMS') as $fact) {
				if ($fact->getTarget() == $family) {
					$old_mother->updateFact($fact->getFactId(), null, !$keep_chan);
				}
			}
			// Remove old WIFE link
			foreach ($family->getFacts('HUSB|WIFE') as $fact) {
				if ($fact->getTarget() == $old_mother) {
					$family->updateFact($fact->getFactId(), null, !$keep_chan);
				}
			}
		}
		if ($new_mother) {
			// Add new FAMS link
			$new_mother->updateFact(null, '1 FAMS @' . $family->getXref() . '@', !$keep_chan);
			// Add new WIFE link
			$family->updateFact(null, '1 WIFE @' . $new_mother->getXref() . '@', !$keep_chan);
		}
	}

	foreach ($old_children as $old_child) {
		if (!in_array($old_child, $new_children)) {
			// Remove old FAMC link
			foreach ($old_child->getFacts('FAMC') as $fact) {
				if ($fact->getTarget() == $family) {
					$old_child->updateFact($fact->getFactId(), null, !$keep_chan);
				}
			}
			// Remove old CHIL link
			foreach ($family->getFacts('CHIL') as $fact) {
				if ($fact->getTarget() == $old_child) {
					$family->updateFact($fact->getFactId(), null, !$keep_chan);
				}
			}
		}
	}

	foreach ($new_children as $new_child) {
		if (!in_array($new_child, $old_children)) {
			// Add new FAMC link
			$new_child->updateFact(null, '1 FAMS @' . $family->getXref() . '@', !$keep_chan);
			// Add new CHIL link
			$family->updateFact(null, '1 CHIL @' . $new_child->getXref() . '@', !$keep_chan);
		}
	}

	$controller->addInlineJavascript('closePopupAndReloadParent();');
	break;

////////////////////////////////////////////////////////////////////////////////
case 'reorder_update':
	$xref      = safe_POST('xref', WT_REGEX_XREF);
	$order     = safe_POST('order');
	$keep_chan = safe_POST_bool('keep_chan');

	$family = WT_Family::getInstance($xref);
	check_record_access($family);

	$controller
		->setPageTitle(WT_I18N::translate('Re-order children'))
		->pageHeader();

	if (is_array($order)) {
		$gedcom = array('0 @' . $family->getXref() . '@ FAM');
		$facts  = $family->getFacts();

		// Move children to the end of the record
		foreach ($order as $child=>$num) {
			foreach ($facts as $n=>$fact) {
				if ($fact->getValue() == '@'.$child.'@') {
					$facts[]=$fact;
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
case 'reorder_fams':
	$xref   = safe_GET('xref', WT_REGEX_XREF);
	$option = safe_GET('option');

	$person = WT_Individual::getInstance($xref);
	check_record_access($person);

	$controller
		->addInlineJavascript('jQuery("#reorder_list").sortable({forceHelperSize: true, forcePlaceholderSize: true, opacity: 0.7, cursor: "move", axis: "y"});')
		//-- update the order numbers after drag-n-drop sorting is complete
		->addInlineJavascript('jQuery("#reorder_list").bind("sortupdate", function(event, ui) { jQuery("#"+jQuery(this).attr("id")+" input").each( function (index, value) { value.value = index+1; }); });')
		->setPageTitle(WT_I18N::translate('Re-order families'))
		->pageHeader();

	$fams = $person->getSpouseFamilies();
	if ($option=='bymarriage') {
		usort($fams, array('WT_Family', 'CompareMarrDate'));
	}

	?>
	<div id="edit_interface-page">
	<h4><?php echo $controller->getPageTitle(); ?></h4>
	<form name="reorder_form" method="post" action="edit_interface.php">
		<input type="hidden" name="action" value="reorder_fams_update">
		<input type="hidden" name="xref" value="<?php echo $xref; ?>">
		<input type="hidden" name="option" value="bymarriage">
		<ul id="reorder_list">
		<?php foreach ($fams as $n=>$family) { ?>
			<li class="facts_value" style="cursor:move;margin-bottom:2px;" id="li_<?php echo $family->getXref(); ?>" >
				<div class="name2"><?php echo $family->getFullName(); ?></div>
				<?php echo $family->format_first_major_fact(WT_EVENTS_MARR, 2); ?>
				<input type="hidden" name="order[<?php echo $family->getXref(); ?>]" value="<?php echo $n; ?>">
			</li>
		<?php } ?>
		</ul>
		<p id="save-cancel">
			<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
			<input type="submit" class="save" onclick="document.reorder_form.action.value='reorder_fams'; document.reorder_form.submit();" value="<?php echo WT_I18N::translate('sort by date of marriage'); ?>">
			<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
		</p>
	</form>
	</div><!-- id="edit_interface-page" -->
	<?php
	break;

////////////////////////////////////////////////////////////////////////////////
case 'reorder_fams_update':
	$xref      = safe_POST('xref', WT_REGEX_XREF);
	$order     = safe_POST('order');
	$keep_chan = safe_POST_bool('keep_chan');

	$person = WT_Individual::getInstance($xref);
	check_record_access($person);

	$controller
		->setPageTitle(WT_I18N::translate('Re-order families'))
		->pageHeader();

	if (is_array($order)) {
		$gedcom = array('0 @' . $person->getXref() . '@ INDI');
		$facts  = $person->getFacts();

		// Move families to the end of the record
		foreach ($order as $family=>$num) {
			foreach ($facts as $n=>$fact) {
				if ($fact->getValue() == '@'.$family.'@') {
					$facts[]=$fact;
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

// Keep the existing CHAN record when editing
function keep_chan(WT_GedcomRecord $record=null) {
	global $NO_UPDATE_CHAN;

	if (WT_USER_IS_ADMIN) {
		$checked = $NO_UPDATE_CHAN ? ' checked="checked"' : '';

		if ($record) {
			$details = 
				WT_Gedcom_Tag::getLabelValue('DATE', $record->LastChangeTimestamp()) .
				WT_Gedcom_Tag::getLabelValue('_WT_USER', $record->LastChangeUser());
		} else {
			$details = '';
		}
	
		return
			'<tr><td class="descriptionbox wrap width25">' .
			WT_Gedcom_Tag::getLabel('CHAN') .
			'</td><td class="optionbox wrap">' .
			'<input type="checkbox" name="keep_chan"' . $checked . '>' .
			WT_I18N::translate('Do not update the “last change” record') .
			help_link('no_update_CHAN') .
			$details;
			'</td></tr>';
	} else {
		return '';
	}
}

// prints a form to add an individual or edit an individual's name
function print_indi_form($nextaction, WT_Individual $person=null, WT_Family $family=null, WT_Fact $name_fact=null, $famtag='CHIL', $sextag='U') {
	global $WORD_WRAPPED_NOTES;
	global $NPFX_accept, $SPFX_accept, $NSFX_accept, $FILE_FORM_accept;
	global $bdm, $STANDARD_NAME_FACTS, $REVERSED_NAME_FACTS, $ADVANCED_NAME_FACTS, $ADVANCED_PLAC_FACTS;
	global $QUICK_REQUIRED_FACTS, $QUICK_REQUIRED_FAMFACTS, $NO_UPDATE_CHAN, $controller;

	$SURNAME_TRADITION=get_gedcom_setting(WT_GED_ID, 'SURNAME_TRADITION');

	if ($person) {
		$xref = $person->getXref();
	} else {
		$xref = null;
	}
	if ($family) {
		$famid = $family->getXref();
	} else {
		$famid = null;
	}
	if ($name_fact) {
		$name_fact_id = $name_fact->getFactId();
		$name_type    = $name_fact->getAttribute('TYPE');
		$namerec = $name_fact->getGedcom();
		// Populate the standard NAME field and subfields
		foreach ($STANDARD_NAME_FACTS as $tag) {
			$name_fields[$tag] = $name_fact->getAttribute($tag);
		}
	} else {
		$name_fact_id = null;
		$name_type    = null;
		$name_fields  = array();
		$namerec      = null;
		// Populate the standard NAME field and subfields
		foreach ($STANDARD_NAME_FACTS as $tag) {
			$name_fields[$tag] = '';
		}
	}

	$bdm = ''; // used to copy '1 SOUR' to '2 SOUR' for BIRT DEAT MARR

	echo '<div id="edit_interface-page">';
	echo '<h4>', $controller->getPageTitle(), '</h4>';
	init_calendar_popup();
	echo "<form method=\"post\" name=\"addchildform\" onsubmit=\"return checkform();\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"$nextaction\">";
	echo "<input type=\"hidden\" name=\"fact_id\" value=\"$name_fact_id\">";
	echo "<input type=\"hidden\" name=\"famid\" value=\"$famid\">";
	echo "<input type=\"hidden\" name=\"xref\" value=\"$xref\">";
	echo "<input type=\"hidden\" name=\"famtag\" value=\"$famtag\">";
	echo "<input type=\"hidden\" name=\"goto\" value=\"\">"; // set by javascript
	echo "<table class=\"facts_table\">";

	switch ($nextaction) {
	case 'addchildaction':
	case 'addopfchildaction':
		// When adding a new child, specify the pedigree
		add_simple_tag('0 PEDI');
		break;
	case 'update':
		// When adding/editing a name, specify the type
		add_simple_tag('0 TYPE '.$name_type);
		break;
	}

	$new_marnm='';
	// Inherit surname from parents, spouse or child
	if (!$namerec) {
		// We'll need the parent's name to set the child's surname
		$family = WT_Family::getInstance($famid);
		if ($family) {
			$father = $family->getHusband();
			if ($father->getFactByType('NAME')) {
				$father_name = $father->getFactByType('NAME')->getValue();
			} else {
				$father_name='';
			}
			$mother = $family->getWife();
			if ($mother->getFactByType('NAME')) {
				$mother_name = $mother->getFactByType('NAME')->getValue();
			} else {
				$mother_name = '';
			}
		} else {
			$father_name = '';
			$father_name = '';
		}
		// We'll need the spouse/child's name to set the spouse/parent's surname
		if ($person && $person->getFactByType('NAME')) {
			$indi_name = $person->getFactByType('NAME')->getValue();
		} else {
			$indi_name = '';
		}
		// Different cultures do surnames differently
		switch ($SURNAME_TRADITION) {
		case 'spanish':
			//Mother: Maria /AAAA BBBB/
			//Father: Jose  /CCCC DDDD/
			//Child:  Pablo /CCCC AAAA/
			switch ($nextaction) {
			case 'addchildaction':
				if (preg_match('/\/(\S+)\s+\S+\//', $mother_name, $matchm) &&
						preg_match('/\/(\S+)\s+\S+\//', $father_name, $matchf)) {
					$name_fields['SURN']=$matchf[1].' '.$matchm[1];
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				break;
			case 'addnewparentaction':
				if ($famtag=='HUSB' && preg_match('/\/(\S+)\s+\S+\//', $indi_name, $match)) {
					$name_fields['SURN']=$match[1].' ';
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				if ($famtag=='WIFE' && preg_match('/\/\S+\s+(\S+)\//', $indi_name, $match)) {
					$name_fields['SURN']=$match[1].' ';
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				break;
			}
			break;
		case 'portuguese':
			//Mother: Maria /AAAA BBBB/
			//Father: Jose  /CCCC DDDD/
			//Child:  Pablo /BBBB DDDD/
			switch ($nextaction) {
			case 'addchildaction':
				if (preg_match('/\/\S+\s+(\S+)\//', $mother_name, $matchm) &&
						preg_match('/\/\S+\s+(\S+)\//', $father_name, $matchf)) {
					$name_fields['SURN']=$matchf[1].' '.$matchm[1];
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				break;
			case 'addnewparentaction':
				if ($famtag=='HUSB' && preg_match('/\/\S+\s+(\S+)\//', $indi_name, $match)) {
					$name_fields['SURN']=' '.$match[1];
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				if ($famtag=='WIFE' && preg_match('/\/(\S+)\s+\S+\//', $indi_name, $match)) {
					$name_fields['SURN']=' '.$match[1];
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				break;
			}
			break;
		case 'icelandic':
			// Sons get their father's given name plus "sson"
			// Daughters get their father's given name plus "sdottir"
			switch ($nextaction) {
			case 'addchildaction':
				if ($sextag=='M' && preg_match('/(\S+)\s+\/.*\//', $father_name, $match)) {
					$name_fields['SURN']=preg_replace('/s$/', '', $match[1]).'sson';
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				if ($sextag=='F' && preg_match('/(\S+)\s+\/.*\//', $father_name, $match)) {
					$name_fields['SURN']=preg_replace('/s$/', '', $match[1]).'sdottir';
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				break;
			case 'addnewparentaction':
				if ($famtag=='HUSB' && preg_match('/(\S+)sson\s+\/.*\//i', $indi_name, $match)) {
					$name_fields['GIVN']=$match[1];
					$name_fields['NAME']=$name_fields['GIVN'].' //';
				}
				if ($famtag=='WIFE' && preg_match('/(\S+)sdottir\s+\/.*\//i', $indi_name, $match)) {
					$name_fields['GIVN']=$match[1];
					$name_fields['NAME']=$name_fields['GIVN'].' //';
				}
				break;
			}
			break;
		case 'patrilineal':
			// Father gives his surname to his children
			switch ($nextaction) {
			case 'addchildaction':
				if (preg_match('/\/((?:[a-z]{2,3} )*)(.*)\//i', $father_name, $match)) {
					$name_fields['SURN']=$match[2];
					$name_fields['SPFX']=trim($match[1]);
					$name_fields['NAME']="/{$match[1]}{$match[2]}/";
				}
				break;
			case 'addnewparentaction':
				if ($famtag=='HUSB' && preg_match('/\/((?:[a-z]{2,3} )*)(.*)\//i', $indi_name, $match)) {
					$name_fields['SURN']=$match[2];
					$name_fields['SPFX']=trim($match[1]);
					$name_fields['NAME']="/{$match[1]}{$match[2]}/";
				}
				break;
			}
			break;
		case 'matrilineal':
			// Mother gives her surname to her children
			switch ($nextaction) {
			case 'addchildaction':
				if (preg_match('/\/((?:[a-z]{2,3} )*)(.*)\//i', $mother, $match)) {
					$name_fields['SURN']=$match[2];
					$name_fields['SPFX']=trim($match[1]);
					$name_fields['NAME']="/{$match[1]}{$match[2]}/";
				}
				break;
			case 'addnewparentaction':
				if ($famtag=='WIFE' && preg_match('/\/((?:[a-z]{2,3} )*)(.*)\//i', $indi_name, $match)) {
					$name_fields['SURN']=$match[2];
					$name_fields['SPFX']=trim($match[1]);
					$name_fields['NAME']="/{$match[1]}{$match[2]}/";
				}
				break;
			}
			break;
		case 'paternal':
		case 'polish':
		case 'lithuanian':
			// Father gives his surname to his wife and children
			switch ($nextaction) {
			case 'addspouseaction':
				if ($famtag=='WIFE' && preg_match('/\/(.*)\//', $indi_name, $match)) {
					if ($SURNAME_TRADITION=='polish') {
						$match[1]=preg_replace(array('/ski$/', '/cki$/', '/dzki$/', '/żki$/'), array('ska', 'cka', 'dzka', 'żka'), $match[1]);
					} else if ($SURNAME_TRADITION=='lithuanian') {
						$match[1]=preg_replace(array('/as$/', '/is$/', '/ys$/', '/us$/'), array('ienė', 'ienė', 'ienė', 'ienė'), $match[1]);
					}
					$new_marnm=$match[1];
				}
				break;
			case 'addchildaction':
				if (preg_match('/\/((?:[a-z]{2,3} )*)(.*)\//i', $father_name, $match)) {
					$name_fields['SURN']=$match[2];
					if ($SURNAME_TRADITION=='polish' && $sextag=='F') {
						$match[2]=preg_replace(array('/ski$/', '/cki$/', '/dzki$/', '/żki$/'), array('ska', 'cka', 'dzka', 'żka'), $match[2]);
					} else if ($SURNAME_TRADITION=='lithuanian' && $sextag=='F') {
						$match[2]=preg_replace(array('/as$/', '/a$/', '/is$/', '/ys$/', '/ius$/', '/us$/'), array('aitė', 'aitė', 'ytė', 'ytė', 'iūtė', 'utė'), $match[2]);
					}
					$name_fields['SPFX']=trim($match[1]);
					$name_fields['NAME']="/{$match[1]}{$match[2]}/";
				}
				break;
			case 'addnewparentaction':
				if ($famtag=='HUSB' && preg_match('/\/((?:[a-z]{2,3} )*)(.*)\//i', $indi_name, $match)) {
					if ($SURNAME_TRADITION=='polish' && $sextag=='M') {
						$match[2]=preg_replace(array('/ska$/', '/cka$/', '/dzka$/', '/żka$/'), array('ski', 'cki', 'dzki', 'żki'), $match[2]);
					} else if ($SURNAME_TRADITION=='lithuanian') {
						// not a complete list as the rules are somewhat complicated but will do 95% correctly
						$match[2]=preg_replace(array('/aitė$/', '/ytė$/', '/iūtė$/', '/utė$/'), array('as', 'is', 'ius', 'us'), $match[2]);
					}
					$name_fields['SPFX']=trim($match[1]);
					$name_fields['SURN']=$match[2];
					$name_fields['NAME']="/{$match[1]}{$match[2]}/";
				}
				if ($famtag=='WIFE' && preg_match('/\/((?:[a-z]{2,3} )*)(.*)\//i', $indi_name, $match)) {
					if ($SURNAME_TRADITION=='lithuanian') {
						$match[2]=preg_replace(array('/as$/', '/is$/', '/ys$/', '/us$/'), array('ienė', 'ienė', 'ienė', 'ienė'), $match[2]);
						$match[2]=preg_replace(array('/aitė$/', '/ytė$/', '/iūtė$/', '/utė$/'), array('ienė', 'ienė', 'ienė', 'ienė'), $match[2]);
						$new_marnm=$match[2];
					}
				}
				break;
			}
			break;
		}
	}

	// Make sure there are two slashes in the name
	if (!preg_match('/\//', $name_fields['NAME']))
		$name_fields['NAME'].=' /';
	if (!preg_match('/\/.*\//', $name_fields['NAME']))
		$name_fields['NAME'].='/';

	// Populate any missing 2 XXXX fields from the 1 NAME field
	$npfx_accept=implode('|', $NPFX_accept);
	if (preg_match ("/((($npfx_accept)\.? +)*)([^\n\/\"]*)(\"(.*)\")? *\/(([a-z]{2,3} +)*)(.*)\/ *(.*)/i", $name_fields['NAME'], $name_bits)) {
		if (empty($name_fields['NPFX'])) {
			$name_fields['NPFX']=$name_bits[1];
		}
		if (empty($name_fields['SPFX']) && empty($name_fields['SURN'])) {
			$name_fields['SPFX']=trim($name_bits[7]);
			// For names with two surnames, there will be four slashes.
			// Turn them into a list
			$name_fields['SURN']=preg_replace('~/[^/]*/~', ',', $name_bits[9]);
		}
		if (empty($name_fields['GIVN'])) {
			$name_fields['GIVN']=$name_bits[4];
		}
		// Don't automatically create an empty NICK - it is an "advanced" field.
		if (empty($name_fields['NICK']) && !empty($name_bits[6]) && !preg_match('/^2 NICK/m', $namerec)) {
			$name_fields['NICK']=$name_bits[6];
		}
	}

	// Edit the standard name fields
	foreach ($name_fields as $tag=>$value) {
		add_simple_tag("0 $tag $value");
	}

	// Get the advanced name fields
	$adv_name_fields=array();
	if (preg_match_all('/('.WT_REGEX_TAG.')/', $ADVANCED_NAME_FACTS, $match))
		foreach ($match[1] as $tag)
			$adv_name_fields[$tag]='';
	// This is a custom tag, but webtrees uses it extensively.
	if ($SURNAME_TRADITION=='paternal' || $SURNAME_TRADITION=='polish' || $SURNAME_TRADITION=='lithuanian' || (strpos($namerec, '2 _MARNM')!==false)) {
		$adv_name_fields['_MARNM']='';
	}
	$person = WT_Individual::getInstance($xref);
	if (isset($adv_name_fields['TYPE'])) {
		unset($adv_name_fields['TYPE']);
	}
	foreach ($adv_name_fields as $tag=>$dummy) {
		// Edit existing tags
		if (preg_match_all("/2 $tag (.+)/", $namerec, $match))
			foreach ($match[1] as $value) {
				if ($tag=='_MARNM') {
					$mnsct = preg_match('/\/(.+)\//', $value, $match2);
					$marnm_surn = '';
					if ($mnsct>0) $marnm_surn = $match2[1];
					add_simple_tag("2 _MARNM ".$value);
					add_simple_tag("2 _MARNM_SURN ".$marnm_surn);
				} else {
					add_simple_tag("2 $tag $value", '', WT_Gedcom_Tag::getLabel("NAME:{$tag}", $person));
				}
			}
			// Allow a new row to be entered if there was no row provided
			if (count($match[1])==0 && empty($name_fields[$tag]) || $tag!='_HEB' && $tag!='NICK')
				if ($tag=='_MARNM') {
					if (strstr($ADVANCED_NAME_FACTS, '_MARNM')==false) {
						add_simple_tag("0 _MARNM");
						add_simple_tag("0 _MARNM_SURN $new_marnm");
					}
				} else {
					add_simple_tag("0 $tag", '', WT_Gedcom_Tag::getLabel("NAME:{$tag}", $person));
				}
	}

	// Handle any other NAME subfields that aren't included above (SOUR, NOTE, _CUSTOM, etc)
	if ($namerec) {
		$gedlines = explode("\n", $namerec); // -- find the number of lines in the record
		$fields = explode(' ', $gedlines[0]);
		$glevel = $fields[0];
		$level = $glevel;
		$type = trim($fields[1]);
		$level1type = $type;
		$tags=array();
		$i = 0;
		do {
			if ($type!='TYPE' && !isset($name_fields[$type]) && !isset($adv_name_fields[$type])) {
				$text = '';
				for ($j=2; $j<count($fields); $j++) {
					if ($j>2) $text .= ' ';
					$text .= $fields[$j];
				}
				$iscont = false;
				while (($i+1<count($gedlines))&&(preg_match("/".($level+1)." (CON[CT]) ?(.*)/", $gedlines[$i+1], $cmatch)>0)) {
					$iscont=true;
					if ($cmatch[1]=="CONT") $text.="\n";
					if ($WORD_WRAPPED_NOTES) $text .= ' ';
					$text .= $cmatch[2];
					$i++;
				}
				add_simple_tag($level.' '.$type.' '.$text);
			}
			$tags[]=$type;
			$i++;
			if (isset($gedlines[$i])) {
				$fields = explode(' ', $gedlines[$i]);
				$level = $fields[0];
				if (isset($fields[1])) $type = $fields[1];
			}
		} while (($level>$glevel)&&($i<count($gedlines)));
	}

	// If we are adding a new individual, add the basic details
	if ($nextaction!='update') {
		echo '</table><br><table class="facts_table">';
		// 1 SEX
		if ($famtag=="HUSB" || $sextag=="M") {
			add_simple_tag("0 SEX M");
		} elseif ($famtag=="WIFE" || $sextag=="F") {
			add_simple_tag("0 SEX F");
		} else {
			add_simple_tag("0 SEX");
		}
		$bdm = "BD";
		if (preg_match_all('/('.WT_REGEX_TAG.')/', $QUICK_REQUIRED_FACTS, $matches)) {
			foreach ($matches[1] as $match) {
				if (!in_array($match, explode('|', WT_EVENTS_DEAT))) {
					addSimpleTags($match);
				}
			}
		}
		//-- if adding a spouse add the option to add a marriage fact to the new family
		if ($nextaction=='addspouseaction' || ($nextaction=='addnewparentaction' && $famid!='new')) {
			$bdm .= "M";
			if (preg_match_all('/('.WT_REGEX_TAG.')/', $QUICK_REQUIRED_FAMFACTS, $matches)) {
				foreach ($matches[1] as $match) {
					addSimpleTags($match);
				}
			}
		}
		if (preg_match_all('/('.WT_REGEX_TAG.')/', $QUICK_REQUIRED_FACTS, $matches)) {
			foreach ($matches[1] as $match) {
				if (in_array($match, explode('|', WT_EVENTS_DEAT))) {
					addSimpleTags($match);
				}
			}
		}
	}
	echo keep_chan($person);
	echo "</table>";
	if ($nextaction=='update') { // GEDCOM 5.5.1 spec says NAME doesn't get a OBJE
		print_add_layer('SOUR');
		print_add_layer('NOTE');
		print_add_layer('SHARED_NOTE');
	} else {
		print_add_layer('SOUR', 1);
		print_add_layer('OBJE', 1);
		print_add_layer('NOTE', 1);
		print_add_layer('SHARED_NOTE', 1);
	}
	echo '<p id="save-cancel">';
	echo '<input type="submit" class="save" value="', /* I18N: button label */ WT_I18N::translate('save'), '">';
	if (preg_match('/^add(child|spouse|newparent)/', $nextaction)) {
		echo '<input type="submit" class="save" value="', /* I18N: button label */ WT_I18N::translate('go to new individual'), '" onclick="document.addchildform.goto.value=\'new\';">';
	}
	echo '<input type="button" class="cancel" value="', /* I18N: button label */ WT_I18N::translate('close'), '" onclick="window.close();">';
	echo '</p>';
	echo '</form>';
	$controller->addInlineJavascript('
	SURNAME_TRADITION="'.$SURNAME_TRADITION.'";
	sextag="'.$sextag.'";
	famtag="'.$famtag.'";
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
		var frm =document.forms[0];
		var npfx=frm.NPFX.value;
		var givn=frm.GIVN.value;
		var spfx=frm.SPFX.value;
		var surn=frm.SURN.value;
		var nsfx=frm.NSFX.value;
		if (SURNAME_TRADITION=="polish" && (sextag=="F" || famtag=="WIFE")) {
			surn=surn.replace(/ski$/, "ska");
			surn=surn.replace(/cki$/, "cka");
			surn=surn.replace(/dzki$/, "dzka");
			surn=surn.replace(/żki$/, "żka");
		}
		// Commas are used in the GIVN and SURN field to separate lists of surnames.
		// For example, to differentiate the two Spanish surnames from an English
		// double-barred name.
		// Commas *may* be used in other fields, and will form part of the NAME.
		if (WT_LOCALE=="vi" || WT_LOCALE=="hu") {
			// Default format: /SURN/ GIVN
			return trim(npfx+" /"+trim(spfx+" "+surn).replace(/ *, */g, " ")+"/ "+givn.replace(/ *, */g, " ")+" "+nsfx);
		} else if (WT_LOCALE=="zh") {
			// Default format: /SURN/GIVN
			return trim(npfx+" /"+trim(spfx+" "+surn).replace(/ *, */g, " ")+"/"+givn.replace(/ *, */g, " ")+" "+nsfx);
		} else {
			// Default format: GIVN /SURN/
			return trim(npfx+" "+givn.replace(/ *, */g, " ")+" /"+trim(spfx+" "+surn).replace(/ *, */g, " ")+"/ "+nsfx);
		}
	}

	// Update the NAME and _MARNM fields from the name components
	// and also display the value in read-only "gedcom" format.
	function updatewholename() {
		// don’t update the name if the user manually changed it
		if (manualChange) return;
		// Update NAME field from components and display it
		var frm =document.forms[0];
		var npfx=frm.NPFX.value;
		var givn=frm.GIVN.value;
		var spfx=frm.SPFX.value;
		var surn=frm.SURN.value;
		var nsfx=frm.NSFX.value;
		document.getElementById("NAME").value=generate_name();
		document.getElementById("NAME_display").innerHTML=frm.NAME.value;
		// Married names inherit some NSFX values, but not these
		nsfx=nsfx.replace(/^(I|II|III|IV|V|VI|Junior|Jr\.?|Senior|Sr\.?)$/i, "");
		// Update _MARNM field from _MARNM_SURN field and display it
		// Be careful of mixing latin/hebrew/etc. character sets.
		var ip=document.getElementsByTagName("input");
		var marnm_id="";
		var romn="";
		var heb="";
		for (var i=0; i<ip.length; i++) {
			var val=ip[i].value;
			if (ip[i].id.indexOf("_HEB")==0)
				heb=val;
			if (ip[i].id.indexOf("ROMN")==0)
				romn=val;
			if (ip[i].id.indexOf("_MARNM")==0) {
				if (ip[i].id.indexOf("_MARNM_SURN")==0) {
					var msurn="";
					if (val!="") {
						var lc=lang_class(document.getElementById(ip[i].id).value);
						if (lang_class(frm.NAME.value)==lc)
							msurn=trim(npfx+" "+givn+" /"+val+"/ "+nsfx);
						else if (lc=="hebrew")
							msurn=heb.replace(/\/.*\//, "/"+val+"/");
						else if (lang_class(romn)==lc)
							msurn=romn.replace(/\/.*\//, "/"+val+"/");
					}
					document.getElementById(marnm_id).value=msurn;
					document.getElementById(marnm_id+"_display").innerHTML=msurn;
				} else {
					marnm_id=ip[i].id;
				}
			}
		}
	}

	/**
	* convert a hidden field to a text box
	*/
	var oldName = "";
	var manualChange = false;
	function convertHidden(eid) {
		var element = document.getElementById(eid);
		if (element) {
			if (element.type=="hidden") {
				// IE doesn’t allow changing the "type" of an input field so we’ll cludge it ( silly :P)
				if (IE) {
					var newInput = document.createElement("input");
					newInput.setAttribute("type", "text");
					newInput.setAttribute("name", element.Name);
					newInput.setAttribute("id", element.id);
					newInput.setAttribute("value", element.value);
					newInput.setAttribute("onchange", element.onchange);
					var parent = element.parentNode;
					parent.replaceChild(newInput, element);
					element = newInput;
				}
				else {
					element.type="text";
				}
				element.size="40";
				oldName = element.value;
				manualChange = true;
				var delement = document.getElementById(eid+"_display");
				if (delement) {
					delement.style.display="none";
					// force FF ui to update the display
					if (delement.innerHTML != oldName) {
						oldName = delement.innerHTML;
						element.value = oldName;
					}
				}
			}
			else {
				manualChange = false;
				// IE doesn’t allow changing the "type" of an input field so we’ll cludge it ( silly :P)
				if (IE) {
					var newInput = document.createElement("input");
					newInput.setAttribute("type", "hidden");
					newInput.setAttribute("name", element.Name);
					newInput.setAttribute("id", element.id);
					newInput.setAttribute("value", element.value);
					newInput.setAttribute("onchange", element.onchange);
					var parent = element.parentNode;
					parent.replaceChild(newInput, element);
					element = newInput;
				}
				else {
					element.type="hidden";
				}
				var delement = document.getElementById(eid+"_display");
				if (delement) {
					delement.style.display="inline";
				}
			}
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
	echo '</div><!-- id="edit_interface-page" -->';
}

// Can we edit a WT_GedcomRecord object
function check_record_access(WT_GedcomRecord $object=null) {
	global $controller;
	
	if (!$object || !$object->canShow() || !$object->canEdit()) {
		$controller
			->pageHeader()
			->addInlineJavascript('closePopupAndReloadParent();');
		exit;
	}
}
