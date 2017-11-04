<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= Bootstrap4::breadcrumbs([Html::url('admin.php', ['route' => 'admin-control-panel']) => I18N::translate('Control panel')], $title) ?>

<h1><?= $title ?></h1>

<h2>
	<?= I18N::translate('PHP information') ?>
</h2>

<div class="php-info" dir="ltr">
	<?= $phpinfo ?>
</div>


<h2>
	<?= I18N::translate('MySQL variables') ?>
</h2>
<dl>
	<?php foreach ($mysql_variables as $variable => $value): ?>
		<dt><?= Html::escape($variable) ?></dt>
		<dd><?= Html::escape($value) ?></dd>
	<?php endforeach ?>
</dl>
