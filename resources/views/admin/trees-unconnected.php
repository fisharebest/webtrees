<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-trees', ['ged' => $tree->getName()]) => I18N::translate('Manage family trees '), $title]]) ?>

<h1><?= $title ?></h1>

<form class="form-inline">
	<input type="hidden" name="route" value="admin-trees-unconnected"">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">
			<?= Bootstrap4::checkbox(I18N::translate('Include associates'), true, ['checked' => $associates, 'name' => 'associates']) ?>
	<button type="submit">
		<?= I18N::translate('update') ?>
	</button>
</form>

<p><?= I18N::translate('These groups of individuals are not related to %s.', $root->getFullName()) ?></p>

<?php foreach ($individual_groups as $group): ?>
	<h2><?= I18N::plural('%s individual', '%s individuals', count($group), I18N::number(count($group))) ?></h2>
	<ul>
		<?php foreach ($group as $individual): ?>
			<li>
				<a href="<?= e($individual->url()) ?>"><?= $individual->getFullName() ?></a>
			</li>
		<?php endforeach ?>
	</ul>
<?php endforeach ?>
