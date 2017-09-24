<?php use Fisharebest\Webtrees\Html; ?>

<div class="card mb-4 wt-block wt-block-<?= Html::escape($class) ?>" id="<?= Html::escape($id) ?>">
	<div class="card-header wt-block-header wt-block-header-<?= Html::escape($class) ?>" dir="auto">
		<?= Html::escape($title) ?>
	</div>
	<div class="card-block wt-block-content wt-block-content-<?= Html::escape($class) ?>">
		<?= $content ?>
	</div>
</div>
