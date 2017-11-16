<?php use Fisharebest\Webtrees\Html; ?>

<?php if ($block->loadAjax()): ?>
	<div class="wt-ajax-load" data-ajax-url="<?= Html::escape(route('user-page-block', ['ged' => $tree->getName(), 'block_id' => $block_id])) ?>">
	</div>
<?php else: ?>
	<?= $block->getBlock($block_id) ?>
<?php endif ?>
