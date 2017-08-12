<?php namespace Fisharebest\Webtrees; ?>
<?= I18N::translate('Hello %s…', $sender->getRealName()) ?>

<?= I18N::translate('You sent the following message to a webtrees user:') ?><?= $recipient->getRealName() ?>

------------------------------------------------------------
<?= $message ?>
------------------------------------------------------------

<?= I18N::translate('This message was sent while viewing the following URL: ') ?>
<?= $url ?>
