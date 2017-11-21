<?php namespace Fisharebest\Webtrees; ?>
<p>
	<?= I18N::translate('Hello %s…', e($recipient->getRealName())) ?>
</p>

<p>
	<?= /* I18N: %s is a person's name */ I18N::translate('%s sent you the following message.', e($sender->getRealName())) ?>
</p>

<hr>

<p>
	<?= nl2br(e($message), false) ?>
</p>

<hr>

<p>
	<?= I18N::translate('This message was sent while viewing the following URL: ') ?>
	<?= e($url) ?>
</p>
