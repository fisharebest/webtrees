<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>

<h2 class="wt-page-title"><?= $title ?></h2>

<form class="wt-page-options wt-page-options-pedigreemap-chart d-print-none">
	<input type="hidden" name="route" value="module">
	<input type="hidden" name="module" value="<?= $module ?>">
	<input type="hidden" name="action" value="Pedigreemap">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">

	<div class="form-group row">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="xref">
			<?= I18N::translate('Individual') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= FunctionsEdit::formControlIndividual($tree, $individual, ['id' => 'xref', 'name' => 'xref']) ?>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="generations">
			<?= I18N::translate('Generations') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?=
			Bootstrap4::select(
				FunctionsEdit::numericOptions(range(2, $maxgenerations)),
				$generations,
				['id' => 'generations', 'name' => 'generations']
			)
			?>
		</div>
	</div>

	<div class="form-group row">
		<div class="col-sm-3 wt-page-options-label"></div>
		<div class="col-sm-9 wt-page-options-value">
			<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */
			I18N::translate('view')
			?>">
		</div>
	</div>
</form>


<div class="wt-ajax-load wt-page-content">
	<?= $map ?>
</div>

