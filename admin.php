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

// POST request - check the CSRF token.
if ($method === 'POST' && !Filter::checkCsrf()) {
	$referer_url = $request->headers->get('referer', 'index.php');

	return (new RedirectResponse($referer_url))->prepare($request)->send();
}

$controller = new AdminController;

// Route the request to a controller action.
if (Auth::isManager($controller->tree())) {
	switch ($method . ':' . $route) {
	case '':
	}
}

if (Auth::isAdmin()) {
	switch ($method . ':' . $route) {
	default:
	case 'GET:control-panel':
		return $controller->controlPanel();

	case 'GET:control-panel-manager':
		return $controller->controlPanelManager();

	case 'GET:modules':

	}
}

// No route matched?
return (new RedirectResponse('index.php'))->prepare($request)->send();
