<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="num">
		<?= /* I18N: ... to show in a list */ I18N::translate('Number of surnames') ?>
	</label>
	<div class="col-sm-9">
		<input class="form-control"id="num" name="num" type="text"  value="<?= e($num) ?>">
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
