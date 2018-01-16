<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="card mb-4">
	<div class="card-header">
		<a href="#" data-toggle="collapse" data-target="#add-note-object" aria-expanded="false" aria-controls="add-note-object">
			<?= I18N::translate('Add a shared note') ?>
		</a>
	</div>

	<div class="card-body collapse" id="add-note-object">
		<?= FunctionsEdit::addSimpleTag($level . ' SHARED_NOTE', $parent_tag) ?>
	</div>
</div>
