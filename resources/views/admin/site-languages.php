<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Site; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<form method="post" class="form-horizontal">
	<?= csrf_field() ?>

	<p>
		<?= I18N::translate('Select the languages that will be shown in menus.') ?>
	</p>

	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= /* I18N: A configuration setting */ I18N::translate('Language') ?>
			</legend>
			<div class="col-sm-9" style="columns: 4 150px;">
				<?php foreach (I18N::installedLocales() as $installed_locale): ?>
					<div class="form-check">
						<label title="<?= $installed_locale->languageTag() ?>">
							<input type="checkbox" name="LANGUAGES[]" value="<?= $installed_locale->languageTag() ?>"<?= in_array($installed_locale->languageTag(), $language_tags) ? ' checked' : '' ?>>
							<?= $installed_locale->endonym() ?>
						</label>
					</div>
				<?php endforeach ?>
			</div>
		</div>
	</fieldset>

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
