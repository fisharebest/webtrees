<?php use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="card mb-4 wt-block wt-block-<?= Html::escape($block) ?>-block" id="block-<?= Html::escape($id) ?>">
	<div class="card-header wt-block-header wt-block-header-<?= Html::escape($block) ?>" dir="auto">
		<?php if ($admin_url !== ''): ?>
			<?= FontAwesome::linkIcon('preferences', I18N::translate('Preferences'), ['class' => 'btn btn-link', 'href' => $admin_url]) ?>
		<?php endif ?>
		<?= Html::escape($title) ?>
	</div>
	<div class="card-block wt-block-content wt-block-content-<?= Html::escape($block) ?>">
		<?= $content ?>
	</div>
</div>
