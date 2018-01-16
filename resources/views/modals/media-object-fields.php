<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('modals/media-file-fields', ['max_upload_size' => $max_upload_size, 'unused_files' => $unused_files]) ?>

<div class="form-group row">
	<label class="col-form-label col-sm-2" for="media-note">
		<?= I18N::translate('Note') ?>
	</label>
	<div class="col-sm-10">
		<textarea class="form-control" id="media-note" name="media-note"></textarea>
	</div>
</div>

<?= view('modals/restriction-fields') ?>
