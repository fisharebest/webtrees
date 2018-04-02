<p class="wt-lifespans-subtitle">
	<?= e($subtitle) ?>
</p>

<div class="wt-lifespans-scale">
	<?php for ($year = $start_year; $year < $end_year; $year += 10): ?><div class="wt-lifespans-decade"><?= $year ?></div><?php endfor ?>
</div>

<div class="wt-lifespans-individuals position-relative" style="height: <?= (5 + $max_rows) * 1.5 ?>rem; width: <?= ($end_year - $start_year) * 7 ?>px;">
	<?php foreach ($lifespans as $lifespan): ?>
	<a href="<?= e($lifespan->id) ?>" data-toggle="collapse" data-target="#<?= e($lifespan->id) ?>" aria-expanded="false" aria-controls="<?= e($lifespan->id) ?>">
		<div class="wt-lifespans-individual position-absolute text-nowrap text-truncate" dir="auto" style="background: <?= $lifespan->background ?>; <?= $dir === 'ltr' ? 'left' : 'right' ?>:<?= ($lifespan->birth_year - $start_year) * 7 ?>px; top:<?= $lifespan->row * 1.5 ?>rem; width:<?= ($lifespan->death_year - $lifespan->birth_year) * 7 + 5 ?>px;">
			<?= $lifespan->individual->getFullName() ?>
			<?= strip_tags($lifespan->individual->getLifespan()) ?>
		</div>
	</a>

	<div class="wt-lifespans-summary collapse position-absolute" id="<?= e($lifespan->id) ?>" style="<?= $dir === 'ltr' ? 'left' : 'right' ?>:<?= (min($lifespan->birth_year, $end_year - 50) - $start_year) * 7 ?>px; top:<?= ($lifespan->row + 1) * 1.5 ?>rem; width:350px;">
		<a class="wt-lifespans-summary-link" href="<?= e($lifespan->individual->url()) ?>">
			<?= $lifespan->individual->getFullName() ?>
		</a>

		<?php foreach ($lifespan->individual->getFacts(WT_EVENTS_BIRT . '|' . WT_EVENTS_DEAT, true) as $fact): ?>
			<?= $fact->summary() ?>
		<?php endforeach ?>
	</div>
	<?php endforeach ?>
</div>

