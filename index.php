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
use Fisharebest\Webtrees\Http\Middleware\CheckForMaintenanceMode;
use Fisharebest\Webtrees\Http\Middleware\UseTransaction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

try {
	// Most requests will need the current tree and user.
	$all_trees = Tree::getAll();

	$tree = $all_trees[$request->get('ged')] ?? null;

	// No tree specified/available?  Choose one.
	if ($tree === null && $method === 'GET') {
		$tree = $all_trees[Site::getPreference('DEFAULT_GEDCOM')] ?? array_values($all_trees)[0] ?? null;
	}

	$request->attributes->set('tree', $tree);
	$request->attributes->set('user', Auth::user());

	// Most layouts will require a tree for the page header/footer
	View::share('tree', $tree);

	// Load the routing table.
	$routes = require 'routes/web.php';

	// Find the action for the selected route
	$controller_action = $routes[$method . ':' . $route] ?? 'ErrorController@noRouteFound';


	// Create the controller
	list($controller_name, $action) = explode('@', $controller_action);
	$controller_class = __NAMESPACE__ . '\\Http\\Controllers\\' . $controller_name;
	$controller       = new $controller_class;

	DebugBar::stopMeasure('routing');

	// Note that we can't stop this timer, as running the action will
	// generate the response - which includes (and stops) the timer
	DebugBar::startMeasure('controller_action', $controller_action);

	$middleware_stack = [
		new CheckForMaintenanceMode,
	];

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

	if ($ex instanceof HttpException) {
		// Show a friendly page for expected exceptions.
		if ($request->isXmlHttpRequest()) {
			$response = new Response($ex->getMessage(), $ex->getStatusCode());
		} else {
			$controller = new ErrorController;
			$response   = $controller->errorResponse($ex);
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
