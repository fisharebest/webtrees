<?php namespace Fisharebest\Webtrees; ?>
<?= I18N::translate('Hello administrator…') ?>

<?= /* I18N: %1$s is a real-name, %2$s is a username, %3$s is an email address */ I18N::translate(
'A new user (%1$s) has requested an account (%2$s) and verified an email address (%3$s).', Html::escape($user->getRealName()), Html::escape($user->getUserName()), Html::escape($user->getEmail())) ?>

<?= I18N::translate('You need to review the account details.') ?>

<?= Html::url(WT_BASE_URL . 'admin_users.php', ['action' => 'edit', 'user_id' => $user->getUserId()]) ?>

<?= /* I18N: You need to: */ I18N::translate('Set the status to “approved”.') ?>
<?= /* I18N: You need to: */ I18N::translate('Set the access level for each tree.') ?>
<?= /* I18N: You need to: */ I18N::translate('Link the user account to an individual.') ?>
