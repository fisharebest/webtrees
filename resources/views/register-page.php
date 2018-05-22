<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<?php if ($show_caution): ?>
	<div class="wt-register-caution">
		<?= I18N::translate('<div class="largeError">Notice:</div><div class="error">By completing and submitting this form, you agree:<ul><li>to protect the privacy of living individuals listed on our site;</li><li>and in the text box below, to explain to whom you are related, or to provide us with information on someone who should be listed on our website.</li></ul></div>') ?>
	</div>
<?php endif ?>

<form autocomplete="off" class="wt-page-options wt-page-options-register" method="post" action="<?= e(route('register')) ?>">
	<?= csrf_field() ?>
	<input name="ged" type="hidden" value="<?= e($tree->getName()) ?>">

	<div class="form-group row">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="realname">
			<?= I18N::translate('Real name') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control" type="text" id="realname" name="realname" required maxlength="64" value="<?= e($realname) ?>">
			<p class="small text-muted">
				<?= I18N::translate('This is your real name, as you would like it displayed on screen.') ?>
			</p>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="email">
			<?= I18N::translate('Email address') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control" type="email" id="email" name="email" required maxlength="64" value="<?= e($email) ?>">
			<p class="small text-muted">
				<?= I18N::translate('This email address will be used to send password reminders, website notifications, and messages from other family members who are registered on the website.') ?>
			</p>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="username">
			<?= I18N::translate('Username') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control" type="text" id="username" name="username" required maxlength="32" value="<?= e($username) ?>" autocomplete="username">
			<p class="small text-muted">
				<?= I18N::translate('Usernames are case-insensitive and ignore accented letters, so that “chloe”, “chloë”, and “Chloe” are considered to be the same.') ?>
			</p>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="password1">
			<?= I18N::translate('Password') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control" type="password" id="password1" name="password1" placeholder="<?= /* I18N: placeholder text for new-password field */ I18N::plural('Use at least %s character.', 'Use at least %s characters.', WT_MINIMUM_PASSWORD_LENGTH, I18N::number(WT_MINIMUM_PASSWORD_LENGTH)) ?>" pattern="<?=  WT_REGEX_PASSWORD ?>" onchange="document.getElementById('password2').pattern = regex_quote(this.value);" required>
			<p class="small text-muted">
				<?= I18N::translate('Passwords must be at least 6 characters long and are case-sensitive, so that “secret” is different from “SECRET”.') ?>
			</p>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="password2">
			<?= I18N::translate('Confirm password') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control" required type="password" id="password2" name="password2" placeholder="<?= /* I18N: placeholder text for repeat-password field */ I18N::translate('Type the password again.') ?>" pattern="<?= WT_REGEX_PASSWORD ?>">
			<p class="small text-muted">
				<?= I18N::translate('Type your password again, to make sure you have typed it correctly.') ?>
			</p>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="comments">
			<?= I18N::translate('Comments') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<textarea class="form-control" required rows="4" id="comments" name="comments" placeholder="<?php /* I18N: placeholder text for registration-comments field */ I18N::translate('Explain why you are requesting an account.') ?>"><?= e($comments) ?></textarea>
			<p class="small text-muted">
				<?= I18N::translate('Use this field to tell the site administrator why you are requesting an account and how you are related to the genealogy displayed on this site. You can also use this to enter any other comments you may have for the site administrator.') ?>
			</p>
		</div>
	</div>

	<div class="form-group row">
		<div class="col-sm-3 col-form-label wt-page-options-label">
		</div>
		<div class="col-sm-9 wt-page-options-value">
		<button class="btn btn-primary">
			<?= I18N::translate('continue') ?>
		</button>
	</div>
</form>


<?php View::push('javascript') ?>
<script>
  function regex_quote(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  }
</script>
<?php View::endpush() ?>
