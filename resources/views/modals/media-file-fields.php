<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\GedcomTag; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-group row <?= $media_file ? 'd-none' : '' ?>">
	<label class="col-form-label col-sm-2" for="file-location">
		<?= I18N::translate('Media file') ?>
	</label>
	<div class="col-sm-10">
		<select class="form-control" id="file-location" name="file_location">
			<option value="upload">
				<?= I18N::translate('A file on your computer') ?>
			</option>
			<?php if (!empty($unused_files)): ?>
			<option value="unused">
				<?= I18N::translate('A file on the server') ?>
			</option>
			<?php endif ?>
			<option value="url">
				<?= I18N::translate('A URL') ?>
			</option>
		</select>
	</div>
</div>

<div class="form-group row file-location file-location-upload <?= $media_file ? 'd-none' : '' ?>">
	<label class="col-form-label col-sm-2" for="file">
		<?= I18N::translate('A file on your computer') ?>
	</label>
	<div class="col-sm-10">
		<input class="form-control" id="file" name="file" type="file">
		<small class="text-muted">
			<?= I18N::translate('Maximum upload size: ') ?>
			<?= $max_upload_size ?>
		</small>
	</div>
</div>

<div class="form-group row file-location file-location-upload <?= $media_file && $media_file->isExternal() ? 'd-none' : '' ?>">
	<label class="col-form-label col-sm-2" for="type">
		<?= I18N::translate('Filename on server') ?>
	</label>
	<div class="col-sm-10">
		<div class="form-check">
			<label class="form-check-label">
				<input class="form-check-input" type="radio" name="auto" value="0" checked>
				<span class="input-group">
					<input class="form-control" name="folder" placeholder="<?= I18N::translate('Folder') ?>" data-autocomplete-type="folder" type="text" value="<?= e($media_file ? $media_file->dirname() : '') ?>">
					<span class="input-group-addon">/</span>
					<input class="form-control" name="new_file" type="text" placeholder="<?= I18N::translate('Same as uploaded file') ?>" value="<?= e($media_file ? $media_file->basename() : '') ?>">
				</span>
			</label>
		</div>
		<p class="small text-muted">
			<?= I18N::translate('If you have a large number of media files, you can organize them into folders and subfolders.') ?>
		</p>
		<div class="form-check">
			<label class="form-check-label">
				<input class="form-check-input" type="radio" name="auto" value="1">
				<?= I18N::translate('Create a unique filename') ?>
			</label>
		</div>
	</div>
</div>

<div class="form-group row file-location file-location-unused d-none">
	<label class="col-form-label col-sm-2" for="unused">
		<?= I18N::translate('A file on the server') ?>
	</label>
	<div class="col-sm-10">
		<?= Bootstrap4::select($unused_files, '', ['id' => 'unused', 'name' => 'unused']) ?>
		<small class="text-muted">
		</small>
	</div>
</div>

<div class="form-group row file-location file-location-url <?= $media_file && $media_file->isExternal() ? '' : 'd-none' ?>">
	<label class="col-form-label col-sm-2" for="remote">
		<?= I18N::translate('URL') ?>
	</label>
	<div class="col-sm-10">
		<input class="form-control" type="url" id="remote" name="remote" placeholder="https://www.example.com/photo.jpeg" value="<?= e($media_file ? $media_file->filename() : '') ?>">
		<small class="text-muted">
			<?= \Fisharebest\Webtrees\FontAwesome::semanticIcon('warning', I18N::translate('Caution!')) ?>
			<?= I18N::translate('The GEDCOM standard does not allow URLs in media objects.') ?>
			<?= I18N::translate('Other genealogy applications might not recognize this data.') ?>
		</small>
	</div>
</div>

<div class="form-group row">
	<label class="col-form-label col-sm-2" for="title">
		<?= I18N::translate('Title') ?>
	</label>
	<div class="col-sm-10">
		<input class="form-control" id="title" name="title" type="text" value="<?= e($media_file ? $media_file->title() : '') ?>">
	</div>
</div>

<div class="form-group row">
	<label class="col-form-label col-sm-2" for="type">
		<?= I18N::translate('Media type') ?>
	</label>
	<div class="col-sm-10">
		<?= Bootstrap4::select(['' => ''] + GedcomTag::getFileFormTypes(), $media_file ? $media_file->type() : '', ['id'   => 'type', 'name' => 'type']) ?>
	</div>
</div>

<script>
	document.getElementById('file-location').addEventListener('change', function () {
    $('.file-location').addClass('d-none');
    $('.file-location-' + $(this).val()).removeClass('d-none');
  });
</script>
