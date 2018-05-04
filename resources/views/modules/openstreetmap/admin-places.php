<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Tree; ?>

<?= view('admin/breadcrumbs', ['links' => $breadcrumbs]) ?>

<div class="form-group row">
	<div class="col-sm-3 col-form-label">
		<?= I18N::translate('Module configuration') ?>
	</div>
	<div class="col-sm-9">
		<a class="btn btn-primary"
		   href="<?= e(route('admin-module', ['module' => $module, 'action' => 'AdminConfig'])) ?>">
			<?= FontAwesome::decorativeIcon('edit') ?>
			<?= I18N::translate('edit') ?>
		</a>
	</div>
</div>
<form method="POST"
	  action="<?=e(route('admin-module',
		  ['module' => $module, 'action' => 'AdminPlaces', 'parent_id' => $parent_id])) ?>">

	<?= csrf_field() ?>
	<?= Bootstrap4::checkbox(
		I18N::translate('Show inactive places'),
		false,
		['name' => 'inactive', 'checked' => $inactive, 'onclick' => 'this.form.submit()']
	) ?>
	<p class="small text-muted">
		<?= I18N::translate(
			'By default, the list shows only those places which can be found in your family trees. You may have details for other places, such as those imported in bulk from an external file. Selecting this option will show all places, including ones that are not currently used.'
		) ?>
		<?= I18N::translate('If you have a large number of inactive places, it can be slow to generate the list.') ?>

	</p>
</form>

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
            <td>
                <a href="<?= e(route('admin-module',
					   ['module' => $module,
					    'action' => 'AdminPlaces',
					    'parent_id' => $place->pl_id,
					    'inactive' => $inactive]
				   )
				   ) ?>">
					<?= e($place->pl_place) ?>
                    <span class="badge badge-pill badge-<?= $place->badge ?>">
				        <?= I18N::number($place->child_count) ?>
                    </span>
                </a>
            </td>
			<td>
				<?= ($place->pl_lati === null) ? FontAwesome::decorativeIcon('warning') :
                    strtr($place->pl_lati, ['N' => '', 'S' => '-', ',' => '.']) ?>
			</td>
			<td>
				<?= ($place->pl_long === null) ? FontAwesome::decorativeIcon('warning') :
                    strtr($place->pl_long, ['E' => '', 'W' => '-', ',' => '.']) ?>
			</td>
			<td>
				<?= $place->pl_long === null ? FontAwesome::decorativeIcon('warning') : $place->pl_zoom ?>
			</td>
			<td>
				<?php if (is_file(WT_MODULES_DIR . $module . '/' . $place->pl_icon)): ?>
					<img src="<?= e(WT_MODULES_DIR . $module . '/' . $place->pl_icon) ?>" width="25" height="15"
						 title="<?= I18N::translate("Flag of %s", $place->pl_place) ?>"
                         alt="<?= I18N::translate("Flag of %s", $place->pl_place) ?>">
				<?php endif ?>
			</td>
			<td>
				<?=
				FontAwesome::linkIcon(
					'edit',
					I18N::translate('Edit'),
					[
						'href' => route('admin-module',
							[
								'module'	=> $module,
								'action'	=> 'AdminPlaceEdit',
								'place_id'  => $place->pl_id,
								'parent_id' => $place->pl_parent_id,
								'inactive'  => $inactive,
							]
						),
						'class' => 'btn btn-primary'
					]
				)
				?>
			</td>
			<td>
				<?php if ($place->child_count === 0): ?>
					<form method="POST" action="<?=
					e(route('admin-module', [
						'module' => $module,
						'action' => 'AdminDeleteRecord'
					]));
					?>"
						  data-confirm="<?= I18N::translate('Remove this location?') ?>"
						  onsubmit="return confirm(this.dataset.confirm)">
						<input type="hidden" name="parent_id" value="<?= $parent_id ?>">
						<input type="hidden" name="place_id" value="<?= $place->pl_id ?>">
						<input type="hidden" name="inactive" value="<?= $inactive ?>">
						<?= csrf_field() ?>
						<button type="submit" class="btn btn-danger">
							<?= FontAwesome::semanticIcon('delete', I18N::translate('Delete')) ?>
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
			<a class="btn btn-primary" href="<?= e(route('admin-module',
				[
					'module'	=> $module,
					'action'	=> 'AdminPlaceEdit',
					'parent_id' => $parent_id,
					'inactive'  => $inactive,
				]
			)) ?>">
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
				<a class="dropdown-item" href="<?=
				e(route('admin-module',
					[
						'module'	=> $module,
						'action'	=> 'AdminExport',
						'parent_id' => $parent_id,
						'inactive'  => $inactive,
						'format'	=> 'csv',
					]
				)) ?>">csv
				</a>
				<a class="dropdown-item" href="<?=
				e(route('admin-module',
					[
						'module'	=> $module,
						'action'	=> 'AdminExport',
						'parent_id' => $parent_id,
						'inactive'  => $inactive,
						'format'	=> 'geojson',
					]
				)) ?>">geoJSON
				</a>
			</div>
			<a class="btn btn-primary" href="<?=
			e(route('admin-module',
				[
					'module'	=> $module,
					'action'	=> 'AdminImportForm',
					'parent_id' => $parent_id,
					'inactive'  => $inactive,
				]
			)) ?>">
				<?= FontAwesome::decorativeIcon('upload') ?>
				<?= /* I18N: A button label. */
				I18N::translate('import file') ?>
			</a>
		</td>
	</tr>
	</tfoot>
</table>

<form method="POST" action="<?= e(route('admin-module',[
		'module' => $module,
		'action' => 'AdminImportPlaces'
	])) ?>">
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
				<?= FontAwesome::decorativeIcon('add') ?>
				<?= /* I18N: A button label. */
				I18N::translate('import') ?>
			</button>
		</div>
	</div>
</form>
