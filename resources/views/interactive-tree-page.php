<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form class="wt-page-options wt-page-options-compact-chart d-print-none">
	<input type="hidden" name="route" value="module">
	<input type="hidden" name="module" value="tree">
	<input type="hidden" name="action" value="Treeview">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="xref">
			<?= I18N::translate('Individual') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= FunctionsEdit::formControlIndividual($individual->getTree(), $individual, ['id' => 'xref', 'name' => 'xref']) ?>
		</div>
	</div>

	<div class="row form-group">
		<div class="col-sm-3 wt-page-options-label"></div>
		<div class="col-sm-9 wt-page-options-value">
			<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */
			I18N::translate('view') ?>">
		</div>
	</div>
</form>

<div class="wt-page-content wt-chart wt-interactive-tree">
	<?= $html ?>
</div>

<?php View::push('javascript') ?>
<script src="<?= e(WT_MODULES_DIR) ?>tree/js/treeview.js"></script>
<script>
	<?= $js ?>
</script>
<?php View::endpush() ?>

<?php View::push('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= e(WT_MODULES_DIR) ?>tree/css/treeview.css">
<?php View::endpush() ?>
