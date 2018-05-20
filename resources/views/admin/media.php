<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<form>
	<input type="hidden" name="route" value="admin-media">
	<table class="table table-bordered table-sm">
		<thead>
			<tr>
				<th><?= I18N::translate('Media files') ?></th>
				<th><?= I18N::translate('Media folders') ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<label>
						<input type="radio" name="files" value="local" <?= $files === 'local' ? 'checked' : '' ?> onchange="this.form.submit();">
						<?= /* I18N: “Local files” are stored on this computer */ I18N::translate('Local files') ?>
					</label>
					<br>
					<label>
						<input type="radio" name="files" value="external" <?= $files === 'external' ? 'checked' : '' ?> onchange="this.form.submit();">
						<?= /* I18N: “External files” are stored on other computers */ I18N::translate('External files') ?>
					</label>
					<br>
					<label>
						<input type="radio" name="files" value="unused" <?= $files === 'unused' ? 'checked' : '' ?> onchange="this.form.submit();">
						<?= I18N::translate('Unused files') ?>
					</label>
				</td>
				<td>
					<?php if ($files === 'local' || $files === 'unused'): ?>

						<div dir="ltr" class="form-inline">
							<?php if (count($media_folders) > 1): ?>
								<?= WT_DATA_DIR . Bootstrap4::select($media_folders, $media_folder, ['name' => 'media_folder', 'onchange' => 'this.form.submit();']) ?>
							<?php else: ?>
								<?= WT_DATA_DIR . e($media_folder) ?>
								<input type="hidden" name="media_folder" value="<?= e($media_folder) ?>">
							<?php endif ?>
						</div>

						<?php if (count($media_paths) > 1): ?>
							<?= Bootstrap4::select($media_paths, $media_path, ['name' => 'media_path', 'onchange' => 'this.form.submit();']) ?>
						<?php else: ?>
							<?= e($media_path) ?>
							<input type="hidden" name="media_path" value="<?= e($media_path) ?>">
						<?php endif ?>

						<label>
							<input type="radio" name="subfolders" value="include" <?= $subfolders === 'include' ? 'checked' : '' ?> onchange="this.form.submit();">
							<?= I18N::translate('Include subfolders') ?>
						</label>
						<br>
						<label>
							<input type="radio" name="subfolders" value="exclude" <?= $subfolders === 'exclude' ? ' checked' : '' ?> onchange="this.form.submit();">
							<?= I18N::translate('Exclude subfolders') ?>
						</label>

					<?php elseif ($files === 'external'): ?>

						<?= I18N::translate('External media files have a URL instead of a filename.') ?>
						<input type="hidden" name="media_folder" value="<?= e($media_folder) ?>">
						<input type="hidden" name="media_path" value="<?= e($media_path) ?>">

					<?php endif ?>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<br>
<br>

<table class="table table-bordered table-sm" id="media-table-<?= e($table_id) ?>" data-ajax="<?= e(route('admin-media-data', ['files' => $files, 'media_folder' => $media_folder, 'media_path' => $media_path, 'subfolders' => $subfolders ])) ?>">
	<thead>
		<tr>
			<th><?= I18N::translate('Media file') ?></th>
			<th><?= I18N::translate('Media') ?></th>
			<th><?= I18N::translate('Media object') ?></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<?= view('modals/create-media-from-file') ?>


<?php View::push('javascript') ?>
<script>
  $("#media-table-<?= e($table_id) ?>").dataTable({
    processing: true,
    serverSide: true,
    autoWidth: false,
    pageLength: 10,
    pagingType: "full_numbers",
    stateSave: true,
    stateDuration: 300,
    columns: [
      {},
      {sortable: false},
      {sortable: <?= $files === 'unused' ? 'false' : 'true' ?>}
    ],
	  <?= I18N::datatablesI18N([5, 10, 20, 50, 100, 500, 1000, -1]) ?>
  });
</script>
<?php View::endpush() ?>
