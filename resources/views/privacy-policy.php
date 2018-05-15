<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<p>
	<?= I18N::translate('The data in this website has been collected for the purposes of genealogical research.') ?>
</p>

<h3>
	<?= I18N::translate('Cookies') ?>
</h3>

<p>
	<?= I18N::translate('This site uses cookies to store your preferences on this site, such as the language you have selected.') ?>
</p>

<h3>
	<?= I18N::translate('Tracking and analytics') ?>
</h3>

<?php if ($uses_analytics): ?>
<p>
	<?= I18N::translate('This site does not use any third-party tracking or analytics services.') ?>
</p>
<?php else: ?>
<?php endif ?>

