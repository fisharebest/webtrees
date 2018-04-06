<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Module\ModuleConfigInterface; ?>
<?php use Fisharebest\Webtrees\Tree; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-modules') => I18N::translate('Modules'), $title]]) ?>

<h1><?= $title ?></h1>

<form action="<?= e(route('admin-update-module-access')) ?>" method="post">
	<input type="hidden" name="component" value="<?= e($component) ?>">
	<?= csrf_field() ?>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th><?= $component_title ?></th>
				<th class="d-none d-sm-table-cell"><?= I18N::translate('Description') ?></th>
				<th><?= I18N::translate('Access level') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($modules as $module_name => $module): ?>
				<tr>
					<td>
						<?php if ($module instanceof ModuleConfigInterface): ?>
							<a href="<?= e($module->getConfigLink()) ?>"><?= $module->getTitle() ?> <i class="fas fa-cogs"></i></a>
						<?php else: ?>
							<?= $module->getTitle() ?>
						<?php endif ?>
					</td>
					<td class="d-none d-sm-table-cell">
						<?= $module->getDescription() ?>
					</td>
					<td>
						<table class="table table-sm">
							<tbody>
								<?php foreach (Tree::getAll() as $tree): ?>
									<tr>
										<td>
											<?= e($tree->getTitle()) ?>
										</td>
										<td>
											<?= Bootstrap4::select(FunctionsEdit::optionsAccessLevels(), $module->getAccessLevel($tree, $component), ['name' => 'access-' . $module->getName() . '-' . $tree->getTreeId()]) ?>
										</td>
									</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
	<button class="btn btn-primary" type="submit">
		<i class="fas fa-check"></i>
		<?= I18N::translate('save') ?>
	</button>
</form>
