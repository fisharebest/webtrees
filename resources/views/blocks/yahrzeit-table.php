<?php use Fisharebest\Webtrees\I18N; ?>

<table class="table table-sm datatable wt-table-yahrzeits" data-info="false" data-paging="false" data-searching="false" data-sorting="<?= e('[[3, "asc" ]]') ?>">
	<thead>
		<tr>
			<th><?= I18N::translate('Name') ?></th>
			<th><?= I18N::translate('Death') ?></th>
			<th><i class="icon-reminder" title="<?= I18N::translate('Anniversary') ?>"></i></th>
			<th><?= I18N::translate('Yahrzeit') ?></th>
		</tr>
	</thead>
	<tbody>

		<?php foreach ($yahrzeits as $yahrzeit): ?>
			<tr>
				<td data-sort="<?= e($yahrzeit->individual->getSortName()) ?>">
					<a href="<?= e($yahrzeit->individual->url()) ?>">
						<?= $yahrzeit->individual->getFullname() ?>
						<?php if ($yahrzeit->individual->getAddName()): ?>
						<br>
						<?= $yahrzeit->individual->getAddName() ?>
						<?php endif ?>
					</a>
				</td>
				<td data-sort="<?= e($yahrzeit->fact_date->julianDay()) ?>">
					<?= $yahrzeit->fact_date->display() ?>
				</td>
				<td data-sort="<?= e($yahrzeit->fact->anniv) ?>">
					<?= I18N::number($yahrzeit->fact->anniv) ?>
				</td>
				<td data-sort="<?= e($yahrzeit->yahrzeit_date->julianDay()) ?>">
					<?= $yahrzeit->yahrzeit_date->display() ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<script>
  $(".wt-table-yahrzeits").dataTable();
</script>
