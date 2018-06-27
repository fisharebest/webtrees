<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<div class="wt-map-tab py-4">
	<div class="gm-wrapper">
		<div class="gm-map"></div>
		<?= $map_data ?>
	</div>

	<?php if ($is_admin): ?>
		<div class="gm-options">
			<a href="<?= e(Html::url('module.php', ['mod' => 'googlemap', 'mod_action' => 'admin_config'])) ?>">
				<?= I18N::translate('Google Maps™ preferences') ?>
			</a>
			|
			<a href="<?= e(Html::url('module.php', ['mod' => 'googlemap', 'mod_action' => 'admin_places'])) ?>">
				<?= I18N::translate('Geographic data') ?>
			</a>
		</div>
	<?php endif ?>
</div>

<?php View::push('javascript') ?>
<script>
  'use strict';

  // Load the module's CSS file
  var newSheet=document.createElement("link");
  newSheet.setAttribute("rel","stylesheet");
  newSheet.setAttribute("type","text/css");
  newSheet.setAttribute("href","<?= $google_map_css ?>");
  document.head.appendChild(newSheet);

</script>
<script src="<?= e($google_map_js . '&callback=loadMap') ?>"></script>

<?php View::endpush() ?>
