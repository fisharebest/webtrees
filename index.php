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
use DateTime;
use ErrorException;
use Exception;
use Fisharebest\Localization\Locale;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Exceptions\Handler;
use Fisharebest\Webtrees\Http\Controllers\SetupController;
use Fisharebest\Webtrees\Http\Middleware\CheckCsrf;
use Fisharebest\Webtrees\Http\Middleware\CheckForMaintenanceMode;
use Fisharebest\Webtrees\Http\Middleware\Housekeeping;
use Fisharebest\Webtrees\Http\Middleware\PageHitCounter;
use Fisharebest\Webtrees\Http\Middleware\UseTransaction;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PDOException;
use Throwable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Identify ourself
define('WT_WEBTREES', 'webtrees');
define('WT_VERSION', '2.0.0-dev');
define('WT_WEBTREES_URL', 'https://www.webtrees.net/');

// Location of our modules and themes. These are used as URLs and folder paths.
define('WT_MODULES_DIR', 'modules_v3/');
define('WT_THEMES_DIR', 'themes/');
define('WT_ASSETS_URL', 'public/assets-2.0.0/'); // See also webpack.mix.js
define('WT_CKEDITOR_BASE_URL', 'public/ckeditor-4.5.2-custom/');

// Enable debugging output on development builds
define('WT_DEBUG', strpos(WT_VERSION, 'dev') !== false);

// Required version of database tables/columns/indexes/etc.
define('WT_SCHEMA_VERSION', 40);

// Regular expressions for validating user input, etc.
define('WT_MINIMUM_PASSWORD_LENGTH', 6);
define('WT_REGEX_XREF', '[A-Za-z0-9:_-]+');
define('WT_REGEX_TAG', '[_A-Z][_A-Z0-9]*');
define('WT_REGEX_INTEGER', '-?\d+');
define('WT_REGEX_BYTES', '[0-9]+[bBkKmMgG]?');
define('WT_REGEX_PASSWORD', '.{' . WT_MINIMUM_PASSWORD_LENGTH . ',}');

define('WT_UTF8_BOM', "\xEF\xBB\xBF"); // U+FEFF (Byte order mark)

// Alternatives to BMD events for lists, charts, etc.
define('WT_EVENTS_BIRT', 'BIRT|CHR|BAPM|_BRTM|ADOP');
define('WT_EVENTS_DEAT', 'DEAT|BURI|CREM');
define('WT_EVENTS_MARR', 'MARR|_NMR');
define('WT_EVENTS_DIV', 'DIV|ANUL|_SEPR');

define('WT_ROOT', __DIR__ . DIRECTORY_SEPARATOR);

// Keep track of time so we can handle timeouts gracefully.
define('WT_START_TIME', microtime(true));

// We want to know about all PHP errors during development, and fewer in production.
if (WT_DEBUG) {
    error_reporting(E_ALL | E_STRICT | E_NOTICE | E_DEPRECATED);
} else {
    error_reporting(E_ALL);
}

require WT_ROOT . 'vendor/autoload.php';

// Initialise the DebugBar for development.
// Use `composer install --dev` on a development build to enable.
// Note that you may need to increase the size of the fcgi buffers on nginx.
// e.g. add these lines to your fastcgi_params file:
// fastcgi_buffers 16 16m;
// fastcgi_buffer_size 32m;
DebugBar::init(WT_DEBUG && class_exists('\\DebugBar\\StandardDebugBar'));

// PHP requires a time zone to be set. We'll set a better one later on.
date_default_timezone_set('UTC');

// Calculate the base URL, so we can generate absolute URLs.
$request     = Request::createFromGlobals();
$request_uri = $request->getSchemeAndHttpHost() . $request->getRequestUri();

// Remove any PHP script name and parameters.
$base_uri = preg_replace('/[^\/]+\.php(\?.*)?$/', '', $request_uri);
define('WT_BASE_URL', $base_uri);

// Convert PHP warnings/notices into exceptions
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    // Ignore errors that are silenced with '@'
    if (error_reporting() & $errno) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
});

DebugBar::startMeasure('init database');

// Load our configuration file, so we can connect to the database
define('WT_CONFIG_FILE', 'data/config.ini.php');
if (!file_exists(WT_ROOT . WT_CONFIG_FILE)) {
    // No config file. Set one up.
    define('WT_DATA_DIR', 'data/');
    $request  = Request::createFromGlobals();
    $response = (new SetupController())->setup($request);
    $response->prepare($request)->send();

    return;
}

// Connect to the database
try {
    // Read the connection settings and create the database
    Database::createInstance(parse_ini_file(WT_ROOT . 'data/config.ini.php'));

    // Update the database schema, if necessary.
    Database::updateSchema('\Fisharebest\Webtrees\Schema', 'WT_SCHEMA_VERSION', WT_SCHEMA_VERSION);
} catch (PDOException $ex) {
    DebugBar::addThrowable($ex);

    define('WT_DATA_DIR', 'data/');
    I18N::init();
    if ($ex->getCode() === 1045) {
        // Error during connection?
        $content = view('errors/database-connection', ['error' => $ex->getMessage()]);
    } else {
        // Error in a migration script?
        $content = view('errors/database-error', ['error' => $ex->getMessage()]);
    }
    $html     = view('layouts/error', ['content' => $content]);
    $response = new Response($html, 503);
    $response->prepare($request)->send();
    return;
} catch (Throwable $ex) {
    DebugBar::addThrowable($ex);

    define('WT_DATA_DIR', 'data/');
    I18N::init();
    $content  = view('errors/database-connection', ['error' => $ex->getMessage()]);
    $html     = view('layouts/error', ['content' => $content]);
    $response = new Response($html, 503);
    $response->prepare($request)->send();
    return;
}

