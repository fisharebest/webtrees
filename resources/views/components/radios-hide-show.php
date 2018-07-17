<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-check form-check-inline">
	<input class="form-check-input" type="radio" name="<?= e($name) ?>" id="radio-<?= e($name) ?>-hide" <?= $value ? '' : 'checked' ?>>
	<label class="form-check-label" for="radio-<?= e($name) ?>-hide">
    <?= I18N::translate('hide') ?>
	</label>
</div>

<div class="form-check form-check-inline">
	<input class="form-check-input" type="radio" name="<?= e($name) ?>" id="radio-<?= e($name) ?>-show" <?= $value ? 'checked' : '' ?>>
	<label class="form-check-label" for="radio-<?= e($name) ?>-show">
    <?= I18N::translate('show') ?>
	</label>
</div>
