<?php
// Various functions used by the Edit interface
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/functions/functions_import.php';

// Create an edit control for inline editing using jeditable
function edit_field_inline($name, $value, $controller=null) {
	$html='<span class="editable" id="' . $name . '">' . htmlspecialchars($value) . '</span>';
	$js='jQuery("#' . $name . '").editable("' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'save.php", {submit:"&nbsp;&nbsp;' . /* I18N: button label */ WT_I18N::translate('save') . '&nbsp;&nbsp;", style:"inherit", placeholder: "'.WT_I18N::translate('click to edit').'"});';

	if ($controller) {
		$controller->addInlineJavascript($js);
		return $html;
	} else {
		// For AJAX callbacks
		return $html . '<script>' . $js . '</script>';
	}
}

// Create a text area for inline editing using jeditable
function edit_text_inline($name, $value, $controller=null) {
	$html='<span class="editable" style="white-space:pre-wrap;" id="' . $name . '">' . htmlspecialchars($value) . '</span>';
	$js='jQuery("#' . $name . '").editable("' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'save.php", {submit:"&nbsp;&nbsp;' . WT_I18N::translate('save') . '&nbsp;&nbsp;", style:"inherit", placeholder: "'.WT_I18N::translate('click to edit').'", type: "textarea", rows:4, cols:60 });';

	if ($controller) {
		$controller->addInlineJavascript($js);
		return $html;
	} else {
		// For AJAX callbacks
		return $html . '<script>' . $js . '</script>';
	}
}

// Create a <select> control for a form
// $name     - the ID for the form element
// $values   - array of value=>display items
// $empty    - if not null, then add an entry ""=>$empty
// $selected - the currently selected item (if any)
// $extra    - extra markup for field (e.g. tab key sequence)
function select_edit_control($name, $values, $empty, $selected, $extra='') {
	if (is_null($empty)) {
		$html='';
	} else {
		if (empty($selected)) {
			$html='<option value="" selected="selected">'.htmlspecialchars($empty).'</option>';
		} else {
			$html='<option value="">'.htmlspecialchars($empty).'</option>';
		}
	}
	// A completely empty list would be invalid, and break various things
	if (empty($values) && empty($html)) {
		$html='<option value=""></option>';
	}
	foreach ($values as $key=>$value) {
		if ($key==$selected) {
			$html.='<option value="'.htmlspecialchars($key).'" selected="selected" dir="auto">'.htmlspecialchars($value).'</option>';
		} else {
			$html.='<option value="'.htmlspecialchars($key).'" dir="auto">'.htmlspecialchars($value).'</option>';
		}
	}
	return '<select id="'.$name.'" name="'.$name.'" '.$extra.'>'.$html.'</select>';
}

// An inline-editing version of select_edit_control()
function select_edit_control_inline($name, $values, $empty, $selected, $controller=null) {
	if (!is_null($empty)) {
		// Push ''=>$empty onto the front of the array, maintaining keys
		$tmp=array(''=>htmlspecialchars($empty));
		foreach ($values as $key=>$value) {
			$tmp[$key]=htmlspecialchars($value);
		}
		$values=$tmp;
	}
	$values['selected']=htmlspecialchars($selected);

	$html='<span class="editable" id="' . $name . '">' .  (array_key_exists($selected, $values) ? $values[$selected] : '') . '</span>';
	$js='jQuery("#' . $name . '").editable("' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'save.php", {type:"select", data:' . json_encode($values) . ', submit:"&nbsp;&nbsp;' . WT_I18N::translate('save') . '&nbsp;&nbsp;", style:"inherit", placeholder: "'.WT_I18N::translate('click to edit').'", callback:function(value, settings) {jQuery(this).html(settings.data[value]);} });';

	if ($controller) {
		$controller->addInlineJavascript($js);
		return $html;
	} else {
		// For AJAX callbacks
		return $html . '<script>' . $js . '</script>';
	}
}

// Create a set of radio buttons for a form
// $name     - the ID for the form element
// $values   - array of value=>display items
// $selected - the currently selected item (if any)
// $extra    - extra markup for field (e.g. tab key sequence)
function radio_buttons($name, $values, $selected, $extra='') {
	$html='';
	foreach ($values as $key=>$value) {
		$uniqueID = $name.(int)(microtime() * 1000000);
		$html.='<input type="radio" name="'.$name.'" id="'.$uniqueID.'" value="'.htmlspecialchars($key).'"';
		if ($key==$selected) {
			$html.=' checked';
		}
		$html.='><label for="'.$uniqueID.'">'.htmlspecialchars($value).'</label>';
	}
	return $html;
}

// Print an edit control for a Yes/No field
function edit_field_yes_no($name, $selected=false, $extra='') {
	return radio_buttons(
		$name, array(false=>WT_I18N::translate('no'),true=>WT_I18N::translate('yes')), $selected, $extra
	);
}

// An inline-editing version of edit_field_yes_no()
function edit_field_yes_no_inline($name, $selected=false, $controller=null) {
	return select_edit_control_inline(
		$name, array(true=>WT_I18N::translate('yes'), false=>WT_I18N::translate('no')), null, (int)$selected, $controller
	);
}

// Print an edit control for a checkbox
function checkbox($name, $is_checked=false, $extra='') {
	return '<input type="checkbox" name="'.$name.'" value="1" '.($is_checked ? 'checked="checked" ' : '').$extra.'>';
}

// Print an edit control for a checkbox, with a hidden field to store one of the two states.
// By default, a checkbox is either set, or not sent.
// This function gives us a three options, set, unset or not sent.
// Useful for dynamically generated forms where we don't know what elements are present.
function two_state_checkbox($name, $is_checked=0, $extra='') {
	return
		'<input type="hidden" id="'.$name.'" name="'.$name.'" value="'.($is_checked?1:0).'">'.
		'<input type="checkbox" name="'.$name.'-GUI-ONLY" value="1"'.
		($is_checked ? ' checked="checked"' : '').
		' onclick="document.getElementById(\''.$name.'\').value=(this.checked?1:0);" '.$extra.'>';
}

// Print a set of edit controls to select languages
function edit_language_checkboxes($field_prefix, $languages) {
	echo '<table>';
	$i=0;
	foreach (WT_I18N::installed_languages() as $code=>$name) {
		$content = '<input type="checkbox" name="'.$field_prefix.$code.'" id="'.$field_prefix.$code.'"';
		if (strpos(",{$languages},", ",{$code},")!==false) {
			$content .= 'checked="checked"';
		}
		$content .= '><label for="'.$field_prefix.$code.'"> '.$name.'</label>';
		// print in two columns
		switch ($i % 3) {
		case 0: echo '<tr><td>', $content, '</td>'; break;
		case 1: echo '<td>', $content, '</td>'; break;
		case 2: echo '<td>', $content, '</td></tr>'; break;
		}
		$i++;
	}
	switch ($i % 3) {
	case 0: echo '</tr>'; break;
	case 1: echo '</td></td></tr>'; break;
	case 2: echo '</td></tr>'; break;
	}
	echo '</table>';
}

// Print an edit control for access level
function edit_field_access_level($name, $selected='', $extra='') {
	$ACCESS_LEVEL=array(
		WT_PRIV_PUBLIC=>WT_I18N::translate('Show to visitors'),
		WT_PRIV_USER  =>WT_I18N::translate('Show to members'),
		WT_PRIV_NONE  =>WT_I18N::translate('Show to managers'),
		WT_PRIV_HIDE  =>WT_I18N::translate('Hide from everyone')
	);
	return select_edit_control($name, $ACCESS_LEVEL, null, $selected, $extra);
}

// Print an edit control for a RESN field
function edit_field_resn($name, $selected='', $extra='') {
	$RESN=array(
		''            =>'',
		'none'        =>WT_I18N::translate('Show to visitors'), // Not valid GEDCOM, but very useful
		'privacy'     =>WT_I18N::translate('Show to members'),
		'confidential'=>WT_I18N::translate('Show to managers'),
		'locked'      =>WT_I18N::translate('Only managers can edit')
	);
	return select_edit_control($name, $RESN, null, $selected, $extra);
}

// Print an edit control for a contact method field
function edit_field_contact($name, $selected='', $extra='') {
	// Different ways to contact the users
	$CONTACT_METHODS=array(
		'messaging' =>WT_I18N::translate('webtrees internal messaging'),
		'messaging2'=>WT_I18N::translate('Internal messaging with emails'),
		'messaging3'=>WT_I18N::translate('webtrees sends emails with no storage'),
		'mailto'    =>WT_I18N::translate('Mailto link'),
		'none'      =>WT_I18N::translate('No contact'),
	);
	if (!WT_Site::preference('STORE_MESSAGES')) {
		unset($CONTACT_METHODS['messaging'], $CONTACT_METHODS['messaging2']);
	}
	return select_edit_control($name, $CONTACT_METHODS, null, $selected, $extra);
}
function edit_field_contact_inline($name, $selected='', $controller=null) {
	// Different ways to contact the users
	$CONTACT_METHODS=array(
		'messaging' =>WT_I18N::translate('webtrees internal messaging'),
		'messaging2'=>WT_I18N::translate('Internal messaging with emails'),
		'messaging3'=>WT_I18N::translate('webtrees sends emails with no storage'),
		'mailto'    =>WT_I18N::translate('Mailto link'),
		'none'      =>WT_I18N::translate('No contact'),
	);
	if (!WT_Site::preference('STORE_MESSAGES')) {
		unset($CONTACT_METHODS['messaging'], $CONTACT_METHODS['messaging2']);
	}
	return select_edit_control_inline($name, $CONTACT_METHODS, null, $selected, $controller);
}

// Print an edit control for a language field
function edit_field_language($name, $selected='', $extra='') {
	return select_edit_control($name, WT_I18N::installed_languages(), null, $selected, $extra);
}

// An inline-editing version of edit_field_language()
function edit_field_language_inline($name, $selected=false, $controller=null) {
	return select_edit_control_inline(
		$name, WT_I18N::installed_languages(), null, $selected, $controller
	);
}

// Print an edit control for a range of integers
function edit_field_integers($name, $selected='', $min, $max, $extra='') {
	$array=array();
	for ($i=$min; $i<=$max; ++$i) {
		$array[$i]=WT_I18N::number($i);
	}
	return select_edit_control($name, $array, null, $selected, $extra);
}

// Print an edit control for a username
function edit_field_username($name, $selected='', $extra='') {
	$all_users=WT_DB::prepare(
		"SELECT user_name, CONCAT_WS(' ', real_name, '-', user_name) FROM `##user` ORDER BY real_name"
	)->fetchAssoc();
	// The currently selected user may not exist
	if ($selected && !array_key_exists($selected, $all_users)) {
		$all_users[$selected]=$selected;
	}
	return select_edit_control($name, $all_users, '-', $selected, $extra);
}

// Print an edit control for a ADOP field
function edit_field_adop_u($name, $selected='', $extra='') {
	return select_edit_control($name, WT_Gedcom_Code_Adop::getValues(), null, $selected, $extra);
}

// Print an edit control for a ADOP female field
function edit_field_adop_f($name, $selected='', $extra='') {
	return select_edit_control($name, WT_Gedcom_Code_Adop::getValues(new WT_Person("0 @XXX@ INDI\n1 SEX F")), null, $selected, $extra);
}

// Print an edit control for a ADOP male field
function edit_field_adop_m($name, $selected='', $extra='') {
	return select_edit_control($name, WT_Gedcom_Code_Adop::getValues(new WT_Person("0 @XXX@ INDI\n1 SEX M")), null, $selected, $extra);
}

