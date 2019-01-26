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
use Fisharebest\Webtrees\Module\AhnentafelReportModule;
use Fisharebest\Webtrees\Module\AlbumModule;
use Fisharebest\Webtrees\Module\AncestorsChartModule;
use Fisharebest\Webtrees\Module\BatchUpdateModule;
use Fisharebest\Webtrees\Module\BingWebmasterToolsModule;
use Fisharebest\Webtrees\Module\BirthDeathMarriageReportModule;
use Fisharebest\Webtrees\Module\BirthReportModule;
use Fisharebest\Webtrees\Module\CalendarMenuModule;
use Fisharebest\Webtrees\Module\CemeteryReportModule;
use Fisharebest\Webtrees\Module\CensusAssistantModule;
use Fisharebest\Webtrees\Module\ChangeReportModule;
use Fisharebest\Webtrees\Module\ChartsBlockModule;
use Fisharebest\Webtrees\Module\ChartsMenuModule;
use Fisharebest\Webtrees\Module\CkeditorModule;
use Fisharebest\Webtrees\Module\ClippingsCartModule;
use Fisharebest\Webtrees\Module\CompactTreeChartModule;
use Fisharebest\Webtrees\Module\DeathReportModule;
use Fisharebest\Webtrees\Module\DescendancyChartModule;
use Fisharebest\Webtrees\Module\DescendancyModule;
use Fisharebest\Webtrees\Module\DescendancyReportModule;
use Fisharebest\Webtrees\Module\ExtraInformationModule;
use Fisharebest\Webtrees\Module\FactSourcesReportModule;
use Fisharebest\Webtrees\Module\FamilyBookChartModule;
use Fisharebest\Webtrees\Module\FamilyGroupReportModule;
use Fisharebest\Webtrees\Module\FamilyNavigatorModule;
use Fisharebest\Webtrees\Module\FamilyTreeFavoritesModule;
use Fisharebest\Webtrees\Module\FamilyTreeNewsModule;
use Fisharebest\Webtrees\Module\FamilyTreeStatisticsModule;
use Fisharebest\Webtrees\Module\FanChartModule;
use Fisharebest\Webtrees\Module\FrequentlyAskedQuestionsModule;
use Fisharebest\Webtrees\Module\GoogleAnalyticsModule;
use Fisharebest\Webtrees\Module\GoogleWebmasterToolsModule;
use Fisharebest\Webtrees\Module\HourglassChartModule;
use Fisharebest\Webtrees\Module\HtmlBlockModule;
use Fisharebest\Webtrees\Module\IndividualFactsTabModule;
use Fisharebest\Webtrees\Module\IndividualFamiliesReportModule;
use Fisharebest\Webtrees\Module\IndividualReportModule;
use Fisharebest\Webtrees\Module\InteractiveTreeModule;
use Fisharebest\Webtrees\Module\LifespansChartModule;
use Fisharebest\Webtrees\Module\ListsMenuModule;
use Fisharebest\Webtrees\Module\LoggedInUsersModule;
use Fisharebest\Webtrees\Module\LoginBlockModule;
use Fisharebest\Webtrees\Module\MarriageReportModule;
use Fisharebest\Webtrees\Module\MatomoAnalyticsModule;
use Fisharebest\Webtrees\Module\MediaTabModule;
use Fisharebest\Webtrees\Module\MissingFactsReportModule;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\NotesTabModule;
use Fisharebest\Webtrees\Module\OccupationReportModule;
use Fisharebest\Webtrees\Module\OnThisDayModule;
use Fisharebest\Webtrees\Module\PedigreeChartModule;
use Fisharebest\Webtrees\Module\PedigreeMapModule;
use Fisharebest\Webtrees\Module\PedigreeReportModule;
use Fisharebest\Webtrees\Module\PlacesModule;
use Fisharebest\Webtrees\Module\RecentChangesModule;
use Fisharebest\Webtrees\Module\RelatedIndividualsReportModule;
use Fisharebest\Webtrees\Module\RelationshipsChartModule;
use Fisharebest\Webtrees\Module\RelativesTabModule;
use Fisharebest\Webtrees\Module\ReportsMenuModule;
use Fisharebest\Webtrees\Module\ResearchTaskModule;
use Fisharebest\Webtrees\Module\ReviewChangesModule;
use Fisharebest\Webtrees\Module\SearchMenuModule;
use Fisharebest\Webtrees\Module\SiteMapModule;
use Fisharebest\Webtrees\Module\SlideShowModule;
use Fisharebest\Webtrees\Module\SourcesTabModule;
use Fisharebest\Webtrees\Module\StatcounterModule;
use Fisharebest\Webtrees\Module\StatisticsChartModule;
use Fisharebest\Webtrees\Module\StoriesModule;
use Fisharebest\Webtrees\Module\ThemeSelectModule;
use Fisharebest\Webtrees\Module\TimelineChartModule;
use Fisharebest\Webtrees\Module\TopGivenNamesModule;
use Fisharebest\Webtrees\Module\TopPageViewsModule;
use Fisharebest\Webtrees\Module\TopSurnamesModule;
use Fisharebest\Webtrees\Module\TreesMenuModule;
use Fisharebest\Webtrees\Module\UpcomingAnniversariesModule;
use Fisharebest\Webtrees\Module\UserFavoritesModule;
use Fisharebest\Webtrees\Module\UserJournalModule;
use Fisharebest\Webtrees\Module\UserMessagesModule;
use Fisharebest\Webtrees\Module\UserWelcomeModule;
use Fisharebest\Webtrees\Module\WelcomeBlockModule;
use Fisharebest\Webtrees\Module\YahrzeitModule;
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
    // Some types of module have different access levels in different trees.
    private const COMPONENTS = [
        'block'   => ModuleBlockInterface::class,
        'chart'   => ModuleChartInterface::class,
        'menu'    => ModuleMenuInterface::class,
        'report'  => ModuleReportInterface::class,
        'sidebar' => ModuleSidebarInterface::class,
        'tab'     => ModuleTabInterface::class,
    ];

    // Array keys are module names, and should match module names from earlier versions of webtrees.
    private const CORE_MODULES = [
        'GEDFact_assistant'      => CensusAssistantModule::class,
        'ahnentafel_report'      => AhnentafelReportModule::class,
        'ancestors_chart'        => AncestorsChartModule::class,
        'batch_update'           => BatchUpdateModule::class,
        'bdm_report'             => BirthDeathMarriageReportModule::class,
        'bing-webmaster-tools'   => BingWebmasterToolsModule::class,
        'birth_report'           => BirthReportModule::class,
        'calendar_menu'          => CalendarMenuModule::class,
        'cemetery_report'        => CemeteryReportModule::class,
        'change_report'          => ChangeReportModule::class,
        'charts'                 => ChartsBlockModule::class,
        'charts_menu'            => ChartsMenuModule::class,
        'ckeditor'               => CkeditorModule::class,
        'clippings'              => ClippingsCartModule::class,
        'compact-chart'          => CompactTreeChartModule::class,
        'death_report'           => DeathReportModule::class,
        'descendancy'            => DescendancyModule::class,
        'descendancy_chart'      => DescendancyChartModule::class,
        'descendancy_report'     => DescendancyReportModule::class,
        'extra_info'             => ExtraInformationModule::class,
        'fact_sources'           => FactSourcesReportModule::class,
        'family_book_chart'      => FamilyBookChartModule::class,
        'family_group_report'    => FamilyGroupReportModule::class,
        'family_nav'             => FamilyNavigatorModule::class,
        'fan_chart'              => FanChartModule::class,
        'faq'                    => FrequentlyAskedQuestionsModule::class,
        'gedcom_block'           => WelcomeBlockModule::class,
        'gedcom_favorites'       => FamilyTreeFavoritesModule::class,
        'gedcom_news'            => FamilyTreeNewsModule::class,
        'gedcom_stats'           => FamilyTreeStatisticsModule::class,
        'google-analytics'       => GoogleAnalyticsModule::class,
        'google-webmaster-tools' => GoogleWebmasterToolsModule::class,
        'hourglass_chart'        => HourglassChartModule::class,
        'html'                   => HtmlBlockModule::class,
        'individual_ext_report'  => IndividualFamiliesReportModule::class,
        'individual_report'      => IndividualReportModule::class,
        'lifespans_chart'        => LifespansChartModule::class,
        'lightbox'               => AlbumModule::class,
        'lists_menu'             => ListsMenuModule::class,
        'logged_in'              => LoggedInUsersModule::class,
        'login_block'            => LoginBlockModule::class,
        'marriage_report'        => MarriageReportModule::class,
        'matomo-analytics'       => MatomoAnalyticsModule::class,
        'media'                  => MediaTabModule::class,
        'missing_facts_report'   => MissingFactsReportModule::class,
        'notes'                  => NotesTabModule::class,
        'occupation_report'      => OccupationReportModule::class,
        'pedigree-map'           => PedigreeMapModule::class,
        'pedigree_chart'         => PedigreeChartModule::class,
        'pedigree_report'        => PedigreeReportModule::class,
        'personal_facts'         => IndividualFactsTabModule::class,
        'places'                 => PlacesModule::class,
        'random_media'           => SlideShowModule::class,
        'recent_changes'         => RecentChangesModule::class,
        'relationships_chart'    => RelationshipsChartModule::class,
        'relative_ext_report'    => RelatedIndividualsReportModule::class,
        'relatives'              => RelativesTabModule::class,
        'reports_menu'           => ReportsMenuModule::class,
        'review_changes'         => ReviewChangesModule::class,
        'search_menu'            => SearchMenuModule::class,
        'sitemap'                => SiteMapModule::class,
        'sources_tab'            => SourcesTabModule::class,
        'statcounter'            => StatcounterModule::class,
        'statistics_chart'       => StatisticsChartModule::class,
        'stories'                => StoriesModule::class,
        'theme_select'           => ThemeSelectModule::class,
        'timeline_chart'         => TimelineChartModule::class,
        'todays_events'          => OnThisDayModule::class,
        'todo'                   => ResearchTaskModule::class,
        'top10_givnnames'        => TopGivenNamesModule::class,
        'top10_pageviews'        => TopPageViewsModule::class,
        'top10_surnames'         => TopSurnamesModule::class,
        'tree'                   => InteractiveTreeModule::class,
        'trees_menu'             => TreesMenuModule::class,
        'upcoming_events'        => UpcomingAnniversariesModule::class,
        'user_blog'              => UserJournalModule::class,
        'user_favorites'         => UserFavoritesModule::class,
        'user_messages'          => UserMessagesModule::class,
        'user_welcome'           => UserWelcomeModule::class,
        'yahrzeit'               => YahrzeitModule::class,
    ];

    /**
     * All core modules in the system.
     *
     * @return Collection
     */
    private static function coreModules(): Collection
    {
        $modules = new Collection(self::CORE_MODULES);

        return $modules->map(function (string $class, string $name): ModuleInterface {
            $module = app()->make($class);

            $module->setName($name);

            return $module;
        });
    }

    /**
     * All custom modules in the system.  Custom modules are defined in modules_v4/
     *
     * @return Collection
     */
    private static function customModules(): Collection
    {
        $pattern   = WT_ROOT . Webtrees::MODULES_PATH . '*/module.php';
        $filenames = glob($pattern);

        return (new Collection($filenames))
            ->filter(function (string $filename): bool {
                // Special characters will break PHP variable names.
                // This also allows us to ignore modules called "foo.example" and "foo.disable"
                $module_name = basename(dirname($filename));

                return !Str::contains($module_name, ['.', ' ', '[', ']']) && Str::length($module_name) <= 30;
            })
            ->map(function (string $filename): ?ModuleCustomInterface {
                try {
                    $module = self::load($filename);

                    if ($module instanceof ModuleCustomInterface) {
                        $module_name = '_' . basename(dirname($filename)) . '_';

                        $module->setName($module_name);
                    } else {
                        return null;
                    }

                    return $module;
                } catch (Throwable $ex) {
                    $message = '<pre>' . e($ex->getMessage()) . "\n" . e($ex->getTraceAsString()) . '</pre>';
                    FlashMessages::addMessage($message, 'danger');

                    return null;
                }
            })
            ->filter();
    }

    /**
     * All modules.
     *
     * @return Collection|ModuleInterface[]
     */
    public static function all(): Collection
    {
        return app('cache.array')->rememberForever('all_modules', function (): Collection {
            // Modules have a default status, order etc.
            // We can override these from database settings.
            $module_info = DB::table('module')
                ->get()
                ->mapWithKeys(function (stdClass $row): array {
                    return [$row->module_name => $row];
                });

            return self::coreModules()
                ->merge(self::customModules())
                ->map(function (ModuleInterface $module) use ($module_info): ModuleInterface {
                    $info = $module_info->get($module->name());

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
                        DB::table('module')->insert(['module_name' => $module->name()]);
                    }

                    return $module;
                })
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
