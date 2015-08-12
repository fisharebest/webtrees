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

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

define('WT_SCRIPT_NAME', 'edituser.php');
require './includes/session.php';

// Need to be logged in
if (!Auth::check()) {
	header('Location: ' . WT_BASE_URL);

	return;
}

// Extract form variables
$form_action         = Filter::post('form_action');
$form_username       = Filter::post('form_username');
$form_realname       = Filter::post('form_realname');
$form_pass1          = Filter::post('form_pass1', WT_REGEX_PASSWORD);
$form_pass2          = Filter::post('form_pass2', WT_REGEX_PASSWORD);
$form_email          = Filter::postEmail('form_email');
$form_rootid         = Filter::post('form_rootid', WT_REGEX_XREF);
$form_theme          = Filter::post('form_theme');
$form_language       = Filter::post('form_language');
$form_timezone       = Filter::post('form_timezone');
$form_contact_method = Filter::post('form_contact_method');
$form_visible_online = Filter::postBool('form_visible_online');

// Respond to form action
if ($form_action && Filter::checkCsrf()) {
	switch ($form_action) {
	case 'update':
		if ($form_username !== Auth::user()->getUserName() && User::findByIdentifier($form_username)) {
			FlashMessages::addMessage(I18N::translate('Duplicate user name.  A user with that user name already exists.  Please choose another user name.'));
		} elseif ($form_email !== Auth::user()->getEmail() && User::findByIdentifier($form_email)) {
			FlashMessages::addMessage(I18N::translate('Duplicate email address.  A user with that email already exists.'));
		} else {
			// Change username
			if ($form_username !== Auth::user()->getUserName()) {
				Log::addAuthenticationLog('User ' . Auth::user()->getUserName() . ' renamed to ' . $form_username);
				Auth::user()->setUserName($form_username);
			}

			// Change password
			if ($form_pass1 && $form_pass1 === $form_pass2) {
				Auth::user()->setPassword($form_pass1);
			}

			// Change other settings
			Auth::user()
				->setRealName($form_realname)
				->setEmail($form_email)
				->setPreference('language', $form_language)
				->setPreference('TIMEZONE', $form_timezone)
				->setPreference('contactmethod', $form_contact_method)
				->setPreference('visibleonline', $form_visible_online ? '1' : '0');

			if ($form_theme === null) {
				Auth::user()->deletePreference('theme');
			} else {
				Auth::user()->setPreference('theme', $form_theme);
			}

			$WT_TREE->setUserPreference(Auth::user(), 'rootid', $form_rootid);
		}
		break;

	case 'delete':
		// An administrator can only be deleted by another administrator
		if (!Auth::user()->getPreference('canadmin')) {
			// Keep a reference to the currently logged in user because after logging out this user,
			// a call to Auth::user() will not return this user anymore
			$currentUser = Auth::user();
			Auth::logout();
			$currentUser->delete();
		}
		break;
	}

	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME);

	return;
}

$controller = new PageController;
$controller
	->setPageTitle(I18N::translate('My account'))
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();');

$my_individual_record = Individual::getInstance($WT_TREE->getUserPreference(Auth::user(), 'gedcomid'), $WT_TREE);
$default_individual   = Individual::getInstance($WT_TREE->getUserPreference(Auth::user(), 'rootid'), $WT_TREE);

// Form validation
?>
<script>
function checkform(frm) {
	if (frm.form_username.value=="") {
		alert("<?php echo I18N::translate('You must enter a user name.'); ?>");
		frm.form_username.focus();
		return false;
	}
	if (frm.form_realname.value=="") {
		alert("<?php echo I18N::translate('You must enter a real name.'); ?>");
		frm.form_realname.focus();
		return false;
	}
	if (frm.form_pass1.value!=frm.form_pass2.value) {
		alert("<?php echo I18N::translate('Passwords do not match.'); ?>");
		frm.form_pass1.focus();
		return false;
	}
	if (frm.form_pass1.value.length > 0 && frm.form_pass1.value.length < 6) {
		alert("<?php echo I18N::translate('Passwords must contain at least 6 characters.'); ?>");
		frm.form_pass1.focus();
		return false;
	}
	return true;
}
</script>

