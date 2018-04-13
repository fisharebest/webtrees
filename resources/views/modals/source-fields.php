<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-group row">
	<label class="col-form-label col-sm-2" for="source-title">
		<?= I18N::translate('Title') ?>
	</label>
	<div class="col-sm-10">
		<input class="form-control" type="text" id="source-title" name="source-title" required>
	</div>
</div>
<div class="form-group row">
	<label class="col-form-label col-sm-2" for="source-abbreviation">
		<?= I18N::translate('Abbreviation') ?>
	</label>
	<div class="col-sm-10">
		<input class="form-control" type="text" id="source-abbreviation" name="source-abbreviation">
	</div>
</div>
<div class="form-group row">
	<label class="col-form-label col-sm-2" for="source-author">
		<?= I18N::translate('Author') ?>
	</label>
	<div class="col-sm-4">
		<input class="form-control" type="text" id="source-author" name="source-author">
	</div>
	<label class="col-form-label col-sm-2" for="source-publication">
		<?= I18N::translate('Publication') ?>
	</label>
	<div class="col-sm-4">
		<input class="form-control" type="text" id="source-publication" name="source-publication">
	</div>
</div>
<div class="form-group row">
	<label class="col-form-label col-sm-2" for="source-repository">
		<?= I18N::translate('Repository') ?>
	</label>
	<div class="col-sm-4">
		<?= FunctionsEdit::formControlRepository($tree, null, ['id' => 'source-repository', 'name' => 'source-repository']) ?>
	</div>
	<label class="col-form-label col-sm-2" for="source-call-number">
		<?= I18N::translate('Call number') ?>
	</label>
	<div class="col-sm-4">
		<input class="form-control" type="text" id="source-call-number" name="source-call-number">
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

<?= view('modals/restriction-fields') ?>
