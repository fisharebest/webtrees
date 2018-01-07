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
		'GET:unused-media-thumbnail'          => 'MediaController@unusedMediaThumbnail',
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
		'GET:tree-preferences'            => 'AdminController@treePreferencesEdit',
		'POST:tree-preferences'           => 'AdminController@treePreferencesUpdate',
		'GET:tree-privacy'                => 'AdminController@treePrivacyEdit',
		'POST:tree-privacy'               => 'AdminController@treePrivacyUpdate',
	];
}

// Member routes.
if ($tree instanceof Tree && $tree->getPreference('imported') === '1' && Auth::isMember($tree)) {
	$routes += [
		'GET:user-page'         => 'HomePageController@userPage',
		'GET:user-page-block'   => 'HomePageController@userPageBlock',
		'GET:user-page-edit'    => 'HomePageController@userPageEdit',
		'POST:user-page-update' => 'HomePageController@userPageUpdate',
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
	];
}

// Public routes.
if ($tree instanceof Tree && $tree->getPreference('imported') === '1') {
	$routes += [
		'GET:autocomplete-folder' => 'AutocompleteController@folder',
		'GET:autocomplete-place'  => 'AutocompleteController@place',
		'GET:help-text'           => 'HelpTextController@helpText',
		'GET:tree-page'           => 'HomePageController@treePage',
		'GET:tree-page-block'     => 'HomePageController@treePageBlock',
		'GET:media-thumbnail'     => 'MediaController@mediaThumbnail',
	];
}

return $routes;
