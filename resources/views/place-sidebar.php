<?php use Fisharebest\Webtrees\I18N; ?>

<div class="offset-1 w-75 d-table">
    <div class="d-table-row">
        <div class="d-table-cell label">
			<?php if ($showlink): ?>
                <a href="<?= $place->getURL() ?>">
					<?= $place->getPlaceName() ?>
                </a>
			<?php else: ?>
				<?= $place->getPlaceName() ?>
			<?php endif ?>
        </div>
        <div class="d-table-cell text-right">
			<?php if ($flag): ?>
                <img src='<?= $flag ?>'>
			<?php endif ?>
        </div>
    </div>

    <div class="d-table-row">
        <div class="d-table-cell">
			<?= I18N::translate('Individuals') ?>
        </div>
        <div class="d-table-cell text-right">
			<?= $stats['INDI'] ?>
        </div>
    </div>

    <div class="d-table-row">
        <div class="d-table-cell">
			<?= I18N::translate('Families') ?>
        </div>
        <div class="d-table-cell text-right">
			<?= $stats['FAM'] ?>
        </div>
    </div>
</div>
