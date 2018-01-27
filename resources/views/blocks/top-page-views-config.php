<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="num">
		<?= /* I18N: ... to show in a list */ I18N::translate('Number of pages') ?>
	</label>
	<div class="col-sm-9">
		<input class="form-control" id="num" name="num" type="text" value="<?= e($num) ?>">
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="count_placement">
		<?= /* I18N: Label for a configuration option */ I18N::translate('Show counts before or after name') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::select($options, $count_placement, ['id' => 'count_placement', 'name' => 'count_placement']) ?>
	</div>
</div>
