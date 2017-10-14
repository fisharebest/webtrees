<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php ?>

<div class="card mb-4">
	<div class="card-header">
		<?= I18N::translate('Add a restriction') ?>
	</div>

	<div class="card-body">
		<?= FunctionsEdit::addSimpleTag($level . ' RESN') ?>
	</div>
</div>
