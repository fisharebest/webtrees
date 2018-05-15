<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<table class="table table-sm table-bordered table-user-list">
	<thead>
		<tr>
			<th><?= I18N::translate('Edit') ?></th>
			<th><!-- user id --></th>
			<th><?= I18N::translate('Username') ?></th>
			<th><?= I18N::translate('Real name') ?></th>
			<th><?= I18N::translate('Email address') ?></th>
			<th><?= I18N::translate('Language') ?></th>
			<th><!-- date registered --></th>
			<th><?= I18N::translate('Date registered') ?></th>
			<th><!-- last login --></th>
			<th><?= I18N::translate('Last signed in') ?></th>
			<th><?= I18N::translate('Verified') ?></th>
			<th><?= I18N::translate('Approved') ?></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

<?php View::push('javascript') ?>
<script>
  $(".table-user-list").dataTable({
    stateSave:           true,
    stateDuration:       300,
    processing:          true,
    serverSide:          true,
    ajax:                {
      url: "<?= e(route('admin-users-data')) ?>"
    },
    autoWidth:           false,
    pageLength:          <?= $page_size ?>,
    sorting:             [[2, "asc"]],
    columns:             [
      /* details           */ {sortable: false},
      /* user-id           */ {visible: false},
      /* user_name         */ null,
      /* real_name         */ null,
      /* email             */ null,
      /* language          */ null,
      /* registered (sort) */ {visible: false},
      /* registered        */ {dataSort: 6},
      /* last_login (sort) */ {visible: false},
      /* last_login        */ {dataSort: 8},
      /* verified          */ null,
      /* approved          */ null
    ],
	  <?= I18N::datatablesI18N() ?>
  }).fnFilter("<?= e($filter) ?>"); // Pre-fill the search box
</script>
<?php View::endpush() ?>
