<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-trees', ['ged' => $tree->getName()]) => I18N::translate('Manage family trees '), $title]]) ?>

<h1><?= $title ?></h1>

<?php if (!empty($xrefs)): ?>
	<p>
		<?= I18N::translate('In a family tree, each record has an internal reference number (called an “XREF”) such as “F123” or “R14”.') ?>
	</p>
	<p>
		<?= I18N::plural(/* I18N: An XREF is the identification number used in GEDCOM files. */ 'The two family trees have %1$s record which uses the same “XREF”.', 'The two family trees have %1$s records which use the same “XREF”.', count($xrefs), count($xrefs)) ?>
	</p>
	<p>
		<?= I18N::translate('You must renumber the records in one of the trees before you can merge them.') ?>
	</p>
	<p>
		<a class="current" href="<?= e(route('admin-trees-renumber', ['ged' => $tree1->getName()])) ?>">
			<?= I18N::translate('Renumber family tree') ?> — <?= e($tree1->getTitle()) ?>
		</a>
	</p>
	<p>
		<a class="current" href="<?= e(route('admin-trees-renumber', ['ged' => $tree2->getName()])) ?>">
			<?= I18N::translate('Renumber family tree') ?> — <?= e($tree2->getTitle()) ?>
		</a>
	</p>
<?php endif ?>

<form action="<?= e(route('admin-trees-merge')) ?>" method="post">
	<?= csrf_field() ?>
	<p class="form-inline">
		<?= I18N::translate(/* I18N: Copy all the records from [family tree 1] into [family tree 2] */
			'Copy all the records from %1$s into %2$s.',
			Bootstrap4::select($tree_list, $tree1 ? $tree1->getName() : '', ['name' => 'tree1_name']),
			Bootstrap4::select($tree_list, $tree2 ? $tree2->getName() : '', ['name' => 'tree2_name'])
		) ?>
	</p>

	<button type="submit" class="btn btn-primary">
		<i class="fas fa-check" aria-hidden="true"></i>
		<?= I18N::translate('continue') ?>
	</button>
</form>
