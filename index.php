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
use Fisharebest\Webtrees\Http\Middleware\UseFilesystem;
use Fisharebest\Webtrees\Http\Middleware\UseLocale;
use Fisharebest\Webtrees\Http\Middleware\UseSession;
use Fisharebest\Webtrees\Http\Middleware\UseTheme;
use Fisharebest\Webtrees\Http\Middleware\UseTransaction;
use Fisharebest\Webtrees\Http\Middleware\UseTree;
use Fisharebest\Webtrees\Http\RequestHandlers\RequestHandler;
use Fisharebest\Webtrees\MiddlewareDispatcher;
use Fisharebest\Webtrees\MiddlewareInterface;
use Fisharebest\Webtrees\Request;
use Fisharebest\Webtrees\ServerRequestInterface;
use Fisharebest\Webtrees\Services\MigrationService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Support\Collection;

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
app()->instance(ServerRequestInterface::class, $request);

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

    // Core middleware.
    $middleware_stack = new Collection([
        CheckForMaintenanceMode::class,
        UseFilesystem::class,
        UseSession::class,
        UseTree::class,
        UseTheme::class,
        UseLocale::class,
        BootModules::class,
        DebugBarData::class,
        Housekeeping::class,
        UseTransaction::class,
        CheckCsrf::class,
    ]);

    $module_middleware = app(ModuleService::class)->findByInterface(MiddlewareInterface::class);
    $middleware_stack  = $middleware_stack->merge($module_middleware);

    $dispatcher = new MiddlewareDispatcher($middleware_stack->all(), new RequestHandler());
    $response   = $dispatcher->handle($request);
} catch (Throwable $exception) {
    $response = (new Handler())->render($request, $exception);
}

// Send response
$response->prepare($request)->send();
