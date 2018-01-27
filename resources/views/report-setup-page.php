<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form class="wt-page-options wt-page-options-report-setup">
	<input type="hidden" name="route" value="report-run">
	<input type="hidden" name="report" value="<?= e($report) ?>">

	<div class="row form-group">
		<div class="col-sm-3 col-form-label wt-page-options-label">
			<?= I18N::translate('Description') ?>
		</div>
		<div class="col-sm-9 wt-page-options-value">
			<?= $description ?>
		</div>
	</div>

	<?php foreach ($inputs as $n => $input): ?>
		<input type="hidden" name="varnames[]" value="<?= e($input['name']) ?>">
		<div class="row form-group">
			<label class="col-sm-3 col-form-label wt-page-options-label" for="input-<?= $n ?>">
				<?= I18N::translate($input['value']) ?>
			</label>
			<div class="col-sm-9 wt-page-options-value">
				<?= $input['control'] ?>
				<?= $input['extra'] ?>
			</div>
		</div>
	<?php endforeach ?>

	<div class="row form-group">
		<div class="col-sm-3 col-form-label wt-page-options-label">
		</div>

		<div class="col-sm-9 wt-page-options-value d-flex justify-content-around">
			<div class="text-center">
				<label for="HTML"><i class="icon-mime-text-html"></i></label>
				<br>
				<input type="radio" name="output" id="HTML" value="HTML" checked>
			</div>
			<div class="text-center">
				<label for="PDF"><i class="icon-mime-application-pdf"></i></label>
				<br>
				<input type="radio" name="output" value="PDF" id="PDF">
			</div>
		</div>
	</div>

	<div class="row form-group">
		<div class="col-sm-3 col-form-label wt-page-options-label"></div>
		<div class="col-sm-9 wt-page-options-value">
			<button type="submit" class="btn btn-primary">
				<?= I18N::translate('continue') ?>
			</button>
		</div>
	</div>
</form>
