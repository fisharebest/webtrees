<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="modal-footer">
	<button type="submit" class="btn btn-primary">
		<?= FontAwesome::decorativeIcon('save') ?>
		<?= I18N::translate('save') ?>
	</button>
	<button type="button" class="btn btn-secondary" data-dismiss="modal">
		<?= FontAwesome::decorativeIcon('cancel') ?>
		<?= I18N::translate('cancel') ?>
	</button>
</div>
