<?php namespace Fisharebest\Webtrees; ?>
<p>
	<?= e(I18N::translate('Hello %s…', $user->getRealName())) ?>
</p>

<p>
	<?= I18N::translate('A new password has been requested for your username.') ?>
</p>

<dl>
	<dt><?= I18N::translate('Username') ?></dt>
	<dd><?= e($user->getUserName())?></dd>
	<dt><?= I18N::translate('Password') ?></dt>
	<dd><?= e($new_password) ?></dd>
</dl>

<p>
	<a href="<?= e(route('login', ['username' => $user->getUserName(), 'url' => route('my-account', [])], true)) ?>">
		<?= I18N::translate('Sign in') ?>
	</a>
</p>

<p>
	<?= I18N::translate('After you have signed in, select the “My account” link under the “My pages” menu and fill in the password fields to change your password.') ?>
</p>
