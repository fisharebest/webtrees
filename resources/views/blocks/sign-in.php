<?php use Fisharebest\Webtrees\I18N; ?>

<form name="login-form" method="POST" action="<?= WT_LOGIN_URL ?>">
	<input type="hidden" name="action" value="login">
	<input type="hidden" name="url" value="<?= e(route('user-page')) ?>">

	<div class="form-group">
		<label for="username">
			<?= I18N::translate('Username') ?>
		</label>
		<input type="text" id="username" name="username" class="form-control">
	</div>

	<div class="form-group">
		<label for="password">
			<?= I18N::translate('Password') ?>
		</label>
		<input type="password" id="password" name="password" class="form-control">
	</div>

	<div>
		<button type="submit" class="btn btn-primary">
			<?= /* I18N: A button label. */ I18N::translate('sign in') ?>
		</button>

		<button type="button" class="btn btn-secondary" data-toggle="collapse" data-target="#forgot-password" aria-expanded="false" aria-controls="forgot-password">
			<?= I18N::translate('Forgot password?') ?>
		</button>
	</div>
</form>

<?php if ($allow_register): ?>
	<a class="btn btn-link" href="<?= WT_LOGIN_URL ?>?action=register">
		<?= I18N::translate('Request a new user account') ?>
	</a>
<?php endif ?>

<div class="collapse" id="forgot-password">
	<form action="<?= WT_LOGIN_URL ?>" method="POST">
		<input type="hidden" name="time" value="">
		<input type="hidden" name="action" value="requestpw">
		<?= I18N::translate('Request a new password') ?>
		<div class="form-group">
			<label for="new_passwd_username">
				<?= I18N::translate('Username or email address') ?>
				<input type="text" id="new_passwd_username" name="new_passwd_username" class="form-control">
			</label>
		</div>
		<div>
			<button type="submit" class="btn btn-primary">
				<?= /* I18N: A button label. */ I18N::translate('continue') ?>
			</button>
		</div>
	</form>
</div>

