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

use Closure;
use Fisharebest\Webtrees\Http\Controllers\ErrorController;
use Fisharebest\Webtrees\Http\Middleware\CheckCsrf;
use Fisharebest\Webtrees\Http\Middleware\UseTransaction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

// Bootstrap the application
require 'includes/session.php';

DebugBar::startMeasure('routing');

// The HTTP request.
$request = Request::createFromGlobals();
$method  = $request->getMethod();
$route   = $request->get('route');

/**
 * Init the tree instance.
 *
 * @return Tree
 */
function initTree() {
	$tree = null;

	// Set the tree for the page; (1) the request, (2) the session, (3) the site default, (4) any tree
	foreach ([Filter::post('ged'), Filter::get('ged'), Session::get('GEDCOM'), Site::getPreference('DEFAULT_GEDCOM')] as $treeName) {
		$tree = Tree::findByName($treeName);

		if ($tree) {
			Session::put('GEDCOM', $treeName);
			break;
		}
	}

	// No chosen tree? Use any one.
	if (!$tree) {
		foreach (Tree::getAll() as $tree) {
			break;
		}
	}

	return $tree;
}

try {
	$tree = initTree();

	// Avoid global vars! But needed as some controllers still rely on it. :(
	$WT_TREE = $tree;

	$request->attributes->set('tree', $tree);
	$request->attributes->set('user', Auth::user());

	// Load the routing table.
	$routes = require 'routes/web.php';

	// Force login
	if (!$tree) {
		if (!Auth::check() && empty($route)) {
			$route = 'login';
		}

		// Check if tree exists and user is not an admin
		// -> Clear route to force redirect to "ErrorController@noRouteFound" to render "errors/no-tree-access"
		if (Auth::check() && !Auth::isAdmin() && Auth::id() && ($route !== 'logout')) {
			$route = '';
		}
	}

	// Find the action for the selected route
	$controller_action = $routes[$method . ':' . $route] ?? 'ErrorController@noRouteFound';

	DebugBar::stopMeasure('routing');

	// Create the controller
	DebugBar::startMeasure('create controller');

	list($controller_name, $action) = explode('@', $controller_action);
	$controller_class = __NAMESPACE__ . '\\Http\\Controllers\\' . $controller_name;
	$controller = new $controller_class;

	DebugBar::stopMeasure('create controller');

	// Note that we can't stop this timer, as running the action will
	// generate the response - which includes (and stops) the timer
	DebugBar::startMeasure('controller_action', $controller_action);

	$middleware_stack = [];

	if ($method === 'POST') {
		$middleware_stack[] = new UseTransaction;
		$middleware_stack[] = new CheckCsrf;
	}

	// Apply the middleware using the "onion" pattern.
	$pipeline = array_reduce($middleware_stack, function (Closure $next, $middleware): Closure {
		// Create a closure to apply the middleware.
		return function (Request $request) use ($middleware, $next): Response {
			return $middleware->handle($request, $next);
		};
	}, function (Request $request) use ($controller, $action): Response {
		// Create a closure to generate the response.
		return call_user_func([$controller, $action], $request);
	});

	$response = call_user_func($pipeline, $request);
} catch (Throwable $ex) {
	DebugBar::addThrowable($ex);

	// Clear any buffered output.
	while (ob_get_level() > 0) {
		ob_end_clean();
	}

	if ($ex instanceof HttpExceptionInterface) {
		// Show a friendly page for expected exceptions.
		if ($request->isXmlHttpRequest()) {
			$response = new Response($ex->getMessage(), $ex->getStatusCode());
		} else {
			$controller = new ErrorController;
			$response   = $controller->errorResponse($ex->getMessage());
		}
	} else {
		// Show an error page for unexpected exceptions.
		if (getenv('DEBUG')) {
			// Local dev environment?  Show full debug.
			$whoops = new Run;
			$whoops->pushHandler(new PrettyPageHandler);
			$whoops->handleException($ex);
		} else {
			// Running remotely?  Show a friendly error page.
			$controller = new ErrorController;
			$response   = $controller->unhandledExceptionResponse($request, $ex);
		}
	}
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
