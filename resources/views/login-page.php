<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= I18N::translate('Welcome to this genealogy website') ?>
</h2>

<p>
	<?= nl2br($welcome, false) ?>
</p>

<div class="form-login">
	<?php if ($error): ?>
		<div class="alert alert-danger">
			<?= $error ?>
		</div>
	<?php endif; ?>

	<div class="wt-page-options wt-page-options-login">
		<form action="<?= e(route('login')) ?>" method="post">
			<?= csrf_field() ?>
			<input type="hidden" name="url" value="<?= e($url) ?>">

			<div class="form-label-group mb-2">
				<label for="username" class="col-form-label sr-only">
					<?= I18N::translate('Username') ?>
				</label>
				<input type="text" id="username" name="username" class="form-control"
					value="<?= e($username) ?>" placeholder="<?= I18N::translate('Username') ?>" required autofocus>
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

			<?php /* Emails are sent from a TREE, not from a SITE. Therefore if there is no
					tree available (initial setup or all trees private), then we can't send email. */ ?>
			<?php if ($tree): ?>
				<div class="password-reset clearfix">
					<a class="float-right" href="<?= e(route('forgot-password')) ?>">
						<?= I18N::translate('Forgot password?') ?>
					</a>
				</div>
			<?php endif; ?>
		</form>
	</div>

	<?php if ($tree && $can_register): ?>
		<a class="text-center new-account mt-3" href="<?= e(route('register')) ?>">
			<?= I18N::translate('Request a new user account') ?>
		</a>
	<?php endif; ?>
</div>
