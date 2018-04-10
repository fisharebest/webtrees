<?php use Fisharebest\Webtrees\I18N; ?>

<form action="<?= e(route('edit-media-file', ['xref' => $media->getXref(), 'ged' => $media->getTree()->getName(), 'fact_id' => $media_file->factId()])) ?>" enctype="multipart/form-data" method="POST">
	<?= csrf_field() ?>

	<?= view('modals/header', ['title' => I18N::translate('Edit a media file')]) ?>

	<div class="modal-body">
		<?= view('modals/media-file-fields', ['max_upload_size' => $max_upload_size, 'unused_files' => [], 'media_file' => $media_file]) ?>
	</div>

	<?= view('modals/footer-save-cancel') ?>
</form>
