<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form class="wt-page-options wt-page-options-descendants-chart d-print-none">
	<input type="hidden" name="route" value="descendants">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="xref">
			<?= I18N::translate('Individual') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= FunctionsEdit::formControlIndividual($tree, $individual, ['id' => 'xref', 'name' => 'xref']) ?>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="generations">
			<?= I18N::translate('Generations') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control" id="generations" name="generations" type="number" min="<?= e($minimum_generations) ?>" max="<?= e($maximum_generations) ?>" value="<?= e($generations) ?>" required>
		</div>
	</div>

	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3 wt-page-options-label">
				<?= I18N::translate('Layout') ?>
			</legend>
			<div class="col-sm-9 wt-page-options-value">
				<?= Bootstrap4::radioButtons('chart_style', $chart_styles, $chart_style, true, []) ?>
			</div>
		</div>
	</fieldset>

	<div class="row form-group">
		<div class="col-form-label col-sm-3 wt-page-options-label"></div>
		<div class="col-sm-9 wt-page-options-value">
			<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */
			I18N::translate('view') ?>">
		</div>
	</div>
</form>

<div class="wt-ajax-load wt-page-content wt-chart wt-descendants-chart" data-ajax-url="<?= e(route('descendants-chart', ['xref' => $individual->getXref(), 'ged' => $individual->getTree()->getName(), 'generations' => $generations, 'chart_style' => $chart_style])) ?>"></div>
