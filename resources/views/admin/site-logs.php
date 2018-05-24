<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<form class="form" name="logs">
	<input type="hidden" name="action" value="show">

	<div class="row">
		<div class="form-group col-xs-6 col-sm-3">
			<label for="from">
				<?= /* I18N: label for the start of a date range (from x to y) */ I18N::translate('From') ?>
			</label>
			<input type="date" class="form-control" id="from" max="<?= e($latest) ?>" min="<?= e($earliest) ?>" name="from" value="<?= e($from) ?>" required>
		</div>

		<div class="form-group col-xs-6 col-sm-3">
			<label for="to">
				<?= /* I18N: label for the end of a date range (from x to y) */ I18N::translate('To') ?>
			</label>
			<input type="date" class="form-control" id="to" max="<?= e($latest) ?>" min="<?= e($earliest) ?>" name="to" value="<?= e($to) ?>" required>
		</div>

		<div class="form-group col-xs-6 col-sm-2">
			<label for="type">
				<?= I18N::translate('Type') ?>
			</label>
			<?= Bootstrap4::select(['' => '', 'auth' => 'auth', 'config' => 'config', 'debug' => 'debug', 'edit' => 'edit', 'error' => 'error', 'media' => 'media', 'search' => 'search'], $type, ['id' => 'type', 'name' => 'type']) ?>
		</div>

		<div class="form-group col-xs-6 col-sm-4">
			<label for="ip">
				<?= I18N::translate('IP address') ?>
			</label>
			<input class="form-control" type="text" id="ip" name="ip" value="<?= e($ip) ?>">
		</div>
	</div>

	<div class="row">
		<div class="form-group col-sm-4">
			<label for="text">
				<?= I18N::translate('Message') ?>
			</label>
			<input class="form-control" type="text" id="text" name="text" value="<?= e($text) ?>">
		</div>

		<div class="form-group col-sm-4">
			<label for="user">
				<?= I18N::translate('User') ?>
			</label>
			<?= Bootstrap4::select($user_options, $user, ['id' => 'user', 'name' => 'user']) ?>
		</div>

		<div class="form-group col-sm-4">
			<label for="gedc">
				<?= I18N::translate('Family tree') ?>
			</label>
			<?= Bootstrap4::select($tree_options, $gedc, ['id' => 'gedc', 'name' => 'gedc']) ?>
		</div>
	</div>

	<div class="text-center">
		<button type="submit" class="btn btn-primary">
			<i class="fas fa-search">
			<?= /* I18N: A button label. */ I18N::translate('search') ?>
		</button>

		<button type="submit" class="btn btn-primary" onclick="document.logs.action.value='export';return true;" <?= $action === 'show' ? '' : 'disabled' ?>>
			<i class="fas fa-download">
			<?= /* I18N: A button label. */ I18N::translate('download') ?>
		</button>

		<button type="submit" class="btn btn-primary" data-confirm="<?= I18N::translate('Permanently delete these records?') ?>" onclick="if (confirm(this.dataset.confirm)) {document.logs.action.value='delete'; return true;} else {return false;}" <?= $action === 'show' ? '' : 'disabled' ?>>
			<i class="fas fa-trash-alt"
			<?= /* I18N: A button label. */ I18N::translate('delete') ?>
		</button>
	</div>
</form>

<?php if ($action): ?>
	<table class="table table-bordered table-sm table-hover table-site-logs">
		<caption class="sr-only">
			<?= $controller->getPageTitle() ?>
		</caption>
		<thead>
			<tr>
				<th></th>
				<th><?= I18N::translate('Timestamp') ?></th>
				<th><?= I18N::translate('Type') ?></th>
				<th><?= I18N::translate('Message') ?></th>
				<th><?= I18N::translate('IP address') ?></th>
				<th><?= I18N::translate('User') ?></th>
				<th><?= I18N::translate('Family tree') ?></th>
			</tr>
		</thead>
	</table>
<?php endif ?>
