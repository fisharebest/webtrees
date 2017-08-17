<?php namespace Fisharebest\Webtrees; ?>
<p>
	<?= I18N::translate('Hello %s…', Html::escape($recipient->getRealName())) ?>
</p>

<p>
	<?= /* I18N: %s is a person's name */ I18N::translate('%s sent you the following message.', Html::escape($sender->getRealName())) ?>
</p>

<hr>

<p>
	<?= nl2br(Html::escape($message), false) ?>
</p>

<hr>

<p>
	<?= I18N::translate('This message was sent while viewing the following URL: ') ?>
	<?= Html::escape($url) ?>
</p>
