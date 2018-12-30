<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

namespace Fisharebest\Webtrees;

use Exception;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;

/**
 * Functions for managing and maintaining modules.
 */
class Module
{
    // We use a list of core modules to help identify custom ones.
    const CORE_MODULES = [
        'GEDFact_assistant',
        'ahnentafel_report',
        'ancestors_chart',
        'batch_update',
        'bdm_report',
        'birth_report',
        'cemetery_report',
        'change_report',
        'charts',
        'ckeditor',
        'clippings',
        'compact_tree_chart',
        'death_report',
        'descendancy',
        'descendancy_chart',
        'descendancy_report',
        'extra_info',
        'fact_sources',
        'family_book_chart',
        'family_group_report',
        'family_nav',
        'fan_chart',
        'faq',
        'gedcom_block',
        'gedcom_favorites',
        'gedcom_news',
        'gedcom_stats',
        'hourglass_chart',
        'html',
        'individual_ext_report',
        'individual_report',
        'lifespans_chart',
        'lightbox',
        'logged_in',
        'login_block',
        'marriage_report',
        'media',
        'missing_facts_report',
        'notes',
        'occupation_report',
        'pedigree-map',
        'pedigree_chart',
        'pedigree_report',
        'personal_facts',
        'places',
        'random_media',
        'recent_changes',
        'relationships_chart',
        'relative_ext_report',
        'relatives',
        'review_changes',
        'sitemap',
        'sources_tab',
        'statistics_chart',
        'stories',
        'theme_select',
        'timeline_chart',
        'todays_events',
        'todo',
        'top10_givnnames',
        'top10_pageviews',
        'top10_surnames',
        'tree',
        'upcoming_events',
        'user_blog',
        'user_favorites',
        'user_messages',
        'user_welcome',
        'yahrzeit',
    ];

    /** @var ModuleInterface[] */
    private static $modules = [];

    /**
     * Load a module from a file.  Since third-party modules may declare classes or functions,
     * we must only load each file once.
     *
     * @param string $file
     *
     * @return ModuleInterface|null
     */
    private static function loadModule($file)
    {
        if (!array_key_exists($file, self::$modules)) {
            self::$modules[$file] = null;
            try {
                $module = include $file;
                if ($module instanceof ModuleInterface) {
                    self::$modules[$file] = $module;
                }
            } catch (Exception $ex) {
                Log::addErrorLog($ex->getMessage());
            }
        }

        return self::$modules[$file];
    }

    /**
     * Get a list of all active (enabled) modules.
     *
     * @return ModuleInterface[]
     */
    private static function getActiveModules(): array
    {
        /** @var ModuleInterface[] - Only query the database once. */
        static $modules;

        if ($modules === null) {
            $module_names = DB::table('module')
                ->where('status', '=', 'enabled')
                ->pluck('module_name');

            $modules = [];
            foreach ($module_names as $module_name) {
                $module = self::loadModule(WT_ROOT . Webtrees::MODULES_PATH . $module_name . '/module.php');
                if ($module instanceof ModuleInterface) {
                    $modules[$module->getName()] = $module;
                } else {
                    // The module has been deleted or is broken? Disable it.
                    Log::addConfigurationLog("Module {$module_name} is missing or broken - disabling it. ", null);
                    DB::table('module')
                        ->where('module_name', '=', $module_name)
                        ->update(['status' => 'disabled']);
                }
            }
        }

        return $modules;
    }

    /**
     * Which column to sort by when fetching components.
     *
     * @param string $component
     *
     * @return string
     */
    private static function componentSortColumn(string $component): string
    {
        if ($component === 'menu' || $component === 'sidebar' || $component === 'tab') {
            return $component . '_order';
        } else {
            return 'module_name';
        }
    }

    /**
     * Get a list of modules which (a) provide a specific function and (b) we have permission to see.
     * We cannot currently use auto-loading for modules, as there may be user-defined
     * modules about which the auto-loader knows nothing.
     *
     * @param Tree   $tree
     * @param string $component The type of module, such as "tab", "report" or "menu"
     *
     * @return ModuleBlockInterface[]|ModuleChartInterface[]|ModuleMenuInterface[]|ModuleReportInterface[]|ModuleSidebarInterface[]|ModuleTabInterface[]|ModuleThemeInterface[]
     */
    private static function getActiveModulesByComponent(Tree $tree, $component): array
    {
        $sort_column = self::componentSortColumn($component);

        $module_names = DB::table('module')
            ->join('module_privacy', 'module.module_name', '=', 'module_privacy.module_name')
            ->where('gedcom_id', '=', $tree->id())
            ->where('component', '=', $component)
            ->where('status', '=', 'enabled')
            ->where('access_level', '>=', Auth::accessLevel($tree))
            ->orderBy('module.' . $sort_column)
            ->pluck('module.module_name');

        $array = [];
        foreach ($module_names as $module_name) {
            $interface = '\Fisharebest\Webtrees\Module\Module' . ucfirst($component) . 'Interface';
            $module    = self::getModuleByName($module_name);
            if ($module instanceof $interface) {
                $array[$module_name] = $module;
            }
        }

        // The order of menus/sidebars/tabs is defined in the database. Others are sorted by name.
        if ($component !== 'menu' && $component !== 'sidebar' && $component !== 'tab') {
            uasort($array, function (ModuleInterface $x, ModuleInterface $y): int {
                return I18N::strcasecmp($x->getTitle(), $y->getTitle());
            });
        }

        return $array;
    }

