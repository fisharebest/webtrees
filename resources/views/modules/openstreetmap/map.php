<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<div class="wt-map-tab py-4">
	<div class="gchart osm-wrapper">
		<div id="osm-map" class="wt-ajax-load osm-user-map"></div>
		<div class='osm-sidebar'></div>
	</div>

	<?php if (Auth::isAdmin()): ?>
		<div class="osm-options">
			<a href="<?= e(Html::url('module.php', ['mod' => 'openstreetmap', 'mod_action' => 'admin_config'])) ?>">
				<?= I18N::translate('Map module preferences') ?>
			</a>
			|
			<a href="<?= e(Html::url('module.php', ['mod' => 'openstreetmap', 'mod_action' => 'admin_places'])) ?>">
				<?= I18N::translate('Geographic data') ?>
			</a>
		</div>
	<?php endif ?>
</div>

<?php View::push('javascript') ?>
	<script>
		'use strict';
		// Load the module's CSS files
		let link;
		<?php foreach ($assets['css_files'] as $css_file): ?>
            link = document.createElement("link");
            link.setAttribute("rel", "stylesheet");
            link.setAttribute("type", "text/css");
            link.setAttribute("href", "<?= e($css_file) ?>");
            document.head.appendChild(link);
		<?php endforeach ?>
	</script>

	<?php foreach ($assets['js_files'] as $js_file): ?>
		<script src="<?= e($js_file) ?>"></script>
	<?php endforeach ?>

	<script>
		'use strict';
		WT_OSM.drawMap('<?= e($ref) . "','" . e($tree) ?>', '<?= $type ?>');
	</script>
<?php View::endpush() ?>
