<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="card mb-4">
	<div class="card-header">
		<a href="#" data-toggle="collapse" data-target="#add-note" aria-expanded="false" aria-controls="add-note">
			<?= I18N::translate('Add a note') ?>
		</a>
	</div>

	<div class="card-body collapse" id="add-note">
		<?= FunctionsEdit::addSimpleTag($level . ' NOTE') ?>
	</div>
</div>
