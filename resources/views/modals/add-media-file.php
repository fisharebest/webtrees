<?php use Fisharebest\Webtrees\I18N; ?>

<form action="<?= e(Route('add-media-file', ['xref' => $media->getXref(), 'ged' => $media->getTree()->getName()])) ?>" enctype="multipart/form-data" method="POST">
	<?= csrf_field() ?>

	<?= view('modals/header', ['title' => I18N::translate('Add a media file')]) ?>

	<div class="modal-body">
		<?= view('modals/media-file-fields', ['max_upload_size' => $max_upload_size, 'unused_files' => $unused_files, 'media_file' => null]) ?>
	</div>

	<?= view('modals/footer-save-cancel') ?>
</form>
