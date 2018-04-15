<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form class="wt-page-options wt-page-options-family-book-chart d-print-none">
	<input type="hidden" name="route" value="family-book">
	<input type="hidden" name="ged" value="<?= e($individual->getTree()->getName()) ?>">

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="xref">
			<?= I18N::translate('Individual') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= FunctionsEdit::formControlIndividual($individual->getTree(), $individual, ['id' => 'xref', 'name' => 'xref']) ?>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="book_size">
			<?= I18N::translate('Generations') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= Bootstrap4::select(FunctionsEdit::numericOptions(range(2, 5)), $book_size, ['id' => 'book_size', 'name' => 'book_size']) ?>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="generations">
			<?= I18N::translate('Descendant generations') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= Bootstrap4::select(FunctionsEdit::numericOptions(range($minimum_generations, $maximum_generations)), $generations, ['id' => 'generations', 'name' => 'generations']) ?>
		</div>
	</div>

	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3 wt-page-options-label">
				<?= I18N::translate('Spouses') ?>
			</legend>
			<div class="col-sm-9 wt-page-options-value">
				<?= Bootstrap4::checkbox(I18N::translate('Show spouses'), false, ['name' => 'show_spouse', 'checked' => $show_spouse]) ?>
			</div>
		</div>
	</fieldset>

	<div class="row form-group">
		<div class="col-sm-3 wt-page-options-label"></div>
		<div class="col-sm-9 wt-page-options-value">
			<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */ I18N::translate('view') ?>">
		</div>
	</div>
</form>

<div class="wt-ajax-load wt-page-content wt-chart wt-family-book-chart" data-ajax-url="<?= e(route('family-book-chart', ['xref' => $individual->getXref(), 'ged' => $individual->getTree()->getName(), 'book_size' => $book_size, 'generations' => $generations, 'show_spouse' => (int) $show_spouse])) ?>"></div>