    /**
     * Get a list of all modules, enabled or not, which provide a specific function.
     * We cannot currently use auto-loading for modules, as there may be user-defined
     * modules about which the auto-loader knows nothing.
     *
     * @param string $component The type of module, such as "tab", "report" or "menu"
     *
     * @return ModuleInterface[]
     */
    public static function getAllModulesByComponent($component): array
    {
        $sort_column = self::componentSortColumn($component);

        $module_names = DB::table('module')
            ->orderBy($sort_column)
            ->pluck('module_name');

        $array = [];
        foreach ($module_names as $module_name) {
            $interface = '\Fisharebest\Webtrees\Module\Module' . ucfirst($component) . 'Interface';
            $module    = self::getModuleByName($module_name);
            if ($module instanceof $interface) {
                $array[$module_name] = $module;
            }
        }

        // The order of menus/sidebars/tabs is defined in the database. Others are sorted by name.
        if ($component !== 'menu' && $component !== 'sidebar' && $component !== 'tab') {
            uasort($array, function (ModuleInterface $x, ModuleInterface $y): int {
                return I18N::strcasecmp($x->getTitle(), $y->getTitle());
            });
        }

        return $array;
    }

    /**
     * Get a list of modules which (a) provide a block and (b) we have permission to see.
     *
     * @param Tree $tree
     *
     * @return ModuleBlockInterface[]
     */
    public static function getActiveBlocks(Tree $tree): array
    {
        return self::getActiveModulesByComponent($tree, 'block');
    }

    /**
     * Get a list of modules which (a) provide a chart and (b) we have permission to see.
     *
     * @param Tree $tree
     *
     * @return ModuleChartInterface[]
     */
    public static function getActiveCharts(Tree $tree): array
    {
        return self::getActiveModulesByComponent($tree, 'chart');
    }

    /**
     * Get a list of modules which (a) provide a chart and (b) we have permission to see.
     *
     * @param Tree   $tree
     * @param string $module
     *
     * @return bool
     */
    public static function isActiveChart(Tree $tree, $module): bool
    {
        return array_key_exists($module, self::getActiveModulesByComponent($tree, 'chart'));
    }

    /**
     * Get a list of module names which have configuration options.
     *
     * @return ModuleConfigInterface[]
     */
    public static function configurableModules(): array
    {
        $modules = array_filter(self::getInstalledModules('disabled'), function (ModuleInterface $module): bool {
            return $module instanceof ModuleConfigInterface;
        });

        // Exclude disabled modules
        $enabled_modules = DB::table('module')
            ->where('status', '=', 'enabled')
            ->pluck('module_name')
            ->all();

        return array_filter($modules, function (ModuleConfigInterface $module) use ($enabled_modules): bool {
            return in_array($module->getName(), $enabled_modules);
        });
    }

    /**
     * Get a list of modules which (a) provide a menu and (b) we have permission to see.
     *
     * @param Tree $tree
     *
     * @return ModuleMenuInterface[]
     */
    public static function getActiveMenus(Tree $tree): array
    {
        return self::getActiveModulesByComponent($tree, 'menu');
    }

    /**
     * Get a list of modules which (a) provide a report and (b) we have permission to see.
     *
     * @param Tree $tree
     *
     * @return ModuleReportInterface[]
     */
    public static function getActiveReports(Tree $tree): array
    {
        return self::getActiveModulesByComponent($tree, 'report');
    }

    /**
     * Get a list of modules which (a) provide a sidebar and (b) we have permission to see.
     *
     * @param Tree $tree
     *
     * @return ModuleSidebarInterface[]
     */
    public static function getActiveSidebars(Tree $tree): array
    {
        return self::getActiveModulesByComponent($tree, 'sidebar');
    }

    /**
     * Get a list of modules which (a) provide a tab and (b) we have permission to see.
     *
     * @param Tree $tree
     *
     * @return ModuleTabInterface[]
     */
    public static function getActiveTabs(Tree $tree): array
    {
        return self::getActiveModulesByComponent($tree, 'tab');
    }

    /**
     * Get a list of modules which (a) provide a theme and (b) we have permission to see.
     *
     * @param Tree $tree
     *
     * @return ModuleThemeInterface[]
     */
    public static function getActiveThemes(Tree $tree): array
    {
        return self::getActiveModulesByComponent($tree, 'theme');
    }

