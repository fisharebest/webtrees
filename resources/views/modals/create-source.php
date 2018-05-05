<?php use Fisharebest\Webtrees\I18N; ?>

<form action="<?= e(route('create-source')) ?>" id="wt-modal-form" method="POST">
	<?= csrf_field() ?>
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">

	<?= view('modals/header', ['title' => I18N::translate('Create a source')]) ?>

	<div class="modal-body">
		<?= view('modals/source-fields', ['tree' => $tree]) ?>
	</div>

	<?= view('modals/footer-save-cancel') ?>
</form>

<script>
  // Submit the modal form using AJAX
  document.getElementById('wt-modal-form').addEventListener('submit', function(event) {
    event.preventDefault();
    let form          = event.target;
    let modal_content = document.querySelector('#wt-ajax-modal .modal-content');
    let select        = document.getElementById(modal_content.dataset.selectId);

    $.ajax({
      url:         form.action,
      type:        form.method,
      data:        new FormData(form),
      async:       false,
      cache:       false,
      contentType: false,
      processData: false,
      success:     function (data) {
        if (select) {
          // If this modal was activated by the "new" button in a select2
          // edit control, then insert the result and select it.
          $(select)
            .select2()
            .empty()
            .append(new Option(data.text, data.id)).val(data.id)
            .trigger('change');

          $('#wt-ajax-modal').modal('hide');
        } else {
          modal_content.innerHTML = data.html;
        }
      },
      failure:     function (data) {
        modal_content.innerHTML = data.html;
      }
    });
  });
</script>
