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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\FamilyListModule;
use Fisharebest\Webtrees\Module\IndividualListModule;
use Fisharebest\Webtrees\Module\MediaListModule;
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
use Fisharebest\Webtrees\Module\NoteListModule;
use Fisharebest\Webtrees\Module\RepositoryListModule;
use Fisharebest\Webtrees\Module\SourceListModule;
use Fisharebest\Webtrees\Services\HousekeepingService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\ServerCheckService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface;

/**
 * Controller for the administration pages
 */
class ControlPanelController extends AbstractAdminController
{
    /**
     * The control panel shows a summary of the site and links to admin functions.
     *
     * @param HousekeepingService $housekeeping_service
     * @param UpgradeService      $upgrade_service
     * @param ModuleService       $module_service
     * @param ServerCheckService  $server_check_service
     * @param UserService         $user_service
     *
     * @return ResponseInterface
     */
    public function controlPanel(
        HousekeepingService $housekeeping_service,
        UpgradeService $upgrade_service,
        ModuleService $module_service,
        ServerCheckService $server_check_service,
        UserService $user_service
    ): ResponseInterface {
        $filesystem      = new Filesystem(new Local(WT_ROOT));
        $files_to_delete = $housekeeping_service->deleteOldWebtreesFiles($filesystem);

        return $this->viewResponse('admin/control-panel', [
            'title'                      => I18N::translate('Control panel'),
            'server_errors'              => $server_check_service->serverErrors(),
            'server_warnings'            => $server_check_service->serverWarnings(),
            'latest_version'             => $upgrade_service->latestVersion(),
            'all_users'                  => $user_service->all(),
            'administrators'             => $user_service->administrators(),
            'managers'                   => $user_service->managers(),
            'moderators'                 => $user_service->moderators(),
            'unapproved'                 => $user_service->unapproved(),
            'unverified'                 => $user_service->unverified(),
            'all_trees'                  => Tree::getAll(),
            'changes'                    => $this->totalChanges(),
            'individuals'                => $this->totalIndividuals(),
            'families'                   => $this->totalFamilies(),
            'sources'                    => $this->totalSources(),
            'media'                      => $this->totalMediaObjects(),
            'repositories'               => $this->totalRepositories(),
            'notes'                      => $this->totalNotes(),
            'individual_list_module'     => $module_service->findByInterface(IndividualListModule::class)->first(),
            'family_list_module'         => $module_service->findByInterface(FamilyListModule::class)->first(),
            'media_list_module'          => $module_service->findByInterface(MediaListModule::class)->first(),
            'note_list_module'           => $module_service->findByInterface(NoteListModule::class)->first(),
            'repository_list_module'     => $module_service->findByInterface(RepositoryListModule::class)->first(),
            'source_list_module'         => $module_service->findByInterface(SourceListModule::class)->first(),
            'files_to_delete'            => $files_to_delete,
            'all_modules_disabled'       => $module_service->all(true),
            'all_modules_enabled'        => $module_service->all(false),
            'deleted_modules'            => $module_service->deletedModules(),
            'analytics_modules_disabled' => $module_service->findByInterface(ModuleAnalyticsInterface::class, true),
            'analytics_modules_enabled'  => $module_service->findByInterface(ModuleAnalyticsInterface::class, false),
            'block_modules_disabled'     => $module_service->findByInterface(ModuleBlockInterface::class, true),
            'block_modules_enabled'      => $module_service->findByInterface(ModuleBlockInterface::class, false),
            'chart_modules_disabled'     => $module_service->findByInterface(ModuleChartInterface::class, true),
            'chart_modules_enabled'      => $module_service->findByInterface(ModuleChartInterface::class, false),
            'other_modules'              => $module_service->otherModules(true),
            'footer_modules_disabled'    => $module_service->findByInterface(ModuleFooterInterface::class, true),
            'footer_modules_enabled'     => $module_service->findByInterface(ModuleFooterInterface::class, false),
            'history_modules_disabled'   => $module_service->findByInterface(ModuleHistoricEventsInterface::class, true),
            'history_modules_enabled'    => $module_service->findByInterface(ModuleHistoricEventsInterface::class, false),
            'language_modules_disabled'  => $module_service->findByInterface(ModuleLanguageInterface::class, true),
            'language_modules_enabled'   => $module_service->findByInterface(ModuleLanguageInterface::class, false),
            'list_modules_disabled'      => $module_service->findByInterface(ModuleListInterface::class, true),
            'list_modules_enabled'       => $module_service->findByInterface(ModuleListInterface::class, false),
            'menu_modules_disabled'      => $module_service->findByInterface(ModuleMenuInterface::class, true),
            'menu_modules_enabled'       => $module_service->findByInterface(ModuleMenuInterface::class, false),
            'report_modules_disabled'    => $module_service->findByInterface(ModuleReportInterface::class, true),
            'report_modules_enabled'     => $module_service->findByInterface(ModuleReportInterface::class, false),
            'sidebar_modules_disabled'   => $module_service->findByInterface(ModuleSidebarInterface::class, true),
            'sidebar_modules_enabled'    => $module_service->findByInterface(ModuleSidebarInterface::class, false),
            'tab_modules_disabled'       => $module_service->findByInterface(ModuleTabInterface::class, true),
            'tab_modules_enabled'        => $module_service->findByInterface(ModuleTabInterface::class, false),
            'theme_modules_disabled'     => $module_service->findByInterface(ModuleThemeInterface::class, true),
            'theme_modules_enabled'      => $module_service->findByInterface(ModuleThemeInterface::class, false),
        ]);
    }

