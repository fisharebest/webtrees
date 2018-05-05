<?php use Fisharebest\Webtrees\I18N; ?>

<div class="<?= $col_class ?> list_table">
    <div class="row">
        <span class="col list_label">
            <i class="icon-place"></i>
            <?= I18N::translate('Place hierarchy') ?>
        </span>
    </div>
    <div class="row">
        <?php foreach ($columns as $column): ?>
            <div class="col list_value_wrap">
                <?php foreach ($column as $item): ?>
                    <div>
                        <a href="<?= $item->getURL() ?>"><?= $item->getPlaceName() ?></a>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endforeach ?>
    </div>
</div>
