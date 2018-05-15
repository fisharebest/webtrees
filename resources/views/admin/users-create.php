<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-users') => I18N::translate('User administration'), $title]]) ?>

<h1><?= $title ?></h1>

<form class="form-horizontal" name="newform" method="post" autocomplete="off" action="<?= e(route('admin-users-create')) ?>">
	<?= csrf_field() ?>

	<!-- REAL NAME -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="real_name">
			<?= I18N::translate('Real name') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" type="text" id="real_name" name="real_name" required maxlength="64" value="<?= e($real_name) ?>" dir="auto">
			<p class="small text-muted">
				<?= I18N::translate('This is your real name, as you would like it displayed on screen.') ?>
			</p>
		</div>
	</div>

	<!-- USER NAME -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="username">
			<?= I18N::translate('Username') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" type="text" id="username" name="username" required maxlength="32" value="<?= e($username) ?>" dir="auto">
			<p class="small text-muted">
				<?= I18N::translate('Usernames are case-insensitive and ignore accented letters, so that “chloe”, “chloë”, and “Chloe” are considered to be the same.') ?>
			</p>
		</div>
	</div>

	<!-- PASSWORD -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="pass1">
			<?= I18N::translate('Password') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" type="password" id="pass1" name="pass1" pattern = "<?= WT_REGEX_PASSWORD ?>" placeholder="<?= I18N::plural('Use at least %s character.', 'Use at least %s characters.', WT_MINIMUM_PASSWORD_LENGTH, I18N::number(WT_MINIMUM_PASSWORD_LENGTH)) ?>" required onchange="form.pass2.pattern = regex_quote(this.value);" autocomplete="new-password">
			<p class="small text-muted">
				<?= I18N::translate('Passwords must be at least 6 characters long and are case-sensitive, so that “secret” is different from “SECRET”.') ?>
			</p>
		</div>
	</div>

	<!-- CONFIRM PASSWORD -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="pass2">
			<?= I18N::translate('Confirm password') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" type="password" id="pass2" name="pass2" pattern = "<?= WT_REGEX_PASSWORD ?>" placeholder="<?= I18N::translate('Type the password again.') ?>" required autocomplete="new-password">
		</div>
	</div>

	<!-- EMAIL ADDRESS -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="email">
			<?= I18N::translate('Email address') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" type="email" id="email" name="email" required maxlength="64" value="<?= e($email) ?>">
			<p class="small text-muted">
				<?= I18N::translate('This email address will be used to send password reminders, website notifications, and messages from other family members who are registered on the website.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<div class="offset-sm-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<?= I18N::translate('save') ?>
			</button>
		</div>
	</div>
</form>

<?php View::push('javascript') ?>
<script>
  function regex_quote(str) {
    return str.replace(/[\\.?+*()[\](){}|]/g, "\\$&");
  }
</script>
<?php View::endpush() ?>
