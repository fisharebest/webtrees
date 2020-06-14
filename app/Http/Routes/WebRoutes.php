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

namespace Fisharebest\Webtrees\Http\Routes;

use Aura\Router\Map;
use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\Http\Middleware\AuthAdministrator;
use Fisharebest\Webtrees\Http\Middleware\AuthEditor;
use Fisharebest\Webtrees\Http\Middleware\AuthLoggedIn;
use Fisharebest\Webtrees\Http\Middleware\AuthManager;
use Fisharebest\Webtrees\Http\Middleware\AuthMember;
use Fisharebest\Webtrees\Http\Middleware\AuthModerator;
use Fisharebest\Webtrees\Http\RequestHandlers\AccountDelete;
use Fisharebest\Webtrees\Http\RequestHandlers\AccountEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\AccountUpdate;
use Fisharebest\Webtrees\Http\RequestHandlers\AddChildToFamilyAction;
use Fisharebest\Webtrees\Http\RequestHandlers\AddChildToFamilyPage;
use Fisharebest\Webtrees\Http\RequestHandlers\AddChildToIndividualAction;
use Fisharebest\Webtrees\Http\RequestHandlers\AddChildToIndividualPage;
use Fisharebest\Webtrees\Http\RequestHandlers\AddName;
use Fisharebest\Webtrees\Http\RequestHandlers\AddNewFact;
use Fisharebest\Webtrees\Http\RequestHandlers\AddParentToIndividualAction;
use Fisharebest\Webtrees\Http\RequestHandlers\AddParentToIndividualPage;
use Fisharebest\Webtrees\Http\RequestHandlers\AddSpouseToFamilyAction;
use Fisharebest\Webtrees\Http\RequestHandlers\AddSpouseToFamilyPage;
use Fisharebest\Webtrees\Http\RequestHandlers\AddSpouseToIndividualAction;
use Fisharebest\Webtrees\Http\RequestHandlers\AddSpouseToIndividualPage;
use Fisharebest\Webtrees\Http\RequestHandlers\AddUnlinkedAction;
use Fisharebest\Webtrees\Http\RequestHandlers\AddUnlinkedPage;
use Fisharebest\Webtrees\Http\RequestHandlers\AppleTouchIconPng;
use Fisharebest\Webtrees\Http\RequestHandlers\BroadcastAction;
use Fisharebest\Webtrees\Http\RequestHandlers\BroadcastPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ChangeFamilyMembersAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ChangeFamilyMembersPage;
use Fisharebest\Webtrees\Http\RequestHandlers\CleanDataFolder;
use Fisharebest\Webtrees\Http\RequestHandlers\ContactAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ContactPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ControlPanel;
use Fisharebest\Webtrees\Http\RequestHandlers\CopyFact;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateMediaObjectAction;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateMediaObjectModal;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateNoteAction;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateNoteModal;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateRepositoryAction;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateRepositoryModal;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateSourceAction;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateSourceModal;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateSubmitterAction;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateSubmitterModal;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateTreeAction;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateTreePage;
use Fisharebest\Webtrees\Http\RequestHandlers\DataFixChoose;
use Fisharebest\Webtrees\Http\RequestHandlers\DataFixData;
use Fisharebest\Webtrees\Http\RequestHandlers\DataFixPage;
use Fisharebest\Webtrees\Http\RequestHandlers\DataFixPreview;
use Fisharebest\Webtrees\Http\RequestHandlers\DataFixSelect;
use Fisharebest\Webtrees\Http\RequestHandlers\DataFixUpdate;
use Fisharebest\Webtrees\Http\RequestHandlers\DataFixUpdateAll;
use Fisharebest\Webtrees\Http\RequestHandlers\DeleteFact;
use Fisharebest\Webtrees\Http\RequestHandlers\DeletePath;
use Fisharebest\Webtrees\Http\RequestHandlers\DeleteRecord;
use Fisharebest\Webtrees\Http\RequestHandlers\DeleteTreeAction;
use Fisharebest\Webtrees\Http\RequestHandlers\DeleteUser;
use Fisharebest\Webtrees\Http\RequestHandlers\EditFactAction;
use Fisharebest\Webtrees\Http\RequestHandlers\EditFactPage;
use Fisharebest\Webtrees\Http\RequestHandlers\EditName;
use Fisharebest\Webtrees\Http\RequestHandlers\EditRawFactAction;
use Fisharebest\Webtrees\Http\RequestHandlers\EditRawFactPage;
use Fisharebest\Webtrees\Http\RequestHandlers\EditRawRecordAction;
use Fisharebest\Webtrees\Http\RequestHandlers\EditRawRecordPage;
use Fisharebest\Webtrees\Http\RequestHandlers\EmailPreferencesAction;
use Fisharebest\Webtrees\Http\RequestHandlers\EmailPreferencesPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ExportGedcomClient;
use Fisharebest\Webtrees\Http\RequestHandlers\ExportGedcomPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ExportGedcomServer;
use Fisharebest\Webtrees\Http\RequestHandlers\FamilyPage;
use Fisharebest\Webtrees\Http\RequestHandlers\FaviconIco;
use Fisharebest\Webtrees\Http\RequestHandlers\GedcomRecordPage;
use Fisharebest\Webtrees\Http\RequestHandlers\HeaderPage;
use Fisharebest\Webtrees\Http\RequestHandlers\HelpText;
use Fisharebest\Webtrees\Http\RequestHandlers\HomePage;
use Fisharebest\Webtrees\Http\RequestHandlers\IndividualPage;
use Fisharebest\Webtrees\Http\RequestHandlers\LinkChildToFamilyAction;
use Fisharebest\Webtrees\Http\RequestHandlers\LinkChildToFamilyPage;
use Fisharebest\Webtrees\Http\RequestHandlers\LinkSpouseToIndividualAction;
use Fisharebest\Webtrees\Http\RequestHandlers\LinkSpouseToIndividualPage;
use Fisharebest\Webtrees\Http\RequestHandlers\LoginAction;
use Fisharebest\Webtrees\Http\RequestHandlers\LoginPage;
use Fisharebest\Webtrees\Http\RequestHandlers\Logout;
use Fisharebest\Webtrees\Http\RequestHandlers\Masquerade;
use Fisharebest\Webtrees\Http\RequestHandlers\MediaPage;
use Fisharebest\Webtrees\Http\RequestHandlers\MergeFactsAction;
use Fisharebest\Webtrees\Http\RequestHandlers\MergeFactsPage;
use Fisharebest\Webtrees\Http\RequestHandlers\MergeRecordsAction;
use Fisharebest\Webtrees\Http\RequestHandlers\MergeRecordsPage;
use Fisharebest\Webtrees\Http\RequestHandlers\MessageAction;
use Fisharebest\Webtrees\Http\RequestHandlers\MessagePage;
use Fisharebest\Webtrees\Http\RequestHandlers\MessageSelect;
use Fisharebest\Webtrees\Http\RequestHandlers\ModuleAction;
use Fisharebest\Webtrees\Http\RequestHandlers\NotePage;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordRequestAction;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordRequestPage;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordResetAction;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordResetPage;
use Fisharebest\Webtrees\Http\RequestHandlers\PasteFact;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChanges;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChangesAcceptChange;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChangesAcceptRecord;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChangesAcceptTree;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChangesLogAction;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChangesLogData;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChangesLogDelete;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChangesLogDownload;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChangesLogPage;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChangesRejectChange;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChangesRejectRecord;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChangesRejectTree;
use Fisharebest\Webtrees\Http\RequestHandlers\PhpInformation;
use Fisharebest\Webtrees\Http\RequestHandlers\Ping;
use Fisharebest\Webtrees\Http\RequestHandlers\RegisterAction;
use Fisharebest\Webtrees\Http\RequestHandlers\RegisterPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ReorderChildrenAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ReorderChildrenPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ReorderFamiliesAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ReorderFamiliesPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ReorderMediaAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ReorderMediaPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ReorderNamesAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ReorderNamesPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ReportGenerate;
use Fisharebest\Webtrees\Http\RequestHandlers\ReportListAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ReportListPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ReportSetupAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ReportSetupPage;
use Fisharebest\Webtrees\Http\RequestHandlers\RepositoryPage;
use Fisharebest\Webtrees\Http\RequestHandlers\RobotsTxt;
use Fisharebest\Webtrees\Http\RequestHandlers\SearchAdvancedAction;
use Fisharebest\Webtrees\Http\RequestHandlers\SearchAdvancedPage;
use Fisharebest\Webtrees\Http\RequestHandlers\SearchGeneralAction;
use Fisharebest\Webtrees\Http\RequestHandlers\SearchGeneralPage;
use Fisharebest\Webtrees\Http\RequestHandlers\SearchPhoneticAction;
use Fisharebest\Webtrees\Http\RequestHandlers\SearchPhoneticPage;
use Fisharebest\Webtrees\Http\RequestHandlers\SearchQuickAction;
use Fisharebest\Webtrees\Http\RequestHandlers\SearchReplaceAction;
use Fisharebest\Webtrees\Http\RequestHandlers\SearchReplacePage;
use Fisharebest\Webtrees\Http\RequestHandlers\Select2Family;
use Fisharebest\Webtrees\Http\RequestHandlers\Select2Individual;
use Fisharebest\Webtrees\Http\RequestHandlers\Select2MediaObject;
use Fisharebest\Webtrees\Http\RequestHandlers\Select2Note;
use Fisharebest\Webtrees\Http\RequestHandlers\Select2Place;
use Fisharebest\Webtrees\Http\RequestHandlers\Select2Repository;
use Fisharebest\Webtrees\Http\RequestHandlers\Select2Source;
use Fisharebest\Webtrees\Http\RequestHandlers\Select2Submitter;
use Fisharebest\Webtrees\Http\RequestHandlers\SelectDefaultTree;
use Fisharebest\Webtrees\Http\RequestHandlers\SelectLanguage;
use Fisharebest\Webtrees\Http\RequestHandlers\SelectNewFact;
use Fisharebest\Webtrees\Http\RequestHandlers\SelectTheme;
use Fisharebest\Webtrees\Http\RequestHandlers\SiteLogsAction;
use Fisharebest\Webtrees\Http\RequestHandlers\SiteLogsData;
use Fisharebest\Webtrees\Http\RequestHandlers\SiteLogsDelete;
use Fisharebest\Webtrees\Http\RequestHandlers\SiteLogsDownload;
use Fisharebest\Webtrees\Http\RequestHandlers\SiteLogsPage;
use Fisharebest\Webtrees\Http\RequestHandlers\SitePreferencesAction;
use Fisharebest\Webtrees\Http\RequestHandlers\SitePreferencesPage;
use Fisharebest\Webtrees\Http\RequestHandlers\SiteRegistrationAction;
use Fisharebest\Webtrees\Http\RequestHandlers\SiteRegistrationPage;
use Fisharebest\Webtrees\Http\RequestHandlers\SourcePage;
use Fisharebest\Webtrees\Http\RequestHandlers\SubmissionPage;
use Fisharebest\Webtrees\Http\RequestHandlers\SubmitterPage;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePage;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageBlock;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageBlockEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageBlockUpdate;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageDefaultEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageDefaultUpdate;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageUpdate;
use Fisharebest\Webtrees\Http\RequestHandlers\UnconnectedAction;
use Fisharebest\Webtrees\Http\RequestHandlers\UnconnectedPage;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPage;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPageBlock;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPageBlockEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPageBlockUpdate;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPageDefaultEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPageDefaultUpdate;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPageEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPageUpdate;
use Fisharebest\Webtrees\Http\RequestHandlers\UsersCleanupAction;
use Fisharebest\Webtrees\Http\RequestHandlers\UsersCleanupPage;
use Fisharebest\Webtrees\Http\RequestHandlers\VerifyEmail;

