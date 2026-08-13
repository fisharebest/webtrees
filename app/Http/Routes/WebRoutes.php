<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Routes;

use Fisharebest\Webtrees\Http\Controllers\Account;
use Fisharebest\Webtrees\Http\Controllers\AccountDelete;
use Fisharebest\Webtrees\Http\Controllers\AddChildToFamily;
use Fisharebest\Webtrees\Http\Controllers\AddChildToIndividual;
use Fisharebest\Webtrees\Http\Controllers\AddMediaFile;
use Fisharebest\Webtrees\Http\Controllers\AddNewFact;
use Fisharebest\Webtrees\Http\Controllers\AddParentToIndividual;
use Fisharebest\Webtrees\Http\Controllers\AddSpouseToFamily;
use Fisharebest\Webtrees\Http\Controllers\AddSpouseToIndividual;
use Fisharebest\Webtrees\Http\Controllers\AddUnlinked;
use Fisharebest\Webtrees\Http\Controllers\AdminMediaFileDownload;
use Fisharebest\Webtrees\Http\Controllers\AdminMediaFileThumbnail;
use Fisharebest\Webtrees\Http\Controllers\AdsTxt;
use Fisharebest\Webtrees\Http\Controllers\AppAdsTxt;
use Fisharebest\Webtrees\Http\Controllers\AppleTouchIconPng;
use Fisharebest\Webtrees\Http\Controllers\AutoCompleteCitation;
use Fisharebest\Webtrees\Http\Controllers\AutoCompleteFolder;
use Fisharebest\Webtrees\Http\Controllers\AutoCompletePlace;
use Fisharebest\Webtrees\Http\Controllers\AutoCompleteSurname;
use Fisharebest\Webtrees\Http\Controllers\Broadcast;
use Fisharebest\Webtrees\Http\Controllers\ChangeFamilyMembers;
use Fisharebest\Webtrees\Http\Controllers\CheckForNewVersionNow;
use Fisharebest\Webtrees\Http\Controllers\CheckTree;
use Fisharebest\Webtrees\Http\Controllers\CleanDataFolder;
use Fisharebest\Webtrees\Http\Controllers\Contact;
use Fisharebest\Webtrees\Http\Controllers\ControlPanel;
use Fisharebest\Webtrees\Http\Controllers\CopyFact;
use Fisharebest\Webtrees\Http\Controllers\CreateLocation;
use Fisharebest\Webtrees\Http\Controllers\CreateMediaObject;
use Fisharebest\Webtrees\Http\Controllers\CreateMediaObjectFromFile;
use Fisharebest\Webtrees\Http\Controllers\CreateNote;
use Fisharebest\Webtrees\Http\Controllers\CreateRepository;
use Fisharebest\Webtrees\Http\Controllers\CreateSource;
use Fisharebest\Webtrees\Http\Controllers\CreateSubmission;
use Fisharebest\Webtrees\Http\Controllers\CreateSubmitter;
use Fisharebest\Webtrees\Http\Controllers\CreateTree;
use Fisharebest\Webtrees\Http\Controllers\DataFixChoose;
use Fisharebest\Webtrees\Http\Controllers\DataFixData;
use Fisharebest\Webtrees\Http\Controllers\DataFixPage;
use Fisharebest\Webtrees\Http\Controllers\DataFixPreview;
use Fisharebest\Webtrees\Http\Controllers\DataFixSelect;
use Fisharebest\Webtrees\Http\Controllers\DataFixUpdate;
use Fisharebest\Webtrees\Http\Controllers\DataFixUpdateAll;
use Fisharebest\Webtrees\Http\Controllers\DeleteFact;
use Fisharebest\Webtrees\Http\Controllers\DeletePath;
use Fisharebest\Webtrees\Http\Controllers\DeleteRecord;
use Fisharebest\Webtrees\Http\Controllers\DeleteTreeAction;
use Fisharebest\Webtrees\Http\Controllers\DeleteUser;
use Fisharebest\Webtrees\Http\Controllers\EditFact;
use Fisharebest\Webtrees\Http\Controllers\EditMediaFile;
use Fisharebest\Webtrees\Http\Controllers\EditNote;
use Fisharebest\Webtrees\Http\Controllers\EditRawFact;
use Fisharebest\Webtrees\Http\Controllers\EditRawRecord;
use Fisharebest\Webtrees\Http\Controllers\EditRecord;
use Fisharebest\Webtrees\Http\Controllers\EmailPreferences;
use Fisharebest\Webtrees\Http\Controllers\EmptyClipboard;
use Fisharebest\Webtrees\Http\Controllers\ExportGedcomClient;
use Fisharebest\Webtrees\Http\Controllers\ExportGedcomPage;
use Fisharebest\Webtrees\Http\Controllers\ExportGedcomServer;
use Fisharebest\Webtrees\Http\Controllers\FamilyPage;
use Fisharebest\Webtrees\Http\Controllers\FaviconIco;
use Fisharebest\Webtrees\Http\Controllers\FindDuplicateRecords;
use Fisharebest\Webtrees\Http\Controllers\FixLevel0Media;
use Fisharebest\Webtrees\Http\Controllers\FixLevel0MediaData;
use Fisharebest\Webtrees\Http\Controllers\GedcomLoad;
use Fisharebest\Webtrees\Http\Controllers\GedcomRecordPage;
use Fisharebest\Webtrees\Http\Controllers\HeaderPage;
use Fisharebest\Webtrees\Http\Controllers\HelpText;
use Fisharebest\Webtrees\Http\Controllers\HomePage;
use Fisharebest\Webtrees\Http\Controllers\ImportGedcom;
use Fisharebest\Webtrees\Http\Controllers\IndividualPage;
use Fisharebest\Webtrees\Http\Controllers\LinkChildToFamily;
use Fisharebest\Webtrees\Http\Controllers\LinkMediaToFamilyModal;
use Fisharebest\Webtrees\Http\Controllers\LinkMediaToIndividualModal;
use Fisharebest\Webtrees\Http\Controllers\LinkMediaToRecordAction;
use Fisharebest\Webtrees\Http\Controllers\LinkMediaToSourceModal;
use Fisharebest\Webtrees\Http\Controllers\LinkSpouseToIndividual;
use Fisharebest\Webtrees\Http\Controllers\LocationPage;
use Fisharebest\Webtrees\Http\Controllers\Login;
use Fisharebest\Webtrees\Http\Controllers\Logout;
use Fisharebest\Webtrees\Http\Controllers\ManageMedia;
use Fisharebest\Webtrees\Http\Controllers\ManageMediaData;
use Fisharebest\Webtrees\Http\Controllers\ManageTrees;
use Fisharebest\Webtrees\Http\Controllers\MapDataAdd;
use Fisharebest\Webtrees\Http\Controllers\MapDataDelete;
use Fisharebest\Webtrees\Http\Controllers\MapDataDeleteUnused;
use Fisharebest\Webtrees\Http\Controllers\MapDataEdit;
use Fisharebest\Webtrees\Http\Controllers\MapDataExportCSV;
use Fisharebest\Webtrees\Http\Controllers\MapDataExportGeoJson;
use Fisharebest\Webtrees\Http\Controllers\MapDataImport;
use Fisharebest\Webtrees\Http\Controllers\MapDataList;
use Fisharebest\Webtrees\Http\Controllers\MapDataSave;
use Fisharebest\Webtrees\Http\Controllers\Masquerade;
use Fisharebest\Webtrees\Http\Controllers\MediaFileDownload;
use Fisharebest\Webtrees\Http\Controllers\MediaFileThumbnail;
use Fisharebest\Webtrees\Http\Controllers\MediaPage;
use Fisharebest\Webtrees\Http\Controllers\MergeFacts;
use Fisharebest\Webtrees\Http\Controllers\MergeRecords;
use Fisharebest\Webtrees\Http\Controllers\MergeTrees;
use Fisharebest\Webtrees\Http\Controllers\Message;
use Fisharebest\Webtrees\Http\Controllers\MessageSelect;
use Fisharebest\Webtrees\Http\Controllers\ModuleDeleteSettings;
use Fisharebest\Webtrees\Http\Controllers\ModulesAll;
use Fisharebest\Webtrees\Http\Controllers\ModulesAnalytics;
use Fisharebest\Webtrees\Http\Controllers\ModulesBlocks;
use Fisharebest\Webtrees\Http\Controllers\ModulesCharts;
use Fisharebest\Webtrees\Http\Controllers\ModulesDataFixes;
use Fisharebest\Webtrees\Http\Controllers\ModulesFooters;
use Fisharebest\Webtrees\Http\Controllers\ModulesHistoricEvents;
use Fisharebest\Webtrees\Http\Controllers\ModulesLanguages;
use Fisharebest\Webtrees\Http\Controllers\ModulesLists;
use Fisharebest\Webtrees\Http\Controllers\ModulesMapAutocomplete;
use Fisharebest\Webtrees\Http\Controllers\ModulesMapGeoLocations;
use Fisharebest\Webtrees\Http\Controllers\ModulesMapLinks;
use Fisharebest\Webtrees\Http\Controllers\ModulesMapProviders;
use Fisharebest\Webtrees\Http\Controllers\ModulesMenus;
use Fisharebest\Webtrees\Http\Controllers\ModulesReports;
use Fisharebest\Webtrees\Http\Controllers\ModulesShares;
use Fisharebest\Webtrees\Http\Controllers\ModulesSidebars;
use Fisharebest\Webtrees\Http\Controllers\ModulesTabs;
use Fisharebest\Webtrees\Http\Controllers\ModulesThemes;
use Fisharebest\Webtrees\Http\Controllers\NotePage;
use Fisharebest\Webtrees\Http\Controllers\PasswordRequest;
use Fisharebest\Webtrees\Http\Controllers\PasswordReset;
use Fisharebest\Webtrees\Http\Controllers\PasteFact;
use Fisharebest\Webtrees\Http\Controllers\PendingChanges;
use Fisharebest\Webtrees\Http\Controllers\PendingChangesAcceptChange;
use Fisharebest\Webtrees\Http\Controllers\PendingChangesAcceptRecord;
use Fisharebest\Webtrees\Http\Controllers\PendingChangesAcceptTree;
use Fisharebest\Webtrees\Http\Controllers\PendingChangesLog;
use Fisharebest\Webtrees\Http\Controllers\PendingChangesLogData;
use Fisharebest\Webtrees\Http\Controllers\PendingChangesLogDelete;
use Fisharebest\Webtrees\Http\Controllers\PendingChangesLogDownload;
use Fisharebest\Webtrees\Http\Controllers\PendingChangesRejectChange;
use Fisharebest\Webtrees\Http\Controllers\PendingChangesRejectRecord;
use Fisharebest\Webtrees\Http\Controllers\PendingChangesRejectTree;
use Fisharebest\Webtrees\Http\Controllers\PhpInformation;
use Fisharebest\Webtrees\Http\Controllers\Ping;
use Fisharebest\Webtrees\Http\Controllers\Register;
use Fisharebest\Webtrees\Http\Controllers\RenumberTree;
use Fisharebest\Webtrees\Http\Controllers\ReorderChildren;
use Fisharebest\Webtrees\Http\Controllers\ReorderFamilies;
use Fisharebest\Webtrees\Http\Controllers\ReorderMedia;
use Fisharebest\Webtrees\Http\Controllers\ReorderMediaFiles;
use Fisharebest\Webtrees\Http\Controllers\ReorderNames;
use Fisharebest\Webtrees\Http\Controllers\ReportGenerate;
use Fisharebest\Webtrees\Http\Controllers\ReportList;
use Fisharebest\Webtrees\Http\Controllers\ReportSetup;
use Fisharebest\Webtrees\Http\Controllers\RepositoryPage;
use Fisharebest\Webtrees\Http\Controllers\RobotsTxt;
use Fisharebest\Webtrees\Http\Controllers\SearchAdvanced;
use Fisharebest\Webtrees\Http\Controllers\SearchGeneral;
use Fisharebest\Webtrees\Http\Controllers\SearchPhonetic;
use Fisharebest\Webtrees\Http\Controllers\SearchQuickAction;
use Fisharebest\Webtrees\Http\Controllers\SearchReplace;
use Fisharebest\Webtrees\Http\Controllers\SelectDefaultTree;
use Fisharebest\Webtrees\Http\Controllers\SelectLanguage;
use Fisharebest\Webtrees\Http\Controllers\SelectNewFact;
use Fisharebest\Webtrees\Http\Controllers\SelectTheme;
use Fisharebest\Webtrees\Http\Controllers\SharedNotePage;
use Fisharebest\Webtrees\Http\Controllers\SiteLogs;
use Fisharebest\Webtrees\Http\Controllers\SiteLogsData;
use Fisharebest\Webtrees\Http\Controllers\SiteLogsDelete;
use Fisharebest\Webtrees\Http\Controllers\SiteLogsDownload;
use Fisharebest\Webtrees\Http\Controllers\SitemapDataXml;
use Fisharebest\Webtrees\Http\Controllers\SitemapIndexXml;
use Fisharebest\Webtrees\Http\Controllers\SitemapXsl;
use Fisharebest\Webtrees\Http\Controllers\SitePreferences;
use Fisharebest\Webtrees\Http\Controllers\SiteRegistration;
use Fisharebest\Webtrees\Http\Controllers\SiteTags;
use Fisharebest\Webtrees\Http\Controllers\SourcePage;
use Fisharebest\Webtrees\Http\Controllers\SubmissionPage;
use Fisharebest\Webtrees\Http\Controllers\SubmitterPage;
use Fisharebest\Webtrees\Http\Controllers\SynchronizeTrees;
use Fisharebest\Webtrees\Http\Controllers\TomSelectFamily;
use Fisharebest\Webtrees\Http\Controllers\TomSelectIndividual;
use Fisharebest\Webtrees\Http\Controllers\TomSelectLocation;
use Fisharebest\Webtrees\Http\Controllers\TomSelectMediaObject;
use Fisharebest\Webtrees\Http\Controllers\TomSelectNote;
use Fisharebest\Webtrees\Http\Controllers\TomSelectPlace;
use Fisharebest\Webtrees\Http\Controllers\TomSelectRepository;
use Fisharebest\Webtrees\Http\Controllers\TomSelectSharedNote;
use Fisharebest\Webtrees\Http\Controllers\TomSelectSource;
use Fisharebest\Webtrees\Http\Controllers\TomSelectSubmission;
use Fisharebest\Webtrees\Http\Controllers\TomSelectSubmitter;
use Fisharebest\Webtrees\Http\Controllers\TreePage;
use Fisharebest\Webtrees\Http\Controllers\TreePageBlock;
use Fisharebest\Webtrees\Http\Controllers\TreePageBlockEdit;
use Fisharebest\Webtrees\Http\Controllers\TreePageDefault;
use Fisharebest\Webtrees\Http\Controllers\TreePageEdit;
use Fisharebest\Webtrees\Http\Controllers\TreePreferences;
use Fisharebest\Webtrees\Http\Controllers\TreePrivacy;
use Fisharebest\Webtrees\Http\Controllers\Unconnected;
use Fisharebest\Webtrees\Http\Controllers\UpgradeWizardConfirm;
use Fisharebest\Webtrees\Http\Controllers\UpgradeWizardPage;
use Fisharebest\Webtrees\Http\Controllers\UpgradeWizardStep;
use Fisharebest\Webtrees\Http\Controllers\UploadMedia;
use Fisharebest\Webtrees\Http\Controllers\UserAdd;
use Fisharebest\Webtrees\Http\Controllers\UserEdit;
use Fisharebest\Webtrees\Http\Controllers\UserListData;
use Fisharebest\Webtrees\Http\Controllers\UserListPage;
use Fisharebest\Webtrees\Http\Controllers\UserPage;
use Fisharebest\Webtrees\Http\Controllers\UserPageBlock;
use Fisharebest\Webtrees\Http\Controllers\UserPageBlockEdit;
use Fisharebest\Webtrees\Http\Controllers\UserPageDefaultEdit;
use Fisharebest\Webtrees\Http\Controllers\UserPageEdit;
use Fisharebest\Webtrees\Http\Controllers\UsersCleanup;
use Fisharebest\Webtrees\Http\Controllers\VerifyEmail;
use Fisharebest\Webtrees\Http\Middleware\AuthAdministrator;
use Fisharebest\Webtrees\Http\Middleware\AuthEditor;
use Fisharebest\Webtrees\Http\Middleware\AuthLoggedIn;
use Fisharebest\Webtrees\Http\Middleware\AuthManager;
use Fisharebest\Webtrees\Http\Middleware\AuthModerator;
use Fisharebest\Webtrees\Http\Middleware\AuthNotRobot;
use Fisharebest\Webtrees\Http\RequestHandlers\ModuleAction;
use Fisharebest\Webtrees\Http\Routing\RouteCollection;