    /**
     * Find a specified module, if it is currently active.
     *
     * @param string $module_name
     *
     * @return ModuleInterface|null
     */
    public static function getModuleByName($module_name)
    {
        $modules = self::getActiveModules();
        if (array_key_exists($module_name, $modules)) {
            return $modules[$module_name];
        }

        return null;
    }

    /**
     * For newly discovered modules, set the access level for all trees.
     *
     * @param ModuleInterface $module
     * @param string          $component
     */
    private static function setAllAccessLevels(ModuleInterface $module, string $component): void
    {
        (new Builder(DB::connection()))->from('module_privacy')->insertUsing(
            ['module_name', 'gedcom_id', 'component', 'access_level'],
            function (Builder $query) use ($module, $component) {
                $query->select([
                    DB::raw(DB::connection()->getPdo()->quote($module->getName())),
                    'gedcom_id',
                    DB::raw(DB::connection()->getPdo()->quote($component)),
                    DB::raw(DB::connection()->getPdo()->quote($module->defaultAccessLevel())),
                ])->from('gedcom');
            }
        );
    }

    /**
     * Scan the source code to find a list of all installed modules.
     * During setup, new modules need a status of “enabled”.
     * In admin->modules, new modules need status of “disabled”.
     *
     * @param string $default_status
     *
     * @return ModuleInterface[]
     */
    public static function getInstalledModules($default_status): array
    {
        $modules = [];

        foreach (glob(WT_ROOT . Webtrees::MODULES_PATH . '*/module.php') as $file) {
            try {
                $module = self::loadModule($file);
                if ($module instanceof ModuleInterface) {
                    $modules[$module->getName()] = $module;

                    $exists = DB::table('module')->where('module_name', '=', $module->getName())->pluck('module_name')->isNotEmpty();

                    if (!$exists) {
                        DB::table('module')->insert([
                            'module_name'   => $module->getName(),
                            'status'        => $default_status,
                            'menu_order'    => $module instanceof ModuleMenuInterface ? $module->defaultMenuOrder() : null,
                            'sidebar_order' => $module instanceof ModuleSidebarInterface ? $module->defaultSidebarOrder() : null,
                            'tab_order'     => $module instanceof ModuleTabInterface ? $module->defaultTabOrder() : null,
                        ]);

                        // Set the default privcy for this module. Note that this also sets it for the
                        // default family tree, with a gedcom_id of -1
                        if ($module instanceof ModuleMenuInterface) {
                            self::setAllAccessLevels($module, 'menu');
                        }
                        if ($module instanceof ModuleSidebarInterface) {
                            self::setAllAccessLevels($module, 'sidebar');
                        }
                        if ($module instanceof ModuleTabInterface) {
                            self::setAllAccessLevels($module, 'tab');
                        }
                        if ($module instanceof ModuleBlockInterface) {
                            self::setAllAccessLevels($module, 'block');
                        }
                        if ($module instanceof ModuleChartInterface) {
                            self::setAllAccessLevels($module, 'chart');
                        }
                        if ($module instanceof ModuleReportInterface) {
                            self::setAllAccessLevels($module, 'report');
                        }
                        if ($module instanceof ModuleThemeInterface) {
                            self::setAllAccessLevels($module, 'theme');
                        }
                    }
                }
            } catch (Exception $ex) {
                // Old or invalid module?
                Log::addErrorLog($ex->getMessage());
            }
        }

        return $modules;
    }

    /**
     * Set the access level for a tree/module/component.
     *
     * @param int             $tree_id
     * @param ModuleInterface $module
     * @param string          $component
     */
    private static function setAccessLevel(int $tree_id, ModuleInterface $module, string $component): void
    {
        DB::table('module_privacy')->updateOrInsert([
            'module_name' => $module->getName(),
            'gedcom_id'   => $tree_id,
            'component'   => $component,
        ], [
            'access_level' => $module->defaultAccessLevel(),
        ]);
    }

    /**
     * After creating a new family tree, we need to assign the default access
     * rights for each module.
     *
     * @param int $tree_id
     *
     * @return void
     */
    public static function setDefaultAccess($tree_id)
    {
        foreach (self::getInstalledModules('disabled') as $module) {
            if ($module instanceof ModuleMenuInterface) {
                self::setAccessLevel($tree_id, $module, 'menu');
            }
            if ($module instanceof ModuleSidebarInterface) {
                self::setAccessLevel($tree_id, $module, 'sidebar');
            }
            if ($module instanceof ModuleTabInterface) {
                self::setAccessLevel($tree_id, $module, 'tab');
            }
            if ($module instanceof ModuleBlockInterface) {
                self::setAccessLevel($tree_id, $module, 'block');
            }
            if ($module instanceof ModuleChartInterface) {
                self::setAccessLevel($tree_id, $module, 'chart');
            }
            if ($module instanceof ModuleReportInterface) {
                self::setAccessLevel($tree_id, $module, 'report');
            }
            if ($module instanceof ModuleThemeInterface) {
                self::setAccessLevel($tree_id, $module, 'theme');
            }
        }
    }
}
