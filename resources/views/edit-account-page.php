<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<script>
  function checkform(frm) {
    if (frm.password.value !== frm.password2.value) {
      alert("<?= I18N::translate('The passwords do not match.') ?>");
      frm.password.focus();
      return false;
    }
    if (frm.password.value.length > 0 && frm.password.value.length < 6) {
      alert("<?= I18N::translate('Passwords must contain at least 6 characters.') ?>");
      frm.password.focus();
      return false;
    }
    return true;
  }
</script>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form class="wt-page-options wt-page-options-my-account" method="post" onsubmit="return checkform(this);">
	<?= csrf_field() ?>
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="user-name">
			<?= I18N::translate('Username') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input type="text" class="form-control" id="user-name" name="user_name" value="<?= e($user->getUserName()) ?>" dir="auto" aria-describedby="username-description" required>
			<p class="small text-muted" id="username-description">
				<?= I18N::translate('Usernames are case-insensitive and ignore accented letters, so that “chloe”, “chloë”, and “Chloe” are considered to be the same.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="real-name">
			<?= I18N::translate('Real name') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input type="text" class="form-control" id="real-name" name="real_name" value="<?= e($user->getRealName()) ?>" dir="auto" aria-describedby="real-name-description" required>
			<p class="small text-muted" id="username-description">
				<?= I18N::translate('This is your real name, as you would like it displayed on screen.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="gedcom-id">
			<?= I18N::translate('Individual record') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<select class="form-control" id="gedcom-id" aria-describedby="gedcom-id-description" disabled>
				<?php if ($my_individual_record !== null): ?>
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
		<label class="col-sm-3 col-form-label wt-page-options-label" for="root-id">
			<?= I18N::translate('Default individual') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= FunctionsEdit::formControlIndividual($tree, $default_individual, ['id' => 'root-id',
 'name' => 'root_id', 'aria-describedby' => 'root-id-description']) ?>
			<p class="small text-muted" id="root-id-description">
				<?= I18N::translate('This individual will be selected by default when viewing charts and reports.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="password">
			<?= I18N::translate('Password') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control" type="password" id="password" name="password" aria-describedby="password-description" autocomplete="new-password">
			<p class="small text-muted" id="password-description">
				<?= I18N::translate('Passwords must be at least 6 characters long and are case-sensitive, so that “secret” is different from “SECRET”.') ?>
				<br>
				<?= I18N::translate('Leave the password blank if you want to keep the current password.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="password2">
			<?= I18N::translate('Confirm password') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control" type="password" id="password2" name="password2" aria-describedby="password2-description" autocomplete="new-password">
			<p class="small text-muted" id="password2-description">
				<?= I18N::translate('Type your password again, to make sure you have typed it correctly.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="language">
			<?= I18N::translate('Language') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= Bootstrap4::select($installed_languages, $user->getPreference('language'), ['id' => 'language', 'name' => 'language']) ?>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="timezone">
			<?= I18N::translate('Time zone') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= Bootstrap4::select($timezones, $user->getPreference('TIMEZONE', 'UTC'), ['id' => 'timezone', 'name' => 'timezone', 'aria-describedby' => 'timezone-description']) ?>
			<p class="small text-muted" id="timezone-description">
				<?= I18N::translate('The time zone is required for date calculations, such as knowing today’s date.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="email">
			<?= I18N::translate('Email address') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control" type="email" id="email" name="email" value="<?= e($user->getEmail()) ?>" aria-describedby="email-description">
			<p class="small text-muted" id="email-description">
				<?= I18N::translate('This email address will be used to send password reminders, website notifications, and messages from other family members who are registered on the website.') ?>
			</p>
		</div>
	</div>

	<?php if ($allow_user_themes): ?>
		<div class="row form-group">
			<label class="col-sm-3 col-form-label wt-page-options-label" for="theme">
				<?= I18N::translate('Theme') ?>
			</label>
			<div class="col-sm-9 wt-page-options-value">
				<?= Bootstrap4::select($themes, $user->getPreference('theme'), ['id' => 'theme', 'name' => 'theme', 'aria-describedby' => 'theme-description',]) ?>
				<p class="small text-muted" id="theme-description">
					<?= /* I18N: Help text for the "Default theme" site configuration setting */
					I18N::translate('You can change the appearance of webtrees using “themes”. Each theme has a different style, layout, color scheme, etc.') ?>
				</p>
			</div>
		</div>
	<?php endif ?>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="contact-method">
			<?= I18N::translate('Contact method') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= Bootstrap4::select($contact_methods, $user->getPreference('contactmethod'), ['id' => 'contact-method', 'name' => 'contact_method', 'aria-describedby' => 'contact-method-description',]) ?>
			<p class="small text-muted" id="contact-method-description">
			<?= I18N::translate('Site members can send each other messages. You can choose to how these messages are sent to you, or choose not receive them at all.') ?>
			</p>
		</div>
	</div>

	<fieldset class="form-group">
		<div class="row">
			<legend class="col-sm-3 col-form-label wt-page-options-label">
				<?= I18N::translate('Visible online') ?>
			</legend>
			<div class="col-sm-9 wt-page-options-value">
				<?= Bootstrap4::checkbox(I18N::translate('Visible to other users when online'), false, ['name' => 'visible_online', 'checked' => (bool) $user->getPreference('visibleonline'), 'aria-describedby' => 'visible-online-description']) ?>
				<p class="small text-muted" id="visible-online-description">
					<?= I18N::translate('You can choose whether to appear in the list of users who are currently signed-in.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<div class="row form-group">
		<div class="col-sm-3 wt-page-options-label"></div>
		<div class="col-sm-9 wt-page-options-value">
			<input class="btn btn-primary" type="submit" value="<?= I18N::translate('save') ?>">
		</div>
	</div>
</form>

<?php if ($show_delete_option): ?>
	<form action="<?= e(route('delete-account', ['ged' => $tree->getName()])) ?>" method="post">
		<?= csrf_field() ?>
		<div class="row form-group">
			<div class="col-sm-3 wt-page-options-label"></div>
			<div class="col-sm-9 wt-page-options-value">
				<input class="btn btn-danger" type="submit" value="<?= I18N::translate('Delete your account') ?>" data-confirm="<?= I18N::translate('Are you sure you want to delete “%s”?', e($user->getUserName())) ?>" onclick="return confirm(this.dataset.confirm);">
			</div>
		</div>
	</form>
<?php endif ?>
