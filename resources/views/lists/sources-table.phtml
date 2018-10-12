<?php use Fisharebest\Webtrees\Database; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?php
// Count the number of linked records. These numbers include private records.
// It is not good to bypass privacy, but many servers do not have the resources
// to process privacy for every record in the tree
$count_individuals = Database::prepare(
    "SELECT l_to, COUNT(*) FROM `##individuals` JOIN `##link` ON l_from = i_id AND l_file = i_file AND l_type = 'SOUR' AND l_file = :tree_id GROUP BY l_to"
)->execute(['tree_id' => $tree->getTreeId()])->fetchAssoc();
$count_families    = Database::prepare(
    "SELECT l_to, COUNT(*) FROM `##families` JOIN `##link` ON l_from = f_id AND l_file = f_file AND l_type = 'SOUR' AND l_file = :tree_id GROUP BY l_to"
)->execute(['tree_id' => $tree->getTreeId()])->fetchAssoc();
$count_media       = Database::prepare(
    "SELECT l_to, COUNT(*) FROM `##media` JOIN `##link` ON l_from = m_id AND l_file = m_file AND l_type = 'SOUR' AND l_file = :tree_id GROUP BY l_to"
)->execute(['tree_id' => $tree->getTreeId()])->fetchAssoc();
$count_notes       = Database::prepare(
    "SELECT l_to, COUNT(*) FROM `##other` JOIN `##link` ON l_from = o_id AND l_file = o_file AND o_type = 'NOTE' AND l_type = 'SOUR' AND l_file = :tree_id GROUP BY l_to"
)->execute(['tree_id' => $tree->getTreeId()])->fetchAssoc();
?>

<table
    class="table table-bordered table-sm wt-table-source datatables d-none"
    data-columns="<?= e(json_encode([
        null,
        null,
        ['visible' => array_sum($count_individuals) > 0],
        ['visible' => array_sum($count_families) > 0],
        ['visible' => array_sum($count_media) > 0],
        ['visible' => array_sum($count_notes) > 0],
        ['visible' => (bool) $tree->getPreference('SHOW_LAST_CHANGE'), 'searchable' => false],
    ])) ?>"
    data-state-save="true"
>
    <caption class="sr-only">
        <?= $caption ?? I18N::translate('Sources') ?>
    </caption>

    <thead>
        <tr>
            <th><?= I18N::translate('Title') ?></th>
            <th><?= I18N::translate('Author') ?></th>
            <th><?= I18N::translate('Individuals') ?></th>
            <th><?= I18N::translate('Families') ?></th>
            <th><?= I18N::translate('Media objects') ?></th>
            <th><?= I18N::translate('Shared notes') ?></th>
            <th><?= I18N::translate('Last change') ?></th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($sources as $source) : ?>
            <tr class="<?= $source->isPendingDeletion() ? 'old' : ($source->isPendingAddition() ? 'new' : '') ?>">
                <!-- Title -->
                <td data-sort="<?= e($source->getSortName()) ?>">
                    <a href="<?= e($source->url()) ?>">
                        <?= $source->getFullName() ?>
                    </a>
                </td>

                <!-- Author -->
                <td>
                    <?= e($source->getFirstFact('AUTH') ? $source->getFirstFact('AUTH')->getValue() : '') ?>
                </td>

                <!-- Count of linked individuals -->
                <td class="center" data-sort="<?= $count_individuals[$source->getXref()] ?? 0 ?>">
                    <?= I18N::number($count_individuals[$source->getXref()] ?? 0) ?>
                </td>

                <!-- Count of linked families -->
                <td class="center" data-sort="<?= $count_families[$source->getXref()] ?? 0 ?>">
                    <?= I18N::number($count_families[$source->getXref()] ?? 0) ?>
                </td>

                <!-- Count of linked media objects -->
                <td class="center" data-sort="<?= $count_media[$source->getXref()] ?? 0 ?>">
                    <?= I18N::number($count_media[$source->getXref()] ?? 0) ?>
                </td>

                <!-- Count of linked notes -->
                <td class="center" data-sort="<?= $count_notes[$source->getXref()] ?? 0 ?>">
                    <?= I18N::number($count_notes[$source->getXref()] ?? 0) ?>
                </td>

                <!-- Last change -->
                <td data-sort="<?= $source->lastChangeTimestamp(true) ?>">
                    <?= $source->lastChangeTimestamp() ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
