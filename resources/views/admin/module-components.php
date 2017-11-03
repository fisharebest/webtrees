<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Filter; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Tree; ?>
<?php use Fisharebest\Webtrees\View; ?>
<?php use Fisharebest\Webtrees\Module\ModuleConfigInterface; ?>

<?= View::make('admin/breadcrumbs', ['links' => ['admin.php' => I18N::translate('Control panel'), Html::url('admin.php', ['route' => 'admin-modules']) => I18N::translate('Modules'), $page_title]]) ?>

<h1><?= $page_title ?></h1>

<form action="admin.php" method="post">
	<input type="hidden" name="route" value="update-module-access">
	<input type="hidden" name="component" value="<?= Html::escape($component) ?>">
	<?= Filter::getCsrf() ?>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th class="col-xs-2"><?= $component_title ?></th>
				<th class="col-xs-5"><?= I18N::translate('Description') ?></th>
				<th class="col-xs-5"><?= I18N::translate('Access level') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($modules as $module_name => $module): ?>
				<tr>
					<td class="col-xs-2">
						<?php if ($module instanceof ModuleConfigInterface): ?>
							<a href="<?= Html::escape($module->getConfigLink()) ?>"><?= $module->getTitle() ?> <i class="fa fa-cogs"></i></a>
						<?php else: ?>
							<?= $module->getTitle() ?>
						<?php endif ?>
					</td>
					<td class="col-xs-5"><?= $module->getDescription() ?></td>
					<td class="col-xs-5">
						<table class="table">
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
