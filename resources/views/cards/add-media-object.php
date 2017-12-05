<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="card mb-4">
	<div class="card-header">
		<a href="#" data-toggle="collapse" data-target="#add-media-object" aria-expanded="false" aria-controls="add-media-object">
			<?= I18N::translate('Add a media object') ?>
		</a>
	</div>

	<div class="card-body collapse" id="add-media-object">
		<?= FunctionsEdit::addSimpleTag($level . ' OBJE') ?>
	</div>
</div>
