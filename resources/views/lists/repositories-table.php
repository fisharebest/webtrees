<?php use Fisharebest\Webtrees\Database; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?php
// Count the number of linked records. These numbers include private records.
// It is not good to bypass privacy, but many servers do not have the resources
// to process privacy for every record in the tree
$count_sources = Database::prepare(
"SELECT l_to, COUNT(*) FROM `##sources` JOIN `##link` ON l_from = s_id AND l_file = s_file AND l_type = 'REPO' AND l_file = :tree_id GROUP BY l_to"
)->execute(['tree_id' => $tree->getTreeId()])->fetchAssoc();
?>

<table
	class="table table-bordered table-sm wt-table-source datatables d-none"
	data-columns="<?= e(json_encode([
		null,
		['visible' => array_sum($count_sources) > 0],
		['visible' => (bool) $tree->getPreference('SHOW_LAST_CHANGE'), 'searchable' => false],
	])) ?>"
	data-state-save="true"
>
	<caption class="sr-only">
		<?= $caption ?? I18N::translate('Repositories') ?>
	</caption>

	<thead>
		<tr>
			<th><?= I18N::translate('Repository name') ?></th>
			<th><?= I18N::translate('Sources') ?></th>
			<th><?= I18N::translate('Last change') ?></th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($repositories as $repository): ?>
			<tr class="<?= $repository->isPendingDeletion() ? 'old' : ($repository->isPendingAddition() ? 'new' : '') ?>">
				<!-- Repository name -->
				<td data-sort="<?= e($repository->getSortName()) ?>">
					<a href="<?= e($repository->url()) ?>">
						<?= $repository->getFullName() ?>
					</a>
				</td>

				<!-- Count of linked sources -->
				<td class="center" data-sort="<?= $count_sources[$repository->getXref()] ?? 0 ?>">
					<?= I18N::number($count_sources[$repository->getXref()] ?? 0) ?>
				</td>

				<!-- Last change -->
				<td data-sort="<?= $repository->lastChangeTimestamp(true) ?>">
					<?= $repository->lastChangeTimestamp() ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
