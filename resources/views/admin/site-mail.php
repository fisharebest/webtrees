<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Site; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<p class="alert alert-info">
	<?= I18N::translate('To use a Google mail account, use the following settings: server=smtp.gmail.com, port=587, security=tls, username=xxxxx@gmail.com, password=[your gmail password]') . '<br>' . I18N::translate('You must also enable “less secure applications” in your Google account.') . ' <a href="https://www.google.com/settings/security/lesssecureapps">https://www.google.com/settings/security/lesssecureapps</a>' ?>
</p>

<form method="post" class="form-horizontal">
	<?= csrf_field() ?>

	<!-- SMTP_ACTIVE -->
	<div class="row form-group">
		<label for="SMTP_ACTIVE" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Messages') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select($mail_transport_options, Site::getPreference('SMTP_ACTIVE'), ['id' => 'SMTP_ACTIVE', 'name' => 'SMTP_ACTIVE']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Messages” site configuration setting */ I18N::translate('webtrees needs to send emails, such as password reminders and website notifications. To do this, it can use this server’s built in PHP mail facility (which is not always available) or an external SMTP (mail-relay) service, for which you will need to provide the connection details.') ?>
			</p>
		</div>
	</div>

	<!-- SMTP_FROM_NAME -->
	<div class="row form-group">
		<label for="SMTP_FROM_NAME" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Sender name') ?>
		</label>
		<div class="col-sm-9">
			<input type="email" class="form-control" id="SMTP_FROM_NAME" name="SMTP_FROM_NAME" value="<?= e(Site::getPreference('SMTP_FROM_NAME')) ?>" placeholder="no-reply@localhost" maxlength="255">
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Sender name” site configuration setting */ I18N::translate('This name is used in the “From” field, when sending automatic emails from this server.') ?>
			</p>
		</div>
	</div>

	<h2><?= I18N::translate('SMTP mail server') ?></h2>

	<!-- SMTP_HOST -->
	<div class="row form-group">
		<label for="SMTP_HOST" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Server name') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="SMTP_HOST" name="SMTP_HOST" value="<?= e(Site::getPreference('SMTP_HOST')) ?>" placeholder="smtp.example.com" maxlength="255" pattern="[a-z0-9-]+(\.[a-z0-9-]+)*">
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Server name” site configuration setting */ I18N::translate('This is the name of the SMTP server. “localhost” means that the mail service is running on the same computer as your web server.') ?>
			</p>
		</div>
	</div>

	<!-- SMTP_PORT -->
	<div class="row form-group">
		<label for="SMTP_PORT" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Port number') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="SMTP_PORT" name="SMTP_PORT" value="<?= e(Site::getPreference('SMTP_PORT')) ?>" pattern="[0-9]*" placeholder="25" maxlength="5">
			<p class="small text-muted">
				<?= /* I18N: Help text for the "Port number" site configuration setting */ I18N::translate('By default, SMTP works on port 25.') ?>
			</p>
		</div>
	</div>

	<!-- SMTP_AUTH -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Use password') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('SMTP_AUTH', FunctionsEdit::optionsNoYes(), Site::getPreference('SMTP_AUTH'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Use password” site configuration setting */ I18N::translate('Most SMTP servers require a password.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- SMTP_AUTH_USER -->
	<div class="row form-group">
		<label for="SMTP_AUTH_USER" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Username') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="SMTP_AUTH_USER" name="SMTP_AUTH_USER" value="<?= e(Site::getPreference('SMTP_AUTH_USER')) ?>" maxlength="255">
			<p class="small text-muted">
				<?= /* I18N: Help text for the "Username" site configuration setting */ I18N::translate('The username required for authentication with the SMTP server.') ?>
			</p>
		</div>
	</div>

	<!-- SMTP_AUTH_PASS -->
	<div class="row form-group">
		<label for="SMTP_AUTH_PASS" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Password') ?>
		</label>
		<div class="col-sm-9">
			<input type="password" class="form-control" id="SMTP_AUTH_PASS" name="SMTP_AUTH_PASS" value="">
			<p class="small text-muted">
				<?= /* I18N: Help text for the "Password" site configuration setting */ I18N::translate('The password required for authentication with the SMTP server.') ?>
			</p>
		</div>
	</div>

	<!-- SMTP_SSL -->
	<div class="row form-group">
		<label for="SMTP_SSL" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Secure connection') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select($mail_ssl_options, Site::getPreference('SMTP_SSL'), ['id' => 'SMTP_SSL', 'name' => 'SMTP_SSL']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Secure connection” site configuration setting */ I18N::translate('Most servers do not use secure connections.') ?>
			</p>
		</div>
	</div>

	<!-- SMTP_HELO -->
	<div class="row form-group">
		<label for="SMTP_HELO" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Sending server name') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="SMTP_HELO" name="SMTP_HELO" value="<?= e(Site::getPreference('SMTP_HELO')) ?>" placeholder="localhost" maxlength="255" pattern="[a-z0-9-]+(\.[a-z0-9-]+)*">
			<p class="small text-muted">
				<?= /* I18N: Help text for the "Sending server name" site configuration setting */ I18N::translate('Many mail servers require that the sending server identifies itself correctly, using a valid domain name.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<div class="offset-sm-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<i class="fas fa-check" aria-hidden="true"></i>
				<?= I18N::translate('save') ?>
			</button>

			<a href="<?= e(route('admin-control-panel')) ?>" class="btn btn-secondary">
				<i class="fas fa-times" aria-hidden="true"></i>
				<?= I18N::translate('cancel') ?>
			</a>
		</div>
	</div>
</form>
