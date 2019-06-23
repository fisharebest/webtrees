<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleAnalyticsInterface;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleFooterInterface;
use Fisharebest\Webtrees\Module\ModuleHistoricEventsInterface;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Module\ModuleListInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller for configuring the modules.
 */
class ModuleController extends AbstractAdminController
{
    private const COMPONENTS_WITH_ACCESS = [
        ModuleBlockInterface::class,
        ModuleChartInterface::class,
        ModuleListInterface::class,
        ModuleMenuInterface::class,
        ModuleReportInterface::class,
        ModuleSidebarInterface::class,
        ModuleTabInterface::class,
    ];

    private const COMPONENTS_WITH_SORT = [
        ModuleFooterInterface::class,
        ModuleMenuInterface::class,
        ModuleSidebarInterface::class,
        ModuleTabInterface::class,
    ];

    /**
     * @var ModuleService
     */
    private $module_service;

    /**
     * ModuleController constructor.
     *
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * Show the administrator a list of modules.
     *
     * @return ResponseInterface
     */
    public function list(): ResponseInterface
    {
        return $this->viewResponse('admin/modules', [
            'title'           => I18N::translate('All modules'),
            'modules'         => $this->module_service->all(true),
            'deleted_modules' => $this->module_service->deletedModules(),
        ]);
    }

    /**
     * @return ResponseInterface
     */
    public function listAnalytics(): ResponseInterface
    {
        return $this->listComponents(
            ModuleAnalyticsInterface::class,
            I18N::translate('Tracking and analytics'),
            I18N::translate('If you use one of the following tracking and analytics services, webtrees can add the tracking codes automatically.')
        );
    }

    /**
     * @return ResponseInterface
     */
    public function listBlocks(): ResponseInterface
    {
        return $this->listComponents(
            ModuleBlockInterface::class,
            I18N::translate('Blocks'),
            ''
        );
    }

    /**
     * @return ResponseInterface
     */
    public function listCharts(): ResponseInterface
    {
        return $this->listComponents(
            ModuleChartInterface::class,
            I18N::translate('Charts'),
            ''
        );
    }

    /**
     * @return ResponseInterface
     */
    public function listFooters(): ResponseInterface
    {
        return $this->listComponents(
            ModuleFooterInterface::class,
            I18N::translate('Footers'),
            ''
        );
    }

    /**
     * @return ResponseInterface
     */
    public function listHistory(): ResponseInterface
    {
        return $this->listComponents(
            ModuleHistoricEventsInterface::class,
            I18N::translate('Historic events'),
            ''
        );
    }

    /**
     * @return ResponseInterface
     */
    public function listLanguages(): ResponseInterface
    {
        return $this->listComponents(
            ModuleLanguageInterface::class,
            I18N::translate('Languages'),
            ''
        );
    }

    /**
     * @return ResponseInterface
     */
    public function listLists(): ResponseInterface
    {
        return $this->listComponents(
            ModuleListInterface::class,
            I18N::translate('Lists'),
            ''
        );
    }

    /**
     * @return ResponseInterface
     */
    public function listMenus(): ResponseInterface
    {
        return $this->listComponents(
            ModuleMenuInterface::class,
            I18N::translate('Menus'),
            ''
        );
    }

    /**
     * @return ResponseInterface
     */
    public function listReports(): ResponseInterface
    {
        return $this->listComponents(
            ModuleReportInterface::class,
            I18N::translate('Reports'),
            ''
        );
    }

    /**
     * @return ResponseInterface
     */
    public function listSidebars(): ResponseInterface
    {
        return $this->listComponents(
            ModuleSidebarInterface::class,
            I18N::translate('Sidebars'),
            ''
        );
    }

    /**
     * @return ResponseInterface
     */
    public function listTabs(): ResponseInterface
    {
        return $this->listComponents(
            ModuleTabInterface::class,
            I18N::translate('Tabs'),
            ''
        );
    }

    /**
     * @return ResponseInterface
     */
    public function listThemes(): ResponseInterface
    {
        return $this->listComponents(
            ModuleThemeInterface::class,
            I18N::translate('Themes'),
            ''
        );
    }

    /**
     * @param string $interface
     * @param string $title
     * @param string $description
     *
     * @return ResponseInterface
     */
    private function listComponents(string $interface, string $title, string $description): ResponseInterface
    {
        $uses_access  = in_array($interface, self::COMPONENTS_WITH_ACCESS, true);
        $uses_sorting = in_array($interface, self::COMPONENTS_WITH_SORT, true);

        return $this->viewResponse('admin/components', [
            'description'  => $description,
            'interface'    => $interface,
            'modules'      => $this->module_service->findByInterface($interface, true, true),
            'title'        => $title,
            'trees'        => Tree::all(),
            'uses_access'  => $uses_access,
            'uses_sorting' => $uses_sorting,
        ]);
    }

