<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-modules') => I18N::translate('Modules'), $title]]) ?>

<h1><?= $title ?></h1>
<p>
	<?= I18N::translate('Searching for all possible relationships can take a lot of time in complex trees.') ?>
</p>

<form method="post">
	<?= csrf_field() ?>
	<?php foreach ($all_trees as $tree): ?>
		<h2><?= e($tree->getTitle()) ?></h2>
		<div class="row form-group">
			<label class="col-sm-3 col-form-label" for="relationship-ancestors-<?= $tree->getTreeId() ?>">
				<?= /* I18N: Configuration option */
				I18N::translate('Relationships') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::select($ancestors_options, $tree->getPreference('RELATIONSHIP_ANCESTORS', $default_ancestors), ['id' => 'relationship-ancestors-' . $tree->getTreeId(), 'name' => 'relationship-ancestors-' . $tree->getTreeId()]) ?>
			</div>
		</div>

		<fieldset class="form-group">
			<div class="row">
				<legend class="col-form-label col-sm-3">
					<?= /* I18N: Configuration option */
					I18N::translate('How much recursion to use when searching for relationships') ?>
				</legend>
				<div class="col-sm-9">
					<?= Bootstrap4::radioButtons('relationship-recursion-' . $tree->getTreeId(), $recursion_options, $tree->getPreference('RELATIONSHIP_RECURSION', $default_recursion), true) ?>
				</div>
			</div>
		</fieldset>
	<?php endforeach ?>

	<div class="row form-group">
		<div class="offset-sm-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<i class="fas fa-check"></i>
				<?= I18N::translate('save') ?>
			</button>
		</div>
	</div>
</form>
