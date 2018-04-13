<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>

<div class="modal wt-modal-create-record" id="modal-create-family">
	<form action="<?= e(route('create-family')) ?>" id="form-create-family">
		<?= csrf_field() ?>
		<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title"><?= I18N::translate('Create a family from existing individuals') ?></h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="<?= I18N::translate('close') ?>">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label class="col-form-label" for="husband">
							<?= I18N::translate('Husband') ?>
						</label>
						<?= FunctionsEdit::formControlIndividual($tree, null, ['id' => 'husband', 'name' => 'husband']) ?>
					</div>
					<div class="form-group">
						<label class="col-form-label" for="wife">
							<?= I18N::translate('Wife') ?>
						</label>
						<?= FunctionsEdit::formControlIndividual($tree, null, ['id' => 'wife', 'name' => 'wife']) ?>
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
