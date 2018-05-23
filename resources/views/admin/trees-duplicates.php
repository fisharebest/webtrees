<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-trees') => I18N::translate('Manage family trees'), $title]]) ?>

<h1><?= $title ?></h1>

<?php foreach ($duplicates as $category => $records): ?>

	<h2><?= $category ?></h2>

	<?php if (!empty($records)): ?>
		<ul>
			<?php foreach ($records as $duplicates): ?>
				<li>
					<?= $duplicates[0]->getFullName() ?>
					<?php foreach ($duplicates as $record): ?>
						—
						<a href="<?= e($record->url()) ?>">
							<?= $record->getXref() ?>
						</a>
					<?php endforeach ?>
					<?php if (count($duplicates) === 2): ?>
						—
						<a href="<?= e(route('merge-records', ['ged' => $tree->getName(), 'xref1' => $duplicates[0]->getXref(), 'xref2' => $duplicates[1]->getXref()])) ?>">
							<?= I18N::translate('Merge') ?>
						</a>
					<?php endif ?>
				</li>
			<?php endforeach ?>
		</ul>
	<?php else: ?>
		<p><?= I18N::translate('No duplicates have been found.') ?></p>
	<?php endif ?>
<?php endforeach ?>
