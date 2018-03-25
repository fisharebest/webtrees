<?php namespace Fisharebest\Webtrees; ?>
<p>
	<?= e(I18N::translate('Hello %s…', $user->getRealName())) ?>
</p>

<p>
	<?= I18N::translate('There are pending changes for you to moderate.') ?>
</p>

<ul>
	<li>
		<a href="<?= e(route('show-pending', ['ged' => $tree->getName()], true)) ?>">
			<?= e($tree->getTitle()) ?>
		</a>
	</li>
</ul>
