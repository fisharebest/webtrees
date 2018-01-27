<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form class="wt-page-options wt-page-options-report-select">
	<input type="hidden" name="route" value="report-setup">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">
	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="report">
			<?= I18N::translate('Report') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<select class="form-control" id="report" name="report">
				<?php foreach ($reports as $file => $report): ?>
					<option value="<?= e($file) ?>">
						<?= e($report) ?>
					</option>
				<?php endforeach ?>
			</select>
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
