<?php foreach ($media->mediaFiles() as $media_file): ?>
	<?= $media_file->displayImage(30, 40, 'crop', []) ?>
<?php endforeach ?>
<?= $media->getFullName() ?>