    /**
     * Update the enabled/disabled status of the modules.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->all(true);

        foreach ($modules as $module) {
            $new_status = (bool) ($request->getParsedBody()['status-' . $module->name()] ?? false);
            $old_status = $module->isEnabled();

            if ($new_status !== $old_status) {
                DB::table('module')
                    ->where('module_name', '=', $module->name())
                    ->update(['status' => $new_status ? 'enabled' : 'disabled']);

                if ($new_status) {
                    FlashMessages::addMessage(I18N::translate('The module “%s” has been enabled.', $module->title()), 'success');
                } else {
                    FlashMessages::addMessage(I18N::translate('The module “%s” has been disabled.', $module->title()), 'success');
                }
            }
        }

        return redirect(route('modules'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateAnalytics(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->findByInterface(ModuleAnalyticsInterface::class, true);

        $this->updateStatus($modules, $request);

        return redirect(route('analytics'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateBlocks(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->findByInterface(ModuleBlockInterface::class, true);

        $this->updateStatus($modules, $request);
        $this->updateAccessLevel($modules, ModuleBlockInterface::class, $request);

        return redirect(route('blocks'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateCharts(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->findByInterface(ModuleChartInterface::class, true);

        $this->updateStatus($modules, $request);
        $this->updateAccessLevel($modules, ModuleChartInterface::class, $request);

        return redirect(route('charts'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateFooters(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->findByInterface(ModuleFooterInterface::class, true);

        $this->updateStatus($modules, $request);
        $this->updateOrder($modules, 'footer_order', $request);

        return redirect(route('footers'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateHistory(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->findByInterface(ModuleHistoricEventsInterface::class, true);

        $this->updateStatus($modules, $request);

        return redirect(route('history'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateLanguages(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->findByInterface(ModuleLanguageInterface::class, true);

        $this->updateStatus($modules, $request);

        return redirect(route('languages'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateLists(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->findByInterface(ModuleListInterface::class, true);

        $this->updateStatus($modules, $request);
        $this->updateAccessLevel($modules, ModuleListInterface::class, $request);

        return redirect(route('lists'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateMenus(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->findByInterface(ModuleMenuInterface::class, true);

        $this->updateStatus($modules, $request);
        $this->updateOrder($modules, 'menu_order', $request);
        $this->updateAccessLevel($modules, ModuleMenuInterface::class, $request);

        return redirect(route('menus'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateReports(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->findByInterface(ModuleReportInterface::class, true);

        $this->updateStatus($modules, $request);
        $this->updateAccessLevel($modules, ModuleReportInterface::class, $request);

        return redirect(route('reports'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateSidebars(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->findByInterface(ModuleSidebarInterface::class, true);

        $this->updateStatus($modules, $request);
        $this->updateOrder($modules, 'sidebar_order', $request);
        $this->updateAccessLevel($modules, ModuleSidebarInterface::class, $request);

        return redirect(route('sidebars'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateTabs(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->findByInterface(ModuleTabInterface::class, true);

        $this->updateStatus($modules, $request);
        $this->updateOrder($modules, 'tab_order', $request);
        $this->updateAccessLevel($modules, ModuleTabInterface::class, $request);

        return redirect(route('tabs'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateThemes(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->findByInterface(ModuleThemeInterface::class, true);

        $this->updateStatus($modules, $request);

        return redirect(route('themes'));
    }

    /**
     * Update the access levels of the modules.
     *
     * @param Collection             $modules
     * @param string                 $column
     * @param ServerRequestInterface $request
     *
     * @return void
     */
    private function updateOrder(Collection $modules, string $column, ServerRequestInterface $request): void
    {
        $order = (array) ($request->getParsedBody()['order'] ?? []);
        $order = array_flip($order);

        foreach ($modules as $module) {
            DB::table('module')
                ->where('module_name', '=', $module->name())
                ->update([
                    $column => $order[$module->name()] ?? 0,
                ]);
        }
    }

    /**
     * Update the access levels of the modules.
     *
     * @param Collection             $modules
     * @param ServerRequestInterface $request
     *
     * @return void
     */
    private function updateStatus(Collection $modules, ServerRequestInterface $request): void
    {
        foreach ($modules as $module) {
            $enabled = (bool) ($request->getParsedBody()['status-' . $module->name()] ?? false);

            if ($enabled !== $module->isEnabled()) {
                DB::table('module')
                    ->where('module_name', '=', $module->name())
                    ->update(['status' => $enabled ? 'enabled' : 'disabled']);

                if ($enabled) {
                    $message = I18N::translate('The module “%s” has been enabled.', $module->title());
                } else {
                    $message = I18N::translate('The module “%s” has been disabled.', $module->title());
                }

                FlashMessages::addMessage($message, 'success');
            }
        }
    }

    /**
     * Update the access levels of the modules.
     *
     * @param Collection             $modules
     * @param string                 $interface
     * @param ServerRequestInterface $request
     *
     * @return void
     */
    private function updateAccessLevel(Collection $modules, string $interface, ServerRequestInterface $request): void
    {
        $trees = Tree::all();

        foreach ($modules as $module) {
            foreach ($trees as $tree) {
                $key          = 'access-' . $module->name() . '-' . $tree->id();
                $access_level = (int) ($request->getParsedBody()[$key] ?? 0);

                if ($access_level !== $module->accessLevel($tree, $interface)) {
                    DB::table('module_privacy')->updateOrInsert([
                        'module_name' => $module->name(),
                        'gedcom_id'   => $tree->id(),
                        'interface'   => $interface,
                    ], [
                        'access_level' => $access_level,
                    ]);
                }
            }
        }
    }

    /**
     * Delete the database settings for a deleted module.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function deleteModuleSettings(ServerRequestInterface $request): ResponseInterface
    {
        $module_name = $request->getParsedBody()['module_name'];

        DB::table('block_setting')
            ->join('block', 'block_setting.block_id', '=', 'block.block_id')
            ->join('module', 'block.module_name', '=', 'module.module_name')
            ->where('module.module_name', '=', $module_name)
            ->delete();

        DB::table('block')
            ->join('module', 'block.module_name', '=', 'module.module_name')
            ->where('module.module_name', '=', $module_name)
            ->delete();

        DB::table('module_setting')
            ->where('module_name', '=', $module_name)
            ->delete();

        DB::table('module_privacy')
            ->where('module_name', '=', $module_name)
            ->delete();

        DB::table('module')
            ->where('module_name', '=', $module_name)
            ->delete();

        FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been deleted.', $module_name), 'success');

        return redirect(route('modules'));
    }
}
