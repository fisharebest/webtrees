<?php use Fisharebest\Webtrees\I18N; ?>

<table class="list_table">
    <thead>
    <tr>
        <th class="list_label" colspan="<?= $colcount ?>">
            <i class="icon-place"></i>
			<?= I18N::translate('Place hierarchy') ?>
        </th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <?php foreach ($columns as $column): ?>
                <td class="list_value_wrap">
                    <?php foreach ($column as $item): ?>
                        <div>
                            <a href="<?= $item->getURL() ?>"><?= $item->getPlaceName() ?></a>
                        </div>
                    <?php endforeach ?>
                </td>
            <?php endforeach ?>
        </tr>
    </tbody>
	<?php if ($showfooter): ?>
        <tfoot>
            <tr>
                <td class="list_label" colspan="<?= $colcount ?>">
                    <?= I18N::translate('View all records found in this place') ?>
                </td>
            </tr>
            <tr>
                <td class="list_value wt-footer-content" colspan="<?= $colcount ?>">
                    <a class="formField" href= <?= e(route('place-hierarchy',
                            ['ged' => $tree->getName(), 'parent' => $parent, 'action' => 'hierarchy-e']
                        )
                    ) ?>><?= $place->getPlaceName() ?></a>
                </td>
            </tr>
        </tfoot>
	<?php endif ?>
</table>

