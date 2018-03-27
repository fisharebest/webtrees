<?php use Fisharebest\Webtrees\I18N; ?>

<div id="place-hierarchy">
	<div class="container">
		<h4><?= $title ?></h4>
        <h5 class="center">
			<?php if ($current): ?>
                <a href="<?= e(route('place-hierarchy', ['ged' => $tree->getName()])
				) ?>"><?= I18N::translate('World') ?></a>
			<?php else: ?>
				<?= I18N::translate('World') ?>
			<?php endif ?>
			<?php foreach ($breadcrumbs as $item): ?>
                - <a href="<?= $item->getURL() ?>" dir="auto"><?= $item->getPlaceName() ?></a>
			<?php endforeach ?>
			<?php if ($current): ?>
                - <?= $current->getPlaceName() ?>
			<?php endif ?>
        </h5>
		<?php if ($note): ?>
            <div class="center small text-muted">
				<?= I18N::translate("Places without valid co-ordinates are not shown on the map and have a red border around the sidebar entry") ?>
            </div>
		<?php endif ?>
		<?= $content ?>
        <div class="center">
            <?php if ($showeventslink): ?>
                    <a class="formField" href= <?= e(route('place-hierarchy',
                            ['ged' => $tree->getName(), 'parent' => $parent, 'action' => 'hierarchy-e']
                        )
                    ) ?>><?= I18N::translate('View table of events occurring in %s', $place) ?></a>
                |
            <?php endif ?>
            <a href="<?= e(route('place-hierarchy', ['ged' => $tree->getName(), 'action' => key($nextaction)]))
                ?>"><?= current($nextaction) ?></a>
        </div>
	</div>
</div>

