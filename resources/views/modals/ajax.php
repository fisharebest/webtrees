<!-- A dynamic modal, with content loaded using AJAX. -->
<div class="modal fade" id="wt-ajax-modal" tabindex="-1" role="dialog" aria-labelledBy="wt-ajax-modal-title" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content wt-ajax-load" id="wt-ajax-modal-content"></div>
	</div>
</div>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		$('#wt-ajax-modal').on('show.bs.modal', function (event) {
			$('#wt-ajax-modal-content')
				.empty()
				.load(event.relatedTarget.dataset.href);
		});
	});
</script>
