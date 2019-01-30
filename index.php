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

use Fisharebest\Localization\Locale as WebtreesLocale;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Exceptions\Handler;
use Fisharebest\Webtrees\Http\Controllers\SetupController;
use Fisharebest\Webtrees\Http\Middleware\CheckCsrf;
use Fisharebest\Webtrees\Http\Middleware\CheckForMaintenanceMode;
use Fisharebest\Webtrees\Http\Middleware\DebugBarData;
use Fisharebest\Webtrees\Http\Middleware\Housekeeping;
use Fisharebest\Webtrees\Http\Middleware\UseTransaction;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\View;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Database\Capsule\Manager as DB;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require __DIR__ . '/vendor/autoload.php';

// Regular expressions for validating user input, etc.
const WT_MINIMUM_PASSWORD_LENGTH = 6;
const WT_REGEX_PASSWORD          = '.{' . WT_MINIMUM_PASSWORD_LENGTH . ',}';

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

// Extract the request parameters.
$request = Request::createFromGlobals();
app()->instance(Request::class, $request);

// Dummy value, until we have created our first tree.
app()->bind(Tree::class, function () {
    return null;
});

// Calculate the base URL, so we can generate absolute URLs.
$request_uri = $request->getSchemeAndHttpHost() . $request->getRequestUri();

// Remove any PHP script name and parameters.
$base_uri = preg_replace('/[^\/]+\.php(\?.*)?$/', '', $request_uri);
define('WT_BASE_URL', $base_uri);

DebugBar::startMeasure('init database');

// Connect to the database
try {
    // No config file? Run the setup wizard
    if (!file_exists(Webtrees::CONFIG_FILE)) {
        define('WT_DATA_DIR', 'data/');
        $controller = new SetupController();
        $response   = $controller->setup($request);
        $response->prepare($request)->send();

        return;
    }

    $database_config = parse_ini_file(Webtrees::CONFIG_FILE);

    if ($database_config === false) {
        throw new Exception('Invalid config file: ' . Webtrees::CONFIG_FILE);
    }

    // Read the connection settings and create the database
    Database::createInstance($database_config);

    // Update the database schema, if necessary.
    Database::updateSchema('\Fisharebest\Webtrees\Schema', 'WT_SCHEMA_VERSION', Webtrees::SCHEMA_VERSION);
} catch (PDOException $exception) {
    define('WT_DATA_DIR', 'data/');
    I18N::init();
    if ($exception->getCode() === 1045) {
        // Error during connection?
        $content = view('errors/database-connection', ['error' => $exception->getMessage()]);
    } else {
        // Error in a migration script?
        $content = view('errors/database-error', ['error' => $exception->getMessage()]);
    }
    $html     = view('layouts/error', ['content' => $content]);
    $response = new Response($html, Response::HTTP_SERVICE_UNAVAILABLE);
    $response->prepare($request)->send();

    return;
} catch (Throwable $exception) {
    define('WT_DATA_DIR', 'data/');
    I18N::init();
    $content  = view('errors/database-connection', ['error' => $exception->getMessage()]);
    $html     = view('layouts/error', ['content' => $content]);
    $response = new Response($html, Response::HTTP_SERVICE_UNAVAILABLE);
    $response->prepare($request)->send();

    return;
}

DebugBar::stopMeasure('init database');

// The config.ini.php file must always be in a fixed location.
// Other user files can be stored elsewhere...
define('WT_DATA_DIR', realpath(Site::getPreference('INDEX_DIRECTORY', 'data/')) . DIRECTORY_SEPARATOR);

$filesystem = new Filesystem(new CachedAdapter(new Local(WT_DATA_DIR), new Memory()));

// Some broken servers block access to their own temp folder using open_basedir...
$filesystem->createDir('tmp');
putenv('TMPDIR=' . WT_DATA_DIR . 'tmp');
Swift_Preferences::getInstance()->setTempDir(WT_DATA_DIR . 'tmp');

// Request more resources - if we can/want to
$memory_limit = Site::getPreference('MEMORY_LIMIT');
if ($memory_limit !== '' && strpos(ini_get('disable_functions'), 'ini_set') === false) {
    ini_set('memory_limit', $memory_limit);
}
$max_execution_time = Site::getPreference('MAX_EXECUTION_TIME');
if ($max_execution_time !== '' && strpos(ini_get('disable_functions'), 'set_time_limit') === false) {
    set_time_limit((int) $max_execution_time);
}

// Sessions
Session::start();

// Note that the database/webservers may not be synchronised, so use DB time throughout.
define('WT_TIMESTAMP', DB::select('SELECT UNIX_TIMESTAMP() AS unix_timestamp')[0]->unix_timestamp);