DebugBar::stopMeasure('init database');

// The config.ini.php file must always be in a fixed location.
// Other user files can be stored elsewhere...
define('WT_DATA_DIR', realpath(Site::getPreference('INDEX_DIRECTORY', 'data/')) . DIRECTORY_SEPARATOR);

// Some broken servers block access to their own temp folder using open_basedir...
$data_dir = new Filesystem(new Local(WT_DATA_DIR));
$data_dir->createDir('tmp');
putenv('TMPDIR=' . WT_DATA_DIR . 'tmp');

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

DebugBar::startMeasure('init i18n');

// With no parameters, init() looks to the environment to choose a language
define('WT_LOCALE', I18N::init());
Session::put('locale', WT_LOCALE);

DebugBar::stopMeasure('init i18n');

// Note that the database/webservers may not be synchronised, so use DB time throughout.
define('WT_TIMESTAMP', (int) Database::prepare("SELECT UNIX_TIMESTAMP()")->fetchOne());

// Users get their own time-zone. Visitors get the site time-zone.
try {
    if (Auth::check()) {
        date_default_timezone_set(Auth::user()->getPreference('TIMEZONE'));
    } else {
        date_default_timezone_set(Site::getPreference('TIMEZONE'));
    }
} catch (ErrorException $ex) {
    // Server upgrades and migrations can leave us with invalid timezone settings.
    date_default_timezone_set('UTC');
}

define('WT_TIMESTAMP_OFFSET', date_offset_get(new DateTime('now')));

define('WT_CLIENT_JD', 2440588 + (int) ((WT_TIMESTAMP + WT_TIMESTAMP_OFFSET) / 86400));

// Update the last-login time no more than once a minute
if (WT_TIMESTAMP - Session::get('activity_time') >= 60) {
    if (Session::get('masquerade') === null) {
        Auth::user()->setPreference('sessiontime', (string) WT_TIMESTAMP);
    }
    Session::put('activity_time', WT_TIMESTAMP);
}

DebugBar::startMeasure('routing');

// The HTTP request.
$request = Request::createFromGlobals();
$route   = $request->get('route');

try {
    // Most requests will need the current tree and user.
    $all_trees = Tree::getAll();

    $tree = $all_trees[$request->get('ged')] ?? null;

    // No tree specified/available?  Choose one.
    if ($tree === null && $request->getMethod() === Request::METHOD_GET) {
        $tree = $all_trees[Site::getPreference('DEFAULT_GEDCOM')] ?? array_values($all_trees)[0] ?? null;
    }

    $request->attributes->set('tree', $tree);
    $request->attributes->set('user', Auth::user());

    // Most layouts will require a tree for the page header/footer
    View::share('tree', $tree);

    // Load the routing table.
    $routes = require 'routes/web.php';

    // Find the controller and action for the selected route
    $controller_action = $routes[$request->getMethod() . ':' . $route] ?? 'ErrorController@noRouteFound';
    list($controller_name, $action) = explode('@', $controller_action);
    $controller_class = __NAMESPACE__ . '\\Http\\Controllers\\' . $controller_name;

    // Set up dependency injection for the controllers.
    $resolver = new Resolver();
    $resolver->bind(Resolver::class, $resolver);
    $resolver->bind(Tree::class, $tree);
    $resolver->bind(User::class, Auth::user());
    $resolver->bind(LocaleInterface::class, Locale::create(WT_LOCALE));

    $controller = $resolver->resolve($controller_class);

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
        if ($theme->themeId() === $theme_id) {
            Theme::theme($theme)->init($tree);
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
    DebugBar::startMeasure('controller_action', $controller_action);

    $middleware_stack = [
        CheckForMaintenanceMode::class,
    ];

    if ($request->getMethod() === Request::METHOD_GET) {
        $middleware_stack[] = PageHitCounter::class;
        $middleware_stack[] = Housekeeping::class;
    }

    if ($request->getMethod() === Request::METHOD_POST) {
        $middleware_stack[] = UseTransaction::class;
        $middleware_stack[] = CheckCsrf::class;
    }

    // Apply the middleware using the "onion" pattern.
    $pipeline = array_reduce($middleware_stack, function (Closure $next, string $middleware) use ($resolver): Closure {
        // Create a closure to apply the middleware.
        return function (Request $request) use ($middleware, $next, $resolver): Response {
            return $resolver->resolve($middleware)->handle($request, $next);
        };
    }, function (Request $request) use ($controller, $action, $resolver): Response {
        $resolver->bind(Request::class, $request);

        return $resolver->dispatch($controller, $action);
    });

    $response = call_user_func($pipeline, $request);
} catch (Exception $exception) {
    DebugBar::addThrowable($exception);

    $response = (new Handler())->render($request, $exception);
}

// Send response
if ($response instanceof RedirectResponse) {
    // Show the debug data on the next page
    DebugBar::stackData();
} elseif ($response instanceof JsonResponse) {
    // Use HTTP headers and some jQuery to add debug to the current page.
    DebugBar::sendDataInHeaders();
}

$response->prepare($request)->send();
