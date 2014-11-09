<?php
// Various functions used by the Edit interface
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use Rhumsaa\Uuid\Uuid;
use WT\Auth;

/**
 * Create an edit control for inline editing using jeditable.
 *
 * @param string             $name
 * @param string             $value
 * @param WT_Controller_Base $controller
 *
 * @return string
 */
function edit_field_inline($name, $value, WT_Controller_Base $controller=null) {
	$html='<span class="editable" id="' . $name . '">' . WT_Filter::escapeHtml($value) . '</span>';
	$js='jQuery("#' . $name . '").editable("' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'save.php", {submitdata: {csrf: WT_CSRF_TOKEN}, submit:"&nbsp;&nbsp;' . /* I18N: button label */ WT_I18N::translate('save') . '&nbsp;&nbsp;", style:"inherit", placeholder: "'.WT_I18N::translate('click to edit').'"});';

	if ($controller) {
		$controller->addInlineJavascript($js);
		return $html;
	} else {
		// For AJAX callbacks
		return $html . '<script>' . $js . '</script>';
	}
}

/**
 * Create a text area for inline editing using jeditable.
 *
 * @param string             $name
 * @param string             $value
 * @param WT_Controller_Base $controller
 *
 * @return string
 */
function edit_text_inline($name, $value, WT_Controller_Base $controller=null) {
	$html='<span class="editable" style="white-space:pre-wrap;" id="' . $name . '">' . WT_Filter::escapeHtml($value) . '</span>';
	$js='jQuery("#' . $name . '").editable("' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'save.php", {submitdata: {csrf: WT_CSRF_TOKEN}, submit:"&nbsp;&nbsp;' . WT_I18N::translate('save') . '&nbsp;&nbsp;", style:"inherit", placeholder: "'.WT_I18N::translate('click to edit').'", type: "textarea", rows:4, cols:60 });';

	if ($controller) {
		$controller->addInlineJavascript($js);
		return $html;
	} else {
		// For AJAX callbacks
		return $html . '<script>' . $js . '</script>';
	}
}

/**
 * Create a <select> control for a form.
 *
 * @param string      $name
 * @param string[]    $values
 * @param string|null $empty
 * @param string      $selected
 * @param string      $extra
 *
 * @return string
 */
function select_edit_control($name, $values, $empty, $selected, $extra='') {
	if (is_null($empty)) {
		$html='';
	} else {
		if (empty($selected)) {
			$html='<option value="" selected="selected">'.WT_Filter::escapeHtml($empty).'</option>';
		} else {
			$html='<option value="">'.WT_Filter::escapeHtml($empty).'</option>';
		}
	}
	// A completely empty list would be invalid, and break various things
	if (empty($values) && empty($html)) {
		$html='<option value=""></option>';
	}
	foreach ($values as $key=>$value) {
		if ((string)$key===(string)$selected) { // Because "0" != ""
			$html.='<option value="'.WT_Filter::escapeHtml($key).'" selected="selected" dir="auto">'.WT_Filter::escapeHtml($value).'</option>';
		} else {
			$html.='<option value="'.WT_Filter::escapeHtml($key).'" dir="auto">'.WT_Filter::escapeHtml($value).'</option>';
		}
	}
	if (substr($name, -2)=='[]') {
		// id attribute is not used for arrays
		return '<select name="'.$name.'" '.$extra.'>'.$html.'</select>';
	} else {
		return '<select id="'.$name.'" name="'.$name.'" '.$extra.'>'.$html.'</select>';
	}
}

/**
 * An inline-editing version of select_edit_control()
 *
 * @param string             $name
 * @param string[]           $values
 * @param string|null        $empty
 * @param string             $selected
 * @param WT_Controller_Base $controller
 *
 * @return string
 */
function select_edit_control_inline($name, $values, $empty, $selected, WT_Controller_Base $controller=null) {
	if (!is_null($empty)) {
		// Push ''=>$empty onto the front of the array, maintaining keys
		$tmp=array(''=>WT_Filter::escapeHtml($empty));
		foreach ($values as $key=>$value) {
			$tmp[$key]=WT_Filter::escapeHtml($value);
		}
		$values=$tmp;
	}
	$values['selected']=WT_Filter::escapeHtml($selected);

	$html='<span class="editable" id="' . $name . '">' .  (array_key_exists($selected, $values) ? $values[$selected] : '') . '</span>';
	$js='jQuery("#' . $name . '").editable("' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'save.php", {submitdata: {csrf: WT_CSRF_TOKEN}, type:"select", data:' . json_encode($values) . ', submit:"&nbsp;&nbsp;' . WT_I18N::translate('save') . '&nbsp;&nbsp;", style:"inherit", placeholder: "'.WT_I18N::translate('click to edit').'", callback:function(value, settings) {jQuery(this).html(settings.data[value]);} });';

	if ($controller) {
		$controller->addInlineJavascript($js);
		return $html;
	} else {
		// For AJAX callbacks
		return $html . '<script>' . $js . '</script>';
	}
}

/**
 * Create a set of radio buttons for a form
 *
 * @param string   $name      The ID for the form element
 * @param string[] $values    Array of value=>display items
 * @param string   $selected  The currently selected item
 * @param string   $extra     Additional markup for the label
 *
 * @return string
 */
function radio_buttons($name, $values, $selected, $extra = '') {
	$html = '';
	foreach ($values as $key => $value) {
		$html .= '<label ' . $extra . '><input type="radio" name="' . $name . '" value="' . WT_Filter::escapeHtml($key) . '"';
		if ((string)$key === (string)$selected) { // Beware PHP array keys are cast to integers!  Cast them back
			$html .= ' checked';
		}
		$html .= '>' . WT_Filter::escapeHtml($value) . '</label>';
	}

	return $html;
}

// Print an edit control for a Yes/No field
/**
 * @param string  $name
 * @param boolean $selected
 *
 * @return string
 */
function edit_field_yes_no($name, $selected=false) {
	return radio_buttons(
		$name, array(false=>WT_I18N::translate('no'), true=>WT_I18N::translate('yes')), $selected
	);
}

