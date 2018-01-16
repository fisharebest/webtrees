<?php use Fisharebest\Webtrees\MediaFile; ?>

<?php if ($individual->findHighlightedMediaFile() instanceof MediaFile): ?>
	<?= $individual->findHighlightedMediaFile()->displayImage(30, 40, 'crop', []) ?>
<?php endif; ?>
<?= $individual->getFullName() ?>, <?= $individual->getLifeSpan() ?>
