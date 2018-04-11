<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="wt-map-tab py-4">
    <div class="gchart osm-wrapper">
        <div id="osm-map" class="wt-ajax-load osm-user-map"></div>
        <div class='osm-sidebar'></div>
    </div>

	<?php if (Auth::isAdmin()): ?>
        <div class="osm-options">
            <a href="<?=
			e(
				Html::url(
					'module.php',
					['mod' => $module, 'mod_action' => 'admin_config']
				)
			)
			?>">
				<?= I18N::translate('Open Street Maps preferences') ?>
            </a>
            |
            <a href="<?=
			e(
				Html::url(
					'module.php',
					['mod' => $module, 'mod_action' => 'admin_places']
				)
			)
			?>">
				<?= I18N::translate('Geographic data') ?>
            </a>
        </div>
	<?php endif ?>
</div>

<script>
	'use strict';
	WT_OSM.drawMap('<?= e($ref) . "','" . e($tree->getTreeId()) ?>', 'pedigree', <?= e($generations) ?>);
</script>
