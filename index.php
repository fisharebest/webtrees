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

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Exceptions\Handler;
use Fisharebest\Webtrees\Http\Controllers\SetupController;
use Fisharebest\Webtrees\Http\Middleware\BootModules;
use Fisharebest\Webtrees\Http\Middleware\CheckCsrf;
use Fisharebest\Webtrees\Http\Middleware\CheckForMaintenanceMode;
use Fisharebest\Webtrees\Http\Middleware\DebugBarData;
use Fisharebest\Webtrees\Http\Middleware\Housekeeping;
use Fisharebest\Webtrees\Http\Middleware\MiddlewareInterface;
use Fisharebest\Webtrees\Http\Middleware\UseFilesystem;
use Fisharebest\Webtrees\Http\Middleware\UseLocale;
use Fisharebest\Webtrees\Http\Middleware\UseSession;
use Fisharebest\Webtrees\Http\Middleware\UseTheme;
use Fisharebest\Webtrees\Http\Middleware\UseTransaction;
use Fisharebest\Webtrees\Http\Middleware\UseTree;
use Fisharebest\Webtrees\Services\MigrationService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require __DIR__ . '/vendor/autoload.php';

const WT_ROOT = __DIR__ . DIRECTORY_SEPARATOR;

Webtrees::init();

// Initialise the DebugBar for development.
// Use `composer install --dev` on a development build to enable.
// Note that you may need to increase the size of the fcgi buffers on nginx.
// e.g. add these lines to your fastcgi_params file:
// fastcgi_buffers 16 16m;
// fastcgi_buffer_size 32m;
DebugBar::init(class_exists('\\DebugBar\\StandardDebugBar'));

// Use an array cache for database calls, etc.
app()->instance('cache.array', new Repository(new ArrayStore()));

// Start the timer.
app()->instance(TimeoutService::class, new TimeoutService(microtime(true)));

// Extract the request parameters.
$request = Request::createFromGlobals();
app()->instance(Request::class, $request);

// Calculate the base URL, so we can generate absolute URLs.
$request_uri = $request->getSchemeAndHttpHost() . $request->getRequestUri();

// Remove any PHP script name and parameters.
$base_uri = preg_replace('/[^\/]+\.php(\?.*)?$/', '', $request_uri);
define('WT_BASE_URL', $base_uri);

try {
    // No config file? Run the setup wizard
    if (!file_exists(Webtrees::CONFIG_FILE)) {
        define('WT_DATA_DIR', 'data/');

        /** @var SetupController $controller */
        $controller = app(SetupController::class);
        $response   = $controller->setup($request);
        $response->prepare($request)->send();

        return;
    }

    $database_config = parse_ini_file(Webtrees::CONFIG_FILE);

    if ($database_config === false) {
        throw new Exception('Invalid config file: ' . Webtrees::CONFIG_FILE);
    }

    // Read the connection settings and create the database
    Database::connect($database_config);

    // Update the database schema, if necessary.
    app(MigrationService::class)->updateSchema('\Fisharebest\Webtrees\Schema', 'WT_SCHEMA_VERSION', Webtrees::SCHEMA_VERSION);

    // Middleware allows code to intercept the request before it reaches the controller, and to
    // intercept the response afterwards.
    //
    //                   +----------------------------------+
    //                   |           Middleware1            |
    //                   | +------------------------------+ |
    //                   | |         Middleware2          | |
    //                   | | +--------------------------+ | |
    //                   | | |                          | | |
    //       Request ----|-|-|-> Controller::action() --|-|-|---> Response
    //                   | | |                          | | |
    //                   | | +--------------------------+ | |
    //                   | |                              | |
    //                   | +------------------------------+ |
    //                   |                                  |
    //                   +----------------------------------+

    // Create the middleware, from the "inside" to the "outside".
    /** @var Collection $middleware_stack */
    $middleware_stack = app(ModuleService::class)
        ->findByInterface(MiddlewareInterface::class);

    // Core middleware.
    $middleware_stack = $middleware_stack->merge([
        CheckCsrf::class,
        UseTransaction::class,
        Housekeeping::class,
        DebugBarData::class,
        BootModules::class,
        UseTheme::class,
        UseLocale::class,
        UseTree::class,
        UseSession::class,
        UseFilesystem::class,
        CheckForMaintenanceMode::class,
    ]);

    // Construct the core middleware *after* loading the modules, to reduce dependencies.
    $middleware_stack = $middleware_stack->map(function ($middleware): MiddlewareInterface {
        return $middleware instanceof MiddlewareInterface ? $middleware : app($middleware);
    });

    // Create a pipeline, which applies the middleware as a nested function call.
    $pipeline = $middleware_stack->reduce(function (Closure $next, MiddlewareInterface $middleware): Closure {
        // Create a closure to apply the middleware.
        return function (Request $request) use ($middleware, $next): Response {
            return $middleware->handle($request, $next);
        };
    }, function (Request $request): Response {
        // Load the route and routing table.
        $route  = $request->get('route');
        $routes = require 'routes/web.php';

        // Find the controller and action for the selected route
        $controller_action = $routes[$request->getMethod() . ':' . $route] ?? 'ErrorController@noRouteFound';
        [$controller_name, $action] = explode('@', $controller_action);
        $controller_class = '\\Fisharebest\\Webtrees\\Http\\Controllers\\' . $controller_name;

        $controller = app($controller_class);

        return app()->dispatch($controller, $action);
    });

    $response = $pipeline($request);
} catch (Throwable $exception) {
    $response = (new Handler())->render($request, $exception);
}

// Send response
$response->prepare($request)->send();
