<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?php if ($individual === null): ?>
	<?= FontAwesome::decorativeIcon($class) ?>
<?php else: ?>
	<?= FontAwesome::linkIcon($class, I18N::translate('Compact tree of %s', strip_tags($individual->getFullName())), ['href' => route('compact-tree', ['xref' => $individual->getXref(), 'ged' => $individual->getTree()->getName()])]) ?>
<?php endif ?>
