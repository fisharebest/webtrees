<?php use Fisharebest\Webtrees\Html; ?>

<div class="d-flex flex-wrap justify-content-around">
	<?php foreach ($links as $link): ?>
		<div class="text-center m-1">
			<a href="<?= Html::escape($link['url']) ?>">
				<i class="<?= $link['icon'] ?>"></i>
				<br>
				<?= $link['title'] ?>
			</a>
		</div>
	<?php endforeach ?>
</div>