    /**
     * Managers see a restricted version of the contol panel.
     *
     * @param ModuleService $module_service
     *
     * @return ResponseInterface
     */
    public function controlPanelManager(ModuleService $module_service): ResponseInterface
    {
        $all_trees = array_filter(Tree::getAll(), static function (Tree $tree): bool {
            return Auth::isManager($tree);
        });

        return $this->viewResponse('admin/control-panel-manager', [
            'title'                  => I18N::translate('Control panel'),
            'all_trees'              => $all_trees,
            'changes'                => $this->totalChanges(),
            'individuals'            => $this->totalIndividuals(),
            'families'               => $this->totalFamilies(),
            'sources'                => $this->totalSources(),
            'media'                  => $this->totalMediaObjects(),
            'repositories'           => $this->totalRepositories(),
            'notes'                  => $this->totalNotes(),
            'individual_list_module' => $module_service->findByInterface(IndividualListModule::class)->first(),
            'family_list_module'     => $module_service->findByInterface(FamilyListModule::class)->first(),
            'media_list_module'      => $module_service->findByInterface(MediaListModule::class)->first(),
            'note_list_module'       => $module_service->findByInterface(NoteListModule::class)->first(),
            'repository_list_module' => $module_service->findByInterface(RepositoryListModule::class)->first(),
            'source_list_module'     => $module_service->findByInterface(SourceListModule::class)->first(),
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
            ->groupBy('gedcom.gedcom_id')
            ->pluck(DB::raw('COUNT(change_id)'), 'gedcom.gedcom_id')
            ->all();
    }

    /**
     * Count the number of families in each tree.
     *
     * @return Collection
     * @return int[]
     */
    private function totalFamilies(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('families', 'f_file', '=', 'gedcom_id')
            ->groupBy('gedcom_id')
            ->pluck(DB::raw('COUNT(f_id)'), 'gedcom_id')
            ->map(static function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of individuals in each tree.
     *
     * @return Collection
     * @return int[]
     */
    private function totalIndividuals(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('individuals', 'i_file', '=', 'gedcom_id')
            ->groupBy('gedcom_id')
            ->pluck(DB::raw('COUNT(i_id)'), 'gedcom_id')
            ->map(static function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of media objects in each tree.
     *
     * @return Collection
     * @return int[]
     */
    private function totalMediaObjects(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('media', 'm_file', '=', 'gedcom_id')
            ->groupBy('gedcom_id')
            ->pluck(DB::raw('COUNT(m_id)'), 'gedcom_id')
            ->map(static function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of notes in each tree.
     *
     * @return Collection
     * @return int[]
     */
    private function totalNotes(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('other', static function (JoinClause $join): void {
                $join
                    ->on('o_file', '=', 'gedcom_id')
                    ->where('o_type', '=', 'NOTE');
            })
            ->groupBy('gedcom_id')
            ->pluck(DB::raw('COUNT(o_id)'), 'gedcom_id')
            ->map(static function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of repositorie in each tree.
     *
     * @return Collection
     * @return int[]
     */
    private function totalRepositories(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('other', static function (JoinClause $join): void {
                $join
                    ->on('o_file', '=', 'gedcom_id')
                    ->where('o_type', '=', 'REPO');
            })
            ->groupBy('gedcom_id')
            ->pluck(DB::raw('COUNT(o_id)'), 'gedcom_id')
            ->map(static function (string $count) {
                return (int) $count;
            });
    }

    /**
     * Count the number of sources in each tree.
     *
     * @return Collection
     * @return int[]
     */
    private function totalSources(): Collection
    {
        return DB::table('gedcom')
            ->leftJoin('sources', 's_file', '=', 'gedcom_id')
            ->groupBy('gedcom_id')
            ->pluck(DB::raw('COUNT(s_id)'), 'gedcom_id')
            ->map(static function (string $count) {
                return (int) $count;
            });
    }
}
