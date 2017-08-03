<?php namespace Fisharebest\Webtrees; ?>
<p>
	<?= I18N::translate('Hello %s…', Html::escape($sender->getRealName())) ?>
</p>

<p>
	<?= I18N::translate('You sent the following message to a webtrees user:') ?><?= Html::escape($recipient->getRealName()) ?>
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
