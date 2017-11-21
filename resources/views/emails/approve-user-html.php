<?php namespace Fisharebest\Webtrees; ?>
<p>
	<?= I18N::translate('Hello %s…', e($user->getRealName())) ?>
</p>

<p>
	<?= I18N::translate('The administrator at the webtrees site %s has approved your application for an account. You may now sign in by accessing the following link: %s', e(WT_BASE_URL), e(WT_BASE_URL)) ?>
</p>
