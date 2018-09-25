<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('components/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-trees') => I18N::translate('Manage family trees'), $title]]) ?>

<h1><?= $title ?></h1>

<div class="alert alert-warning">
	<?= /* I18N: %s is the name of a family tree */ I18N::translate('This will delete all the genealogy data from “%s” and replace it with data from a GEDCOM file.', e($tree->getTitle())) ?>
</div>

<form class="form form-horizontal" method="post" enctype="multipart/form-data" onsubmit="return checkGedcomImportForm('<?= e(I18N::translate('You have selected a GEDCOM file with a different name. Is this correct?')) ?>');">
	<input type="hidden" name="ged" value="<?= $tree->getName() ?>">
	<input type="hidden" id="gedcom_filename" value="<?= e($default_gedcom_file) ?>">
	<?= csrf_field() ?>

	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= I18N::translate('Select a GEDCOM file to import') ?>
			</legend>
			<div class="col-sm-9">
				<div class="row form-group">
					<label class="col-sm-3">
						<input type="radio" name="source" id="import-computer" value="client" checked>
						<?= I18N::translate('A file on your computer') ?>
					</label>
					<div class="col-sm-9">
						<div class="btn btn-default">
							<input type="file" class="form-control-file" name="tree_name" id="import-computer-file">
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-3">
						<input type="radio" name="source" id="import-server" value="server">
						<?= I18N::translate('A file on the server') ?>
					</label>
					<div class="col-sm-9">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">
								<?= WT_DATA_DIR ?>
								</span>
							</div>
							<select name="tree_name" class="form-control" id="import-server-file">
								<option value=""></option>
								<?php foreach ($gedcom_files as $gedcom_file): ?>
									<option value="<?= e($gedcom_file) ?>" <?= $gedcom_file === $default_gedcom_file ? 'selected' : '' ?>>
										<?= e($gedcom_file) ?>
									</option>
								<?php endforeach ?>
								<?php if (empty($gedcom_files)): ?>
									<option disabled selected>
										<?= I18N::translate('No GEDCOM files found.') ?>
									</option>
								<?php endif ?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</fieldset>

	<hr>

	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3">
				<?= I18N::translate('Import preferences') ?>
			</legend>
			<div class="col-sm-9">
				<label>
					<input type="checkbox" name="keep_media" value="1" <?= $tree->getPreference('keep_media') ? 'checked' : '' ?>>
					<?= /* I18N: A configuration setting */ I18N::translate('Keep media objects') ?>
				</label>
				<p class="small text-muted">
					<?= I18N::translate('If you have created media objects in webtrees, and have subsequently edited this GEDCOM file using genealogy software that deletes media objects, then select this option to merge the current media objects with the new GEDCOM file.') ?>
				</p>
				<label>
					<input type="checkbox" name="WORD_WRAPPED_NOTES" value="1" <?= $tree->getPreference('WORD_WRAPPED_NOTES') ? 'checked' : '' ?>>
					<?= I18N::translate('Add spaces where long lines were wrapped') ?>
				</label>
				<p class="small text-muted">
					<?= I18N::translate('If you created this GEDCOM file using genealogy software that omits spaces when splitting long lines, then select this option to reinsert the missing spaces.') ?>
				</p>
				<label for="GEDCOM_MEDIA_PATH">
					<?= /* I18N: A media path (e.g. c:\aaa\bbb\ccc\ddd.jpeg) in a GEDCOM file */ I18N::translate('Remove the GEDCOM media path from filenames') ?>
				</label>
				<input
					class="form-control"
					dir="ltr"
					id="GEDCOM_MEDIA_PATH"
					maxlength="255"
					name="GEDCOM_MEDIA_PATH"
					type="text"
					value="<?= e($gedcom_media_path) ?>"
				>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “GEDCOM media path” configuration setting. A “path” is something like “C:\Documents\Genealogy\Photos\John_Smith.jpeg” */ I18N::translate('Some genealogy software creates GEDCOM files that contain media filenames with full paths. These paths will not exist on the web-server. To allow webtrees to find the file, the first part of the path must be removed.') ?>
					<?= /* I18N: Help text for the “GEDCOM media path” configuration setting. %s are all folder names */ I18N::translate('For example, if the GEDCOM file contains %1$s and webtrees expects to find %2$s in the media folder, then you would need to remove %3$s.', '<code>C:\\Documents\\family\\photo.jpeg</code>', '<code>family\\photo.jpeg</code>', '<code>C:\\Documents\\</code>') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<div class="row form-group">
		<div class="offset-sm-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<?= /* I18N: A button label. */ I18N::translate('continue') ?>
			</button>
		</div>
	</div>
</form>

<?php View::push('javascript') ?>
<script>
  function checkGedcomImportForm (message) {
    var oldFile = $('#gedcom_filename').val();
    var method = $('input[name=action]:checked').val();
    var newFile = method === 'replace_import' ? $('#import-server-file').val() : $('#import-computer-file').val();

    // Some browsers include c:\fakepath\ in the filename.
    newFile = newFile.replace(/.*[/\\]/, '');
    if (newFile !== oldFile && oldFile !== '') {
      return window.confirm(message);
    } else {
      return true;
    }
  }

  document.getElementById("import-computer-file").addEventListener("click", function () {
    document.getElementById("import-computer").checked = true;
  });

  document.getElementById("import-server-file").addEventListener("focus", function () {
    document.getElementById("import-server").checked = true;
  });
</script>
<?php View::endpush() ?>
