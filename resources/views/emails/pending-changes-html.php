<?php namespace Fisharebest\Webtrees; ?>
<p>
	<?= e(I18N::translate('Hello %s…', $user->getRealName())) ?>
</p>

<p>
	<?= I18N::translate('There are pending changes for you to moderate.') ?>
</p>

<ul>
	<li>
		<a href="<?= e(Html::url(WT_BASE_URL . 'edit_changes.php', ['ged' => $tree->getName()])) ?>">
			<?= e($tree->getTitle()) ?>
		</a>
	</li>
</ul>
