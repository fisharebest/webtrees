<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-trees') => I18N::translate('Manage family trees'), $title]]) ?>

<h1><?= $title ?></h1>

<form class="form" name="logs">
	<input type="hidden" name="action" value="show">
	<input type="hidden" name="route" value="admin-changes-log">
	<input type="hidden" name="ged" value="<?= e($ged) ?>">

	<div class="row">
		<div class="form-group col-xs-6 col-md-3">
			<label for="from">
				<?= /* I18N: From date1 (To date2) */ I18N::translate('From') ?>
			</label>
			<input type="date" class="form-control" id="from" max="<?= e($latest) ?>" min="<?= e($earliest) ?>" name="from" value="<?= e($from) ?>" required>
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="to">
				<?= /* I18N: (From date1) To date2 */ I18N::translate('To') ?>
			</label>
			<input type="date" class="form-control" id="to" max="<?= e($latest) ?>" min="<?= e($earliest) ?>"  name="to" value="<?= e($to) ?>" required>
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="type">
				<?= I18N::translate('Status') ?>
			</label>
			<?= Bootstrap4::select($statuses, $type, ['id' => 'type', 'name' => 'type']) ?>
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="xref">
				<?= I18N::translate('Record') ?>
			</label>
			<input class="form-control" type="text" id="xref" name="xref" value="<?= e($xref) ?>">
		</div>
	</div>

	<div class="row">
		<div class="form-group col-xs-6 col-md-3">
			<label for="oldged">
				<?= I18N::translate('Old data') ?>
			</label>
			<input class="form-control" type="text" id="oldged" name="oldged" value="<?= e($oldged) ?>">
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="newged">
				<?= I18N::translate('New data') ?>
			</label>
			<input class="form-control" type="text" id="newged" name="newged" value="<?= e($newged) ?>">
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="username">
				<?= I18N::translate('User') ?>
			</label>
			<?= Bootstrap4::select($user_list, $username, ['id' => 'username', 'name' => 'username']) ?>
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="ged">
				<?= I18N::translate('Family tree') ?>
			</label>
			<?= Bootstrap4::select($tree_list, $ged, ['id' => 'ged', 'name' => 'ged']) ?>
		</div>
	</div>

	<div class="text-center">
		<button type="submit" class="btn btn-primary">
			<i class="fas fa-search">
			<?= I18N::translate('search') ?>
		</button>

		<button type="submit" class="btn btn-secondary" onclick="document.logs.action.value='export';return true;" <?= $action === 'show' ? '' : 'disabled' ?>>
			<i class="fas fa-download">
			<?= /* I18N: A button label. */ I18N::translate('download') ?>
		</button>

		<button type="submit" class="btn btn-danger" data-confirm="<?= I18N::translate('Permanently delete these records?') ?>" onclick="if (confirm(this.dataset.confirm)) {document.logs.action.value='delete'; return true;} else {return false;}" <?= $action === 'show' ? '' : 'disabled' ?>>
			<i class="fas fa-trash-alt"
			<?= I18N::translate('delete') ?>
		</button>
	</div>
</form>

<?php if ($action === 'show'): ?>
	<table
		class="table table-bordered table-sm table-hover table-site-changes datatables"
		data-ajax="<?= route('admin-changes-log-data', ['from' => $from, 'to' => $to, 'type' => $type, 'xref' => $xref, 'oldged' => $oldged, 'newged' => $newged, 'ged' => $ged, 'username' => $username]) ?>"
		data-server-side="true"
		data-sorting="<?= e('[[ 0, "desc" ]]') ?>"
	>
		<caption class="sr-only">
			<?= $title ?>
		</caption>
		<thead>
			<tr>
				<th data-visible="false"></th>
				<th><?= I18N::translate('Timestamp') ?></th>
				<th><?= I18N::translate('Status') ?></th>
				<th><?= I18N::translate('Record') ?></th>
				<th data-sortable="false"><?= I18N::translate('Data') ?></th>
				<th><?= I18N::translate('User') ?></th>
				<th><?= I18N::translate('Family tree') ?></th>
			</tr>
		</thead>
	</table>
<?php endif ?>
