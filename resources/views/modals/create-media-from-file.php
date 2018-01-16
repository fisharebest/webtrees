<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\GedcomTag; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="modal" id="modal-create-media-from-file">
	<form action="action.php" method="POST">
		<?= csrf_field() ?>
		<input type="hidden" name="action" value="create-media-object-from-file">
		<input type="hidden" name="ged" id="ged" value="">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title"><?= I18N::translate('Create a media object') ?></h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="<?= I18N::translate('close') ?>">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group row">
						<label class="col-form-label col-sm-2" for="file">
							<?= I18N::translate('Media file') ?>
						</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="file" name="file" value="" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-form-label col-sm-2" for="title">
							<?= I18N::translate('Title') ?>
						</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="title" id="title">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-form-label col-sm-2" for="type">
							<?= I18N::translate('Media type') ?>
						</label>
						<div class="col-sm-10">
							<?= Bootstrap4::select(['' => ''] + GedcomTag::getFileFormTypes(), '') ?>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-form-label col-sm-2" for="note">
							<?= I18N::translate('Note') ?>
						</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="note" name="note"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">
						<?= FontAwesome::decorativeIcon('save') ?>
						<?= I18N::translate('save') ?>
					</button>
					<button type="button" class="btn btn-text" data-dismiss="modal">
						<?= FontAwesome::decorativeIcon('cancel') ?>
						<?= I18N::translate('cancel') ?>
					</button>
				</div>
			</div>
		</div>
	</form>
</div>
