<?php use Fisharebest\Webtrees\I18N; ?>

<p>
	<?= I18N::translate('The <b>name</b> field contains the individual’s full name, as they would have spelled it or as it was recorded. This is how it will be displayed on screen. It uses standard genealogy annotations to identify different parts of the name.') ?>
</p>

<ul>
	<li>
		<?= I18N::translate('The surname is enclosed by slashes: <%s>John Paul /Smith/<%s>', 'b', '/b') ?>
	</li>
	<li>
		<?= I18N::translate('If the surname is unknown, use empty slashes: <%s>Mary //<%s>', 'b', '/b') ?>
	</li>
	<li>
		<?= I18N::translate('If an individual has two separate surnames, both should be enclosed by slashes: <%s>José Antonio /Gómez/ /Iglesias/<%s>', 'b', '/b') ?>
	</li>
	<li>
		<?= I18N::translate('If an individual does not have a surname, no slashes are needed: <%s>Jón Einarsson<%s>', 'b', '/b') ?>
	</li>
	<li>
		<?= I18N::translate('If an individual was not known by their first given name, the preferred name should be indicated with an asterisk: <%s>John Paul* /Smith/<%s>', 'b', '/b') ?>
	</li>
	<li>
		<?= I18N::translate('If an individual was known by a nickname which is not part of their formal name, it should be enclosed by quotation marks. For example, <%s>John &quot;Nobby&quot; /Clark/<%s>.', 'b', '/b') ?>
	</li>
</ul>
