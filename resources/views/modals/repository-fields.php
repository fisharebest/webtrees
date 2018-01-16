<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-group">
	<label class="col-form-label" for="repository-name">
		<?= I18N::translateContext('Repository', 'Name') ?>
	</label>
	<input class="form-control" type="text" id="repository-name" name="repository-name" required>
</div>

<?= view('modals/restriction-fields') ?>
