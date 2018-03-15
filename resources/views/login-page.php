<?php use Fisharebest\Webtrees\I18N; ?>

<?php if ($error): ?>
	<div class="alert alert-danger">
		<?= $error ?>
	</div>
<?php endif ?>

<h2 class="wt-page-title">
	<?= I18N::translate('Welcome to this genealogy website') ?>
</h2>

<p>
	<?= nl2br($welcome, false) ?>
</p>

<form action="<?= e(route('login')) ?>" class="wt-page-options wt-page-options-login" method="post">
	<?= csrf_field() ?>
	<input type="hidden" name="url" value="<?= e($url) ?>">

	<div class="form-group row">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="username">
			<?= I18N::translate('Username') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control" type="text" id="username" name="username" required value="<?= e($username) ?>">
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="password">
			<?= I18N::translate('Password') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input autocomplete="current-password" class="form-control" id="password" name="password" required type="password">
		</div>
	</div>

	<div class="form-group row">
		<div class="col-sm-3 col-form-label wt-page-options-label">
		</div>
		<div class="col-sm-9 wt-page-options-value">
			<button class="btn btn-primary" type="submit">
				<?= /* I18N: A button label. */ I18N::translate('sign in') ?>
			</button>
			<!--
			Emails are sent from a TREE, not from a SITE. Therefore if there is no
			tree available (initial setup or all trees private), then we can't send email.
			-->
			<?php if ($tree): ?>
				<a class="btn btn-link" href="<?= e(route('forgot-password')) ?>">
					<?= I18N::translate('Forgot password?') ?>
				</a>

				<?php if ($can_register): ?>
					<a class="btn btn-link" href="<?= e(route('register')) ?>">
						<?= I18N::translate('Request a new user account') ?>
					</a>
				<?php endif ?>
			<?php endif ?>
		</div>
	</div>
</form>
