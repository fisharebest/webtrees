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

use Fisharebest\Webtrees\Http\Middleware\Router;
use Fisharebest\Webtrees\Http\RequestHandlers\DeleteUser;
use Fisharebest\Webtrees\Http\RequestHandlers\LoginAction;
use Fisharebest\Webtrees\Http\RequestHandlers\LoginPage;
use Fisharebest\Webtrees\Http\RequestHandlers\Logout;
use Fisharebest\Webtrees\Http\RequestHandlers\MasqueradeAsUser;
use Fisharebest\Webtrees\Http\RequestHandlers\ModuleAction;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordRequestAction;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordRequestPage;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordResetAction;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordResetPage;
use Fisharebest\Webtrees\Http\RequestHandlers\Ping;
use Fisharebest\Webtrees\Http\RequestHandlers\PrivacyPolicy;
use Fisharebest\Webtrees\Http\RequestHandlers\RegisterAction;
use Fisharebest\Webtrees\Http\RequestHandlers\RegisterPage;
use Fisharebest\Webtrees\Http\RequestHandlers\SelectLanguage;
use Fisharebest\Webtrees\Http\RequestHandlers\SelectTheme;
use Fisharebest\Webtrees\Http\RequestHandlers\VerifyEmail;

/** @var Tree|null $tree */

$tree = app(Tree::class);

/** @var Router $router */
$router = app(Router::class);

