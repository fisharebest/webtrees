<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="card mb-4">
	<div class="card-header">
		<?= I18N::translate('Add a shared note') ?>
	</div>

	<div class="card-body">
		<?= FunctionsEdit::addSimpleTag($level . ' SHARED_NOTE', $parent_tag) ?>
	</div>
</div>
