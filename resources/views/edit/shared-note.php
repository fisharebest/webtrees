<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title"><?= $title ?></h2>

<form class="wt-page-options" method="post">
	<?= csrf_field() ?>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="NOTE">
			<?= I18N::translate('Shared note') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<textarea class="form-control" name="NOTE" id="NOTE" rows="15"><?= e($note->getNote()) ?></textarea>
			<?= FontAwesome::linkIcon('keyboard', I18N::translate('Find a special character'), ['class' => 'wt-osk-trigger', 'href' => '#', 'data-id' => 'NOTE']) ?>
		</div>
	</div>

	<div class="row form-group">
		<div class="col-sm-9 offset-sm-3">
			<button class="btn btn-primary" type="submit">
				<?= FontAwesome::decorativeIcon('save') ?>
				<?= /* I18N: A button label. */
				I18N::translate('save') ?>
			</button>
			<a class="btn btn-secondary" href="<?= e($note->url()) ?>">
				<?= FontAwesome::decorativeIcon('cancel') ?>
				<?= /* I18N: A button label. */
				I18N::translate('cancel') ?>
			</a>
		</div>
	</div>
</form>
<?= view('modals/on-screen-keyboard'); ?>
