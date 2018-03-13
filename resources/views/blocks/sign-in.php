<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-signin">
	<div class="wt-page-options wt-page-options-login">
		<form action="<?= route('login', ['url' => route('user-page')]) ?>" method="post">
			<?= csrf_field() ?>

			<div class="form-label-group mb-2">
				<label for="username" class="col-form-label sr-only">
					<?= I18N::translate('Username') ?>
				</label>
				<input type="text" id="username" name="username" class="form-control"
					value="" placeholder="<?= I18N::translate('Username') ?>" required autofocus>
			</div>

			<div class="form-label-group mb-3">
				<label for="password" class="col-form-label sr-only">
					<?= I18N::translate('Password') ?>
				</label>
				<input autocomplete="current-password" type="password" id="password"
					name="password" class="form-control" placeholder="<?= I18N::translate('Password') ?>" required>
			</div>

			<button class="btn btn-lg btn-primary btn-block mb-2" type="submit">
				<?= I18N::translate('sign in') ?>
			</button>
		</form>
	</div>

	<a class="password-reset" href="<?= e(route('forgot-password')) ?>">
		<?= I18N::translate('Forgot password?') ?>
	</a>

	<?php if ($allow_register): ?>
		<a class="new-account" href="<?= e(route('register')) ?>">
			<?= I18N::translate('Request a new user account') ?>
		</a>
	<?php endif; ?>
</div>
