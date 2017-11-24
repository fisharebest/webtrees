<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="modal-header">
	<h3 class="modal-title" id="wt-ajax-modal-title">
		<?= $title ?>
	</h3>
	<button type="button" class="close" data-dismiss="modal" aria-label="<?= I18N::translate('close') ?>">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<div class="alert alert-danger">
		<?= $error ?>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">
		<?= FontAwesome::decorativeIcon('cancel') ?>
		<?= I18N::translate('cancel') ?>
	</button>
</div>
