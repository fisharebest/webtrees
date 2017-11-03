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

// POST request? Check the CSRF token.
if ($method === 'POST' && !Filter::checkCsrf()) {
	$referer_url = $request->headers->get('referer', 'index.php');

	return (new RedirectResponse($referer_url))->prepare($request)->send();
}

if (Auth::isAdmin()) {
	// Admin routes.
	switch ($method . ':' . $route) {
	default:
	case 'GET:blocks':
		return ($controller = new AdminController)->blocks();

	case 'GET:charts':
		return ($controller = new AdminController)->charts();

	case 'GET:control-panel':
		return ($controller = new AdminController)->controlPanel();

	case 'GET:control-panel-manager':
		return ($controller = new AdminController)->controlPanelManager();

	case 'POST:delete-module-settings':
		return ($controller = new AdminController)->deleteModuleSettings($request);

	case 'GET:menus':
		return ($controller = new AdminController)->menus();

	case 'GET:modules':
		return ($controller = new AdminController)->modules();

	case 'GET:reports':
		return ($controller = new AdminController)->reports();

	case 'GET:sidebars':
		return ($controller = new AdminController)->sidebars();

	case 'GET:tabs':
		return ($controller = new AdminController)->tabs();

	case 'POST:update-module-access':
		return ($controller = new AdminController)->updateModuleAccess($request);

	case 'POST:update-module-status':
		return ($controller = new AdminController)->updateModuleStatus($request);
	}
}

if (Auth::isManager(($controller = new AdminController)->tree())) {
	// Manager routes.
	switch ($method . ':' . $route) {
	case 'GET:control-panel-manager':
		return ($controller = new AdminController)->controlPanelManager();
	}
}

// No route matched?
return (new RedirectResponse('index.php'))->prepare($request)->send();
