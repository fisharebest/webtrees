<?php
// PopUp Window to provide editing features.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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

$controller=new WT_Controller_Simple();
$controller
	->requireMemberLogin()
	->addExternalJavascript(WT_JQUERY_URL)
	->addExternalJavascript(WT_JQUERYUI_URL)
	->addExternalJavascript(WT_STATIC_URL.'js/webtrees.js')
	->addExternalJavascript(WT_STATIC_URL.'js/autocomplete.js')
	->setPageTitle(WT_I18N::translate('Edit'))
	->pageHeader();

require WT_ROOT.'includes/functions/functions_edit.php';

// TODO work out whether to use GET/POST for these
// TODO decide what (if any) validation is required on these parameters
$action =safe_REQUEST($_REQUEST, 'action',  WT_REGEX_UNSAFE);
$linenum=safe_REQUEST($_REQUEST, 'linenum', WT_REGEX_UNSAFE);
$pid    =safe_REQUEST($_REQUEST, 'pid',     WT_REGEX_XREF);
$famid  =safe_REQUEST($_REQUEST, 'famid',   WT_REGEX_XREF);
$text   =safe_REQUEST($_REQUEST, 'text',    WT_REGEX_UNSAFE);
$tag    =safe_REQUEST($_REQUEST, 'tag',     WT_REGEX_UNSAFE);
$famtag =safe_REQUEST($_REQUEST, 'famtag',  WT_REGEX_UNSAFE);
$glevels=safe_REQUEST($_REQUEST, 'glevels', WT_REGEX_UNSAFE);
$islink =safe_REQUEST($_REQUEST, 'islink',  WT_REGEX_UNSAFE);
$type   =safe_REQUEST($_REQUEST, 'type',    WT_REGEX_UNSAFE);
$fact   =safe_REQUEST($_REQUEST, 'fact',    WT_REGEX_UNSAFE);
$option =safe_REQUEST($_REQUEST, 'option',  WT_REGEX_UNSAFE);
$gender =safe_REQUEST($_REQUEST, 'gender',  WT_REGEX_UNSAFE);

$assist =safe_REQUEST($_REQUEST, 'assist',  WT_REGEX_UNSAFE);
$noteid =safe_REQUEST($_REQUEST, 'noteid',  WT_REGEX_UNSAFE);

$pid_array      =safe_REQUEST($_REQUEST, 'pid_array',       WT_REGEX_XREF);
$pids_array_add =safe_REQUEST($_REQUEST, 'pids_array_add',  WT_REGEX_XREF);
$pids_array_edit=safe_REQUEST($_REQUEST, 'pids_array_edit', WT_REGEX_XREF);

$update_CHAN=!safe_POST_bool('preserve_last_changed');

$uploaded_files = array();

echo '<script>';
?>
	var locale_date_format='<?php echo preg_replace('/[^DMY]/', '', str_replace(array('J', 'F'), array('D', 'M'), strtoupper($DATE_FORMAT))); ?>';

	function addnewrepository(field) {
		pastefield = field;
		window.open('edit_interface.php?action=addnewrepository&pid=newrepo', '_blank', find_window_specs);
		return false;
	}

	function openerpasteid(id) {
		window.opener.paste_id(id);
		window.close();
	}

	function paste_id(value) {
		pastefield.value = value;
	}

	function paste_char(value) {
		pastefield.value += value;
		if (pastefield.id=='NPFX' || pastefield.id=='GIVN' || pastefield.id=='SPFX' || pastefield.id=='SURN' || pastefield.id=='NSFX') {
			updatewholename();
		}
	}
<?php
echo '</script>';
//-- check if user has access to the gedcom record
$edit = false;
$success = false;

if (!empty($pid)) {
	if (($pid!="newsour") && ($pid!="newrepo") && ($noteid!="newnote")) {
		$gedrec = find_gedcom_record($pid, WT_GED_ID, true);
		$ct = preg_match("/^0 @$pid@ (.*)/i", $gedrec, $match);
		if ($ct>0) {
			$type = trim($match[1]);
			$tmp  = WT_GedcomRecord::getInstance($pid);
			$edit = $tmp->canDisplayDetails() && $tmp->canEdit();
		}
		// Don't allow edits if the record has changed since the edit-link was created
		checkChangeTime($pid, $gedrec, safe_GET('accesstime', WT_REGEX_INTEGER));
	} else {
		$edit = true;
	}
} elseif (!empty($famid)) {
	if ($famid != "new") {
		$gedrec = find_gedcom_record($famid, WT_GED_ID, true);
		$ct = preg_match("/^0 @$famid@ (.*)/i", $gedrec, $match);
		if ($ct>0) {
			$type = trim($match[1]);
			$tmp  = WT_GedcomRecord::getInstance($famid);
			$edit = $tmp->canDisplayDetails() && $tmp->canEdit();
		}
		// Don't allow edits if the record has changed since the edit-link was created
		checkChangeTime($famid, $gedrec, safe_GET('accesstime', WT_REGEX_INTEGER));
	}
} elseif (($action!="addchild")&&($action!="addchildaction")&&($action!="addnewsource")&&($action!="mod_edit_fact")&&($action!="addnewnote")&&($action!="addmedia_links")&&($action!="addnoteaction")&&($action!="addnoteaction_assisted")) {
	// No $pid?  Internal error of some sort
	$edit = true;
} else {
	$edit = true;
}

echo '<div id="edit_interface-page">';// page container
if (!WT_USER_CAN_EDIT || !$edit || !$ALLOW_EDIT_GEDCOM) {
	echo
		'<p class="error">', WT_I18N::translate('Privacy settings prevent you from editing this record.'), '</p>',
		'<p><a href="#" onclick="window.close();">', WT_I18N::translate('Close Window'), '</a></p>';
	exit;
}

if (!isset($type)) {
	$type='';
}
$level0type = $type;
if ($type=='INDI') {
	$record=WT_Person::getInstance($pid);
	echo '<h4>', $record->getFullName(), '</h4>';
} elseif ($type=='FAM') {
	if (!empty($pid)) {
		$record=WT_Family::getInstance($pid);
	} else {
		$record=WT_Family::getInstance($famid);
	}
	echo '<h4>', $record->getFullName(), '</h4>';
} elseif ($type=='SOUR') {
	$record=WT_Source::getInstance($pid);
	echo '<h4>', $record->getFullName(),  '</h4>';
}