// Admin routes.
if (Auth::isAdmin()) {
    $router->get('admin-control-panel', '/admin-control-panel', 'Admin\ControlPanelController::controlPanel');
    $router->get('admin-fix-level-0-media', '/admin-fix-level-0-media', 'Admin\FixLevel0MediaController::fixLevel0Media');
    $router->post('admin-fix-level-0-media-action', '/admin-fix-level-0-media-action', 'Admin\FixLevel0MediaController::fixLevel0MediaAction');
    $router->get('admin-fix-level-0-media-data', '/admin-fix-level-0-media-data', 'Admin\FixLevel0MediaController::fixLevel0MediaData');
    $router->get('admin-webtrees1-thumbs', '/admin-webtrees1-thumbs', 'Admin\ImportThumbnailsController::webtrees1Thumbnails');
    $router->post('admin-webtrees1-thumbs-action', '/admin-webtrees1-thumbs-action', 'Admin\ImportThumbnailsController::webtrees1ThumbnailsAction');
    $router->get('admin-webtrees1-thumbs-data', '/admin-webtrees1-thumbs-data', 'Admin\ImportThumbnailsController::webtrees1ThumbnailsData');
    $router->get('modules', '/modules', 'Admin\ModuleController::list');
    $router->post('modules-update', '/modules', 'Admin\ModuleController::update');
    $router->get('analytics', '/analytics', 'Admin\ModuleController::listAnalytics');
    $router->post('analytics-update', '/analytics', 'Admin\ModuleController::updateAnalytics');
    $router->get('blocks', '/blocks', 'Admin\ModuleController::listBlocks');
    $router->post('blocks-update', '/blocks', 'Admin\ModuleController::updateBlocks');
    $router->get('charts', '/charts', 'Admin\ModuleController::listCharts');
    $router->post('charts-update', '/charts', 'Admin\ModuleController::updateCharts');
    $router->get('lists', '/lists', 'Admin\ModuleController::listLists');
    $router->post('lists-update', '/lists', 'Admin\ModuleController::updateLists');
    $router->get('footers', '/footers', 'Admin\ModuleController::listFooters');
    $router->post('footers-update','footers', 'Admin\ModuleController::updateFooters');
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
    $router->get('admin-media', '/admin-media', 'Admin\MediaController::index');
    $router->get('admin-media-data', '/admin-media-data', 'Admin\MediaController::data');
    $router->post('admin-media-delete', '/admin-media-delete', 'Admin\MediaController::delete');
    $router->get('admin-media-upload', '/admin-media-upload', 'Admin\MediaController::upload');
    $router->post('admin-media-upload-action', '/admin-media-upload', 'Admin\MediaController::uploadAction');
    $router->get('upgrade', '/upgrade', 'Admin\UpgradeController::wizard');
    $router->post('upgrade-action', '/upgrade', 'Admin\UpgradeController::step');
    $router->get('admin-users', '/admin-users', 'Admin\UsersController::index');
    $router->get('admin-users-data', '/admin-users-data', 'Admin\UsersController::data');
    $router->get('admin-users-create', '/admin-users-create', 'Admin\UsersController::create');
    $router->post('admin-users-create-action', '/admin-users-create', 'Admin\UsersController::save');
    $router->get('admin-users-edit', '/admin-users-edit', 'Admin\UsersController::edit');
    $router->post('admin-users-update', '/admin-users-edit', 'Admin\UsersController::update');
    $router->get('admin-users-cleanup', '/admin-users-cleanup', 'Admin\UsersController::cleanup');
    $router->post('admin-users-cleanup-action', '/admin-users-cleanup', 'Admin\UsersController::cleanupAction');
    $router->get('admin-clean-data', '/admin-clean-data', 'AdminSiteController::cleanData');
    $router->post('admin-clean-data-action', '/admin-clean-data', 'AdminSiteController::cleanDataAction');
    $router->get('admin-site-preferences', '/admin-site-preferences', 'AdminSiteController::preferencesForm');
    $router->post('admin-site-preferences-update', '/admin-site-preferences', 'AdminSiteController::preferencesSave');
    $router->get('admin-site-mail', '/admin-site-mail', 'AdminSiteController::mailForm');
    $router->post('admin-site-mail-update', '/admin-site-mail', 'AdminSiteController::mailSave');
    $router->get('admin-site-registration', '/admin-site-registration', 'AdminSiteController::registrationForm');
    $router->post('admin-site-registration-update', '/admin-site-registration', 'AdminSiteController::registrationSave');
    $router->get('admin-site-logs', '/admin-site-logs', 'AdminSiteController::logs');
    $router->get('admin-site-logs-data', '/admin-site-logs-data', 'AdminSiteController::logsData');
    $router->post('admin-site-logs-delete', '/admin-site-logs-delete', 'AdminSiteController::logsDelete');
    $router->get('admin-site-logs-export', '/admin-site-logs-export', 'AdminSiteController::logsExport');
    $router->get('admin-site-information', '/admin-site-information', 'AdminSiteController::serverInformation');
    $router->get('admin-trees', '/admin-trees', 'AdminTreesController::index');
    $router->post('admin-trees-create', '/admin-trees-create', 'AdminTreesController::create');
    $router->post('admin-trees-default', '/admin-trees-default', 'AdminTreesController::setDefault');
    $router->post('admin-trees-delete', '/admin-trees-delete', 'AdminTreesController::delete');
    $router->post('admin-trees-sync', '/admin-trees-sync', 'AdminTreesController::synchronize');
    $router->get('admin-trees-merge', '/admin-trees-merge', 'AdminTreesController::merge');
    $router->post('admin-trees-merge-action', '/admin-trees-merge', 'AdminTreesController::mergeAction');
    $router->get('user-page-default-edit', '/user-page-default-edit', 'HomePageController::userPageDefaultEdit');
    $router->post('user-page-default-update', '/user-page-default-update', 'HomePageController::userPageDefaultUpdate');
    $router->get('user-page-user-edit', '/user-page-user-edit', 'HomePageController::userPageUserEdit');
    $router->post('user-page-user-update', '/user-page-user-update', 'HomePageController::userPageUserUpdate');
    $router->get('unused-media-thumbnail', '/unused-media-thumbnail', 'MediaFileController::unusedMediaThumbnail');
    $router->get('broadcast', '/broadcast', 'MessageController::broadcastPage');
    $router->post('broadcast-action', '/broadcast', 'MessageController::broadcastAction');
    $router->post('delete-user', '/delete-user', DeleteUser::class);
    $router->post('masquerade', '/masquerade', MasqueradeAsUser::class);
}

