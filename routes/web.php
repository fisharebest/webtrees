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

$routes = [];

// Admin routes.
if (Auth::isAdmin()) {
	$routes += [
		'GET:admin-blocks'                    => 'AdminController@blocks',
		'GET:admin-charts'                    => 'AdminController@charts',
		'GET:admin-clean-data'                => 'AdminController@cleanData',
		'POST:admin-clean-data'               => 'AdminController@cleanDataAction',
		'GET:admin-control-panel'             => 'AdminController@controlPanel',
		'POST:admin-delete-module-settings'   => 'AdminController@deleteModuleSettings',
		'GET:admin-fix-level-0-media'         => 'AdminController@fixLevel0Media',
		'POST:admin-fix-level-0-media-action' => 'AdminController@fixLevel0MediaAction',
		'GET:admin-fix-level-0-media-data'    => 'AdminController@fixLevel0MediaData',
		'GET:admin-menus'                     => 'AdminController@menus',
		'GET:admin-modules'                   => 'AdminController@modules',
		'GET:admin-reports'                   => 'AdminController@reports',
		'GET:admin-server-information'        => 'AdminController@serverInformation',
		'GET:admin-sidebars'                  => 'AdminController@sidebars',
		'GET:admin-tabs'                      => 'AdminController@tabs',
		'POST:admin-update-module-access'     => 'AdminController@updateModuleAccess',
		'POST:admin-update-module-status'     => 'AdminController@updateModuleStatus',
		'GET:admin-webtrees1-thumbs'          => 'AdminController@webtrees1Thumbnails',
		'POST:admin-webtrees1-thumbs-action'  => 'AdminController@webtrees1ThumbnailsAction',
		'GET:admin-webtrees1-thumbs-data'     => 'AdminController@webtrees1ThumbnailsData',
		'GET:tree-page-default-edit'          => 'HomePageController@treePageDefaultEdit',
		'POST:tree-page-default-update'       => 'HomePageController@treePageDefaultUpdate',
		'GET:user-page-default-edit'          => 'HomePageController@userPageDefaultEdit',
		'POST:user-page-default-update'       => 'HomePageController@userPageDefaultUpdate',
		'GET:user-page-user-edit'             => 'HomePageController@userPageUserEdit',
		'POST:user-page-user-update'          => 'HomePageController@userPageUserUpdate',
		'GET:unused-media-thumbnail'          => 'MediaFileController@unusedMediaThumbnail',
		'GET:broadcast'                       => 'MessageController@broadcastPage',
		'POST:broadcast'                      => 'MessageController@broadcastAction',
	];
}

// Manager routes.
if ($tree instanceof Tree && Auth::isManager($tree)) {
	$routes += [
		'GET:admin-control-panel-manager' => 'AdminController@controlPanelManager',
		'GET:admin-changes-log'           => 'AdminController@changesLog',
		'GET:admin-changes-log-data'      => 'AdminController@changesLogData',
		'GET:admin-changes-log-download'  => 'AdminController@changesLogDownload',
		'GET:tree-page-edit'              => 'HomePageController@treePageEdit',
		'POST:tree-page-update'           => 'HomePageController@treePageUpdate',
		'GET:merge-records'               => 'AdminController@mergeRecords',
		'POST:merge-records'              => 'AdminController@mergeRecordsAction',
		'GET:tree-page-block-edit'        => 'HomePageController@treePageBlockEdit',
		'POST:tree-page-block-edit'       => 'HomePageController@treePageBlockUpdate',
		'GET:tree-preferences'            => 'AdminController@treePreferencesEdit',
		'POST:tree-preferences'           => 'AdminController@treePreferencesUpdate',
		'GET:tree-privacy'                => 'AdminController@treePrivacyEdit',
		'POST:tree-privacy'               => 'AdminController@treePrivacyUpdate',
	];
}

// Moderator routes.
if ($tree instanceof Tree && $tree->getPreference('imported') === '1' && Auth::isModerator($tree)) {
	$routes += [
		'GET:show-pending'        => 'PendingChangesController@showChanges',
		'POST:accept-pending'     => 'PendingChangesController@acceptChange',
		'POST:reject-pending'     => 'PendingChangesController@rejectChange',
		'POST:accept-all-pending' => 'PendingChangesController@acceptAllChanges',
		'POST:reject-all-pending' => 'PendingChangesController@rejectAllChanges',
	];
}

