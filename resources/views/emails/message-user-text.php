<?php namespace Fisharebest\Webtrees; ?>
<?= I18N::translate('Hello %s…', $recipient->getRealName()) ?>

<?= /* I18N: %s is a person's name */ I18N::translate('%s sent you the following message.', $sender->getRealName()) ?>

------------------------------------------------------------
<?= $message ?>

------------------------------------------------------------

<?= I18N::translate('This message was sent while viewing the following URL: ') ?>
<?= $url ?>
