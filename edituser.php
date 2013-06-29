<?php
// User Account Edit Interface.
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

define('WT_SCRIPT_NAME', 'edituser.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';
require WT_ROOT.'includes/functions/functions_edit.php';

// prevent users with editing account disabled from being able to edit their account
if (!get_user_setting(WT_USER_ID, 'editaccount')) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
	exit;
}

$controller=new WT_Controller_Page();
$controller->setPageTitle(WT_I18N::translate('User administration'));

// Valid values for form variables
$ALL_THEMES_DIRS=array();
foreach (get_theme_names() as $themename=>$themedir) {
	$ALL_THEME_DIRS[]=$themedir;
}

// Extract form variables
$form_action        =safe_POST('form_action'   );
$form_username      =safe_POST('form_username',       WT_REGEX_USERNAME);
$form_realname      =safe_POST('form_realname' );
$form_pass1         =safe_POST('form_pass1',          WT_REGEX_PASSWORD);
$form_pass2         =safe_POST('form_pass2',          WT_REGEX_PASSWORD);
$form_email         =safe_POST('form_email',          WT_REGEX_EMAIL,                         'email@example.com');
$form_rootid        =safe_POST('form_rootid',         WT_REGEX_XREF,                           WT_USER_ROOT_ID   );
$form_theme         =safe_POST('form_theme',          $ALL_THEME_DIRS);
$form_language      =safe_POST('form_language',       array_keys(WT_I18N::installed_languages()), WT_LOCALE          );
$form_contact_method=safe_POST('form_contact_method');
$form_visible_online=safe_POST_bool('form_visible_online');

// Respond to form action
if ($form_action=='update') {
	if ($form_username!=WT_USER_NAME && get_user_id($form_username)) {
		$controller->pageHeader();
		echo '<span class="error">', WT_I18N::translate('Duplicate user name.  A user with that user name already exists.  Please choose another user name.'), '</span><br>';
	} elseif ($form_email!=getUserEmail(WT_USER_ID) && get_user_by_email($form_email)) {
		$controller->pageHeader();
		echo '<span class="error">', WT_I18N::translate('Duplicate email address.  A user with that email already exists.'), '</span><br>';
	} else {
		// Change password
		if (!empty($form_pass1)) {
			set_user_password(WT_USER_ID, $form_pass1);
		}
		$old_realname =getUserFullName(WT_USER_ID);
		$old_email    =getUserEmail(WT_USER_ID);
		// Change other settings
		setUserFullName(WT_USER_ID, $form_realname);
		setUserEmail   (WT_USER_ID, $form_email);
		set_user_setting(WT_USER_ID, 'theme',         $form_theme);
		$WT_SESSION->theme_dir=$form_theme; // switch to the new theme right away
		set_user_setting(WT_USER_ID, 'language',      $form_language);
		$WT_SESSION->locale=$form_language; // switch to the new language right away
		set_user_setting(WT_USER_ID, 'contactmethod', $form_contact_method);
		set_user_setting(WT_USER_ID, 'visibleonline', $form_visible_online);
		$WT_TREE->userPreference(WT_USER_ID, 'rootid', $form_rootid);

		// Change username
		if ($form_username!=WT_USER_NAME) {
			AddToLog('User renamed to ->'.$form_username.'<-', 'auth');
			rename_user(WT_USER_ID, $form_username);
		}
		// Reload page to pick up changes such as theme and user_id
		header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME);
		exit;
	}
} else {
	$controller
		->pageHeader()
		->addExternalJavascript(WT_STATIC_URL.'js/autocomplete.js');
}

