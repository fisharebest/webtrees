<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Site; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<form method="post" class="form-horizontal">
	<?= csrf_field() ?>

	<!-- WELCOME_TEXT_AUTH_MODE -->
	<div class="row form-group">
		<label for="WELCOME_TEXT_AUTH_MODE" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Welcome text on sign-in page') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select($registration_text_options, Site::getPreference('WELCOME_TEXT_AUTH_MODE'), ['id' => 'WELCOME_TEXT_AUTH_MODE', 'name' => 'WELCOME_TEXT_AUTH_MODE']) ?>
			<p class="small text-muted">
			</p>
		</div>
	</div>

	<!-- WELCOME_TEXT_AUTH_MODE_4 -->
	<div class="row form-group">
		<label for="WELCOME_TEXT_AUTH_MODE_4" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Custom welcome text') ?>
		</label>
		<div class="col-sm-9">
			<textarea class="form-control" maxlength="2000" id="WELCOME_TEXT_AUTH_MODE_4" name="WELCOME_TEXT_AUTH_MODE_4" rows="4"><?= e(Site::getPreference('WELCOME_TEXT_AUTH_MODE_' . WT_LOCALE)) ?></textarea>
			<p class="small text-muted">
				<?= /* I18N: Help text for the "Custom welcome text" site configuration setting */ I18N::translate('To set this text for other languages, you must switch to that language, and visit this page again.') ?>
			</p>
		</div>
	</div>

	<!-- USE_REGISTRATION_MODULE -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Allow visitors to request a new user account') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('USE_REGISTRATION_MODULE', FunctionsEdit::optionsNoYes(), (string) (int) Site::getPreference('USE_REGISTRATION_MODULE'), true) ?>
				<p class="small text-muted">
					<?= I18N::translate('The new user will be asked to confirm their email address before the account is created.') ?>
					<?= I18N::translate('Details of the new user will be sent to the genealogy contact for the corresponding family tree.') ?>
					<?= I18N::translate('An administrator must approve the new user account and select an access level before the user can sign in.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- SHOW_REGISTER_CAUTION -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Show acceptable use agreement on “Request a new user account” page') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('SHOW_REGISTER_CAUTION', FunctionsEdit::optionsNoYes(), (string) (int) Site::getPreference('SHOW_REGISTER_CAUTION'), true) ?>
				<p class="small text-muted">
				</p>
			</div>
		</div>
	</fieldset>

	<div class="row form-group">
		<div class="offset-sm-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<i class="fas fa-check"></i>
				<?= I18N::translate('save') ?>
			</button>

			<a href="<?= e(route('admin-control-panel')) ?>" class="btn btn-secondary">
				<i class="fas fa-times"></i>
				<?= I18N::translate('cancel') ?>
			</a>
		</div>
	</div>
</form>
