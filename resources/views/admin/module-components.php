<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Filter; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Tree; ?>
<?php use Fisharebest\Webtrees\View; ?>
<?php use Fisharebest\Webtrees\Module\ModuleConfigInterface; ?>

<?= View::make('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-modules') => I18N::translate('Modules'), $title]]) ?>

<h1><?= $title ?></h1>

<form action="<?= e(route('admin-control-panel')) ?>" method="post">
	<input type="hidden" name="route" value="admin-update-module-access">
	<input type="hidden" name="component" value="<?= e($component) ?>">
	<?= Filter::getCsrf() ?>
	<table class="table table-bordered" class="row">
		<thead>
			<tr>
				<th class="col-sm-2"><?= $component_title ?></th>
				<th class="col-sm-4"><?= I18N::translate('Description') ?></th>
				<th class="col-sm-6"><?= I18N::translate('Access level') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($modules as $module_name => $module): ?>
				<tr>
					<td>
						<?php if ($module instanceof ModuleConfigInterface): ?>
							<a href="<?= e($module->getConfigLink()) ?>"><?= $module->getTitle() ?> <i class="fa fa-cogs"></i></a>
						<?php else: ?>
							<?= $module->getTitle() ?>
						<?php endif ?>
					</td>
					<td><?= $module->getDescription() ?></td>
					<td>
						<table class="table table-sm">
							<tbody>
								<?php foreach (Tree::getAll() as $tree): ?>
									<tr>
										<td>
											<?= $tree->getTitleHtml() ?>
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
		<i class="fa fa-check"></i>
		<?= I18N::translate('save') ?>
	</button>
</form>