if (strstr($action, 'addchild')) {
	if (empty($famid)) {
		echo '<h4>', WT_I18N::translate('Add an unlinked person'), '</h4>';
	} else {
		echo '<h4>', WT_I18N::translate('Add a new child'), '</h4>';
	}
} elseif (strstr($action, 'addspouse')) {
	if ($famtag=='WIFE') {
		echo '<h4>', WT_I18N::translate('Add a new wife'), '</h4>';
	} else {
		echo '<h4>', WT_I18N::translate('Add a new husband'), '</h4>';
	}
} elseif (strstr($action, 'addnewparent')) {
	if ($famtag=='WIFE') {
		echo '<h4>', WT_I18N::translate('Add a new mother'), '</h4>';
	} else {
		echo '<h4>', WT_I18N::translate('Add a new father'), '</h4>';
	}
} elseif (strstr($action, 'addopfchild')) {
	echo '<h4>', WT_I18N::translate('Add a child to create a one-parent family'), '</h4>', help_link('add_opf_child');
}
//------------------------------------------------------------------------------
switch ($action) {
case 'delete':
	if (!empty($linenum)) {
		if ($linenum===0) {
			delete_gedrec($pid, WT_GED_ID);
			$success=true;
		} else {
			// Retrieve the private data
			$tmp=new WT_GedcomRecord($gedrec);
			list($gedcom, $private_gedrec)=$tmp->privatizeGedcom(WT_USER_ACCESS_LEVEL);
			
			//-- when deleting a media link
			//-- $linenum comes is an OBJE and the $mediaid to delete should be set
			if ($linenum=='OBJE') {
				$newged = remove_subrecord($gedrec, $linenum, $_REQUEST['mediaid']);
			} else {
				$newged = remove_subline($gedrec, $linenum);
			}
			if (replace_gedrec($pid, WT_GED_ID, $newged.$private_gedrec, $update_CHAN)) {
				$success=true;
			}
		}
	}
	break;
//------------------------------------------------------------------------------
//-- echo a form to edit the raw gedcom record in a large textarea
case 'editraw':
	// Hide the private data
	$tmp=new WT_GedcomRecord($gedrec);
	list($gedrec)=$tmp->privatizeGedcom(WT_USER_ACCESS_LEVEL);

	echo '<h4>', WT_I18N::translate('Edit raw GEDCOM record'), '</h4>', help_link('edit_edit_raw');
	echo '<form method="post" action="edit_interface.php">';
	echo '<input type="hidden" name="action" value="updateraw">';
	echo '<input type="hidden" name="pid" value="', $pid, '">';
	echo '<input id="savebutton2" type="submit" value="', WT_I18N::translate('Save'), '"><br>';
	// Remove the first line of the gedrec - things go wrong when users
	// change either the TYPE or XREF
	// Notes are special - they may contain data on the first line
	$gedrec=preg_replace('/^(0 @'.WT_REGEX_XREF.'@ NOTE) (.+)/', "$1\n1 CONC $2", $gedrec);
	list($gedrec1, $gedrec2)=explode("\n", $gedrec, 2);
	echo '<textarea name="newgedrec1" id="newgedrec1" dir="ltr" readonly="readonly">', $gedrec1, '</textarea><br>';
	echo '<textarea name="newgedrec2" id="newgedrec2" dir="ltr">', htmlspecialchars($gedrec2), "</textarea><br>";
	if (WT_USER_IS_ADMIN) {
		echo '<table class="facts_table">';
		echo '<tr><td class="descriptionbox  wrap width25">';
		echo WT_Gedcom_Tag::getLabel('CHAN'), '</td><td class="optionbox wrap">';
		echo '<input type="checkbox" name="preserve_last_changed"';
		if ($NO_UPDATE_CHAN) {
			echo ' checked="checked"';
		} 
		echo '>';
		echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN');
		echo WT_Gedcom_Tag::getLabelValue('DATE', $tmp->LastChangeTimestamp());
		echo WT_Gedcom_Tag::getLabelValue('_WT_USER', $tmp->LastChangeUser());
		echo '</td></tr>';
		echo '</table>';
	}
	echo print_specialchar_link('newgedrec2');
	echo '<br>';
	echo '<input id="savebutton" type="submit" value="', WT_I18N::translate('Save'), '"><br>';
	echo '</form>';
	echo '<script>';
	echo "textbox = document.getElementById('newgedrec');";
	echo "savebutton = document.getElementById('savebutton');";
	echo 'if (textbox && savebutton) {';
	echo ' window.resizeTo(textbox.offsetLeft+textbox.offsetWidth+100, savebutton.offsetTop+savebutton.offsetHeight+150);';
	echo '}';
	echo '</script>';
	break;
//------------------------------------------------------------------------------
//-- edit a fact record in a form
case 'edit':
	// Hide the private data
	$tmp=new WT_GedcomRecord($gedrec);
	list($gedrec)=$tmp->privatizeGedcom(WT_USER_ACCESS_LEVEL);

	init_calendar_popup();
	echo '<form name="editform" method="post" action="edit_interface.php" enctype="multipart/form-data">';
	echo '<input type="hidden" name="action" value="update">';
	echo '<input type="hidden" name="linenum" value="', $linenum, '">';
	echo '<input type="hidden" name="pid" value="', $pid, '">';
	echo '<input type="hidden" id="pids_array_edit" name="pids_array_edit" value="no_array">';
	echo '<br><input type="submit" value="', WT_I18N::translate('Save'), '"><br>';

	echo "<table class=\"facts_table\">";
	$level1type = create_edit_form($gedrec, $linenum, $level0type);
	if (WT_USER_IS_ADMIN) {
		echo '<tr><td class="descriptionbox wrap width25">';
		echo WT_Gedcom_Tag::getLabel('CHAN'), '</td><td class="optionbox wrap">';
		echo '<input type="checkbox" name="preserve_last_changed"';
		if ($NO_UPDATE_CHAN) {
			echo ' checked="checked"';
		} 
		echo '>';
		echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN');
		echo WT_Gedcom_Tag::getLabelValue('DATE', $tmp->LastChangeTimestamp());
		echo WT_Gedcom_Tag::getLabelValue('_WT_USER', $tmp->LastChangeUser());
		echo '</td></tr>';
	}
	echo '</table>';
	switch ($level0type) {
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

	echo '<br><input type="submit" value="', WT_I18N::translate('Save'), '"><br>';
	echo '</form>';
	break;
//------------------------------------------------------------------------------
case 'add':
	//
	// Start of add section...
	//
	init_calendar_popup();
	echo '<form name="addform" method="post" action="edit_interface.php" enctype="multipart/form-data">';
	echo '<input type="hidden" name="action" value="update">';
	echo '<input type="hidden" name="linenum" value="new">';
	echo '<input type="hidden" name="pid" value="', $pid, '">';
	echo '<input type="hidden" id="pids_array_add" name="pids_array_add" value="no_array">';
	echo '<br><input type="submit" value="', WT_I18N::translate('Add'), '"><br>';
	echo '<table class="facts_table">';

	create_add_form($fact);

	if (WT_USER_IS_ADMIN) {
		echo '<tr><td class="descriptionbox wrap width25">';
		echo WT_Gedcom_Tag::getLabel('CHAN'), '</td><td class="optionbox wrap">';
		echo '<input type="checkbox" name="preserve_last_changed"';
		if ($NO_UPDATE_CHAN) {
			echo ' checked="checked"';
		} 
		echo '>';
		echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN');
		echo WT_Gedcom_Tag::getLabelValue('DATE', $tmp->LastChangeTimestamp());
		echo WT_Gedcom_Tag::getLabelValue('_WT_USER', $tmp->LastChangeUser());
		echo '</td></tr>';
	}
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

	echo '<br><input type="submit" value="', WT_I18N::translate('Add'), '"><br>';
	echo '</form>';
// }
	break;
//------------------------------------------------------------------------------
case 'addchild':
	print_indi_form('addchildaction', $famid, '', '', 'CHIL', $gender);
	break;
//------------------------------------------------------------------------------
case 'addspouse':
	print_indi_form('addspouseaction', $famid, '', '', $famtag);
	break;
//------------------------------------------------------------------------------
case 'addnewparent':
	print_indi_form('addnewparentaction', $famid, '', '', $famtag);
	break;
//------------------------------------------------------------------------------
case 'addopfchild':
	print_indi_form('addopfchildaction', $famid, '', '', 'CHIL');
	break;
//------------------------------------------------------------------------------
case 'addfamlink':
	echo '<form method="post" name="addchildform" action="edit_interface.php">';
	echo '<input type="hidden" name="action" value="linkfamaction">';
	echo '<input type="hidden" name="pid" value="', $pid, '">';
	echo '<input type="hidden" name="famtag" value="', $famtag, '">';
	echo '<table class="facts_table">';
	echo '<tr><td class="facts_label">', WT_I18N::translate('Family'), '</td>';
	echo '<td class="facts_value"><input type="text" id="famid" name="famid" size="8"> ';
	echo print_findfamily_link('famid');
	echo '</td></tr>';
	if ($famtag=='CHIL') {
		echo '<tr><td class="facts_label">', WT_Gedcom_Tag::getLabel('PEDI'), '</td><td class="facts_value">';
		switch (WT_Person::getInstance($pid)->getSex()) {
		case 'M': echo edit_field_pedi_m('PEDI'); break;
		case 'F': echo edit_field_pedi_f('PEDI'); break;
		case 'U': echo edit_field_pedi_u('PEDI'); break;
		}
		echo help_link('PEDI');
		echo '</td></tr>';
	}
	if (WT_USER_IS_ADMIN) {
		echo '<tr><td class="descriptionbox wrap width25">';
		echo WT_Gedcom_Tag::getLabel('CHAN'), '</td><td class="optionbox wrap">';
		echo '<input type="checkbox" name="preserve_last_changed"';
		if ($NO_UPDATE_CHAN) {
			echo ' checked="checked"';
		} 
		echo '>';
		echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN');
		echo WT_Gedcom_Tag::getLabelValue('DATE', $tmp->LastChangeTimestamp());
		echo WT_Gedcom_Tag::getLabelValue('_WT_USER', $tmp->LastChangeUser());
		echo '</td></tr>';
	}
	echo '</table>';
	echo '<input type="submit" value="', WT_I18N::translate('Set link'), '"><br>';
	echo '</form>';
	break;
//------------------------------------------------------------------------------
case 'linkspouse':
	init_calendar_popup();
	echo '<form method="post" name="addchildform" action="edit_interface.php">';
	echo '<input type="hidden" name="action" value="linkspouseaction">';
	echo '<input type="hidden" name="pid" value="', $pid, '">';
	echo '<input type="hidden" name="famid" value="new">';
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
	if (WT_USER_IS_ADMIN) {
		echo "<tr><td class=\"descriptionbox wrap width25\">";
		echo WT_Gedcom_Tag::getLabel('CHAN'), '</td><td class="optionbox wrap">';
		echo '<input type="checkbox" name="preserve_last_changed"';
		if ($NO_UPDATE_CHAN) {
			echo ' checked="checked"';
		} 
		echo '>';
		echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN');
		echo WT_Gedcom_Tag::getLabelValue('DATE', $tmp->LastChangeTimestamp());
		echo WT_Gedcom_Tag::getLabelValue('_WT_USER', $tmp->LastChangeUser());
		echo '</td></tr>';
	}
	echo '</table>';
	print_add_layer("SOUR");
	print_add_layer("OBJE");
	print_add_layer("NOTE");
	print_add_layer("SHARED_NOTE");
	print_add_layer("ASSO");
	// allow to add godfather and godmother for CHR fact or best man and bridesmaid  for MARR fact in one window
	print_add_layer("ASSO2");
	print_add_layer("RESN");
	echo '<input type="submit" value="', WT_I18N::translate('Set link'), '"><br>';
	echo '</form>';
	break;
//------------------------------------------------------------------------------
case 'linkfamaction':
	// Make sure we have the right ID (f123 vs. F123)
	$famid=WT_Family::getInstance($famid)->getXref();
	$famrec = find_gedcom_record($famid, WT_GED_ID, true);
	if (!empty($famrec)) {
		$itag = "FAMC";
		if ($famtag=="HUSB" || $famtag=="WIFE") $itag="FAMS";

		//-- update the individual record for the person
		if (strpos($gedrec, "1 $itag @$famid@")===false) {
			switch ($itag) {
			case 'FAMC':
				if (isset($_REQUEST['PEDI'])) {
					$PEDI = $_REQUEST['PEDI'];
				} else {
					$PEDI='';
				}
				$gedrec.="\n".WT_Gedcom_Code_Pedi::createNewFamcPedi($PEDI, $famid);
				break;
			case 'FAMS':
				$gedrec.="\n1 FAMS @$famid@";
				break;
			}
			if (replace_gedrec($pid, WT_GED_ID, $gedrec, $update_CHAN)) {
				$success=true;
			}
		}

		//-- if it is adding a new child to a family
		if ($famtag=="CHIL") {
			if (strpos($famrec, "1 $famtag @$pid@")===false) {
				$famrec .= "\n1 $famtag @$pid@";
				if (replace_gedrec($famid, WT_GED_ID, $famrec, $update_CHAN)) {
					$success=true;
				}
			}
		} else {
			//-- if it is adding a husband or wife
			//-- check if the family already has a HUSB or WIFE
			$ct = preg_match("/1 $famtag @(.*)@/", $famrec, $match);
			if ($ct>0) {
				//-- get the old ID
				$spid = trim($match[1]);
				//-- only continue if the old husb/wife is not the same as the current one
				if ($spid!=$pid) {
					//-- change a of the old ids to the new id
					$famrec = str_replace("\n1 $famtag @$spid@", "\n1 $famtag @$pid@", $famrec);
					if (replace_gedrec($famid, WT_GED_ID, $famrec, $update_CHAN)) {
						$success=true;
					}
					//-- remove the FAMS reference from the old husb/wife
					if (!empty($spid)) {
						$srec = find_gedcom_record($spid, WT_GED_ID, true);
						if ($srec) {
							$srec = str_replace("\n1 $itag @$famid@", "", $srec);
							if (replace_gedrec($spid, WT_GED_ID, $srec, $update_CHAN)) {
								$success=true;
							}
						}
					}
				}
			} else {
				$famrec .= "\n1 $famtag @$pid@";
				if (replace_gedrec($famid, WT_GED_ID, $famrec, $update_CHAN)) {
					$success=true;
				}
			}
		}
	} else {
		echo "Family record not found";
	}
	break;
//------------------------------------------------------------------------------
//-- add new source
case 'addnewsource':
	echo '<script>';
	?>
		function check_form(frm) {
			if (frm.TITL.value=="") {
				alert('<?php echo WT_I18N::translate('You must provide a source title'); ?>');
				frm.TITL.focus();
				return false;
			}
			return true;
		}
	<?php
	echo '</script>';
	?>
	<b><?php echo WT_I18N::translate('Create a new source'); ?></b>
	<form method="post" action="edit_interface.php" onsubmit="return check_form(this);">
		<input type="hidden" name="action" value="addsourceaction">
		<input type="hidden" name="pid" value="newsour">
		<table class="facts_table">
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('ABBR'), help_link('ABBR'); ?></td>
			<td class="optionbox wrap"><input type="text" name="ABBR" id="ABBR" value="" size="40" maxlength="255"> <?php echo print_specialchar_link('ABBR'); ?></td></tr>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('TITL'), help_link('TITL'); ?></td>
			<td class="optionbox wrap"><input type="text" name="TITL" id="TITL" value="" size="60"> <?php echo print_specialchar_link('TITL'); ?></td></tr>
			<?php if (strstr($ADVANCED_NAME_FACTS, "_HEB")!==false) { ?>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('_HEB'), help_link('_HEB'); ?></td>
			<td class="optionbox wrap"><input type="text" name="_HEB" id="_HEB" value="" size="60"> <?php echo print_specialchar_link('_HEB'); ?></td></tr>
			<?php } ?>
			<?php if (strstr($ADVANCED_NAME_FACTS, "ROMN")!==false) { ?>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('ROMN'), help_link('ROMN'); ?></td>
			<td class="optionbox wrap"><input  type="text" name="ROMN" id="ROMN" value="" size="60"> <?php echo print_specialchar_link('ROMN'); ?></td></tr>
			<?php } ?>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('AUTH'), help_link('AUTH'); ?></td>
			<td class="optionbox wrap"><input type="text" name="AUTH" id="AUTH" value="" size="40" maxlength="255"> <?php echo print_specialchar_link('AUTH'); ?></td></tr>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('PUBL'), help_link('PUBL'); ?></td>
			<td class="optionbox wrap"><textarea name="PUBL" id="PUBL" rows="5" cols="60"></textarea><br><?php echo print_specialchar_link('PUBL'); ?></td></tr>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('REPO'), help_link('REPO'); ?></td>
			<td class="optionbox wrap"><input type="text" name="REPO" id="REPO" value="" size="10"> <?php echo print_findrepository_link('REPO'), ' ', print_addnewrepository_link('REPO'); ?></td></tr>
			<tr><td class="descriptionbox wrap width25"><?php echo WT_Gedcom_Tag::getLabel('CALN'), help_link('CALN'); ?></td>
			<td class="optionbox wrap"><input type="text" name="CALN" id="CALN" value=""></td></tr>
		<?php
			if (WT_USER_IS_ADMIN) {
				echo '<tr><td class="descriptionbox wrap width25">';
				echo WT_Gedcom_Tag::getLabel('CHAN'), '</td><td class="optionbox wrap">';
				echo '<input type="checkbox" name="preserve_last_changed"';
				if ($NO_UPDATE_CHAN) {
					echo ' checked="checked"';
				} 
				echo '>';
				echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN');
				echo '</td></tr>';
			}
		?>
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
		<br><br>
		<input type="submit" value="<?php echo WT_I18N::translate('Create a new source'); ?>">
	</form>
	<?php
	break;
//------------------------------------------------------------------------------
//-- create a source record from the incoming variables
case 'addsourceaction':
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
	$xref = append_gedrec($newgedrec, WT_GED_ID);
	if ($xref) {
		$controller->addInlineJavascript('openerpasteid("' . $xref . '");');
	}
	break;
//------------------------------------------------------------------------------
//-- add new Shared Note
case 'addnewnote':
	?>
	<b><?php echo WT_I18N::translate('Create a new Shared Note'); ?></b>
	<form method="post" action="edit_interface.php" onsubmit="return check_form(this);">
		<input type="hidden" name="action" value="addnoteaction">
		<input type="hidden" name="noteid" value="newnote">
		<!-- <input type="hidden" name="pid" value="$pid"> -->
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
			if (WT_USER_IS_ADMIN) {
				echo '<tr><td class="descriptionbox wrap width25">';
				echo WT_Gedcom_Tag::getLabel('CHAN'), '</td><td class="optionbox wrap">';
				echo '<input type="checkbox" name="preserve_last_changed"';
				if ($NO_UPDATE_CHAN) {
					echo ' checked="checked"';
				} 
				echo '>';
				echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN');
				echo "</td></tr>";
			}
			echo '</table>';
			echo '<br><br>';
			echo '<input type="submit" value="', WT_I18N::translate('Save'), '">';
		?>
	</form>
	<?php
	break;
//------------------------------------------------------------------------------
//-- create a shared note record from the incoming variables
case 'addnoteaction':
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

	$xref = append_gedrec($newgedrec, WT_GED_ID);
	if ($xref) {
		$controller->addInlineJavascript('openerpasteid("' . $xref . '");');
	}
	break;
//------------------------------------------------------------------------------
//-- add new Shared Note census event using GEDFact assistant
case 'addnewnote_assisted':
	if (isset($_REQUEST['pid'])) $pid = $_REQUEST['pid'];
	global $pid;

	?>
	<div class="center font11" style="width:100%;">
		<b><?php echo WT_I18N::translate('Create a new Shared Note using Assistant'); ?></b>
		<?php
			// When more languages are added to the wiki, we can expand or redesign this
			switch (WT_LOCALE) {
			case 'fr':
				echo wiki_help_link('fr:Module_Assistant_Recensement');
				break;
			case 'en':
			default:
				echo wiki_help_link('Census_Assistant_module');
				break;
			}
		?>
		<form method="post" action="edit_interface.php" onsubmit="return check_form(this);">
			<input type="hidden" name="action" value="addnoteaction_assisted">
			<input type="hidden" name="noteid" value="newnote">
			<input id="pid_array" type="hidden" name="pid_array" value="none">
			<input id="pid" type="hidden" name="pid" value=<?php echo $pid; ?>>
			<?php
				require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/CENS_ctrl.php';
			?>
		</form>
	</div>
	<div style="clear:both;"></div>
	<?php
	break;
//------------------------------------------------------------------------------
//-- create a shared note assisted record from the incoming variables
case 'addnoteaction_assisted':
	require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/_CENS/addnoteaction_assisted.php';
	break;

//-- add new Media Links
case 'addmedia_links':
	global $pid;
	?>
	<!-- <form method="post" action="edit_interface.php" onsubmit="return check_form(this);"> -->
	<form method="post" action="edit_interface.php?pid=<?php echo $pid; ?>" onsubmit="findindi()">
		<input type="hidden" name="action" value="addmedia_links">
		<input type="hidden" name="noteid" value="newnote">
		<?php
		require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/MEDIA_ctrl.php';
		?>
	</form>
	<?php
	break;

//-- edit source
case 'editsource':
	// Hide the private data
	$tmp=new WT_GedcomRecord($gedrec);
	list($gedrec)=$tmp->privatizeGedcom(WT_USER_ACCESS_LEVEL);
	
	init_calendar_popup();
	echo '<form method="post" action="edit_interface.php" enctype="multipart/form-data">';
	echo '<input type="hidden" name="action" value="update">';
	echo '<input type="hidden" name="pid" value="', $pid, '">';
	echo '<br><input type="submit" value="', WT_I18N::translate('Save'), '"><br>';

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
			$level1type = create_edit_form($gedrec, $i, $level0type);
			echo '<input type="hidden" name="linenum[]" value="', $i, '">';
			$usedfacts[]=$fields[1];
			foreach ($uniquefacts as $key=>$fact) {
				if ($fact==$fields[1]) unset($uniquefacts[$key]);
			}
		}
	}
	foreach ($uniquefacts as $key=>$fact) {
		$gedrec.="\n1 ".$fact;
		$level1type = create_edit_form($gedrec, $lines++, $level0type);
		echo '<input type="hidden" name="linenum[]" value="', $i, '">';
	}

	if (WT_USER_IS_ADMIN) {
		echo '<tr><td class="descriptionbox wrap width25">';
		echo WT_Gedcom_Tag::getLabel('CHAN'), '</td><td class="optionbox wrap">';
		echo '<input type="checkbox" name="preserve_last_changed"';
		if ($NO_UPDATE_CHAN) {
			echo ' checked="checked"';
		} 
		echo '>';
		echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN');
		echo WT_Gedcom_Tag::getLabelValue('DATE', $tmp->LastChangeTimestamp());
		echo WT_Gedcom_Tag::getLabelValue('_WT_USER', $tmp->LastChangeUser());
		echo '</td></tr>';
	}
	echo '</table>';
	print_add_layer("OBJE");
	print_add_layer("NOTE");
	print_add_layer("SHARED_NOTE");
	print_add_layer("RESN");
	echo '<br><input type="submit" value="', WT_I18N::translate('Save'), '"><br>';
	echo '</form>';
	break;
