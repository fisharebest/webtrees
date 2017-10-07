<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?php if ($individual === null): ?>
	<?= FontAwesome::decorativeIcon($class) ?>
<?php else: ?>
	<?= FontAwesome::linkIcon($class, I18N::translate('Compact tree of %s', strip_tags($individual->getFullName())), ['href' => Html::url('compact.php', ['ged' => $individual->getTree()->getName(), 'rootid' => $individual->getXref()])]) ?>
<?php endif ?>
