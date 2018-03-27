<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>

<?= view('admin/breadcrumbs', ['links' => $breadcrumbs]) ?>

<div class="form-group row">
	<div class="col-sm-10 offset-sm-1">
		<div id="osm-map" class="wt-ajax-load col-sm-12 osm-admin-map"></div>
	</div>
</div>

<form method="post" id="editplaces" name="editplaces"
	  action="<?= e(route('admin-module', ['module' => $module, 'action' => 'AdminSave'])) ?>">
	<?= csrf_field() ?>
	<input type="hidden" name="place_id" value="<?= $place_id ?>">
	<input type="hidden" name="level" value="<?= count($hierarchy) ?>">
	<input type="hidden" name="parent_id" value="<?= $parent_id ?>">
	<input type="hidden" name="place_long" value="<?= $lng ?>">
	<input type="hidden" name="place_lati" value="<?= $lat ?>">
	<input type="hidden" name="inactive" value="<?= $inactive ?>">

	<div class="form-group row">
		<label class="col-form-label col-sm-1" for="new_place_name">
			<?= I18N::translate('Place') ?>
		</label>
		<div class="col-sm-5">
			<input type="text" id="new_place_name" name="new_place_name" value="<?= e($location->getPlace()) ?>"
				   class="form-control" required>
		</div>
		<label class="col-form-label col-sm-1" for="icon">
			<?= I18N::translate('Flag') ?>
		</label>
		<div class="col-sm-4">
			<div class="input-group" dir="ltr">
				<?= FunctionsEdit::formControlFlag(
					$location->getIcon(),
					['name' => 'icon', 'id' => 'icon', 'class' => 'form-control']
				)
				?>
			</div>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-form-label col-sm-1">
			<?= I18N::translate('Latitude') ?>
		</label>
		<div class="col-sm-3">
			<div class="input-group">
				<input type="text" id="new_place_lati" class="editable form-control" name="new_place_lati" required
					   placeholder="<?= I18N::translate('degrees') ?>" value="<?= $lat ?>"
				>
			</div>
		</div>

		<label class="col-form-label col-sm-1">
			<?= I18N::translate('Longitude') ?>
		</label>
		<div class="col-sm-3">
			<div class="input-group">
				<input type="text" id="new_place_long" class="editable form-control" name="new_place_long" required
					   placeholder="<?= I18N::translate('degrees') ?>" value="<?= $lng ?>"
				>
			</div>
		</div>
		<label class="col-form-label col-sm-1" for="new_zoom_factor">
			<?= I18N::translate('Zoom') ?>
		</label>
		<div class="col-sm-2">
			<input type="text" id="new_zoom_factor" name="new_zoom_factor" value="<?= $location->getZoom() ?>"
				   class="form-control" required readonly>
		</div>
	</div>

	<div class="form-group row">
		<div class="col-sm-10 offset-sm-1">
			<button class="btn btn-primary" type="submit">
				<?= /* I18N: A button label. */
				I18N::translate('save')
				?>
			</button>
			<a class="btn btn-secondary" href="<?=
			e(route('admin-module', [
					'module' => $module,
					'action' => 'AdminPlaces',
					'parent_id' => $parent_id,
					'inactive'   => $inactive,
					]
				));
			?>">
				<?= /* I18N: A button label. */
				I18N::translate('cancel')
				?>
			</a>
		</div>
	</div>
</form>

<?php View::push('javascript') ?>
	<script>
		'use strict';
		// Load the module's CSS files
		let link;
		<?php foreach ($assets['css'] as $css_file): ?>
		link = document.createElement("link");
		link.setAttribute("rel", "stylesheet");
		link.setAttribute("type", "text/css");
		link.setAttribute("href", "<?= e($css_file) ?>");
		document.head.appendChild(link);
		<?php endforeach ?>
	</script>

<?php foreach ($assets['js'] as $js_file): ?>
	<script src="<?= e($js_file) ?>"></script>
<?php endforeach ?>

	<script>
		'use strict';
		WT_OSM_ADMIN.drawMap('<?= e($ref) ?>');
	</script>
<?php View::endpush() ?>
