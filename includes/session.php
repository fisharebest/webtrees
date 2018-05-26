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
namespace Fisharebest\Webtrees;

use DateTime;
use ErrorException;
use Fisharebest\Webtrees\Theme\AdministrationTheme;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PDOException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * We set the following globals
 *
 * @global Tree    $WT_TREE
 */
global $WT_TREE;

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
define('WT_SCHEMA_VERSION', 38);

// Regular expressions for validating user input, etc.
define('WT_MINIMUM_PASSWORD_LENGTH', 6);
define('WT_REGEX_XREF', '[A-Za-z0-9:_-]+');
define('WT_REGEX_TAG', '[_A-Z][_A-Z0-9]*');
define('WT_REGEX_INTEGER', '-?\d+');
define('WT_REGEX_BYTES', '[0-9]+[bBkKmMgG]?');
define('WT_REGEX_PASSWORD', '.{' . WT_MINIMUM_PASSWORD_LENGTH . ',}');

// UTF8 representation of various characters
define('WT_UTF8_BOM', "\xEF\xBB\xBF"); // U+FEFF (Byte order mark)
define('WT_UTF8_LRM', "\xE2\x80\x8E"); // U+200E (Left to Right mark:  zero-width character with LTR directionality)
define('WT_UTF8_RLM', "\xE2\x80\x8F"); // U+200F (Right to Left mark:  zero-width character with RTL directionality)
define('WT_UTF8_LRO', "\xE2\x80\xAD"); // U+202D (Left to Right override: force everything following to LTR mode)
define('WT_UTF8_RLO', "\xE2\x80\xAE"); // U+202E (Right to Left override: force everything following to RTL mode)
define('WT_UTF8_LRE', "\xE2\x80\xAA"); // U+202A (Left to Right embedding: treat everything following as LTR text)
define('WT_UTF8_RLE', "\xE2\x80\xAB"); // U+202B (Right to Left embedding: treat everything following as RTL text)
define('WT_UTF8_PDF', "\xE2\x80\xAC"); // U+202C (Pop directional formatting: restore state prior to last LRO, RLO, LRE, RLE)

// Alternatives to BMD events for lists, charts, etc.
define('WT_EVENTS_BIRT', 'BIRT|CHR|BAPM|_BRTM|ADOP');
define('WT_EVENTS_DEAT', 'DEAT|BURI|CREM');
define('WT_EVENTS_MARR', 'MARR|_NMR');
define('WT_EVENTS_DIV', 'DIV|ANUL|_SEPR');

// For performance, it is quicker to refer to files using absolute paths
define('WT_ROOT', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);

// Keep track of time statistics, for the summary in the footer
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

// What is the name of the requested script.
define('WT_SCRIPT_NAME', basename(Filter::server('SCRIPT_NAME')));

// Convert PHP warnings/notices into exceptions
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
	// Ignore errors that are silenced with '@'
	if (error_reporting() & $errno) {
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
});

DebugBar::startMeasure('init database');

// Load our configuration file, so we can connect to the database
if (!file_exists(WT_ROOT . 'data/config.ini.php')) {
	// No config file. Set one up.
	$url      = Html::url('setup.php', ['route' => 'setup']);
	$response = new RedirectResponse($url);
	$response->send();
	exit;
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
	exit;
} catch (Throwable $ex) {
	DebugBar::addThrowable($ex);

	define('WT_DATA_DIR', 'data/');
	I18N::init();
	$content = view('errors/database-connection', ['error' => $ex->getMessage()]);
	$html     = view('layouts/error', ['content' => $content]);
	$response = new Response($html, 503);
	$response->prepare($request)->send();
	exit;
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
if (!ini_get('safe_mode')) {
	$memory_limit = Site::getPreference('MEMORY_LIMIT');
	if ($memory_limit !== '' && strpos(ini_get('disable_functions'), 'ini_set') === false) {
		ini_set('memory_limit', $memory_limit);
	}
	$max_execution_time = Site::getPreference('MAX_EXECUTION_TIME');
	if ($max_execution_time !== '' && strpos(ini_get('disable_functions'), 'set_time_limit') === false) {
		set_time_limit($max_execution_time);
	}
}

// Sessions
Session::setSaveHandler();
Session::start([
	'gc_maxlifetime' => Site::getPreference('SESSION_TIME'),
	'cookie_path'    => implode('/', array_map('rawurlencode', explode('/', parse_url(WT_BASE_URL, PHP_URL_PATH)))),
]);

// A new session, so prevent session fixation attacks by choosing a new PHPSESSID.
if (!Session::get('initiated')) {
	Session::regenerate(true);
	Session::put('initiated', true);
}

DebugBar::startMeasure('init tree');

// Set the tree for the page; (1) the request, (2) the session, (3) the site default, (4) any tree
foreach ([Filter::post('ged'), Filter::get('ged'), Site::getPreference('DEFAULT_GEDCOM')] as $tree_name) {
	$WT_TREE = Tree::findByName($tree_name);
	if ($WT_TREE) {
		break;
	}
}
// No chosen tree? Use any one.
if (!$WT_TREE) {
	foreach (Tree::getAll() as $WT_TREE) {
		break;
	}
}

DebugBar::stopMeasure('init tree');

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

// Redirect to login url
if (!$WT_TREE && !Auth::check() && WT_SCRIPT_NAME !== 'index.php') {
	header('Location: ' . route('login', ['url' => $request->getRequestUri()]));
	exit;
}

// Update the last-login time no more than once a minute
if (WT_TIMESTAMP - Session::get('activity_time') >= 60) {
	if (Session::get('masquerade') === null) {
		Auth::user()->setPreference('sessiontime', WT_TIMESTAMP);
	}
	Session::put('activity_time', WT_TIMESTAMP);
}

DebugBar::startMeasure('init theme');

// Set the theme
if (substr(WT_SCRIPT_NAME, 0, 5) === 'admin' || WT_SCRIPT_NAME === 'module.php' && substr(Filter::get('mod_action'), 0, 5) === 'admin') {
	// Administration scripts begin with “admin” and use a special administration theme
	Theme::theme(new AdministrationTheme)->init($WT_TREE);
} else {
	// Last theme used?
	$theme_id = Session::get('theme_id');
	// Default for tree
	if (!array_key_exists($theme_id, Theme::themeNames()) && $WT_TREE) {
		$theme_id = $WT_TREE->getPreference('THEME_DIR');
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
			Theme::theme($theme)->init($WT_TREE);
			// Remember this setting
			if (Site::getPreference('ALLOW_USER_THEMES') === '1') {
				Session::put('theme_id', $theme_id);
			}
			break;
		}
	}
}

DebugBar::stopMeasure('init theme');

