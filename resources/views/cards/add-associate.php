<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="card mb-4">
	<div class="card-header">
		<a href="#" data-toggle="collapse" data-target="#add-associate" aria-expanded="false" aria-controls="add-associate">
			<?= I18N::translate('Add an associate') ?>
		</a>
	</div>

	<div class="card-body collapse" id="add-associate">
		<?= FunctionsEdit::addSimpleTag($level . ' _ASSO @') ?>
		<?= FunctionsEdit::addSimpleTag(($level + 1) . ' RELA') ?>
		<?= FunctionsEdit::addSimpleTag(($level + 1) . ' NOTE') ?>
		<?= FunctionsEdit::addSimpleTag(($level + 1) . ' SHARED_NOTE') ?>
	</div>
</div>
