<?php use Fisharebest\Webtrees\Database; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?php
// Count the number of linked records. These numbers include private records.
// It is not good to bypass privacy, but many servers do not have the resources
// to process privacy for every record in the tree
$count_individuals = Database::prepare(
	"SELECT l_to, COUNT(*) FROM `##individuals` JOIN `##link` ON l_from = i_id AND l_file = i_file AND l_type = 'NOTE' AND l_file = :tree_id GROUP BY l_to"
)->execute(['tree_id' => $tree->getTreeId()])->fetchAssoc();
$count_families    = Database::prepare(
	"SELECT l_to, COUNT(*) FROM `##families` JOIN `##link` ON l_from = f_id AND l_file = f_file AND l_type = 'NOTE' AND l_file = :tree_id GROUP BY l_to"
)->execute(['tree_id' => $tree->getTreeId()])->fetchAssoc();
$count_sources     = Database::prepare(
	"SELECT l_to, COUNT(*) FROM `##sources` JOIN `##link` ON l_from = s_id AND l_file = s_file AND l_type = 'NOTE' AND l_file = :tree_id GROUP BY l_to"
)->execute(['tree_id' => $tree->getTreeId()])->fetchAssoc();
?>

<table
	class="table table-bordered table-sm wt-table-media datatables"
	data-columns="<?= e(json_encode([
		null,
		null,
		['visible' => array_sum($count_individuals) > 0],
		['visible' => array_sum($count_families) > 0],
		['visible' => array_sum($count_sources) > 0],
		['visible' => (bool) $tree->getPreference('SHOW_LAST_CHANGE'), 'searchable' => false],
	])) ?>"
	data-state-save="true"
>
	<caption class="sr-only">
		<?= $caption ?? I18N::translate('Media objects') ?>
	</caption>

	<thead>
		<tr>
			<th><?= I18N::translate('Media') ?></th>
			<th><?= I18N::translate('Title') ?></th>
			<th><?= I18N::translate('Individuals') ?></th>
			<th><?= I18N::translate('Families') ?></th>
			<th><?= I18N::translate('Sources') ?></th>
			<th><?= I18N::translate('Last change') ?></th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($media_objects as $media_object): ?>
			<tr class="<?= $media_object->isPendingDeletion() ? 'old' : ($media_object->isPendingAddition() ? 'new' : '') ?>">
				<!-- Thumbnails-->
				<td data-sort="<?= e($media_object->getSortName()) ?>">
					<?php foreach ($media_object->mediaFiles() as $media_file): ?>
						<?= $media_file->displayImage(100, 100, 'contain', []) ?>
					<?php endforeach ?>
				</td>

				<!-- Title -->
				<td data-sort="<?= e($media_object->getSortName()) ?>">
					<a href="<?= e($media_object->url()) ?>">
						<?= $media_object->getFullName() ?>
					</a>
				</td>

				<!-- Count of linked individuals -->
				<td class="center" data-sort="<?= $count_individuals[$media_object->getXref()] ?? 0 ?>">
					<?= I18N::number($count_individuals[$media_object->getXref()] ?? 0) ?>
				</td>

				<!-- Count of linked families -->
				<td class="center" data-sort="<?= $count_families[$media_object->getXref()] ?? 0 ?>">
					<?= I18N::number($count_families[$media_object->getXref()] ?? 0) ?>
				</td>

				<!-- Count of sources -->
				<td class="center" data-sort="<?= $count_sources[$media_object->getXref()] ?? 0 ?>">
					<?= I18N::number($count_sources[$media_object->getXref()] ?? 0) ?>
				</td>

				<!-- Last change -->
				<td data-sort="<?= $media_object->lastChangeTimestamp(true) ?>">
					<?= $media_object->lastChangeTimestamp() ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
