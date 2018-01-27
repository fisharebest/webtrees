<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="filter">
		<?= I18N::translate('Show only individuals, events, or all') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::select(['indi' => I18N::translate('Individuals'), 'event' => I18N::translate('Facts and events'), 'all' => I18N::translate('All')], $filter, ['id' => 'filter', 'name' => 'filter']) ?>
	</div>
</div>

<fieldset class="form-group">
	<div class="row">
		<legend class="col-form-label col-sm-3">
			<?= I18n::translate('Type') ?>
		</legend>
		<div class="col-sm-9">
			<?php foreach ($formats as $typeName => $typeValue): ?>
				<?= Bootstrap4::checkbox($typeValue, false, ['name' => 'filter_' . $typeName, 'checked' => (bool) $filters[$typeName]]) ?>
			<?php endforeach ?>
		</div>
	</div>
</fieldset>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="controls">
		<?= I18N::translate('Show slide show controls') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::radioButtons('controls', FunctionsEdit::optionsNoYes(), $controls, true) ?>
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="start">
		<?= I18N::translate('Start slide show on page load') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::radioButtons('start', FunctionsEdit::optionsNoYes(), $start, true) ?>
	</div>
</div>
