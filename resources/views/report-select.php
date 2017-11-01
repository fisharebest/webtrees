<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= I18N::translate('Choose a report to run') ?>
</h2>

<form action="reportengine.php" class="wt-page-options wt-page-options-report-select">
	<input type="hidden" name="action" value="setup">
	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="report">
			<?= I18N::translate('Report') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<select class="form-control" id="report" name="report">
				<?php foreach ($reports as $file => $report): ?>
					<option value="<?= Html::escape($file) ?>">
						<?= Html::escape($report) ?>
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