// Print an edit control for a PEDI field
function edit_field_pedi_u($name, $selected='', $extra='') {
	return select_edit_control($name, WT_Gedcom_Code_Pedi::getValues(), '', $selected, $extra);
}

// Print an edit control for a PEDI female field
function edit_field_pedi_f($name, $selected='', $extra='') {
	return select_edit_control($name, WT_Gedcom_Code_Pedi::getValues(new WT_Person("0 @XXX@ INDI\n1 SEX F")), '', $selected, $extra);
}

// Print an edit control for a PEDI male field
function edit_field_pedi_m($name, $selected='', $extra='') {
	return select_edit_control($name, WT_Gedcom_Code_Pedi::getValues(new WT_Person("0 @XXX@ INDI\n1 SEX M")), '', $selected, $extra);
}

// Print an edit control for a NAME TYPE field
function edit_field_name_type_u($name, $selected='', $extra='') {
	return select_edit_control($name, WT_Gedcom_Code_Name::getValues(), '', $selected, $extra);
}

// Print an edit control for a female NAME TYPE field
function edit_field_name_type_f($name, $selected='', $extra='') {
	return select_edit_control($name, WT_Gedcom_Code_Name::getValues(new WT_Person("0 @XXX@ INDI\n1 SEX F")), '', $selected, $extra);
}

// Print an edit control for a male NAME TYPE field
function edit_field_name_type_m($name, $selected='', $extra='') {
	return select_edit_control($name, WT_Gedcom_Code_Name::getValues(new WT_Person("0 @XXX@ INDI\n1 SEX M")), '', $selected, $extra);
}

// Print an edit control for a RELA field
function edit_field_rela($name, $selected='', $extra='') {
	$rela_codes=WT_Gedcom_Code_Rela::getValues();
	// The user is allowed to specify values that aren't in the list.
	if (!array_key_exists($selected, $rela_codes)) {
		$rela_codes[$selected]=$selected;
	}
	return select_edit_control($name, $rela_codes, '', $selected, $extra);
}

/**
* Check if the given gedcom record has changed since the last session access
* This is used to check if the gedcom record changed between the time the user
* loaded the individual page and the time they clicked on a link to edit
* the data.
*
* @param string $pid The gedcom id of the record to check
*/
function checkChangeTime($pid, $gedrec, $last_time) {
	$change=WT_DB::prepare(
		"SELECT UNIX_TIMESTAMP(change_time) AS change_time, user_name".
		" FROM `##change`".
		" JOIN `##user` USING (user_id)".
		" WHERE status<>'rejected' AND gedcom_id=? AND xref=? AND change_time>?".
		" ORDER BY change_id DESC".
		" LIMIT 1"
	)->execute(array(WT_GED_ID, $pid, $last_time))->fetchOneRow();

	if ($change) {
		$changeTime=$change->change_time;
		$changeUser=$change->user_name;
	} else {
		$changeTime = 0;
		$changeUser = '';
	}
	if (isset($_REQUEST['linenum']) && $changeTime!=0 && $last_time && $changeTime > $last_time) {
		echo "<span class=\"error\">", WT_I18N::translate('The record with id %s was changed by another user since you last accessed it.', $pid), "<br><br>";
		if (!empty($changeUser)) echo /* I18N: %s placeholders are a user-ID and a timestamp */ WT_I18N::translate('This record was last changed by <i>%s</i> at %s', $changeUser, date("d M Y H:i:s", $changeTime)), "<br><br>";
		echo WT_I18N::translate('Please reload the previous page to make sure you are working with the most recent record.'), "</span>";
		exit;
	}
}

// Replace an updated record with a newer version
// $xref/$ged_id - the record to update
// $gedrec       - the new gedcom record
// $chan         - whether or not to update the CHAN record
function replace_gedrec($xref, $ged_id, $gedrec, $chan=true) {
	if (($gedrec = check_gedcom($gedrec, $chan))!==false) {
		$old_gedrec=find_gedcom_record($xref, $ged_id, true);
		if ($old_gedrec!=$gedrec) {
			WT_DB::prepare(
				"INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, ?, ?, ?)"
			)->execute(array(
				$ged_id,
				$xref,
				$old_gedrec,
				$gedrec,
				WT_USER_ID
			));
		}

		if (get_user_setting(WT_USER_ID, 'auto_accept')) {
			accept_all_changes($xref, $ged_id);
		}
		return true;
	}
	return false;
}

//-- this function will append a new gedcom record at
//-- the end of the gedcom file.
function append_gedrec($gedrec, $ged_id) {
	if (($gedrec = check_gedcom($gedrec, true))!==false && preg_match("/0 @(".WT_REGEX_XREF.")@ (".WT_REGEX_TAG.")/", $gedrec, $match)) {
		$gid  = $match[1];
		$type = $match[2];

		if (preg_match("/\d/", $gid)==0) {
			$xref = get_new_xref($type);
		} else {
			$xref = $gid;
		}
		$gedrec=preg_replace("/^0 @(.*)@/", "0 @$xref@", $gedrec);

		WT_DB::prepare(
			"INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, ?, ?, ?)"
		)->execute(array(
			$ged_id,
			$xref,
			'',
			$gedrec,
			WT_USER_ID
		));

		AddToLog("Appending new $type record $xref", 'edit');

		if (get_user_setting(WT_USER_ID, 'auto_accept')) {
			accept_all_changes($xref, WT_GED_ID);
		}
		return $xref;
	}
	return false;
}

//-- this function will delete the gedcom record with
//-- the given $xref
function delete_gedrec($xref, $ged_id) {
	WT_DB::prepare(
		"INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, ?, ?, ?)"
	)->execute(array(
		$ged_id,
		$xref,
		find_gedcom_record($xref, $ged_id, true),
		'',
		WT_USER_ID
	));

	AddToLog("Deleting gedcom record $xref", 'edit');

	if (get_user_setting(WT_USER_ID, 'auto_accept')) {
		accept_all_changes($xref, WT_GED_ID);
	}
}

//-- this function will check a GEDCOM record for valid gedcom format
function check_gedcom($gedrec, $chan=true) {
	$ct = preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
	if ($ct==0) {
		echo "ERROR 20: Invalid GEDCOM format";
		AddToLog("ERROR 20: Invalid GEDCOM format:\n".$gedrec, 'edit');
		if (WT_DEBUG) {
			echo "<pre>$gedrec</pre>";
			echo debug_print_backtrace();
		}
		return false;
	}
	$gedrec = trim($gedrec);
	if ($chan) {
		$pos1 = strpos($gedrec, "1 CHAN");
		if ($pos1!==false) {
			$pos2 = strpos($gedrec, "\n1", $pos1+4);
			if ($pos2===false) $pos2 = strlen($gedrec);
			$newgedrec = substr($gedrec, 0, $pos1);
			$newgedrec .= "1 CHAN\n2 DATE ".strtoupper(date("d M Y"))."\n";
			$newgedrec .= "3 TIME ".date("H:i:s")."\n";
			$newgedrec .= "2 _WT_USER ".WT_USER_NAME."\n";
			$newgedrec .= substr($gedrec, $pos2);
			$gedrec = $newgedrec;
		}
		else {
			$newgedrec = "\n1 CHAN\n2 DATE ".strtoupper(date("d M Y"))."\n";
			$newgedrec .= "3 TIME ".date("H:i:s")."\n";
			$newgedrec .= "2 _WT_USER ".WT_USER_NAME;
			$gedrec .= $newgedrec;
		}
	}
	$gedrec = preg_replace('/\\\+/', "\\", $gedrec);

	//-- remove any empty lines
	$lines = explode("\n", $gedrec);
	$newrec = '';
	foreach ($lines as $ind=>$line) {
		//-- remove any whitespace
		$line = trim($line);
		if (!empty($line)) $newrec .= $line."\n";
	}

	$newrec = html_entity_decode($newrec, ENT_COMPAT, 'UTF-8');
	return $newrec;
}

/**
* remove a subrecord from a parent record by gedcom tag
*
* @param string $oldrecord the parent record to remove the subrecord from
* @param string $tag the GEDCOM subtag to start deleting at
* @param string $gid [optional] gid can be used to limit to @gid@
* @param int $num [optional] num specifies which multiple of the tag to remove, set to -1 to remove all
* @return string returns the oldrecord minus the subrecord(s)
*/
function remove_subrecord($oldrecord, $tag, $gid='', $num=0) {
	$newrec = '';
	$gedlines = explode("\n", $oldrecord);

	$n = 0;
	$matchstr = $tag;
	if (!empty($gid)) $matchstr .= " @".$gid."@";
	for ($i=0; $i<count($gedlines); $i++) {
		if (preg_match("/".$matchstr."/", $gedlines[$i])>0) {
			if ($num==-1 || $n==$num) {
				$glevel = $gedlines[$i]{0};
				$i++;
				while ((isset($gedlines[$i]))&&(strlen($gedlines[$i])<4 || $gedlines[$i]{0}>$glevel)) $i++;
				$i--;
			}
			else $n++;
		}
		else $newrec .= $gedlines[$i]."\n";
	}

	return trim($newrec);
}

/**
* delete a subrecord from a parent record using the linenumber
*
* @param string $oldrecord parent record to delete from
* @param int $linenum linenumber where the subrecord to delete starts
* @return string the new record
*/
function remove_subline($oldrecord, $linenum) {
	$newrec = '';
	$gedlines = explode("\n", $oldrecord);

	for ($i=0; $i<$linenum; $i++) {
		if (trim($gedlines[$i])!='') $newrec .= $gedlines[$i]."\n";
	}
	if (isset($gedlines[$linenum])) {
		$fields = explode(' ', $gedlines[$linenum]);
		$glevel = $fields[0];
		$i++;
		if ($i<count($gedlines)) {
			//-- don't put empty lines in the record
			while ((isset($gedlines[$i]))&&(strlen($gedlines[$i])<4 || $gedlines[$i]{0}>$glevel)) $i++;
			while ($i<count($gedlines)) {
				if (trim($gedlines[$i])!='') $newrec .= $gedlines[$i]."\n";
				$i++;
			}
		}
	}
	else return $oldrecord;

	$newrec = trim($newrec);
	return $newrec;
}

