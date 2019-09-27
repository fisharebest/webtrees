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

use Fisharebest\Webtrees\Http\RequestHandlers\DeleteUser;
use Fisharebest\Webtrees\Http\RequestHandlers\MasqueradeAsUser;
use Fisharebest\Webtrees\Http\RequestHandlers\ModuleAction;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordRequestAction;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordRequestForm;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordResetAction;
use Fisharebest\Webtrees\Http\RequestHandlers\PasswordResetForm;
use Fisharebest\Webtrees\Http\RequestHandlers\Ping;
use Fisharebest\Webtrees\Http\RequestHandlers\PrivacyPolicy;
use Fisharebest\Webtrees\Http\RequestHandlers\SelectLanguage;
use Fisharebest\Webtrees\Http\RequestHandlers\SelectTheme;

/** @var Tree|null $tree */
$tree = app(Tree::class);

/** @var @var Router $router */
$router = app(Router::class);

// Admin routes.
if (Auth::isAdmin()) {
    $router->get('admin-control-panel', 'Admin\ControlPanelController::controlPanel');
    $router->get('analytics-edit', 'Admin\AnalyticsController::edit');
    $router->post('analytics-edit', 'Admin\AnalyticsController::save');
    $router->get('admin-fix-level-0-media', 'Admin\FixLevel0MediaController::fixLevel0Media');
    $router->post('admin-fix-level-0-media-action', 'Admin\FixLevel0MediaController::fixLevel0MediaAction');
    $router->get('admin-fix-level-0-media-data', 'Admin\FixLevel0MediaController::fixLevel0MediaData');
    $router->get('admin-webtrees1-thumbs', 'Admin\ImportThumbnailsController::webtrees1Thumbnails');
    $router->post('admin-webtrees1-thumbs-action', 'Admin\ImportThumbnailsController::webtrees1ThumbnailsAction');
    $router->get('admin-webtrees1-thumbs-data', 'Admin\ImportThumbnailsController::webtrees1ThumbnailsData');
    $router->get('modules', 'Admin\ModuleController::list');
    $router->post('modules', 'Admin\ModuleController::update');
    $router->get('analytics', 'Admin\ModuleController::listAnalytics');
    $router->post('analytics', 'Admin\ModuleController::updateAnalytics');
    $router->get('blocks', 'Admin\ModuleController::listBlocks');
    $router->post('blocks', 'Admin\ModuleController::updateBlocks');
    $router->get('charts', 'Admin\ModuleController::listCharts');
    $router->post('charts', 'Admin\ModuleController::updateCharts');
    $router->get('lists', 'Admin\ModuleController::listLists');
    $router->post('lists', 'Admin\ModuleController::updateLists');
    $router->get('footers', 'Admin\ModuleController::listFooters');
    $router->post('footers', 'Admin\ModuleController::updateFooters');
    $router->get('history', 'Admin\ModuleController::listHistory');
    $router->post('history', 'Admin\ModuleController::updateHistory');
    $router->get('menus', 'Admin\ModuleController::listMenus');
    $router->post('menus', 'Admin\ModuleController::updateMenus');
    $router->get('languages', 'Admin\ModuleController::listLanguages');
    $router->post('languages', 'Admin\ModuleController::updateLanguages');
    $router->get('reports', 'Admin\ModuleController::listReports');
    $router->post('reports', 'Admin\ModuleController::updateReports');
    $router->get('sidebars', 'Admin\ModuleController::listSidebars');
    $router->post('sidebars', 'Admin\ModuleController::updateSidebars');
    $router->get('themes', 'Admin\ModuleController::listThemes');
    $router->post('themes', 'Admin\ModuleController::updateThemes');
    $router->get('tabs', 'Admin\ModuleController::listTabs');
    $router->post('tabs', 'Admin\ModuleController::updateTabs');
    $router->post('delete-module-settings', 'Admin\ModuleController::deleteModuleSettings');
    $router->get('map-data', 'Admin\LocationController::mapData');
    $router->get('map-data-edit', 'Admin\LocationController::mapDataEdit');
    $router->post('map-data-edit', 'Admin\LocationController::mapDataSave');
    $router->post('map-data-delete', 'Admin\LocationController::mapDataDelete');
    $router->get('locations-export', 'Admin\LocationController::exportLocations');
    $router->get('locations-import', 'Admin\LocationController::importLocations');
    $router->post('locations-import', 'Admin\LocationController::importLocationsAction');
    $router->post('locations-import-from-tree', 'Admin\LocationController::importLocationsFromTree');
    $router->get('map-provider', 'Admin\MapProviderController::mapProviderEdit');
    $router->post('map-provider', 'Admin\MapProviderController::mapProviderSave');
    $router->get('admin-media', 'Admin\MediaController::index');
    $router->get('admin-media-data', 'Admin\MediaController::data');
    $router->post('admin-media-delete', 'Admin\MediaController::delete');
    $router->get('admin-media-upload', 'Admin\MediaController::upload');
    $router->post('admin-media-upload', 'Admin\MediaController::uploadAction');
    $router->get('upgrade', 'Admin\UpgradeController::wizard');
    $router->post('upgrade', 'Admin\UpgradeController::step');
    $router->get('admin-users', 'Admin\UsersController::index');
    $router->get('admin-users-data', 'Admin\UsersController::data');
    $router->get('admin-users-create', 'Admin\UsersController::create');
    $router->post('admin-users-create', 'Admin\UsersController::save');
    $router->get('admin-users-edit', 'Admin\UsersController::edit');
    $router->post('admin-users-edit', 'Admin\UsersController::update');
    $router->get('admin-users-cleanup', 'Admin\UsersController::cleanup');
    $router->post('admin-users-cleanup', 'Admin\UsersController::cleanupAction');
    $router->get('admin-clean-data', 'AdminSiteController::cleanData');
    $router->post('admin-clean-data', 'AdminSiteController::cleanDataAction');
    $router->get('admin-site-preferences', 'AdminSiteController::preferencesForm');
    $router->post('admin-site-preferences', 'AdminSiteController::preferencesSave');
    $router->get('admin-site-mail', 'AdminSiteController::mailForm');
    $router->post('admin-site-mail', 'AdminSiteController::mailSave');
    $router->get('admin-site-registration', 'AdminSiteController::registrationForm');
    $router->post('admin-site-registration', 'AdminSiteController::registrationSave');
    $router->get('admin-site-logs', 'AdminSiteController::logs');
    $router->get('admin-site-logs-data', 'AdminSiteController::logsData');
    $router->post('admin-site-logs-delete', 'AdminSiteController::logsDelete');
    $router->get('admin-site-logs-export', 'AdminSiteController::logsExport');
    $router->get('admin-site-information', 'AdminSiteController::serverInformation');
    $router->get('admin-trees', 'AdminTreesController::index');
    $router->post('admin-trees-create', 'AdminTreesController::create');
    $router->post('admin-trees-default', 'AdminTreesController::setDefault');
    $router->post('admin-trees-delete', 'AdminTreesController::delete');
    $router->post('admin-trees-sync', 'AdminTreesController::synchronize');
    $router->get('admin-trees-merge', 'AdminTreesController::merge');
    $router->post('admin-trees-merge', 'AdminTreesController::mergeAction');
    $router->get('user-page-default-edit', 'HomePageController::userPageDefaultEdit');
    $router->post('user-page-default-update', 'HomePageController::userPageDefaultUpdate');
    $router->get('user-page-user-edit', 'HomePageController::userPageUserEdit');
    $router->post('user-page-user-update', 'HomePageController::userPageUserUpdate');
    $router->get('unused-media-thumbnail', 'MediaFileController::unusedMediaThumbnail');
    $router->get('broadcast', 'MessageController::broadcastPage');
    $router->post('broadcast', 'MessageController::broadcastAction');
    $router->post('delete-user', DeleteUser::class);
    $router->post('masquerade', MasqueradeAsUser::class);
}

