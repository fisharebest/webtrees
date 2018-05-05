<?php use Fisharebest\Webtrees\I18N; ?>

<div class="row">
            <span class="col list_label">
                <i class="icon-place"></i>
	            <?= I18N::translate('Place list') ?>
            </span>
</div>
<div class="row">
	<?php foreach ($columns as $column): ?>
        <div class="list_value_wrap col">
			<?php foreach ($column as $item): ?>
                <div>
                    <a href="<?= $item->getURL() ?>"><?= $item->getReverseName() ?></a>
                </div>
			<?php endforeach ?>
        </div>
	<?php endforeach ?>
</div>

