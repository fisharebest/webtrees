<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Filter; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\GedcomTag; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<div class="modal wt-modal-create-record" id="modal-create-media-object">
	<form id="form-create-media-object"><!-- This form is posted using jQuery -->
		<?= Filter::getCsrf() ?>
		<input type="hidden" name="action" value="create-media-object">
		<input type="hidden" name="ged" value="<?= $tree->getNameHtml() ?>">
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
							<?= I18N::translate('Media file to upload') ?>
						</label>
						<div class="col-sm-10">
							<input type="file" class="form-control" id="file" name="file">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-form-label col-sm-2" for="type">
							<?= I18N::translate('Filename on server') ?>
						</label>
						<div class="col-sm-10">
							<div class="form-check">
								<label class="form-check-label">
									<input class="form-check-input" type="radio" name="auto" value="0" checked>
									<span class="input-group">
												<input class="form-control" type="text" placeholder="<?= I18N::translate('Folder') ?>" data-autocomplete-type="folder">
												<span class="input-group-addon">/</span>
												<input class="form-control" type="text" placeholder="<?= I18N::translate('Same as uploaded file') ?>">
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