/**
 * An inline-editing version of edit_field_yes_no()
 *
 * @param string             $name
 * @param boolean            $selected
 * @param WT_Controller_Base $controller
 *
 * @return string
 */
function edit_field_yes_no_inline($name, $selected=false, WT_Controller_Base $controller=null) {
	return select_edit_control_inline(
		$name, array(true=>WT_I18N::translate('yes'), false=>WT_I18N::translate('no')), null, (int)$selected, $controller
	);
}

/**
 * Print an edit control for a checkbox.
 *
 * @param string  $name
 * @param boolean $is_checked
 * @param string  $extra
 *
 * @return string
 */
function checkbox($name, $is_checked=false, $extra='') {
	return '<input type="checkbox" name="'.$name.'" value="1" '.($is_checked ? 'checked="checked" ' : '').$extra.'>';
}

/**
 * Print an edit control for a checkbox, with a hidden field to store one of the two states.
 * By default, a checkbox is either set, or not sent.
 * This function gives us a three options, set, unset or not sent.
 * Useful for dynamically generated forms where we don't know what elements are present.
 *
 * @param string  $name
 * @param integer $is_checked 0 or 1
 * @param string  $extra
 *
 * @return string
 */
function two_state_checkbox($name, $is_checked=0, $extra='') {
	return
		'<input type="hidden" id="'.$name.'" name="'.$name.'" value="'.($is_checked?1:0).'">'.
		'<input type="checkbox" name="'.$name.'-GUI-ONLY" value="1"'.
		($is_checked ? ' checked="checked"' : '').
		' onclick="document.getElementById(\''.$name.'\').value=(this.checked?1:0);" '.$extra.'>';
}

/**
 * Print a set of edit controls to select languages
 *
 * @param string $field_prefix
 * @param string $languages
 *
 * @return string
 */
function edit_language_checkboxes($field_prefix, $languages) {
	$html = '';
	$i    = 0;

	foreach (WT_I18N::installed_languages() as $code=>$name) {
		$content = '<input type="checkbox" name="'.$field_prefix.$code.'" id="'.$field_prefix.$code.'"';
		if (strpos(",{$languages},", ",{$code},")!==false) {
			$content .= ' checked="checked"';
		}
		$content .= '><label for="'.$field_prefix.$code.'"> '.$name.'</label>';
		// print in three columns
		switch ($i % 3) {
		case 0:
			$html .= '<tr><td>' . $content . '</td>';
			break;
		case 1:
			$html .= '<td>' . $content . '</td>';
			break;
		case 2:
			$html .= '<td>' . $content . '</td></tr>';
			break;
		}
		$i++;
	}
	switch ($i % 3) {
	case 0:
		break;
	case 1:
		$html .= '<td></td><td></td></tr>';
		break;
	case 2:
		$html .= '<td></td></tr>';
		break;
	}

	return '<table>' . $html . '</table>';
}

/**
 * Print an edit control for access level.
 *
 * @param string $name
 * @param string $selected
 * @param string $extra
 *
 * @return string
 */
function edit_field_access_level($name, $selected='', $extra='') {
	$ACCESS_LEVEL=array(
		WT_PRIV_PUBLIC=>WT_I18N::translate('Show to visitors'),
		WT_PRIV_USER  =>WT_I18N::translate('Show to members'),
		WT_PRIV_NONE  =>WT_I18N::translate('Show to managers'),
		WT_PRIV_HIDE  =>WT_I18N::translate('Hide from everyone')
	);
	return select_edit_control($name, $ACCESS_LEVEL, null, $selected, $extra);
}

/**
 * Print an edit control for a RESN field.
 *
 * @param string $name
 * @param string $selected
 * @param string $extra
 *
 * @return string
 */
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

/**
 * Print an edit control for a contact method field.
 *
 * @param string $name
 * @param string $selected
 * @param string $extra
 *
 * @return string
 */
function edit_field_contact($name, $selected='', $extra='') {
	// Different ways to contact the users
	$CONTACT_METHODS=array(
		'messaging' =>WT_I18N::translate('webtrees internal messaging'),
		'messaging2'=>WT_I18N::translate('Internal messaging with emails'),
		'messaging3'=>WT_I18N::translate('webtrees sends emails with no storage'),
		'mailto'    =>WT_I18N::translate('Mailto link'),
		'none'      =>WT_I18N::translate('No contact'),
	);
	return select_edit_control($name, $CONTACT_METHODS, null, $selected, $extra);
}

/**
 * Print an edit control for a contact method field.
 *
 * @param string             $name
 * @param string             $selected
 * @param WT_Controller_Base $controller
 *
 * @return string
 */
function edit_field_contact_inline($name, $selected='', WT_Controller_Base $controller=null) {
	// Different ways to contact the users
	$CONTACT_METHODS=array(
		'messaging' =>WT_I18N::translate('webtrees internal messaging'),
		'messaging2'=>WT_I18N::translate('Internal messaging with emails'),
		'messaging3'=>WT_I18N::translate('webtrees sends emails with no storage'),
		'mailto'    =>WT_I18N::translate('Mailto link'),
		'none'      =>WT_I18N::translate('No contact'),
	);
	return select_edit_control_inline($name, $CONTACT_METHODS, null, $selected, $controller);
}

/**
 * Print an edit control for a language field.
 *
 * @param string $name
 * @param string $selected
 * @param string $extra
 *
 * @return string
 */
function edit_field_language($name, $selected='', $extra='') {
	return select_edit_control($name, WT_I18N::installed_languages(), null, $selected, $extra);
}

/**
 * An inline-editing version of edit_field_language().
 *
 * @param string             $name
 * @param string             $selected
 * @param WT_Controller_Base $controller
 *
 * @return string
 */
function edit_field_language_inline($name, $selected='', WT_Controller_Base $controller=null) {
	return select_edit_control_inline(
		$name, WT_I18N::installed_languages(), null, $selected, $controller
	);
}

/**
 * Print an edit control for a range of integers.
 *
 * @param string  $name
 * @param string  $selected
 * @param integer $min
 * @param integer $max
 * @param string  $extra
 *
 * @return string
 */
