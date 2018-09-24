<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Tree; ?>

<?= view('components/breadcrumbs', ['links' => $breadcrumbs]) ?>

<h1><?= $title ?></h1>

<table class="table table-bordered table-striped table-sm table-hover">
	<thead class="thead-dark">
	<tr>
		<th><?= I18N::translate('Place') ?></th>
		<th><?= I18N::translate('Latitude') ?></th>
		<th><?= I18N::translate('Longitude') ?></th>
		<th><?= I18N::translate('Zoom level') ?></th>
		<th><?= I18N::translate('Flag') ?> </th>
		<th><?= I18N::translate('Edit') ?></th>
		<th><?= I18N::translate('Delete') ?></th>
	</tr>
	</thead>
	<tbody>

	<?php foreach ($placelist as $place): ?>
		<tr>
            <th scope="row">
                <a href="<?= e(route('map-data', ['parent_id' => $place->pl_id])) ?>">
					<?= e($place->pl_place) ?>
                    <span class="badge badge-pill badge-<?= $place->badge ?>">
				        <?= I18N::number($place->child_count) ?>
                    </span>
                </a>
            </th>
			<td>
				<?= ($place->pl_lati === null) ? FontAwesome::decorativeIcon('warning') : strtr($place->pl_lati, ['N' => '', 'S' => '-', ',' => '.']) ?>
			</td>
			<td>
				<?= ($place->pl_long === null) ? FontAwesome::decorativeIcon('warning') : strtr($place->pl_long, ['E' => '', 'W' => '-', ',' => '.']) ?>
			</td>
			<td>
				<?= $place->pl_long === null ? FontAwesome::decorativeIcon('warning') : $place->pl_zoom ?>
			</td>
			<td>
				<?php if (is_file(WT_MODULES_DIR . 'openstreetmap' . '/' . $place->pl_icon)): ?>
					<img src="<?= e(WT_MODULES_DIR . 'openstreetmap' . '/' . $place->pl_icon) ?>" width="25" height="15" alt="<?= I18N::translate("Flag of %s", $place->pl_place) ?>">
				<?php endif ?>
			</td>
			<td>
				<?= FontAwesome::linkIcon('edit', I18N::translate('Edit'), ['href' => route('map-data-edit', ['place_id'  => $place->pl_id, 'parent_id' => $place->pl_parent_id]), 'class' => 'btn btn-primary']) ?>
			</td>
			<td>
				<?php if ($place->child_count === 0): ?>
					<form method="POST" action="<?= e(route('map-data-delete', ['parent_id' => $parent_id, 'place_id' => $place->pl_id])) ?>"
						  data-confirm="<?= I18N::translate('Remove this location?') ?>"
						  onsubmit="return confirm(this.dataset.confirm)">
                        <?= csrf_field() ?>
						<button type="submit" class="btn btn-danger">
							<?= FontAwesome::semanticIcon('delete', I18N::translate('delete')) ?>
						</button>
					</form>
				<?php else: ?>
					<button type="button" class="btn btn-danger" disabled>
						<?= FontAwesome::decorativeIcon('delete') ?>
					</button>
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
	</tbody>
	<tfoot>
	<tr>
		<td colspan="7">
			<a class="btn btn-primary" href="<?= e(route('map-data', ['parent_id' => $parent_id])) ?>">
				<?= FontAwesome::decorativeIcon('add') ?>
				<?= /* I18N: A button label. */
				I18N::translate('add place') ?>
			</a>
			<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
					data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<?= FontAwesome::decorativeIcon('download') ?>
				<?= /* I18N: A button label. */
				I18N::translate('export file') ?>
			</button>
			<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				<a class="dropdown-item" href="<?= e(route('locations-export', ['parent_id' => $parent_id, 'format' => 'csv'])) ?>">
                    csv
				</a>
				<a class="dropdown-item" href="<?= e(route('locations-export', ['parent_id' => $parent_id, 'format' => 'geojson']
				)) ?>">
                    geoJSON
				</a>
			</div>
			<a class="btn btn-primary" href="<?= e(route('locations-import', ['parent_id' => $parent_id])) ?>">
                <?= FontAwesome::decorativeIcon('upload') ?>
				<?= /* I18N: A button label. */
				I18N::translate('import file') ?>
			</a>
		</td>
	</tr>
	</tfoot>
</table>

<form method="POST" action="<?= e(route('locations-import-from-tree')) ?>">
	<?= csrf_field() ?>

	<div class="form-group row">
		<label class="form-control-plaintext col-sm-4" for="ged">
			<?= I18N::translate('Import all places from a family tree') ?>
		</label>
		<div class="col-sm-6">
			<?= Bootstrap4::select(
				Tree::getNameList(),
				'',
				['id' => 'ged', 'name' => 'ged']
			) ?>
		</div>
		<div class="col-sm-2">
			<button type="submit" class="btn btn-primary">
                <?= view('icons/upload') ?>
				<?= /* I18N: A button label. */
				I18N::translate('import') ?>
			</button>
		</div>
	</div>
</form>
