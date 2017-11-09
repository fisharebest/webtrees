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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

require 'includes/session.php';

// The HTTP request.
$request = Request::createFromGlobals();
$method  = $request->getMethod();
$route   = $request->get('route');

// Default route
$response = new RedirectResponse(Html::url('index.php', []));

// POST request? Check the CSRF token.
if ($method === 'POST' && !Filter::checkCsrf()) {
	$referer_url = $request->headers->get('referer', Html::url('index.php', []));

	return (new RedirectResponse($referer_url))->prepare($request)->send();
}

// Admin routes.
if (Auth::isAdmin()) {
	switch ($method . ':' . $route) {
	default:
	case 'GET:':
		$url      = Html::url('admin.php', ['route' => 'admin-control-panel']);
		$response = new RedirectResponse($url);
		break;

	case 'GET:admin-modules':
		$response = ($controller = new AdminController)->modules();
		break;

	case 'GET:admin-blocks':
		$response = ($controller = new AdminController)->blocks();
		break;

	case 'GET:admin-charts':
		$response = ($controller = new AdminController)->charts();
		break;

	case 'GET:admin-clean-data':
		$response = ($controller = new AdminController)->cleanData();
		break;

	case 'POST:admin-clean-data':
		$response = ($controller = new AdminController)->cleanDataAction($request);
		break;

	case 'GET:admin-fix-level-0-media':
		$response = ($controller = new AdminController)->fixLevel0Media();
		break;

	case 'POST:admin-fix-level-0-media-action':
		$response = ($controller = new AdminController)->fixLevel0MediaAction($request);
		break;

	case 'GET:admin-fix-level-0-media-data':
		$response = ($controller = new AdminController)->fixLevel0MediaData($request);
		break;

	case 'GET:admin-menus':
		$response = ($controller = new AdminController)->menus();
		break;

	case 'GET:admin-reports':
		$response = ($controller = new AdminController)->reports();
		break;

	case 'GET:admin-server-information':
		$response = ($controller = new AdminController)->serverInformation();
		break;

	case 'GET:admin-sidebars':
		$response = ($controller = new AdminController)->sidebars();
		break;

	case 'GET:admin-tabs':
		$response = ($controller = new AdminController)->tabs();
		break;

	case 'GET:admin-control-panel':
		$response = ($controller = new AdminController)->controlPanel();
		break;

	case 'GET:admin-control-panel-manager':
		$response = ($controller = new AdminController)->controlPanelManager();
		break;

	case 'POST:admin-delete-module-settings':
		$response = ($controller = new AdminController)->deleteModuleSettings($request);
		break;

	case 'POST:admin-update-module-access':
		$response = ($controller = new AdminController)->updateModuleAccess($request);
		break;

	case 'POST:admin-update-module-status':
		$response = ($controller = new AdminController)->updateModuleStatus($request);
		break;
	}
}

// Manager routes.
if (Auth::isManager(($controller = new AdminController)->tree())) {
	switch ($method . ':' . $route) {
	case 'GET:admin-control-panel-manager':
		$response = ($controller = new AdminController)->controlPanelManager();
		break;

	case 'GET:admin-changes-log':
		$response = ($controller = new AdminController)->changesLog($request);
		break;

	case 'GET:admin-changes-log-data':
		$response = ($controller = new AdminController)->changesLogData($request);
		break;

	case 'GET:admin-changes-log-download':
		$response = ($controller = new AdminController)->changesLogDownload($request);
		break;
	}
}

$response->prepare($request)->send();
