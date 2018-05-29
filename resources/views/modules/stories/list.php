<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<h2 class="wt-page-title"><?= $title ?></h2>

<table id="story_table" class="w-100">
	<thead>
		<tr>
			<th><?= I18N::translate('Story title') ?></th>
			<th><?= I18N::translate('Individual') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($stories as $story): ?>
			<tr>
				<td>
					<?= e($story->title) ?>
				</td>
				<td>
					<a href="<?= e($story->individual->url()) ?>#tab-stories">
						<?= $story->individual->getFullName() ?>
					</a>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<?php View::push('javascript') ?>
<script>
  $("#story_table").dataTable({
    dom: '<"H"pf<"dt-clear">irl>t<"F"pl>',
    autoWidth: false,
    paging: true,
    pagingType: "full_numbers",
    lengthChange: true,
    filter: true,
    info: true,
    sorting: [[0,"asc"]],
    columns: [
	    /* 0-name */ null,
      /* 1-NAME */ null
    ],
    <?= I18N::datatablesI18N() ?>
  });

</script>
<?php View::endpush() ?>
