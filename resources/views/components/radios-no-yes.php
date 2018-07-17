<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-check form-check-inline">
	<input class="form-check-input" type="radio" name="<?= e($name) ?>" id="radio-<?= e($name) ?>-no" <?= $value ? '' : 'checked' ?>>
	<label class="form-check-label" for="radio-<?= e($name) ?>-no">
    <?= I18N::translate('no') ?>
	</label>
</div>

<div class="form-check form-check-inline">
	<input class="form-check-input" type="radio" name="<?= e($name) ?>" id="radio-<?= e($name) ?>-yes" <?= $value ? 'checked' : '' ?>>
	<label class="form-check-label" for="radio-<?= e($name) ?>-yes">
    <?= I18N::translate('yes') ?>
	</label>
</div>
