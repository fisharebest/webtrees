<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-group">
	<label class="col-form-label" for="submitter-name">
		<?= I18N::translate('Name') ?>
	</label>
	<input class="form-control" type="text" id="submitter-name" name="submitter_name" required>
</div>
<div class="form-group">
	<label class="col-form-label" for="submitter-address">
		<?= I18N::translate('Address') ?>
	</label>
	<input class="form-control" type="text" id="submitter-address" name="submitter_address">
</div>

<?= view('modals/restriction-fields') ?>