//------------------------------------------------------------------------------
//-- edit a Shared Note
case 'editnote':
	// Hide the private data
	$tmp=new WT_GedcomRecord($gedrec);
	list($gedrec)=$tmp->privatizeGedcom(WT_USER_ACCESS_LEVEL);
	
	?>
	<b><?php echo WT_I18N::translate('Edit Shared Note'), "&nbsp;&nbsp;(" . $pid . ")"; ?></b><br><br>
	<form method="post" action="edit_interface.php" >
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="pid" value="<?php echo $pid; ?>">

		<?php
		if (preg_match("/^0 @$pid@ NOTE ?(.*)/", $gedrec, $n1match)) {
			$note_content=$n1match[1].get_cont(1, $gedrec, false);
			
			$num_note_lines=0;
			foreach (preg_split("/\r?\n/", $note_content, -1 ) as $j=>$line) {
				$num_note_lines++;
			}
	
		} else {
			$note_content='';
		}
		?>
		<table class="facts_table">
			<tr>
				<td class="descriptionbox wrap width25"><?php echo WT_I18N::translate('Shared note'), help_link('SHARED_NOTE'); ?></td>
				<td class="optionbox wrap">
					<textarea name="NOTE" id="NOTE" rows="15" cols="90"><?php
						echo htmlspecialchars($note_content);
					?></textarea><br><?php echo print_specialchar_link('NOTE'); ?>
				</td>
			</tr>
			<?php
				if (WT_USER_IS_ADMIN) {
					echo '<tr><td class="descriptionbox wrap width25">';
					echo WT_Gedcom_Tag::getLabel('CHAN'), '</td><td class="optionbox wrap">';
					echo '<input type="checkbox" name="preserve_last_changed"';
					if ($NO_UPDATE_CHAN) {
						echo ' checked="checked"';
					} 
					echo '>';
					echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN');
					echo WT_Gedcom_Tag::getLabelValue('DATE', $tmp->LastChangeTimestamp());
					echo WT_Gedcom_Tag::getLabelValue('_WT_USER', $tmp->LastChangeUser());
					echo '</td></tr>';
				}
			?>
		</table>
		<br><br>
		<input type="hidden" name="num_note_lines" value="<?php echo $num_note_lines; ?>">
		<input type="submit" value="<?php echo WT_I18N::translate('Save'); ?>">
	</form>
	<?php
	break;
