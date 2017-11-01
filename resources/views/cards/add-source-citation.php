<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="card mb-4">
	<div class="card-header">
		<?= I18N::translate('Add a source citation') ?>
	</div>

	<div class="card-body">
		<?= FunctionsEdit::addSimpleTag($level . ' SOUR @') ?>
		<?= FunctionsEdit::addSimpleTag(($level + 1) . ' PAGE') ?>
		<?= FunctionsEdit::addSimpleTag(($level + 1) . ' DATA') ?>
		<?= FunctionsEdit::addSimpleTag(($level + 2) . ' TEXT') ?>

		<?php if ($full_citations): ?>
			<?= FunctionsEdit::addSimpleTag(($level + 2) . ' DATE', '', I18N::translate('Date of entry in original source')) ?>
			<?= FunctionsEdit::addSimpleTag(($level + 1) . ' QUAY') ?>
		<?php endif ?>

		<?= FunctionsEdit::addSimpleTag(($level + 1) . ' OBJE') ?>
		<?= FunctionsEdit::addSimpleTag(($level + 1) . ' SHARED_NOTE') ?>
	</div>
</div>