/**
 * Routing table for web requests
 */
class WebRoutes
{
    public function load(RouteCollection $routes): void
    {
        // Admin routes.
        $routes->group('/admin', [AuthAdministrator::class], static function (RouteCollection $routes): void {
            $routes->add('', ControlPanel::class);
            $routes->add('/check-now', CheckForNewVersionNow::class);
            $routes->add('/broadcast/{to}', Broadcast::class);
            $routes->add('/clean', CleanDataFolder::class);
            $routes->add('/delete-path', DeletePath::class);
            $routes->add('/email', EmailPreferences::class);
            $routes->add('/fix-level-0-media', FixLevel0Media::class);
            $routes->add('/fix-level-0-media-data', FixLevel0MediaData::class);
            $routes->add('/information', PhpInformation::class);
            $routes->add('/logs', SiteLogs::class);
            $routes->add('/logs-data', SiteLogsData::class);
            $routes->add('/logs-delete', SiteLogsDelete::class);
            $routes->add('/logs-download', SiteLogsDownload::class);
            $routes->add('/masquerade/{user_id}', Masquerade::class);
            $routes->add('/media', ManageMedia::class);
            $routes->add('/media-data', ManageMediaData::class);
            $routes->add('/media-upload', UploadMedia::class);
            $routes->add('/media-file', AdminMediaFileDownload::class);
            $routes->add('/media-thumbnail', AdminMediaFileThumbnail::class);
            $routes->add('/trees/create', CreateTree::class);
            $routes->add('/trees/default/{tree}', SelectDefaultTree::class);
            $routes->add('/trees/delete/{tree}', DeleteTreeAction::class);
            $routes->add('/users-cleanup', UsersCleanup::class);
            $routes->add('/map-data-add{/parent_id}', MapDataAdd::class);
            $routes->add('/map-data-delete/{location_id}', MapDataDelete::class);
            $routes->add('/map-data-delete-unused', MapDataDeleteUnused::class);
            $routes->add('/map-data-edit/{location_id}', MapDataEdit::class);
            $routes->add('/map-data-csv{/parent_id}', MapDataExportCSV::class);
            $routes->add('/map-data-geojson{/parent_id}', MapDataExportGeoJson::class);
            $routes->add('/locations-import', MapDataImport::class);
            $routes->add('/map-data{/parent_id}', MapDataList::class);
            $routes->add('/map-data-update', MapDataSave::class);
            $routes->add('/module-delete-settings', ModuleDeleteSettings::class);
            $routes->add('/modules', ModulesAll::class);
            $routes->add('/analytics', ModulesAnalytics::class);
            $routes->add('/blocks', ModulesBlocks::class);
            $routes->add('/charts', ModulesCharts::class);
            $routes->add('/data-fixes', ModulesDataFixes::class);
            $routes->add('/footers', ModulesFooters::class);
            $routes->add('/historic-events', ModulesHistoricEvents::class);
            $routes->add('/lists', ModulesLists::class);
            $routes->add('/map-autocomplete', ModulesMapAutocomplete::class);
            $routes->add('/map-links', ModulesMapLinks::class);
            $routes->add('/map-providers', ModulesMapProviders::class);
            $routes->add('/map-searches', ModulesMapGeoLocations::class);
            $routes->add('/menus', ModulesMenus::class);
            $routes->add('/languages', ModulesLanguages::class);
            $routes->add('/reports', ModulesReports::class);
            $routes->add('/shares', ModulesShares::class);
            $routes->add('/sidebars', ModulesSidebars::class);
            $routes->add('/tabs', ModulesTabs::class);
            $routes->add('/themes', ModulesThemes::class);
            $routes->add('/upgrade', UpgradeWizardPage::class);
            $routes->add('/upgrade-confirm', UpgradeWizardConfirm::class);
            $routes->add('/upgrade-action', UpgradeWizardStep::class);
            $routes->add('/admin-users', UserListPage::class);
            $routes->add('/admin-users-data', UserListData::class);
            $routes->add('/admin-users-create', UserAdd::class);
            $routes->add('/admin-users-edit', UserEdit::class);
            $routes->add('/site-preferences', SitePreferences::class);
            $routes->add('/site-registration', SiteRegistration::class);
            $routes->add('/tags', SiteTags::class);
            $routes->add('/trees/default-blocks', TreePageDefault::class);
            $routes->add('/trees/merge', MergeTrees::class);
            $routes->add('/trees/sync', SynchronizeTrees::class);
            $routes->add('/users/delete/{user_id}', DeleteUser::class);
            $routes->add('/user-page-default-edit', UserPageDefaultEdit::class);
        });

        // Manager routes (multiple trees).
        $routes->group('/admin', [AuthManager::class], static function (RouteCollection $routes): void {
            $routes->add('/trees/manage/{tree}', ManageTrees::class);
        });

        // Manager routes.
        $routes->group('/tree/{tree}', [AuthManager::class], static function (RouteCollection $routes): void {
            $routes->add('/changes-log', PendingChangesLog::class);
            $routes->add('/changes-data', PendingChangesLogData::class);
            $routes->add('/changes-delete', PendingChangesLogDelete::class);
            $routes->add('/changes-download', PendingChangesLogDownload::class);
            $routes->add('/check', CheckTree::class);
            $routes->add('/data-fix', DataFixChoose::class);
            $routes->add('/data-fix', DataFixSelect::class);
            $routes->add('/data-fix/{data_fix}', DataFixPage::class);
            $routes->add('/data-fix/{data_fix}/update', DataFixUpdate::class);
            $routes->add('/data-fix/{data_fix}/update-all', DataFixUpdateAll::class);
            $routes->add('/data-fix/{data_fix}/data', DataFixData::class);
            $routes->add('/data-fix/{data_fix}/preview', DataFixPreview::class);
            $routes->add('/duplicates', FindDuplicateRecords::class);
            $routes->add('/export', ExportGedcomPage::class);
            $routes->add('/export-client', ExportGedcomClient::class);
            $routes->add('/export-server', ExportGedcomServer::class);
            $routes->add('/import', ImportGedcom::class);
            $routes->add('/merge-step1', MergeRecords::class);
            $routes->add('/merge-step2', MergeFacts::class);
            $routes->add('/preferences', TreePreferences::class);
            $routes->add('/renumber', RenumberTree::class);
            $routes->add('/tree-page-edit', TreePageEdit::class);
            $routes->add('/load', GedcomLoad::class);
            $routes->add('/tree-page-block-edit/{block_id}', TreePageBlockEdit::class);
            $routes->add('/privacy', TreePrivacy::class);
            $routes->add('/unconnected', Unconnected::class);
        });

        // Moderator routes.
        $routes->group('/tree/{tree}', [AuthModerator::class], static function (RouteCollection $routes): void {
            $routes->add('/accept', PendingChangesAcceptTree::class);
            $routes->add('/accept/{xref}', PendingChangesAcceptRecord::class);
            $routes->add('/accept/{xref}/{change}', PendingChangesAcceptChange::class);
            $routes->add('/pending', PendingChanges::class);
            $routes->add('/reject', PendingChangesRejectTree::class);
            $routes->add('/reject/{xref}', PendingChangesRejectRecord::class);
            $routes->add('/reject/{xref}/{change}', PendingChangesRejectChange::class);
        });

        // Editor routes.
        $routes->group('/tree/{tree}', [AuthEditor::class], static function (RouteCollection $routes): void {
            $routes->add('/autocomplete/citation', AutoCompleteCitation::class);
            $routes->add('/autocomplete/folder', AutoCompleteFolder::class);
            $routes->add('/autocomplete/place', AutoCompletePlace::class);
            $routes->add('/add-child-to-family/{xref}/{sex}', AddChildToFamily::class);
            $routes->add('/add-fact/{xref}/{fact}', AddNewFact::class);
            $routes->add('/add-fact/{xref}', SelectNewFact::class);
            $routes->add('/add-media-file/{xref}', AddMediaFile::class);
            $routes->add('/add-spouse-to-family/{xref}/{sex}', AddSpouseToFamily::class);
            $routes->add('/change-family-members', ChangeFamilyMembers::class);
            $routes->add('/create-location', CreateLocation::class);
            $routes->add('/create-media-object', CreateMediaObject::class);
            $routes->add('/create-media-from-file', CreateMediaObjectFromFile::class);
            $routes->add('/copy/{xref}/{fact_id}', CopyFact::class);
            $routes->add('/create-note-object', CreateNote::class);
            $routes->add('/create-repository', CreateRepository::class);
            $routes->add('/create-source', CreateSource::class);
            $routes->add('/create-submitter', CreateSubmitter::class);
            $routes->add('/create-submission', CreateSubmission::class);
            $routes->add('/delete/{xref}', DeleteRecord::class);
            $routes->add('/delete/{xref}/{fact_id}', DeleteFact::class);
            $routes->add('/edit-fact/{xref}/{fact_id}', EditFact::class);
            $routes->add('/edit-media-file/{xref}/{fact_id}', EditMediaFile::class);
            $routes->add('/edit-note-object/{xref}', EditNote::class);
            $routes->add('/edit-raw/{xref}/{fact_id}', EditRawFact::class);
            $routes->add('/edit-raw/{xref}', EditRawRecord::class);
            $routes->add('/link-media-to-family/{xref}', LinkMediaToFamilyModal::class);
            $routes->add('/link-media-to-individual/{xref}', LinkMediaToIndividualModal::class);
            $routes->add('/link-media-to-source/{xref}', LinkMediaToSourceModal::class);
            $routes->add('/link-media-to-record/{xref}', LinkMediaToRecordAction::class);
            $routes->add('/edit-record/{xref}', EditRecord::class);
            $routes->add('/paste-fact/{xref}', PasteFact::class);
            $routes->add('/reorder-children/{xref}', ReorderChildren::class);
            $routes->add('/reorder-media/{xref}', ReorderMedia::class);
            $routes->add('/reorder-media-files/{xref}', ReorderMediaFiles::class);
            $routes->add('/reorder-names/{xref}', ReorderNames::class);
            $routes->add('/reorder-spouses/{xref}', ReorderFamilies::class);
            $routes->add('/search-replace', SearchReplace::class);
            $routes->add('/add-child-to-individual/{xref}', AddChildToIndividual::class);
            $routes->add('/add-parent-to-individual/{xref}/{sex}', AddParentToIndividual::class);
            $routes->add('/add-spouse-to-individual/{xref}', AddSpouseToIndividual::class);
            $routes->add('/add-unlinked-individual', AddUnlinked::class);
            $routes->add('/link-child-to-family/{xref}', LinkChildToFamily::class);
            $routes->add('/link-spouse-to-individual/{xref}', LinkSpouseToIndividual::class);
        });

        // User routes with a tree.
        $routes->group('/tree/{tree}', [AuthLoggedIn::class], static function (RouteCollection $routes): void {
            $routes->add('/message-select', MessageSelect::class);
            $routes->add('/message-compose', Message::class);
            $routes->add('/my-page', UserPage::class);
            $routes->add('/my-page-block', UserPageBlock::class);
            $routes->add('/my-page-edit', UserPageEdit::class);
            $routes->add('/my-page-block-edit/{block_id}', UserPageBlockEdit::class);
        });

        // User routes without a tree.
        $routes->group('', [AuthLoggedIn::class], static function (RouteCollection $routes): void {
            $routes->add('/my-account{/tree}', Account::class);
            $routes->add('/my-account-delete', AccountDelete::class);
            $routes->add('/empty-clipboard', EmptyClipboard::class);
        });

        // Visitor routes - with an optional tree (for sites with no public trees).
        $routes->add('/login{/tree}', Login::class);
        $routes->add('/password-request{/tree}', PasswordRequest::class);
        $routes->add('/register{/tree}', Register::class);
        $routes->add('/password-reset/{token}{/tree}', PasswordReset::class);
        $routes->add('/verify/{username}/{token}{/tree}', VerifyEmail::class);

        // Visitor routes with a tree (robots allowed).
        $routes->group('/tree/{tree}', [], static function (RouteCollection $routes): void {
            $routes->add('/family/{xref}{/slug}', FamilyPage::class);
            $routes->add('/header/{xref}{/slug}', HeaderPage::class);
            $routes->add('/individual/{xref}{/slug}', IndividualPage::class);
            $routes->add('/location/{xref}{/slug}', LocationPage::class);
            $routes->add('/media-thumbnail', MediaFileThumbnail::class);
            $routes->add('/media-download', MediaFileDownload::class);
            $routes->add('/media/{xref}{/slug}', MediaPage::class);
            $routes->add('/note/{xref}{/slug}', NotePage::class);
            $routes->add('/shared-note/{xref}{/slug}', SharedNotePage::class);
            $routes->add('/record/{xref}{/slug}', GedcomRecordPage::class);
            $routes->add('/repository/{xref}{/slug}', RepositoryPage::class);
            $routes->add('/source/{xref}{/slug}', SourcePage::class);
            $routes->add('/submission/{xref}{/slug}', SubmissionPage::class);
            $routes->add('/submitter/{xref}{/slug}', SubmitterPage::class);
            $routes->add('', TreePage::class);
            $routes->add('/tree-page-block', TreePageBlock::class);
            $routes->add('/…', 'example');
        });

        // Visitor routes with a tree (robots not allowed).
        $routes->group('/tree/{tree}', [AuthNotRobot::class], static function (RouteCollection $routes): void {
            $routes->add('/autocomplete/surname', AutoCompleteSurname::class);
            $routes->add('/contact', Contact::class);
            $routes->add('/report', ReportList::class);
            $routes->add('/report/{report}', ReportSetup::class);
            $routes->add('/report-run/{report}', ReportGenerate::class);
            $routes->add('/search-advanced', SearchAdvanced::class);
            $routes->add('/search-general', SearchGeneral::class);
            $routes->add('/search-phonetic', SearchPhonetic::class);
            $routes->add('/search-quick', SearchQuickAction::class);
            $routes->add('/tom-select-family', TomSelectFamily::class);
            $routes->add('/tom-select-individual', TomSelectIndividual::class);
            $routes->add('/tom-select-location', TomSelectLocation::class);
            $routes->add('/tom-select-media', TomSelectMediaObject::class);
            $routes->add('/tom-select-note', TomSelectNote::class);
            $routes->add('/tom-select-shared-note', TomSelectSharedNote::class);
            $routes->add('/tom-select-place', TomSelectPlace::class);
            $routes->add('/tom-select-source', TomSelectSource::class);
            $routes->add('/tom-select-submission', TomSelectSubmission::class);
            $routes->add('/tom-select-submitter', TomSelectSubmitter::class);
            $routes->add('/tom-select-repository', TomSelectRepository::class);
        });

        // Module routes - match with and without tree.
        $routes->add('/module/{module}/{action}{/tree}', ModuleAction::class);

        $routes->add('/help/{topic}', HelpText::class);
        $routes->add('/language/{language}', SelectLanguage::class);
        $routes->add('/logout', Logout::class);
        $routes->add('/ping', Ping::class);
        $routes->add('/theme/{theme}', SelectTheme::class);
        $routes->add('/', HomePage::class);

        // Special files, either dynamic or need to be in the root folder.
        $routes->add('/ads.txt', AdsTxt::class);
        $routes->add('/app-ads.txt', AppAdsTxt::class);
        $routes->add('/apple-touch-icon.png', AppleTouchIconPng::class);
        $routes->add('/favicon.ico', FaviconIco::class);
        $routes->add('/robots.txt', RobotsTxt::class);
        $routes->add('/sitemap.xsl', SitemapXsl::class);
        $routes->add('/sitemap.xml', SitemapIndexXml::class);
        $routes->add('/sitemap-{tree}-{type}-{page}.xml', SitemapDataXml::class);
    }
}
