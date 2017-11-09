<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= View::make('admin/breadcrumbs', ['links' => ['admin.php' => I18N::translate('Control panel'), 'admin_trees_manage.php' => I18N::translate('Manage family trees'), $title]]) ?>

<h1><?= $title ?></h1>

<form class="form" name="logs" action="admin.php">
	<input type="hidden" name="route" value="admin-changes-log">
	<input type="hidden" name="action" value="show">

	<div class="row">
		<div class="form-group col-xs-6 col-md-3">
			<label for="from">
				<?= /* I18N: From date1 (To date2) */ I18N::translate('From') ?>
			</label>
			<div class="input-group date">
				<input type="text" autocomplete="off" class="form-control" id="from" name="from" value="<?= Html::escape($from) ?>">
				<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
			</div>
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="to">
				<?= /* I18N: (From date1) To date2 */ I18N::translate('To') ?>
			</label>
			<div class="input-group date">
				<input type="text" autocomplete="off" class="form-control" id="to" name="to" value="<?= Html::escape($to) ?>">
				<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
			</div>
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
			<input class="form-control" type="text" id="xref" name="xref" value="<?= Html::escape($xref) ?>">
		</div>
	</div>

	<div class="row">
		<div class="form-group col-xs-6 col-md-3">
			<label for="oldged">
				<?= I18N::translate('Old data') ?>
			</label>
			<input class="form-control" type="text" id="oldged" name="oldged" value="<?= Html::escape($oldged) ?>">
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="newged">
				<?= I18N::translate('New data') ?>
			</label>
			<input class="form-control" type="text" id="newged" name="newged" value="<?= Html::escape($newged) ?>">
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="user">
				<?= I18N::translate('User') ?>
			</label>
			<?= Bootstrap4::select($user_list, $user, ['id' => 'user', 'name' => 'user']) ?>
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="gedc">
				<?= I18N::translate('Family tree') ?>
			</label>
			<?= Bootstrap4::select($tree_list, $gedc, ['id' => 'gedc', 'name' => 'gedc']) ?>
		</div>
	</div>

	<div class="row text-center">
		<button type="submit" class="btn btn-primary">
			<?= I18N::translate('search') ?>
		</button>

		<button type="submit" class="btn btn-primary" onclick="document.logs.action.value='export';return true;" <?= $action === 'show' ? '' : 'disabled' ?>>
			<?= /* I18N: A button label. */ I18N::translate('download') ?>
		</button>

		<button type="submit" class="btn btn-primary" onclick="if (confirm('<?= I18N::translate('Permanently delete these records?') ?>')) {document.logs.action.value='delete'; return true;} else {return false;}" <?= $action === 'show' ? '' : 'disabled' ?>>
			<?= I18N::translate('delete') ?>
		</button>
	</div>
</form>

<?php if ($action === 'show'): ?>
	<table
		class="table table-bordered table-sm table-hover table-site-changes datatables"
		data-ajax="<?= Html::escape(Html::url('admin.php', ['route' => 'admin-changes-log-data', 'from' => $from, 'to' => $to, 'type' => $type, 'xref' => $xref, 'oldged' => $oldged, 'newged' => $newged, 'tree' => $gedc, 'user' => $user])) ?>"
		data-server-side="true"
		data-sorting="<?= Html::escape('[[ 0, "desc" ]]') ?>"
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


