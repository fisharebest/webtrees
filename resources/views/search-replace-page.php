<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form class="wt-page-options wt-page-options-ancestors-chart hidden-print" method="post" action="<?= e(route('search-replace', ['ged' => $tree->getName()])) ?>">
	<?= csrf_field() ?>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="search">
			<?= I18N::translate('Search for') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control form-control-sm" id="search" name="search" value="<?= e($search) ?>" type="text" autofocus>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="replace">
			<?= I18N::translate('Replace with') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control form-control-sm" id="replace" name="replace" value="<?= e($replace) ?>" type="text">
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label">
			<?= /* I18N: A button label. */ I18N::translate('Search') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= Bootstrap4::radioButtons('context', ['all' => I18N::translate('Entire record'), 'name' => I18N::translate('Names'), 'place' => I18N::translate('Places')], $context, false) ?>
		</div>
	</div>
	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label"></label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */ I18N::translate('replace') ?>">
		</div>
	</div>
</form>