// Manager routes.
if ($tree instanceof Tree && Auth::isManager($tree)) {
    $router->get('admin-control-panel-manager', 'Admin\ControlPanelController::controlPanelManager');
    $router->get('admin-changes-log', 'Admin\ChangesLogController::changesLog');
    $router->get('admin-changes-log-data', 'Admin\ChangesLogController::changesLogData');
    $router->get('admin-changes-log-download', 'Admin\ChangesLogController::changesLogDownload');
    $router->get('admin-trees', 'AdminTreesController::index');
    $router->get('admin-trees-check', 'AdminTreesController::check');
    $router->get('admin-trees-duplicates', 'AdminTreesController::duplicates');
    $router->get('admin-trees-export', 'AdminTreesController::export');
    $router->get('admin-trees-download', 'AdminTreesController::exportClient');
    $router->post('admin-trees-export', 'AdminTreesController::exportServer');
    $router->get('admin-trees-import', 'AdminTreesController::importForm');
    $router->post('admin-trees-import', 'AdminTreesController::importAction');
    $router->get('admin-trees-places', 'AdminTreesController::places');
    $router->post('admin-trees-places', 'AdminTreesController::placesAction');
    $router->get('admin-trees-preferences', 'AdminTreesController::preferences');
    $router->post('admin-trees-preferences', 'AdminTreesController::preferencesUpdate');
    $router->get('admin-trees-renumber', 'AdminTreesController::renumber');
    $router->post('admin-trees-renumber', 'AdminTreesController::renumberAction');
    $router->get('admin-trees-unconnected', 'AdminTreesController::unconnected');
    $router->get('tree-page-default-edit', 'HomePageController::treePageDefaultEdit');
    $router->post('tree-page-default-update', 'HomePageController::treePageDefaultUpdate');
    $router->get('tree-page-edit', 'HomePageController::treePageEdit');
    $router->post('import', 'GedcomFileController::import');
    $router->post('tree-page-update', 'HomePageController::treePageUpdate');
    $router->get('merge-records', 'AdminController::mergeRecords');
    $router->post('merge-records', 'AdminController::mergeRecordsAction');
    $router->get('tree-page-block-edit', 'HomePageController::treePageBlockEdit');
    $router->post('tree-page-block-edit', 'HomePageController::treePageBlockUpdate');
    $router->get('tree-preferences', 'AdminController::treePreferencesEdit');
    $router->post('tree-preferences', 'AdminController::treePreferencesUpdate');
    $router->get('tree-privacy', 'AdminController::treePrivacyEdit');
    $router->post('tree-privacy', 'AdminController::treePrivacyUpdate');
}