function edit_field_integers($name, $selected='', $min, $max, $extra='') {
	$array=array();
	for ($i=$min; $i<=$max; ++$i) {
		$array[$i]=WT_I18N::number($i);
	}
	return select_edit_control($name, $array, null, $selected, $extra);
}

/**
 * Print an edit control for a username.
 *
 * @param string $name
 * @param string $selected
 * @param string $extra
 *
 * @return string
 */
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

/**
 * Print an edit control for a ADOP field.
 *
 * @param string        $name
 * @param string        $selected
 * @param string        $extra
 * @param WT_Individual $individual
 *
 * @return string
 */
function edit_field_adop($name, $selected='', $extra='', WT_Individual $individual=null) {
	return select_edit_control($name, WT_Gedcom_Code_Adop::getValues($individual), null, $selected, $extra);
}

/**
 * Print an edit control for a PEDI field.
 *
 * @param string        $name
 * @param string        $selected
 * @param string        $extra
 * @param WT_Individual $individual
 *
 * @return string
 */
function edit_field_pedi($name, $selected='', $extra='', WT_Individual $individual=null) {
	return select_edit_control($name, WT_Gedcom_Code_Pedi::getValues($individual), '', $selected, $extra);
}

/**
 * Print an edit control for a NAME TYPE field.
 *
 * @param string        $name
 * @param string        $selected
 * @param string        $extra
 * @param WT_Individual $individual
 *
 * @return string
 */
function edit_field_name_type($name, $selected='', $extra='', WT_Individual $individual=null) {
	return select_edit_control($name, WT_Gedcom_Code_Name::getValues($individual), '', $selected, $extra);
}

/**
 * Print an edit control for a RELA field.
 *
 * @param string $name
 * @param string $selected
 * @param string $extra
 *
 * @return string
 */
function edit_field_rela($name, $selected='', $extra='') {
	$rela_codes=WT_Gedcom_Code_Rela::getValues();
	// The user is allowed to specify values that aren't in the list.
	if (!array_key_exists($selected, $rela_codes)) {
		$rela_codes[$selected]=$selected;
	}
	return select_edit_control($name, $rela_codes, '', $selected, $extra);
}

/**
 * Remove all links from $gedrec to $xref, and any sub-tags.
 *
 * @param string $gedrec
 * @param string $xref
 *
 * @return string
 */
function remove_links($gedrec, $xref) {
	$gedrec = preg_replace('/\n1 '.WT_REGEX_TAG.' @'.$xref.'@(\n[2-9].*)*/', '', $gedrec);
	$gedrec = preg_replace('/\n2 '.WT_REGEX_TAG.' @'.$xref.'@(\n[3-9].*)*/', '', $gedrec);
	$gedrec = preg_replace('/\n3 '.WT_REGEX_TAG.' @'.$xref.'@(\n[4-9].*)*/', '', $gedrec);
	$gedrec = preg_replace('/\n4 '.WT_REGEX_TAG.' @'.$xref.'@(\n[5-9].*)*/', '', $gedrec);
	$gedrec = preg_replace('/\n5 '.WT_REGEX_TAG.' @'.$xref.'@(\n[6-9].*)*/', '', $gedrec);

	return $gedrec;
}

/**
 * Generates javascript code for calendar popup in user’s language.
 *
 * @param string $id
 *
 * @return string
 */
function print_calendar_popup($id) {
	return
		' <a href="#" onclick="cal_toggleDate(\'caldiv'.$id.'\', \''.$id.'\'); return false;" class="icon-button_calendar" title="'.WT_I18N::translate('Select a date').'"></a>'.
		'<div id="caldiv'.$id.'" style="position:absolute;visibility:hidden;background-color:white;z-index:1000;"></div>';
}

/**
 * @param string $element_id
 *
 * @return string
 */
function print_addnewmedia_link($element_id) {
	return '<a href="#" onclick="pastefield=document.getElementById(\''.$element_id.'\'); window.open(\'addmedia.php?action=showmediaform\', \'_blank\', edit_window_specs); return false;" class="icon-button_addmedia" title="'.WT_I18N::translate('Create a new media object').'"></a>';
}

/**
 * @param string $element_id
 *
 * @return string
 */
function print_addnewrepository_link($element_id) {
	return '<a href="#" onclick="addnewrepository(document.getElementById(\''.$element_id.'\')); return false;" class="icon-button_addrepository" title="'.WT_I18N::translate('Create a new repository').'"></a>';
}

/**
 * @param string $element_id
 *
 * @return string
 */
function print_addnewnote_link($element_id) {
	return '<a href="#" onclick="addnewnote(document.getElementById(\''.$element_id.'\')); return false;" class="icon-button_addnote" title="'.WT_I18N::translate('Create a new shared note').'"></a>';
}

/**
 * @param string $note_id
 *
 * @return string
 */
function print_editnote_link($note_id) {
	return '<a href="#" onclick="edit_note(\''.$note_id.'\'); return false;" class="icon-button_note" title="'.WT_I18N::translate('Edit shared note').'"></a>';
}

/**
 * @param string $element_id
 *
 * @return string
 */
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
 * @param string        $tag        fact record to edit (eg 2 DATE xxxxx)
 * @param string        $upperlevel optional upper level tag (eg BIRT)
 * @param string        $label      An optional label to echo instead of the default
 * @param string        $extra      optional text to display after the input field
 * @param WT_Individual $person     For male/female translations
 *
 * @return string
 */
