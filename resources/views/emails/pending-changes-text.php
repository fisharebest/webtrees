<?php namespace Fisharebest\Webtrees; ?>

<?= I18N::translate('Hello %s…', $user->getRealName()) ?>

<?= I18N::translate('There are pending changes for you to moderate.') ?>

<?= $tree->getTitle() ?> - <?= route('show-pending', ['ged' => $tree->getName()], true) ?>