// Manager routes.
if ($tree instanceof Tree && Auth::isManager($tree)) {
    $router->get('admin-control-panel-manager', '/admin-control-panel-manager', 'Admin\ControlPanelController::controlPanelManager');
    $router->get('admin-changes-log', '/admin-changes-log', 'Admin\ChangesLogController::changesLog');
    $router->get('admin-changes-log-data', '/admin-changes-log-data', 'Admin\ChangesLogController::changesLogData');
    $router->get('admin-changes-log-download', '/admin-changes-log-download', 'Admin\ChangesLogController::changesLogDownload');
    $router->get('admin-trees-manager', '/admin-trees', 'AdminTreesController::index');
    $router->get('admin-trees-check', '/admin-trees-check', 'AdminTreesController::check');
    $router->get('admin-trees-duplicates', '/admin-trees-duplicates', 'AdminTreesController::duplicates');
    $router->get('admin-trees-export', '/admin-trees-export', 'AdminTreesController::export');
    $router->get('admin-trees-download', '/admin-trees-download', 'AdminTreesController::exportClient');
    $router->post('admin-trees-export-action', '/admin-trees-export', 'AdminTreesController::exportServer');
    $router->get('admin-trees-import', '/admin-trees-import', 'AdminTreesController::importForm');
    $router->post('admin-trees-import-action', '/admin-trees-import', 'AdminTreesController::importAction');
    $router->get('admin-trees-places', '/admin-trees-places', 'AdminTreesController::places');
    $router->post('admin-trees-places-action', '/admin-trees-places', 'AdminTreesController::placesAction');
    $router->get('admin-trees-preferences', '/admin-trees-preferences', 'AdminTreesController::preferences');
    $router->post('admin-trees-preferences-update', '/admin-trees-preferences', 'AdminTreesController::preferencesUpdate');
    $router->get('admin-trees-renumber', '/admin-trees-renumber', 'AdminTreesController::renumber');
    $router->post('admin-trees-renumber-action', '/admin-trees-renumber', 'AdminTreesController::renumberAction');
    $router->get('admin-trees-unconnected', '/admin-trees-unconnected', 'AdminTreesController::unconnected');
    $router->get('tree-page-default-edit', '/tree-page-default-edit', 'HomePageController::treePageDefaultEdit');
    $router->post('tree-page-default-update', '/tree-page-default-update', 'HomePageController::treePageDefaultUpdate');
    $router->get('tree-page-edit', '/tree-page-edit', 'HomePageController::treePageEdit');
    $router->post('import', '/import', 'GedcomFileController::import');
    $router->post('tree-page-update', '/tree-page-update', 'HomePageController::treePageUpdate');
    $router->get('merge-records', '/merge-records', 'AdminController::mergeRecords');
    $router->post('merge-records-update', '/merge-records', 'AdminController::mergeRecordsAction');
    $router->get('tree-page-block-edit', '/tree-page-block-edit', 'HomePageController::treePageBlockEdit');
    $router->post('tree-page-block-update', '/tree-page-block-edit', 'HomePageController::treePageBlockUpdate');
    $router->get('tree-preferences', '/tree-preferences', 'AdminController::treePreferencesEdit');
    $router->post('tree-preferences-update', '/tree-preferences', 'AdminController::treePreferencesUpdate');
    $router->get('tree-privacy', '/tree-privacy', 'AdminController::treePrivacyEdit');
    $router->post('tree-privacy-update', '/tree-privacy', 'AdminController::treePrivacyUpdate');
}

// Moderator routes.
if ($tree instanceof Tree && $tree->getPreference('imported') === '1' && Auth::isModerator($tree)) {
    $router->get('show-pending', '/show-pending', 'PendingChangesController::showChanges');
    $router->post('accept-pending', '/accept-pending', 'PendingChangesController::acceptChange');
    $router->post('reject-pending', '/reject-pending', 'PendingChangesController::rejectChange');
    $router->post('accept-all-pending', '/accept-all-pending', 'PendingChangesController::acceptAllChanges');
    $router->post('reject-all-pending', '/reject-all-pending', 'PendingChangesController::rejectAllChanges');
}

