<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\GedcomTag; ?>

<div class="row form-group">
	<label class="col-sm-3 col-form-label wt-page-options-label" for="fields[<?= e($field_name) ?>]">
		<?= GedcomTag::getLabel($field_name) ?>
	</label>

	<div class="col-sm-6 wt-page-options-value">
		<input class="form-control form-control" type="text" id="fields[<?= e($field_name) ?>]" name="fields[<?= e($field_name) ?>]" value="<?= e($field_value) ?>">
		</div>

	<div class="col-sm-3 wt-page-options-value">
		<?php if (preg_match('/(GIVN|SURN)$/', $field_name)): ?>
			<?= Bootstrap4::select($name_options, $modifier, ['name' => 'modifiers[' . $field_name . ']']) ?>
		<?php endif ?>

		<?php if (preg_match('/(DATE)$/', $field_name)): ?>
			<?= Bootstrap4::select($date_options, $modifier, ['name' => 'modifiers[' . $field_name . ']']) ?>
		<?php endif ?>
	</div>
</div>
