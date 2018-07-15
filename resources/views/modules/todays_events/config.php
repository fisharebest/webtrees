<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="filter">
		<?= I18N::translate('Show only events of living individuals') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::radioButtons('filter', FunctionsEdit::optionsNoYes(), $filter, true) ?>
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="events">
		<?= I18N::translate('Events') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::multiSelect($all_events, $event_array, ['id' => 'events', 'name' => 'events[]', 'class' => 'select2']) ?>
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="infoStyle">
		<?= /* I18N: Label for a configuration option */ I18N::translate('Presentation style') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::select($info_styles, $infoStyle, ['id' => 'infoStyle', 'name' => 'infoStyle']) ?>
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="sortStyle">
		<?= /* I18N: Label for a configuration option */ I18N::translate('Sort order') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::select($sort_styles, $sortStyle, ['id' => 'sortStyle', 'name' => 'sortStyle']) ?>
	</div>
</div>
