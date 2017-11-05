<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<form method="POST" autocomplete="off">
	<input name="route" type="hidden" value="setup">
	<input name="lang" type="hidden" value="<?= Html::escape($lang) ?>">
	<input name="dbhost" type="hidden" value="<?= Html::escape($dbhost) ?>">
	<input name="dbport" type="hidden" value="<?= Html::escape($dbport) ?>">
	<input name="dbuser" type="hidden" value="<?= Html::escape($dbuser) ?>">
	<input name="dbpass" type="hidden" value="<?= Html::escape($dbpass) ?>">
	<input name="wtname" type="hidden" value="<?= Html::escape($wtname) ?>">
	<input name="wtuser" type="hidden" value="<?= Html::escape($wtuser) ?>">
	<input name="wtpass" type="hidden" value="<?= Html::escape($wtpass) ?>">
	<input name="wtemail" type="hidden" value="<?= Html::escape($wtemail) ?>">

	<h2>
		<?= I18N::translate('Database and table names') ?>
	</h2>

	<p>
		<?= I18N::translate('A database server can store many separate databases. You need to select an existing database (created by your server’s administrator) or create a new one (if your database user account has sufficient privileges).') ?>
	</p>

	<?php if ($db_conn_error): ?>
		<p class="alert alert-danger">
			<?= $db_error ?>
		</p>
	<?php endif ?>

	<div class="row form-group">
		<label class="col-form-label col-sm-3" for="dbname">
			<?= I18N::translate('Database name') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" dir="ltr" id="dbname" name="dbname" pattern="^[^`]+$" required type="text" value="<?= Html::escape($dbname) ?>">
			<p class="small text-muted">
				<?= I18N::translate('This is case sensitive. If a database with this name does not already exist webtrees will attempt to create one for you. Success will depend on permissions set for your web server, but you will be notified if this fails.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-form-label col-sm-3" for="tblpfx">
			<?= I18N::translate('Table prefix') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" dir="ltr" id="tblpfx" name="tblpfx" pattern="^[^`]*$" type="text" value="<?= Html::escape($tblpfx) ?>">
			<p class="small text-muted">
				<?= I18N::translate('The prefix is optional, but recommended. By giving the table names a unique prefix you can let several different applications share the same database. “wt_” is suggested, but can be anything you want.') ?>
			</p>
		</div>
	</div>

	<hr>

	<button class="btn btn-primary" name="step" type="submit" value="5">
		<?= I18N::translate('next') ?>
	</button>

	<button class="btn btn-link" name="step" type="submit" value="3">
		<?= I18N::translate('previous') ?>
	</button>
</form>
