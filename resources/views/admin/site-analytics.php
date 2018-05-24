<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Site; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<form method="post" class="form-horizontal">
	<?= csrf_field() ?>

	<p>
		<?= I18N::translate('If you use one of the following tracking and analytics services, webtrees can add the tracking codes automatically.') ?>
	</p>

	<h2><a href="https://www.bing.com/toolbox/webmaster/">Bing Webmaster Tools</a></h2>

	<!-- BING_WEBMASTER_ID -->
	<div class="row form-group">
		<label for="BING_WEBMASTER_ID" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Site verification code') ?>
			<span class="sr-only">Google Webmaster Tools</span>
		</label>
		<div class="col-sm-9">
			<input
				type="text" class="form-control"
				id="BING_WEBMASTER_ID" name="BING_WEBMASTER_ID" <?= dirname(parse_url(WT_BASE_URL, PHP_URL_PATH)) === '/' ? '' : 'disabled' ?>
				value="<?= e(Site::getPreference('BING_WEBMASTER_ID')) ?>"
				maxlength="255" pattern="[0-9a-zA-Z+=/_:.!-]*"
			>
			<p class="small text-muted">
				<?= /* I18N: Help text for the "Site verification code for Google Webmaster Tools" site configuration setting */ I18N::translate('Site verification codes do not work when webtrees is installed in a subfolder.') ?>
			</p>
		</div>
	</div>

	<h2><a href="https://www.google.com/webmasters/">Google Webmaster Tools</a></h2>

	<!-- GOOGLE_WEBMASTER_ID -->
	<div class="row form-group">
		<label for="GOOGLE_WEBMASTER_ID" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Site verification code') ?>
			<span class="sr-only">Google Webmaster Tools</span>
		</label>
		<div class="col-sm-9">
			<input
				type="text" class="form-control"
				id="GOOGLE_WEBMASTER_ID" name="GOOGLE_WEBMASTER_ID" <?= dirname(parse_url(WT_BASE_URL, PHP_URL_PATH)) === '/' ? '' : 'disabled' ?>
				value="<?= e(Site::getPreference('GOOGLE_WEBMASTER_ID')) ?>"
				maxlength="255" pattern="[0-9a-zA-Z+=/_:.!-]*"
			>
			<p class="small text-muted">
				<?= /* I18N: Help text for the "Site verification code for Google Webmaster Tools" site configuration setting */ I18N::translate('Site verification codes do not work when webtrees is installed in a subfolder.') ?>
			</p>
		</div>
	</div>

	<h2><a href="https://www.google.com/analytics/">Google Analytics</a></h2>

	<!-- GOOGLE_ANALYTICS_ID -->
	<div class="row form-group">
		<label for="GOOGLE_ANALYTICS_ID" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Site identification code') ?>
			<span class="sr-only">Google Analytics</span>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="GOOGLE_ANALYTICS_ID" name="GOOGLE_ANALYTICS_ID" value="<?= e(Site::getPreference('GOOGLE_ANALYTICS_ID')) ?>" placeholder="UA-12345-6" maxlength="255" pattern="UA-[0-9]+-[0-9]+">
			<p class="small text-muted">
				<?= I18N::translate('Tracking and analytics are not added to the control panel.') ?>
			</p>
		</div>
	</div>

	<h2><a href="https://piwik.org/">Piwik</a></h2>

	<!-- PIWIK_SITE_ID -->
	<div class="row form-group">
		<label for="PIWIK_SITE_ID" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Site identification code') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="PIWIK_SITE_ID" name="PIWIK_SITE_ID" value="<?= e(Site::getPreference('PIWIK_SITE_ID')) ?>" maxlength="255" pattern="[0-9]+">
		</div>
	</div>

	<!-- PIWIK_URL -->
	<div class="row form-group">
		<label for="PIWIK_URL" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('URL') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="PIWIK_URL" name="PIWIK_URL" value="<?= e(Site::getPreference('PIWIK_URL')) ?>" placeholder="example.com/piwik" maxlength="255">
			<p class="small text-muted">
				<?= I18N::translate('Tracking and analytics are not added to the control panel.') ?>
			</p>
		</div>
	</div>

	<h2><a href="https://statcounter.com/">StatCounter</a></h2>

	<!-- STATCOUNTER_PROJECT_ID -->
	<div class="row form-group">
		<label for="STATCOUNTER_PROJECT_ID" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Site identification code') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="STATCOUNTER_PROJECT_ID" name="STATCOUNTER_PROJECT_ID" value="<?= e(Site::getPreference('STATCOUNTER_PROJECT_ID')) ?>" maxlength="255" pattern="[0-9]+">
		</div>
	</div>

	<!-- STATCOUNTER_SECURITY_ID -->
	<div class="row form-group">
		<label for="STATCOUNTER_SECURITY_ID" class="col-sm-3 col-form-label">
			<?= /* I18N: A configuration setting */ I18N::translate('Security code') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="STATCOUNTER_SECURITY_ID" name="STATCOUNTER_SECURITY_ID" value="<?= e(Site::getPreference('STATCOUNTER_SECURITY_ID')) ?>" maxlength="255" pattern="[0-9a-zA-Z]+">
			<p class="small text-muted">
				<?= I18N::translate('Tracking and analytics are not added to the control panel.') ?>
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