//------------------------------------------------------------------------------
//-- add new repository
case 'addnewrepository':
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
	<b><?php echo WT_I18N::translate('Create Repository');
	?></b>
	<form method="post" action="edit_interface.php" onsubmit="return check_form(this);">
		<input type="hidden" name="action" value="addrepoaction">
		<input type="hidden" name="pid" value="newrepo">
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
		<?php
			if (WT_USER_IS_ADMIN) {
				echo '<tr><td class="descriptionbox wrap width25">';
				echo WT_Gedcom_Tag::getLabel('CHAN'), '</td><td class="optionbox wrap">';
				echo '<input type="checkbox" name="preserve_last_changed"';
				if ($NO_UPDATE_CHAN) {
					echo ' checked="checked"';
				} 
				echo '>';
				echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN');
				echo '</td></tr>';
			}
		?>
		</table>
		<input type="submit" value="<?php echo WT_I18N::translate('Create Repository'); ?>">
	</form>
	<?php
	break;
//------------------------------------------------------------------------------
//-- create a repository record from the incoming variables
case 'addrepoaction':
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

	$xref = append_gedrec($newgedrec, WT_GED_ID);
	if ($xref) {
		$controller->addInlineJavascript('openerpasteid("' . $xref . '");');
	}
	break;
//------------------------------------------------------------------------------
//-- get the new incoming raw gedcom record and store it in the file
case 'updateraw':
	// Retrieve the private data
	$tmp=new WT_GedcomRecord($gedrec);
	list(, $private_gedrec)=$tmp->privatizeGedcom(WT_USER_ACCESS_LEVEL);
			
	if (isset($_POST['newgedrec1']) && isset($_POST['newgedrec2'])) {
		$newgedrec = $_POST['newgedrec1']."\n".$_POST['newgedrec2'];
		if (replace_gedrec($pid, WT_GED_ID, $newgedrec.$private_gedrec, $update_CHAN)) {
			$success=true;
		}
	}
	break;

