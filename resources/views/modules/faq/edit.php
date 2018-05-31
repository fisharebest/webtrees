<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-modules') => I18N::translate('Modules'), route('module', ['module' => 'faq', 'action' => 'Admin', 'ged' => $tree->getName()]) => I18N::translate('Frequently asked questions'), $title]]) ?>

<h1><?= $title ?></h1>

<form name="faq" class="form-horizontal" method="post">
	<?= csrf_field() ?>
	<input type="hidden" name="block_id" value="<?= $block_id ?>">

	<div class="row form-group">
		<label for="header" class="col-sm-3 col-form-label">
			<?= I18N::translate('Question') ?>
		</label>

		<div class="col-sm-9">
			<input type="text" class="form-control" name="header" id="header"
				value="<?= e($header) ?>">
		</div>
	</div>

	<div class="row form-group">
		<label for="faqbody" class="col-sm-3 col-form-label">
			<?= I18N::translate('Answer') ?>
		</label>

		<div class="col-sm-9">
			<textarea name="faqbody" id="faqbody" class="form-control html-edit" rows="10"><?= e($faqbody) ?></textarea>
		</div>
	</div>

	<div class="row form-group">
		<label for="xref" class="col-sm-3 col-form-label">
			<?= /* I18N: Label for a configuration option */
			I18N::translate('Show this block for which languages') ?>
		</label>

		<div class="col-sm-9">
			<?= FunctionsEdit::editLanguageCheckboxes('languages', $languages) ?>
		</div>
	</div>

	<div class="row form-group">
		<label for="block_order" class="col-sm-3 col-form-label">
			<?= I18N::translate('Sort order') ?>
		</label>

		<div class="col-sm-9">
			<input type="text" name="block_order" id="block_order" class="form-control" value="<?= $block_order ?>">
		</div>
	</div>

	<div class="row form-group">
		<label for="gedcom_id" class="col-sm-3 col-form-label">
			<?= I18N::translate('Family tree') ?>
		</label>

		<div class="col-sm-9">
			<?= Bootstrap4::select(['' => I18N::translate('All')] + $tree_names, $tree->getName(), ['id' => 'gedcom_id', 'name' => 'gedcom_id']) ?>
			<p class="small text-muted">
				<?= /* I18N: FAQ = “Frequently Asked Question” */
				I18N::translate('An FAQ can be displayed on just one of the family trees, or on all the family trees.') ?>
			</p>
		</div>
	</div>

	<div class="row form-group">
		<div class="offset-sm-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<i class="fas fa-check"></i>
				<?= I18N::translate('save') ?>
			</button>
		</div>
	</div>

</form>
