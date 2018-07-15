<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="type">
		<?= I18N::translate('Chart type') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::select($charts, $type, ['id' => 'type', 'name' => 'type']) ?>
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="pid">
		<label for="pid">
			<?= I18N::translate('Individual') ?>
		</label>
	</label>
	<div class="col-sm-9">
		<?= FunctionsEdit::formControlIndividual($tree, $individual, ['id' => 'pid', 'name' => 'pid']) ?>
	</div>
</div>