<div id="edituser-page">
	<h2><?php echo $controller->getPageTitle(); ?></h2>

	<form name="editform" method="post" action="?" onsubmit="return checkform(this);">

		<input type="hidden" id="form_action" name="form_action" value="update">
		<?php echo Filter::getCsrf(); ?>

		<div id="edituser-table">
			<div class="label">
				<label for="form_username">
					<?php echo I18N::translate('Username'); ?>
				</label>
			</div>
			<div class="value">
				<input type="text" id="form_username" name="form_username" value="<?php echo Filter::escapeHtml(Auth::user()->getUserName()); ?>" dir="auto">
				<p class="small text-muted">
					<?php echo I18N::translate('Usernames are case-insensitive and ignore accented letters, so that “chloe”, “chloë”, and “Chloe” are considered to be the same.'); ?>
				</p>
			</div>

			<div class="label">
				<label for="form_realname">
					<?php echo I18N::translate('Real name'); ?>
				</label>
			</div>
			<div class="value">
				<input type="text" id="form_realname" name="form_realname" value="<?php echo Filter::escapeHtml(Auth::user()->getRealName()); ?>" dir="auto">
				<p class="small text-muted">
					<?php echo I18N::translate('This is your real name, as you would like it displayed on screen.'); ?>
				</p>
			</div>

			<div class="label">
				<?php echo I18N::translate('Individual record'); ?>
			</div>
			<div class="value">
				<?php if ($my_individual_record): ?>
				<?php echo $my_individual_record->formatList('span'); ?>
				<?php else: ?>
					<?php echo I18N::translateContext('unknown people', 'Unknown'); ?>
				<?php endif; ?>
				<p class="small text-muted">
					<?php echo I18N::translate('This is a link to your own record in the family tree.  If this is the wrong individual, contact an administrator.'); ?>
				</p>
			</div>

			<div class="label">
				<label for="form_rootid">
					<?php echo I18N::translate('Default individual'); ?>
				</label>
			</div>
			<div class="value">
				<input data-autocomplete-type="INDI" type="text" name="form_rootid" id="form_rootid" value="<?php echo $WT_TREE->getUserPreference(Auth::user(), 'rootid'); ?>">
				<?php echo FunctionsPrint::printFindIndividualLink('form_rootid'); ?>
				<br>
				<?php if ($default_individual): ?>
				<?php echo $default_individual->formatList('span'); ?>
				<?php endif; ?>
				<p class="small text-muted">
					<?php echo I18N::translate('This individual will be selected by default when viewing charts and reports.'); ?>
				</p>
			</div>

			<div class="label">
				<label for="form_pass1">
					<?php echo I18N::translate('Password'); ?>
				</label>
			</div>
			<div class="value">
				<input type="password" id="form_pass1" name="form_pass1">
				<p class="small text-muted">
					<?php echo I18N::translate('Passwords must be at least 6 characters long and are case-sensitive, so that “secret” is different from “SECRET”.'); ?>
					<?php echo I18N::translate('Leave the password blank if you want to keep the current password.'); ?>
				</p>
			</div>

			<div class="label">
				<label for="form_pass2">
					<?php echo I18N::translate('Confirm password'); ?>
				</label>
			</div>
			<div class="value">
				<input type="password" id="form_pass2" name="form_pass2">
				<p class="small text-muted">
					<?php echo I18N::translate('Type your password again, to make sure you have typed it correctly.'); ?>
				</p>
			</div>

			<div class="label">
				<label for="form_language">
					<?php echo I18N::translate('Language'); ?>
				</label>
			</div>
			<div class="value">
				<?php echo FunctionsEdit::editFieldLanguage('form_language', Auth::user()->getPreference('language')); ?>
			</div>

			<div class="label">
				<label for="form_timezone">
					<?php echo I18N::translate('Time zone'); ?>
				</label>
			</div>
			<div class="value">
				<?php echo FunctionsEdit::selectEditControl('form_timezone', array_combine(\DateTimeZone::listIdentifiers(), \DateTimeZone::listIdentifiers()), null, Auth::user()->getPreference('TIMEZONE') ?: 'UTC', 'class="form-control"'); ?>
				<p class="small text-muted">
					<?php echo I18N::translate('The time zone is required for date calculations, such as knowing today’s date.'); ?>
				</p>
			</div>

			<div class="label">
				<label for="form_email">
					<?php echo I18N::translate('Email address'); ?>
				</label>
			</div>
			<div class="value">
				<input type="email" id="form_email" name="form_email" value="<?php echo Filter::escapeHtml(Auth::user()->getEmail()); ?>" size="50">
				<p class="small text-muted">
					<?php echo I18N::translate('This email address will be used to send password reminders, website notifications, and messages from other family members who are registered on the website.'); ?>
				</p>
			</div>
			<?php if (Site::getPreference('ALLOW_USER_THEMES')): ?>

			<div class="label">
				<label for="form_theme">
					<?php echo I18N::translate('Theme'); ?>
				</label>
			</div>
			<div class="value">
				<select id="form_theme" name="form_theme">
					<option value="">
						<?php echo Filter::escapeHtml(/* I18N: default option in list of themes */ I18N::translate('<default theme>')); ?>
					</option>
					<?php foreach (Theme::themeNames() as $theme_id => $theme_name): ?>
					<option value="<?php echo $theme_id; ?>" <?php echo $theme_id === Auth::user()->getPreference('theme') ? 'selected' : ''; ?>>
						<?php echo $theme_name; ?>
					</option>
					<?php endforeach; ?>
				</select>
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the "Default theme" site configuration setting */ I18N::translate('You can change the appearance of webtrees using “themes”.  Each theme has a different style, layout, color scheme, etc.'); ?>
				</p>
			</div>
			<?php endif; ?>

			<div class="label">
				<label for="form_contact_method">
					<?php echo I18N::translate('Contact method'); ?>
				</label>
			</div>
			<div class="value">
				<?php echo FunctionsEdit::editFieldContact('form_contact_method', Auth::user()->getPreference('contactmethod')); ?>
				<p class="small text-muted">
					<?php echo I18N::translate('Site members can send each other messages.  You can choose to how these messages are sent to you, or choose not receive them at all.'); ?>
				</p>
			</div>

			<div class="label">
				<label for="form_visible_online">
					<?php echo I18N::translate('Visible to other users when online'); ?>
				</label>
			</div>
			<div class="value">
				<?php echo FunctionsEdit::checkbox('form_visible_online', Auth::user()->getPreference('visibleonline')); ?>
				<p class="small text-muted">
					<?php echo I18N::translate('This checkbox controls your visibility to other users while you’re online.  It also controls your ability to see other online users who are configured to be visible.<br><br>When this box is unchecked, you will be completely invisible to others, and you will also not be able to see other online users.  When this box is checked, exactly the opposite is true.  You will be visible to others, and you will also be able to see others who are configured to be visible.'); ?>
				</p>
			</div>
		</div>
		<div id="edituser_submit">
			<input type="submit" value="<?php echo I18N::translate('save'); ?>">
		</div>
		<?php if (!Auth::user()->getPreference('canadmin')): ?>
		<a href="#" onclick="if (confirm('<?php echo I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeJs(Auth::user()->getUserName())); ?>')) {jQuery('#form_action').val('delete'); document.editform.submit(); }">
			<?php echo I18N::translate('Delete your account'); ?>
		</a>
		<?php endif; ?>
	</form>
</div>
