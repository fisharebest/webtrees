<?php use Fisharebest\Webtrees\Filter; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<div class="modal wt-modal-create-record" id="modal-create-media-object">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<form id="form-create-media-object"><!-- This form is posted using jQuery -->
					<?= Filter::getCsrf() ?>
					<input type="hidden" name="action" value="create-media-object">
					<input type="hidden" name="ged" value="<?= $tree->getNameHtml() ?>">

					<?= View::make('modals/header', ['title' => I18N::translate('Create a media object')]) ?>

				<div class="modal-body">
					<?= View::make('modals/media-file-fields', ['max_upload_size' => $max_upload_size]) ?>

					<div class="form-group row">
						<label class="col-form-label col-sm-2" for="note">
							<?= I18N::translate('Note') ?>
						</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="note" name="note"></textarea>
						</div>
					</div>
				</div>

				<?= View::make('modals/footer-save-cancel') ?>
			</form>
		</div>
	</div>
</div>