//----------------------------------------------------------------------------------
//-- reconstruct the gedcom from the incoming fields and store it in the file
case 'update':
	/* -----------------------------------------------------------------------------
	 * $pids_array is a text file passed via js from the CENS GEDFact Assistant
	 * to the hidden field id=\"pids_array\" in the case 'add'.
	 * The subsequent array ($cens_pids), after exploding this text file,
	 * is an array of indi id's within the Census Transcription
	 * If $cens_pids is set, then this allows the array to "copy" the new CENS event
	 * using the foreach loop to these id's
	 * If $cens_pids is not set, then the array created is just the current $pid.
	 * -----------------------------------------------------------------------------
	 */
	if (isset($_REQUEST['pids_array_add'])) $pids_array = $_REQUEST['pids_array_add'];
	if (isset($_REQUEST['pids_array_edit'])) $pids_array = $_REQUEST['pids_array_edit'];
	if (isset($_REQUEST['num_note_lines'])) $num_note_lines = $_REQUEST['num_note_lines'];

	if (isset($pids_array) && $pids_array!="no_array") {
		$cens_pids=explode(', ', $pids_array);
	}

	if (!isset($cens_pids)) {
		$cens_pids = array($pid);
		$idnums="";
	} else {
		$cens_pids = $cens_pids;
		$idnums="multi";
	}

	// Cycle through each individual concerned defined by $cens_pids array.
	foreach ($cens_pids as $pid) {
		if (isset($pid)) {
			$gedrec = find_gedcom_record($pid, WT_GED_ID, true);
		} elseif (isset($famid)) {
			$gedrec = find_gedcom_record($famid, WT_GED_ID, true);
		}

		// Retrieve the private data
		$tmp=new WT_GedcomRecord($gedrec);
		list($gedrec, $private_gedrec)=$tmp->privatizeGedcom(WT_USER_ACCESS_LEVEL);
			
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

		$gedlines = explode("\n", trim($gedrec));
		//-- for new facts set linenum to number of lines
		if (!is_array($linenum)) {
			if ($linenum=="new" || $idnums=="multi") {
				$linenum = count($gedlines);
			}
			$newged = "";
			for ($i=0; $i<$linenum; $i++) {
				$newged .= $gedlines[$i]."\n";
			}
			//-- for edits get the level from the line
			if (isset($gedlines[$linenum])) {
				$fields = explode(' ', $gedlines[$linenum]);
				$glevel = $fields[0];
				$i++;
				while (($i<count($gedlines))&&($gedlines[$i]{0}>$glevel)) {
					$i++;				
				}
			}

			if (!isset($glevels)) $glevels = array();
			if (isset($_REQUEST['NAME'])) $NAME = $_REQUEST['NAME'];
			if (isset($_REQUEST['TYPE'])) $TYPE = $_REQUEST['TYPE'];
			if (isset($_REQUEST['NPFX'])) $NPFX = $_REQUEST['NPFX'];
			if (isset($_REQUEST['GIVN'])) $GIVN = $_REQUEST['GIVN'];
			if (isset($_REQUEST['NICK'])) $NICK = $_REQUEST['NICK'];
			if (isset($_REQUEST['SPFX'])) $SPFX = $_REQUEST['SPFX'];
			if (isset($_REQUEST['SURN'])) $SURN = $_REQUEST['SURN'];
			if (isset($_REQUEST['NSFX'])) $NSFX = $_REQUEST['NSFX'];
			if (isset($_REQUEST['ROMN'])) $ROMN = $_REQUEST['ROMN'];
			if (isset($_REQUEST['FONE'])) $FONE = $_REQUEST['FONE'];
			if (isset($_REQUEST['_HEB'])) $_HEB = $_REQUEST['_HEB'];
			if (isset($_REQUEST['_AKA'])) $_AKA = $_REQUEST['_AKA'];
			if (isset($_REQUEST['_MARNM'])) $_MARNM = $_REQUEST['_MARNM'];
			if (isset($_REQUEST['NOTE'])) $NOTE = $_REQUEST['NOTE'];

			if (!empty($NAME)) $newged .= "\n1 NAME $NAME";
			if (!empty($TYPE)) $newged .= "\n2 TYPE $TYPE";
			if (!empty($NPFX)) $newged .= "\n2 NPFX $NPFX";
			if (!empty($GIVN)) $newged .= "\n2 GIVN $GIVN";
			if (!empty($NICK)) $newged .= "\n2 NICK $NICK";
			if (!empty($SPFX)) $newged .= "\n2 SPFX $SPFX";
			if (!empty($SURN)) $newged .= "\n2 SURN $SURN";
			if (!empty($NSFX)) $newged .= "\n2 NSFX $NSFX";
			
			if (!empty($NOTE)) {			
				$cmpfunc = create_function('$e', 'return strpos($e,"0 @N") !==0 && strpos($e,"1 CONT") !==0;');
				$gedlines = array_filter($gedlines, $cmpfunc);
				$tempnote = preg_split('/\r?\n/', trim($NOTE) . "\n"); // make sure only one line ending on the end
				$title[] = "0 @$pid@ NOTE " . array_shift($tempnote);
				foreach($tempnote as &$line) {
    				$line = trim("1 CONT " . $line,' ');
				}
				$gedlines = array_merge($title,$tempnote,$gedlines);	
			}

			//-- Refer to Bug [ 1329644 ] Add Married Name - Wrong Sequence
			//-- _HEB/ROMN/FONE have to be before _AKA, even if _AKA exists in input and the others are now added
			if (!empty($ROMN)) $newged .= "\n2 ROMN $ROMN";
			if (!empty($FONE)) $newged .= "\n2 FONE $FONE";
			if (!empty($_HEB)) $newged .= "\n2 _HEB $_HEB";

			$newged = handle_updates($newged);

			if (!empty($_AKA)) $newged .= "\n2 _AKA $_AKA";
			if (!empty($_MARNM)) $newged .= "\n2 _MARNM $_MARNM";

			while ($i<count($gedlines)) {
				$newged .= "\n".$gedlines[$i];
				$i++;
			}
		} else {
			$newged = "";
			$current = 0;
			foreach ($linenum as $editline) {
				for ($i=$current; $i<$editline; $i++) {
					$newged .= "\n".$gedlines[$i];
				}
				//-- for edits get the level from the line
				if (isset($gedlines[$editline])) {
					$fields = explode(' ', $gedlines[$editline]);
					$glevel = $fields[0];
					$i++;
					while (($i<count($gedlines))&&($gedlines[$i]{0}>$glevel)) $i++;
				}

				if (!isset($glevels)) $glevels = array();
				if (isset($_REQUEST['NAME'])) $NAME = $_REQUEST['NAME'];
				if (isset($_REQUEST['TYPE'])) $TYPE = $_REQUEST['TYPE'];
				if (isset($_REQUEST['NPFX'])) $NPFX = $_REQUEST['NPFX'];
				if (isset($_REQUEST['GIVN'])) $GIVN = $_REQUEST['GIVN'];
				if (isset($_REQUEST['NICK'])) $NICK = $_REQUEST['NICK'];
				if (isset($_REQUEST['SPFX'])) $SPFX = $_REQUEST['SPFX'];
				if (isset($_REQUEST['SURN'])) $SURN = $_REQUEST['SURN'];
				if (isset($_REQUEST['NSFX'])) $NSFX = $_REQUEST['NSFX'];
				if (isset($_REQUEST['ROMN'])) $ROMN = $_REQUEST['ROMN'];
				if (isset($_REQUEST['FONE'])) $FONE = $_REQUEST['FONE'];
				if (isset($_REQUEST['_HEB'])) $_HEB = $_REQUEST['_HEB'];
				if (isset($_REQUEST['_AKA'])) $_AKA = $_REQUEST['_AKA'];
				if (isset($_REQUEST['_MARNM'])) $_MARNM = $_REQUEST['_MARNM'];
				if (isset($_REQUEST['NOTE'])) $NOTE = $_REQUEST['NOTE'];

				if (!empty($NAME)) $newged .= "\n1 NAME $NAME";
				if (!empty($TYPE)) $newged .= "\n2 TYPE $TYPE";
				if (!empty($NPFX)) $newged .= "\n2 NPFX $NPFX";
				if (!empty($GIVN)) $newged .= "\n2 GIVN $GIVN";
				if (!empty($NICK)) $newged .= "\n2 NICK $NICK";
				if (!empty($SPFX)) $newged .= "\n2 SPFX $SPFX";
				if (!empty($SURN)) $newged .= "\n2 SURN $SURN";
				if (!empty($NSFX)) $newged .= "\n2 NSFX $NSFX";
					
				if (!empty($NOTE)) {				
					$cmpfunc = create_function('$e', 'return strpos($e,"0 @N") !==0 && strpos($e,"1 CONT") !==0;');
					$gedlines = array_filter($gedlines, $cmpfunc);
					$tempnote = preg_split('/\r?\n/', trim($NOTE) . "\n"); // make sure only one line ending on the end
					$title[] = "0 @$pid@ NOTE " . array_shift($tempnote);
					foreach($tempnote as &$line) {
    					$line = trim("1 CONT " . $line,' ');
					}
					$gedlines = array_merge($title,$tempnote,$gedlines);	
				}
				
				//-- Refer to Bug [ 1329644 ] Add Married Name - Wrong Sequence
				//-- _HEB/ROMN/FONE have to be before _AKA, even if _AKA exists in input and the others are now added
				if (!empty($ROMN)) $newged .= "\n2 ROMN $ROMN";
				if (!empty($FONE)) $newged .= "\n2 FONE $FONE";
				if (!empty($_HEB)) $newged .= "\n2 _HEB $_HEB";

				if (!empty($_AKA)) $newged .= "\n2 _AKA $_AKA";
				if (!empty($_MARNM)) $newged .= "\n2 _MARNM $_MARNM";

				$newged = handle_updates($newged);
				$current = $editline;
				break;
			}

		}
		if (replace_gedrec($pid, WT_GED_ID, $newged.$private_gedrec, $update_CHAN)) {
			$success=true;
		}
	} // end foreach $cens_pids  -------------
	break;

//------------------------------------------------------------------------------
case 'addchildaction':
	splitSOUR(); // separate SOUR record from the rest

	$gedrec ="0 @REF@ INDI";
	$gedrec.=addNewName();
	$gedrec.=addNewSex ();
	if (preg_match_all('/([A-Z0-9_]+)/', $QUICK_REQUIRED_FACTS, $matches)) {
		foreach ($matches[1] as $match) {
			$gedrec.=addNewFact($match);
		}
	}

	if (!empty($famid)) {
		if (isset($_REQUEST['PEDI'])) {
			$PEDI = $_REQUEST['PEDI'];
		} else {
			$PEDI='';
		}
		$gedrec.="\n".WT_Gedcom_Code_Pedi::createNewFamcPedi($PEDI, $famid);
	}

	if (safe_POST_bool('SOUR_INDI')) {
		$gedrec = handle_updates($gedrec);
	} else {
		$gedrec = updateRest($gedrec);
	}

	$xref = append_gedrec($gedrec, WT_GED_ID);
	$link = "individual.php?pid=$xref";
	if ($xref) {
		$gedrec = "";
		if (!empty($famid)) {
			// Insert new child at the right place [ 1686246 ]
			$newchild = WT_Person::getInstance($xref);
			$gedrec=find_gedcom_record($famid, WT_GED_ID, true);
			$family=new WT_Family($gedrec);
			$done = false;
			foreach ($family->getChildren() as $key=>$child) {
				if (WT_Date::Compare($newchild->getEstimatedBirthDate(), $child->getEstimatedBirthDate())<0) {
					// new child is older : insert before
					$gedrec = str_replace("1 CHIL @".$child->getXref()."@",
																"1 CHIL @$xref@\n1 CHIL @".$child->getXref()."@",
																$gedrec);
					$done = true;
					break;
				}
			}
			// new child is the only one
			if (count($family->getChildren())<1) {
				$gedrec .= "\n1 CHIL @$xref@";
			} elseif (!$done) {
				// new child is the youngest or undated : insert after
				$gedrec = str_replace("\n1 CHIL @".$child->getXref()."@",
															"\n1 CHIL @".$child->getXref()."@\n1 CHIL @$xref@",
															$gedrec);
			}
			if (replace_gedrec($famid, WT_GED_ID, $gedrec, $update_CHAN)) {
				$success=true;
			}
		}
	}
	break;
