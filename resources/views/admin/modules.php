<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Filter; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>
<?php use Fisharebest\Webtrees\Module\ModuleBlockInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleChartInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleConfigInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleMenuInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleReportInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleSidebarInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleTabInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleThemeInterface; ?>

<?= View::make('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<?php foreach ($deleted_modules as $module_name): ?>
	<div class="alert alert-warning" role="alert">
		<form action="<?= Html::escape(route('admin-control-panel')) ?>" class="form-inline" method="POST">
			<?= Filter::getCsrf() ?>
			<input type="hidden" name="route" value="admin-delete-module-settings">
			<input type="hidden" name="module_name" value="<?= $module_name ?>">
			<?= I18N::translate('Preferences exist for the module “%s”, but this module no longer exists.', $module_name) ?>
			<button type="submit" class="btn btn-secondary text-wrap">
				<?= I18N::translate('Delete the preferences for this module.') ?>
			</button>
			</form>
	</div>
<?php endforeach ?>

<form action="<?= Html::escape(route('admin-update-module-status')) ?>" method="POST">
	<input type="hidden" name="route" value="admin-update-module-status">
	<?= Filter::getCsrf() ?>
	<table class="table table-bordered table-hover table-sm table-module-administration" data-info="false" data-paging="false" data-state-save="true">
		<caption class="sr-only">
			<?= I18N::translate('Module administration') ?>
		</caption>
		<thead>
			<tr>
				<th>
					<?= I18N::translate('Module') ?>
				</th>
				<th>
					<?= I18N::translate('Enabled') ?>
				</th>
				<th class="d-none d-sm-table-cell" data-orderable="false">
					<?= I18N::translate('Description') ?>
				</th>
				<th class="d-none d-sm-table-cell">
					<a href="<?= Html::escape(route('admin-menus')) ?>">
						<?= I18N::translate('Menus') ?>
					</a>
				</th>
				<th class="d-none d-sm-table-cell">
					<a href="<?= Html::escape(route('admin-tabs')) ?>">
						<?= I18N::translate('Tabs') ?>
					</a>
				</th>
				<th class="d-none d-sm-table-cell">
					<a href="<?= Html::escape(route('admin-sidebars')) ?>">
						<?= I18N::translate('Sidebars') ?>
					</a>
				</th>
				<th class="d-none d-sm-table-cell">
					<a href="<?= Html::escape(route('admin-blocks')) ?>">
						<?= I18N::translate('Blocks') ?>
					</a>
				</th>
				<th class="d-none d-sm-table-cell">
					<a href="<?= Html::escape(route('admin-charts')) ?>">
						<?= I18N::translate('Charts') ?>
					</a>
				</th>
				<th class="d-none d-sm-table-cell">
					<a href="<?= Html::escape(route('admin-reports')) ?>">
						<?= I18N::translate('Reports') ?>
					</a>
				</th>
				<th class="d-none">
					<?= I18N::translate('Themes') ?>
				</th>
				<th class="d-sm-none" data-orderable="false">
					<?= I18N::translate('Type') ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($modules as $module_name => $module): ?>
				<tr>
					<th scope="row" data-sort="<?= $module->getTitle() ?>">
						<?php if ($module instanceof ModuleConfigInterface): ?>
							<a href="<?= Html::escape($module->getConfigLink()) ?>">
								<?= $module->getTitle() ?> <i class="fa fa-cogs"></i>
							</a>
						<?php else: ?>
							<?= $module->getTitle() ?>
						<?php endif ?>
					</th>
					<td class="text-center" data-sort="<?= $module_status[$module_name] ?>">
						<?= Bootstrap4::checkbox('', false, ['name' => 'status-' . $module->getName(), 'checked' => $module_status[$module_name] === 'enabled']) ?>
					</td>
					<td class="d-none d-sm-table-cell">
						<?= $module->getDescription() ?>
						<?php if (!in_array($module->getName(), $core_module_names)): ?>
							<br>
							<i class="fa fa-asterisk"></i>
							<?= I18N::translate('Custom module') ?>
							<?php if ($module::CUSTOM_VERSION): ?>
								- <?= I18N::translate('Version') ?> <?= $module::CUSTOM_VERSION ?>
							<?php endif ?>
							<?php if ($module::CUSTOM_WEBSITE): ?>
								- <a href="<?= $module::CUSTOM_WEBSITE ?>">
									<?= $module::CUSTOM_WEBSITE ?>
								</a>
							<?php endif ?>
						<?php endif ?>
					</td>
					<td class="text-center text-muted d-none d-sm-table-cell">
						<?php if ($module instanceof ModuleMenuInterface): ?>
							<?= FontAwesome::semanticIcon('menu', I18N::translate('Menu')) ?>
						<?php else: ?>
							-
						<?php endif ?>
					</td>
					<td class="text-center text-muted d-none d-sm-table-cell">
						<?php if ($module instanceof ModuleTabInterface): ?>
							<?= FontAwesome::semanticIcon('tab', I18N::translate('Taba')) ?>
						<?php else: ?>
							-
						<?php endif ?>
					</td>
					<td class="text-center text-muted d-none d-sm-table-cell">
						<?php if ($module instanceof ModuleSidebarInterface): ?>
							<?= FontAwesome::semanticIcon('sidebar', I18N::translate('Sidebar')) ?>
						<?php else: ?>
							-
						<?php endif ?>
					</td>
					<td class="text-center text-muted d-none d-sm-table-cell">
						<?php if ($module instanceof ModuleBlockInterface): ?>
							<?php if ($module->isUserBlock()): ?>
								<?= FontAwesome::semanticIcon('block-user', I18N::translate('My page')) ?>
							<?php endif ?>
							<?php if ($module->isUserBlock()): ?>
								<?= FontAwesome::semanticIcon('block-tree', I18N::translate('Home page')) ?>
							<?php endif ?>
						<?php else: ?>
							-
						<?php endif ?>
					</td>
					<td class="text-center text-muted d-none d-sm-table-cell">
						<?php if ($module instanceof ModuleChartInterface): ?>
							<?= FontAwesome::semanticIcon('chart', I18N::translate('Chart')) ?>
						<?php else: ?>
							-
						<?php endif ?>
					</td>
					<td class="text-center text-muted d-none d-sm-table-cell">
						<?php if ($module instanceof ModuleReportInterface): ?>
							<?= FontAwesome::semanticIcon('report', I18N::translate('Report')) ?>
						<?php else: ?>
							-
						<?php endif ?>
					</td>
					<td class="text-center text-muted d-none">
						<?php if ($module instanceof ModuleThemeInterface): ?>
							<?= FontAwesome::semanticIcon('theme', I18N::translate('Theme')) ?>
						<?php else: ?>
							-
						<?php endif ?>
					</td>
					<td class="text-center text-muted d-sm-none">
						<?php if ($module instanceof ModuleMenuInterface): ?>
							<?= FontAwesome::semanticIcon('menu', I18N::translate('Menu')) ?>
						<?php endif ?>
						<?php if ($module instanceof ModuleTabInterface): ?>
							<?= FontAwesome::semanticIcon('tab', I18N::translate('Taba')) ?>
						<?php endif ?>
						<?php if ($module instanceof ModuleSidebarInterface): ?>
							<?= FontAwesome::semanticIcon('sidebar', I18N::translate('Sidebar')) ?>
						<?php endif ?>
						<?php if ($module instanceof ModuleBlockInterface && $module->isUserBlock()): ?>
							<?= FontAwesome::semanticIcon('block-user', I18N::translate('My page')) ?>
						<?php endif ?>
						<?php if ($module instanceof ModuleBlockInterface && $module->isUserBlock()): ?>
							<?= FontAwesome::semanticIcon('block-tree', I18N::translate('Home page')) ?>
						<?php endif ?>
						<?php if ($module instanceof ModuleChartInterface): ?>
							<?= FontAwesome::semanticIcon('chart', I18N::translate('Chart')) ?>
						<?php endif ?>
						<?php if ($module instanceof ModuleReportInterface): ?>
							<?= FontAwesome::semanticIcon('report', I18N::translate('Report')) ?>
						<?php endif ?>
						<?php if ($module instanceof ModuleThemeInterface): ?>
							<?= FontAwesome::semanticIcon('theme', I18N::translate('Theme')) ?>
						<?php endif ?>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
	<button class="btn btn-primary" type="submit">
		<i class="fa fa-check"></i>
		<?= I18N::translate('save') ?></button>
</form>

<script>
  document.addEventListener("DOMContentLoaded", function(event) {
    $(".table-module-administration").dataTable(<?= json_encode(I18N::datatablesI18N()) ?>);
  }
</script>
