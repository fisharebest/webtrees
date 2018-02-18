<?php namespace Fisharebest\Webtrees; ?>
<p>
	<?= I18N::translate('Hello %s…', e($user->getRealName())) ?>
</p>

<p>
	<?= /* I18N: %1$s is the site URL and %2$s is an email address */I18N::translate('You (or someone claiming to be you) has requested an account at %1$s using the email address %2$s.', e(WT_BASE_URL), e($user->getEmail())) ?>
</p>

<p>
	<?= I18N::translate('Follow this link to verify your email address.') ?>
</p>

<p>
	<a href="<?= e(route('verify', ['username' => $user->getUserName(), 'token' => $user->getPreference('reg_hashcode'), 'ged' => $tree->getName()], true)) ?>">
		<?= e(route('verify', ['username' => $user->getUserName(), 'token' => $user->getPreference('reg_hashcode'), 'ged' => $tree->getName()], true)) ?>
	</a>
</p>

<p>
	<?= I18N::translate('If you didn’t request an account, you can just delete this message.') ?>
</p>