//------------------------------------------------------------------------------
case 'addspouseaction':
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

	$xref = append_gedrec($gedrec, WT_GED_ID);
	$link = "individual.php?pid=$xref";
	$success = true;
	if ($famid=="new") {
		$famrec = "0 @new@ FAM";
		$SEX=safe_POST('SEX', '[MF]', 'U');
		if ($SEX=="M") $famtag = "HUSB";
		if ($SEX=="F") $famtag = "WIFE";
		if ($famtag=="HUSB") {
			$famrec .= "\n1 HUSB @$xref@";
			$famrec .= "\n1 WIFE @$pid@";
		} else {
			$famrec .= "\n1 WIFE @$xref@";
			$famrec .= "\n1 HUSB @$pid@";
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

		$famid = append_gedrec($famrec, WT_GED_ID);
	} elseif (!empty($famid)) {
		$famrec = find_gedcom_record($famid, WT_GED_ID, true);
		if (!empty($famrec)) {
			$famrec .= "\n1 $famtag @$xref@";

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

			if (replace_gedrec($famid, WT_GED_ID, $famrec, $update_CHAN)) {
				$success=true;
			}
		}
	}
	if ((!empty($famid))&&($famid!="new")) {
		$gedrec = find_gedcom_record($xref, WT_GED_ID, true) . "\n1 FAMS @$famid@";
		if (replace_gedrec($xref, WT_GED_ID, $gedrec, $update_CHAN)) {
			$success=true;
		}
	}
	if (!empty($pid)) {
		$indirec = find_gedcom_record($pid, WT_GED_ID, true);
		if ($indirec) {
			$indirec .= "\n1 FAMS @$famid@";
			if (replace_gedrec($pid, WT_GED_ID, $indirec, $update_CHAN)) {
				$success=true;
			}
		}
	}
	break;
//------------------------------------------------------------------------------
case 'linkspouseaction':
	splitSOUR(); // separate SOUR record from the rest

	if (isset($_REQUEST['spid'])) $spid = $_REQUEST['spid'];
	if (!empty($spid)) {
		$gedrec = find_gedcom_record($spid, WT_GED_ID, true);
		if ($gedrec) {
			if ($famid=="new") {
				$famrec = "0 @new@ FAM";
				$SEX = get_gedcom_value("SEX", 1, $gedrec);
				if ($SEX=="M") $famtag = "HUSB";
				if ($SEX=="F") $famtag = "WIFE";
				if ($famtag=="HUSB") {
					$famrec .= "\n1 HUSB @$spid@";
					$famrec .= "\n1 WIFE @$pid@";
				} else {
					$famrec .= "\n1 WIFE @$spid@";
					$famrec .= "\n1 HUSB @$pid@";
				}
				$famrec.=addNewFact('MARR');

				if (safe_POST_bool('SOUR_FAM') || count($tagSOUR)>0) {
					// before adding 2 SOUR it needs to add 1 MARR Y first
					if (addNewFact('MARR')=='') {
						$famrec .= "\n1 MARR Y";
					}
					$famrec = handle_updates($famrec);
				} else {
					// before adding level 2 facts it needs to add 1 MARR Y first
					if (addNewFact('MARR')=='') {
						$famrec .= "\n1 MARR Y";
					}
					$famrec = updateRest($famrec);
				}

				$famid = append_gedrec($famrec, WT_GED_ID);
			}
			if ((!empty($famid))&&($famid!="new")) {
				$gedrec .= "\n1 FAMS @$famid@";
				if (replace_gedrec($spid, WT_GED_ID, $gedrec, $update_CHAN)) {
					$success=true;
				}
				
			}
			if (!empty($pid)) {
				$indirec = find_gedcom_record($pid, WT_GED_ID, true);
				if (!empty($indirec)) {
					$indirec = trim($indirec) . "\n1 FAMS @$famid@";
					if (replace_gedrec($pid, WT_GED_ID, $indirec, $update_CHAN)) {
						$success=true;
					}
				}
			}
		}
	}
	break;
//------------------------------------------------------------------------------
case 'addnewparentaction':
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

	$xref = append_gedrec($gedrec, WT_GED_ID);
	$link = "individual.php?pid=$xref";
	$success = true;
	if ($famid=="new") {
		$famrec = "0 @new@ FAM";
		if ($famtag=="HUSB") {
			$famrec .= "\n1 HUSB @$xref@";
			$famrec .= "\n1 CHIL @$pid@";
		} else {
			$famrec .= "\n1 WIFE @$xref@";
			$famrec .= "\n1 CHIL @$pid@";
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

		$famid = append_gedrec($famrec, WT_GED_ID);
	} elseif (!empty($famid)) {
		$famrec = find_gedcom_record($famid, WT_GED_ID, true);
		if (!empty($famrec)) {
			$famrec .= "\n1 $famtag @$xref@";
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
			if (replace_gedrec($famid, WT_GED_ID, $famrec, $update_CHAN)) {
				$success=true;
			}
		}
	}
	if (!empty($famid) && $famid!="new") {
		$gedrec = find_gedcom_record($xref, WT_GED_ID, true);
		$gedrec.= "\n1 FAMS @$famid@";
		if (replace_gedrec($xref, WT_GED_ID, $gedrec, $update_CHAN)) {
			$success=true;
		}
	}
	if (!empty($pid)) {
		$indirec = find_gedcom_record($pid, WT_GED_ID, true);
		if ($indirec) {
			if (strpos($indirec, "1 FAMC @$famid@")===false) {
				$indirec .= "\n1 FAMC @$famid@";
				if (replace_gedrec($pid, WT_GED_ID, $indirec, $update_CHAN)) {
					$success=true;
				}
			}
		}
	}
	break;
//------------------------------------------------------------------------------
case 'addopfchildaction':
	splitSOUR(); // separate SOUR record from the rest

	$newindixref=get_new_xref('INDI');
	$newfamxref=get_new_xref('FAM');

	$gedrec ="0 @{$newindixref}@ INDI".addNewName().addNewSex ();
	if (preg_match_all('/([A-Z0-9_]+)/', $QUICK_REQUIRED_FACTS, $matches)) {
		foreach ($matches[1] as $match) {
			$gedrec.=addNewFact($match);
		}
	}

	if (isset($_REQUEST['PEDI'])) {
		$PEDI = $_REQUEST['PEDI'];
	} else {
		$PEDI='';
	}
	$gedrec.="\n".WT_Gedcom_Code_Pedi::createNewFamcPedi($PEDI, $newfamxref);

	if (safe_POST_bool('SOUR_INDI')) {
		$gedrec=handle_updates($gedrec);
	} else {
		$gedrec=updateRest($gedrec);
	}

	$famrec="0 @$newfamxref@ FAM\n1 CHIL @{$newindixref}@";
	$person=WT_Person::getInstance($pid);
	if ($person->getSex()=='F') {
		$famrec.="\n1 WIFE @{$pid}@";
	} else {
		$famrec.="\n1 HUSB @{$pid}@";
	}

	$indirec=find_gedcom_record($pid, WT_GED_ID, true);
	if ($indirec) {
		$indirec.="\n1 FAMS @{$newfamxref}@";
		if (replace_gedrec($pid, WT_GED_ID, $indirec, $update_CHAN)) {
			$success=true;
		}
		append_gedrec($gedrec, WT_GED_ID);
		append_gedrec($famrec, WT_GED_ID);
	}
	break;
//------------------------------------------------------------------------------
case 'editname':
	// Hide the private data
	$tmp=new WT_GedcomRecord($gedrec);
	list($gedrec)=$tmp->privatizeGedcom(WT_USER_ACCESS_LEVEL);
	$person=WT_Person::getInstance($pid);
	$gedlines = explode("\n", trim($gedrec));
	$fields = explode(' ', $gedlines[$linenum]);
	$glevel = $fields[0];
	$i = $linenum+1;
	$namerec = $gedlines[$linenum];
	while (($i<count($gedlines))&&($gedlines[$i]{0}>$glevel)) {
		$namerec.="\n".$gedlines[$i];
		$i++;
	}
	print_indi_form('update', '', $linenum, $namerec, '', $person->getSex());
	break;
//------------------------------------------------------------------------------
case 'addname':
	$person=WT_Person::getInstance($pid);
	print_indi_form('update', '', 'new', 'NEW', '', $person->getSex());
	break;
//------------------------------------------------------------------------------
case 'paste':
	$gedrec .= "\n".$WT_SESSION->clipboard[$fact]['factrec'];
	if (replace_gedrec($pid, WT_GED_ID, $gedrec, $NO_UPDATE_CHAN)) {
		$success=true;
	}
	break;


//LBox  Reorder Media ========================================================

//------------------------------------------------------------------------------
case 'reorder_media': // Sort page using Popup
	require_once WT_ROOT.'includes/media_reorder.php';
	break;

//------------------------------------------------------------------------------
case 'reset_media_update': // Reset sort using popup
	$lines = explode("\n", $gedrec);
	$newgedrec = '';
	foreach ($lines as $line) {
		if (strpos($line, '1 _WT_OBJE_SORT')===false) {
			$newgedrec .= $line."\n";
		}
	}
	if (replace_gedrec($pid, WT_GED_ID, $newgedrec, $update_CHAN)) {
		$success=true;
	}
	break;

//------------------------------------------------------------------------------
case 'reorder_media_update': // Update sort using popup
	if (isset($_REQUEST['order1'])) $order1 = $_REQUEST['order1'];
	$lines = explode("\n", $gedrec);
	$newgedrec = "";
	foreach ($lines as $line) {
		if (strpos($line, '1 _WT_OBJE_SORT')===false) {
			$newgedrec .= $line."\n";
		}
	}
	foreach ($order1 as $m_media=>$num) {
		$newgedrec .= "\n1 _WT_OBJE_SORT @".$m_media."@";
	}
	if (replace_gedrec($pid, WT_GED_ID, $newgedrec, $update_CHAN)) {
		$success=true;
	}

	$link = "individual.php?pid=$pid";
	break;

//------------------------------------------------------------------------------
case 'al_reset_media_update': // Reset sort using Album Page
	$lines = explode("\n", $gedrec);
	$newgedrec = "";
	foreach ($lines as $line) {
		if (strpos($line, "1 _WT_OBJE_SORT")===false) {
			$newgedrec .= $line."\n";
		}
	}
	if (replace_gedrec($pid, WT_GED_ID, $newgedrec, $update_CHAN)) {
		$success=true;
	}
	$link = "individual.php?pid=$pid";
	break;

//------------------------------------------------------------------------------
case 'al_reorder_media_update': // Update sort using Album Page
	if (isset($_REQUEST['order1'])) $order1 = $_REQUEST['order1'];
	function SwapArray($Array) {
		$Values = array();
		while (list($Key, $Val) = each($Array))
			$Values[$Val] = $Key;
		return $Values;
	}
	if (isset($_REQUEST['order2'])) $order2 = $_REQUEST['order2'];
	$order2 = SwapArray(explode(",", substr($order2, 0, -1)));

	$lines = explode("\n", $gedrec);
	$newgedrec = "";
	foreach ($lines as $line) {
		if (strpos($line, "1 _WT_OBJE_SORT")===false) {
			$newgedrec .= $line."\n";
		}
	}
	foreach ($order2 as $m_media=>$num) {
		$newgedrec .= "\n1 _WT_OBJE_SORT @".$m_media."@";
	}
	if (replace_gedrec($pid, WT_GED_ID, $newgedrec, $update_CHAN)) {
		$success=true;
	}
	$link = "individual.php?pid=$pid";
	break;

//LBox ===================================================


//------------------------------------------------------------------------------
case 'reorder_children':
	$controller
		->addInlineJavascript('jQuery("#reorder_list").sortable({forceHelperSize: true, forcePlaceholderSize: true, opacity: 0.7, cursor: "move", axis: "y"});')
		//-- update the order numbers after drag-n-drop sorting is complete
		->addInlineJavascript('jQuery("#reorder_list").bind("sortupdate", function(event, ui) { jQuery("#"+jQuery(this).attr("id")+" input").each( function (index, value) { value.value = index+1; }); });');

	echo '<br><b>', WT_I18N::translate('Re-order children'), '</b>';
	?>
	<form name="reorder_form" method="post" action="edit_interface.php">
		<input type="hidden" name="action" value="reorder_update">
		<input type="hidden" name="pid" value="<?php echo $pid; ?>">
		<input type="hidden" name="option" value="bybirth">
		<ul id="reorder_list">
		<?php
			// reorder children in modified families
			$family = WT_Family::getInstance($pid);
			$ids = array();
			foreach ($family->getChildren() as $child) {
				$ids[]=$child->getXref();
			}
			if ($family->getUpdatedFamily()) $family = $family->getUpdatedFamily();
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
			if ((!empty($option))&&($option=="bybirth")) {
				asort($children);
			}
			$i=0;
			$show_full = 1; // Force details to show for each child
			foreach ($children as $id=>$child) {
				echo '<li style="cursor:move; margin-bottom:2px; position:relative;"';
				if (!in_array($id, $ids)) echo ' class="facts_valueblue"';
				echo ' id="li_',$id,'" >';
				print_pedigree_person(WT_Person::getInstance($id), 2);
				echo '<input type="hidden" name="order[',$id,']" value="',$i,'">';
				echo '</li>';
				$i++;
			}
		echo '</ul>';
		if (WT_USER_IS_ADMIN) {
			echo "<center><table width=93%><tr><td class=\"descriptionbox wrap width25\">";
			echo WT_Gedcom_Tag::getLabel('CHAN'), "</td><td class=\"optionbox wrap\">";
			echo '<input type="checkbox" name="preserve_last_changed"';
			if ($NO_UPDATE_CHAN) {
				echo ' checked="checked"';
			} 
			echo '>';
			echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN');
			echo WT_Gedcom_Tag::getLabelValue('DATE', $family->LastChangeTimestamp());
			echo WT_Gedcom_Tag::getLabelValue('_WT_USER', $family->LastChangeUser());
			echo '</td></tr></table></center><br>';
		}
		?>
		<button type="submit"><?php echo WT_I18N::translate('Save'); ?></button>
		<button type="submit" onclick="document.reorder_form.action.value='reorder_children'; document.reorder_form.submit();"><?php echo WT_I18N::translate('sort by date of birth'); ?></button>
		<button type="submit" onclick="window.close();"><?php echo WT_I18N::translate('Cancel'); ?></button>
	</form>
	<br>
	<?php
	break;
//------------------------------------------------------------------------------
case 'changefamily':
	$family = new WT_Family($gedrec);
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
	echo '<script>';
	?>
		var nameElement = null;
		var remElement = null;
		function pastename(name) {
			if (nameElement) {
				nameElement.innerHTML = name;
			}
			if (remElement) {
				remElement.style.display = 'block';
			}
		}
	<?php
		echo '</script>
			<div id="changefam">
			<p>', WT_I18N::translate('Use this page to change or remove family members.<br /><br />For each member in the family, you can use the Change link to choose a different person to fill that role in the family.  You can also use the Remove link to remove that person from the family.<br /><br />When you have finished changing the family members, click the Save button to save the changes.'), '</p>';
	?>
	<form name="changefamform" method="post" action="edit_interface.php">
		<input type="hidden" name="action" value="changefamily_update">
		<input type="hidden" name="famid" value="<?php echo $famid; ?>">
		<table>
			<tr><td colspan="4" class="topbottombar"><?php echo WT_I18N::translate('Change Family Members'); ?></td></tr>
			<tr>
			<?php
			if (!is_null($father)) {
			?>
				<td class="descriptionbox"><b><?php echo $father->getLabel(); ?></b><input type="hidden" name="HUSB" value="<?php echo $father->getXref(); ?>"></td>
				<td id="HUSBName" class="optionbox"><?php echo $father->getFullName(); ?></td>
			<?php
			} else {
			?>
				<td class="descriptionbox"><b><?php echo WT_I18N::translate('spouse'); ?></b><input type="hidden" name="HUSB" value=""></td>
				<td id="HUSBName" class="optionbox"></td>
			<?php
			}
			?>
				<td class="optionbox">
					<a href="#" id="husbrem" style="display: <?php echo is_null($father) ? 'none':'block'; ?>;" onclick="document.changefamform.HUSB.value=''; document.getElementById('HUSBName').innerHTML=''; this.style.display='none'; return false;"><?php echo WT_I18N::translate('Remove'); ?></a>
				</td>
				<td class="optionbox">
					<a href="#" onclick="nameElement = document.getElementById('HUSBName'); remElement = document.getElementById('husbrem'); return findIndi(document.changefamform.HUSB);"><?php echo WT_I18N::translate('Change'); ?></a>
				</td>
			</tr>
			<tr>
			<?php
			if (!is_null($mother)) {
			?>
				<td class="descriptionbox"><b><?php echo $mother->getLabel(); ?></b><input type="hidden" name="WIFE" value="<?php echo $mother->getXref(); ?>"></td>
				<td id="WIFEName" class="optionbox"><?php echo $mother->getFullName(); ?></td>
			<?php
			} else {
			?>
				<td class="descriptionbox"><b><?php echo WT_I18N::translate('spouse'); ?></b><input type="hidden" name="WIFE" value=""></td>
				<td id="WIFEName" class="optionbox"></td>
			<?php
			}
			?>
				<td class="optionbox">
					<a href="#" id="wiferem" style="display: <?php echo is_null($mother) ? 'none':'block'; ?>;" onclick="document.changefamform.WIFE.value=''; document.getElementById('WIFEName').innerHTML=''; this.style.display='none'; return false;"><?php echo WT_I18N::translate('Remove'); ?></a>
				</td>
				<td class="optionbox">
					<a href="#" onclick="nameElement = document.getElementById('WIFEName'); remElement = document.getElementById('wiferem'); return findIndi(document.changefamform.WIFE);"><?php echo WT_I18N::translate('Change'); ?></a>
				</td>
			</tr>
			<?php
			$i=0;
			foreach ($children as $key=>$child) {
				if (!is_null($child)) {
				?>
			<tr>
				<td class="descriptionbox"><b><?php echo $child->getLabel(); ?></b><input type="hidden" name="CHIL<?php echo $i; ?>" value="<?php echo $child->getXref(); ?>"></td>
				<td id="CHILName<?php echo $i; ?>" class="optionbox"><?php echo $child->getFullName(); ?></td>
				<td class="optionbox">
					<a href="#" id="childrem<?php echo $i; ?>" style="display: block;" onclick="document.changefamform.CHIL<?php echo $i; ?>.value=''; document.getElementById('CHILName<?php echo $i; ?>').innerHTML=''; this.style.display='none'; return false;"><?php echo WT_I18N::translate('Remove'); ?></a>
				</td>
				<td class="optionbox">
					<a href="#" onclick="nameElement = document.getElementById('CHILName<?php echo $i; ?>'); remElement = document.getElementById('childrem<?php echo $i; ?>'); return findIndi(document.changefamform.CHIL<?php echo $i; ?>);"><?php echo WT_I18N::translate('Change'); ?></a>
				</td>
			</tr>
				<?php
					$i++;
				}
			}
				?>
			<tr>
				<td class="descriptionbox"><b><?php echo WT_I18N::translate('child'); ?></b><input type="hidden" name="CHIL<?php echo $i; ?>" value=""></td>
				<td id="CHILName<?php echo $i; ?>" class="optionbox"></td>
				<td colspan="2" class="optionbox child">
					<a href="#" id="childrem<?php echo $i; ?>" style="display: none;" onclick="document.changefamform.CHIL<?php echo $i; ?>.value=''; document.getElementById('CHILName<?php echo $i; ?>').innerHTML=''; this.style.display='none'; return false;"><?php echo WT_I18N::translate('Remove'); ?></a>
					<a href="#" onclick="nameElement = document.getElementById('CHILName<?php echo $i; ?>'); remElement = document.getElementById('childrem<?php echo $i; ?>'); return findIndi(document.changefamform.CHIL<?php echo $i; ?>);"><?php echo WT_I18N::translate('Add'); ?></a>
				</td>
			</tr>
		</table>
		<!-- <p><a href="#" onclick="addnewchild(''); return false;"><?php echo WT_I18N::translate('Add an unlinked person'); ?></a></p> -->
		<div id="save-cancel">
			<div class="save"><input type="submit" value="<?php echo WT_I18N::translate('Save'); ?>"></div>
			<div class="cancel"><input type="button" value="<?php echo WT_I18N::translate('Cancel'); ?>" onclick="window.close();"></div>
		</div>
	</form>
	<?php
	echo '</div>';//close #changefam
	break;
//------------------------------------------------------------------------------
case 'changefamily_update':
	$family = new WT_Family($gedrec);
	$father = $family->getHusband();
	$mother = $family->getWife();
	$children = $family->getChildren();
	$updated = false;
	//-- add the new father link
	if (isset($_REQUEST['HUSB'])) $HUSB = $_REQUEST['HUSB'];
	if (!empty($HUSB) && (is_null($father) || $father->getXref()!=$HUSB)) {
		if (strstr($gedrec, "1 HUSB")!==false) {
			$gedrec = preg_replace("/1 HUSB @.*@/", "1 HUSB @$HUSB@", $gedrec);
		} else {
			$gedrec .= "\n1 HUSB @$HUSB@";
		}
		$indirec = find_gedcom_record($HUSB, WT_GED_ID, true);
		if (!empty($indirec) && (strpos($indirec, "1 FAMS @$famid@")===false)) {
			$indirec .= "\n1 FAMS @$famid@";
			replace_gedrec($HUSB, WT_GED_ID, $indirec, $update_CHAN);
		}
		$updated = true;
	}
	//-- remove the father link
	if (empty($HUSB)) {
		$pos1 = strpos($gedrec, "1 HUSB @");
		if ($pos1!==false) {
			$pos2 = strpos($gedrec, "\n1", $pos1+5);
			if ($pos2===false) {
				$pos2 = strlen($gedrec);
			} else {
				$pos2++;
			}
			$gedrec = substr($gedrec, 0, $pos1) . substr($gedrec, $pos2);
		}
		$updated = true;
	}
	//-- remove the FAMS link from the old father
	if (!is_null($father) && $father->getXref()!=$HUSB) {
		$indirec = find_gedcom_record($father->getXref(), WT_GED_ID, true);
		$pos1 = strpos($indirec, "1 FAMS @$famid@");
		if ($pos1!==false) {
			$pos2 = strpos($indirec, "\n1", $pos1+5);
			if ($pos2===false) {
				$pos2 = strlen($indirec);
			} else {
				$pos2++;
			}
			$indirec = substr($indirec, 0, $pos1) . substr($indirec, $pos2);
			replace_gedrec($father->getXref(), WT_GED_ID, $indirec, $update_CHAN);
		}
	}
	//-- add the new mother link
	if (isset($_REQUEST['WIFE'])) $WIFE = $_REQUEST['WIFE'];
	if (!empty($WIFE) && (is_null($mother) || $mother->getXref()!=$WIFE)) {
		if (strstr($gedrec, "1 WIFE")!==false) {
			$gedrec = preg_replace("/1 WIFE @.*@/", "1 WIFE @$WIFE@", $gedrec);
		} else {
			$gedrec .= "\n1 WIFE @$WIFE@";
		}
		$indirec = find_gedcom_record($WIFE, WT_GED_ID, true);
		if (!empty($indirec) && (strpos($indirec, "1 FAMS @$famid@")===false)) {
			$indirec .= "\n1 FAMS @$famid@";
			replace_gedrec($WIFE, WT_GED_ID, $indirec, $update_CHAN);
		}
		$updated = true;
	}
	//-- remove the father link
	if (empty($WIFE)) {
		$pos1 = strpos($gedrec, "1 WIFE @");
		if ($pos1!==false) {
			$pos2 = strpos($gedrec, "\n1", $pos1+5);
			if ($pos2===false) {
				$pos2 = strlen($gedrec);
			} else {
				$pos2++;
			}
			$gedrec = substr($gedrec, 0, $pos1) . substr($gedrec, $pos2);
		}
		$updated = true;
	}
	//-- remove the FAMS link from the old father
	if (!is_null($mother) && $mother->getXref()!=$WIFE) {
		$indirec = find_gedcom_record($mother->getXref(), WT_GED_ID, true);
		$pos1 = strpos($indirec, "1 FAMS @$famid@");
		if ($pos1!==false) {
			$pos2 = strpos($indirec, "\n1", $pos1+5);
			if ($pos2===false) {
				$pos2 = strlen($indirec);
			} else {
				$pos2++;
			}
			$indirec = substr($indirec, 0, $pos1) . substr($indirec, $pos2);
			replace_gedrec($mother->getXref(), WT_GED_ID, $indirec, $update_CHAN);
		}
	}

	//-- update the children
	$i=0;
	$var = "CHIL".$i;
	$newchildren = array();
	while (isset($_REQUEST[$var])) {
		$CHIL = $_REQUEST[$var];
		if (!empty($CHIL)) {
			$newchildren[] = $CHIL;
			if (strpos($gedrec, "1 CHIL @$CHIL@")===false) {
				$gedrec .= "\n1 CHIL @$CHIL@";
				$updated = true;
				$indirec = find_gedcom_record($CHIL, WT_GED_ID, true);
				if (!empty($indirec) && (strpos($indirec, "1 FAMC @$famid@")===false)) {
					$indirec .= "\n1 FAMC @$famid@";
					replace_gedrec($CHIL, WT_GED_ID, $indirec, $update_CHAN);
				}
			}
		}
		$i++;
		$var = "CHIL".$i;
	}

	//-- remove the old children
	foreach ($children as $key=>$child) {
		if (!is_null($child)) {
			if (!in_array($child->getXref(), $newchildren)) {
				//-- remove the CHIL link from the family record
				$pos1 = strpos($gedrec, "1 CHIL @".$child->getXref()."@");
				if ($pos1!==false) {
					$pos2 = strpos($gedrec, "\n1", $pos1+5);
					if ($pos2===false) {
						$pos2 = strlen($gedrec);
					} else {
						$pos2++;
					}
					$gedrec = substr($gedrec, 0, $pos1) . substr($gedrec, $pos2);
					$updated = true;
				}
				//-- remove the FAMC link from the child record
				$indirec = find_gedcom_record($child->getXref(), WT_GED_ID, true);
				$pos1 = strpos($indirec, "1 FAMC @$famid@");
				if ($pos1!==false) {
					$pos2 = strpos($indirec, "\n1", $pos1+5);
					if ($pos2===false) {
						$pos2 = strlen($indirec);
					} else {
						$pos2++;
					}
					$indirec = substr($indirec, 0, $pos1) . substr($indirec, $pos2);
					replace_gedrec($child->getXref(), WT_GED_ID, $indirec, $update_CHAN);
				}
			}
		}
	}

	if ($updated) {
		replace_gedrec($famid, WT_GED_ID, $gedrec, $update_CHAN);
		$success = true;
	}
	break;
//------------------------------------------------------------------------------
case 'reorder_update':
	if (isset($_REQUEST['order'])) $order = $_REQUEST['order'];
	asort($order);
	reset($order);
	$newgedrec = $gedrec;
	foreach ($order as $child=>$num) {
		// move each child subrecord to the bottom, in the order specified
		$subrec = get_sub_record(1, '1 CHIL @'.$child.'@', $gedrec);
		$subrec = trim($subrec, "\n");
		$newgedrec = str_replace($subrec, '', $newgedrec);
		$newgedrec .= "\n".$subrec."\n";
	}
	if (replace_gedrec($pid, WT_GED_ID, $newgedrec, $update_CHAN)) {
		$success=true;
	}
	break;
//------------------------------------------------------------------------------
case 'reorder_fams':
		$controller
		->addInlineJavascript('jQuery("#reorder_list").sortable({forceHelperSize: true, forcePlaceholderSize: true, opacity: 0.7, cursor: "move", axis: "y"});')
		//-- update the order numbers after drag-n-drop sorting is complete
		->addInlineJavascript('jQuery("#reorder_list").bind("sortupdate", function(event, ui) { jQuery("#"+jQuery(this).attr("id")+" input").each( function (index, value) { value.value = index+1; }); });');

	echo "<br><b>", WT_I18N::translate('Re-order families'), "</b>";
	?>
	<form name="reorder_form" method="post" action="edit_interface.php">
		<input type="hidden" name="action" value="reorder_fams_update">
		<input type="hidden" name="pid" value="<?php echo $pid; ?>">
		<input type="hidden" name="option" value="bymarriage">
		<ul id="reorder_list">
		<?php
			$person = WT_Person::getInstance($pid);
			$fams = $person->getSpouseFamilies();
			if ((!empty($option))&&($option=="bymarriage")) {
				usort($fams, array('WT_Family', 'CompareMarrDate'));
			}
			$i=0;
			foreach ($fams as $family) {
				echo '<li class="facts_value" style="cursor:move;margin-bottom:2px;" id="li_', $family->getXref(), '" >';
				echo '<span class="name2">', $family->getFullName(), '</span><br>';
				echo $family->format_first_major_fact(WT_EVENTS_MARR, 2);
				echo '<input type="hidden" name="order[', $family->getXref(), ']" value="', $i, '">';
				echo '</li>';
				$i++;
			}
		?>
		</ul>
		<button type="submit"><?php echo WT_I18N::translate('Save'); ?></button>
		<button type="submit" onclick="document.reorder_form.action.value='reorder_fams'; document.reorder_form.submit();"><?php echo WT_I18N::translate('sort by date of marriage'); ?></button>
		<button type="submit" onclick="window.close();"><?php echo WT_I18N::translate('Cancel'); ?></button>
	</form>
	<?php
	break;
//------------------------------------------------------------------------------
case 'reorder_fams_update':
	if (isset($_REQUEST['order'])) $order = $_REQUEST['order'];
	asort($order);
	reset($order);
	$lines = explode("\n", $gedrec);
	$newgedrec = "";
	foreach ($lines as $line) {
		if (strpos($line, "1 FAMS")===false) {
			$newgedrec .= $line."\n";
		}
	}
	foreach ($order as $famid=>$num) {
		$newgedrec .= "\n1 FAMS @".$famid."@";
	}
	if (replace_gedrec($pid, WT_GED_ID, $newgedrec, $update_CHAN)) {
		$success=true;
	}
	break;
}


// Redirect to new record, if requested
if (isset($_REQUEST['goto'])) {
	$goto = $_REQUEST['goto'];
}
if (isset($_REQUEST['link'])) {
	$link = $_REQUEST['link'];
}
if (empty($goto) || empty($link)) {
	$link='';
}

// autoclose window when update successful unless debug on
if ($success && !WT_DEBUG) {
	$controller->addInlineJavascript('closePopupAndReloadParent("'.$link.'");');
} else {
	echo '<p class="center"><a href="#" onclick="closePopupAndReloadParent(\'', $link, '\');">', WT_I18N::translate('Close Window'), '</a></p>';
}
echo '</div>';//close #edit_interface-page
