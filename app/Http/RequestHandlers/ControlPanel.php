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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\FamilyListModule;
use Fisharebest\Webtrees\Module\IndividualListModule;
use Fisharebest\Webtrees\Module\MediaListModule;
use Fisharebest\Webtrees\Module\ModuleAnalyticsInterface;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleDataFixInterface;
use Fisharebest\Webtrees\Module\ModuleFooterInterface;
use Fisharebest\Webtrees\Module\ModuleHistoricEventsInterface;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Module\ModuleListInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Module\NoteListModule;
use Fisharebest\Webtrees\Module\RepositoryListModule;
use Fisharebest\Webtrees\Module\SourceListModule;
use Fisharebest\Webtrees\Module\SubmitterListModule;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\HousekeepingService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\ServerCheckService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The control panel shows a summary of the site and links to admin functions.
 */
class ControlPanel implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var ModuleService */
    private $module_service;

    /** @var HousekeepingService */
    private $housekeeping_service;

    /** @var ServerCheckService */
    private $server_check_service;

    /** @var TreeService */
    private $tree_service;

    /** @var UpgradeService */
    private $upgrade_service;

    /** @var UserService */
    private $user_service;

    /**
     * ControlPanel constructor.
     *
     * @param HousekeepingService $housekeeping_service
     * @param ModuleService       $module_service
     * @param ServerCheckService  $server_check_service
     * @param TreeService         $tree_service
     * @param UpgradeService      $upgrade_service
     * @param UserService         $user_service
     */
    public function __construct(
        HousekeepingService $housekeeping_service,
        ModuleService $module_service,
        ServerCheckService $server_check_service,
        TreeService $tree_service,
        UpgradeService $upgrade_service,
        UserService $user_service
    ) {
        $this->module_service       = $module_service;
        $this->housekeeping_service = $housekeeping_service;
        $this->server_check_service = $server_check_service;
        $this->tree_service         = $tree_service;
        $this->upgrade_service      = $upgrade_service;
        $this->user_service         = $user_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $filesystem      = new Filesystem(new Local(Webtrees::ROOT_DIR));
        $files_to_delete = $this->housekeeping_service->deleteOldWebtreesFiles($filesystem);

        $custom_updates = $this->module_service
            ->findByInterface(ModuleCustomInterface::class)
            ->filter(static function (ModuleCustomInterface $module): bool {
                return version_compare($module->customModuleLatestVersion(), $module->customModuleVersion()) > 0;
            });

        return $this->viewResponse('admin/control-panel', [
            'title'                      => I18N::translate('Control panel'),
            'server_errors'              => $this->server_check_service->serverErrors(),
            'server_warnings'            => $this->server_check_service->serverWarnings(),
            'latest_version'             => $this->upgrade_service->latestVersion(),
            'all_users'                  => $this->user_service->all(),
            'administrators'             => $this->user_service->administrators(),
            'managers'                   => $this->user_service->managers(),
            'moderators'                 => $this->user_service->moderators(),
            'unapproved'                 => $this->user_service->unapproved(),
            'unverified'                 => $this->user_service->unverified(),
            'all_trees'                  => $this->tree_service->all(),
            'changes'                    => $this->totalChanges(),
            'individuals'                => $this->totalIndividuals(),
            'families'                   => $this->totalFamilies(),
            'sources'                    => $this->totalSources(),
            'media'                      => $this->totalMediaObjects(),
            'repositories'               => $this->totalRepositories(),
            'notes'                      => $this->totalNotes(),
            'submitters'                 => $this->totalSubmitters(),
            'individual_list_module'     => $this->module_service->findByInterface(IndividualListModule::class)->first(),
            'family_list_module'         => $this->module_service->findByInterface(FamilyListModule::class)->first(),
            'media_list_module'          => $this->module_service->findByInterface(MediaListModule::class)->first(),
            'note_list_module'           => $this->module_service->findByInterface(NoteListModule::class)->first(),
            'repository_list_module'     => $this->module_service->findByInterface(RepositoryListModule::class)->first(),
            'source_list_module'         => $this->module_service->findByInterface(SourceListModule::class)->first(),
            'submitter_list_module'      => $this->module_service->findByInterface(SubmitterListModule::class)->first(),
            'files_to_delete'            => $files_to_delete,
            'all_modules_disabled'       => $this->module_service->all(true),
            'all_modules_enabled'        => $this->module_service->all(),
            'deleted_modules'            => $this->module_service->deletedModules(),
            'analytics_modules_disabled' => $this->module_service->findByInterface(ModuleAnalyticsInterface::class, true),
            'analytics_modules_enabled'  => $this->module_service->findByInterface(ModuleAnalyticsInterface::class),
            'block_modules_disabled'     => $this->module_service->findByInterface(ModuleBlockInterface::class, true),
            'block_modules_enabled'      => $this->module_service->findByInterface(ModuleBlockInterface::class),
            'chart_modules_disabled'     => $this->module_service->findByInterface(ModuleChartInterface::class, true),
            'chart_modules_enabled'      => $this->module_service->findByInterface(ModuleChartInterface::class),
            'custom_updates'             => $custom_updates,
            'data_fix_modules_disabled'  => $this->module_service->findByInterface(ModuleDataFixInterface::class, true),
            'data_fix_modules_enabled'   => $this->module_service->findByInterface(ModuleDataFixInterface::class),
            'other_modules'              => $this->module_service->otherModules(true),
            'footer_modules_disabled'    => $this->module_service->findByInterface(ModuleFooterInterface::class, true),
            'footer_modules_enabled'     => $this->module_service->findByInterface(ModuleFooterInterface::class),
            'history_modules_disabled'   => $this->module_service->findByInterface(ModuleHistoricEventsInterface::class, true),
            'history_modules_enabled'    => $this->module_service->findByInterface(ModuleHistoricEventsInterface::class),
            'language_modules_disabled'  => $this->module_service->findByInterface(ModuleLanguageInterface::class, true),
            'language_modules_enabled'   => $this->module_service->findByInterface(ModuleLanguageInterface::class),
            'list_modules_disabled'      => $this->module_service->findByInterface(ModuleListInterface::class, true),
            'list_modules_enabled'       => $this->module_service->findByInterface(ModuleListInterface::class),
            'menu_modules_disabled'      => $this->module_service->findByInterface(ModuleMenuInterface::class, true),
            'menu_modules_enabled'       => $this->module_service->findByInterface(ModuleMenuInterface::class),
            'report_modules_disabled'    => $this->module_service->findByInterface(ModuleReportInterface::class, true),
            'report_modules_enabled'     => $this->module_service->findByInterface(ModuleReportInterface::class),
            'sidebar_modules_disabled'   => $this->module_service->findByInterface(ModuleSidebarInterface::class, true),
            'sidebar_modules_enabled'    => $this->module_service->findByInterface(ModuleSidebarInterface::class),
            'tab_modules_disabled'       => $this->module_service->findByInterface(ModuleTabInterface::class, true),
            'tab_modules_enabled'        => $this->module_service->findByInterface(ModuleTabInterface::class),
            'theme_modules_disabled'     => $this->module_service->findByInterface(ModuleThemeInterface::class, true),
            'theme_modules_enabled'      => $this->module_service->findByInterface(ModuleThemeInterface::class),
        ]);
    }

    /**
     * Count the number of pending changes in each tree.
     *
     * @return string[]
     */
    private function totalChanges(): array
    {
        return DB::table('gedcom')
            ->leftJoin('change', static function (JoinClause $join): void {
                $join
                    ->on('change.gedcom_id', '=', 'gedcom.gedcom_id')
                    ->where('change.status', '=', 'pending');
            })
            ->groupBy(['gedcom.gedcom_id'])
            ->pluck(new Expression('COUNT(change_id) AS aggregate'), 'gedcom.gedcom_id')
            ->all();
    }

    /**
     * Count the number of individuals in each tree.
     *
     * @return Collection<string,int>
     */
    private function totalIndividuals(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('individuals', 'i_file', '=', 'gedcom_id')
            ->groupBy(['gedcom_id'])
            ->pluck(new Expression('COUNT(i_id) AS aggregate'), 'gedcom_id')
            ->map(static function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of families in each tree.
     *
     * @return Collection<string,int>
     */
    private function totalFamilies(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('families', 'f_file', '=', 'gedcom_id')
            ->groupBy(['gedcom_id'])
            ->pluck(new Expression('COUNT(f_id) AS aggregate'), 'gedcom_id')
            ->map(static function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of sources in each tree.
     *
     * @return Collection<string,int>
     */
    private function totalSources(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('sources', 's_file', '=', 'gedcom_id')
            ->groupBy(['gedcom_id'])
            ->pluck(new Expression('COUNT(s_id) AS aggregate'), 'gedcom_id')
            ->map(static function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of media objects in each tree.
     *
     * @return Collection<string,int>
     */
    private function totalMediaObjects(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('media', 'm_file', '=', 'gedcom_id')
            ->groupBy(['gedcom_id'])
            ->pluck(new Expression('COUNT(m_id) AS aggregate'), 'gedcom_id')
            ->map(static function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of repositorie in each tree.
     *
     * @return Collection<string,int>
     */
    private function totalRepositories(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('other', static function (JoinClause $join): void {
                $join
                    ->on('o_file', '=', 'gedcom_id')
                    ->where('o_type', '=', Repository::RECORD_TYPE);
            })
            ->groupBy(['gedcom_id'])
            ->pluck(new Expression('COUNT(o_id) AS aggregate'), 'gedcom_id')
            ->map(static function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of notes in each tree.
     *
     * @return Collection<string,int>
     */
    private function totalNotes(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('other', static function (JoinClause $join): void {
                $join
                    ->on('o_file', '=', 'gedcom_id')
                    ->where('o_type', '=', Note::RECORD_TYPE);
            })
            ->groupBy(['gedcom_id'])
            ->pluck(new Expression('COUNT(o_id) AS aggregate'), 'gedcom_id')
            ->map(static function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of submitters in each tree.
     *
     * @return Collection<string,int>
     */
    private function totalSubmitters(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('other', static function (JoinClause $join): void {
                $join
                    ->on('o_file', '=', 'gedcom_id')
                    ->where('o_type', '=', Submitter::RECORD_TYPE);
            })
            ->groupBy(['gedcom_id'])
            ->pluck(new Expression('COUNT(o_id) AS aggregate'), 'gedcom_id')
            ->map(static function (string $count) {
                return (int) $count;
            });
    }
}
