<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="card mb-4 wt-block wt-block-<?= Html::escape($block) ?>-block" id="block-<?= Html::escape($id) ?>">
	<div class="card-header wt-block-header wt-block-header-<?= Html::escape($block) ?>" dir="auto">
		<?php if ($config_url): ?>
			<?= FontAwesome::linkIcon('preferences', I18N::translate('Preferences'), ['class' => 'btn btn-link', 'href' => $config_url]) ?>
		<?php endif ?>
		<?= Html::escape($title) ?>
	</div>
	<div class="card-body wt-block-content wt-block-content table-responsive-<?= Html::escape($block) ?>">
		<?= $content ?>
	</div>
</div>
