<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= Bootstrap4::breadcrumbs([route('admin-control-panel') => I18N::translate('Control panel')], $title) ?>

<h1><?= $title ?></h1>

<p>
	<?= /* I18N: "thumbs" is fixed text.  Do not change it. */ I18N::translate('In webtrees version 1, you could add custom thumbnails to media objects by creating files in the “thumbs” folders.') ?>
	<br>
	<?= I18N::translate('In webtrees version 2, custom thumbnails are stored as a second media file in the same media object.') ?>
	<br>
	<?= I18N::translate('If the thumbnail image is the same as the original image, it is no longer needed and you should delete it.  If it is a custom image, you should add it to the media object.') ?>
</p>

<table class="table table-bordered table-sm table-hover datatables wt-fix-table" data-ajax="<?= e(json_encode(['url' => route('admin-webtrees1-thumbs-data')])) ?>" data-server-side="true" data-state-save="true" data-sort="false" data-auto-width="false" data-save-state="true">
	<caption class="sr-only">
		<?= I18N::translate('Media objects') ?>
	</caption>
	<thead class="thead-dark">
		<tr>
			<th data-sortable="false"><?= I18N::translate('Thumbnail image') ?></th>
			<th data-sortable="false"><?= I18N::translate('Media file') ?></th>
			<th data-sortable="false"><?= I18N::translate('Media object') ?></th>
			<th data-sortable="false"><?= I18N::translate('Comparison') ?></th>
			<th data-sortable="false"><?= I18N::translate('Action') ?></th>
		</tr>
	</thead>
</table>

<?php View::push('javascript') ?>
<script>
  'use strict';

  // If we click on a button, post the request and reload the table
  document.querySelector(".wt-fix-table").onclick = function (event) {
    let element = event.target;
    if (element.classList.contains("wt-fix-button")) {
      event.stopPropagation();
      event.preventDefault();
      if (!element.dataset.confirm || confirm(element.dataset.confirm)) {
        let formData = new FormData(element.form);
        formData.append('action', element.dataset.action);

        $.ajax({
          data:        formData,
          method:      element.form.method,
          url:         element.form.action,
          async:       false,
          contentType: false,
          processData: false,
          complete: function() {
            $(".wt-fix-table").DataTable().ajax.reload(null, false);
          }
        });
      }
    }
  };
</script>
<?php View::endpush() ?>
