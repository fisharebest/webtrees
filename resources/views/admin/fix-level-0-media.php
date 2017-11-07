<?php use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;

?>
<?php ?>
<?php ?>

<?= Bootstrap4::breadcrumbs([Html::url('admin.php', ['route' => 'admin-control-panel']) => I18N::translate('Control panel')], $title) ?>

<h1><?= $title ?></h1>

<p>
	<?= I18N::translate('If you have linked a media object to an individual, instead of linking it to one of the facts or events, then you can move it to the correct location.') ?>
</p>

<table class="table table-bordered table-sm table-hover table-responsive datatables wt-fix-table" data-ajax="<?= HTML::escape(json_encode(['url' => Html::url('admin.php', ['route' => 'admin-fix-level-0-media-data'])])) ?>" data-state-save="true">
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
			<th data-sortable="false"><?= I18N::translate('Sources') ?></th>
		</tr>
	</thead>
</table>

<script>
  // If we click on a button, post the request and reload the table
  document.querySelector(".wt-fix-table").onclick = function (event) {
    var element = event.target;
    if (element.classList.contains("wt-fix-button")) {
      event.stopPropagation();
      if (confirm(element.dataset.confirm)) {
        $.ajax({
          data: {
            "route":     "admin-fix-level-0-media-action",
            "fact_id":   element.dataset.factId,
            "indi_xref": element.dataset.individualXref,
            "obje_xref": element.dataset.mediaXref,
            "tree_id":   element.dataset.treeId
          },
          method: "POST",
          url: "admin.php"
        }).done(function () {
          $(".wt-fix-table").DataTable().ajax.reload(null, false);
        });
      }
    }
  };
</script>
