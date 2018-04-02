<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form class="wt-page-options wt-page-options-branches d-print-none">
	<input type="hidden" name="route" value="branches">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">

	<div class="form-group row">
		<label class="col-form-label col-sm-3 wt-page-options-label" for="surname">
			<?= I18N::translate('Surname') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control" data-autocomplete-type="SURN" type="text" name="surname" id="surname" value="<?= e($surname) ?>" dir="auto">
		</div>
	</div>

	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3 wt-page-options-label">
				<?= I18N::translate('Phonetic search') ?>
			</legend>
			<div class="col-sm-9 wt-page-options-value">
				<?= Bootstrap4::checkbox(I18N::translate('Russell'), true, ['name' => 'soundex_std', 'checked' => $soundex_std]) ?>
				<?= Bootstrap4::checkbox(I18N::translate('Daitch-Mokotoff'), true, ['name' => 'soundex_dm', 'checked' => $soundex_dm]) ?>
			</div>
		</div>
	</fieldset>

	<div class="form-group row">
		<div class="col-sm-3 wt-page-options-label">
		</div>
		<div class="col-sm-9 wt-page-options-value">
			<button type="submit" class="btn btn-primary">
				<?= /* I18N: A button label. */ I18N::translate('view') ?>
			</button>
		</div>
	</div>
</form>

<?php if ($surname !== ''): ?>
	<div class="wt-ajax-load wt-page-content wt-chart wt-branches" data-ajax-url="<?= e(route('branches-list', ['surname' => $surname, 'soundex_std' => $soundex_std, 'soundex_dm' => $soundex_dm, 'ged' => $tree->getName()])) ?>"></div>
<?php endif ?>
