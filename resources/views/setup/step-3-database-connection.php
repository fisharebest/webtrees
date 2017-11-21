<?php use Fisharebest\Webtrees\I18N; ?>

<form method="POST" autocomplete="off">
	<input name="route" type="hidden" value="setup">
	<input name="lang" type="hidden" value="<?= e($lang) ?>">
	<input name="dbname" type="hidden" value="<?= e($dbname) ?>">
	<input name="tblpfx" type="hidden" value="<?= e($tblpfx) ?>">
	<input name="wtname" type="hidden" value="<?= e($wtname) ?>">
	<input name="wtuser" type="hidden" value="<?= e($wtuser) ?>">
	<input name="wtpass" type="hidden" value="<?= e($wtpass) ?>">
	<input name="wtemail" type="hidden" value="<?= e($wtemail) ?>">

	<h2><?= I18N::translate('Connection to database server') ?></h2>

	<p>
		<?= I18N::translate('webtrees needs a MySQL database, version %s or later.', '5') ?>
	</p>

	<p>
		<?= I18N::translate('Your server’s administrator will provide you with the connection details.') ?>
	</p>

	<h3><?= I18N::translate('Database connection') ?></h3>

	<?php if ($db_conn_error): ?>
		<p class="alert alert-danger">
			<?= $db_conn_error ?>
		</p>
	<?php endif ?>

	<div class="row form-group">
		<label class="col-form-label col-sm-3" for="dbhost">
			<?= I18N::translate('Server name') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" id="dbhost" name="dbhost" type="text" value="<?= e($dbhost) ?>" dir="ltr" required>
			<p class="small text-muted">
				<?= I18N::translate('Most sites are configured to use localhost. This means that your database runs on the same computer as your web server.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-form-label col-sm-3" for="dbport">
			<?= I18N::translate('Port number') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" id="dbport" name="dbport" pattern="\d+" type="text" value="<?= e($dbport) ?>" dir="ltr" required>
			<p class="small text-muted">
				<?= I18N::translate('Most sites are configured to use the default value of 3306.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-form-label col-sm-3" for="dbuser">
			<?= I18N::translate('Database user account') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" id="dbuser" name="dbuser" type="text" value="<?= e($dbuser) ?>" dir="ltr" required>
			<p class="small text-muted">
				<?= I18N::translate('This is case sensitive.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-form-label col-sm-3" for="dbpass">
			<?= I18N::translate('Database password') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" id="dbpass" name="dbpass" type="password" value="<?= e($dbpass) ?>" dir="ltr">
			<p class="small text-muted">
				<?= I18N::translate('This is case sensitive.') ?>
			</p>
		</div>
	</div>

	<hr>

	<button class="btn btn-primary" name="step" type="submit" value="4">
		<?= I18N::translate('next') ?>
	</button>

	<button class="btn btn-link" name="step" type="submit" value="2">
		<?= I18N::translate('previous') ?>
	</button>
</form>
