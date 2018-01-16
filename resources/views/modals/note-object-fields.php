<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-group">
	<label class="col-form-label" for="note">
		<?= I18N::translate('Note') ?>
	</label>
	<textarea class="form-control" id="note" name="note" required rows="5"></textarea>
</div>

<?= view('modals/restriction-fields') ?>
