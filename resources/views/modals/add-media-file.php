<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<form action="<?= e(Route('add-media-file', ['xref' => $media->getXref(), 'ged' => $media->getTree()->getName()])) ?>" enctype="multipart/form-data" method="POST">
	<?= csrf_field() ?>

	<?= View::make('modals/header', ['title' => I18N::translate('Add a media file')]) ?>

	<div class="modal-body">
		<?= View::make('modals/media-file-fields', ['max_upload_size' => $max_upload_size, 'unused_files' => $unused_files]) ?>
	</div>

	<?= View::make('modals/footer-save-cancel') ?>
</form>
