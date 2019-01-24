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

namespace Fisharebest\Webtrees;

use Closure;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use stdClass;
use Throwable;

/**
 * Functions for managing and maintaining modules.
 */
class Module
{
    // We use a list of core modules to help identify custom ones.
    public const CORE_MODULES = [
        'GEDFact_assistant',
        'ahnentafel_report',
        'ancestors_chart',
        'batch_update',
        'bdm_report',
        'birth_report',
        'calendar_menu',
        'cemetery_report',
        'change_report',
        'charts',
        'charts_menu',
        'ckeditor',
        'clippings',
        'compact-chart',
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
        'lists_menu',
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
        'reports_menu',
        'review_changes',
        'search_menu',
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
        'trees_menu',
        'upcoming_events',
        'user_blog',
        'user_favorites',
        'user_messages',
        'user_welcome',
        'yahrzeit',
    ];

    // Some types of module have different access levels in different trees.
    private const COMPONENTS = [
        'block'   => ModuleBlockInterface::class,
        'chart'   => ModuleChartInterface::class,
        'menu'    => ModuleMenuInterface::class,
        'report'  => ModuleReportInterface::class,
        'sidebar' => ModuleSidebarInterface::class,
        'tab'     => ModuleTabInterface::class,
    ];

    /**
     * All modules.
     *
     * @return Collection|ModuleInterface[]
     */
    public static function all(): Collection
    {
        $pattern   = WT_ROOT . Webtrees::MODULES_PATH . '*/module.php';
        $filenames = glob($pattern);

        return app('cache.array')->rememberForever('all_modules', function () use ($filenames): Collection {
            // Modules have a default status, order etc.
            // We can override these from database settings.
            $module_info = DB::table('module')
                ->get()
                ->mapWithKeys(function (stdClass $row): array {
                    return [$row->module_name => $row];
                });

            return (new Collection($filenames))
                ->filter(function (string $filename): bool {
                    // Module names with dots break things.
                    return !Str::contains(basename(dirname($filename)), '.');
                })
                ->map(function (string $filename) use ($module_info): ?ModuleInterface {
                    try {
                        $module_name = basename(dirname($filename));
                        $module      = self::load($filename);

                        if ($module instanceof ModuleInterface) {
                            $module->setName($module_name);
                        }

                        $info = $module_info->get($module_name);

                        if ($info instanceof stdClass) {
                            $module->setEnabled($info->status === 'enabled');

                            if ($module instanceof ModuleMenuInterface && $info->menu_order !== null) {
                                $module->setMenuOrder((int) $info->menu_order);
                            }

                            if ($module instanceof ModuleSidebarInterface && $info->sidebar_order !== null) {
                                $module->setSidebarOrder((int) $info->sidebar_order);
                            }

                            if ($module instanceof ModuleTabInterface && $info->tab_order !== null) {
                                $module->setTabOrder((int) $info->tab_order);
                            }
                        } else {
                            DB::table('module')->insert(['module_name' => $module_name]);
                        }

                        return $module;
                    } catch (Throwable $ex) {
                        $message = '<pre>' . e($ex->getMessage()) . "\n" . e($ex->getTraceAsString()) . '</pre>';
                        FlashMessages::addMessage($message, 'danger');

                        return null;
                    }
                })
                ->filter()
                ->sort(self::moduleSorter());
        });
    }

    /**
     * Load a module in a separate scope, to prevent it from modifying local variables.
     *
     * @param string $filename
     *
     * @return mixed
     */
    private static function load(string $filename)
    {
        return include $filename;
    }

    /**
     * A function to sort modules by name
     *
     * @return Closure
     */
    private static function moduleSorter(): Closure
    {
        return function (ModuleInterface $x, ModuleInterface $y): int {
            return I18N::strcasecmp($x->title(), $y->title());
        };
    }

    /**
     * A function to sort menus
     *
     * @return Closure
     */
    private static function menuSorter(): Closure
    {
        return function (ModuleMenuInterface $x, ModuleMenuInterface $y): int {
            return $x->getMenuOrder() <=> $y->getMenuOrder();
        };
    }

    /**
     * A function to sort menus
     *
     * @return Closure
     */
    private static function sidebarSorter(): Closure
    {
        return function (ModuleSidebarInterface $x, ModuleSidebarInterface $y): int {
            return $x->getSidebarOrder() <=> $y->getSidebarOrder();
        };
    }

    /**
     * A function to sort menus
     *
     * @return Closure
     */
    private static function tabSorter(): Closure
    {
        return function (ModuleTabInterface $x, ModuleTabInterface $y): int {
            return $x->getTabOrder() <=> $y->getTabOrder();
        };
    }

    /**
     * Modules which (a) provide a specific function and (b) we have permission to see.
     *
     * @param string $component
     * @param Tree   $tree
     * @param User   $user
     *
     * @return Collection|ModuleBlockInterface[]|ModuleChartInterface[]|ModuleMenuInterface[]|ModuleReportInterface[]|ModuleSidebarInterface[]|ModuleTabInterface[]
     */
    public static function findByComponent(string $component, Tree $tree, User $user): Collection
    {
        $interface = self::COMPONENTS[$component];
        
        return self::findByInterface($interface)
            ->filter(function (ModuleInterface $module) use ($component, $tree, $user): bool {
                return $module->accessLevel($tree, $component) >= Auth::accessLevel($tree, $user);
            });
    }

    /**
     * All modules which provide a specific function.
     *
     * @param string $interface
     *
     * @return Collection|ModuleInterface[]
     */
    public static function findByInterface(string $interface): Collection
    {
        $modules = self::all()
            ->filter(function (ModuleInterface $module) use ($interface): bool {
                return $module->isEnabled() && $module instanceof $interface;
            });

        switch ($interface) {
            case ModuleMenuInterface::class:
                return $modules->sort(self::menuSorter());

            case ModuleSidebarInterface::class:
                return $modules->sort(self::sidebarSorter());

            case ModuleTabInterface::class:
                return $modules->sort(self::tabSorter());
        }

        return $modules;
    }

    /**
     * Find a specified module, if it is currently active.
     *
     * @param string $module_name
     *
     * @return ModuleInterface|null
     */
    public static function findByName(string $module_name): ?ModuleInterface
    {
        return self::all()
            ->filter(function (ModuleInterface $module) use ($module_name): bool {
                return $module->isEnabled() && $module->name() === $module_name;
            })
            ->first();
    }

    /**
     * Find a specified module, if it is currently active.
     *
     * @param string $class_name
     *
     * @return ModuleInterface|null
     */
    public static function findByClass(string $class_name): ?ModuleInterface
    {
        return self::all()
            ->filter(function (ModuleInterface $module) use ($class_name): bool {
                return $module->isEnabled() && $module instanceof $class_name;
            })
            ->first();
    }
}
