<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\Http\Middleware\AuthModerator;
use Fisharebest\Webtrees\Http\RequestHandlers\AccountDelete;
use Fisharebest\Webtrees\Http\RequestHandlers\AccountEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\AccountUpdate;
use Fisharebest\Webtrees\Http\RequestHandlers\AddChildToFamilyAction;
use Fisharebest\Webtrees\Http\RequestHandlers\AddChildToFamilyPage;
use Fisharebest\Webtrees\Http\RequestHandlers\AddChildToIndividualAction;
use Fisharebest\Webtrees\Http\RequestHandlers\AddChildToIndividualPage;
use Fisharebest\Webtrees\Http\RequestHandlers\AddMediaFileAction;
use Fisharebest\Webtrees\Http\RequestHandlers\AddMediaFileModal;
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
use Fisharebest\Webtrees\Http\RequestHandlers\AdminMediaFileDownload;
use Fisharebest\Webtrees\Http\RequestHandlers\AdminMediaFileThumbnail;
use Fisharebest\Webtrees\Http\RequestHandlers\AppleTouchIconPng;
use Fisharebest\Webtrees\Http\RequestHandlers\AutoCompleteCitation;
use Fisharebest\Webtrees\Http\RequestHandlers\AutoCompleteFolder;
use Fisharebest\Webtrees\Http\RequestHandlers\AutoCompletePlace;
use Fisharebest\Webtrees\Http\RequestHandlers\AutoCompleteSurname;
use Fisharebest\Webtrees\Http\RequestHandlers\BroadcastAction;
use Fisharebest\Webtrees\Http\RequestHandlers\BroadcastPage;
use Fisharebest\Webtrees\Http\RequestHandlers\CalendarAction;
use Fisharebest\Webtrees\Http\RequestHandlers\CalendarEvents;
use Fisharebest\Webtrees\Http\RequestHandlers\CalendarPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ChangeFamilyMembersAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ChangeFamilyMembersPage;
use Fisharebest\Webtrees\Http\RequestHandlers\CheckTree;
use Fisharebest\Webtrees\Http\RequestHandlers\CleanDataFolder;
use Fisharebest\Webtrees\Http\RequestHandlers\ContactAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ContactPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ControlPanel;
use Fisharebest\Webtrees\Http\RequestHandlers\CopyFact;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateMediaObjectAction;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateMediaObjectFromFile;
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
use Fisharebest\Webtrees\Http\RequestHandlers\EditMediaFileAction;
use Fisharebest\Webtrees\Http\RequestHandlers\EditMediaFileModal;
use Fisharebest\Webtrees\Http\RequestHandlers\EditName;
use Fisharebest\Webtrees\Http\RequestHandlers\EditNoteAction;
use Fisharebest\Webtrees\Http\RequestHandlers\EditNotePage;
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
use Fisharebest\Webtrees\Http\RequestHandlers\FindDuplicateRecords;
use Fisharebest\Webtrees\Http\RequestHandlers\GedcomLoad;
use Fisharebest\Webtrees\Http\RequestHandlers\GedcomRecordPage;
use Fisharebest\Webtrees\Http\RequestHandlers\HeaderPage;
use Fisharebest\Webtrees\Http\RequestHandlers\HelpText;
use Fisharebest\Webtrees\Http\RequestHandlers\HomePage;
use Fisharebest\Webtrees\Http\RequestHandlers\ImportGedcomAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ImportGedcomPage;
use Fisharebest\Webtrees\Http\RequestHandlers\IndividualPage;
use Fisharebest\Webtrees\Http\RequestHandlers\LinkChildToFamilyAction;
use Fisharebest\Webtrees\Http\RequestHandlers\LinkChildToFamilyPage;
use Fisharebest\Webtrees\Http\RequestHandlers\LinkMediaToFamilyModal;
use Fisharebest\Webtrees\Http\RequestHandlers\LinkMediaToIndividualModal;
use Fisharebest\Webtrees\Http\RequestHandlers\LinkMediaToRecordAction;
use Fisharebest\Webtrees\Http\RequestHandlers\LinkMediaToSourceModal;
use Fisharebest\Webtrees\Http\RequestHandlers\LinkSpouseToIndividualAction;
use Fisharebest\Webtrees\Http\RequestHandlers\LinkSpouseToIndividualPage;
use Fisharebest\Webtrees\Http\RequestHandlers\LoginAction;
use Fisharebest\Webtrees\Http\RequestHandlers\LoginPage;
use Fisharebest\Webtrees\Http\RequestHandlers\Logout;
use Fisharebest\Webtrees\Http\RequestHandlers\ManageMediaAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ManageMediaData;
use Fisharebest\Webtrees\Http\RequestHandlers\ManageMediaPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ManageTrees;
use Fisharebest\Webtrees\Http\RequestHandlers\MapDataAdd;
use Fisharebest\Webtrees\Http\RequestHandlers\MapDataDelete;
use Fisharebest\Webtrees\Http\RequestHandlers\MapDataEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\MapDataExportCSV;
use Fisharebest\Webtrees\Http\RequestHandlers\MapDataExportGeoJson;
use Fisharebest\Webtrees\Http\RequestHandlers\MapDataImportAction;
use Fisharebest\Webtrees\Http\RequestHandlers\MapDataImportPage;
use Fisharebest\Webtrees\Http\RequestHandlers\MapDataList;
use Fisharebest\Webtrees\Http\RequestHandlers\MapDataSave;
use Fisharebest\Webtrees\Http\RequestHandlers\MapProviderAction;
use Fisharebest\Webtrees\Http\RequestHandlers\MapProviderPage;
use Fisharebest\Webtrees\Http\RequestHandlers\Masquerade;
use Fisharebest\Webtrees\Http\RequestHandlers\MediaFileDownload;
use Fisharebest\Webtrees\Http\RequestHandlers\MediaFileThumbnail;
use Fisharebest\Webtrees\Http\RequestHandlers\MediaPage;
use Fisharebest\Webtrees\Http\RequestHandlers\MergeFactsAction;
use Fisharebest\Webtrees\Http\RequestHandlers\MergeFactsPage;
use Fisharebest\Webtrees\Http\RequestHandlers\MergeRecordsAction;
use Fisharebest\Webtrees\Http\RequestHandlers\MergeRecordsPage;
use Fisharebest\Webtrees\Http\RequestHandlers\MergeTreesAction;
use Fisharebest\Webtrees\Http\RequestHandlers\MergeTreesPage;
use Fisharebest\Webtrees\Http\RequestHandlers\MessageAction;
use Fisharebest\Webtrees\Http\RequestHandlers\MessagePage;
use Fisharebest\Webtrees\Http\RequestHandlers\MessageSelect;
use Fisharebest\Webtrees\Http\RequestHandlers\ModuleAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModuleDeleteSettings;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesAllAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesAllPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesAnalyticsAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesAnalyticsPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesBlocksAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesBlocksPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesChartsAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesChartsPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesDataFixesAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesDataFixesPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesFootersAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesFootersPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesHistoricEventsAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesHistoricEventsPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesLanguagesAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesLanguagesPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesListsAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesListsPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesMenusAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesMenusPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesReportsAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesReportsPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesSidebarsAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesSidebarsPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesTabsAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesTabsPage;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesThemesAction;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesThemesPage;
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
use Fisharebest\Webtrees\Http\RequestHandlers\RenumberTreeAction;
use Fisharebest\Webtrees\Http\RequestHandlers\RenumberTreePage;
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
use Fisharebest\Webtrees\Http\RequestHandlers\SynchronizeTrees;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePage;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageBlock;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageBlockEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageBlockUpdate;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageDefaultEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageDefaultUpdate;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageUpdate;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePreferencesAction;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePreferencesPage;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePrivacyAction;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePrivacyPage;
use Fisharebest\Webtrees\Http\RequestHandlers\UnconnectedAction;
use Fisharebest\Webtrees\Http\RequestHandlers\UnconnectedPage;
use Fisharebest\Webtrees\Http\RequestHandlers\UploadMediaAction;
use Fisharebest\Webtrees\Http\RequestHandlers\UploadMediaPage;
use Fisharebest\Webtrees\Http\RequestHandlers\UserAddAction;
use Fisharebest\Webtrees\Http\RequestHandlers\UserAddPage;
use Fisharebest\Webtrees\Http\RequestHandlers\UserEditAction;
use Fisharebest\Webtrees\Http\RequestHandlers\UserEditPage;
use Fisharebest\Webtrees\Http\RequestHandlers\UserListData;
use Fisharebest\Webtrees\Http\RequestHandlers\UserListPage;
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

                $router->get(ControlPanel::class, '');
                $router->get(BroadcastPage::class, '/broadcast');
                $router->post(BroadcastAction::class, '/broadcast');
                $router->get(CleanDataFolder::class, '/clean');
                $router->post(DeletePath::class, '/delete-path');
                $router->get(EmailPreferencesPage::class, '/email');
                $router->post(EmailPreferencesAction::class, '/email');
                $router->get(PhpInformation::class, '/information');
                $router->get(SiteLogsPage::class, '/logs');
                $router->post(SiteLogsAction::class, '/logs');
                $router->get(SiteLogsData::class, '/logs-data');
                $router->post(SiteLogsDelete::class, '/logs-delete');
                $router->get(SiteLogsDownload::class, '/logs-download');
                $router->post(Masquerade::class, '/masquerade/{user_id}');
                $router->get(ManageMediaPage::class, '/media');
                $router->post(ManageMediaAction::class, '/media');
                $router->get(ManageMediaData::class, '/media-data');
                $router->get(UploadMediaPage::class, '/media-upload');
                $router->post(UploadMediaAction::class, '/media-upload');
                $router->get(AdminMediaFileDownload::class, '/media-file');
                $router->get(AdminMediaFileThumbnail::class, '/media-thumbnail');
                $router->get(CreateTreePage::class, '/trees/create');
                $router->post(CreateTreeAction::class, '/trees/create');
                $router->post(SelectDefaultTree::class, '/trees/default/{tree}');
                $router->post(DeleteTreeAction::class, '/trees/delete/{tree}');
                $router->get('admin-fix-level-0-media', '/fix-level-0-media', 'Admin\FixLevel0MediaController::fixLevel0Media');
                $router->post('admin-fix-level-0-media-action', '/fix-level-0-media', 'Admin\FixLevel0MediaController::fixLevel0MediaAction');
                $router->get('admin-fix-level-0-media-data', '/fix-level-0-media-data', 'Admin\FixLevel0MediaController::fixLevel0MediaData');
                $router->get('admin-webtrees1-thumbs', '/webtrees1-thumbs', 'Admin\ImportThumbnailsController::webtrees1Thumbnails');
                $router->post('admin-webtrees1-thumbs-action', '/webtrees1-thumbs', 'Admin\ImportThumbnailsController::webtrees1ThumbnailsAction');
                $router->get('admin-webtrees1-thumbs-data', '/webtrees1-thumbs-data', 'Admin\ImportThumbnailsController::webtrees1ThumbnailsData');
                $router->get(UsersCleanupPage::class, '/users-cleanup');
                $router->post(UsersCleanupAction::class, '/users-cleanup');
                $router->get(MapDataAdd::class, '/map-data-add{/parent_id}');
                $router->post(MapDataDelete::class, '/map-data-delete/{place_id}');
                $router->get(MapDataEdit::class, '/map-data-edit/{place_id}');
                $router->get(MapDataExportCSV::class, '/map-data-csv{/parent_id}');
                $router->get(MapDataExportGeoJson::class, '/map-data-geojson{/parent_id}');
                $router->get(MapDataImportPage::class, '/locations-import');
                $router->post(MapDataImportAction::class, '/locations-import');
                $router->get(MapDataList::class, '/map-data{/parent_id}');
                $router->post(MapDataSave::class, '/map-data-update');
                $router->get(MapProviderPage::class, '/map-provider');
                $router->post(MapProviderAction::class, '/map-provider');
                $router->post(ModuleDeleteSettings::class, '/module-delete-settings');
                $router->get(ModulesAllPage::class, '/modules');
                $router->post(ModulesAllAction::class, '/modules');
                $router->get(ModulesAnalyticsPage::class, '/analytics');
                $router->post(ModulesAnalyticsAction::class, '/analytics');
                $router->get(ModulesBlocksPage::class, '/blocks');
                $router->post(ModulesBlocksAction::class, '/blocks');
                $router->get(ModulesChartsPage::class, '/charts');
                $router->post(ModulesChartsAction::class, '/charts');
                $router->get(ModulesDataFixesPage::class, '/data-fixes');
                $router->post(ModulesDataFixesAction::class, '/data-fixes');
                $router->get(ModulesFootersPage::class, '/footers');
                $router->post(ModulesFootersAction::class, '/footers');
                $router->get(ModulesHistoricEventsPage::class, '/historic-events');
                $router->post(ModulesHistoricEventsAction::class, '/historic-events');
                $router->get(ModulesListsPage::class, '/lists');
                $router->post(ModulesListsAction::class, '/lists');
                $router->get(ModulesMenusPage::class, '/menus');
                $router->post(ModulesMenusAction::class, '/menus');
                $router->get(ModulesLanguagesPage::class, '/languages');
                $router->post(ModulesLanguagesAction::class, '/languages');
                $router->get(ModulesReportsPage::class, '/reports');
                $router->post(ModulesReportsAction::class, '/reports');
                $router->get(ModulesSidebarsPage::class, '/sidebars');
                $router->post(ModulesSidebarsAction::class, '/sidebars');
                $router->get(ModulesTabsPage::class, '/tabs');
                $router->post(ModulesTabsAction::class, '/tabs');
                $router->get(ModulesThemesPage::class, '/themes');
                $router->post(ModulesThemesAction::class, '/themes');
                $router->get('upgrade', '/upgrade', 'Admin\UpgradeController::wizard');
                $router->post('upgrade-confirm', '/upgrade-confirm', 'Admin\UpgradeController::confirm');
                $router->post('upgrade-action', '/upgrade', 'Admin\UpgradeController::step');
                $router->get(UserListPage::class, '/admin-users');
                $router->get(UserListData::class, '/admin-users-data');
                $router->get(UserAddPage::class, '/admin-users-create');
                $router->post(UserAddAction::class, '/admin-users-create');
                $router->get(UserEditPage::class, '/admin-users-edit');
                $router->post(UserEditAction::class, '/admin-users-edit');
                $router->get(SitePreferencesPage::class, '/site-preferences');
                $router->post(SitePreferencesAction::class, '/site-preferences');
                $router->get(SiteRegistrationPage::class, '/site-registration');
                $router->post(SiteRegistrationAction::class, '/site-registration');
                $router->get(TreePageDefaultEdit::class, '/trees/default-blocks');
                $router->post(TreePageDefaultUpdate::class, '/trees/default-blocks');
                $router->get(MergeTreesPage::class, '/trees/merge');
                $router->post(MergeTreesAction::class, '/trees/merge');
                $router->post(SynchronizeTrees::class, '/trees/sync');
                $router->post(DeleteUser::class, '/users/delete/{user_id}');
                $router->get(UserPageDefaultEdit::class, '/user-page-default-edit');
                $router->post(UserPageDefaultUpdate::class, '/user-page-default-update');

                // @deprecated since 2.0.8 - will be removed in 2.1.0
                $router->get('modules', '/modules', ModulesAllPage::class);
            });

            // Manager routes (multiple trees).
            $router->attach('', '/admin', static function (Map $router) {
                $router->extras([
                    'middleware' => [
                        AuthManager::class,
                    ],
                ]);

                $router->get(ManageTrees::class, '/trees/manage/{tree}');
            });

            // Manager routes.
            $router->attach('', '/tree/{tree}', static function (Map $router) {
                $router->extras([
                    'middleware' => [
                        AuthManager::class,
                    ],
                ]);

                $router->get(PendingChangesLogPage::class, '/changes-log');
                $router->post(PendingChangesLogAction::class, '/changes-log');
                $router->get(PendingChangesLogData::class, '/changes-data');
                $router->post(PendingChangesLogDelete::class, '/changes-delete');
                $router->get(PendingChangesLogDownload::class, '/changes-download');
                $router->get(CheckTree::class, '/check');
                $router->get(DataFixChoose::class, '/data-fix');
                $router->post(DataFixSelect::class, '/data-fix');
                $router->get(DataFixPage::class, '/data-fix/{data_fix}');
                $router->post(DataFixUpdate::class, '/data-fix/{data_fix}/update');
                $router->post(DataFixUpdateAll::class, '/data-fix/{data_fix}/update-all');
                $router->get(DataFixData::class, '/data-fix/{data_fix}/data');
                $router->get(DataFixPreview::class, '/data-fix/{data_fix}/preview');
                $router->get(FindDuplicateRecords::class, '/duplicates');
                $router->get(ExportGedcomPage::class, '/export');
                $router->post(ExportGedcomClient::class, '/export-client');
                $router->post(ExportGedcomServer::class, '/export-server');
                $router->get(ImportGedcomPage::class, '/import');
                $router->post(ImportGedcomAction::class, '/import');
                $router->get(MergeRecordsPage::class, '/merge-step1');
                $router->post(MergeRecordsAction::class, '/merge-step1');
                $router->get(MergeFactsPage::class, '/merge-step2');
                $router->post(MergeFactsAction::class, '/merge-step2');
                $router->get(TreePreferencesPage::class, '/preferences');
                $router->post(TreePreferencesAction::class, '/preferences');
                $router->get(RenumberTreePage::class, '/renumber');
                $router->post(RenumberTreeAction::class, '/renumber');
                $router->get(TreePageEdit::class, '/tree-page-edit');
                $router->post(GedcomLoad::class, '/load');
                $router->post(TreePageUpdate::class, '/tree-page-update');
                $router->get(TreePageBlockEdit::class, '/tree-page-block-edit');
                $router->post(TreePageBlockUpdate::class, '/tree-page-block-edit');
                $router->get(TreePrivacyPage::class, '/privacy');
                $router->post(TreePrivacyAction::class, '/privacy');
                $router->get(UnconnectedPage::class, '/unconnected');
                $router->post(UnconnectedAction::class, '/unconnected');
            });

            // Moderator routes.
            $router->attach('', '/tree/{tree}', static function (Map $router) {
                $router->extras([
                    'middleware' => [
                        AuthModerator::class,
                    ],
                ]);
                $router->post(PendingChangesAcceptTree::class, '/accept');
                $router->post(PendingChangesAcceptRecord::class, '/accept/{xref}');
                $router->post(PendingChangesAcceptChange::class, '/accept/{xref}/{change}');
                $router->get(PendingChanges::class, '/pending');
                $router->post(PendingChangesRejectTree::class, '/reject');
                $router->post(PendingChangesRejectRecord::class, '/reject/{xref}');
                $router->post(PendingChangesRejectChange::class, '/reject/{xref}/{change}');
            });

            // Editor routes.
            $router->attach('', '/tree/{tree}', static function (Map $router) {
                $router->extras([
                    'middleware' => [
                        AuthEditor::class,
                    ],
                ]);

                $router->get(AutoCompleteCitation::class, '/autocomplete/citation/{query}');
                $router->get(AutoCompleteFolder::class, '/autocomplete/folder/{query}');
                $router->get(AutoCompletePlace::class, '/autocomplete/place/{query}');
                $router->get(AutoCompleteSurname::class, '/autocomplete/surname/{query}');
                $router->get(AddChildToFamilyPage::class, '/add-child-to-family');
                $router->post(AddChildToFamilyAction::class, '/add-child-to-family');
                $router->get(AddNewFact::class, '/add-fact/{xref}/{fact}');
                $router->post(SelectNewFact::class, '/add-fact/{xref}');
                $router->get(AddMediaFileModal::class, '/add-media-file/{xref}');
                $router->post(AddMediaFileAction::class, '/add-media-file/{xref}');
                $router->get(AddName::class, '/add-name');
                $router->get(AddSpouseToFamilyPage::class, '/add-spouse-to-family');
                $router->post(AddSpouseToFamilyAction::class, '/add-spouse-to-family');
                $router->get(ChangeFamilyMembersPage::class, '/change-family-members');
                $router->post(ChangeFamilyMembersAction::class, '/change-family-members');
                $router->get(CreateMediaObjectModal::class, '/create-media-object');
                $router->post(CreateMediaObjectAction::class, '/create-media-object');
                $router->post(CreateMediaObjectFromFile::class, '/create-media-from-file');
                $router->post(CopyFact::class, '/copy/{xref}/{fact_id}');
                $router->get(CreateNoteModal::class, '/create-note-object');
                $router->post(CreateNoteAction::class, '/create-note-object');
                $router->get(CreateRepositoryModal::class, '/create-repository');
                $router->post(CreateRepositoryAction::class, '/create-repository');
                $router->get(CreateSourceModal::class, '/create-source');
                $router->post(CreateSourceAction::class, '/create-source');
                $router->get(CreateSubmitterModal::class, '/create-submitter');
                $router->post(CreateSubmitterAction::class, '/create-submitter');
                $router->post(DeleteRecord::class, '/delete/{xref}');
                $router->post(DeleteFact::class, '/delete/{xref}/{fact_id}');
                $router->get(EditFactPage::class, '/edit-fact/{xref}/{fact_id}');
                $router->post(EditFactAction::class, '/update-fact/{xref}{/fact_id}');
                $router->get(EditMediaFileModal::class, '/edit-media-file/{xref}/{fact_id}');
                $router->post(EditMediaFileAction::class, '/edit-media-file/{xref}/{fact_id}');
                $router->get(EditNotePage::class, '/edit-note-object/{xref}');
                $router->post(EditNoteAction::class, '/edit-note-object/{xref}');
                $router->get(EditRawFactPage::class, '/edit-raw/{xref}/{fact_id}');
                $router->post(EditRawFactAction::class, '/edit-raw/{xref}/{fact_id}');
                $router->get(EditRawRecordPage::class, '/edit-raw/{xref}');
                $router->post(EditRawRecordAction::class, '/edit-raw/{xref}');
                $router->get(LinkMediaToFamilyModal::class, '/link-media-to-family/{xref}');
                $router->get(LinkMediaToIndividualModal::class, '/link-media-to-individual/{xref}');
                $router->get(LinkMediaToSourceModal::class, '/link-media-to-source/{xref}');
                $router->post(LinkMediaToRecordAction::class, '/link-media-to-record/{xref}');
                $router->post(PasteFact::class, '/paste-fact/{xref}');
                $router->get(ReorderChildrenPage::class, '/reorder-children/{xref}');
                $router->post(ReorderChildrenAction::class, '/reorder-children/{xref}');
                $router->get(ReorderMediaPage::class, '/reorder-media/{xref}');
                $router->post(ReorderMediaAction::class, '/reorder-media/{xref}');
                $router->get(ReorderNamesPage::class, '/reorder-names/{xref}');
                $router->post(ReorderNamesAction::class, '/reorder-names/{xref}');
                $router->get(ReorderFamiliesPage::class, '/reorder-spouses/{xref}');
                $router->post(ReorderFamiliesAction::class, '/reorder-spouses/{xref}');
                $router->get(SearchReplacePage::class, '/search-replace');
                $router->post(SearchReplaceAction::class, '/search-replace');
                $router->get(AddChildToIndividualPage::class, '/add-child-to-individual');
                $router->post(AddChildToIndividualAction::class, '/add-child-to-individual');
                $router->get(AddParentToIndividualPage::class, '/add-parent-to-individual');
                $router->post(AddParentToIndividualAction::class, '/add-parent-to-individual');
                $router->get(AddSpouseToIndividualPage::class, '/add-spouse-to-individual');
                $router->post(AddSpouseToIndividualAction::class, '/add-spouse-to-individual');
                $router->get(AddUnlinkedPage::class, '/add-unlinked-individual');
                $router->post(AddUnlinkedAction::class, '/add-unlinked-individual');
                $router->get(LinkChildToFamilyPage::class, '/link-child-to-family');
                $router->post(LinkChildToFamilyAction::class, '/link-child-to-family');
                $router->get(LinkSpouseToIndividualPage::class, '/link-spouse-to-individual');
                $router->post(LinkSpouseToIndividualAction::class, '/link-spouse-to-individual');
                $router->get(EditName::class, '/edit-name/{xref}/{fact_id}');
            });

            // User routes with a tree.
            $router->attach('', '/tree/{tree}', static function (Map $router) {
                $router->extras([
                    'middleware' => [
                        AuthLoggedIn::class,
                    ],
                ]);

                $router->post(MessageSelect::class, '/message-select');
                $router->get(MessagePage::class, '/message-compose');
                $router->post(MessageAction::class, '/message-send');
                $router->get(UserPage::class, '/my-page');
                $router->get(UserPageBlock::class, '/my-page-block');
                $router->get(UserPageEdit::class, '/my-page-edit');
                $router->post(UserPageUpdate::class, '/my-page-edit');
                $router->get(UserPageBlockEdit::class, '/my-page-block-edit');
                $router->post(UserPageBlockUpdate::class, '/my-page-block-edit');
            });

            // User routes without a tree.
            $router->attach('', '', static function (Map $router) {
                $router->extras([
                    'middleware' => [
                        AuthLoggedIn::class,
                    ],
                ]);

                $router->get(AccountEdit::class, '/my-account{/tree}');
                $router->post(AccountUpdate::class, '/my-account{/tree}');
                $router->post(AccountDelete::class, '/my-account-delete');
            });

            // Visitor routes - with an optional tree (for sites with no public trees).
            $router->attach('', '', static function (Map $router) {
                $router->get(LoginPage::class, '/login{/tree}');
                $router->post(LoginAction::class, '/login{/tree}');
                $router->get(PasswordRequestPage::class, '/password-request{/tree}');
                $router->post(PasswordRequestAction::class, '/password-request{/tree}');
                $router->get(RegisterPage::class, '/register{/tree}');
                $router->post(RegisterAction::class, '/register{/tree}');
                $router->get(PasswordResetPage::class, '/password-reset/{token}{/tree}');
                $router->post(PasswordResetAction::class, '/password-reset/{token}{/tree}');
                $router->get(VerifyEmail::class, '/verify/{username}/{token}{/tree}');
            });

            // Visitor routes with a tree.
            $router->attach('', '/tree/{tree}', static function (Map $router) {
                $router->get(TreePage::class, '');
                $router->get(CalendarPage::class, '/calendar/{view}');
                $router->post(CalendarAction::class, '/calendar/{view}');
                $router->get(CalendarEvents::class, '/calendar-events/{view}');
                $router->get(ContactPage::class, '/contact');
                $router->post(ContactAction::class, '/contact');
                $router->get(FamilyPage::class, '/family/{xref}{/slug}');
                $router->get(HeaderPage::class, '/header/{xref}{/slug}');
                $router->get(IndividualPage::class, '/individual/{xref}{/slug}');
                $router->get(MediaFileThumbnail::class, '/media-thumbnail');
                $router->get(MediaFileDownload::class, '/media-download');
                $router->get(MediaPage::class, '/media/{xref}{/slug}');
                $router->get(NotePage::class, '/note/{xref}{/slug}');
                $router->get(GedcomRecordPage::class, '/record/{xref}{/slug}');
                $router->get(RepositoryPage::class, '/repository/{xref}{/slug}');
                $router->get(ReportListPage::class, '/report');
                $router->post(ReportListAction::class, '/report');
                $router->get(ReportSetupPage::class, '/report/{report}');
                $router->post(ReportSetupAction::class, '/report/{report}');
                $router->get(ReportGenerate::class, '/report-run/{report}');
                $router->get(SearchAdvancedPage::class, '/search-advanced');
                $router->post(SearchAdvancedAction::class, '/search-advanced');
                $router->get(SearchGeneralPage::class, '/search-general');
                $router->post(SearchGeneralAction::class, '/search-general');
                $router->get(SearchPhoneticPage::class, '/search-phonetic');
                $router->post(SearchPhoneticAction::class, '/search-phonetic');
                $router->post(SearchQuickAction::class, '/search-quick');
                $router->post(Select2Family::class, '/select2-family');
                $router->post(Select2Individual::class, '/select2-individual');
                $router->post(Select2MediaObject::class, '/select2-media');
                $router->post(Select2Note::class, '/select2-note');
                $router->post(Select2Place::class, '/select2-place');
                $router->post(Select2Source::class, '/select2-source');
                $router->post(Select2Submitter::class, '/select2-submitter');
                $router->post(Select2Repository::class, '/select2-repository');
                $router->get(SourcePage::class, '/source/{xref}{/slug}');
                $router->get(SubmissionPage::class, '/submission/{xref}{/slug}');
                $router->get(SubmitterPage::class, '/submitter/{xref}{/slug}');
                $router->get(TreePageBlock::class, '/tree-page-block');
                $router->get('example', '/…')
                    ->isRoutable(false);
            });

            // Match module routes, with and without a tree.
            $router->get('module-tree', '/module/{module}/{action}/{tree}', ModuleAction::class)
                ->allows(RequestMethodInterface::METHOD_POST);
            $router->get('module-no-tree', '/module/{module}/{action}', ModuleAction::class)
                ->allows(RequestMethodInterface::METHOD_POST);
            // Generate module routes only. The router cannot distinguish a private tree from no tree.
            $router->get('module', '/module/{module}/{action}{/tree}')
                ->isRoutable(false);

            $router->get(HelpText::class, '/help/{topic}');
            $router->post(SelectLanguage::class, '/language/{language}');
            $router->post(Logout::class, '/logout');
            $router->get(Ping::class, '/ping', Ping::class)
                ->allows(RequestMethodInterface::METHOD_HEAD);
            $router->get(RobotsTxt::class, '/robots.txt');
            $router->post(SelectTheme::class, '/theme/{theme}');
            $router->get(HomePage::class, '/');

            // Some URL rewrite configurations will pass everything not in /public to index.php
            $router->get(AppleTouchIconPng::class, '/apple-touch-icon.png');
            $router->get(FaviconIco::class, '/favicon.ico');
        });
    }
}
