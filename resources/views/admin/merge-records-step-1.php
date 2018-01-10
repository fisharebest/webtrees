<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), 'admin_trees_manage.php' => I18N::translate('Manage family trees'), $title]]) ?>

<h1><?= $title ?></h1>

<form>
	<input type="hidden" name="route" value="merge-records">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="record-type">
			<?= I18N::translate('Select two records to merge.') ?>
		</label>
		<div class="col-sm-9">
			<select class="form-control" id="record-type">
				<option value="individual"><?= I18N::translate('Individuals') ?></option>
				<option value="family"><?= I18N::translate('Families') ?></option>
				<option value="source"><?= I18N::translate('Sources') ?></option>
				<option value="repository"><?= I18N::translate('Repositories') ?></option>
				<option value="note"><?= I18N::translate('Notes') ?></option>
				<option value="media"><?= I18N::translate('Media objects') ?></option>
			</select>
		</div>
	</div>

	<label class="row form-group">
		<span class="col-sm-3 col-form-label">
			<?= I18N::translate('First record') ?>
		</span>
		<span class="col-sm-9 select-record select-individual">
			<?= FunctionsEdit::formControlIndividual($individual1, ['name' => 'xref1', 'class' => 'form-control', 'style' => 'width:100%;']) ?>
		</span>
		<span class="col-sm-9 select-record select-family d-none">
			<?= FunctionsEdit::formControlFamily($family1, ['name' => 'xref1', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
		</span>
		<span class="col-sm-9 select-record select-source d-none">
			<?= FunctionsEdit::formControlSource($source1, ['name' => 'xref1', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
		</span>
		<span class="col-sm-9 select-record select-repository d-none">
			<?= FunctionsEdit::formControlRepository($repository1, ['name' => 'xref1', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
		</span>
		<span class="col-sm-9 select-record select-note d-none">
			<?= FunctionsEdit::formControlNote($note1, ['name' => 'xref1', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
		</span>
		<span class="col-sm-9 select-record select-media d-none">
			<?= FunctionsEdit::formControlMediaObject($media2, ['name' => 'xref1', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
		</span>
	</label>

	<label class="row form-group">
		<span class="col-sm-3 col-form-label">
			<?= I18N::translate('Second record') ?>
		</span>
		<span class="col-sm-9 select-record select-individual">
			<?= FunctionsEdit::formControlIndividual($individual2, ['name' => 'xref2', 'class' => 'form-control', 'style' => 'width:100%;']) ?>
		</span>
		<span class="col-sm-9 select-record select-family d-none">
			<?= FunctionsEdit::formControlFamily($family2, ['name' => 'xref2', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
		</span>
		<span class="col-sm-9 select-record select-source d-none">
			<?= FunctionsEdit::formControlSource($source2, ['name' => 'xref2', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
		</span>
		<span class="col-sm-9 select-record select-repository d-none">
			<?= FunctionsEdit::formControlRepository($repository2, ['name' => 'xref2', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
		</span>
		<span class="col-sm-9 select-record select-note d-none">
			<?= FunctionsEdit::formControlNote($note2, ['name' => 'xref2', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
		</span>
		<span class="col-sm-9 select-record select-media d-none">
			<?= FunctionsEdit::formControlMediaObject($media2, ['name' => 'xref2', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
		</span>
	</label>

	<div class="row form-group">
		<div class="col-sm-3">
		</div>
		<div class="col-sm-9">
			<button class="btn btn-primary" type="submit">
				<?= FontAwesome::decorativeIcon('save') ?>
				<?= I18N::translate('continue') ?>
			</button>
		</div>
	</div>
</form>

<?php View::push('javascript') ?>
<script>
  'use strict';

  // Disabled elements do not get submitted with the form.
  $('#record-type').change(function() {
    $('.select-record').addClass('d-none').attr("disabled", true);
    $('.select-' + $(this).val()).removeClass('d-none').attr("disabled", false);
    // Recalculate width of previously hidden elements
  });
</script>
<?php View::endpush() ?>
