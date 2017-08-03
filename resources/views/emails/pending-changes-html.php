<?php namespace Fisharebest\Webtrees; ?>
<p>
	<?= Html::escape(I18N::translate('Hello %s…', $user->getRealName())) ?>
</p>

<p>
	<?= I18N::translate('There are pending changes for you to moderate.') ?>
</p>

<ul>
	<li>
		<a href="<?= Html::url(WT_BASE_URL . 'edit_changes.php', ['ged' => $tree->getName()], '&amp;') ?>">
			<?= Html::escape($tree->getTitle()) ?>
		</a>
	</li>
</ul>
