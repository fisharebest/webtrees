<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

use DateTimeZone;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

// Need to be logged in
if (!Auth::check()) {
	header('Location: ' . WT_BASE_URL);

	return;
}

// Extract form variables
$action         = Filter::post('action', 'update|delete', '');
$username       = Filter::post('username');
$real_name      = Filter::post('real-name');
$password_1     = Filter::post('password-1', WT_REGEX_PASSWORD);
$password_2     = Filter::post('password-2', WT_REGEX_PASSWORD);
$email          = Filter::postEmail('email');
$root_id        = Filter::post('root-id', WT_REGEX_XREF);
$theme          = Filter::post('theme', implode('|', array_keys(Theme::themeNames())), '');
$language       = Filter::post('language', null, '');
$time_zone      = Filter::post('time-zone', null, 'UTC');
$contact_method = Filter::post('contact-method', null, '');
$visible_online = Filter::postBool('visible-online');

// Respond to form action
if ($action !== '' && Filter::checkCsrf()) {
	switch ($action) {
	case 'update':
		if ($username !== Auth::user()->getUserName() && User::findByUserName($username)) {
			FlashMessages::addMessage(I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.'));
		} elseif ($email !== Auth::user()->getEmail() && User::findByEmail($email)) {
			FlashMessages::addMessage(I18N::translate('Duplicate email address. A user with that email already exists.'));
		} else {
			// Change username
			if ($username !== Auth::user()->getUserName()) {
				Log::addAuthenticationLog('User ' . Auth::user()->getUserName() . ' renamed to ' . $username);
				Auth::user()->setUserName($username);
			}

			// Change password
			if ($password_1 !== '' && $password_1 === $password_2) {
				Auth::user()->setPassword($password_1);
			}

			// Change other settings
			Auth::user()
				->setRealName($real_name)
				->setEmail($email)
				->setPreference('language', $language)
				->setPreference('TIMEZONE', $time_zone)
				->setPreference('contactmethod', $contact_method)
				->setPreference('visibleonline', $visible_online ? '1' : '0');

			Auth::user()->setPreference('theme', $theme);

			$WT_TREE->setUserPreference(Auth::user(), 'rootid', $root_id);
		}
		break;

	case 'delete':
		// An administrator can only be deleted by another administrator
		if (!Auth::user()->getPreference('canadmin')) {
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
	->pageHeader();

$my_individual_record = Individual::getInstance($WT_TREE->getUserPreference(Auth::user(), 'gedcomid'), $WT_TREE);
$default_individual   = Individual::getInstance($WT_TREE->getUserPreference(Auth::user(), 'rootid'), $WT_TREE);

// Form validation
?>
<script>
function checkform(frm) {
	if (frm.form_pass1.value!=frm.form_pass2.value) {
		alert("<?= I18N::translate('The passwords do not match.') ?>");
		frm.form_pass1.focus();
		return false;
	}
	if (frm.form_pass1.value.length > 0 && frm.form_pass1.value.length < 6) {
		alert("<?= I18N::translate('Passwords must contain at least 6 characters.') ?>");
		frm.form_pass1.focus();
		return false;
	}
	return true;
}
</script>

<h2><?= $controller->getPageTitle() ?></h2>

<form name="editform" method="post" onsubmit="return checkform(this);">
	<input type="hidden" name="action" value="update">
	<?= Filter::getCsrf() ?>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="username">
			<?= I18N::translate('Username') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="username" name="username" value="<?= Filter::escapeHtml(Auth::user()->getUserName()) ?>" dir="auto" aria-describedby="username-description" required>
			<p class="small text-muted" id="username-description">
				<?= I18N::translate('Usernames are case-insensitive and ignore accented letters, so that “chloe”, “chloë”, and “Chloe” are considered to be the same.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="real-name">
			<?= I18N::translate('Real name') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="real-name" name="real-name" value="<?= Filter::escapeHtml(Auth::user()->getRealName()) ?>" dir="auto" aria-describedby="real-name-description" required>
			<p class="small text-muted" id="username-description">
				<?= I18N::translate('This is your real name, as you would like it displayed on screen.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="gedcom-id">
			<?= I18N::translate('Individual record') ?>
		</label>
		<div class="col-sm-9">
			<select class="form-control" id="gedcom-id" aria-describedby="gedcom-id-description" disabled>
			<?php if ($my_individual_record instanceof Individual): ?>
				<option value=""><?= $my_individual_record->getFullName() ?></option>
			<?php else: ?>
				<option value=""><?= I18N::translateContext('unknown people', 'Unknown') ?></option>
			<?php endif ?>
			</select>
			<p class="small text-muted" id="gedcom-id-description">
				<?= I18N::translate('This is a link to your own record in the family tree. If this is the wrong individual, contact an administrator.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="root-id">
			<?= I18N::translate('Default individual') ?>
		</label>
		<div class="col-sm-9">
			<?= FunctionsEdit::formControlIndividual($default_individual, ['id' => 'root-id', 'name' => 'root-id', 'aria-describedby' => 'root-id-description']) ?>
			<p class="small text-muted" id="root-id-description">
				<?= I18N::translate('This individual will be selected by default when viewing charts and reports.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="password-1">
			<?= I18N::translate('Password') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" type="password" id="password-1" name="password-1" aria-describedby="password-1-description">
			<p class="small text-muted" id="password-1-description">
				<?= I18N::translate('Passwords must be at least 6 characters long and are case-sensitive, so that “secret” is different from “SECRET”.') ?>
				<br>
				<?= I18N::translate('Leave the password blank if you want to keep the current password.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="password-2">
			<?= I18N::translate('Confirm password') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" type="password" id="password-2" name="password-2" aria-describedby="password-2-description">
			<p class="small text-muted" id="password-2-description">
				<?= I18N::translate('Type your password again, to make sure you have typed it correctly.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="language">
			<?= I18N::translate('Language') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select(FunctionsEdit::optionsInstalledLanguages(), Auth::user()->getPreference('language'), ['id' => 'language', 'name' => 'language']) ?>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="time-zone">
			<?= I18N::translate('Time zone') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select(array_combine(DateTimeZone::listIdentifiers(), DateTimeZone::listIdentifiers()), Auth::user()->getPreference('TIMEZONE', 'UTC'), ['id' => 'time-zone', 'name', 'time-zone', 'aria-describedby' => 'time-zone-description']) ?>
			<p class="small text-muted" id="time-zone-description">
				<?= I18N::translate('The time zone is required for date calculations, such as knowing today’s date.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="email">
			<?= I18N::translate('Email address') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" type="email" id="email" name="email" value="<?= Filter::escapeHtml(Auth::user()->getEmail()) ?>" aria-describedby="email-description">
			<p class="small text-muted" id="email-description">
				<?= I18N::translate('This email address will be used to send password reminders, website notifications, and messages from other family members who are registered on the website.') ?>
			</p>
		</div>
	</div>

	<?php if (Site::getPreference('ALLOW_USER_THEMES') === '1'): ?>
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="theme">
			<?= I18N::translate('Theme') ?>
		</label>
		<div class="col-sm-9">
			<select class="form-control" id="theme" name="theme" aria-describedby="theme-description">
				<option value="">
					<?= Filter::escapeHtml(/* I18N: default option in list of themes */ I18N::translate('<default theme>')) ?>
				</option>
				<?php foreach (Theme::themeNames() as $theme_id => $theme_name): ?>
					<option value="<?= $theme_id ?>" <?= $theme_id === Auth::user()->getPreference('theme') ? 'selected' : '' ?>>
						<?= $theme_name ?>
					</option>
				<?php endforeach ?>
			</select>
			<p class="small text-muted" id="theme-description">
				<?= /* I18N: Help text for the "Default theme" site configuration setting */ I18N::translate('You can change the appearance of webtrees using “themes”. Each theme has a different style, layout, color scheme, etc.') ?>
			</p>
		</div>
	</div>
	<?php endif ?>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="contact-method">
			<?= I18N::translate('Contact method') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select(FunctionsEdit::optionsContactMethods(), Auth::user()->getPreference('contactmethod'), ['id' => 'contact-method', 'name' => 'contact-method', 'aria-describedby' => 'contact-method-description']) ?>
			<p class="small text-muted" id="contact-method-description">
				<?= I18N::translate('Site members can send each other messages. You can choose to how these messages are sent to you, or choose not receive them at all.') ?>
			</p>
		</div>
	</div>

	<fieldset class="form-group row">
		<legend  class="col-sm-3 col-form-legend">
			<?= I18N::translate('Visible online') ?>
		</legend>
		<div class="col-sm-9">
			<?= Bootstrap4::checkbox(I18N::translate('Visible to other users when online'), false, ['name' => 'visible-online', 'checked' => (bool) Auth::user()->getPreference('visibleonline'), 'aria-describedby' => 'visible-online-description']) ?>
			<p class="small text-muted" id="visible-online-description">
				<?= I18N::translate('You can choose whether to appear in the list of users who are currently signed-in.') ?>
			</p>
		</div>
	</fieldset>

	<div class="row form-group">
		<div class="col-sm-9 offset-sm-3">
			<input class="btn btn-primary" type="submit" value="<?= I18N::translate('save') ?>">
		</div>
	<div class="row form-group">
</form>

<?php if (!Auth::user()->getPreference('canadmin')): ?>
<form method="post">
	<input type="hidden" name="action" value="delete">
	<?= Filter::getCsrf() ?>
	<div class="row form-group">
		<div class="col-sm-9 offset-sm-3">
			<input class="btn btn-danger" type="submit" value="<?= I18N::translate('Delete your account') ?>" onclick="return confirm('<?= I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeJs(Auth::user()->getUserName())) ?>');">
		</div>
	</div>
</form>
<?php endif ?>
