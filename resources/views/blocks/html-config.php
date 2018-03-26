<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<div class="row form-group">
	<label class="col-sm-3 col-form-label" for="title">
		<?= I18N::translate('Title') ?>
	</label>
	<div class="col-sm-9">
		<input class="form-control" type="text" id="title" name="title" value="<?= e($title) ?>">
	</div>
</div>

<div class="row form-group">
	<label class="col-sm-3 col-form-label" for="template">
		<?= I18N::translate('Templates') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::select([$html => I18N::translate('Custom')] + array_flip($templates), '', ['onchange' => 'this.form.html.value=this.options[this.selectedIndex].value; CKEDITOR.instances.html.setData(document.block.html.value);', 'id' => 'template']) ?>
		<p class="small text-muted">
			<?= I18N::translate('To assist you in getting started with this block, we have created several standard templates. When you select one of these templates, the text area will contain a copy that you can then alter to suit your site’s requirements.') ?>
		</p>
	</div>
</div>

<div class="row form-group">
	<label class="col-sm-3 col-form-label" for="gedcom">
		<?= I18N::translate('Family tree') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::select(['__current__' => I18N::translate('Current'), '__default__' => I18N::translate('Default')] + $all_trees, $gedcom, ['id' => 'gedcom', 'name' => 'gedcom']) ?>
	</div>
</div>

<div class="row form-group">
	<label class="col-sm-3 col-form-label" for="html">
		<?= I18N::translate('Content') ?>
	</label>
	<div class="col-sm-9">
		<p>
			<?= I18N::translate('As well as using the toolbar to apply HTML formatting, you can insert database fields which are updated automatically. These special fields are marked with <b>#</b> characters. For example <b>#totalFamilies#</b> will be replaced with the actual number of families in the database. Advanced users may wish to apply CSS classes to their text, so that the formatting matches the currently selected theme.') ?>
		</p>
		<textarea name="html" id="html" class="html-edit form-control" rows="10"><?= e($html) ?></textarea>
	</div>
</div>

<fieldset class="form-group">
	<div class="row">
		<legend class="form-control-legend col-sm-3">
			<?= I18N::translate('Show the date and time of update') ?>
		</legend>
		<div class="col-sm-9">
			<?= Bootstrap4::radioButtons('show_timestamp', FunctionsEdit::optionsNoYes(), $show_timestamp, true) ?>
		</div>
	</div>
</fieldset>

<fieldset class="form-group">
	<div class="row">
		<legend class="form-control-legend col-sm-3">
			<?= I18N::translate('Show this block for which languages') ?>
		</legend>
		<div class="col-sm-9">
			<?= FunctionsEdit::editLanguageCheckboxes('lang', $languages) ?>
		</div>
	</div>
</fieldset>

<?php if ($enable_ckeditor): ?>
	<?php View::push('javascript') ?>
		<?= view('modules/ckeditor-js') ?>
	<?php View::endpush() ?>
<?php endif ?>
