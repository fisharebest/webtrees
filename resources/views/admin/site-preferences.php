<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Site; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<form method="post" class="form-horizontal">
	<?= csrf_field() ?>

	<!-- INDEX_DIRECTORY -->
	<div class="row form-group">
		<label for="INDEX_DIRECTORY" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */
			I18N::translate('Data folder') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" dir="ltr" id="INDEX_DIRECTORY" name="INDEX_DIRECTORY" value="<?= e(Site::getPreference('INDEX_DIRECTORY')) ?>" maxlength="255" placeholder="data/" required>
			<p class="small text-muted">
				<?= /* I18N: Help text for the "Data folder" site configuration setting */
				I18N::translate('This folder will be used by webtrees to store media files, GEDCOM files, temporary files, etc. These files may contain private data, and should not be made available over the internet.') ?>
			</p>
			<p class="small text-muted">
				<?= /* I18N: “Apache” is a software program. */
				I18N::translate('To protect this private data, webtrees uses an Apache configuration file (.htaccess) which blocks all access to this folder. If your web-server does not support .htaccess files, and you cannot restrict access to this folder, then you can select another folder, away from your web documents.') ?>
			</p>
			<p class="small text-muted">
				<?= I18N::translate('If you select a different folder, you must also move all files (except config.ini.php, index.php, and .htaccess) from the existing folder to the new folder.') ?>
			</p>
			<p class="small text-muted">
				<?= I18N::translate('The folder can be specified in full (e.g. /home/user_name/webtrees_data/) or relative to the installation folder (e.g. ../../webtrees_data/).') ?>
			</p>
		</div>
	</div>

	<!-- MEMORY_LIMIT -->
	<div class="row form-group">
		<label for="MEMORY_LIMIT" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */
			I18N::translate('Memory limit') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="MEMORY_LIMIT" name="MEMORY_LIMIT" value="<?= e(Site::getPreference('MEMORY_LIMIT')) ?>" pattern="[0-9]+[KMG]" placeholder="<?= get_cfg_var('memory_limit') ?>" maxlength="255">
			<p class="small text-muted">
				<?= /* I18N: %s is an amount of memory, such as 32MB */
				I18N::translate('By default, your server allows scripts to use %s of memory.', get_cfg_var('memory_limit')) ?>
				<?= I18N::translate('You can request a higher or lower limit, although the server may ignore this request.') ?>
				<?= I18N::translate('Leave this blank to use the default value.') ?>
			</p>
		</div>
	</div>

	<!-- MAX_EXECUTION_TIME -->
	<div class="row form-group">
		<label for="MAX_EXECUTION_TIME" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */
			I18N::translate('PHP time limit') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="MAX_EXECUTION_TIME" name="MAX_EXECUTION_TIME" value="<?= e(Site::getPreference('MAX_EXECUTION_TIME')) ?>" pattern="[0-9]*" placeholder="<?= get_cfg_var('max_execution_time') ?>" maxlength="255">
			<p class="small text-muted">
				<?= I18N::plural(
					'By default, your server allows scripts to run for %s second.',
					'By default, your server allows scripts to run for %s seconds.',
					get_cfg_var('max_execution_time'), I18N::number((float) get_cfg_var('max_execution_time')));
				?>
				<?= I18N::translate('You can request a higher or lower limit, although the server may ignore this request.') ?>
				<?= I18N::translate('Leave this blank to use the default value.') ?>
			</p>
		</div>
	</div>

	<!-- TIMEZONE -->
	<div class="row form-group">
		<label for="TIMEZONE" class="col-sm-3 col-form-label">
			<?= I18N::translate('Time zone') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select(array_combine(\DateTimeZone::listIdentifiers(), \DateTimeZone::listIdentifiers()), Site::getPreference('TIMEZONE') ?: 'UTC', ['id' => 'TIMEZONE', 'name' => 'TIMEZONE']) ?>
			<p class="small text-muted">
				<?= I18N::translate('The time zone is required for date calculations, such as knowing today’s date.') ?>
			</p>
		</div>
	</div>

	<!-- THEME_DIR -->
	<div class="row form-group">
		<label for="THEME_DIR" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */
			I18N::translate('Default theme') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select($all_themes, Site::getPreference('THEME_DIR'), ['id' => 'THEME_DIR', 'name' => 'THEME_DIR']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the "Default theme" site configuration setting */
				I18N::translate('You can change the appearance of webtrees using “themes”. Each theme has a different style, layout, color scheme, etc.') ?>
			</p>
			<p class="small text-muted">
				<?= I18N::translate('Themes can be selected at three levels: user, family tree, and website. User preferences take priority over family tree preferences, which in turn take priority over the website preferences. Selecting “default theme” at one level will use the theme at the next level.') ?>
			</p>
		</div>
	</div>

	<!-- ALLOW_USER_THEMES -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */
				I18N::translate('Allow users to select their own theme') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('ALLOW_USER_THEMES', FunctionsEdit::optionsNoYes(), (string) (int) Site::getPreference('ALLOW_USER_THEMES'), true) ?>
			</div>
		</div>
	</fieldset>

	<!-- ALLOW_CHANGE_GEDCOM -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */
				I18N::translate('Show list of family trees') ?>
			</legend>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('ALLOW_CHANGE_GEDCOM', FunctionsEdit::optionsNoYes(), (string) (int) Site::getPreference('ALLOW_CHANGE_GEDCOM'), true) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Show list of family trees” site configuration setting */
					I18N::translate('For websites with more than one family tree, this option will show the list of family trees in the main menu, the search pages, etc.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- SESSION_TIME -->
	<div class="row form-group">
		<label for="SESSION_TIME" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */
			I18N::translate('Session timeout') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="SESSION_TIME" name="SESSION_TIME" value="<?= e(Site::getPreference('SESSION_TIME')) ?>" pattern="[0-9]*" placeholder="7200" maxlength="255">
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Session timeout” site configuration setting */
				I18N::translate('The time in seconds that a webtrees session remains active before requiring a new sign-in. The default is 7200, which is 2 hours.') ?>
				<?= I18N::translate('Leave this blank to use the default value.') ?>
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
