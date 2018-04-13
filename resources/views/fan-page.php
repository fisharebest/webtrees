<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form class="wt-page-options wt-page-options-fan-chart d-print-none">
	<input type="hidden" name="route" value="fan">
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
		<label class="col-sm-3 col-form-label wt-page-options-label">
			<?= I18N::translate('Layout') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= Bootstrap4::select($chart_styles, $chart_style, ['id' => 'chart_style', 'name' => 'chart_style']) ?>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="generations">
			<?= I18N::translate('Generations') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= Bootstrap4::select(FunctionsEdit::numericOptions(range($minimum_generations, $maximum_generations)), $generations, ['id' => 'generations', 'name' => 'generations']) ?>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="fan_width">
			<?= I18N::translate('Zoom') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<div class="input-group">
				<input class="form-control" id="fan_width" max="<?= $maximum_width ?>" min="<?= $minimum_width ?>" name="fan_width" required type="number" value="<?= $fan_width ?>">
				<div class="input-group-append">
					<span class="input-group-text">
						%
					</span>
				</div>
			</div>
		</div>
	</div>

	<div class="row form-group">
		<div class="col-form-label col-sm-3 wt-page-options-label"></div>
		<div class="col-sm-9 wt-page-options-value">
			<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */ I18N::translate('view') ?>">
		</div>
	</div>
</form>

<div class="wt-ajax-load wt-page-content wt-chart wt-fan-chart" data-ajax-url="<?= e(route('fan-chart', ['xref' => $individual->getXref(), 'ged' => $individual->getTree()->getName(), 'generations' => $generations, 'chart_style' => $chart_style, 'fan_width' => $fan_width])) ?>"></div>