function add_simple_tag(
	$tag, $upperlevel = '', $label = '', $extra = null,
	WT_Individual $person = null
) {
	global $tags, $emptyfacts, $main_fact, $FILE_FORM_accept, $xref, $bdm, $action;
	global $QUICK_REQUIRED_FACTS, $QUICK_REQUIRED_FAMFACTS, $PREFER_LEVEL2_SOURCES;

	// Keep track of SOUR fields, so we can reference them in subsequent PAGE fields.
	static $source_element_id;

	$subnamefacts = array("NPFX", "GIVN", "SPFX", "SURN", "NSFX", "_MARNM_SURN");
	preg_match('/^(?:(\d+) ('.WT_REGEX_TAG.') ?(.*))/', $tag, $match);
	list(, $level, $fact, $value) = $match;

	// element name : used to POST data
	if ($level==0) {
		if ($upperlevel) $element_name=$upperlevel . '_' . $fact;
		else $element_name=$fact; // ex: OCCU
	} else $element_name="text[]";
	if ($level==1) $main_fact=$fact;

	// element id : used by javascript functions
	if ($level==0)
		$element_id = $fact;
	else
		$element_id = $fact . Uuid::uuid4();
	if ($upperlevel)
		$element_id = $upperlevel . '_' . $fact . Uuid::uuid4();

	// field value
	$islink = (substr($value, 0, 1) === '@' && substr($value, 0, 2) != '@#');
	if ($islink) {
		$value = trim(substr($tag, strlen($fact)+3), " @\r");
	} else {
		$value = substr($tag, strlen($fact)+3);
	}
	if ($fact == 'REPO' || $fact == 'SOUR' || $fact == 'OBJE' || $fact == 'FAMC')
		$islink = true;

	if ($fact == 'SHARED_NOTE_EDIT' || $fact == 'SHARED_NOTE') {
		$islink = true;
		$fact = 'NOTE';
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
	if ($label) {
		echo $label;
	} elseif ($upperlevel) {
		echo WT_Gedcom_Tag::getLabel($upperlevel.':'.$fact);
	} else {
		echo WT_Gedcom_Tag::getLabel($fact);
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
			if ($upperlevel!='REPO') {
				echo help_link($fact);
			}
			break;
		case 'ASSO':
		case '_ASSO': // Some apps (including webtrees) use "2 _ASSO", since "2 ASSO" is not strictly valid GEDCOM
			if ($level==1) {
				echo help_link('ASSO_1');
			} else {
				echo help_link('ASSO_2');
			}
			break;
		case 'ADDR':
		case 'AGNC':
		case 'CAUS':
		case 'DATE':
		case 'EMAI':
		case 'EMAIL':
		case 'EMAL':
		case '_EMAIL':
		case 'FAX':
		case 'OBJE':
		case 'PAGE':
		case 'PEDI':
		case 'PHON':
		case 'PLAC':
		case 'RELA':
		case 'RESN':
		case 'ROMN':
		case 'SEX':
		case 'SOUR':
		case 'STAT':
		case 'SURN':
		case 'TEMP':
		case 'TEXT':
		case 'TIME':
		case 'URL':
		case '_HEB':
		case '_PRIM':
			echo help_link($fact);
			break;
		}
	}
	// tag level
	if ($level>0) {
		if ($fact=='TEXT' && $level>1) {
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
			$noterec=$note1->getGedcom();
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
			echo " onclick=\"if (this.checked) ", $element_id, ".value='Y'; else ", $element_id, ".value='';\">";
			echo WT_I18N::translate('yes');
		}

	} else if ($fact=="TEMP") {
		echo select_edit_control($element_name, WT_Gedcom_Code_Temp::templeNames(), WT_I18N::translate('No temple - living ordinance'), $value);
	} else if ($fact=="ADOP") {
		echo edit_field_adop($element_name, $value, '', $person);
	} else if ($fact=="PEDI") {
		echo edit_field_pedi($element_name, $value, '', $person);
	} else if ($fact=='STAT') {
		echo select_edit_control($element_name, WT_Gedcom_Code_Stat::statusNames($upperlevel), '', $value);
	} else if ($fact=='RELA') {
		echo edit_field_rela($element_name, strtolower($value));
	} else if ($fact=='QUAY') {
		echo select_edit_control($element_name, WT_Gedcom_Code_Quay::getValues(), '', $value);
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
			echo '<option selected="selected" value="', WT_Filter::escapeHtml($value), '" >', WT_Filter::escapeHtml($value), '</option>';
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
		echo "<input type=\"hidden\" id=\"", $element_id, "\" name=\"", $element_name, "\" onchange=\"updateTextName('", $element_id, "');\" value=\"", WT_Filter::escapeHtml($value), "\" class=\"", $fact, "\">";
		echo '<span id="', $element_id, '_display" dir="auto">', WT_Filter::escapeHtml($value), '</span>';
		echo ' <a href="#edit_name" onclick="convertHidden(\'', $element_id, '\'); return false;" class="icon-edit_indi" title="'.WT_I18N::translate('Edit name').'"></a>';
	} else {
		// textarea
		if ($fact=='TEXT' || $fact=='ADDR' || ($fact=='NOTE' && !$islink)) {
			echo "<textarea id=\"", $element_id, "\" name=\"", $element_name, "\" dir=\"auto\">", WT_Filter::escapeHtml($value), "</textarea><br>";
		} else {
			// text
			// If using GEDFact-assistant window
			if ($action=="addnewnote_assisted") {
				echo "<input type=\"text\" id=\"", $element_id, "\" name=\"", $element_name, "\" value=\"", WT_Filter::escapeHtml($value), "\" style=\"width:4.1em;\" dir=\"ltr\"";
			} else {
				echo "<input type=\"text\" id=\"", $element_id, "\" name=\"", $element_name, "\" value=\"", WT_Filter::escapeHtml($value), "\" dir=\"ltr\"";
			}
			echo " class=\"{$fact}\"";
			if (in_array($fact, $subnamefacts)) {
				echo " onblur=\"updatewholename();\" onkeyup=\"updatewholename();\"";
			}

			// Extra markup for specific fact types
			switch ($fact) {
			case 'ASSO':
			case '_ASSO':
				echo ' data-autocomplete-type="ASSO"';
				break;
			case 'DATE':
				echo " onblur=\"valid_date(this);\" onmouseout=\"valid_date(this);\"";
				break;
			case 'GIVN':
				echo ' autofocus data-autocomplete-type="GIVN"';
				break;
			case 'LATI':
				echo " onblur=\"valid_lati_long(this, 'N', 'S');\" onmouseout=\"valid_lati_long(this, 'N', 'S');\"";
				break;
			case 'LONG':
				echo " onblur=\"valid_lati_long(this, 'E', 'W');\" onmouseout=\"valid_lati_long(this, 'E', 'W');\"";
				break;
			case 'NOTE':
				// Shared notes.  Inline notes are handled elsewhere.
				echo ' data-autocomplete-type="NOTE"';
				break;
			case 'OBJE':
				echo ' data-autocomplete-type="OBJE"';
				break;
			case 'PAGE':
				echo ' data-autocomplete-type="PAGE" data-autocomplete-sour="' . $source_element_id . '"';
				break;
			case 'PLAC':
				echo ' data-autocomplete-type="PLAC"';
				break;
			case 'REPO':
				echo ' data-autocomplete-type="REPO"';
				break;
			case 'SOUR':
				$source_element_id = $element_id;
				echo ' data-autocomplete-type="SOUR"';
				break;
			case 'SURN':
			case '_MARNM_SURN':
				echo ' data-autocomplete-type="SURN"';
				break;
			}
			echo '>';
		}

		$tmp_array = array('TYPE','TIME','NOTE','SOUR','REPO','OBJE','ASSO','_ASSO','AGE');

		// split PLAC
		if ($fact=='PLAC') {
			echo "<div id=\"", $element_id, "_pop\" style=\"display: inline;\">";
			echo print_specialchar_link($element_id), ' ', print_findplace_link($element_id);
			echo '<span  onclick="jQuery(\'tr[id^=', $upperlevel,'_LATI],tr[id^=', $upperlevel,'_LONG],tr[id^=LATI],tr[id^=LONG]\').toggle(\'fast\'); return false;" class="icon-target" title="', WT_Gedcom_Tag::getLabel('LATI'), ' / ', WT_Gedcom_Tag::getLabel('LONG'), '"></span>';
			echo '</div>';
			if (array_key_exists('places_assistant', WT_Module::getActiveModules())) {
				places_assistant_WT_Module::setup_place_subfields($element_id);
				places_assistant_WT_Module::print_place_subfields($element_id);
			}
		} elseif (!in_array($fact, $tmp_array)) {
			echo print_specialchar_link($element_id);
		}
	}
	// MARRiage TYPE : hide text field and show a selection list
	if ($fact=='TYPE' && $level==2 && $tags[0]=='MARR') {
		echo '<script>';
		echo "document.getElementById('", $element_id, "').style.display='none'";
		echo '</script>';
		echo "<select id=\"", $element_id, "_sel\" onchange=\"document.getElementById('", $element_id, "').value=this.value;\" >";
		foreach (array("Unknown", "Civil", "Religious", "Partners") as $key) {
			if ($key=="Unknown") echo "<option value=\"\"";
			else echo "<option value=\"", $key, "\"";
			$a=strtolower($key);
			$b=strtolower($value);
			if (@strpos($a, $b) !== false || @strpos($b, $a) !== false) {
				echo ' selected="selected"';
			}
			$tmp="MARR_".strtoupper($key);
			echo ">", WT_Gedcom_Tag::getLabel($tmp), "</option>";
		}
		echo "</select>";
	}
	// NAME TYPE : hide text field and show a selection list
	else if ($fact=='TYPE' && $level==0) {
		$onchange = 'onchange="document.getElementById(\''.$element_id.'\').value=this.value;"';
		echo edit_field_name_type($element_name, $value, $onchange, $person);
		echo '<script>';
		echo "document.getElementById('", $element_id, "').style.display='none';";
		echo '</script>';
	}

	// popup links
	switch ($fact) {
	case 'DATE':
		echo print_calendar_popup($element_id);

		// Allow the GEDFact_assistant module to show a census-date selector
		if (array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
			echo GEDFact_assistant_WT_Module::censusDateSelector($action, $upperlevel, $element_id);
		}
		break;
	case 'FAMC':
	case 'FAMS':
		echo print_findfamily_link($element_id);
		break;
	case 'ASSO':
	case '_ASSO':
		echo print_findindi_link($element_id, $element_id . '_description');
		break;
	case 'FILE':
		print_findmedia_link($element_id, "0file");
		break;
	case 'SOUR':
		echo print_findsource_link($element_id, $element_id . '_description'), ' ', print_addnewsource_link($element_id);
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
				echo '&nbsp;<input type="checkbox" name="SOUR_INDI" ', $level1_checked, ' value="1">';
				echo WT_I18N::translate('Individual');
				if (preg_match_all('/('.WT_REGEX_TAG.')/', $QUICK_REQUIRED_FACTS, $matches)) {
					foreach ($matches[1] as $match) {
						if (!in_array($match, explode('|', WT_EVENTS_DEAT))) {
							echo '&nbsp;<input type="checkbox" name="SOUR_', $match, '"', $level2_checked, ' value="1">';
							echo WT_Gedcom_Tag::getLabel($match);
						}
					}
				}
			}
			if (strpos($bdm, 'D')!==false) {
				if (preg_match_all('/('.WT_REGEX_TAG.')/', $QUICK_REQUIRED_FACTS, $matches)) {
					foreach ($matches[1] as $match) {
						if (in_array($match, explode('|', WT_EVENTS_DEAT))) {
							echo '&nbsp;<input type="checkbox" name="SOUR_', $match, '"', $level2_checked, ' value="1">';
							echo WT_Gedcom_Tag::getLabel($match);
						}
					}
				}
			}
			if (strpos($bdm, 'M')!==false) {
				echo '&nbsp;<input type="checkbox" name="SOUR_FAM" ', $level1_checked, ' value="1">';
				echo WT_I18N::translate('Family');
				if (preg_match_all('/('.WT_REGEX_TAG.')/', $QUICK_REQUIRED_FAMFACTS, $matches)) {
					foreach ($matches[1] as $match) {
						echo '&nbsp;<input type="checkbox" name="SOUR_', $match, '"', $level2_checked, ' value="1">';
						echo WT_Gedcom_Tag::getLabel($match);
					}
				}
			}
		}
		break;
	case 'REPO':
		echo print_findrepository_link($element_id), ' ', print_addnewrepository_link($element_id);
		break;
	case 'NOTE':
		// Shared Notes Icons ========================================
		if ($islink) {
			// Print regular Shared Note icons ---------------------------
			echo ' ', print_findnote_link($element_id, $element_id . '_description'), ' ', print_addnewnote_link($element_id);
			if ($value) {
				echo ' ', print_editnote_link($value);
			}

			// Allow the GEDFact_assistant module to create a formatted shared note.
			if (array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
				echo GEDFact_assistant_WT_Module::print_addnewnote_assisted_link($element_id, $xref, $action);
			}
		}
		break;
	case 'OBJE':
		echo print_findmedia_link($element_id, '1media');
		if (!$value) {
			echo ' ', print_addnewmedia_link($element_id);
			$value = 'new';
		}
		break;
	}

	echo '<div id="' . $element_id . '_description">';

	// current value
	if ($fact == 'DATE') {
		$date = new WT_Date($value);
		echo $date->display();
	}
	if ($value && $value != 'new' && $islink) {
		switch ($fact) {
		case 'ASSO':
		case '_ASSO':
			$tmp = WT_Individual::getInstance($value);
			if ($tmp) {
				echo ' ', $tmp->getFullname();
			}
			break;
		case 'SOUR':
			$tmp = WT_Source::getInstance($value);
			if ($tmp) {
				echo ' ', $tmp->getFullname();
			}
			break;
		case 'NOTE':
			$tmp = WT_Note::getInstance($value);
			if ($tmp) {
				echo ' ', $tmp->getFullname();
			}
			break;
		case 'OBJE':
			$tmp = WT_Media::getInstance($value);
			if ($tmp) {
				echo ' ', $tmp->getFullname();
			}
			break;
		case 'REPO':
			$tmp = WT_Repository::getInstance($value);
			if ($tmp) {
				echo ' ', $tmp->getFullname();
			}
			break;
		}
	}

	// pastable values
	if ($fact=='FORM' && $upperlevel=='OBJE') print_autopaste_link($element_id, $FILE_FORM_accept);
	echo '</div>', $extra, '</td></tr>';

	return $element_id;
}

