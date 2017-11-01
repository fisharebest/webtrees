<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="card mb-4">
	<div class="card-header">
		<?= I18N::translate('Add an associate') ?>
	</div>

	<div class="card-body">
		<?= FunctionsEdit::addSimpleTag($level . ' _ASSO @') ?>
		<?= FunctionsEdit::addSimpleTag(($level + 1) . ' RELA') ?>
		<?= FunctionsEdit::addSimpleTag(($level + 1) . ' NOTE') ?>
		<?= FunctionsEdit::addSimpleTag(($level + 1) . ' SHARED_NOTE') ?>
	</div>
</div>
