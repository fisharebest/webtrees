<?php use Fisharebest\Webtrees\Filter; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<div class="modal wt-modal-create-record" id="modal-create-source">
	<form id="form-create-source"><!-- This form is posted using jQuery -->
		<?= Filter::getCsrf() ?>
		<input type="hidden" name="action" value="create-source">
		<input type="hidden" name="ged" value="<?= $tree->getNameHtml() ?>">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title"><?= I18N::translate('Create a source') ?></h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="<?= I18N::translate('close') ?>">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group row">
						<label class="col-form-label col-sm-2" for="source-title">
							<?= I18N::translate('Title') ?>
						</label>
						<div class="col-sm-10">
							<input class="form-control" type="text" id="source-title" name="TITL" required>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-form-label col-sm-2" for="source-abbreviation">
							<?= I18N::translate('Abbreviation') ?>
						</label>
						<div class="col-sm-10">
							<input class="form-control" type="text" id="source-abbreviation" name="ABBR">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-form-label col-sm-2" for="source-author">
							<?= I18N::translate('Author') ?>
						</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" id="source-author" name="AUTH">
						</div>
						<label class="col-form-label col-sm-2" for="source-publication">
							<?= I18N::translate('Publication') ?>
						</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" id="source-publication" name="PUBL">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-form-label col-sm-2" for="source-repository">
							<?= I18N::translate('Repository') ?>
						</label>
						<div class="col-sm-4">
							<?= FunctionsEdit::formControlRepository(null, ['id' => 'source-repository', 'name' => 'REPO']) ?>
						</div>
						<label class="col-form-label col-sm-2" for="source-call-number">
							<?= I18N::translate('Call number') ?>
						</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" id="source-call-number" name="CALN">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-form-label col-sm-2" for="source-text">
							<?= I18N::translate('Text') ?>
						</label>
						<div class="col-sm-10">
							<textarea class="form-control" rows="2" id="source-text" name="TEXT"></textarea>
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