// Users get their own time-zone. Visitors get the site time-zone.
try {
    if (Auth::check()) {
        date_default_timezone_set(Auth::user()->getPreference('TIMEZONE'));
    } else {
        date_default_timezone_set(Site::getPreference('TIMEZONE'));
    }
} catch (ErrorException $exception) {
    // Server upgrades and migrations can leave us with invalid timezone settings.
    date_default_timezone_set('UTC');
}

define('WT_TIMESTAMP_OFFSET', (new DateTime('now'))->getOffset());

define('WT_CLIENT_JD', 2440588 + intdiv(WT_TIMESTAMP + WT_TIMESTAMP_OFFSET, 86400));

// Update the last-login time no more than once a minute
if (WT_TIMESTAMP - Session::get('activity_time') >= 60) {
    if (Session::get('masquerade') === null) {
        Auth::user()->setPreference('sessiontime', (string) WT_TIMESTAMP);
    }
    Session::put('activity_time', WT_TIMESTAMP);
}

DebugBar::startMeasure('routing');

try {
    // Most requests will need the current tree and user.
    $tree = Tree::findByName($request->get('ged')) ?? null;

    // No tree specified/available?  Choose one.
    if ($tree === null && $request->getMethod() === Request::METHOD_GET) {
        $tree = Tree::findByName(Site::getPreference('DEFAULT_GEDCOM')) ?? array_values(Tree::getAll())[0] ?? null;
    }

    // Select a locale
    define('WT_LOCALE', I18N::init('', $tree));
    Session::put('locale', WT_LOCALE);

    // Most layouts will require a tree for the page header/footer
    View::share('tree', $tree);

    // Load the route and routing table.
    $route  = $request->get('route');
    $routes = require 'routes/web.php';

    // Find the controller and action for the selected route
    $controller_action = $routes[$request->getMethod() . ':' . $route] ?? 'ErrorController@noRouteFound';
    [$controller_name, $action] = explode('@', $controller_action);
    $controller_class = '\\Fisharebest\\Webtrees\\Http\\Controllers\\' . $controller_name;

    app()->instance(Tree::class, $tree);
    app()->instance(User::class, Auth::user());
    app()->instance(LocaleInterface::class, WebtreesLocale::create(WT_LOCALE));
    app()->instance(TimeoutService::class, new TimeoutService(microtime(true)));
    app()->instance(Filesystem::class, $filesystem);

    $controller = app()->make($controller_class);

    DebugBar::stopMeasure('routing');

    DebugBar::startMeasure('init theme');

    // Last theme used?
    $theme_id = Session::get('theme_id');
    // Default for tree
    if (!array_key_exists($theme_id, Theme::themeNames()) && $tree) {
        $theme_id = $tree->getPreference('THEME_DIR');
    }
    // Default for site
    if (!array_key_exists($theme_id, Theme::themeNames())) {
        $theme_id = Site::getPreference('THEME_DIR');
    }
    // Default
    if (!array_key_exists($theme_id, Theme::themeNames())) {
        $theme_id = 'webtrees';
    }
    foreach (Theme::installedThemes() as $theme) {
        if ($theme->name() === $theme_id) {
            Theme::theme($theme);
            // Remember this setting
            if (Site::getPreference('ALLOW_USER_THEMES') === '1') {
                Session::put('theme_id', $theme_id);
            }
            break;
        }
    }

    DebugBar::stopMeasure('init theme');

    // Note that we can't stop this timer, as running the action will
    // generate the response - which includes (and stops) the timer
    DebugBar::startMeasure('controller_action');

    $middleware_stack = [
        CheckForMaintenanceMode::class,
    ];

    if (class_exists(DebugBar::class)) {
        $middleware_stack[] = DebugBarData::class;
    }

    if ($request->getMethod() === Request::METHOD_GET) {
        $middleware_stack[] = Housekeeping::class;
    }

    if ($request->getMethod() === Request::METHOD_POST) {
        $middleware_stack[] = UseTransaction::class;
        $middleware_stack[] = CheckCsrf::class;
    }

    // Apply the middleware using the "onion" pattern.
    $pipeline = array_reduce($middleware_stack, function (Closure $next, string $middleware): Closure {
        // Create a closure to apply the middleware.
        return function (Request $request) use ($middleware, $next): Response {
            return app()->make($middleware)->handle($request, $next);
        };
    }, function (Request $request) use ($controller, $action): Response {
        return app()->dispatch($controller, $action);
    });

    $response = call_user_func($pipeline, $request);
} catch (Exception $exception) {
    $response = (new Handler())->render($request, $exception);
}

// Send response
$response->prepare($request)->send();