// Moderator routes.
if ($tree instanceof Tree && $tree->getPreference('imported') === '1' && Auth::isModerator($tree)) {
    $router->get('show-pending', 'PendingChangesController::showChanges');
    $router->post('accept-pending', 'PendingChangesController::acceptChange');
    $router->post('reject-pending', 'PendingChangesController::rejectChange');
    $router->post('accept-all-pending', 'PendingChangesController::acceptAllChanges');
    $router->post('reject-all-pending', 'PendingChangesController::rejectAllChanges');
}

// Editor routes.
if ($tree instanceof Tree && $tree->getPreference('imported') === '1' && Auth::isEditor($tree)) {
    $router->get('add-media-file', 'EditMediaController::addMediaFile');
    $router->post('add-media-file', 'EditMediaController::addMediaFileAction');
    $router->get('edit-media-file', 'EditMediaController::editMediaFile');
    $router->post('edit-media-file', 'EditMediaController::editMediaFileAction');
    $router->get('create-media-object', 'EditMediaController::createMediaObject');
    $router->post('create-media-object', 'EditMediaController::createMediaObjectAction');
    $router->post('create-media-from-file', 'EditMediaController::createMediaObjectFromFileAction');
    $router->get('link-media-to-individual', 'EditMediaController::linkMediaToIndividual');
    $router->get('link-media-to-family', 'EditMediaController::linkMediaToFamily');
    $router->get('link-media-to-source', 'EditMediaController::linkMediaToSource');
    $router->post('link-media-to-record', 'EditMediaController::linkMediaToRecordAction');
    $router->get('create-note-object', 'EditNoteController::createNoteObject');
    $router->post('create-note-object', 'EditNoteController::createNoteObjectAction');
    $router->get('edit-note-object', 'EditNoteController::editNoteObject');
    $router->post('edit-note-object', 'EditNoteController::updateNoteObject');
    $router->get('create-repository', 'EditRepositoryController::createRepository');
    $router->post('create-repository', 'EditRepositoryController::createRepositoryAction');
    $router->get('create-source', 'EditSourceController::createSource');
    $router->post('create-source', 'EditSourceController::createSourceAction');
    $router->get('create-submitter', 'EditSubmitterController::createSubmitter');
    $router->post('create-submitter', 'EditSubmitterController::createSubmitterAction');
    $router->get('reorder-children', 'EditFamilyController::reorderChildren');
    $router->post('reorder-children', 'EditFamilyController::reorderChildrenAction');
    $router->get('reorder-media', 'EditIndividualController::reorderMedia');
    $router->post('reorder-media', 'EditIndividualController::reorderMediaAction');
    $router->get('reorder-names', 'EditIndividualController::reorderNames');
    $router->post('reorder-names', 'EditIndividualController::reorderNamesAction');
    $router->get('reorder-spouses', 'EditIndividualController::reorderSpouses');
    $router->post('reorder-spouses', 'EditIndividualController::reorderSpousesAction');
    $router->get('edit-raw-record', 'EditGedcomRecordController::editRawRecord');
    $router->post('edit-raw-record', 'EditGedcomRecordController::editRawRecordAction');
    $router->get('edit-raw-fact', 'EditGedcomRecordController::editRawFact');
    $router->post('edit-raw-fact', 'EditGedcomRecordController::editRawFactAction');
    $router->post('copy-fact', 'EditGedcomRecordController::copyFact');
    $router->post('delete-fact', 'EditGedcomRecordController::deleteFact');
    $router->post('paste-fact', 'EditGedcomRecordController::pasteFact');
    $router->post('delete-record', 'EditGedcomRecordController::deleteRecord');
    $router->get('add-fact', 'EditGedcomRecordController::addFact');
    $router->get('edit-fact', 'EditGedcomRecordController::editFact');
    $router->post('update-fact', 'EditGedcomRecordController::updateFact');
    $router->get('search-replace', 'SearchController::replace');
    $router->post('search-replace', 'SearchController::replaceAction');
    $router->get('add-child-to-family', 'EditFamilyController::addChild');
    $router->post('add-child-to-family', 'EditFamilyController::addChildAction');
    $router->get('add-spouse-to-family', 'EditFamilyController::addSpouse');
    $router->post('add-spouse-to-family', 'EditFamilyController::addSpouseAction');
    $router->get('change-family-members', 'EditFamilyController::changeFamilyMembers');
    $router->post('change-family-members', 'EditFamilyController::changeFamilyMembersAction');
    $router->get('add-child-to-individual', 'EditIndividualController::addChild');
    $router->post('add-child-to-individual', 'EditIndividualController::addChildAction');
    $router->get('add-parent-to-individual', 'EditIndividualController::addParent');
    $router->post('add-parent-to-individual', 'EditIndividualController::addParentAction');
    $router->get('add-spouse-to-individual', 'EditIndividualController::addSpouse');
    $router->post('add-spouse-to-individual', 'EditIndividualController::addSpouseAction');
    $router->get('add-unlinked-individual', 'EditIndividualController::addUnlinked');
    $router->post('add-unlinked-individual', 'EditIndividualController::addUnlinkedAction');
    $router->get('link-child-to-family', 'EditIndividualController::linkChildToFamily');
    $router->post('link-child-to-family', 'EditIndividualController::linkChildToFamilyAction');
    $router->get('link-spouse-to-individual', 'EditIndividualController::linkSpouseToIndividual');
    $router->post('link-spouse-to-individual', 'EditIndividualController::linkSpouseToIndividualAction');
    $router->get('edit-name', 'EditIndividualController::editName');
    $router->post('edit-name', 'EditIndividualController::editNameAction');
    $router->get('add-name', 'EditIndividualController::addName');
    $router->post('add-name', 'EditIndividualController::addNameAction');
}

