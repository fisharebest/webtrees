<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<p>
	<?= $block->getDescription() ?>
</p>

<form method="post">
	<input type="hidden" name="save" value="1">
	<?= csrf_field() ?>

	<?= $block->configureBlock($block_id) ?>

	<div class="row form-group">
		<div class="offset-sm-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<?= FontAwesome::decorativeIcon('save') ?>
				<?= I18N::translate('save') ?>
			</button>
			<a class="btn btn-secondary" href="<?= e($cancel_url) ?>">
				<?= FontAwesome::decorativeIcon('cancel') ?>
				<?= I18N::translate('cancel') ?>
			</a>
		</div>
	</div>
</form>
