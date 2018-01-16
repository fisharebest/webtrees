<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= Bootstrap4::breadcrumbs([route('admin-control-panel') => I18N::translate('Control panel')], $title) ?>

<h1><?= $title ?></h1>

<p>
	<?= I18N::translate('If you have linked a media object to an individual, instead of linking it to one of the facts or events, then you can move it to the correct location.') ?>
</p>

<table class="table table-bordered table-sm table-hover table-responsive datatables wt-fix-table" data-ajax="<?= e(json_encode(['url' => route('admin-fix-level-0-media-data')])) ?>" data-server-side="true" data-state-save="true">
	<caption class="sr-only">
		<?= I18N::translate('Media objects') ?>
	</caption>
	<thead class="thead-dark">
		<tr>
			<th data-sortable="false"><?= I18N::translate('Tree') ?></th>
			<th data-sortable="false"><?= I18N::translate('Media object') ?></th>
			<th data-sortable="false"><?= I18N::translate('Title') ?></th>
			<th data-sortable="false"><?= I18N::translate('Individual') ?></th>
			<th data-sortable="false"><?= I18N::translate('Facts and events') ?></th>
		</tr>
	</thead>
</table>

<script>
  // If we click on a button, post the request and reload the table
  document.querySelector(".wt-fix-table").onclick = function (event) {
    let element = event.target;
    if (element.classList.contains("wt-fix-button")) {
      event.stopPropagation();
      if (confirm(element.dataset.confirm)) {
        $.ajax({
          data: {
            "fact_id":   element.dataset.factId,
            "indi_xref": element.dataset.individualXref,
            "obje_xref": element.dataset.mediaXref,
            "tree_id":   element.dataset.treeId
          },
          method: "POST",
          url: <?= json_encode(route('admin-fix-level-0-media-action')) ?>
        }).done(function () {
          $(".wt-fix-table").DataTable().ajax.reload(null, false);
        });
      }
    }
  };
</script>
