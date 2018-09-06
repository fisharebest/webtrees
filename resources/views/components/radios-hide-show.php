<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-check form-check-inline">
	<input class="form-check-input" type="radio" name="<?= e($name) ?>" id="radio-<?= e($name) ?>-false" value="" <?= $value ? '' : 'checked' ?>>
	<label class="form-check-label" for="radio-<?= e($name) ?>-false">
    <?= I18N::translate('hide') ?>
	</label>
</div>

<div class="form-check form-check-inline">
	<input class="form-check-input" type="radio" name="<?= e($name) ?>" id="radio-<?= e($name) ?>-true" value="1" <?= $value ? 'checked' : '' ?>>
	<label class="form-check-label" for="radio-<?= e($name) ?>-true">
    <?= I18N::translate('show') ?>
	</label>
</div>