// Member routes.
if ($tree instanceof Tree && $tree->getPreference('imported') === '1' && Auth::isMember($tree)) {
    $router->get('user-page', 'HomePageController::userPage');
    $router->get('user-page-block', 'HomePageController::userPageBlock');
    $router->get('user-page-edit', 'HomePageController::userPageEdit');
    $router->post('user-page-update', 'HomePageController::userPageUpdate');
    $router->get('user-page-block-edit', 'HomePageController::userPageBlockEdit');
    $router->post('user-page-block-edit', 'HomePageController::userPageBlockUpdate');
    $router->get('my-account', 'AccountController::edit');
    $router->post('my-account', 'AccountController::update');
    $router->post('delete-account', 'AccountController::delete');
}

// Public routes (that need a tree).
if ($tree instanceof Tree && $tree->getPreference('imported') === '1') {
    $router->get('autocomplete-folder', 'AutocompleteController::folder');
    $router->get('autocomplete-page', 'AutocompleteController::page');
    $router->get('autocomplete-place', 'AutocompleteController::place');
    $router->get('calendar', 'CalendarController::page');
    $router->get('calendar-events', 'CalendarController::calendar');
    $router->get('help-text', 'HelpTextController::helpText');
    $router->get('tree-page', 'HomePageController::treePage');
    $router->get('tree-page-block', 'HomePageController::treePageBlock');
    $router->get('media-thumbnail', 'MediaFileController::mediaThumbnail');
    $router->get('media-download', 'MediaFileController::mediaDownload');
    $router->get('family', 'FamilyController::show');
    $router->get('individual', 'IndividualController::show');
    $router->get('individual-tab', 'IndividualController::tab');
    $router->get('media', 'MediaController::show');
    $router->get('contact', 'MessageController::contactPage');
    $router->post('contact', 'MessageController::contactAction');
    $router->get('message', 'MessageController::messagePage');
    $router->post('message', 'MessageController::messageAction');
    $router->get('note', 'NoteController::show');
    $router->get('source', 'SourceController::show');
    $router->get('record', 'GedcomRecordController::show');
    $router->get('repository', 'RepositoryController::show');
    $router->get('report-list', 'ReportEngineController::reportList');
    $router->get('report-setup', 'ReportEngineController::reportSetup');
    $router->get('report-run', 'ReportEngineController::reportRun');
    $router->post('accept-changes', 'PendingChangesController::acceptChanges');
    $router->post('reject-changes', 'PendingChangesController::rejectChanges');
    $router->post('accept-all-changes', 'PendingChangesController::acceptAllChanges');
    $router->post('reject-all-changes', 'PendingChangesController::rejectAllChanges');
    $router->post('select2-family', 'AutocompleteController::select2Family');
    $router->post('select2-individual', 'AutocompleteController::select2Individual');
    $router->post('select2-media', 'AutocompleteController::select2MediaObject');
    $router->post('select2-note', 'AutocompleteController::select2Note');
    $router->post('select2-source', 'AutocompleteController::select2Source');
    $router->post('select2-submitter', 'AutocompleteController::select2Submitter');
    $router->post('select2-repository', 'AutocompleteController::select2Repository');
    $router->get('search-quick', 'SearchController::quick');
    $router->get('search-advanced', 'SearchController::advanced');
    $router->get('search-general', 'SearchController::general');
    $router->get('search-phonetic', 'SearchController::phonetic');
}

// Public routes (that do not need a tree).
$router->get('login', 'Auth\LoginController::loginPage');
$router->post('login', 'Auth\LoginController::loginAction');
$router->get('logout', 'Auth\LoginController::logoutAction');
$router->post('logout', 'Auth\LoginController::logoutAction');
$router->get('register', 'Auth\RegisterController::registerPage');
$router->post('register', 'Auth\RegisterController::registerAction');
$router->get('verify', 'Auth\VerifyEmailController::verify');
$router->get('password-request', PasswordRequestForm::class);
$router->post('password-request', PasswordRequestAction::class);
$router->get('password-reset', PasswordResetForm::class);
$router->post('password-reset', PasswordResetAction::class);
$router->post('language', SelectLanguage::class);
$router->post('theme', SelectTheme::class);
$router->get('privacy-policy', PrivacyPolicy::class);
$router->get('module', ModuleAction::class);
$router->post('module', ModuleAction::class);
$router->get('ping', Ping::class);