// Editor routes.
if ($tree instanceof Tree && $tree->getPreference('imported') === '1' && Auth::isEditor($tree)) {
    $router->get('add-media-file', '/add-media-file', 'EditMediaController::addMediaFile');
    $router->post('add-media-file-update', '/add-media-file', 'EditMediaController::addMediaFileAction');
    $router->get('edit-media-file', '/edit-media-file', 'EditMediaController::editMediaFile');
    $router->post('edit-media-file-update', '/edit-media-file', 'EditMediaController::editMediaFileAction');
    $router->get('create-media-object', '/create-media-object', 'EditMediaController::createMediaObject');
    $router->post('create-media-object-update', '/create-media-object', 'EditMediaController::createMediaObjectAction');
    $router->post('create-media-from-file', '/create-media-from-file', 'EditMediaController::createMediaObjectFromFileAction');
    $router->get('link-media-to-individual', '/link-media-to-individual', 'EditMediaController::linkMediaToIndividual');
    $router->get('link-media-to-family', '/link-media-to-family', 'EditMediaController::linkMediaToFamily');
    $router->get('link-media-to-source', '/link-media-to-source', 'EditMediaController::linkMediaToSource');
    $router->post('link-media-to-record', '/link-media-to-record', 'EditMediaController::linkMediaToRecordAction');
    $router->get('create-note-object', '/create-note-object', 'EditNoteController::createNoteObject');
    $router->post('create-note-object-action', '/create-note-object', 'EditNoteController::createNoteObjectAction');
    $router->get('edit-note-object', '/edit-note-object', 'EditNoteController::editNoteObject');
    $router->post('edit-note-object-action', '/edit-note-object', 'EditNoteController::updateNoteObject');
    $router->get('create-repository', '/create-repository', 'EditRepositoryController::createRepository');
    $router->post('create-repository-action', '/create-repository', 'EditRepositoryController::createRepositoryAction');
    $router->get('create-source', '/create-source', 'EditSourceController::createSource');
    $router->post('create-source-action', '/create-source', 'EditSourceController::createSourceAction');
    $router->get('create-submitter', '/create-submitter', 'EditSubmitterController::createSubmitter');
    $router->post('create-submitter-action', '/create-submitter', 'EditSubmitterController::createSubmitterAction');
    $router->get('reorder-children', '/reorder-children', 'EditFamilyController::reorderChildren');
    $router->post('reorder-children-action', '/reorder-children', 'EditFamilyController::reorderChildrenAction');
    $router->get('reorder-media', '/reorder-media', 'EditIndividualController::reorderMedia');
    $router->post('reorder-media-action', '/reorder-media', 'EditIndividualController::reorderMediaAction');
    $router->get('reorder-names', '/reorder-names', 'EditIndividualController::reorderNames');
    $router->post('reorder-names-action', '/reorder-names', 'EditIndividualController::reorderNamesAction');
    $router->get('reorder-spouses', '/reorder-spouses', 'EditIndividualController::reorderSpouses');
    $router->post('reorder-spouses-action', '/reorder-spouses', 'EditIndividualController::reorderSpousesAction');
    $router->get('edit-raw-record', '/edit-raw-record', 'EditGedcomRecordController::editRawRecord');
    $router->post('edit-raw-record-action', '/edit-raw-record', 'EditGedcomRecordController::editRawRecordAction');
    $router->get('edit-raw-fact', '/edit-raw-fact', 'EditGedcomRecordController::editRawFact');
    $router->post('edit-raw-fact-update', '/edit-raw-fact', 'EditGedcomRecordController::editRawFactAction');
    $router->post('copy-fact', '/copy-fact', 'EditGedcomRecordController::copyFact');
    $router->post('delete-fact', '/delete-fact', 'EditGedcomRecordController::deleteFact');
    $router->post('paste-fact', '/paste-fact', 'EditGedcomRecordController::pasteFact');
    $router->post('delete-record', '/delete-record', 'EditGedcomRecordController::deleteRecord');
    $router->get('add-fact', '/add-fact', 'EditGedcomRecordController::addFact');
    $router->get('edit-fact', '/edit-fact', 'EditGedcomRecordController::editFact');
    $router->post('update-fact', '/update-fact', 'EditGedcomRecordController::updateFact');
    $router->get('search-replace', '/search-replace', 'SearchController::replace');
    $router->post('search-replace-action', '/search-replace', 'SearchController::replaceAction');
    $router->get('add-child-to-family', '/add-child-to-family', 'EditFamilyController::addChild');
    $router->post('add-child-to-family-action', '/add-child-to-family', 'EditFamilyController::addChildAction');
    $router->get('add-spouse-to-family', '/add-spouse-to-family', 'EditFamilyController::addSpouse');
    $router->post('add-spouse-to-family-action', '/add-spouse-to-family', 'EditFamilyController::addSpouseAction');
    $router->get('change-family-members', '/change-family-members', 'EditFamilyController::changeFamilyMembers');
    $router->post('change-family-members-action', '/change-family-members', 'EditFamilyController::changeFamilyMembersAction');
    $router->get('add-child-to-individual', '/add-child-to-individual', 'EditIndividualController::addChild');
    $router->post('add-child-to-individual-action', '/add-child-to-individual', 'EditIndividualController::addChildAction');
    $router->get('add-parent-to-individual', '/add-parent-to-individual', 'EditIndividualController::addParent');
    $router->post('add-parent-to-individual-action', '/add-parent-to-individual', 'EditIndividualController::addParentAction');
    $router->get('add-spouse-to-individual', '/add-spouse-to-individual', 'EditIndividualController::addSpouse');
    $router->post('add-spouse-to-individual-action', '/add-spouse-to-individual', 'EditIndividualController::addSpouseAction');
    $router->get('add-unlinked-individual', '/add-unlinked-individual', 'EditIndividualController::addUnlinked');
    $router->post('add-unlinked-individual-action', '/add-unlinked-individual', 'EditIndividualController::addUnlinkedAction');
    $router->get('link-child-to-family', '/link-child-to-family', 'EditIndividualController::linkChildToFamily');
    $router->post('link-child-to-family-action', '/link-child-to-family', 'EditIndividualController::linkChildToFamilyAction');
    $router->get('link-spouse-to-individual', '/link-spouse-to-individual', 'EditIndividualController::linkSpouseToIndividual');
    $router->post('link-spouse-to-individual-action', '/link-spouse-to-individual', 'EditIndividualController::linkSpouseToIndividualAction');
    $router->get('edit-name', '/edit-name', 'EditIndividualController::editName');
    $router->post('edit-name-action', '/edit-name-update', 'EditIndividualController::editNameAction');
    $router->get('add-name', '/add-name', 'EditIndividualController::addName');
    $router->post('add-name-action', '/add-name-update', 'EditIndividualController::addNameAction');
}

