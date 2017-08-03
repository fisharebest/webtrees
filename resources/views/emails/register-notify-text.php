<?php namespace Fisharebest\Webtrees; ?>
<?= I18N::translate('Hello administrator…') ?>

<?= /* I18N: %s is a server name/URL */I18N::translate('A prospective user has registered with webtrees at %s.', WT_BASE_URL . ' ' . $tree->getTitleHtml()) ?>

<?= I18N::translate('Username') ?> - <?= Html::escape($user->getUserName()) ?>
<?= I18N::translate('Real name') ?> - <?= Html::escape($user->getRealName()) ?>
<?= I18N::translate('Email address') ?> - <?= Html::escape($user->getEmail()) ?>
<?= I18N::translate('Comments') ?> - <?= Html::escape($comments) ?>

<?= I18N::translate('The user has been sent an email with the information necessary to confirm the access request.') ?>

<?= I18N::translate('You will be informed by email when this prospective user has confirmed the request. You can then complete the process by activating the username. The new user will not be able to sign in until you activate the account.') ?>