// Form validation
?>
<script>
function checkform(frm) {
	if (frm.form_username.value=="") {
		alert("<?php echo WT_I18N::translate('You must enter a user name.'); ?>");
		frm.form_username.focus();
		return false;
	}
	if (frm.form_realname.value=="") {
		alert("<?php echo WT_I18N::translate('You must enter a real name.'); ?>");
		frm.form_realname.focus();
		return false;
	}
	if (frm.form_pass1.value!=frm.form_pass2.value) {
		alert("<?php echo WT_I18N::translate('Passwords do not match.'); ?>");
		frm.form_pass1.focus();
		return false;
	}
	if (frm.form_pass1.value.length > 0 && frm.form_pass1.value.length < 6) {
		alert("<?php echo WT_I18N::translate('Passwords must contain at least 6 characters.'); ?>");
		frm.form_pass1.focus();
		return false;
	}
	return true;
}
var pastefield;
function paste_id(value) {
	pastefield.value=value;
}
</script>
<?php

// show the form to edit a user account details
echo '<div id="edituser-page">
	<h2>', WT_I18N::translate('My account'), '</h2>
	<form name="editform" method="post" action="" onsubmit="return checkform(this);">
	<input type="hidden" name="form_action" value="update">
	<div id="edituser-table">
		<div class="label">', WT_I18N::translate('Username'), help_link('username'), '</div>
		<div class="value"><input type="text" name="form_username" value="', WT_USER_NAME, '" autofocus></div>
		<div class="label">', WT_I18N::translate('Real name'), help_link('real_name'), '</div>
		<div class="value"><input type="text" name="form_realname" value="', getUserFullName(WT_USER_ID), '"></div>';
		$person=WT_Individual::getInstance(WT_USER_GEDCOM_ID);
		if ($person) {
			echo '<div class="label">', WT_I18N::translate('Individual record'), help_link('edituser_gedcomid'), '</div>
				<div class="value">', $person->format_list('span'), '</div>';
		}
		$person=WT_Individual::getInstance(WT_USER_ROOT_ID);
		echo '<div class="label">', WT_I18N::translate('Default individual'), help_link('default_individual'), '</div>
			<div class="value"><input type="text" name="form_rootid" id="rootid" value="', WT_USER_ROOT_ID, '">';
				echo print_findindi_link('rootid'), '<br>';
				if ($person) {
					echo $person->format_list('span');
				}		
			echo '</div>
		<div class="label">', WT_I18N::translate('Password'), help_link('password'), '</div>
		<div class="value"><input type="password" name="form_pass1"> ', WT_I18N::translate('Leave password blank if you want to keep the current password.'), '</div>
		<div class="label">', WT_I18N::translate('Confirm password'), help_link('password_confirm'), '</div>
		<div class="value"><input type="password" name="form_pass2"></div>
		<div class="label">', WT_I18N::translate('Language'), '</div>
		<div class="value">', edit_field_language('form_language', get_user_setting(WT_USER_ID, 'language')), '</div>
		<div class="label">', WT_I18N::translate('Email address'), help_link('email'), '</div>
		<div class="value"><input type="email" name="form_email" value="', getUserEmail(WT_USER_ID), '" size="50"></div>
		<div class="label">', WT_I18N::translate('Theme'), help_link('THEME'), '</div>
		<div class="value">
			<select name="form_theme">
			<option value="">', htmlspecialchars(/* I18N: default option in list of themes */ WT_I18N::translate('<default theme>')), '</option>';
			foreach (get_theme_names() as $themename=>$themedir) {
				echo '<option value="', $themedir, '"';
				if ($themedir==get_user_setting(WT_USER_ID, 'theme')) {
					echo ' selected="selected"';
				}
				echo '>', $themename, '</option>';
			}
			echo '</select>
		</div>
		<div class="label">', WT_I18N::translate('Preferred contact method'), help_link('edituser_contact_meth'), '</div>
		<div class="value">', edit_field_contact('form_contact_method', get_user_setting(WT_USER_ID, 'contactmethod')), '</div>
		<div class="label">', WT_I18N::translate('Visible to other users when online'), help_link('useradmin_visibleonline'), '</div>
		<div class="value">', checkbox('form_visible_online', get_user_setting(WT_USER_ID, 'visibleonline')), '</div>
	</div>'; // close edituser-table
	echo '<div id="edituser_submit"><input type="submit" value="', WT_I18N::translate('save'), '"></div>';
	echo '</form>
</div>'; // close edituser-page
