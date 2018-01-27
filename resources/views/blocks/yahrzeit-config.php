<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="days">
		<?= I18N::translate('Number of days to show') ?>
	</label>
	<div class="col-sm-9">
		<input class="form-control" type="text" id="days" name="days" value="<?= e($days) ?>">
		<em>
			<?= I18N::plural('maximum %s day', 'maximum %s days', $max_days, I18N::number($max_days)) ?>
		</em>
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="infoStyle">
		<?= I18N::translate('Presentation style') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::select($styles, $infoStyle, ['id' => 'infoStyle', 'name' => 'infoStyle']) ?>
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="calendar">
		<?= I18N::translate('Calendar') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::select($calendars, $calendar, ['id' => 'calendar', 'name' => 'calendar']) ?>
	</div>
</div>
