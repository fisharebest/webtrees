<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\AdminController;
use Fisharebest\Webtrees\Controller\HomePageController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

// The HTTP request.
$request = Request::createFromGlobals();
$method  = $request->getMethod();
$route   = $request->get('route');

// Most requests are for a specific tree
$tree = $WT_TREE;

// POST request? Check the CSRF token.
if ($method === 'POST' && !Filter::checkCsrf()) {
	$referer_url = $request->headers->get('referer', Html::url('index.php', []));

	return (new RedirectResponse($referer_url))->prepare($request)->send();
}

// Admin routes.
if (Auth::isAdmin()) {
	switch ($method . ':' . $route) {
	default:
	case 'GET:admin-blocks':
		return ($controller = new AdminController)->blocks();

	case 'GET:admin-charts':
		return ($controller = new AdminController)->charts();

	case 'GET:admin-clean-data':
		return ($controller = new AdminController)->cleanData();

	case 'POST:admin-clean-data':
		return ($controller = new AdminController)->cleanDataAction($request);

	case 'GET:admin-control-panel':
		return ($controller = new AdminController)->controlPanel();

	case 'POST:admin-delete-module-settings':
		return ($controller = new AdminController)->deleteModuleSettings($request);

	case 'GET:admin-fix-level-0-media':
		return ($controller = new AdminController)->fixLevel0Media();

	case 'POST:admin-fix-level-0-media-action':
		return ($controller = new AdminController)->fixLevel0MediaAction($request);

	case 'GET:admin-fix-level-0-media-data':
		return ($controller = new AdminController)->fixLevel0MediaData($request);

	case 'GET:admin-menus':
		return ($controller = new AdminController)->menus();

	case 'GET:admin-modules':
		return ($controller = new AdminController)->modules();

	case 'GET:admin-reports':
		return ($controller = new AdminController)->reports();

	case 'GET:admin-server-information':
		return ($controller = new AdminController)->serverInformation();

	case 'GET:admin-sidebars':
		return ($controller = new AdminController)->sidebars();

	case 'GET:admin-tabs':
		return ($controller = new AdminController)->tabs();

	case 'POST:admin-update-module-access':
		return ($controller = new AdminController)->updateModuleAccess($request);

	case 'POST:admin-update-module-status':
		return ($controller = new AdminController)->updateModuleStatus($request);
	}
}

// Manager routes.
if ($tree instanceof Tree && Auth::isManager($tree)) {
	switch ($method . ':' . $route) {
	case 'GET:admin-control-panel-manager':
		return ($controller = new AdminController)->controlPanelManager();

	case 'GET:admin-changes-log':
		return ($controller = new AdminController)->changesLog($request);

	case 'GET:admin-changes-log-data':
		return ($controller = new AdminController)->changesLogData($request);

	case 'GET:admin-changes-log-download':
		return ($controller = new AdminController)->changesLogDownload($request);

	case 'GET:home-page-edit':
		return ($controller = new HomePageController)->treePageEdit($request);

	case 'GET:home-page-update':
		return ($controller = new HomePageController)->treePageUpdate($request);
	}
}

// Member routes.
if ($tree instanceof Tree && Auth::isMember($tree) && $tree->getPreference('imported') === '1') {
	switch ($method . ':' . $route) {
	case 'GET:my-page':
		return ($controller = new HomePageController)->userPage();

	case 'GET:my-page-edit':
		return ($controller = new HomePageController)->userPageEdit($request);

	case 'GET:my-page-update':
		return ($controller = new HomePageController)->userPageUpdate($request);
	}
}

// Public routes.
if ($tree instanceof Tree && $tree->getPreference('imported') === '1') {
	switch ($method . ':' . $route) {
	case 'GET:home-page':
		return ($controller = new HomePageController)->treePage();
	}
}

// Default
return new RedirectResponse(Html::url('index.php', []));
