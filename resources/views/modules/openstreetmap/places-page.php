<?php use Fisharebest\Webtrees\I18N; ?>

<div id="place-hierarchy">
	<div class="container">
		<h4><?= $title ?>
			<?php foreach($breadcrumbs as $item): ?>
				- <a href="<?= $item->getURL() ?>" dir="auto"><?= $item->getPlaceName() ?></a>
			<?php endforeach ?>
			-<a href="<?=e(route('place-hierarchy', ['ged' => $tree->getName()])) ?>"><?= I18N::translate('Top level') ?></a>
		</h4>
		<?= $content ?>
		<h4><a href= <?= e(route('place-hierarchy', ['ged' => $tree->getName(), 'action' => $nextaction])
			) ?>><?= $nextactiontitle ?></a></h4>
	</div>
</div>

