<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= Bootstrap4::breadcrumbs([route('admin-control-panel') => I18N::translate('Control panel')], $title) ?>

<h1><?= $title ?></h1>

<p>
	<?= /* I18N: "thumbs" is fixed text.  Do not change it. */ I18N::translate('In webtrees version 1, you could add custom thumbnails to media objects by creating files in the "thumbs" folders.') ?>
	<br>
	<?= I18N::translate('In webtrees version 2, custom thumbnails are stored as a second media file in the same media object.') ?>
	<br>
	<?= I18N::translate('If the thumbnail image is the same as the original, it is no longer needed and you should delete it.  If it is a custom image, you should import it.') ?>
</p>

<table class="table table-bordered table-sm table-hover table-responsive datatables wt-fix-table" data-ajax="<?= e(json_encode(['url' => route('admin-webtrees1-thumbs-data')])) ?>" data-server-side="true" data-state-save="true" data-sort="false" data-auto-width="false" data-save-state="true">
	<caption class="sr-only">
		<?= I18N::translate('Media objects') ?>
	</caption>
	<thead class="thead-dark">
		<tr>
			<th data-sortable="false"><?= I18N::translate('Thumbnail') ?></th>
			<th data-sortable="false"><?= I18N::translate('Media file') ?></th>
			<th data-sortable="false"><?= I18N::translate('Media object') ?></th>
			<th data-sortable="false"><?= I18N::translate('Comparison') ?></th>
			<th data-sortable="false"><?= I18N::translate('Action') ?></th>
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
