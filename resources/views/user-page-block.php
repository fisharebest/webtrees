<?php if ($block->loadAjax()) : ?>
    <div class="wt-ajax-load" data-ajax-url="<?= e(route('user-page-block', ['ged' => $tree->getName(), 'block_id' => $block_id])) ?>">
    </div>
<?php else : ?>
    <?= $block->getBlock($tree, $block_id) ?>
<?php endif ?>
