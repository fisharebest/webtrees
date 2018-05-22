<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-trees') => I18N::translate('Manage family trees'), $title]]) ?>

<h1><?= $title ?></h1>

<form class="form form-horizontal" method="post">
	<?= csrf_field() ?>

	<div class="row form-group">
		<div for="submit-export" class="col-sm-3 col-form-label">
			<?= I18N::translate('A file on the server') ?>
		</div>
		<div class="col-sm-9">
			<button type="submit" class="btn btn-primary">
				<?= /* I18N: A button label. */ I18N::translate('continue') ?>
			</button>
		</div>
	</div>
</form>

<hr>

<form class="form form-horizontal">
	<input type="hidden" name="route" value="admin-trees-download">

	<!-- DOWNLOAD OPTIONS -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= I18N::translate('Export preferences') ?>
			</legend>
			<div class="col-sm-9">
				<div class="form-check">
					<label class="form-check-label">
						<input class="form-check-input" type="checkbox" name="zip">
						<?= I18N::translate('Compress the GEDCOM file') ?>
					</label>
				</div>
				<p class="small muted">
					<?= I18N::translate('To reduce the size of the download, you can compress the data into a .ZIP file. You will need to uncompress the .ZIP file before you can use it.') ?>
				</p>

				<div class="form-check">
					<label class="form-check-label">
						<input class="form-check-input" type="checkbox" name="media">
						<?= I18N::translate('Include media (automatically zips files)') ?>
					</label>
				</div>

				<?php if ($tree->getPreference('GEDCOM_MEDIA_PATH')): ?>
					<label>
						<input type="checkbox" name="media-path" value="<?= e($tree->getPreference('GEDCOM_MEDIA_PATH')) ?>">
						<?= /* I18N: A media path (e.g. C:\aaa\bbb\ccc\) in a GEDCOM file */ I18N::translate('Add the GEDCOM media path to filenames') ?>
					</label>
					<p>
						<?= /* I18N: %s is the name of a folder. */ I18N::translate('Media filenames will be prefixed by %s.', '<code dir="ltr">' . e($tree->getPreference('GEDCOM_MEDIA_PATH')) . '</code>') ?>
					</p>
				<?php endif ?>

				<div class="form-check">
					<label class="form-check-label">
						<input class="form-check-input" type="checkbox" name="convert">
						<?= I18N::translate('Convert from UTF-8 to ISO-8859-1') ?>
					</label>
				</div>
				<p class="small muted">
					<?= I18N::translate('webtrees uses UTF-8 encoding for accented letters, special characters and non-Latin scripts. If you want to use this GEDCOM file with genealogy software that does not support UTF-8, then you can create it using ISO-8859-1 encoding.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- PRIVACY OPTIONS -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= I18N::translate('Apply privacy settings') ?>
			</legend>
			<div class="col-sm-9">
				<div class="form-check form-check-inline">
					<label>
						<input type="radio" name="privatize_export" value="none" checked>
						<?= I18N::translate('None') ?>
					</label>
				</div>
				<div class="form-check form-check-inline">
					<label>
						<input type="radio" name="privatize_export" value="gedadmin">
						<?= I18N::translate('Manager') ?>
					</label>
				</div>
				<div class="form-check form-check-inline">
					<label>
						<input type="radio" name="privatize_export" value="user">
						<?= I18N::translate('Member') ?>
					</label>
				</div>
				<div class="form-check form-check-inline">
					<label>
						<input type="radio" name="privatize_export" value="visitor">
						<?= I18N::translate('Visitor') ?>
					</label>
				</div>
			</div>
		</div>
	</fieldset>

	<div class="row form-group">
		<div class="col-sm-3 col-form-label">
			<?= I18N::translate('A file on your computer') ?>
		</div>
		<div class="col-sm-9">
			<button type="submit" class="btn btn-primary">
				<?= /* I18N: A button label. */ I18N::translate('continue') ?>
			</button>
		</div>
	</div>
</form>
