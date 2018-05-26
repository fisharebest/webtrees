<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<form class="form" name="logs">
	<input type="hidden" name="route" value="admin-site-logs" id="route">
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
			<label for="username">
				<?= I18N::translate('User') ?>
			</label>
			<?= Bootstrap4::select($user_options, $username, ['id' => 'username', 'name' => 'username']) ?>
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
			<i class="fas fa-search"></i>
			<?= /* I18N: A button label. */ I18N::translate('search') ?>
		</button>

		<a href="<?= e(route('admin-site-logs-export', ['from' => $from, 'to' => $to, 'type' => $type, 'text' => $text, 'ip' => $ip, 'username' => $username, 'gedc' => $gedc])) ?>" class="btn btn-primary" <?= $action === 'show' ? '' : 'disabled' ?>>
			<i class="fas fa-download"></i>
			<?= /* I18N: A button label. */ I18N::translate('download') ?>
		</a>

		<a href="#" class="btn btn-primary" data-confirm="<?= I18N::translate('Permanently delete these records?') ?>" id="delete-button" <?= $action === 'show' ? '' : 'disabled' ?>>
			<i class="fas fa-trash-alt"></i>
			<?= /* I18N: A button label. */ I18N::translate('delete') ?>
		</a>
	</div>
</form>

<?php if ($action): ?>
	<table class="table table-bordered table-sm table-hover table-site-logs" data-ajax="<?= e(route('admin-site-logs-data', ['from' => $from, 'to' => $to, 'type' => $type, 'text' => $text, 'ip' => $ip, 'user' => $username, 'gedc' => $gedc])) ?>" data-server-side="true">
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

<?php View::push('javascript') ?>
<script>
	$("#delete-button").click(function() {
    if (confirm(this.dataset.confirm)) {
      var data = $(this).closest('form').serialize();
      data.csrf = <?= json_encode(csrf_token()) ?>;

      jQuery.post(
        <?= json_encode(route('admin-site-logs-delete')) ?>,
	      data,
        function() { document.location.reload(); }
      )
    }
  });

  $(".table-site-logs").dataTable( {
    processing: true,
    sorting: [[ 0, "desc" ]],
    columns: [
      /* log_id      */ { visible: false },
      /* Timestamp   */ { sort: 0 },
      /* Type        */ { },
      /* message     */ { },
      /* IP address  */ { },
      /* User        */ { },
      /* Family tree */ { }
    ],
    <?= I18N::datatablesI18N([10, 20, 50, 100, 500, 1000, -1]) ?>
  });
</script>
<?php View::endpush() ?>