/**
 * Routing table for web requests
 */
class WebRoutes
{
    public function load(Map $router): void
    {
        $router->attach('', '', static function (Map $router) {
            // Admin routes.
            $router->attach('', '/admin', static function (Map $router) {
                $router->extras([
                    'middleware' => [
                        AuthAdministrator::class,
                    ],
                ]);

                $router->get(ControlPanel::class, '', ControlPanel::class);
                $router->get(BroadcastPage::class, '/broadcast', BroadcastPage::class);
                $router->post(BroadcastAction::class, '/broadcast', BroadcastAction::class);
                $router->get(CleanDataFolder::class, '/clean', CleanDataFolder::class);
                $router->post(DeletePath::class, '/delete-path', DeletePath::class);
                $router->get(EmailPreferencesPage::class, '/email', EmailPreferencesPage::class);
                $router->post(EmailPreferencesAction::class, '/email', EmailPreferencesAction::class);
                $router->get(PhpInformation::class, '/information', PhpInformation::class);
                $router->get(SiteLogsPage::class, '/logs', SiteLogsPage::class);
                $router->post(SiteLogsAction::class, '/logs', SiteLogsAction::class);
                $router->get(SiteLogsData::class, '/logs-data', SiteLogsData::class);
                $router->post(SiteLogsDelete::class, '/logs-delete', SiteLogsDelete::class);
                $router->get(SiteLogsDownload::class, '/logs-download', SiteLogsDownload::class);
                $router->post(Masquerade::class, '/masquerade/{user_id}', Masquerade::class);
                $router->get('admin-media', '/media', 'Admin\MediaController::index');
                $router->post('admin-media-select', '/media', 'Admin\MediaController::select');
                $router->get('admin-media-data', '/media-data', 'Admin\MediaController::data');
                $router->get('admin-media-upload', '/media-upload', 'Admin\MediaController::upload');
                $router->post('admin-media-upload-action', '/media-upload', 'Admin\MediaController::uploadAction');
                $router->get(CreateTreePage::class, '/trees/create', CreateTreePage::class);
                $router->post(CreateTreeAction::class, '/trees/create', CreateTreeAction::class);
                $router->post(SelectDefaultTree::class, '/trees/default/{tree}', SelectDefaultTree::class);
                $router->post(DeleteTreeAction::class, '/trees/delete/{tree}', DeleteTreeAction::class);
                $router->get('admin-fix-level-0-media', '/fix-level-0-media', 'Admin\FixLevel0MediaController::fixLevel0Media');
                $router->post('admin-fix-level-0-media-action', '/fix-level-0-media', 'Admin\FixLevel0MediaController::fixLevel0MediaAction');
                $router->get('admin-fix-level-0-media-data', '/fix-level-0-media-data', 'Admin\FixLevel0MediaController::fixLevel0MediaData');
                $router->get('admin-webtrees1-thumbs', '/webtrees1-thumbs', 'Admin\ImportThumbnailsController::webtrees1Thumbnails');
                $router->post('admin-webtrees1-thumbs-action', '/webtrees1-thumbs', 'Admin\ImportThumbnailsController::webtrees1ThumbnailsAction');
                $router->get('admin-webtrees1-thumbs-data', '/webtrees1-thumbs-data', 'Admin\ImportThumbnailsController::webtrees1ThumbnailsData');
                $router->get('modules', '/modules', 'Admin\ModuleController::list');
                $router->post('modules-update', '/modules', 'Admin\ModuleController::update');
                $router->get('analytics', '/analytics', 'Admin\ModuleController::listAnalytics');
                $router->post('analytics-update', '/analytics', 'Admin\ModuleController::updateAnalytics');
                $router->get('blocks', '/blocks', 'Admin\ModuleController::listBlocks');
                $router->post('blocks-update', '/blocks', 'Admin\ModuleController::updateBlocks');
                $router->get('charts', '/charts', 'Admin\ModuleController::listCharts');
                $router->post('charts-update', '/charts', 'Admin\ModuleController::updateCharts');
                $router->get('data-fixes', '/data-fixes', 'Admin\ModuleController::listDataFixes');
                $router->post('data-fixes-update', '/data-fixes', 'Admin\ModuleController::updateDataFixes');
                $router->get('lists', '/lists', 'Admin\ModuleController::listLists');
                $router->post('lists-update', '/lists', 'Admin\ModuleController::updateLists');
                $router->get('footers', '/footers', 'Admin\ModuleController::listFooters');
                $router->post('footers-update', '/footers', 'Admin\ModuleController::updateFooters');
                $router->get('history', '/history', 'Admin\ModuleController::listHistory');
                $router->post('history-update', '/history', 'Admin\ModuleController::updateHistory');
                $router->get('menus', '/menus', 'Admin\ModuleController::listMenus');
                $router->post('menus-update', '/menus', 'Admin\ModuleController::updateMenus');
                $router->get('languages', '/languages', 'Admin\ModuleController::listLanguages');
                $router->post('languages-update', '/languages', 'Admin\ModuleController::updateLanguages');
                $router->get('reports', '/reports', 'Admin\ModuleController::listReports');
                $router->post('reports-update', '/reports', 'Admin\ModuleController::updateReports');
                $router->get('sidebars', '/sidebars', 'Admin\ModuleController::listSidebars');
                $router->post('sidebars-update', '/sidebars', 'Admin\ModuleController::updateSidebars');
                $router->get('themes', '/themes', 'Admin\ModuleController::listThemes');
                $router->post('themes-update', '/themes', 'Admin\ModuleController::updateThemes');
                $router->get('tabs', '/tabs', 'Admin\ModuleController::listTabs');
                $router->post('tabs-update', '/tabs', 'Admin\ModuleController::updateTabs');
                $router->get(UsersCleanupPage::class, '/users-cleanup', UsersCleanupPage::class);
                $router->post(UsersCleanupAction::class, '/users-cleanup', UsersCleanupAction::class);
                $router->post('delete-module-settings', '/delete-module-settings', 'Admin\ModuleController::deleteModuleSettings');
                $router->get('map-data', '/map-data', 'Admin\LocationController::mapData');
                $router->get('map-data-edit', '/map-data-edit', 'Admin\LocationController::mapDataEdit');
                $router->post('map-data-update', '/map-data-edit', 'Admin\LocationController::mapDataSave');
                $router->post('map-data-delete', '/map-data-delete', 'Admin\LocationController::mapDataDelete');
                $router->get('locations-export', '/locations-export', 'Admin\LocationController::exportLocations');
                $router->get('locations-import', '/locations-import', 'Admin\LocationController::importLocations');
                $router->post('locations-import-action', '/locations-import', 'Admin\LocationController::importLocationsAction');
                $router->post('locations-import-from-tree', '/locations-import-from-tree', 'Admin\LocationController::importLocationsFromTree');
                $router->get('map-provider', '/map-provider', 'Admin\MapProviderController::mapProviderEdit');
                $router->post('map-provider-action', '/map-provider', 'Admin\MapProviderController::mapProviderSave');
                $router->get('upgrade', '/upgrade', 'Admin\UpgradeController::wizard');
                $router->post('upgrade-confirm', '/upgrade-confirm', 'Admin\UpgradeController::confirm');
                $router->post('upgrade-action', '/upgrade', 'Admin\UpgradeController::step');
                $router->get('admin-users', '/admin-users', 'Admin\UsersController::index');
                $router->get('admin-users-data', '/admin-users-data', 'Admin\UsersController::data');
                $router->get('admin-users-create', '/admin-users-create', 'Admin\UsersController::create');
                $router->post('admin-users-create-action', '/admin-users-create', 'Admin\UsersController::save');
                $router->get('admin-users-edit', '/admin-users-edit', 'Admin\UsersController::edit');
                $router->post('admin-users-update', '/admin-users-edit', 'Admin\UsersController::update');
                $router->get(SitePreferencesPage::class, '/site-preferences', SitePreferencesPage::class);
                $router->post(SitePreferencesAction::class, '/site-preferences', SitePreferencesAction::class);
                $router->get(SiteRegistrationPage::class, '/site-registration', SiteRegistrationPage::class);
                $router->post(SiteRegistrationAction::class, '/site-registration', SiteRegistrationAction::class);
                $router->get(TreePageDefaultEdit::class, '/trees/default-blocks', TreePageDefaultEdit::class);
                $router->post(TreePageDefaultUpdate::class, '/trees/default-blocks', TreePageDefaultUpdate::class);
                $router->get('admin-trees-merge', '/trees/merge', 'AdminTreesController::merge');
                $router->post('admin-trees-merge-action', '/trees/merge', 'AdminTreesController::mergeAction');
                $router->post('admin-trees-sync', '/trees/sync', 'AdminTreesController::synchronize');
                $router->get('unused-media-thumbnail', '/unused-media-thumbnail', 'MediaFileController::unusedMediaThumbnail');
                $router->post('delete-user', '/users/delete/{user_id}', DeleteUser::class);
                $router->get(UserPageDefaultEdit::class, '/user-page-default-edit', UserPageDefaultEdit::class);
                $router->post(UserPageDefaultUpdate::class, '/user-page-default-update', UserPageDefaultUpdate::class);
            });

            // Manager routes (multiple trees).
            $router->attach('', '/admin', static function (Map $router) {
                $router->extras([
                    'middleware' => [
                        AuthManager::class,
                    ],
                ]);

                $router->get('manage-trees', '/trees/manage/{tree}', 'AdminTreesController::index');
            });

            // Manager routes.
            $router->attach('', '/tree/{tree}', static function (Map $router) {
                $router->extras([
                    'middleware' => [
                        AuthManager::class,
                    ],
                ]);

                $router->get(PendingChangesLogPage::class, '/changes-log', PendingChangesLogPage::class);
                $router->post(PendingChangesLogAction::class, '/changes-log', PendingChangesLogAction::class);
                $router->get(PendingChangesLogData::class, '/changes-data', PendingChangesLogData::class);
                $router->post(PendingChangesLogDelete::class, '/changes-delete', PendingChangesLogDelete::class);
                $router->get(PendingChangesLogDownload::class, '/changes-download', PendingChangesLogDownload::class);
                $router->get('admin-trees-check', '/check', 'AdminTreesController::check');
                $router->get(DataFixChoose::class, '/data-fix', DataFixChoose::class);
                $router->post(DataFixSelect::class, '/data-fix', DataFixSelect::class);
                $router->get(DataFixPage::class, '/data-fix/{data_fix}', DataFixPage::class);
                $router->post(DataFixUpdate::class, '/data-fix/{data_fix}/update', DataFixUpdate::class);
                $router->post(DataFixUpdateAll::class, '/data-fix/{data_fix}/update-all', DataFixUpdateAll::class);
                $router->get(DataFixData::class, '/data-fix/{data_fix}/data', DataFixData::class);
                $router->get(DataFixPreview::class, '/data-fix/{data_fix}/preview', DataFixPreview::class);
                $router->get('admin-trees-duplicates', '/duplicates', 'AdminTreesController::duplicates');
                $router->get(ExportGedcomPage::class, '/export', ExportGedcomPage::class);
                $router->post(ExportGedcomClient::class, '/export-client', ExportGedcomClient::class);
                $router->post(ExportGedcomServer::class, '/export-server', ExportGedcomServer::class);
                $router->get('admin-trees-import', '/import', 'AdminTreesController::importForm');
                $router->post('admin-trees-import-action', '/import', 'AdminTreesController::importAction');
                $router->get(MergeRecordsPage::class, '/merge-step1', MergeRecordsPage::class);
                $router->post(MergeRecordsAction::class, '/merge-step1', MergeRecordsAction::class);
                $router->get(MergeFactsPage::class, '/merge-step2', MergeFactsPage::class);
                $router->post(MergeFactsAction::class, '/merge-step2', MergeFactsAction::class);
                $router->get('admin-trees-preferences', '/preferences', 'AdminTreesController::preferences');
                $router->post('admin-trees-preferences-update', '/preferences', 'AdminTreesController::preferencesUpdate');
                $router->get('admin-trees-renumber', '/renumber', 'AdminTreesController::renumber');
                $router->post('admin-trees-renumber-action', '/renumber', 'AdminTreesController::renumberAction');
                $router->get(TreePageEdit::class, '/tree-page-edit', TreePageEdit::class);
                $router->post('import', '/load', 'GedcomFileController::import');
                $router->post(TreePageUpdate::class, '/tree-page-update', TreePageUpdate::class);
                $router->get(TreePageBlockEdit::class, '/tree-page-block-edit', TreePageBlockEdit::class);
                $router->post(TreePageBlockUpdate::class, '/tree-page-block-edit', TreePageBlockUpdate::class);
                $router->get('tree-preferences', '/preferences', 'AdminController::treePreferencesEdit');
                $router->post('tree-preferences-update', '/preferences', 'AdminController::treePreferencesUpdate');
                $router->get('tree-privacy', '/privacy', 'AdminController::treePrivacyEdit');
                $router->post('tree-privacy-update', '/privacy', 'AdminController::treePrivacyUpdate');
                $router->get(UnconnectedPage::class, '/unconnected', UnconnectedPage::class);
                $router->post(UnconnectedAction::class, '/unconnected', UnconnectedAction::class);
            });

            // Moderator routes.
            $router->attach('', '/tree/{tree}', static function (Map $router) {
                $router->extras([
                    'middleware' => [
                        AuthModerator::class,
                    ],
                ]);
                $router->post(PendingChangesAcceptTree::class, '/accept', PendingChangesAcceptTree::class);
                $router->post(PendingChangesAcceptRecord::class, '/accept/{xref}', PendingChangesAcceptRecord::class);
                $router->post(PendingChangesAcceptChange::class, '/accept/{xref}/{change}', PendingChangesAcceptChange::class);
                $router->get(PendingChanges::class, '/pending', PendingChanges::class);
                $router->post(PendingChangesRejectTree::class, '/reject', PendingChangesRejectTree::class);
                $router->post(PendingChangesRejectRecord::class, '/reject/{xref}', PendingChangesRejectRecord::class);
                $router->post(PendingChangesRejectChange::class, '/reject/{xref}/{change}', PendingChangesRejectChange::class);
            });

            // Editor routes.
            $router->attach('', '/tree/{tree}', static function (Map $router) {
                $router->extras([
                    'middleware' => [
                        AuthEditor::class,
                    ],
                ]);

                $router->get(AddChildToFamilyPage::class, '/add-child-to-family', AddChildToFamilyPage::class);
                $router->post(AddChildToFamilyAction::class, '/add-child-to-family', AddChildToFamilyAction::class);
                $router->get(AddNewFact::class, '/add-fact/{xref}/{fact}', AddNewFact::class);
                $router->post(SelectNewFact::class, '/add-fact/{xref}', SelectNewFact::class);
                $router->get('add-media-file', '/add-media-file', 'EditMediaController::addMediaFile');
                $router->post('add-media-file-update', '/add-media-file', 'EditMediaController::addMediaFileAction');
                $router->get(AddName::class, '/add-name', AddName::class);
                $router->get(AddSpouseToFamilyPage::class, '/add-spouse-to-family', AddSpouseToFamilyPage::class);
                $router->post(AddSpouseToFamilyAction::class, '/add-spouse-to-family', AddSpouseToFamilyAction::class);
                $router->get(ChangeFamilyMembersPage::class, '/change-family-members', ChangeFamilyMembersPage::class);
                $router->post(ChangeFamilyMembersAction::class, '/change-family-members', ChangeFamilyMembersAction::class);
                $router->get(CreateMediaObjectModal::class, '/create-media-object', CreateMediaObjectModal::class);
                $router->post(CreateMediaObjectAction::class, '/create-media-object', CreateMediaObjectAction::class);
                $router->post('create-media-from-file', '/create-media-from-file', 'EditMediaController::createMediaObjectFromFileAction');
                $router->post(CopyFact::class, '/copy/{xref}/{fact_id}', CopyFact::class);
                $router->get(CreateNoteModal::class, '/create-note-object', CreateNoteModal::class);
                $router->post(CreateNoteAction::class, '/create-note-object', CreateNoteAction::class);
                $router->get(CreateRepositoryModal::class, '/create-repository', CreateRepositoryModal::class);
                $router->post(CreateRepositoryAction::class, '/create-repository', CreateRepositoryAction::class);
                $router->get(CreateSourceModal::class, '/create-source', CreateSourceModal::class);
                $router->post(CreateSourceAction::class, '/create-source', CreateSourceAction::class);
                $router->get(CreateSubmitterModal::class, '/create-submitter', CreateSubmitterModal::class);
                $router->post(CreateSubmitterAction::class, '/create-submitter', CreateSubmitterAction::class);
                $router->post(DeleteRecord::class, '/delete/{xref}', DeleteRecord::class);
                $router->post(DeleteFact::class, '/delete/{xref}/{fact_id}', DeleteFact::class);
                $router->get(EditFactPage::class, '/edit-fact/{xref}/{fact_id}', EditFactPage::class);
                $router->post(EditFactAction::class, '/update-fact/{xref}{/fact_id}', EditFactAction::class);
                $router->get('edit-media-file', '/edit-media-file', 'EditMediaController::editMediaFile');
                $router->post('edit-media-file-update', '/edit-media-file', 'EditMediaController::editMediaFileAction');
                $router->get('edit-note-object', '/edit-note-object/{xref}', 'EditNoteController::editNoteObject');
                $router->post('edit-note-object-action', '/edit-note-object/{xref}', 'EditNoteController::updateNoteObject');
                $router->get(EditRawFactPage::class, '/edit-raw/{xref}/{fact_id}', EditRawFactPage::class);
                $router->post(EditRawFactAction::class, '/edit-raw/{xref}/{fact_id}', EditRawFactAction::class);
                $router->get(EditRawRecordPage::class, '/edit-raw/{xref}', EditRawRecordPage::class);
                $router->post(EditRawRecordAction::class, '/edit-raw/{xref}', EditRawRecordAction::class);
                $router->get('link-media-to-individual', '/link-media-to-individual', 'EditMediaController::linkMediaToIndividual');
                $router->get('link-media-to-family', '/link-media-to-family', 'EditMediaController::linkMediaToFamily');
                $router->get('link-media-to-source', '/link-media-to-source', 'EditMediaController::linkMediaToSource');
                $router->post('link-media-to-record', '/link-media-to-record/{xref}', 'EditMediaController::linkMediaToRecordAction');
                $router->post(PasteFact::class, '/paste-fact/{xref}', PasteFact::class);
                $router->get(ReorderChildrenPage::class, '/reorder-children/{xref}', ReorderChildrenPage::class);
                $router->post(ReorderChildrenAction::class, '/reorder-children/{xref}', ReorderChildrenAction::class);
                $router->get(ReorderMediaPage::class, '/reorder-media/{xref}', ReorderMediaPage::class);
                $router->post(ReorderMediaAction::class, '/reorder-media/{xref}', ReorderMediaAction::class);
                $router->get(ReorderNamesPage::class, '/reorder-names/{xref}', ReorderNamesPage::class);
                $router->post(ReorderNamesAction::class, '/reorder-names/{xref}', ReorderNamesAction::class);
                $router->get(ReorderFamiliesPage::class, '/reorder-spouses/{xref}', ReorderFamiliesPage::class);
                $router->post(ReorderFamiliesAction::class, '/reorder-spouses/{xref}', ReorderFamiliesAction::class);
                $router->get(SearchReplacePage::class, '/search-replace', SearchReplacePage::class);
                $router->post(SearchReplaceAction::class, '/search-replace', SearchReplaceAction::class);
                $router->get(AddChildToIndividualPage::class, '/add-child-to-individual', AddChildToIndividualPage::class);
                $router->post(AddChildToIndividualAction::class, '/add-child-to-individual', AddChildToIndividualAction::class);
                $router->get(AddParentToIndividualPage::class, '/add-parent-to-individual', AddParentToIndividualPage::class);
                $router->post(AddParentToIndividualAction::class, '/add-parent-to-individual', AddParentToIndividualAction::class);
                $router->get(AddSpouseToIndividualPage::class, '/add-spouse-to-individual', AddSpouseToIndividualPage::class);
                $router->post(AddSpouseToIndividualAction::class, '/add-spouse-to-individual', AddSpouseToIndividualAction::class);
                $router->get(AddUnlinkedPage::class, '/add-unlinked-individual', AddUnlinkedPage::class);
                $router->post(AddUnlinkedAction::class, '/add-unlinked-individual', AddUnlinkedAction::class);
                $router->get(LinkChildToFamilyPage::class, '/link-child-to-family', LinkChildToFamilyPage::class);
                $router->post(LinkChildToFamilyAction::class, '/link-child-to-family', LinkChildToFamilyAction::class);
                $router->get(LinkSpouseToIndividualPage::class, '/link-spouse-to-individual', LinkSpouseToIndividualPage::class);
                $router->post(LinkSpouseToIndividualAction::class, '/link-spouse-to-individual', LinkSpouseToIndividualAction::class);
                $router->get(EditName::class, '/edit-name/{xref}/{fact_id}', EditName::class);
            });

            // Member routes.
            $router->attach('', '/tree/{tree}', static function (Map $router) {
                $router->extras([
                    'middleware' => [
                        AuthMember::class,
                    ],
                ]);

                $router->post(MessageSelect::class, '/message-select', MessageSelect::class);
                $router->get(MessagePage::class, '/message-compose', MessagePage::class);
                $router->post(MessageAction::class, '/message-send', MessageAction::class);
                $router->get(UserPage::class, '/my-page', UserPage::class);
                $router->get(UserPageBlock::class, '/my-page-block', UserPageBlock::class);
                $router->get(UserPageEdit::class, '/my-page-edit', UserPageEdit::class);
                $router->post(UserPageUpdate::class, '/my-page-edit', UserPageUpdate::class);
                $router->get(UserPageBlockEdit::class, '/my-page-block-edit', UserPageBlockEdit::class);
                $router->post(UserPageBlockUpdate::class, '/my-page-block-edit', UserPageBlockUpdate::class);
            });

            // User routes.
            $router->attach('', '', static function (Map $router) {
                $router->extras([
                    'middleware' => [
                        AuthLoggedIn::class,
                    ],
                ]);

                $router->get(AccountEdit::class, '/my-account{/tree}', AccountEdit::class);
                $router->post(AccountUpdate::class, '/my-account{/tree}', AccountUpdate::class);
                $router->post(AccountDelete::class, '/my-account-delete', AccountDelete::class);
            });

            // Visitor routes - with an optional tree (for sites with no public trees).
            $router->attach('', '', static function (Map $router) {
                $router->get(LoginPage::class, '/login{/tree}', LoginPage::class);
                $router->post(LoginAction::class, '/login{/tree}', LoginAction::class);
                $router->get(PasswordRequestPage::class, '/password-request{/tree}', PasswordRequestPage::class);
                $router->post(PasswordRequestAction::class, '/password-request{/tree}', PasswordRequestAction::class);
                $router->get(RegisterPage::class, '/register{/tree}', RegisterPage::class);
                $router->post(RegisterAction::class, '/register{/tree}', RegisterAction::class);
                $router->get(PasswordResetPage::class, '/password-reset/{token}{/tree}', PasswordResetPage::class);
                $router->post(PasswordResetAction::class, '/password-reset/{token}{/tree}', PasswordResetAction::class);
                $router->get(VerifyEmail::class, '/verify/{username}/{token}{/tree}', VerifyEmail::class);
            });

            // Visitor routes with a tree.
            $router->attach('', '/tree/{tree}', static function (Map $router) {
                $router->get(TreePage::class, '', TreePage::class);
                $router->get('autocomplete-folder', '/autocomplete-folder', 'AutocompleteController::folder');
                $router->get('autocomplete-page', '/autocomplete-page', 'AutocompleteController::page');
                $router->get('autocomplete-place', '/autocomplete-place', 'AutocompleteController::place');
                $router->get('calendar', '/calendar/{view}', 'CalendarController::page');
                $router->post('calendar-select', '/calendar/{view}', 'CalendarController::select');
                $router->get('calendar-events', '/calendar-events/{view}', 'CalendarController::calendar');
                $router->get(ContactPage::class, '/contact', ContactPage::class);
                $router->post(ContactAction::class, '/contact', ContactAction::class);
                $router->get(FamilyPage::class, '/family/{xref}{/slug}', FamilyPage::class);
                $router->get(HeaderPage::class, '/header/{xref}{/slug}', HeaderPage::class);
                $router->get(IndividualPage::class, '/individual/{xref}{/slug}', IndividualPage::class);
                $router->get('media-thumbnail', '/media-thumbnail', 'MediaFileController::mediaThumbnail');
                $router->get('media-download', '/media-download', 'MediaFileController::mediaDownload');
                $router->get(MediaPage::class, '/media/{xref}{/slug}', MediaPage::class);
                $router->get(NotePage::class, '/note/{xref}{/slug}', NotePage::class);
                $router->get(GedcomRecordPage::class, '/record/{xref}{/slug}', GedcomRecordPage::class);
                $router->get(RepositoryPage::class, '/repository/{xref}{/slug}', RepositoryPage::class);
                $router->get(ReportListPage::class, '/report', ReportListPage::class);
                $router->post(ReportListAction::class, '/report', ReportListAction::class);
                $router->get(ReportSetupPage::class, '/report/{report}', ReportSetupPage::class);
                $router->post(ReportSetupAction::class, '/report/{report}', ReportSetupAction::class);
                $router->get(ReportGenerate::class, '/report-run/{report}', ReportGenerate::class);
                $router->get(SearchAdvancedPage::class, '/search-advanced', SearchAdvancedPage::class);
                $router->post(SearchAdvancedAction::class, '/search-advanced', SearchAdvancedAction::class);
                $router->get(SearchGeneralPage::class, '/search-general', SearchGeneralPage::class);
                $router->post(SearchGeneralAction::class, '/search-general', SearchGeneralAction::class);
                $router->get(SearchPhoneticPage::class, '/search-phonetic', SearchPhoneticPage::class);
                $router->post(SearchPhoneticAction::class, '/search-phonetic', SearchPhoneticAction::class);
                $router->post(SearchQuickAction::class, '/search-quick', SearchQuickAction::class);
                $router->post(Select2Family::class, '/select2-family', Select2Family::class);
                $router->post(Select2Individual::class, '/select2-individual', Select2Individual::class);
                $router->post(Select2MediaObject::class, '/select2-media', Select2MediaObject::class);
                $router->post(Select2Note::class, '/select2-note', Select2Note::class);
                $router->post(Select2Place::class, '/select2-place', Select2Place::class);
                $router->post(Select2Source::class, '/select2-source', Select2Source::class);
                $router->post(Select2Submitter::class, '/select2-submitter', Select2Submitter::class);
                $router->post(Select2Repository::class, '/select2-repository', Select2Repository::class);
                $router->get(SourcePage::class, '/source/{xref}{/slug}', SourcePage::class);
                $router->get(SubmissionPage::class, '/submission/{xref}{/slug}', SubmissionPage::class);
                $router->get(SubmitterPage::class, '/submitter/{xref}{/slug}', SubmitterPage::class);
                $router->get(TreePageBlock::class, '/tree-page-block', TreePageBlock::class);
                $router->get('example', '/…');
            });

            // Match module routes, with and without a tree.
            $router->get('module-tree', '/module/{module}/{action}/{tree}', ModuleAction::class)
                ->allows(RequestMethodInterface::METHOD_POST);
            $router->get('module-no-tree', '/module/{module}/{action}', ModuleAction::class)
                ->allows(RequestMethodInterface::METHOD_POST);
            // Generate module routes only. The router cannot distinguish a private tree from no tree.
            $router->get('module', '/module/{module}/{action}{/tree}', null);

            $router->get(HelpText::class, '/help/{topic}', HelpText::class);
            $router->post(SelectLanguage::class, '/language/{language}', SelectLanguage::class);
            $router->post(Logout::class, '/logout', Logout::class);
            $router->get(Ping::class, '/ping', Ping::class);
            $router->get(RobotsTxt::class, '/robots.txt', RobotsTxt::class);
            $router->post(SelectTheme::class, '/theme/{theme}', SelectTheme::class);
            $router->get(HomePage::class, '/', HomePage::class);

            // Some URL rewrite configurations will pass everything not in /public to index.php
            $router->get(AppleTouchIconPng::class, '/apple-touch-icon.png', AppleTouchIconPng::class);
            $router->get(FaviconIco::class, '/favicon.ico', FaviconIco::class);
        });
    }
}
