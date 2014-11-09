<?php
// User Account Edit Interface.
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

use WT\Auth;
use WT\Log;
use WT\User;

define('WT_SCRIPT_NAME', 'edituser.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';
require WT_ROOT.'includes/functions/functions_edit.php';

// prevent users with editing account disabled from being able to edit their account
if (!Auth::id() || !Auth::user()->getPreference('editaccount')) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
	exit;
}

// Valid values for form variables
$ALL_THEMES_DIRS=array();
foreach (get_theme_names() as $themename=>$themedir) {
	$ALL_THEME_DIRS[]=$themedir;
}

// Extract form variables
$form_action         = WT_Filter::post('form_action');
$form_username       = WT_Filter::post('form_username');
$form_realname       = WT_Filter::post('form_realname' );
$form_pass1          = WT_Filter::post('form_pass1', WT_REGEX_PASSWORD);
$form_pass2          = WT_Filter::post('form_pass2', WT_REGEX_PASSWORD);
$form_email          = WT_Filter::postEmail('form_email');
$form_rootid         = WT_Filter::post('form_rootid', WT_REGEX_XREF);
$form_theme          = WT_Filter::post('form_theme', implode('|', $ALL_THEME_DIRS));
$form_language       = WT_Filter::post('form_language', implode('|', array_keys(WT_I18N::installed_languages())), WT_LOCALE);
$form_contact_method = WT_Filter::post('form_contact_method');
$form_visible_online = WT_Filter::postBool('form_visible_online');

// Respond to form action
if ($form_action=='update' && WT_Filter::checkCsrf()) {
	if ($form_username != Auth::user()->getUserName() && User::findByIdentifier($form_username)) {
		WT_FlashMessages::addMessage(WT_I18N::translate('Duplicate user name.  A user with that user name already exists.  Please choose another user name.'));
	} elseif ($form_email != Auth::user()->getEmail() && User::findByIdentifier($form_email)) {
		WT_FlashMessages::addMessage(WT_I18N::translate('Duplicate email address.  A user with that email already exists.'));
	} else {
		// Change username
		if ($form_username != Auth::user()->getUserName()) {
			Log::addAuthenticationLog('User ' . Auth::user()->getUserName() . ' renamed to ' . $form_username);
			Auth::user()->setUserName($form_username);
		}

		// Change password
		if ($form_pass1 && $form_pass1 == $form_pass2) {
			Auth::user()->setPassword($form_pass1);
		}

		// Change other settings
		Auth::user()
			->setRealName($form_realname)
			->setEmail($form_email)
			->setPreference('language',      $form_language)
			->setPreference('contactmethod', $form_contact_method)
			->setPreference('visibleonline', $form_visible_online ? '1' : '0');

		if ($form_theme === null) {
			Auth::user()->deletePreference('theme');
		} else {
			Auth::user()->setPreference('theme', $form_theme);
		}

		$WT_TREE->setUserPreference(Auth::user(), 'rootid', $form_rootid);

		// Reload page to pick up changes such as theme and user_id
		header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME);
		exit;
	}
}

$controller = new WT_Controller_Page();
$controller
	->setPageTitle(WT_I18N::translate('User administration'))
	->pageHeader()
	->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
	->addInlineJavascript('autocomplete();');

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
</script>
<?php

// show the form to edit a user account details
echo '<div id="edituser-page">
	<h2>', WT_I18N::translate('My account'), '</h2>
	<form name="editform" method="post" action="?" onsubmit="return checkform(this);">
	<input type="hidden" name="form_action" value="update">
	', WT_Filter::getCsrf(), '
	<div id="edituser-table">
		<div class="label">', WT_I18N::translate('Username'), help_link('username'), '</div>
		<div class="value"><input type="text" name="form_username" value="', WT_Filter::escapeHtml(Auth::user()->getUserName()), '" autofocus></div>
		<div class="label">', WT_I18N::translate('Real name'), help_link('real_name'), '</div>
		<div class="value"><input type="text" name="form_realname" value="', WT_Filter::escapeHtml(Auth::user()->getRealName()), '"></div>';
		$person = WT_Individual::getInstance(WT_USER_GEDCOM_ID);
		if ($person) {
			echo '<div class="label">', WT_I18N::translate('Individual record'), help_link('edituser_gedcomid'), '</div>
				<div class="value">', $person->format_list('span'), '</div>';
		}
		$person = WT_Individual::getInstance(WT_USER_ROOT_ID);
		echo '<div class="label">', WT_I18N::translate('Default individual'), help_link('default_individual'), '</div>
			<div class="value"><input data-autocomplete-type="INDI" type="text" name="form_rootid" id="rootid" value="', WT_USER_ROOT_ID, '">';
				echo print_findindi_link('rootid'), '<br>';
				if ($person) {
					echo $person->format_list('span');
				}
			echo '</div>
		<div class="label">', WT_I18N::translate('Password'), help_link('password'), '</div>
		<div class="value"><input type="password" name="form_pass1"> ', WT_I18N::translate('Leave the password blank if you want to keep the current password.'), '</div>
		<div class="label">', WT_I18N::translate('Confirm password'), help_link('password_confirm'), '</div>
		<div class="value"><input type="password" name="form_pass2"></div>
		<div class="label">', WT_I18N::translate('Language'), '</div>
		<div class="value">', edit_field_language('form_language', Auth::user()->getPreference('language')), '</div>
		<div class="label">', WT_I18N::translate('Email address'), help_link('email'), '</div>
		<div class="value"><input type="email" name="form_email" value="', WT_Filter::escapeHtml(Auth::user()->getEmail()), '" size="50"></div>
		<div class="label">', WT_I18N::translate('Theme'), help_link('THEME'), '</div>
		<div class="value">
			<select name="form_theme">
			<option value="">', WT_Filter::escapeHtml(/* I18N: default option in list of themes */ WT_I18N::translate('<default theme>')), '</option>';
			foreach (get_theme_names() as $themename=>$themedir) {
				echo '<option value="', $themedir, '"';
				if ($themedir == Auth::user()->getPreference('theme')) {
					echo ' selected="selected"';
				}
				echo '>', $themename, '</option>';
			}
			echo '</select>
		</div>
		<div class="label">', WT_I18N::translate('Preferred contact method'), help_link('edituser_contact_meth'), '</div>
		<div class="value">', edit_field_contact('form_contact_method', Auth::user()->getPreference('contactmethod')), '</div>
		<div class="label">', WT_I18N::translate('Visible to other users when online'), help_link('useradmin_visibleonline'), '</div>
		<div class="value">', checkbox('form_visible_online', Auth::user()->getPreference('visibleonline')), '</div>
	</div>'; // close edituser-table
	echo '<div id="edituser_submit"><input type="submit" value="', WT_I18N::translate('save'), '"></div>';
	echo '</form>
</div>'; // close edituser-page
