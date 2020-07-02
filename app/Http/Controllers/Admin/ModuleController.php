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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleAnalyticsInterface;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleDataFixInterface;
use Fisharebest\Webtrees\Module\ModuleFooterInterface;
use Fisharebest\Webtrees\Module\ModuleHistoricEventsInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Module\ModuleListInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_flip;
use function in_array;
use function redirect;
use function route;
use function view;

/**
 * Controller for configuring the modules.
 */
class ModuleController extends AbstractAdminController
{
    /** @var ModuleService */
    private $module_service;

    /** @var TreeService */
    private $tree_service;

    /**
     * ModuleController constructor.
     *
     * @param ModuleService $module_service
     * @param TreeService   $tree_service
     */
    public function __construct(ModuleService $module_service, TreeService $tree_service)
    {
        $this->module_service = $module_service;
        $this->tree_service   = $tree_service;
    }

    /**
     * Show the administrator a list of modules.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function list(ServerRequestInterface $request): ResponseInterface
    {
        return $this->viewResponse('admin/modules', [
            'title'           => I18N::translate('All modules'),
            'modules'         => $this->module_service->all(true),
            'deleted_modules' => $this->module_service->deletedModules(),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function listAnalytics(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleAnalyticsInterface::class,
            I18N::translate('Tracking and analytics'),
            I18N::translate('If you use one of the following tracking and analytics services, webtrees can add the tracking codes automatically.')
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function listBlocks(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleBlockInterface::class,
            view('icons/block') . I18N::translate('Blocks'),
            ''
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function listCharts(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleChartInterface::class,
            view('icons/chart') . I18N::translate('Charts'),
            ''
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function listDataFixes(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleDataFixInterface::class,
            view('icons/data-fix') . I18N::translate('Data fixes'),
            ''
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function listFooters(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleFooterInterface::class,
            view('icons/footer') . I18N::translate('Footers'),
            ''
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function listHistory(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleHistoricEventsInterface::class,
            view('icons/history') . I18N::translate('Historic events'),
            ''
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function listLanguages(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleLanguageInterface::class,
            view('icons/language') . I18N::translate('Languages'),
            ''
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function listLists(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleListInterface::class,
            view('icons/list') . I18N::translate('Lists'),
            ''
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function listMenus(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleMenuInterface::class,
            view('icons/menu') . I18N::translate('Menus'),
            ''
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function listReports(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleReportInterface::class,
            view('icons/report') . I18N::translate('Reports'),
            ''
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function listSidebars(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleSidebarInterface::class,
            view('icons/sidebar') . I18N::translate('Sidebars'),
            ''
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function listTabs(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleTabInterface::class,
            view('icons/tab') . I18N::translate('Tabs'),
            ''
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function listThemes(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleThemeInterface::class,
            view('icons/theme') . I18N::translate('Themes'),
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
        $trees        = $this->tree_service->all();
        $modules      = $this->module_service->findByInterface($interface, true, true);
        $uses_access  = in_array($interface, $this->module_service->componentsWithAccess(), true);
        $uses_sorting = in_array($interface, $this->module_service->componentsWithOrder(), true);

        $level_text = Auth::accessLevelNames();

        $access_summary = $modules
            ->mapWithKeys(static function (ModuleInterface $module) use ($interface, $level_text, $trees): array {
                $access_levels = $trees
                    ->map(static function ($tree) use ($interface, $module): int {
                        return $module->accessLevel($tree, $interface);
                    })
                    ->uniqueStrict()
                    ->values()
                    ->map(static function (int $level) use ($level_text): string {
                        return $level_text[$level];
                    })
                    ->all();

                return [$module->name() => $access_levels];
            })
            ->all();

        return $this->viewResponse('admin/components', [
            'description'    => $description,
            'interface'      => $interface,
            'modules'        => $modules,
            'title'          => $title,
            'trees'          => $this->tree_service->all(),
            'uses_access'    => $uses_access,
            'uses_sorting'   => $uses_sorting,
            'access_summary' => $access_summary,
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
        $params = (array) $request->getParsedBody();

        $modules = $this->module_service->all(true);

        foreach ($modules as $module) {
            $new_status = (bool) ($params['status-' . $module->name()] ?? false);
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

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

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

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

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

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

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

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

        return redirect(route('charts'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateDataFixes(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->findByInterface(ModuleDataFixInterface::class, true);

        $this->updateStatus($modules, $request);

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

        return redirect(route('languages'));
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

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

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

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

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

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

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

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

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

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

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

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

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

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

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

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

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

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

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
        $params = (array) $request->getParsedBody();

        $order = (array) ($params['order'] ?? []);
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
        $params = (array) $request->getParsedBody();

        foreach ($modules as $module) {
            $enabled = (bool) ($params['status-' . $module->name()] ?? false);

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
        $params = (array) $request->getParsedBody();

        $trees = $this->tree_service->all();

        foreach ($modules as $module) {
            foreach ($trees as $tree) {
                $key          = 'access-' . $module->name() . '-' . $tree->id();
                $access_level = (int) ($params[$key] ?? 0);

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
        $params = (array) $request->getParsedBody();

        $module_name = $params['module_name'];

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
