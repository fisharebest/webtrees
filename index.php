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
declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

// Bootstrap the application
require 'includes/session.php';

DebugBar::startMeasure('routing');

// The HTTP request.
$request = Request::createFromGlobals();
$method  = $request->getMethod();
$route   = $request->get('route');

// Most requests will need the current tree and user.
$all_tree_names     = array_keys(Tree::getNameList());
$first_tree_name    = current($all_tree_names) ?? '';
$previous_tree_name = Session::get('GEDCOM', $first_tree_name);
$default_tree_name  = $previous_tree_name ?: Site::getPreference('DEFAULT_GEDCOM');
$tree_name          = $request->get('ged', $default_tree_name);
$tree               = Tree::findByName($tree_name);
Session::put('GEDCOM', $tree_name);

$request->attributes->set('tree', $tree);
$request->attributes->set('user', AUth::user());

// Load the routing table.
$routes = require 'routes/web.php';

// Find the action for the selected route
$controller_action = $routes[$method . ':' . $route] ?? 'ErrorController@noRouteFound';

DebugBar::stopMeasure('routing');

DebugBar::startMeasure('create controller');

// Create the controller
list($controller_name, $action) = explode('@', $controller_action);
$controller_class = __NAMESPACE__ . '\\Http\\Controllers\\' . $controller_name;
$controller = new $controller_class;

DebugBar::stopMeasure('create controller');

// Note that we can't stop this timer, as running the action will
// generate the response - which includes (and stops) the timer
DebugBar::startMeasure('controller_action', $controller_action);

if ($method === 'POST' && Database::isConnected()) {
	Database::beginTransaction();
	try {
		/** @var Response $response */
		$response = $controller->$action($request);
		Database::commit();
	} catch (Throwable $ex) {
		DebugBar::addThrowable($ex);

		Database::rollBack();

		// Yikes!  Something went badly wrong.
		throw $ex;
	}
} else {
	/** @var Response $response */
	$response = $controller->$action($request);
}

// Send response
if ($response instanceof RedirectResponse) {
	// Show the debug data on the next page
	DebugBar::stackData();
} elseif ($response instanceof JsonResponse) {
	// Use HTTP headers and some jQuery to add debug to the current page.
	DebugBar::sendDataInHeaders();
}

return $response->prepare($request)->send();