/**
 * Prints collapsable fields to add ASSO/RELA, SOUR, OBJE, etc.
 *
 * @param string  $tag
 * @param integer $level
 */
function print_add_layer($tag, $level=2) {
	global $FULL_SOURCES, $WT_TREE;

	switch ($tag) {
	case 'SOUR':
		echo "<a href=\"#\" onclick=\"return expand_layer('newsource');\"><i id=\"newsource_img\" class=\"icon-plus\"></i> ", WT_I18N::translate('Add a new source citation'), '</a>';
		echo help_link('edit_add_SOUR');
		echo '<br>';
		echo '<div id="newsource" style="display: none;">';
		echo '<table class="facts_table">';
		// 2 SOUR
		add_simple_tag($level . ' SOUR @');
		// 3 PAGE
		add_simple_tag(($level+1) . ' PAGE');
		// 3 DATA
		// 4 TEXT
		add_simple_tag(($level+2) . ' TEXT');
		if ($FULL_SOURCES) {
			// 4 DATE
			add_simple_tag(($level+2) . ' DATE', '', WT_Gedcom_Tag::getLabel('DATA:DATE'));
			// 3 QUAY
			add_simple_tag(($level+1) . ' QUAY');
		}
		// 3 OBJE
		add_simple_tag(($level+1) . ' OBJE');
		// 3 SHARED_NOTE
		add_simple_tag(($level+1) . ' SHARED_NOTE');
		echo '</table></div>';
		break;

	case 'ASSO':
	case 'ASSO2':
		//-- Add a new ASSOciate
		if ($tag=='ASSO') {
			echo "<a href=\"#\" onclick=\"return expand_layer('newasso');\"><i id=\"newasso_img\" class=\"icon-plus\"></i> ", WT_I18N::translate('Add a new associate'), '</a>';
			echo help_link('edit_add_ASSO');
			echo '<br>';
			echo '<div id="newasso" style="display: none;">';
		} else {
			echo "<a href=\"#\" onclick=\"return expand_layer('newasso2');\"><i id=\"newasso2_img\" class=\"icon-plus\"></i> ", WT_I18N::translate('Add a new associate'), '</a>';
			echo help_link('edit_add_ASSO');
			echo '<br>';
			echo '<div id="newasso2" style="display: none;">';
		}
		echo '<table class="facts_table">';
		// 2 ASSO
		add_simple_tag($level . ' ASSO @');
		// 3 RELA
		add_simple_tag(($level+1) . ' RELA');
		// 3 NOTE
		add_simple_tag(($level+1) . ' NOTE');
		// 3 SHARED_NOTE
		add_simple_tag(($level+1) . ' SHARED_NOTE');
		echo '</table></div>';
		break;

	case 'NOTE':
		//-- Retrieve existing note or add new note to fact
		echo "<a href=\"#\" onclick=\"return expand_layer('newnote');\"><i id=\"newnote_img\" class=\"icon-plus\"></i> ", WT_I18N::translate('Add a new note'), '</a>';
		echo help_link('edit_add_NOTE');
		echo '<br>';
		echo '<div id="newnote" style="display: none;">';
		echo '<table class="facts_table">';
		// 2 NOTE
		add_simple_tag($level . ' NOTE');
		echo '</table></div>';
		break;

	case 'SHARED_NOTE':
		echo "<a href=\"#\" onclick=\"return expand_layer('newshared_note');\"><i id=\"newshared_note_img\" class=\"icon-plus\"></i> ", WT_I18N::translate('Add a new shared note'), '</a>';
		echo help_link('edit_add_SHARED_NOTE');
		echo '<br>';
		echo '<div id="newshared_note" style="display: none;">';
		echo '<table class="facts_table">';
		// 2 SHARED NOTE
		add_simple_tag($level . ' SHARED_NOTE');
		echo '</table></div>';
		break;

	case 'OBJE':
		if ($WT_TREE->getPreference('MEDIA_UPLOAD') >= WT_USER_ACCESS_LEVEL) {
			echo "<a href=\"#\" onclick=\"return expand_layer('newobje');\"><i id=\"newobje_img\" class=\"icon-plus\"></i> ", WT_I18N::translate('Add a new media object'), '</a>';
			echo help_link('OBJE');
			echo '<br>';
			echo '<div id="newobje" style="display: none;">';
			echo '<table class="facts_table">';
			add_simple_tag($level . ' OBJE');
			echo '</table></div>';
		}
		break;

	case 'RESN':
		echo "<a href=\"#\" onclick=\"return expand_layer('newresn');\"><i id=\"newresn_img\" class=\"icon-plus\"></i> ", WT_I18N::translate('Add a new restriction'), '</a>';
		echo help_link('RESN');
		echo '<br>';
		echo '<div id="newresn" style="display: none;">';
		echo '<table class="facts_table">';
		// 2 RESN
		add_simple_tag($level . ' RESN');
		echo '</table></div>';
		break;
	}
}