// Member routes.
if ($tree instanceof Tree && $tree->getPreference('imported') === '1' && Auth::isMember($tree)) {
    $router->get('user-page', '/my-page', 'HomePageController::userPage');
    $router->get('user-page-block', '/user-page-block', 'HomePageController::userPageBlock');
    $router->get('user-page-edit', '/user-page-edit', 'HomePageController::userPageEdit');
    $router->post('user-page-update', '/user-page-update', 'HomePageController::userPageUpdate');
    $router->get('user-page-block-edit', '/user-page-block-edit', 'HomePageController::userPageBlockEdit');
    $router->post('user-page-block-update', '/user-page-block-edit', 'HomePageController::userPageBlockUpdate');
    $router->get('my-account', '/my-account', 'AccountController::edit');
    $router->post('my-account-update', '/my-account', 'AccountController::update');
    $router->post('delete-account', '/delete-account', 'AccountController::delete');
}

// Public routes (that need a tree).
if ($tree instanceof Tree && $tree->getPreference('imported') === '1') {
    $router->get('autocomplete-folder', '/autocomplete-folder', 'AutocompleteController::folder');
    $router->get('autocomplete-page', '/autocomplete-page', 'AutocompleteController::page');
    $router->get('autocomplete-place', '/autocomplete-place', 'AutocompleteController::place');
    $router->get('calendar', '/calendar', 'CalendarController::page');
    $router->get('calendar-events', '/calendar-events', 'CalendarController::calendar');
    $router->get('help-text', '/help-text', 'HelpTextController::helpText');
    $router->get('tree-page', '/tree-page', 'HomePageController::treePage');
    $router->get('tree-page-block', '/tree-page-block', 'HomePageController::treePageBlock');
    $router->get('media-thumbnail', '/media-thumbnail', 'MediaFileController::mediaThumbnail');
    $router->get('media-download', '/media-download', 'MediaFileController::mediaDownload');
    $router->get('family', '/family/{xref}{/slug}', 'FamilyController::show');
    $router->get('individual', '/individual/{xref}{/slug}', 'IndividualController::show');
    $router->get('individual-tab', '/individual-tab', 'IndividualController::tab');
    $router->get('media', '/media/{xref}{/slug}', 'MediaController::show');
    $router->get('contact', '/contact', 'MessageController::contactPage');
    $router->post('contact-action', '/contact', 'MessageController::contactAction');
    $router->get('message', '/message', 'MessageController::messagePage');
    $router->post('message-action', '/message', 'MessageController::messageAction');
    $router->get('note', '/note/{xref}{/slug}', 'NoteController::show');
    $router->get('source', '/source/{xref}{/slug}', 'SourceController::show');
    $router->get('record', '/record/{xref}{/slug}', 'GedcomRecordController::show');
    $router->get('repository', '/repository/{xref}{/slug}', 'RepositoryController::show');
    $router->get('report-list', '/report-list', 'ReportEngineController::reportList');
    $router->get('report-setup', '/report-setup', 'ReportEngineController::reportSetup');
    $router->get('report-run', '/report-run', 'ReportEngineController::reportRun');
    $router->post('accept-changes', '/accept-changes', 'PendingChangesController::acceptChanges');
    $router->post('reject-changes', '/reject-changes', 'PendingChangesController::rejectChanges');
    $router->post('accept-all-changes', '/accept-all-changes', 'PendingChangesController::acceptAllChanges');
    $router->post('reject-all-changes', '/reject-all-changes', 'PendingChangesController::rejectAllChanges');
    $router->post('select2-family', '/select2-family', 'AutocompleteController::select2Family');
    $router->post('select2-individual', '/select2-individual', 'AutocompleteController::select2Individual');
    $router->post('select2-media', '/select2-media', 'AutocompleteController::select2MediaObject');
    $router->post('select2-note', '/select2-note', 'AutocompleteController::select2Note');
    $router->post('select2-source', '/select2-source', 'AutocompleteController::select2Source');
    $router->post('select2-submitter', '/select2-submitter', 'AutocompleteController::select2Submitter');
    $router->post('select2-repository', '/select2-repository', 'AutocompleteController::select2Repository');
    $router->get('search-quick', '/search-quick', 'SearchController::quick');
    $router->get('search-advanced', '/search-advanced', 'SearchController::advanced');
    $router->get('search-general', '/search-general', 'SearchController::general');
    $router->get('search-phonetic', '/search-phonetic', 'SearchController::phonetic');
}

$router->get('login', '/login', LoginPage::class);
$router->post('login-action', '/login', LoginAction::class);
$router->get('logout', '/logout', Logout::class);
$router->post('logout', '/logout', Logout::class);
$router->get('register', '/register', RegisterPage::class);
$router->post('register-action', '/register', RegisterAction::class);
$router->get('verify', '/verify', VerifyEmail::class);
$router->get('password-request', '/password-request', PasswordRequestPage::class);
$router->post('password-request-action', '/password-request', PasswordRequestAction::class);
$router->get('password-reset', '/password-reset', PasswordResetPage::class);
$router->post('password-reset-action', '/password-reset', PasswordResetAction::class);
$router->post('language', '/language', SelectLanguage::class);
$router->post('theme', '/theme', SelectTheme::class);
$router->get('privacy-policy', '/privacy-policy', PrivacyPolicy::class);
$router->get('module', '/module', ModuleAction::class);
$router->post('module', '/module', ModuleAction::class);
$router->get('ping', '/ping', Ping::class);
