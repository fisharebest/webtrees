<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="card mb-4">
	<div class="card-header">
		<a href="#" data-toggle="collapse" data-target="#add-restriction" aria-expanded="false" aria-controls="add-restriction">
			<?= I18N::translate('Add a restriction') ?>
		</a>
	</div>

	<div class="card-body collapse" id="add-restriction">
		<?= FunctionsEdit::addSimpleTag($level . ' RESN') ?>
	</div>
</div>
