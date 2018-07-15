<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="days">
		<?= I18N::translate('Number of days to show') ?>
	</label>
	<div class="col-sm-9">
		<input class="form-control" id="days" name="days" type="text" value="<?= e($days) ?>">
		<?= I18N::plural('maximum %s day', 'maximum %s days', I18N::number($max_days), I18N::number($max_days)) ?>
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="infoStyle">
		<?= I18N::translate('Presentation style') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::select($info_styles, $infoStyle, ['id' => 'infoStyle', 'name' => 'infoStyle']) ?>
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="sortStyle">
		<?= I18N::translate('Sort order') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::select($sort_styles, $sortStyle, ['id' => 'sortStyle', 'name' => 'sortStyle']) ?>
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="show_usere">
		<?= /* I18N: label for a yes/no option */ I18N::translate('Show the user who made the change') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::radioButtons('show_user', FunctionsEdit::optionsNoYes(), $show_user, true) ?>
	</div>
</div>
