<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<button
	class="btn btn-primary btn-small mb-1 wt-fix-button"
	data-confirm="<?= I18N::translate('Move the media object?') ?>"
	data-fact-id="<?= Html::escape($fact->getFactId()) ?>"
	data-tree-id="<?= Html::escape($tree->getTreeId()) ?>"
	data-individual-xref="<?= Html::escape($individual->getXref()) ?>"
	data-media-xref="<?= Html::escape($media->getXref()) ?>"
	type="button"
>
	<?= $fact->getLabel() ?>
	<?= $fact->getDate()->display(false, '%Y', false) ?>
</button>