// Editor routes.
if ($tree instanceof Tree && $tree->getPreference('imported') === '1' && Auth::isEditor($tree)) {
	$routes += [
		'GET:add-media-file'       => 'EditMediaController@addMediaFile',
		'POST:add-media-file'      => 'EditMediaController@addMediaFileAction',
		'GET:edit-media-file'      => 'EditMediaController@editMediaFile',
		'POST:edit-media-file'     => 'EditMediaController@editMediaFileAction',
		'GET:create-media-object'  => 'EditMediaController@createMediaObject',
		'POST:create-media-object' => 'EditMediaController@createMediaObjectAction',
		'GET:create-note-object'   => 'EditNoteController@createNoteObject',
		'POST:create-note-object'  => 'EditNoteController@createNoteObjectAction',
		'GET:create-repository'    => 'EditRepositoryController@createRepository',
		'POST:create-repository'   => 'EditRepositoryController@createRepositoryAction',
		'GET:create-source'        => 'EditSourceController@createSource',
		'POST:create-source'       => 'EditSourceController@createSourceAction',
		'GET:create-submitter'     => 'EditSubmitterController@createSubmitter',
		'POST:create-submitter'    => 'EditSubmitterController@createSubmitterAction',
		'GET:reorder-children'     => 'EditFamilyController@reorderChildren',
		'POST:reorder-children'    => 'EditFamilyController@reorderChildrenAction',
		'GET:reorder-media'        => 'EditIndividualController@reorderMedia',
		'POST:reorder-media'       => 'EditIndividualController@reorderMediaAction',
		'GET:reorder-names'        => 'EditIndividualController@reorderNames',
		'POST:reorder-names'       => 'EditIndividualController@reorderNamesAction',
		'GET:reorder-spouses'      => 'EditIndividualController@reorderSpouses',
		'POST:reorder-spouses'     => 'EditIndividualController@reorderSpousesAction',
		'GET:edit-raw-record'      => 'EditGedcomRecordController@editRawRecord',
		'POST:edit-raw-record'     => 'EditGedcomRecordController@editRawRecordAction',
		'GET:edit-raw-fact'        => 'EditGedcomRecordController@editRawFact',
		'POST:edit-raw-fact'       => 'EditGedcomRecordController@editRawFactAction',
	];
}

// Member routes.
if ($tree instanceof Tree && $tree->getPreference('imported') === '1' && Auth::isMember($tree)) {
	$routes += [
		'GET:user-page'             => 'HomePageController@userPage',
		'GET:user-page-block'       => 'HomePageController@userPageBlock',
		'GET:user-page-edit'        => 'HomePageController@userPageEdit',
		'POST:user-page-update'     => 'HomePageController@userPageUpdate',
		'GET:user-page-block-edit'  => 'HomePageController@userPageBlockEdit',
		'POST:user-page-block-edit' => 'HomePageController@userPageBlockUpdate',
		'GET:my-account'            => 'AccountController@edit',
		'POST:my-account'           => 'AccountController@update',
		'POST:delete-account'       => 'AccountController@delete',
	];
}

