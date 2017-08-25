<?php namespace Fisharebest\Webtrees; ?>
<p>
	<?= I18N::translate('Hello %s…', Html::escape($user->getRealName())) ?>
</p>

<p>
	<?= /* I18N: %1$s is the site URL and %2$s is an email address */I18N::translate('You (or someone claiming to be you) has requested an account at %1$s using the email address %2$s.', Html::escape(WT_BASE_URL . ' ' . $tree->getTitle()), HTML::escape($user->getEmail())) ?>
</p>

<p>
	<?= I18N::translate('Follow this link to verify your email address.') ?>
</p>

<p>
	<a href="<?= Html::escape(Html::url(WT_LOGIN_URL, ['username' => $user->getUserName(), 'user_hashcode' => $user->getPreference('reg_hashcode'), 'action' => 'userverify', 'ged' => $tree->getName()])) ?>">
		<?= Html::escape(Html::url(WT_LOGIN_URL, ['username' => $user->getUserName(), 'user_hashcode' => $user->getPreference('reg_hashcode'), 'action' => 'userverify', 'ged' => $tree->getName()])) ?>
	</a>
</p>

<p>
	<?= I18N::translate('If you didn’t request an account, you can just delete this message.') ?>
</p>
