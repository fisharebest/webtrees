<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrint; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="text-center slide-show-container">
	<?php if ($show_controls): ?>
		<div class="slide-show-controls">
			<?= FontAwesome::linkIcon('media-play', I18N::translate('Play'), ['href' => '#', 'hidden' => $start_automatically]) ?>
			<?= FontAwesome::linkIcon('media-stop', I18N::translate('Stop'), ['href' => '#', 'hidden' => !$start_automatically]) ?>
			<?= FontAwesome::linkIcon('media-next', I18N::translate('Next image'), ['href' => '#']) ?>
		</div>
	<?php endif ?>

	<figure class="text-center slide-show-figure">
		<?= $media_file->displayImage(200, 200, '', ['class' => 'slide-show-image']) ?>
		<figcaption class="slide-show-figcaption">
			<a href="<?= e($media->url()) ?>">
				<b><?= $media->getFullName() ?></b>
			</a>
		</figcaption>
	</figure>

	<p class="slide-show-notes">
		<?= FunctionsPrint::printFactNotes($media->getGedcom(), 1) ?>
	</p>

	<ul class="slide-show-links">
		<?php foreach ($media->linkedIndividuals('OBJE') as $individual): ?>
			<?= I18N::translate('Individual') ?> —
			<a href="<?= e($individual->url()) ?>" class="slide-show-link">
				<?= $individual->getFullName() ?>
			</a>
			<br>
		<?php endforeach ?>

		<?php foreach ($media->linkedFamilies('OBJE') as $family): ?>
			<?= I18N::translate('View this family') ?> —
			<a href="<?= e($family->url()) ?>" class="slide-show-link">
				<?= $family->getFullName() ?>
			</a>
			<br>
		<?php endforeach ?>

		<?php foreach ($media->linkedSources('OBJE') as $source): ?>
			<?= I18N::translate('View this source') ?> —
			<a href="<?= e($source->url()) ?>" class="slide-show-link">
				<?= $source->getFullName() ?>
			</a>
			<br>
		<?php endforeach ?>
	</ul>
</div>

<script>
	// Reload automatically?
	var play = <?= json_encode($start_automatically); ?>;

	function slideShowReload() {
		if (play) {
			var block = $("#block-<?= $block_id ?>").parent();
			clearTimeout(timeout);
			block.load(block.data('ajaxUrl') + '&start=' + (play ? '1' : '0'));
		}

		return false;
	}

	$(".wt-icon-media-play").on('click', function () {
		$(".wt-icon-media-play").parent().attr('hidden', true);
		$(".wt-icon-media-stop").parent().attr('hidden', false);
		play = true;
		return slideShowReload();
	});

	$(".wt-icon-media-stop").on('click', function () {
		$(".wt-icon-media-stop").parent().attr('hidden', true);
		$(".wt-icon-media-play").parent().attr('hidden', false);
		play = false;
		return false;
	});

	$(".wt-icon-media-next").on('click', function () {
		play = true;
		return slideShowReload();
	});

	var timeout = setTimeout(slideShowReload, 6000);
</script>
