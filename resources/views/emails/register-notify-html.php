<?php namespace Fisharebest\Webtrees; ?>
<p>
	<?= I18N::translate('Hello administrator…') ?>
</p>

<p>
	<?= /* I18N: %s is a server name/URL */I18N::translate('A prospective user has registered with webtrees at %s.', WT_BASE_URL . ' ' . $tree->getTitleHtml()) ?>
</p>

<dl>
	<dt><?= I18N::translate('Username') ?></dt>
	<dd><?= Html::escape($user->getUserName()) ?></dd>
	<dt><?= I18N::translate('Real name') ?></dt>
	<dd><?= Html::escape($user->getRealName()) ?></dd>
	<dt><?= I18N::translate('Email address') ?></dt>
	<dd><?= Html::escape($user->getEmail()) ?></dd>
	<dt><?= I18N::translate('Comments') ?></dt>
	<dd><?= Html::escape($comments) ?></dd>
</dl>

<p>
	<?= I18N::translate('The user has been sent an email with the information necessary to confirm the access request.') ?>
</p>

<p>
	<?= I18N::translate('You will be informed by email when this prospective user has confirmed the request. You can then complete the process by activating the username. The new user will not be able to sign in until you activate the account.') ?>
</p>
