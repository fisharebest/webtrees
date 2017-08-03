<?php namespace Fisharebest\Webtrees; ?>
<p>
	<?= Html::escape(I18N::translate('Hello %s…', $user->getRealName())) ?>
</p>

<p>
	<?= I18N::translate('A new password has been requested for your username.') ?>
</p>

<dl>
	<dt><?= I18N::translate('Username') ?></dt>
	<dd><?= Html::escape($user->getUserName())?></dd>
	<dt><?= I18N::translate('Password') ?></dt>
	<dd><?= Html::escape($new_password) ?></dd>
</dl>

<p>
	<a href="<?= Html::url(WT_BASE_URL . 'login.php', ['username' => $user->getUserName(), 'url' => 'edituser.php'], '&amp;') ?>">
		<?= I18N::translate('Sign in') ?>
	</a>
</p>

<p>
	<?= I18N::translate('After you have signed in, select the “My account” link under the “My pages” menu and fill in the password fields to change your password.') ?>
</p>