// Public routes (that need a tree).
if ($tree instanceof Tree && $tree->getPreference('imported') === '1') {
	$routes += [
		'GET:autocomplete-folder'    => 'AutocompleteController@folder',
		'GET:autocomplete-place'     => 'AutocompleteController@place',
		'GET:branches'               => 'BranchesController@page',
		'GET:branches-list'          => 'BranchesController@list',
		'GET:calendar'               => 'CalendarController@page',
		'GET:calendar-events'        => 'CalendarController@calendar',
		'POST:expand-chart-box'      => 'IndividualController@expandChartBox',
		'GET:help-text'              => 'HelpTextController@helpText',
		'GET:tree-page'              => 'HomePageController@treePage',
		'GET:tree-page-block'        => 'HomePageController@treePageBlock',
		'GET:media-thumbnail'        => 'MediaFileController@mediaThumbnail',
		'GET:media-download'         => 'MediaFileController@mediaDownload',
		'GET:family'                 => 'FamilyController@show',
		'GET:individual'             => 'IndividualController@show',
		'GET:individual-tab'         => 'IndividualController@tab',
		'GET:media'                  => 'MediaController@show',
		'GET:contact'                => 'MessageController@contactPage',
		'POST:contact'               => 'MessageController@contactAction',
		'GET:message'                => 'MessageController@messagePage',
		'POST:message'               => 'MessageController@messageAction',
		'GET:note'                   => 'NoteController@show',
		'GET:source'                 => 'SourceController@show',
		'GET:record'                 => 'GedcomRecordController@show',
		'GET:repository'             => 'RepositoryController@show',
		'GET:report-list'            => 'ReportEngineController@reportList',
		'GET:report-setup'           => 'ReportEngineController@reportSetup',
		'GET:report-run'             => 'ReportEngineController@reportRun',
		'GET:family-list'            => 'ListController@familyList',
		'GET:individual-list'        => 'ListController@individualList',
		'GET:media-list'             => 'ListController@mediaList',
		'GET:note-list'              => 'ListController@noteList',
		'GET:place-hierarchy'        => 'PlaceHierarchyController@show',
		'GET:repository-list'        => 'ListController@repositoryList',
		'GET:source-list'            => 'ListController@sourceList',
		'GET:ancestors'              => 'AncestorsChartController@page',
		'GET:ancestors-chart'        => 'AncestorsChartController@chart',
		'GET:compact-tree'           => 'CompactTreeChartController@page',
		'GET:compact-tree-chart'     => 'CompactTreeChartController@chart',
		'GET:descendants'            => 'DescendantsChartController@page',
		'GET:descendants-chart'      => 'DescendantsChartController@chart',
		'GET:family-book'            => 'FamilyBookChartController@page',
		'GET:family-book-chart'      => 'FamilyBookChartController@chart',
		'GET:fan'                    => 'FanChartController@page',
		'GET:fan-chart'              => 'FanChartController@chart',
		'GET:hourglass'              => 'HourglassChartController@page',
		'GET:hourglass-chart'        => 'HourglassChartController@chart',
		'POST:hourglass-add-asc'     => 'HourglassChartController@chartAddAncestor',
		'POST:hourglass-add-desc'    => 'HourglassChartController@chartAddDescendant',
		'GET:interactive'            => 'InteractiveChartController@page',
		'GET:interactive-chart'      => 'InteractiveChartController@chart',
		'GET:lifespans'              => 'LifespansChartController@page',
		'GET:lifespans-chart'        => 'LifespansChartController@chart',
		'GET:pedigree'               => 'PedigreeChartController@page',
		'GET:pedigree-chart'         => 'PedigreeChartController@chart',
		'GET:pedigreemap'            => 'PedigreeMapChartController@page',
		'GET:pedigreemap-chart'      => 'PedigreeMapChartController@chart',
		'GET:relationships'          => 'RelationshipsChartController@page',
		'GET:relationships-chart'    => 'RelationshipsChartController@chart',
		'GET:statistics'             => 'StatisticsChartController@page',
		'GET:statistics-families'    => 'StatisticsChartController@chartFamilies',
		'GET:statistics-individuals' => 'StatisticsChartController@chartIndividuals',
		'GET:statistics-other'       => 'StatisticsChartController@chartOther',
		'GET:statistics-options'     => 'StatisticsChartController@chartCustomOptions',
		'GET:statistics-chart'       => 'StatisticsChartController@chartCustomChart',
		'GET:timeline'               => 'TimelineChartController@page',
		'GET:timeline-chart'         => 'TimelineChartController@chart',
	];
}

// Public routes (that do not need a tree).
$routes += [
	'GET:module'           => 'ModuleController@action',
	'POST:module'          => 'ModuleController@action',
	'GET:login'            => 'Auth\\LoginController@loginPage',
	'POST:login'           => 'Auth\\LoginController@loginAction',
	'GET:logout'           => 'Auth\\LoginController@logoutAction',
	'POST:logout'          => 'Auth\\LoginController@logoutAction',
	'GET:register'         => 'Auth\\RegisterController@registerPage',
	'POST:register'        => 'Auth\\RegisterController@registerAction',
	'GET:verify'           => 'Auth\\VerifyEmailController@verify',
	'GET:forgot-password'  => 'Auth\\ForgotPasswordController@forgotPasswordPage',
	'POST:forgot-password' => 'Auth\\ForgotPasswordController@forgotPasswordAction',
];

return $routes;
