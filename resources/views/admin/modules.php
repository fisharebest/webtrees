<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Module\ModuleBlockInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleChartInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleConfigInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleMenuInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleReportInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleSidebarInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleTabInterface; ?>
<?php use Fisharebest\Webtrees\Module\ModuleThemeInterface; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('components/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<?php foreach ($deleted_modules as $module_name) : ?>
    <div class="alert alert-warning" role="alert">
        <form action="<?= e(route('admin-delete-module-settings')) ?>" class="form-inline" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="module_name" value="<?= $module_name ?>">
            <?= I18N::translate('Preferences exist for the module “%s”, but this module no longer exists.', $module_name) ?>
            <button type="submit" class="btn btn-secondary text-wrap">
                <?= I18N::translate('Delete the preferences for this module.') ?>
            </button>
            </form>
    </div>
<?php endforeach ?>

<form action="<?= e(route('admin-update-module-status')) ?>" method="POST">
    <input type="hidden" name="route" value="admin-update-module-status">
    <?= csrf_field() ?>
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
                    <a href="<?= e(route('admin-menus')) ?>">
                        <?= I18N::translate('Menus') ?>
                    </a>
                </th>
                <th class="d-none d-sm-table-cell">
                    <a href="<?= e(route('admin-tabs')) ?>">
                        <?= I18N::translate('Tabs') ?>
                    </a>
                </th>
                <th class="d-none d-sm-table-cell">
                    <a href="<?= e(route('admin-sidebars')) ?>">
                        <?= I18N::translate('Sidebars') ?>
                    </a>
                </th>
                <th class="d-none d-sm-table-cell">
                    <a href="<?= e(route('admin-blocks')) ?>">
                        <?= I18N::translate('Blocks') ?>
                    </a>
                </th>
                <th class="d-none d-sm-table-cell">
                    <a href="<?= e(route('admin-charts')) ?>">
                        <?= I18N::translate('Charts') ?>
                    </a>
                </th>
                <th class="d-none d-sm-table-cell">
                    <a href="<?= e(route('admin-reports')) ?>">
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
            <?php foreach ($modules as $module_name => $module) : ?>
                <tr>
                    <th scope="row" data-sort="<?= $module->getTitle() ?>" dir="auto">
                        <?php if ($module instanceof ModuleConfigInterface) : ?>
                            <a href="<?= e($module->getConfigLink()) ?>">
                                <?= $module->getTitle() ?>
                            <?= view('icons/preferences') ?>
                            </a>
                        <?php else : ?>
                            <?= $module->getTitle() ?>
                        <?php endif ?>
                    </th>
                    <td class="text-center" data-sort="<?= $module_status[$module_name] ?>">
                        <?= Bootstrap4::checkbox('', false, ['name' => 'status-' . $module->getName(), 'checked' => $module_status[$module_name] === 'enabled']) ?>
                    </td>
                    <td class="d-none d-sm-table-cell">
                        <?= $module->getDescription() ?>
                        <?php if (!in_array($module->getName(), $core_module_names)) : ?>
                            <br>
                            <?= view('icons/warning') ?>
                            <?= I18N::translate('Custom module') ?>
                            <?php if ($module::CUSTOM_VERSION) : ?>
                                - <?= I18N::translate('Version') ?> <?= $module::CUSTOM_VERSION ?>
                            <?php endif ?>
                            <?php if ($module::CUSTOM_WEBSITE) : ?>
                                - <a href="<?= $module::CUSTOM_WEBSITE ?>">
                                    <?= $module::CUSTOM_WEBSITE ?>
                                </a>
                            <?php endif ?>
                        <?php endif ?>
                    </td>
                    <td class="text-center text-muted d-none d-sm-table-cell">
                        <?php if ($module instanceof ModuleMenuInterface) : ?>
                            <?= view('icons/menu') ?>
                            <span class="sr-only"><?= I18N::translate('Menu') ?></span>
                        <?php else : ?>
                            -
                        <?php endif ?>
                    </td>
                    <td class="text-center text-muted d-none d-sm-table-cell">
                        <?php if ($module instanceof ModuleTabInterface) : ?>
                            <?= view('icons/tab') ?>
                            <span class="sr-only"><?= I18N::translate('Tabs') ?></span>
                        <?php else : ?>
                            -
                        <?php endif ?>
                    </td>
                    <td class="text-center text-muted d-none d-sm-table-cell">
                        <?php if ($module instanceof ModuleSidebarInterface) : ?>
                            <?= view('icons/sidebar') ?>
                            <span class="sr-only"><?= I18N::translate('Sidebar') ?></span>
                        <?php else : ?>
                            -
                        <?php endif ?>
                    </td>
                    <td class="text-center text-muted d-none d-sm-table-cell">
                        <?php if ($module instanceof ModuleBlockInterface) : ?>
                            <?php if ($module->isUserBlock()) : ?>
                                <?= view('icons/block-user') ?>
                                <span class="sr-only"><?= I18N::translate('My page') ?></span>
                            <?php endif ?>
                            <?php if ($module->isUserBlock()) : ?>
                                <?= view('icons/block-tree') ?>
                                <span class="sr-only"><?= I18N::translate('Home page') ?></span>
                            <?php endif ?>
                        <?php else : ?>
                            -
                        <?php endif ?>
                    </td>
                    <td class="text-center text-muted d-none d-sm-table-cell">
                        <?php if ($module instanceof ModuleChartInterface) : ?>
                            <?= view('icons/chart') ?>
                            <span class="sr-only"><?= I18N::translate('Chart') ?></span>
                        <?php else : ?>
                            -
                        <?php endif ?>
                    </td>
                    <td class="text-center text-muted d-none d-sm-table-cell">
                        <?php if ($module instanceof ModuleReportInterface) : ?>
                            <?= view('icons/report') ?>
                            <span class="sr-only"><?= I18N::translate('Report') ?></span>
                        <?php else : ?>
                            -
                        <?php endif ?>
                    </td>
                    <td class="text-center text-muted d-none">
                        <?php if ($module instanceof ModuleThemeInterface) : ?>
                            <?= view('icons/theme') ?>
                            <span class="sr-only"><?= I18N::translate('Theme') ?></span>
                        <?php else : ?>
                            -
                        <?php endif ?>
                    </td>
                    <td class="text-center text-muted d-sm-none">
                        <?php if ($module instanceof ModuleMenuInterface) : ?>
                            <?= view('icons/menu') ?>
                            <span class="sr-only"><?= I18N::translate('Menu') ?></span>
                        <?php endif ?>
                        <?php if ($module instanceof ModuleTabInterface) : ?>
                            <?= view('icons/tab') ?>
                            <span class="sr-only"><?= I18N::translate('Tab') ?></span>
                        <?php endif ?>
                        <?php if ($module instanceof ModuleSidebarInterface) : ?>
                            <?= view('icons/sidebar') ?>
                            <span class="sr-only"><?= I18N::translate('Sidebar') ?></span>
                        <?php endif ?>
                        <?php if ($module instanceof ModuleBlockInterface && $module->isUserBlock()) : ?>
                            <?= view('icons/block-user') ?>
                            <span class="sr-only"><?= I18N::translate('My page') ?></span>
                        <?php endif ?>
                        <?php if ($module instanceof ModuleBlockInterface && $module->isUserBlock()) : ?>
                            <?= view('icons/block-tree') ?>
                            <span class="sr-only"><?= I18N::translate('Home page') ?></span>
                        <?php endif ?>
                        <?php if ($module instanceof ModuleChartInterface) : ?>
                            <?= view('icons/chart') ?>
                            <span class="sr-only"><?= I18N::translate('Chart') ?></span>
                        <?php endif ?>
                        <?php if ($module instanceof ModuleReportInterface) : ?>
                            <?= view('icons/report') ?>
                            <span class="sr-only"><?= I18N::translate('Report') ?></span>
                        <?php endif ?>
                        <?php if ($module instanceof ModuleThemeInterface) : ?>
                            <?= view('icons/theme') ?>
                            <span class="sr-only"><?= I18N::translate('Theme') ?></span>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <button class="btn btn-primary" type="submit">
        <?= view('icons/save') ?>
        <?= I18N::translate('save') ?></button>
</form>

<?php View::push('javascript') ?>
<script>
  'use strict';

  $(".table-module-administration").dataTable({<?= I18N::datatablesI18N() ?>});
</script>
<?php View::endpush() ?>