/**
 * Add some empty tags to create a new fact.
 *
 * @param string $fact
 */
function addSimpleTags($fact) {
	global $ADVANCED_PLAC_FACTS, $nonplacfacts, $nondatefacts;

	// For new individuals, these facts default to "Y"
	if ($fact=='MARR') {
		add_simple_tag("0 {$fact} Y");
	} else {
		add_simple_tag("0 {$fact}");
	}

	if (!in_array($fact, $nondatefacts)) {
		add_simple_tag("0 DATE", $fact, WT_Gedcom_Tag::getLabel("{$fact}:DATE"));
	}

	if (!in_array($fact, $nonplacfacts)) {
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
}

/**
 * Assemble the pieces of a newly created record into gedcom
 *
 * @return string
 */
function addNewName() {
	global $ADVANCED_NAME_FACTS, $WT_TREE;

	$gedrec="\n1 NAME ".WT_Filter::post('NAME');

	$tags=array('NPFX', 'GIVN', 'SPFX', 'SURN', 'NSFX');

	if (preg_match_all('/('.WT_REGEX_TAG.')/', $ADVANCED_NAME_FACTS, $match)) {
		$tags=array_merge($tags, $match[1]);
	}

	// Paternal and Polish and Lithuanian surname traditions can also create a _MARNM
	$SURNAME_TRADITION = $WT_TREE->getPreference('SURNAME_TRADITION');
	if ($SURNAME_TRADITION=='paternal' || $SURNAME_TRADITION=='polish' || $SURNAME_TRADITION=='lithuanian') {
		$tags[]='_MARNM';
	}

	foreach (array_unique($tags) as $tag) {
		$TAG=WT_Filter::post($tag);
		if ($TAG) {
			$gedrec.="\n2 {$tag} {$TAG}";
		}
	}
	return $gedrec;
}

/**
 * @return string
 */
function addNewSex() {
	switch (WT_Filter::post('SEX', '[MF]', 'U')) {
	case 'M':
		return "\n1 SEX M";
	case 'F':
		return "\n1 SEX F";
	default:
		return "\n1 SEX U";
	}
}

/**
 * @param string $fact
 *
 * @return string
 */
function addNewFact($fact) {
	global $ADVANCED_PLAC_FACTS;

	$FACT = WT_Filter::post($fact);
	$DATE = WT_Filter::post("{$fact}_DATE");
	$PLAC = WT_Filter::post("{$fact}_PLAC");
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
					$TAG=WT_Filter::post("{$fact}_{$tag}");
					if ($TAG) {
						$gedrec.="\n3 {$tag} {$TAG}";
					}
				}
			}
			$LATI=WT_Filter::post("{$fact}_LATI");
			$LONG=WT_Filter::post("{$fact}_LONG");
			if ($LATI || $LONG) {
				$gedrec.="\n3 MAP\n4 LATI {$LATI}\n4 LONG {$LONG}";
			}
		}
		if (WT_Filter::postBool("SOUR_{$fact}")) {
			return updateSOUR($gedrec, 2);
		} else {
			return $gedrec;
		}
	} elseif ($FACT=='Y') {
		if (WT_Filter::postBool("SOUR_{$fact}")) {
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
 * See the handle_updates() function for details.
 *
 * @param string $inputRec
 * @param string $levelOverride
 *
 * @return string
 */
function updateSOUR($inputRec, $levelOverride = 'no') {
	global $glevels, $tag, $islink, $text;
	global $glevelsSOUR, $tagSOUR, $islinkSOUR, $textSOUR;

	if (count($tagSOUR)==0) {
		return $inputRec; // No update required
	}

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
 * See the handle_updates() function for details.
 *
 * @param string $inputRec
 * @param string $levelOverride
 *
 * @return string
 */
function updateRest($inputRec, $levelOverride = 'no') {
	global $glevels, $tag, $islink, $text;
	global $glevelsRest, $tagRest, $islinkRest, $textRest;

	if (count($tagRest)==0) {
		return $inputRec; // No update required
	}

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
 *
 * @param string $newged        the new gedcom record to add the lines to
 * @param string $levelOverride Override GEDCOM level specified in $glevels[0]
 *
 * @return string The updated gedcom record
 */
function handle_updates($newged, $levelOverride = 'no') {
	global $glevels, $islink, $tag, $uploaded_files, $text;

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
			if ($tag[$j] == 'DATE' && $text[$j]!='') {
			}
			if ($text[$j] != '') {
				if ($islink[$j]) {
					$newline .= ' @' . $text[$j] . '@';
				} else {
					$newline .= ' ' . $text[$j];
				}
			}
			$newged .= "\n".str_replace("\n", "\n" . (1 + substr($newline, 0, 1)) . ' CONT ', $newline);
		}
	}

	return $newged;
}

/**
 * builds the form for adding new facts
 *
 * @param string $fact the new fact we are adding
 */
function create_add_form($fact) {
	global $tags, $FULL_SOURCES, $emptyfacts;

	$tags = array();

	// handle  MARRiage TYPE
	if (substr($fact, 0, 5) == 'MARR_') {
		$tags[0] = 'MARR';
		add_simple_tag('1 MARR');
		insert_missing_subtags($fact);
	} else {
		$tags[0] = $fact;
		if ($fact == '_UID') {
			$fact .= ' ' . WT_Gedcom_Tag::createUid();
		}
		// These new level 1 tags need to be turned into links
		if (in_array($fact, array('ASSO'))) {
			$fact .= ' @';
		}
		if (in_array($fact, $emptyfacts)) {
			add_simple_tag('1 ' . $fact . ' Y');
		} else {
			add_simple_tag('1 ' . $fact);
		}
		insert_missing_subtags($tags[0]);
		//-- handle the special SOURce case for level 1 sources [ 1759246 ]
		if ($fact == 'SOUR') {
			add_simple_tag('2 PAGE');
			add_simple_tag('3 TEXT');
			if ($FULL_SOURCES) {
				add_simple_tag('3 DATE', '', WT_Gedcom_Tag::getLabel('DATA:DATE'));
				add_simple_tag('2 QUAY');
			}
		}
	}
}

/**
 * Create a form to edit a WT_Fact object.
 *
 * @param WT_GedcomRecord $record
 * @param WT_Fact         $fact
 *
 * @return string
 */
function create_edit_form(WT_GedcomRecord $record, WT_Fact $fact) {
	global $ADVANCED_PLAC_FACTS, $date_and_time, $FULL_SOURCES, $tags;

	$pid = $record->getXref();

	$tags=array();
	$gedlines = explode("\n", $fact->getGedcom());

	$linenum = 0;
	$fields = explode(' ', $gedlines[$linenum]);
	$glevel = $fields[0];
	$level = $glevel;

	$type = $fact->getTag();
	$parent = $fact->getParent();
	$level0type = $parent::RECORD_TYPE;
	$level1type = $type;

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
			$person = WT_Individual::getInstance($pid);
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

	if ($level1type!='_PRIM') {
		insert_missing_subtags($level1type, $add_date);
	}
	return $level1type;
}

/**
 * Populates the global $tags array with any missing sub-tags.
 *
 * @param string  $level1tag the type of the level 1 gedcom record
 * @param boolean $add_date
 */
function insert_missing_subtags($level1tag, $add_date=false) {
	global $tags, $date_and_time, $level2_tags, $ADVANCED_PLAC_FACTS, $ADVANCED_NAME_FACTS;
	global $nondatefacts, $nonplacfacts;

	// handle  MARRiage TYPE
	$type_val = '';
	if (substr($level1tag, 0, 5)=='MARR_') {
		$type_val = substr($level1tag, 5);
		$level1tag = 'MARR';
	}

	foreach ($level2_tags as $key=>$value) {
		if ($key=='DATE' && in_array($level1tag, $nondatefacts) || $key=='PLAC' && in_array($level1tag, $nonplacfacts)) {
			continue;
		}
		if (in_array($level1tag, $value) && !in_array($key, $tags)) {
			if ($key=='TYPE') {
				add_simple_tag('2 TYPE '.$type_val, $level1tag);
			} elseif ($level1tag=='_TODO' && $key=='DATE') {
				add_simple_tag('2 '.$key.' '.strtoupper(date('d M Y')), $level1tag);
			} elseif ($level1tag=='_TODO' && $key=='_WT_USER') {
				add_simple_tag('2 '.$key.' '.Auth::user()->getUserName(), $level1tag);
			} else if ($level1tag=='TITL' && strstr($ADVANCED_NAME_FACTS, $key)!==false) {
				add_simple_tag('2 '.$key, $level1tag);
			} else if ($level1tag=='NAME' && strstr($ADVANCED_NAME_FACTS, $key)!==false) {
				add_simple_tag('2 '.$key, $level1tag);
			} else if ($level1tag!='TITL' && $level1tag!='NAME') {
				add_simple_tag('2 '.$key, $level1tag);
			}
			switch ($key) { // Add level 3/4 tags as appropriate
				case 'PLAC':
					if (preg_match_all('/('.WT_REGEX_TAG.')/', $ADVANCED_PLAC_FACTS, $match)) {
						foreach ($match[1] as $tag) {
							add_simple_tag("3 $tag", '', WT_Gedcom_Tag::getLabel("{$level1tag}:PLAC:{$tag}"));
						}
					}
					add_simple_tag('3 MAP');
					add_simple_tag('4 LATI');
					add_simple_tag('4 LONG');
					break;
				case 'FILE':
					add_simple_tag('3 FORM');
					break;
				case 'EVEN':
					add_simple_tag('3 DATE');
					add_simple_tag('3 PLAC');
					break;
				case 'STAT':
					if (WT_Gedcom_Code_Temp::isTagLDS($level1tag)) {
						add_simple_tag('3 DATE', '', WT_Gedcom_Tag::getLabel('STAT:DATE'));
					}
					break;
				case 'DATE':
					if (in_array($level1tag, $date_and_time))
						add_simple_tag('3 TIME'); // TIME is NOT a valid 5.5.1 tag
					break;
				case 'HUSB':
				case 'WIFE':
					add_simple_tag('3 AGE');
					break;
				case 'FAMC':
					if ($level1tag=='ADOP')
						add_simple_tag('3 ADOP BOTH');
					break;
			}
		} elseif ($key=='DATE' && $add_date) {
			add_simple_tag('2 DATE', $level1tag, WT_Gedcom_Tag::getLabel("{$level1tag}:DATE"));
		}
	}
	// Do something (anything!) with unrecognized custom tags
	if (substr($level1tag, 0, 1)=='_' && $level1tag!='_UID' && $level1tag!='_TODO')
		foreach (array('DATE', 'PLAC', 'ADDR', 'AGNC', 'TYPE', 'AGE') as $tag)
			if (!in_array($tag, $tags)) {
				add_simple_tag("2 {$tag}");
				if ($tag=='PLAC') {
					if (preg_match_all('/('.WT_REGEX_TAG.')/', $ADVANCED_PLAC_FACTS, $match)) {
						foreach ($match[1] as $ptag) {
							add_simple_tag("3 $ptag", '', WT_Gedcom_Tag::getLabel("{$level1tag}:PLAC:{$ptag}"));
						}
					}
					add_simple_tag('3 MAP');
					add_simple_tag('4 LATI');
					add_simple_tag('4 LONG');
				}
			}
}
