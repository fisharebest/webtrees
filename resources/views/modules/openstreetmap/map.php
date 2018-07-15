<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<div class="py-4">
	<div class="row gchart osm-wrapper">
		<div id="osm-map" class="col-sm-9 wt-ajax-load osm-user-map"></div>
		<ul class='col-sm-3 osm-sidebar wt-page-options-value'></ul>
	</div>

	<?php if (Auth::isAdmin()): ?>
		<p class="center">
			<a href="<?= e(route('admin-module',[
				'module' => $module,
				'action' => 'AdminConfig',
			])) ?>">
				<?= I18N::translate('Map module preferences') ?>
			</a>
			|
			<a href="<?= e(route('admin-module',[
				'module' => $module,
				'action' => 'AdminPlaces',
			])) ?>">
				<?= I18N::translate('Geographic data') ?>
			</a>
		</p>
	<?php endif ?>
</div>

<?php View::push('javascript') ?>
<?php foreach ($assets['css'] as $css_file): ?>
	<link rel="stylesheet" type="text/css" href="<?= e($css_file) ?>">
<?php endforeach; ?>
<?php View::endpush() ?>

<?php View::push('javascript') ?>
<?php foreach ($assets['js'] as $js_file): ?>
	<script src="<?= e($js_file) ?>"></script>
<?php endforeach ?>

<script>
	'use strict';
	WT_OSM.drawMap(<?= json_encode($ref) ?>, <?= json_encode($type) ?>, <?= json_encode($generations ?? null) ?>);
</script>
<?php View::endpush() ?>
