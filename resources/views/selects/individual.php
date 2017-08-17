<?php if ($individual->findHighlightedMedia() instanceof Media): ?>
	<?= $individual->findHighlightedMedia()->displayImage(30, 40, 'crop', []) ?>
<?php endif; ?>
<?= $individual->getFullName() ?>, <?= $individual->getLifeSpan() ?>
