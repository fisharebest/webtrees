<?php use Fisharebest\Webtrees\I18N; ?>

<?php foreach ($all_trees as $tree) : ?>
    <tr class="<?= $changes[$tree->getTreeId()] ? 'danger' : '' ?>">
        <th scope="row">
            <a href="<?= e(route('tree-page', ['ged' => $tree->getName()])) ?>">
                <?= e($tree->getName()) ?>
                -
                <?= e($tree->getTitle()) ?>
            </a>
        </th>
        <td>
            <a href="<?= e(route('admin-trees', ['ged' => $tree->getName()])) ?>" title="<?= I18N::translate('Preferences') ?>">
                <?= view('icons/preferences') ?>
            </a>
        </td>
        <td class="text-right">
            <?php if ($changes[$tree->getTreeId()]) : ?>
                <a href="<?= e(route('show-pending', ['ged' => $tree->getName(), 'url' => route('admin-control-panel')])) ?>">
                    <?= I18N::number($changes[$tree->getTreeId()]) ?>
                    <span class="sr-only"><?= I18N::translate('Pending changes') ?> <?= e($tree->getTitle()) ?></span>
                </a>
            <?php else : ?>
                -
            <?php endif ?>
        </td>
        <td class="d-none d-sm-table-cell text-right">
            <?php if ($individuals[$tree->getTreeId()]) : ?>
                <a href="<?= e(route('individual-list', ['ged' => $tree->getName()])) ?>">
                    <?= I18N::number($individuals[$tree->getTreeId()]) ?>
                </a>
            <?php else : ?>
                -
            <?php endif ?>
        </td>
        <td class="d-none d-lg-table-cell text-right">
            <?php if ($families[$tree->getTreeId()]) : ?>
                <a href="<?= e(route('family-list', ['ged' => $tree->getName()])) ?>">
                    <?= I18N::number($families[$tree->getTreeId()]) ?>
                </a>
            <?php else : ?>
                -
            <?php endif ?>
        </td>
        <td class="d-none d-sm-table-cell text-right">
            <?php if ($sources[$tree->getTreeId()]) : ?>
                <a href="<?= e(route('source-list', ['ged' => $tree->getName()])) ?>">
                    <?= I18N::number($sources[$tree->getTreeId()]) ?>
                </a>
            <?php else : ?>
                -
            <?php endif ?>
        </td>
        <td class="d-none d-lg-table-cell text-right">
            <?php if ($repositories[$tree->getTreeId()]) : ?>
                <a href="<?= e(route('repository-list', ['ged' => $tree->getName()])) ?>">
                    <?= I18N::number($repositories[$tree->getTreeId()]) ?>
                </a>
            <?php else : ?>
                -
            <?php endif ?>
        </td>
        <td class="d-none d-sm-table-cell text-right">
            <?php if ($media[$tree->getTreeId()]) : ?>
                <a href="<?= e(route('media-list', ['ged' => $tree->getName()])) ?>">
                    <?= I18N::number($media[$tree->getTreeId()]) ?>
                </a>
            <?php else : ?>
                -
            <?php endif ?>
        </td>
        <td class="d-none d-lg-table-cell text-right">
            <?php if ($notes[$tree->getTreeId()]) : ?>
                <a href="<?= e(route('note-list', ['ged' => $tree->getName()])) ?>">
                    <?= I18N::number($media[$tree->getTreeId()]) ?>
                </a>
            <?php else : ?>
                -
            <?php endif ?>
        </td>
    </tr>
<?php endforeach ?>
