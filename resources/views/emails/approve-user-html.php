<?php namespace Fisharebest\Webtrees; ?>
<p>
	<?= I18N::translate('Hello %s…', Html::escape($user->getRealName())) ?>
</p>

<p>
	<?= I18N::translate('The administrator at the webtrees site %s has approved your application for an account. You may now sign in by accessing the following link: %s', Html::escape(WT_BASE_URL), Html::escape(WT_BASE_URL)) ?>
</p>