/**
* prints a form to add an individual or edit an individual's name
*
* @param string $nextaction the next action the edit_interface.php file should take after the form is submitted
* @param string $famid the family that the new person should be added to
* @param string $namerec the name subrecord when editing a name
* @param string $famtag how the new person is added to the family
*/
function print_indi_form($nextaction, $famid, $linenum='', $namerec='', $famtag="CHIL", $sextag='') {
	global $pid, $WORD_WRAPPED_NOTES;
	global $NPFX_accept, $SPFX_accept, $NSFX_accept, $FILE_FORM_accept;
	global $bdm, $STANDARD_NAME_FACTS, $REVERSED_NAME_FACTS, $ADVANCED_NAME_FACTS, $ADVANCED_PLAC_FACTS;
	global $QUICK_REQUIRED_FACTS, $QUICK_REQUIRED_FAMFACTS, $NO_UPDATE_CHAN;

	$SURNAME_TRADITION=get_gedcom_setting(WT_GED_ID, 'SURNAME_TRADITION');

	$bdm = ''; // used to copy '1 SOUR' to '2 SOUR' for BIRT DEAT MARR
	init_calendar_popup();
	echo "<form method=\"post\" name=\"addchildform\" onsubmit=\"return checkform();\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"$nextaction\">";
	echo "<input type=\"hidden\" name=\"linenum\" value=\"$linenum\">";
	echo "<input type=\"hidden\" name=\"famid\" value=\"$famid\">";
	echo "<input type=\"hidden\" name=\"pid\" value=\"$pid\">";
	echo "<input type=\"hidden\" name=\"famtag\" value=\"$famtag\">";
	echo "<input type=\"submit\" value=\"", WT_I18N::translate('Save'), "\">";
	echo "<input type=\"hidden\" name=\"goto\" value=\"\">";
	if (preg_match('/^add(child|spouse|newparent|newrepository)/', $nextaction)) {
		echo "<input type=\"submit\" value=\"", WT_I18N::translate('Save and go to new record'), "\" onclick=\"document.addchildform.goto.value='new';\">";
	}
	echo "<table class=\"facts_table\">";

	// When adding a new child, specify the pedigree
	if ($nextaction=='addchildaction' || $nextaction=='addopfchildaction') {
		add_simple_tag('0 PEDI');
	}

	if ($nextaction=='update') {
		$name_type=get_gedcom_value('TYPE', 2, $namerec);
		add_simple_tag('0 TYPE '.$name_type);
	}
	// Populate the standard NAME field and subfields
	$name_fields=array();
	foreach ($STANDARD_NAME_FACTS as $tag) {
		$name_fields[$tag]=get_gedcom_value($tag, 0, $namerec);
	}

	$new_marnm='';
	// Inherit surname from parents, spouse or child
	if (empty($namerec)) {
		// We'll need the parent's name to set the child's surname
		$family=WT_Family::getInstance($famid);
		if ($family && $family->getHusband()) {
			$father_name=get_gedcom_value('NAME', 0, $family->getHusband()->getGedcomRecord());
		} else {
			$father_name='';
		}
		if ($family && $family->getWife()) {
			$mother_name=get_gedcom_value('NAME', 0, $family->getWife()->getGedcomRecord());
		} else {
			$mother_name='';
		}
		// We'll need the spouse/child's name to set the spouse/parent's surname
		$prec=find_gedcom_record($pid, WT_GED_ID, true);
		$indi_name=get_gedcom_value('NAME', 0, $prec);
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
				if (preg_match('/\/((?:[a-z]{2,3}\s+)*)(.*)\//i', $father_name, $match)) {
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
				if ($famtag=='HUSB' && preg_match('/\/((?:[a-z]{2,3}\s+)*)(.*)\//i', $indi_name, $match)) {
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
				if ($famtag=='WIFE' && preg_match('/\/((?:[a-z]{2,3}\s+)*)(.*)\//i', $indi_name, $match)) {
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
			$name_fields['SURN']=$name_bits[9];
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
	$person = WT_Person::getInstance($pid);
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
	if ($namerec!='' && $namerec!="NEW") {
		$gedlines = explode("\n", $namerec); // -- find the number of lines in the record
		$fields = explode(' ', $gedlines[0]);
		$glevel = $fields[0];
		$level = $glevel;
		$type = trim($fields[1]);
		$level1type = $type;
		$tags=array();
		$i = 0;
		do {
			if (!isset($name_fields[$type]) && !isset($adv_name_fields[$type])) {
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
		} while (($level>$glevel)&&($i<count($gedlines))&&($type!='TYPE'));
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
	if (WT_USER_IS_ADMIN) {
		echo "<tr><td class=\"descriptionbox wrap width25\">";
		echo WT_Gedcom_Tag::getLabel('CHAN'), "</td><td class=\"optionbox wrap\">";
		if ($NO_UPDATE_CHAN) {
			echo "<input type=\"checkbox\" checked=\"checked\" name=\"preserve_last_changed\">";
		} else {
			echo "<input type=\"checkbox\" name=\"preserve_last_changed\">";
		}
		echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN'), "<br>";
		if (isset($famrec)) {
			$event = new WT_Event(get_sub_record(1, "1 CHAN", $famrec), null, 0);
			echo format_fact_date($event, new WT_Person(''), false, true);
		}
		echo "</td></tr>";
	}
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
	echo "<input type=\"submit\" value=\"", WT_I18N::translate('Save'), "\">";
	if (preg_match('/^add(child|spouse|newparent|source)/', $nextaction)) {
		echo "<input type=\"submit\" value=\"", WT_I18N::translate('Save and go to new record'), "\" onclick=\"document.addchildform.goto.value='new';\">";
	}
	echo "</form>";
	?>
	<script>
	function trim(str) {
		str=str.replace(/\s\s+/g, " ");
		return str.replace(/(^\s+)|(\s+$)/g, '');
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
		<?php if ($SURNAME_TRADITION=='polish' && ($sextag=='F' || $famtag=='WIFE')) { ?>
			surn=surn.replace(/ski$/, 'ska');
			surn=surn.replace(/cki$/, 'cka');
			surn=surn.replace(/dzki$/, 'dzka');
			surn=surn.replace(/żki$/, 'żka');
		<?php } ?>
		// Commas are used in the GIVN and SURN field to separate lists of surnames.
		// For example, to differentiate the two Spanish surnames from an English
		// double-barred name.
		// Commas *may* be used in other fields, and will form part of the NAME.
		if (WT_LOCALE=='vi' || WT_LOCALE=='hu') {
			// Default format: /SURN/ GIVN
			return trim(npfx+" /"+trim(spfx+" "+surn).replace(/ *, */, " ")+"/ "+givn.replace(/ *, */, " ")+" "+nsfx);
		} else if (WT_LOCALE=='zh') {
			// Default format: /SURN/GIVN
			return trim(npfx+" /"+trim(spfx+" "+surn).replace(/ *, */, " ")+"/"+givn.replace(/ *, */, " ")+" "+nsfx);
		} else {
			// Default format: GIVN /SURN/
			return trim(npfx+" "+givn.replace(/ *, */, " ")+" /"+trim(spfx+" "+surn).replace(/ *, */, " ")+"/ "+nsfx);
		}
	}

	// Update the NAME and _MARNM fields from the name components
	// and also display the value in read-only "gedcom" format.
	function updatewholename() {
		// don't update the name if the user manually changed it
		if (manualChange) return;
		// Update NAME field from components and display it
		var frm =document.forms[0];
		var npfx=frm.NPFX.value;
		var givn=frm.GIVN.value;
		var spfx=frm.SPFX.value;
		var surn=frm.SURN.value;
		var nsfx=frm.NSFX.value;
		document.getElementById('NAME').value=generate_name();
		document.getElementById('NAME_display').innerHTML=frm.NAME.value;
		// Married names inherit some NSFX values, but not these
		nsfx=nsfx.replace(/^(I|II|III|IV|V|VI|Junior|Jr\.?|Senior|Sr\.?)$/i, '');
		// Update _MARNM field from _MARNM_SURN field and display it
		// Be careful of mixing latin/hebrew/etc. character sets.
		var ip=document.getElementsByTagName('input');
		var marnm_id='';
		var romn='';
		var heb='';
		for (var i=0; i<ip.length; i++) {
			var val=ip[i].value;
			if (ip[i].id.indexOf("_HEB")==0)
				heb=val;
			if (ip[i].id.indexOf("ROMN")==0)
				romn=val;
			if (ip[i].id.indexOf("_MARNM")==0) {
				if (ip[i].id.indexOf("_MARNM_SURN")==0) {
					var msurn='';
					if (val!='') {
						var lc=lang_class(document.getElementById(ip[i].id).value);
						if (lang_class(frm.NAME.value)==lc)
							msurn=trim(npfx+" "+givn+" /"+val+"/ "+nsfx);
						else if (lc=="hebrew")
							msurn=heb.replace(/\/.*\//, '/'+val+'/');
						else if (lang_class(romn)==lc)
							msurn=romn.replace(/\/.*\//, '/'+val+'/');
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
				// IE doesn't allow changing the "type" of an input field so we'll cludge it ( silly :P)
				if (IE) {
					var newInput = document.createElement('input');
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
					delement.style.display='none';
					// force FF ui to update the display
					if (delement.innerHTML != oldName) {
						oldName = delement.innerHTML;
						element.value = oldName;
					}
				}
			}
			else {
				manualChange = false;
				// IE doesn't allow changing the "type" of an input field so we'll cludge it ( silly :P)
				if (IE) {
					var newInput = document.createElement('input');
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
					delement.style.display='inline';
				}
			}
		}
	}

	/**
	* if the user manually changed the NAME field, then update the textual
	* HTML representation of it
	* If the value changed set manualChange to true so that changing
	* the other fields doesn't change the NAME line
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
		var ip=document.getElementsByTagName('input');
		for (var i=0; i<ip.length; i++) {
			// ADD slashes to _HEB and _AKA names
			if (ip[i].id.indexOf('_AKA')==0 || ip[i].id.indexOf('_HEB')==0 || ip[i].id.indexOf('ROMN')==0)
				if (ip[i].value.indexOf('/')<0 && ip[i].value!='')
					ip[i].value=ip[i].value.replace(/([^\s]+)\s*$/, "/$1/");
			// Blank out temporary _MARNM_SURN
			if (ip[i].id.indexOf("_MARNM_SURN")==0)
					ip[i].value='';
			// Convert "xxx yyy" and "xxx y yyy" surnames to "xxx,yyy"
			if ('<?php echo $SURNAME_TRADITION; ?>'=='spanish' || '<?php echo $SURNAME_TRADITION; ?>'=='portuguese')
				if (ip[i].id.indexOf("SURN")==0) ip[i].value=document.forms[0].SURN.value.replace(/^\s*([^\s,]{2,})\s+([iIyY] +)?([^\s,]{2,})\s*$/, "$1,$3");
		}
		return true;
	}

	// If the name isn't initially formed from the components in a standard way,
	// then don't automatically update it.
	if (document.getElementById("NAME").value!=generate_name() && document.getElementById("NAME").value!="//") convertHidden("NAME");
	</script>
	<?php
}

// generates javascript code for calendar popup in user's language
function print_calendar_popup($id) {
	return 
		' <a href="#" onclick="cal_toggleDate(\'caldiv'.$id.'\', \''.$id.'\'); return false;" class="icon-button_calendar" title="'.WT_I18N::translate('Select a date').'"></a>'.
		'<div id="caldiv'.$id.'" style="position:absolute;visibility:hidden;background-color:white;layer-background-color:white; z-index: 1000;"></div>';
}

function print_addnewmedia_link($element_id) {
	return '<a href="#" onclick="pastefield=document.getElementById(\''.$element_id.'\'); window.open(\'addmedia.php?action=showmediaform&amp;linktoid={$linkToID}&amp;level={$level}\', \'_blank\', edit_window_specs); return false;" class="icon-button_addmedia" title="'.WT_I18N::translate('Add a new media object').'"></a>';
}

function print_addnewrepository_link($element_id) {
	return '<a href="#" onclick="addnewrepository(document.getElementById(\''.$element_id.'\')); return false;" class="icon-button_addrepository" title="'.WT_I18N::translate('Create Repository').'"></a>';
}

function print_addnewnote_link($element_id) {
	return '<a href="#" onclick="addnewnote(document.getElementById(\''.$element_id.'\')); return false;" class="icon-button_addnote" title="'.WT_I18N::translate('Create a new Shared Note').'"></a>';
}

/// Used in GEDFact CENS assistant
function print_addnewnote_assisted_link($element_id, $pid) {
	return '<a href="#" onclick="addnewnote_assisted(document.getElementById(\''.$element_id.'\'), \''.$pid.'\'); return false;">'.WT_I18N::translate('Create a new Shared Note using Assistant').'</a>';
}

function print_editnote_link($note_id) {
	return '<a href="#" onclick="var win02=window.open(\'edit_interface.php?action=editnote&amp;pid='.$note_id.'\', \'win02\', edit_window_specs);" class="icon-button_note" title="'.WT_I18N::translate('Edit Shared Note').'"></a>';
}

function print_addnewsource_link($element_id) {
	return '<a href="#" onclick="addnewsource(document.getElementById(\''.$element_id.'\')); return false;" class="icon-button_addsource" title="'.WT_I18N::translate('Create a new source').'"></a>';
}

/**
* add a new tag input field
*
* called for each fact to be edited on a form.
* Fact level=0 means a new empty form : data are POSTed by name
* else data are POSTed using arrays :
* glevels[] : tag level
*  islink[] : tag is a link
*     tag[] : tag name
*    text[] : tag value
*
* @param string $tag fact record to edit (eg 2 DATE xxxxx)
* @param string $upperlevel optional upper level tag (eg BIRT)
* @param string $label An optional label to echo instead of the default
* @param string $readOnly optional, when "READONLY", fact data can't be changed
* @param string $noClose optional, when "NOCLOSE", final "</td></tr>" won't be printed
* (so that additional text can be printed in the box)
* @param boolean $rowDisplay True to have the row displayed by default, false to hide it by default
*/
function add_simple_tag($tag, $upperlevel='', $label='', $readOnly='', $noClose='', $rowDisplay=true) {
	global $MEDIA_DIRECTORY, $tags, $emptyfacts, $main_fact, $TEXT_DIRECTION;
	global $NPFX_accept, $SPFX_accept, $NSFX_accept, $FILE_FORM_accept, $upload_count;
	global $pid, $gender, $linkToID, $bdm, $action, $event_add, $CensDate;
	global $QUICK_REQUIRED_FACTS, $QUICK_REQUIRED_FAMFACTS, $PREFER_LEVEL2_SOURCES;

	if (substr($tag, 0, strpos($tag, "CENS"))) {
		$event_add="census_add";
	}

	if (substr($tag, 0, strpos($tag, "PLAC"))) {
		?>
	<script>
		function valid_lati_long(field, pos, neg) {
			// valid LATI or LONG according to Gedcom standard
			// pos (+) : N or E
			// neg (-) : S or W
			txt=field.value.toUpperCase();
			txt=txt.replace(/(^\s*)|(\s*$)/g, ''); // trim
			txt=txt.replace(/ /g, ':'); // N12 34 ==> N12.34
			txt=txt.replace(/\+/g, ''); // +17.1234 ==> 17.1234
			txt=txt.replace(/-/g, neg); // -0.5698 ==> W0.5698
			txt=txt.replace(/,/g, '.'); // 0,5698 ==> 0.5698
			// 0�34'11 ==> 0:34:11
			txt=txt.replace(/\uB0/g, ':'); // �
			txt=txt.replace(/\u27/g, ':'); // '
			// 0:34:11.2W ==> W0.5698
			txt=txt.replace(/^([0-9]+):([0-9]+):([0-9.]+)(.*)/g, function($0, $1, $2, $3, $4) { var n=parseFloat($1); n+=($2/60); n+=($3/3600); n=Math.round(n*1E4)/1E4; return $4+n; });
			// 0:34W ==> W0.5667
			txt=txt.replace(/^([0-9]+):([0-9]+)(.*)/g, function($0, $1, $2, $3) { var n=parseFloat($1); n+=($2/60); n=Math.round(n*1E4)/1E4; return $3+n; });
			// 0.5698W ==> W0.5698
			txt=txt.replace(/(.*)([N|S|E|W]+)$/g, '$2$1');
			// 17.1234 ==> N17.1234
			if (txt!='' && txt.charAt(0)!=neg && txt.charAt(0)!=pos) txt=pos+txt;
			field.value = txt;
		}
		</script>
		<?php
	}
	if (!isset($noClose) && isset($readOnly) && $readOnly=="NOCLOSE") {
		$noClose = "NOCLOSE";
		$readOnly = '';
	}

	if (!isset($noClose) || $noClose!="NOCLOSE") $noClose = '';
	if (!isset($readOnly) || $readOnly!="READONLY") $readOnly = '';

	if (empty($linkToID)) $linkToID = $pid;

	$subnamefacts = array("NPFX", "GIVN", "SPFX", "SURN", "NSFX", "_MARNM_SURN");
	preg_match('/^(?:(\d+) ('.WT_REGEX_TAG.') ?(.*))/', $tag, $match);
	list(, $level, $fact, $value) = $match;

	// element name : used to POST data
	if ($level==0) {
		if ($upperlevel) $element_name=$upperlevel."_".$fact; // ex: BIRT_DATE | DEAT_DATE | ...
		else $element_name=$fact; // ex: OCCU
	} else $element_name="text[]";
	if ($level==1) $main_fact=$fact;

	// element id : used by javascript functions
	if ($level==0) $element_id=$fact; // ex: NPFX | GIVN ...
	else $element_id=$fact.(int)(microtime()*1000000); // ex: SOUR56402
	if ($upperlevel) $element_id=$upperlevel."_".$fact; // ex: BIRT_DATE | DEAT_DATE ...

	// field value
	$islink = (substr($value, 0, 1)=="@" and substr($value, 0, 2)!="@#");
	if ($islink) {
		$value=trim(trim(substr($tag, strlen($fact)+3)), " @\r");
	} else { 
		$value=trim(substr($tag, strlen($fact)+3));
	}
	if ($fact=='REPO' || $fact=='SOUR' || $fact=='OBJE' || $fact=='FAMC')
		$islink = true;

	// rows & cols
	switch ($fact) {
	case 'FORM':
		if ($upperlevel=='OBJE') {
			// FILE:FORM
			$rows=1;
			$cols=5;
		} else {
			// FACT:PLAC:FORM
			$rows=1;
			$cols=40;
		}
		break;
	case 'LATI': case 'LONG': case 'NPFX': case 'SPFX': case 'NSFX':
		$rows=1;
		$cols=12;
		break;
	case 'DATE':
		$rows=1;
		$cols=30;
		break;
	case 'TIME': case 'TYPE':
		$rows=1;
		$cols=20;
		break;
	case 'GIVN': case 'SURN': case '_MARNM':
		$rows=1;
		$cols=25;
		break;
	case '_UID':
		$rows=1;
		$cols=50;
		break;
	case 'TEXT': case 'PUBL':
		$rows=10;
		$cols=70;
		break;
	case 'SHARED_NOTE_EDIT':
		$islink=1;
		$fact="NOTE";
		$rows=15;
		$cols=88;
		break;
	case 'SHARED_NOTE':
		$islink=1;
		$fact="NOTE";
		$rows=1;
		$cols=($islink ? 8 : 40);
		break;
	case 'NOTE':
		if ($islink) {
			$rows=1;
			$cols=($islink ? 8 : 40);
			break;
		} else {
			$rows=10;
			$cols=70;
			break;
		}
	case 'ADDR':
		$rows=4;
		$cols=40;
		break;
	case 'PAGE':
		$rows=1;
		$cols=50;
		break;
	default:
		$rows=1;
		$cols=($islink ? 8 : 40);
		break;
	}

	// label
	echo "<tr id=\"", $element_id, "_tr\" ";
	if ($fact=="MAP" || ($fact=="LATI" || $fact=="LONG") && $value=='') {
		echo " style=\"display:none;\"";
	}
	echo " >";

	if (in_array($fact, $subnamefacts) || $fact=="LATI" || $fact=="LONG") {
		echo "<td class=\"optionbox wrap width25\">";
	} else {
		echo "<td class=\"descriptionbox wrap width25\">";
	}


	if (WT_DEBUG) {
		echo $element_name, "<br>";
	}


	// tag name
	if (!empty($label)) {
		if ($label=="Note" && $islink) {
			echo WT_I18N::translate('Shared note');
		} else {
			echo $label;
		}
	} else {
		if ($fact=="NOTE" && $islink) {
			echo WT_Gedcom_Tag::getLabel('SHARED_NOTE');
			/*
			if ($pid && $label=='GEDFact Assistant' && array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
				// use $label (GEDFact Assistant);
			} else {
				echo WT_I18N::translate('Shared note');
			}
			*/
		} else {
			echo WT_Gedcom_Tag::getLabel($fact);
		}
	}

// help link
	// If using GEDFact-assistant window
	if ($action=="addnewnote_assisted") {
		// Do not print on GEDFact Assistant window
	} else {
		// Not all facts have help text.
		switch ($fact) {
		case 'FORM':
			if ($upperlevel!='OBJE') {
				echo help_link($fact);
			}
			break;
		case 'NOTE':
			if ($islink) {
				echo help_link('edit_add_SHARED_NOTE');
			} else {
				echo help_link($fact);
			}
			break;
		case 'NAME':
			if ($upperlevel=='INDI') {
				echo help_link($fact);
			}
			break;
		case 'ABBR':
		case 'ADDR':
		case 'AGNC':
		case 'ASSO':
		case 'CAUS':
		case 'CEME':
		case 'DATE':
		case 'EMAI':
		case 'EMAIL':
		case 'EMAL':
		case '_EMAIL':
		case 'FAX':
		case 'GIVN':
		case 'NICK':
		case 'NPFX':
		case 'NSPX':
		case 'OBJE':
		case 'PAGE':
		case 'PEDI':
		case 'PHON':
		case 'PLAC':
		case 'QUAY':
		case 'RELA':
		case 'RESN':
		case 'ROMN':
		case 'SEX':
		case 'SOUR':
		case 'SPFX':
		case 'STAT':
		case 'SURN':
		case 'TEMP':
		case 'TEXT':
		case 'TIME':
		case 'TITL':
		case 'TYPE':
		case 'URL':
		case '_HEB':
		case '_MARNM':
		case '_PRIM':
			echo help_link($fact);
			break;
		}
	}
	// tag level
	if ($level>0) {
		if ($fact=="TEXT" and $level>1) {
			echo "<input type=\"hidden\" name=\"glevels[]\" value=\"", $level-1, "\">";
			echo "<input type=\"hidden\" name=\"islink[]\" value=\"0\">";
			echo "<input type=\"hidden\" name=\"tag[]\" value=\"DATA\">";
			//-- leave data text[] value empty because the following TEXT line will
			//--- cause the DATA to be added
			echo "<input type=\"hidden\" name=\"text[]\" value=\"\">";
		}
		echo "<input type=\"hidden\" name=\"glevels[]\" value=\"", $level, "\">";
		echo "<input type=\"hidden\" name=\"islink[]\" value=\"", $islink, "\">";
		echo "<input type=\"hidden\" name=\"tag[]\" value=\"", $fact, "\">";
	}
	echo "</td>";

	// value
	echo "<td class=\"optionbox wrap\">";
	if (WT_DEBUG) {
		echo $tag, "<br>";
	}

	// retrieve linked NOTE
	if ($fact=="NOTE" && $islink) {		
		$note1=WT_Note::getInstance($value);
		if ($note1) {
			$noterec=$note1->getGedcomRecord();
			preg_match("/$value/i", $noterec, $notematch);
			$value=$notematch[0];
		}		
	}

	if (in_array($fact, $emptyfacts) && ($value=='' || $value=='Y' || $value=='y')) {
		echo "<input type=\"hidden\" id=\"", $element_id, "\" name=\"", $element_name, "\" value=\"", $value, "\">";
		if ($level<=1) {
			echo '<input type="checkbox" ';
			if ($value) {
				echo ' checked="checked"';
			}
			echo " onclick=\"if (this.checked) ", $element_id, ".value='Y'; else ", $element_id, ".value=''; \">";
			echo WT_I18N::translate('yes');
		}
/*
		// If GEDFAct_assistant/_CENS/ module exists && we are on the INDI page && action is ADD a new CENS event
		// Then show the add Shared note input field and the GEDFact assisted icon.
		// If GEDFAct_assistant/_CENS/ module not installed  ... do not show
		if ($pid && $fact=='CENS' && array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
			$type_pid=GedcomRecord::getInstance($pid);
			if ($type_pid->getType()=="INDI" && $action=="add" ) {
				add_simple_tag("2 SHARED_NOTE", "", "GEDFact Assistant");
			}
		}
		// -----------------------------------------------------------------------------------------------------
*/

	} else if ($fact=="TEMP") {
		echo select_edit_control($element_name, WT_Gedcom_Code_Temp::templeNames(), WT_I18N::translate('No Temple - Living Ordinance'), $value);
	} else if ($fact=="ADOP") {
		switch ($gender) {
		case 'M': echo edit_field_adop_m($element_name, $value); break;
		case 'F': echo edit_field_adop_f($element_name, $value); break;
		default:  echo edit_field_adop_u($element_name, $value); break;
		}
	} else if ($fact=="PEDI") {
		switch ($gender) {
		case 'M': echo edit_field_pedi_m($element_name, $value); break;
		case 'F': echo edit_field_pedi_f($element_name, $value); break;
		default:  echo edit_field_pedi_u($element_name, $value); break;
		}
	} else if ($fact=='STAT') {
		echo select_edit_control($element_name, WT_Gedcom_Code_Stat::statusNames($upperlevel), '', $value);
	} else if ($fact=='RELA') {
		echo edit_field_rela($element_name, strtolower($value));
	} else if ($fact=='_WT_USER') {
		echo edit_field_username($element_name, $value);
	} else if ($fact=='RESN') {
		echo edit_field_resn($element_name, $value);
	} else if ($fact=='_PRIM') {
		echo '<select id="', $element_id, '" name="', $element_name, '" >';
		echo '<option value=""></option>';
		echo '<option value="Y"';
		if ($value=='Y') echo ' selected="selected"';
		echo '>', WT_I18N::translate('yes'), '</option>';
		echo '<option value="N"';
		if ($value=='N') echo ' selected="selected"';
		echo '>', WT_I18N::translate('no'), '</option>';
		echo '</select>';
	} else if ($fact=='SEX') {
		echo '<select id="', $element_id, '" name="', $element_name, '"><option value="M"';
		if ($value=='M') echo ' selected="selected"';
		echo '>', WT_I18N::translate('Male'), '</option><option value="F"';
		if ($value=='F') echo ' selected="selected"';
		echo '>', WT_I18N::translate('Female'), '</option><option value="U"';
		if ($value=='U' || empty($value)) echo ' selected="selected"';
		echo '>', WT_I18N::translate_c('unknown gender', 'Unknown'), '</option></select>';
	} else if ($fact == 'TYPE' && $level == '3') {
		//-- Build the selector for the Media 'TYPE' Fact
		echo '<select name="text[]"><option selected="selected" value="" ></option>';
		$selectedValue = strtolower($value);
		if (!array_key_exists($selectedValue, WT_Gedcom_Tag::getFileFormTypes())) {
			echo '<option selected="selected" value="', htmlspecialchars($value), '" >', htmlspecialchars($value), '</option>';
		}
		foreach (WT_Gedcom_Tag::getFileFormTypes() as $typeName => $typeValue) {
			echo '<option value="', $typeName, '"';
			if ($selectedValue == $typeName) {
				echo ' selected="selected"';
			}
			echo '>', $typeValue, '</option>';
		}
		echo '</select>';
	} else if (($fact=='NAME' && $upperlevel!='REPO') || $fact=='_MARNM') {
		// Populated in javascript from sub-tags
		echo "<input type=\"hidden\" id=\"", $element_id, "\" name=\"", $element_name, "\" onchange=\"updateTextName('", $element_id, "');\" value=\"", htmlspecialchars($value), "\">";
		echo '<span id="', $element_id, '_display" dir="auto">', htmlspecialchars($value), '</span>';
		echo ' <a href="#edit_name" onclick="convertHidden(\'', $element_id, '\'); return false;" class="icon-edit_indi" title="'.WT_I18N::translate('Edit name').'"></a>';
	} else {
		// textarea
		if ($rows>1) echo "<textarea id=\"", $element_id, "\" name=\"", $element_name, "\" rows=\"", $rows, "\" cols=\"", $cols, '" dir="auto">', htmlspecialchars($value), "</textarea><br>";
		else {
			// text
			// If using GEDFact-assistant window
			if ($action=="addnewnote_assisted") {
				echo "<input type=\"text\" id=\"", $element_id, "\" name=\"", $element_name, "\" value=\"", htmlspecialchars($value), "\" style=\"width:4.1em;\" dir=\"ltr\"";
			} else {
				echo "<input type=\"text\" id=\"", $element_id, "\" name=\"", $element_name, "\" value=\"", htmlspecialchars($value), "\" size=\"", $cols, "\" dir=\"ltr\"";
			}
			echo " class=\"{$fact}\"";
			if (in_array($fact, $subnamefacts)) echo " onblur=\"updatewholename();\" onkeyup=\"updatewholename();\"";
			if ($fact=="DATE") echo " onblur=\"valid_date(this);\" onmouseout=\"valid_date(this);\"";
			if ($fact=="LATI") echo " onblur=\"valid_lati_long(this, 'N', 'S');\" onmouseout=\"valid_lati_long(this, 'N', 'S');\"";
			if ($fact=="LONG") echo " onblur=\"valid_lati_long(this, 'E', 'W');\" onmouseout=\"valid_lati_long(this, 'E', 'W');\"";
			//if ($fact=="FILE") echo " onchange=\"if (updateFormat) updateFormat(this.value);\"";
			echo ' ', $readOnly, ">";
		}
		// split PLAC
		if ($fact=="PLAC" && $readOnly=='') {
			echo "<div id=\"", $element_id, "_pop\" style=\"display: inline;\">";
			echo print_specialchar_link($element_id), ' ', print_findplace_link($element_id);
			echo '</div>';
			echo '<a href="#" onclick="jQuery(\'#LATI_tr,#LONG_tr,#INDI_LATI_tr,#INDI_LONG_tr,tr[id^=LATI],tr[id^=LONG]\').toggle(\'fast\'); return false;" class="icon-target" title="', WT_Gedcom_Tag::getLabel('LATI'), ' / ', WT_Gedcom_Tag::getLabel('LONG'), '"></a>';
			if (array_key_exists('places_assistant', WT_Module::getActiveModules())) {
				places_assistant_WT_Module::setup_place_subfields($element_id);
				places_assistant_WT_Module::print_place_subfields($element_id);
			}
		} elseif (($cols>20 || $fact=="NPFX") && $readOnly=='') {
			echo print_specialchar_link($element_id);
		}
	}
	// MARRiage TYPE : hide text field and show a selection list
	if ($fact=='TYPE' && $level==2 && $tags[0]=='MARR') {
		echo '<script>';
		echo "document.getElementById('", $element_id, "').style.display='none'";
		echo '</script>';
		echo "<select id=\"", $element_id, "_sel\" onchange=\"document.getElementById('", $element_id, "').value=this.value;\" >";
		foreach (array("Unknown", "Civil", "Religious", "Partners") as $indexval => $key) {
			if ($key=="Unknown") echo "<option value=\"\"";
			else echo "<option value=\"", $key, "\"";
			$a=strtolower($key);
			$b=strtolower($value);
			if (@strpos($a, $b)!==false or @strpos($b, $a)!==false) echo " selected=\"selected\"";
			$tmp="MARR_".strtoupper($key);
			echo ">", WT_Gedcom_Tag::getLabel($tmp), "</option>";
		}
		echo "</select>";
	}
	// NAME TYPE : hide text field and show a selection list
	else if ($fact=='TYPE' && $level==0) {
		$extra = 'onchange="document.getElementById(\''.$element_id.'\').value=this.value;"';
		switch (WT_Person::getInstance($pid)->getSex()) {
			case 'M': echo edit_field_name_type_m($element_name, $value, $extra); break;
			case 'F': echo edit_field_name_type_f($element_name, $value, $extra); break;
			default:  echo edit_field_name_type_u($element_name, $value, $extra); break;
		}
		echo '<script>';
		echo "document.getElementById('", $element_id, "').style.display='none';";
		echo '</script>';
	}

	// popup links
	if ($readOnly=='') {
		if ($fact=='DATE') {
			echo print_calendar_popup($element_id);
			// If GEDFact_assistant/_CENS/ module is installed -------------------------------------------------
			if ($action=='add' && array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
				if (isset($CensDate) && $CensDate=="yes") {
					require_once WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/_CENS/census_asst_date.php';
				}
			}
			// -------------------------------------------------------------------------------------------------
		}
		if ($fact=="FAMC") echo print_findfamily_link($element_id);
		if ($fact=="FAMS") echo print_findfamily_link($element_id);
		if ($fact=="ASSO") echo print_findindi_link($element_id);
		if ($fact=="FILE") print_findmedia_link($element_id, "0file");
		if ($fact=="SOUR") {
			echo print_findsource_link($element_id), ' ', print_addnewsource_link($element_id);
			//-- checkboxes to apply '1 SOUR' to BIRT/MARR/DEAT as '2 SOUR'
			if ($level==1) {
				echo '<br>';
				if ($PREFER_LEVEL2_SOURCES==='0') {
					$level1_checked='';
					$level2_checked='';
				} else if ($PREFER_LEVEL2_SOURCES==='1' || $PREFER_LEVEL2_SOURCES===true) {
					$level1_checked='';
					$level2_checked=' checked="checked"';
				} else {
					$level1_checked=' checked="checked"';
					$level2_checked='';

				}
				if (strpos($bdm, 'B')!==false) {
					echo '&nbsp;<input type="checkbox" name="SOUR_INDI" ', $level1_checked, ' value="Y">';
					echo WT_I18N::translate('Individual');
					if (preg_match_all('/('.WT_REGEX_TAG.')/', $QUICK_REQUIRED_FACTS, $matches)) {
						foreach ($matches[1] as $match) {
							if (!in_array($match, explode('|', WT_EVENTS_DEAT))) {
								echo '&nbsp;<input type="checkbox" name="SOUR_', $match, '"', $level2_checked, ' value="Y">';
								echo WT_Gedcom_Tag::getLabel($match);
							}
						}
					}
				}
				if (strpos($bdm, 'D')!==false) {
					if (preg_match_all('/('.WT_REGEX_TAG.')/', $QUICK_REQUIRED_FACTS, $matches)) {
						foreach ($matches[1] as $match) {
							if (in_array($match, explode('|', WT_EVENTS_DEAT))) {
								echo '&nbsp;<input type="checkbox" name="SOUR_', $match, '"', $level2_checked, ' value="Y">';
								echo WT_Gedcom_Tag::getLabel($match);
							}
						}
					}
				}
				if (strpos($bdm, 'M')!==false) {
					echo '&nbsp;<input type="checkbox" name="SOUR_FAM" ', $level1_checked, ' value="Y">';
					echo WT_I18N::translate('Family');
					if (preg_match_all('/('.WT_REGEX_TAG.')/', $QUICK_REQUIRED_FAMFACTS, $matches)) {
						foreach ($matches[1] as $match) {
							echo '&nbsp;<input type="checkbox" name="SOUR_', $match, '"', $level2_checked, ' value="Y">';
							echo WT_Gedcom_Tag::getLabel($match);
						}
					}
				}
			}
		}
		if ($fact=="REPO") {
			echo print_findrepository_link($element_id), ' ', print_addnewrepository_link($element_id);
		}

		// Shared Notes Icons ========================================
		if ($fact=="NOTE" && $islink) {
			// Print regular Shared Note icons ---------------------------
			echo ' ', print_findnote_link($element_id), ' ', print_addnewnote_link($element_id);
			if ($value) {
				echo ' ', print_editnote_link($value);
			}
			// If GEDFact_assistant/_CENS/ module exists && we are on the INDI page and the action is a GEDFact CENS assistant addition.
			// Then show the add Shared note assisted icon, if not  ... show regular Shared note icons.
			if (($action=='add' || $action=='edit') && $pid && array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
				// Check if a CENS event ---------------------------
				if ($event_add=="census_add") {
					$type_pid=WT_GedcomRecord::getInstance($pid);
					if ($type_pid->getType()=="INDI" ) {
						echo '<br>', print_addnewnote_assisted_link($element_id, $pid);
					}
				}
			}
		}

		if ($fact=="OBJE") {
			echo print_findmedia_link($element_id, '1media');
		}
		if ($fact=="OBJE" && !$value) {
			echo ' ', print_addnewmedia_link($element_id);
			$value = "new";
		}

		echo "<br>";
	}

	// current value
	if ($fact=="DATE") {
		$date=new WT_Date($value);
		echo $date->Display(false);
	}
	if (($fact=="ASSO" || $fact=="SOUR" || $fact=="OBJE" || ($fact=="NOTE" && $islink)) && $value) {
		$record=WT_GedcomRecord::getInstance($value);
		if ($record) {
			echo ' ', $record->getFullName();
		}
		else if ($value!="new") {
			echo ' ', $value;
		}
	}
	// pastable values
	if ($readOnly=='') {
		if ($fact=="SPFX") print_autopaste_link($element_id, $SPFX_accept, true);
		if ($fact=="NSFX") print_autopaste_link($element_id, $NSFX_accept, true);
		if ($fact=="FORM" && $upperlevel=='OBJE') print_autopaste_link($element_id, $FILE_FORM_accept, false);
	}

	if ($noClose != "NOCLOSE") echo "</td></tr>";

	return $element_id;
}

/**
* prints collapsable fields to add ASSO/RELA, SOUR, OBJE ...
*
* @param string $tag Gedcom tag name
*/
function print_add_layer($tag, $level=2, $printSaveButton=true) {
	global $MEDIA_DIRECTORY, $TEXT_DIRECTION, $gedrec, $FULL_SOURCES, $islink;

	if ($tag=='OBJE' && get_gedcom_setting(WT_GED_ID, 'MEDIA_UPLOAD') < WT_USER_ACCESS_LEVEL) {
		return;
	}

	if ($tag=="SOUR") {
		//-- Add new source to fact
		echo "<a href=\"#\" onclick=\"return expand_layer('newsource');\"><i id=\"newsource_img\" class=\"icon-plus\"></i> ", WT_I18N::translate('Add a new source citation'), "</a>";
		echo help_link('edit_add_SOUR');
		echo "<br>";
		echo "<div id=\"newsource\" style=\"display: none;\">";
		if ($printSaveButton) echo "<input type=\"submit\" value=\"", WT_I18N::translate('Save'), "\">";
		echo "<table class=\"facts_table\">";
		// 2 SOUR
		$source = "SOUR @";
		add_simple_tag("$level $source");
		// 3 PAGE
		$page = "PAGE";
		add_simple_tag(($level+1)." $page");
		// 3 DATA
		// 4 TEXT
		$text = "TEXT";
		add_simple_tag(($level+2)." $text");
		if ($FULL_SOURCES) {
			// 4 DATE
			add_simple_tag(($level+2)." DATE", '', WT_Gedcom_Tag::getLabel('DATA:DATE'));
			// 3 QUAY
			add_simple_tag(($level+1)." QUAY");
		}
		// 3 OBJE
		add_simple_tag(($level+1)." OBJE");
		// 3 SHARED_NOTE
		add_simple_tag(($level+1)." SHARED_NOTE");
		echo "</table></div>";
	}
	if ($tag=="ASSO" || $tag=="ASSO2") {
		//-- Add a new ASSOciate
		if ($tag=="ASSO") {
			echo "<a href=\"#\" onclick=\"return expand_layer('newasso');\"><i id=\"newasso_img\" class=\"icon-plus\"></i> ", WT_I18N::translate('Add a new associate'), "</a>";
			echo help_link('edit_add_ASSO');
			echo "<br>";
			echo "<div id=\"newasso\" style=\"display: none;\">";
		} else {
			echo "<a href=\"#\" onclick=\"return expand_layer('newasso2');\"><i id=\"newasso2_img\" class=\"icon-plus\"></i> ", WT_I18N::translate('Add a new associate'), "</a>";
			echo help_link('edit_add_ASSO');
			echo "<br>";
			echo "<div id=\"newasso2\" style=\"display: none;\">";
		}
		if ($printSaveButton) echo "<input type=\"submit\" value=\"", WT_I18N::translate('Save'), "\">";
		echo "<table class=\"facts_table\">";
		// 2 ASSO
		add_simple_tag(($level)." ASSO @");
		// 3 RELA
		add_simple_tag(($level+1)." RELA");
		// 3 NOTE
		add_simple_tag(($level+1)." NOTE");
		// 3 SHARED_NOTE
		add_simple_tag(($level+1)." SHARED_NOTE");
		echo "</table></div>";
	}
	if ($tag=="NOTE") {
		//-- Retrieve existing note or add new note to fact
		$text = '';
		echo "<a href=\"#\" onclick=\"return expand_layer('newnote');\"><i id=\"newnote_img\" class=\"icon-plus\"></i> ", WT_I18N::translate('Add a new note'), "</a>";
		echo help_link('edit_add_NOTE');
		echo "<br>";
		echo "<div id=\"newnote\" style=\"display: none;\">";
		if ($printSaveButton) echo "<input type=\"submit\" value=\"", WT_I18N::translate('Save'), "\">";
		echo "<table class=\"facts_table\">";
		// 2 NOTE
		add_simple_tag(($level)." NOTE ".$text);
		echo "</table></div>";
	}
	if ($tag=="SHARED_NOTE") {
		//-- Retrieve existing shared note or add new shared note to fact
		$text = '';
		echo "<a href=\"#\" onclick=\"return expand_layer('newshared_note');\"><i id=\"newshared_note_img\" class=\"icon-plus\"></i> ", WT_I18N::translate('Add a new shared note'), "</a>";
		echo help_link('edit_add_SHARED_NOTE');
		echo "<br>";
		echo "<div id=\"newshared_note\" style=\"display: none;\">";
		if ($printSaveButton) echo "<input type=\"submit\" value=\"", WT_I18N::translate('Save'), "\">";
		echo "<table class=\"facts_table\">";
		// 2 SHARED NOTE
		add_simple_tag(($level)." SHARED_NOTE ");

		echo "</table></div>";
	}
	if ($tag=="OBJE") {
		//-- Add new obje to fact
		echo "<a href=\"#\" onclick=\"return expand_layer('newobje');\"><i id=\"newobje_img\" class=\"icon-plus\"></i> ", WT_I18N::translate('Add a new media object'), "</a>";
		echo help_link('OBJE');
		echo "<br>";
		echo "<div id=\"newobje\" style=\"display: none;\">";
		if ($printSaveButton) echo "<input type=\"submit\" value=\"", WT_I18N::translate('Save'), "\">";
		echo "<table class=\"facts_table\">";
		add_simple_tag($level." OBJE");
		echo "</table></div>";
	}
	if ($tag=="RESN") {
		//-- Retrieve existing resn or add new resn to fact
		$text = '';
		echo "<a href=\"#\" onclick=\"return expand_layer('newresn');\"><i id=\"newresn_img\" class=\"icon-plus\"></i> ", WT_Gedcom_Tag::getLabel('RESN'), "</a>";
		echo help_link('RESN');
		echo "<br>";
		echo "<div id=\"newresn\" style=\"display: none;\">";
		if ($printSaveButton) echo "<input type=\"submit\" value=\"", WT_I18N::translate('Save'), "\">";
		echo "<table class=\"facts_table\">";
		// 2 RESN
		add_simple_tag(($level)." RESN ".$text);
		echo "</table></div>";
	}
}

// Add some empty tags to create a new fact
function addSimpleTags($fact) {
	global $ADVANCED_PLAC_FACTS;

	// For new individuals, these facts default to "Y"
	if ($fact=='MARR' /*|| $fact=='BIRT'*/) {
		add_simple_tag("0 {$fact} Y");
	} else {
		add_simple_tag("0 {$fact}");
	}
	add_simple_tag("0 DATE", $fact, WT_Gedcom_Tag::getLabel("{$fact}:DATE"));
	add_simple_tag("0 PLAC", $fact, WT_Gedcom_Tag::getLabel("{$fact}:PLAC"));

	if (preg_match_all('/('.WT_REGEX_TAG.')/', $ADVANCED_PLAC_FACTS, $match)) {
		foreach ($match[1] as $tag) {
			add_simple_tag("0 {$tag}", $fact, WT_Gedcom_Tag::getLabel("{$fact}:PLAC:{$tag}"));
		}
	}
	add_simple_tag("0 MAP", $fact);
	add_simple_tag("0 LATI", $fact);
	add_simple_tag("0 LONG", $fact);
}

// Assemble the pieces of a newly created record into gedcom
function addNewName() {
	global $ADVANCED_NAME_FACTS;

	$gedrec="\n1 NAME ".safe_POST('NAME', WT_REGEX_UNSAFE, '//');

	$tags=array('NPFX', 'GIVN', 'SPFX', 'SURN', 'NSFX');

	if (preg_match_all('/('.WT_REGEX_TAG.')/', $ADVANCED_NAME_FACTS, $match)) {
		$tags=array_merge($tags, $match[1]);
	}

	// Paternal and Polish and Lithuanian surname traditions can also create a _MARNM
	$SURNAME_TRADITION=get_gedcom_setting(WT_GED_ID, 'SURNAME_TRADITION');
	if ($SURNAME_TRADITION=='paternal' || $SURNAME_TRADITION=='polish' || $SURNAME_TRADITION=='lithuanian') {
		$tags[]='_MARNM';
	}

	foreach (array_unique($tags) as $tag) {
		$TAG=safe_POST($tag, WT_REGEX_UNSAFE);
		if ($TAG) {
			$gedrec.="\n2 {$tag} {$TAG}";
		}
	}
	return $gedrec;
}
function addNewSex() {
	switch (safe_POST('SEX', '[MF]', 'U')) {
	case 'M':
		return "\n1 SEX M";
	case 'F':
		return "\n1 SEX F";
	default:
		return "\n1 SEX U";
	}
}
function addNewFact($fact) {
	global $tagSOUR, $ADVANCED_PLAC_FACTS;

	$FACT=safe_POST($fact,          WT_REGEX_UNSAFE);
	$DATE=safe_POST("{$fact}_DATE", WT_REGEX_UNSAFE);
	$PLAC=safe_POST("{$fact}_PLAC", WT_REGEX_UNSAFE);
	if ($DATE || $PLAC || $FACT && $FACT!='Y') {
		if ($FACT && $FACT!='Y') {
			$gedrec="\n1 {$fact} {$FACT}";
		} else {
			$gedrec="\n1 {$fact}";
		}
		if ($DATE) {
			$gedrec.="\n2 DATE {$DATE}";
		}
		if ($PLAC) {
			$gedrec.="\n2 PLAC {$PLAC}";

			if (preg_match_all('/('.WT_REGEX_TAG.')/', $ADVANCED_PLAC_FACTS, $match)) {
				foreach ($match[1] as $tag) {
					$TAG=safe_POST("{$fact}_{$tag}", WT_REGEX_UNSAFE);
					if ($TAG) {
						$gedrec.="\n3 {$tag} {$TAG}";
					}
				}
			}
			$LATI=safe_POST("{$fact}_LATI", WT_REGEX_UNSAFE);
			$LONG=safe_POST("{$fact}_LONG", WT_REGEX_UNSAFE);
			if ($LATI || $LONG) {
				$gedrec.="\n3 MAP\n4 LATI {$LATI}\n4 LONG {$LONG}";
			}
		}
		if (safe_POST_bool("SOUR_{$fact}")) {
			return updateSOUR($gedrec, 2);
		} else {
			return $gedrec;
		}
	} elseif ($FACT=='Y') {
		if (safe_POST_bool("SOUR_{$fact}")) {
			return updateSOUR("\n1 {$fact} Y", 2);
		} else {
			return "\n1 {$fact} Y";
		}
	} else {
		return '';
	}
}

/**
* This function splits the $glevels, $tag, $islink, and $text arrays so that the
* entries associated with a SOUR record are separate from everything else.
*
* Input arrays:
* - $glevels[] - an array of the gedcom level for each line that was edited
* - $tag[] - an array of the tags for each gedcom line that was edited
* - $islink[] - an array of 1 or 0 values to indicate when the text is a link element
* - $text[] - an array of the text data for each line
*
* Output arrays:
* ** For the SOUR record:
* - $glevelsSOUR[] - an array of the gedcom level for each line that was edited
* - $tagSOUR[] - an array of the tags for each gedcom line that was edited
* - $islinkSOUR[] - an array of 1 or 0 values to indicate when the text is a link element
* - $textSOUR[] - an array of the text data for each line
* ** For the remaining records:
* - $glevelsRest[] - an array of the gedcom level for each line that was edited
* - $tagRest[] - an array of the tags for each gedcom line that was edited
* - $islinkRest[] - an array of 1 or 0 values to indicate when the text is a link element
* - $textRest[] - an array of the text data for each line
*
*/
function splitSOUR() {
	global $glevels, $tag, $islink, $text;
	global $glevelsSOUR, $tagSOUR, $islinkSOUR, $textSOUR;
	global $glevelsRest, $tagRest, $islinkRest, $textRest;

	$glevelsSOUR = array();
	$tagSOUR = array();
	$islinkSOUR = array();
	$textSOUR = array();

	$glevelsRest = array();
	$tagRest = array();
	$islinkRest = array();
	$textRest = array();

	$inSOUR = false;

	for ($i=0; $i<count($glevels); $i++) {
		if ($inSOUR) {
			if ($levelSOUR<$glevels[$i]) {
				$dest = "S";
			} else {
				$inSOUR = false;
				$dest = "R";
			}
		} else {
			if ($tag[$i]=="SOUR") {
				$inSOUR = true;
				$levelSOUR = $glevels[$i];
				$dest = "S";
			} else {
				$dest = "R";
			}
		}
		if ($dest=="S") {
			$glevelsSOUR[] = $glevels[$i];
			$tagSOUR[] = $tag[$i];
			$islinkSOUR[] = $islink[$i];
			$textSOUR[] = $text[$i];
		} else {
			$glevelsRest[] = $glevels[$i];
			$tagRest[] = $tag[$i];
			$islinkRest[] = $islink[$i];
			$textRest[] = $text[$i];
		}
	}
}

/**
* Add new GEDCOM lines from the $xxxSOUR interface update arrays, which
* were produced by the splitSOUR() function.
*
* See the handle_updates() function for details.
*
*/
function updateSOUR($inputRec, $levelOverride="no") {
	global $glevels, $tag, $islink, $text;
	global $glevelsSOUR, $tagSOUR, $islinkSOUR, $textSOUR;
	global $glevelsRest, $tagRest, $islinkRest, $textRest;

	if (count($tagSOUR)==0) return $inputRec; // No update required

	// Save original interface update arrays before replacing them with the xxxSOUR ones
	$glevelsSave = $glevels;
	$tagSave = $tag;
	$islinkSave = $islink;
	$textSave = $text;

	$glevels = $glevelsSOUR;
	$tag = $tagSOUR;
	$islink = $islinkSOUR;
	$text = $textSOUR;

	$myRecord = handle_updates($inputRec, $levelOverride); // Now do the update

	// Restore the original interface update arrays (just in case ...)
	$glevels = $glevelsSave;
	$tag = $tagSave;
	$islink = $islinkSave;
	$text = $textSave;

	return $myRecord;
}

/**
* Add new GEDCOM lines from the $xxxRest interface update arrays, which
* were produced by the splitSOUR() function.
*
* See the handle_updates() function for details.
*
*/
function updateRest($inputRec, $levelOverride="no") {
	global $glevels, $tag, $islink, $text;
	global $glevelsSOUR, $tagSOUR, $islinkSOUR, $textSOUR;
	global $glevelsRest, $tagRest, $islinkRest, $textRest;

	if (count($tagRest)==0) return $inputRec; // No update required

	// Save original interface update arrays before replacing them with the xxxRest ones
	$glevelsSave = $glevels;
	$tagSave = $tag;
	$islinkSave = $islink;
	$textSave = $text;

	$glevels = $glevelsRest;
	$tag = $tagRest;
	$islink = $islinkRest;
	$text = $textRest;

	$myRecord = handle_updates($inputRec, $levelOverride); // Now do the update

	// Restore the original interface update arrays (just in case ...)
	$glevels = $glevelsSave;
	$tag = $tagSave;
	$islink = $islinkSave;
	$text = $textSave;

	return $myRecord;
}

/**
* Add new gedcom lines from interface update arrays
* The edit_interface and add_simple_tag function produce the following
* arrays incoming from the $_POST form
* - $glevels[] - an array of the gedcom level for each line that was edited
* - $tag[] - an array of the tags for each gedcom line that was edited
* - $islink[] - an array of 1 or 0 values to tell whether the text is a link element and should be surrounded by @@
* - $text[] - an array of the text data for each line
* With these arrays you can recreate the gedcom lines like this
* <code>$glevel[0].' '.$tag[0].' '.$text[0]</code>
* There will be an index in each of these arrays for each line of the gedcom
* fact that is being edited.
* If the $text[] array is empty for the given line, then it means that the
* user removed that line during editing or that the line is supposed to be
* empty (1 DEAT, 1 BIRT) for example.  To know if the line should be removed
* there is a section of code that looks ahead to the next lines to see if there
* are sub lines.  For example we don't want to remove the 1 DEAT line if it has
* a 2 PLAC or 2 DATE line following it.  If there are no sub lines, then the line
* can be safely removed.
* @param string $newged the new gedcom record to add the lines to
* @param int $levelOverride Override GEDCOM level specified in $glevels[0]
* @return string The updated gedcom record
*/
function handle_updates($newged, $levelOverride="no") {
	global $glevels, $islink, $tag, $uploaded_files, $text, $NOTE, $WORD_WRAPPED_NOTES;

	if ($levelOverride=="no" || count($glevels)==0) $levelAdjust = 0;
	else $levelAdjust = $levelOverride - $glevels[0];

	for ($j=0; $j<count($glevels); $j++) {

		// Look for empty SOUR reference with non-empty sub-records.
		// This can happen when the SOUR entry is deleted but its sub-records
		// were incorrectly left intact.
		// The sub-records should be deleted.
		if ($tag[$j]=="SOUR" && ($text[$j]=="@@" || $text[$j]=='')) {
			$text[$j] = '';
			$k = $j+1;
			while (($k<count($glevels))&&($glevels[$k]>$glevels[$j])) {
				$text[$k] = '';
				$k++;
			}
		}

		if (trim($text[$j])!='') {
			$pass = true;
		}
		else {
			//-- for facts with empty values they must have sub records
			//-- this section checks if they have subrecords
			$k=$j+1;
			$pass=false;
			while (($k<count($glevels))&&($glevels[$k]>$glevels[$j])) {
				if ($text[$k]!='') {
					if (($tag[$j]!="OBJE")||($tag[$k]=="FILE")) {
						$pass=true;
						break;
					}
				}
				if (($tag[$k]=="FILE")&&(count($uploaded_files)>0)) {
					$filename = array_shift($uploaded_files);
					if (!empty($filename)) {
						$text[$k] = $filename;
						$pass=true;
						break;
					}
				}
				$k++;
			}
		}

		//-- if the value is not empty or it has sub lines
		//--- then write the line to the gedcom record
		//if ((($text[trim($j)]!='')||($pass==true)) && (strlen($text[$j]) > 0)) {
		//-- we have to let some emtpy text lines pass through... (DEAT, BIRT, etc)
		if ($pass==true) {
			$newline = $glevels[$j]+$levelAdjust.' '.$tag[$j];
			//-- check and translate the incoming dates
			if ($tag[$j]=="DATE" && $text[$j]!='') {
			}
			// echo $newline;
			if ($text[$j]!='') {
				if ($islink[$j]) $newline .= " @".$text[$j]."@";
				else $newline .= ' '.$text[$j];
			}
			$newged .= "\n".breakConts($newline);
		}
	}

	return $newged;
}

/**
* Link Media ID to Indi, Family, or Source ID
*
* Code was removed from inverselink.php to become a callable function
*
* @param  string  $mediaid Media ID to be linked
* @param string $linktoid Indi, Family, or Source ID that the Media ID should link to
* @param int $level Level where the Media Object reference should be created
* @param boolean $chan Whether or not to update/add the CHAN record
* @return  bool success or failure
*/
function linkMedia($mediaid, $linktoid, $level=1, $chan=true) {
	if (empty($level)) $level = 1;
	if ($level!=1) return false; // Level 2 items get linked elsewhere
	// find Indi, Family, or Source record to link to
	$gedrec = find_gedcom_record($linktoid, WT_GED_ID, true);

	//-- check if we are re-editing an unaccepted link that is not already in the DB
	if (strpos($gedrec, "1 OBJE @$mediaid@")!==false) return false;

	if ($gedrec) {
		$newrec = $gedrec."\n1 OBJE @".$mediaid."@";
		replace_gedrec($linktoid, WT_GED_ID, $newrec, $chan);
		return true;
	} else {
		echo "<br><center>", WT_I18N::translate('No such ID exists in this GEDCOM file.'), "</center>";
		return false;
	}
}

/**
* builds the form for adding new facts
* @param string $fact the new fact we are adding
*/
function create_add_form($fact) {
	global $tags, $FULL_SOURCES, $emptyfacts;

	$tags = array();

	// GEDFact_assistant ================================================
	if ($fact=="CENS") {
		global $TEXT_DIRECTION, $CensDate;
		$CensDate="yes";
	}
	// ==================================================================

	// handle  MARRiage TYPE
	if (substr($fact, 0, 5)=="MARR_") {
		$tags[0] = "MARR";
		add_simple_tag("1 MARR");
		insert_missing_subtags($fact);
	} else {
		$tags[0] = $fact;
		if ($fact=='_UID') {
			$fact.=' '.uuid();
		}
		// These new level 1 tags need to be turned into links
		if (in_array($fact, array('ASSO'))) {
			$fact.=' @';
		}
		if (in_array($fact, $emptyfacts)) {
			add_simple_tag('1 '.$fact.' Y');
		} else {
			add_simple_tag('1 '.$fact);
		}
		insert_missing_subtags($tags[0]);
		//-- handle the special SOURce case for level 1 sources [ 1759246 ]
		if ($fact=="SOUR") {
			add_simple_tag("2 PAGE");
			add_simple_tag("3 TEXT");
			if ($FULL_SOURCES) {
				add_simple_tag("3 DATE", '', WT_Gedcom_Tag::getLabel('DATA:DATE'));
				add_simple_tag("2 QUAY");
			}
		}
	}
}

/**
* creates the form for editing the fact within the given gedcom record at the
* given line number
* @param string $gedrec the level 0 gedcom record
* @param int $linenum the line number of the fact to edit within $gedrec
* @param string $level0type the type of the level 0 gedcom record
*/
function create_edit_form($gedrec, $linenum, $level0type) {
	global $WORD_WRAPPED_NOTES;
	global $pid, $tags, $ADVANCED_PLAC_FACTS, $date_and_time;
	global $FULL_SOURCES;

	$tags=array();
	$gedlines = explode("\n", $gedrec); // -- find the number of lines in the record
	if (!isset($gedlines[$linenum])) {
		echo "<span class=\"error\">", WT_I18N::translate('An error occurred while creating the Edit form.  Another user may have changed this record since you previously viewed it.'), "<br><br>";
		echo WT_I18N::translate('Please reload the previous page to make sure you are working with the most recent record.'), "</span>";
		return;
	}
	$fields = explode(' ', $gedlines[$linenum]);
	$glevel = $fields[0];
	$level = $glevel;

	if ($level!=1 && preg_match("~/@.*/@~i", trim($fields[1]))) {
		echo "<span class=\"error\">", WT_I18N::translate('An error occurred while creating the Edit form.  Another user may have changed this record since you previously viewed it.'), "<br><br>";
		echo WT_I18N::translate('Please reload the previous page to make sure you are working with the most recent record.'), "</span>";
		return;
	}

	$type = trim($fields[1]);
	$level1type = $type;

	// GEDFact_assistant ================================================
	if ($type=="CENS") {
		global $TEXT_DIRECTION, $CensDate;
		$CensDate="yes";
	}
	// ==================================================================

	if (count($fields)>2) {
		$ct = preg_match("/@.*@/", $fields[2]);
		$levellink = $ct > 0;
	} else {
		$levellink = false;
	}
	$i = $linenum;
	$inSource = false;
	$levelSource = 0;
	$add_date = true;
	// List of tags we would expect at the next level
	// NB add_missing_subtags() already takes care of the simple cases
	// where a level 1 tag is missing a level 2 tag.  Here we only need to
	// handle the more complicated cases.
	$expected_subtags=array(
		'SOUR'=>array('PAGE', 'DATA'),
		'DATA'=>array('TEXT'),
		'PLAC'=>array('MAP'),
		'MAP' =>array('LATI', 'LONG')
	);
	if ($FULL_SOURCES) {
		$expected_subtags['SOUR'][]='QUAY';
		$expected_subtags['DATA'][]='DATE';
	}
	if (preg_match_all('/('.WT_REGEX_TAG.')/', $ADVANCED_PLAC_FACTS, $match)) {
		$expected_subtags['PLAC']=array_merge($match[1], $expected_subtags['PLAC']);
	}

	$stack=array(0=>$level0type);
	// Loop on existing tags :
	while (true) {
		// Keep track of our hierarchy, e.g. 1=>BIRT, 2=>PLAC, 3=>FONE
		$stack[(int)$level]=$type;
		// Merge them together, e.g. BIRT:PLAC:FONE
		$label=implode(':', array_slice($stack, 1, $level));

		$text = '';
		for ($j=2; $j<count($fields); $j++) {
			if ($j>2) $text .= ' ';
			$text .= $fields[$j];
		}
		$text = rtrim($text);
		while (($i+1<count($gedlines))&&(preg_match("/".($level+1)." CONT ?(.*)/", $gedlines[$i+1], $cmatch)>0)) {
			$text.="\n".$cmatch[1];
			$i++;
		}

		if ($type=="SOUR") {
			$inSource = true;
			$levelSource = $level;
		} elseif ($levelSource>=$level) {
			$inSource = false;
		}

		if ($type!="DATA" && $type!="CONT") {
			$tags[]=$type;
			$person = WT_Person::getInstance($pid);
			$subrecord = $level.' '.$type.' '.$text;
			if ($inSource && $type=="DATE") {
				add_simple_tag($subrecord, '', WT_Gedcom_Tag::getLabel($label, $person));
			} elseif (!$inSource && $type=="DATE") {
				add_simple_tag($subrecord, $level1type, WT_Gedcom_Tag::getLabel($label, $person));
				$add_date = false;
			} elseif ($type=='STAT') {
				add_simple_tag($subrecord, $level1type, WT_Gedcom_Tag::getLabel($label, $person));
		 	} elseif ($level0type=='REPO') {
				$repo = WT_Repository::getInstance($pid);
				add_simple_tag($subrecord, $level0type, WT_Gedcom_Tag::getLabel($label, $repo));
			} else {
				add_simple_tag($subrecord, $level0type, WT_Gedcom_Tag::getLabel($label, $person));
			}
		}

		// Get a list of tags present at the next level
		$subtags=array();
		for ($ii=$i+1; isset($gedlines[$ii]) && preg_match('/^\s*(\d+)\s+(\S+)/', $gedlines[$ii], $mm) && $mm[1]>$level; ++$ii)
			if ($mm[1]==$level+1)
				$subtags[]=$mm[2];

		// Insert missing tags
		if (!empty($expected_subtags[$type])) {
			foreach ($expected_subtags[$type] as $subtag) {
				if (!in_array($subtag, $subtags)) {
					if (!$inSource || $subtag!="DATA") {
						add_simple_tag(($level+1).' '.$subtag, '', WT_Gedcom_Tag::getLabel("{$label}:{$subtag}"));
					}
					if (!empty($expected_subtags[$subtag])) {
						foreach ($expected_subtags[$subtag] as $subsubtag) {
							add_simple_tag(($level+2).' '.$subsubtag, '', WT_Gedcom_Tag::getLabel("{$label}:{$subtag}:{$subsubtag}"));
						}
					}
				}
			}
		}

		// Awkward special cases
		if ($level==2 && $type=='DATE' && in_array($level1type, $date_and_time) && !in_array('TIME', $subtags)) {
			add_simple_tag("3 TIME"); // TIME is NOT a valid 5.5.1 tag
		}
		if ($level==2 && $type=='STAT' && WT_Gedcom_Code_Temp::isTagLDS($level1type) && !in_array('DATE', $subtags)) {
			add_simple_tag("3 DATE", '', WT_Gedcom_Tag::getLabel('STAT:DATE'));
		}

		$i++;
		if (isset($gedlines[$i])) {
			$fields = explode(' ', $gedlines[$i]);
			$level = $fields[0];
			if (isset($fields[1])) {
				$type = trim($fields[1]);
			} else {
				$level = 0;
			}
		} else {
			$level = 0;
		}
		if ($level<=$glevel) break;
	}

	insert_missing_subtags($level1type, $add_date);
	return $level1type;
}

/**
* Populates the global $tags array with any missing sub-tags.
* @param string $level1tag the type of the level 1 gedcom record
*/
function insert_missing_subtags($level1tag, $add_date=false) {
	global $tags, $date_and_time, $level2_tags, $ADVANCED_PLAC_FACTS, $ADVANCED_NAME_FACTS;
	global $nondatefacts, $nonplacfacts;

	// handle  MARRiage TYPE
	$type_val = '';
	if (substr($level1tag, 0, 5)=="MARR_") {
		$type_val = substr($level1tag, 5);
		$level1tag = "MARR";
	}

	foreach ($level2_tags as $key=>$value) {
		if ($key=='DATE' && in_array($level1tag, $nondatefacts) || $key=='PLAC' && in_array($level1tag, $nonplacfacts)) {
			continue;
		}
		if (in_array($level1tag, $value) && !in_array($key, $tags)) {
			if ($key=="TYPE") {
				add_simple_tag("2 TYPE ".$type_val, $level1tag);
			} elseif ($level1tag=='_TODO' && $key=='DATE') {
				add_simple_tag("2 ".$key.' '.strtoupper(date('d M Y')), $level1tag);
			} elseif ($level1tag=='_TODO' && $key=='_WT_USER') {
				add_simple_tag("2 ".$key.' '.WT_USER_NAME, $level1tag);
			} else if ($level1tag=='TITL' && strstr($ADVANCED_NAME_FACTS, $key)!==false) {
				add_simple_tag("2 ".$key, $level1tag);
			} else if ($level1tag=='NAME' && strstr($ADVANCED_NAME_FACTS, $key)!==false) {
				add_simple_tag("2 ".$key, $level1tag);
			} else if ($level1tag!='TITL' && $level1tag!='NAME') {
				add_simple_tag("2 ".$key, $level1tag);
			}
			switch ($key) { // Add level 3/4 tags as appropriate
				case "PLAC":
					if (preg_match_all('/('.WT_REGEX_TAG.')/', $ADVANCED_PLAC_FACTS, $match)) {
						foreach ($match[1] as $tag) {
							add_simple_tag("3 $tag", '', WT_Gedcom_Tag::getLabel("{$level1tag}:PLAC:{$tag}"));
						}
					}
					add_simple_tag("3 MAP");
					add_simple_tag("4 LATI");
					add_simple_tag("4 LONG");
					break;
				case "FILE":
					add_simple_tag("3 FORM");
					break;
				case "EVEN":
					add_simple_tag("3 DATE");
					add_simple_tag("3 PLAC");
					break;
				case "STAT":
					if (WT_Gedcom_Code_Temp::isTagLDS($level1tag)) {
						add_simple_tag("3 DATE", '', WT_Gedcom_Tag::getLabel('STAT:DATE'));
					}
					break;
				case "DATE":
					if (in_array($level1tag, $date_and_time))
						add_simple_tag("3 TIME"); // TIME is NOT a valid 5.5.1 tag
					break;
				case "HUSB":
				case "WIFE":
					add_simple_tag("3 AGE");
					break;
				case "FAMC":
					if ($level1tag=='ADOP')
						add_simple_tag("3 ADOP BOTH");
					break;
			}
		} elseif ($key=="DATE" && $add_date) {
			add_simple_tag("2 DATE", $level1tag, WT_Gedcom_Tag::getLabel("{$level1tag}:DATE"));
		}
	}
	// Do something (anything!) with unrecognised custom tags
	if (substr($level1tag, 0, 1)=='_' && $level1tag!='_UID' && $level1tag!='_TODO')
		foreach (array('DATE', 'PLAC', 'ADDR', 'AGNC', 'TYPE', 'AGE') as $tag)
			if (!in_array($tag, $tags)) {
				add_simple_tag("2 {$tag}");
				if ($tag=='PLAC') {
					if (preg_match_all('/('.WT_REGEX_TAG.')/', $ADVANCED_PLAC_FACTS, $match)) {
						foreach ($match[1] as $tag) {
							add_simple_tag("3 $tag", '', WT_Gedcom_Tag::getLabel("{$level1tag}:PLAC:{$tag}"));
						}
					}
					add_simple_tag("3 MAP");
					add_simple_tag("4 LATI");
					add_simple_tag("4 LONG");
				}
			}
}
