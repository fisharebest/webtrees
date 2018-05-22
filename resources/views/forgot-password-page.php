<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form class="wt-page-options wt-page-options-forgot-password" method="post">
	<?= csrf_field() ?>
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">

	<div class="form-group row">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="identifier">
			<?= I18N::translate('Username or email address') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control" type="text" id="identifier" name="identifier">
		</div>
	</div>

	<div class="form-group row">
		<div class="col-sm-3 col-form-label wt-page-options-label">
		</div>
		<div class="col-sm-9 wt-page-options-value">
			<button class="btn btn-primary" type="submit">
				<?= /* I18N: A button label. */ I18N::translate('continue') ?>
			</button>
		</div>
	</div>
</form>
