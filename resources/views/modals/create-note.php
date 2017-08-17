<?php use Fisharebest\Webtrees\Filter; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\GedcomTag; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<div class="modal wt-modal-create-record" id="modal-create-note-object">
	<form id="form-create-note-object"><!-- This form is posted using jQuery -->
		<?= Filter::getCsrf() ?>
		<input type="hidden" name="action" value="create-note-object">
		<input type="hidden" name="ged" value="<?= $tree->getNameHtml() ?>">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title"><?= I18N::translate('Create a shared note') ?></h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="<?= I18N::translate('close') ?>">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label class="col-form-label" for="note">
							<?= GedcomTag::getLabel('NOTE') ?>
						</label>
						<textarea class="form-control" id="note" name="note"></textarea>
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
