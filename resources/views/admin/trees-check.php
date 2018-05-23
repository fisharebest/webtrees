<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-trees') => I18N::translate('Manage family trees'), $title]]) ?>

<h1><?= $title ?></h1>

<ul class="list-group">
	<li class="list-group-item">
		<strong><?= I18N::translate('GEDCOM errors') ?></strong>
	</li>

	<?php foreach ($errors as $error): ?>
		<li class="list-group-item list-group-item-danger"><?= $error ?></li>
	<?php endforeach ?>

	<?php foreach ($warnings as $warnings): ?>
		<li class="list-group-item list-group-item-warning"><?= $warnings ?></li>
	<?php endforeach ?>

	<?php if (empty($errors) && empty($warnings)): ?>
		<li class="list-group-item">', I18N::translate('No errors have been found.'), '</li>
	<?php endif ?>
</ul>
