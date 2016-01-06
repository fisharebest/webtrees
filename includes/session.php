<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Theme\AdministrationTheme;
use PDOException;

/**
 * This is the bootstrap script, that is run on every request.
 */

// WT_SCRIPT_NAME is defined in each script that the user is permitted to load.
if (!defined('WT_SCRIPT_NAME')) {
	http_response_code(403);

	return;
}

/**
 * We set the following globals
 *
 * @global boolean $SEARCH_SPIDER
 * @global Tree    $WT_TREE
 */
global $WT_TREE, $SEARCH_SPIDER;

// Identify ourself
define('WT_WEBTREES', 'webtrees');
define('WT_VERSION', '1.7.3');

// External URLs
define('WT_WEBTREES_URL', 'http://www.webtrees.net/');
define('WT_WEBTREES_WIKI', 'http://wiki.webtrees.net/');

// Resources have version numbers in the URL, so that they can be cached indefinitely.
define('WT_STATIC_URL', getenv('STATIC_URL')); // We could set this to load our own static resources from a cookie-free domain.

if (getenv('USE_CDN')) {
	// Caution, using a CDN will break support for responsive features in IE8, as respond.js
	// needs to be on the same domain as all the CSS files.
	define('WT_BOOTSTRAP_CSS_URL', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css');
	define('WT_BOOTSTRAP_DATETIMEPICKER_CSS_URL', '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.15.35/css/bootstrap-datetimepicker.min.css');
	define('WT_BOOTSTRAP_DATETIMEPICKER_JS_URL', '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.15.35/js/bootstrap-datetimepicker.min.js');
	define('WT_BOOTSTRAP_JS_URL', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js');
	define('WT_BOOTSTRAP_RTL_CSS_URL', '//cdnjs.cloudflare.com/ajax/libs/bootstrap-rtl/3.2.0-rc2/css/bootstrap-rtl.min.css'); // Cloudflare is out of date
	//define('WT_DATATABLES_BOOTSTRAP_CSS_URL', '//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.css');
	define('WT_DATATABLES_BOOTSTRAP_JS_URL', '//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.js');
	define('WT_FONT_AWESOME_CSS_URL', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css');
	define('WT_JQUERYUI_JS_URL', '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
	define('WT_JQUERYUI_TOUCH_PUNCH_URL', '//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js');
	define('WT_JQUERY_DATATABLES_JS_URL', '//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.7/js/jquery.dataTables.min.js');
	define('WT_JQUERY_JS_URL', '//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js');
	define('WT_JQUERY2_JS_URL', '//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js');
	define('WT_MODERNIZR_JS_URL', '//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js');
	define('WT_MOMENT_JS_URL', '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment-with-locales.min.js'); // 2.10.3 not yet available
	define('WT_RESPOND_JS_URL', '//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js');
} else {
	define('WT_BOOTSTRAP_CSS_URL', WT_STATIC_URL . 'packages/bootstrap-3.3.5/css/bootstrap.min.css');
	define('WT_BOOTSTRAP_DATETIMEPICKER_CSS_URL', WT_STATIC_URL . 'packages/bootstrap-datetimepicker-4.15.35/css/bootstrap-datetimepicker.min.css');
	define('WT_BOOTSTRAP_DATETIMEPICKER_JS_URL', WT_STATIC_URL . 'packages/bootstrap-datetimepicker-4.15.35/js/bootstrap-datetimepicker.min.js');
	define('WT_BOOTSTRAP_JS_URL', WT_STATIC_URL . 'packages/bootstrap-3.3.5/js/bootstrap.min.js');
	define('WT_BOOTSTRAP_RTL_CSS_URL', WT_STATIC_URL . 'packages/bootstrap-rtl-3.3.4/css/bootstrap-rtl.min.css');
	//define('WT_DATATABLES_BOOTSTRAP_CSS_URL', WT_STATIC_URL . 'packages/datatables-1.10.7/plugins/dataTables.bootstrap.css');
	define('WT_DATATABLES_BOOTSTRAP_JS_URL', WT_STATIC_URL . 'packages/datatables-1.10.7/plugins/dataTables.bootstrap.js');
	define('WT_FONT_AWESOME_CSS_URL', WT_STATIC_URL . 'packages/font-awesome-4.4.0/css/font-awesome.min.css');
	define('WT_JQUERYUI_JS_URL', WT_STATIC_URL . 'packages/jquery-ui-1.11.4/js/jquery-ui.min.js');
	define('WT_JQUERYUI_TOUCH_PUNCH_URL', WT_STATIC_URL . 'packages/jqueryui-touch-punch-0.2.3/jquery.ui.touch-punch.min.js');
	define('WT_JQUERY_DATATABLES_JS_URL', WT_STATIC_URL . 'packages/datatables-1.10.7/js/jquery.dataTables.min.js');
	define('WT_JQUERY_JS_URL', WT_STATIC_URL . 'packages/jquery-1.11.3/jquery.min.js');
	define('WT_JQUERY2_JS_URL', WT_STATIC_URL . 'packages/jquery-2.1.4/jquery.min.js');
	define('WT_MODERNIZR_JS_URL', WT_STATIC_URL . 'packages/modernizr-2.8.3/modernizr.min.js');
	define('WT_MOMENT_JS_URL', WT_STATIC_URL . 'packages/moment-2.10.6/moment-with-locales.min.js');
	define('WT_RESPOND_JS_URL', WT_STATIC_URL . 'packages/respond-1.4.2/respond.min.js');
}

// We can't load these from a CDN, as these have been patched.
define('WT_JQUERY_COLORBOX_URL', WT_STATIC_URL . 'assets/js-1.7.2/jquery.colorbox-1.5.14.js');
define('WT_JQUERY_WHEELZOOM_URL', WT_STATIC_URL . 'assets/js-1.7.2/jquery.wheelzoom-2.0.0.js');
define('WT_CKEDITOR_BASE_URL', WT_STATIC_URL . 'packages/ckeditor-4.5.2-custom/');
// See https://github.com/DataTables/Plugins/pull/178
define('WT_DATATABLES_BOOTSTRAP_CSS_URL', WT_STATIC_URL . 'packages/datatables-1.10.7/plugins/dataTables.bootstrap-rtl.css');

// Location of our own scripts
define('WT_ADMIN_JS_URL', WT_STATIC_URL . 'assets/js-1.7.2/admin.js');
define('WT_AUTOCOMPLETE_JS_URL', WT_STATIC_URL . 'assets/js-1.7.2/autocomplete.js');
define('WT_WEBTREES_JS_URL', WT_STATIC_URL . 'assets/js-1.7.2/webtrees.js');
define('WT_FONT_AWESOME_RTL_CSS_URL', WT_STATIC_URL . 'assets/js-1.7.2/font-awesome-rtl.css');

// Location of our modules and themes.  These are used as URLs and folder paths.
define('WT_MODULES_DIR', 'modules_v3/'); // Update setup.php and build/Makefile when this changes
define('WT_THEMES_DIR', 'themes/');

// Enable debugging output on development builds
define('WT_DEBUG', strpos(WT_VERSION, 'dev') !== false);
define('WT_DEBUG_SQL', false);

// Required version of database tables/columns/indexes/etc.
define('WT_SCHEMA_VERSION', 34);

// Regular expressions for validating user input, etc.
define('WT_MINIMUM_PASSWORD_LENGTH', 6);
define('WT_REGEX_XREF', '[A-Za-z0-9:_-]+');
define('WT_REGEX_TAG', '[_A-Z][_A-Z0-9]*');
define('WT_REGEX_INTEGER', '-?\d+');
define('WT_REGEX_BYTES', '[0-9]+[bBkKmMgG]?');
define('WT_REGEX_IPV4', '\d{1,3}(\.\d{1,3}){3}');
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

// Use these line endings when writing files on the server
define('WT_EOL', "\r\n");

// Gedcom specification/definitions
define('WT_GEDCOM_LINE_LENGTH', 255 - strlen(WT_EOL)); // Characters, not bytes

// Used in Google charts
define('WT_GOOGLE_CHART_ENCODING', 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.');

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

// PHP requires a time zone to be set.  We'll set a better one later on.
date_default_timezone_set('UTC');

// Calculate the base URL, so we can generate absolute URLs.
$protocol = Filter::server('HTTP_X_FORWARDED_PROTO', 'https?', Filter::server('HTTPS', null, 'off') === 'off' ? 'http' : 'https');

// For CLI scripts, use localhost.
$host = Filter::server('SERVER_NAME', null, 'localhost');

$port = Filter::server('HTTP_X_FORWARDED_PORT', '80|443', Filter::server('SERVER_PORT', null, '80'));

// Ignore the default port.
if ($protocol === 'http' && $port === '80' || $protocol === 'https' && $port === '443') {
	$port = '';
} else {
	$port = ':' . $port;
}

// REDIRECT_URL should be set when Apache is following a RedirectRule
// PHP_SELF may have trailing path: /path/to/script.php/FOO/BAR
$path = Filter::server('REDIRECT_URL', null, Filter::server('PHP_SELF'));
$path = substr($path, 0, stripos($path, WT_SCRIPT_NAME));

define('WT_BASE_URL', $protocol . '://' . $host . $port . $path);

// Convert PHP errors into exceptions
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
	if (error_reporting() & $errno) {
		throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
	} else {
		return false;
	}
});

set_exception_handler(function (\Exception $ex) {
	$long_message = '';
	$short_message = '';

	foreach ($ex->getTrace() as $level => $frame) {
		$frame += array('args' => array(), 'file' => 'unknown', 'line' => 'unknown');
		array_walk($frame['args'], function (&$arg) {
			switch (gettype($arg)) {
			case 'boolean':
			case 'integer':
			case 'double':
			case 'null':
				$arg = var_export($arg, true);
				break;
			case 'string':
				if (mb_strlen($arg) > 30) {
					$arg = substr($arg, 0, 30) . '…';
				}
				$arg = var_export($arg, true);
				break;
			case 'object':
				$reflection = new \ReflectionClass($arg);
				if (is_object($arg) && method_exists($arg, '__toString')) {
					$arg = '[' . $reflection->getShortName() . ' ' . (string) $arg . ']';
				} else {
					$arg = '[' . $reflection->getShortName() . ']';
				}
				break;
			default:
				$arg = '[' . gettype($arg) . ']';
				break;
			}
		});
		$frame['file'] = str_replace(dirname(__DIR__), '', $frame['file']);
		$long_message .= '#' . $level . ' ' . $frame['file'] . ':' . $frame['line'] . ' ';
		$short_message .= '#' . $level . ' ' . $frame['file'] . ':' . $frame['line'] . ' ';
		if ($level) {
			$long_message .= $frame['function'] . '(' . implode(', ', $frame['args']) . ')' . PHP_EOL;
			$short_message .= $frame['function'] . "()<br>";
		} else {
			$long_message .= get_class($ex) . '("' . $ex->getMessage() . '")' . PHP_EOL;
			$short_message .= get_class($ex) . '("' . $ex->getMessage() . '")<br>';
		}
	}

	if (WT_DEBUG) {
		echo $long_message;
	} else {
		echo $short_message;
	}

	Log::addErrorLog($long_message);
});

// Load our configuration file, so we can connect to the database
if (file_exists(WT_ROOT . 'data/config.ini.php')) {
	$dbconfig = parse_ini_file(WT_ROOT . 'data/config.ini.php');
	// Invalid/unreadable config file?
	if (!is_array($dbconfig)) {
		header('Location: ' . WT_BASE_URL . 'site-unavailable.php');
		exit;
	}
	// Down for maintenance?
	if (file_exists(WT_ROOT . 'data/offline.txt')) {
		header('Location: ' . WT_BASE_URL . 'site-offline.php');
		exit;
	}
} else {
	// No config file. Set one up.
	header('Location: ' . WT_BASE_URL . 'setup.php');
	exit;
}

// What is the remote client's IP address
if (Filter::server('HTTP_CLIENT_IP') !== null) {
	define('WT_CLIENT_IP', Filter::server('HTTP_CLIENT_IP'));
} elseif (Filter::server('HTTP_X_FORWARDED_FOR') !== null) {
	define('WT_CLIENT_IP', Filter::server('HTTP_X_FORWARDED_FOR'));
} else {
	define('WT_CLIENT_IP', Filter::server('REMOTE_ADDR', WT_REGEX_IPV4, '127.0.0.1'));
}

// Connect to the database
try {
	Database::createInstance($dbconfig['dbhost'], $dbconfig['dbport'], $dbconfig['dbname'], $dbconfig['dbuser'], $dbconfig['dbpass']);
	define('WT_TBLPREFIX', $dbconfig['tblpfx']);
	unset($dbconfig);
	// Some of the FAMILY JOIN HUSBAND JOIN WIFE queries can excede the MAX_JOIN_SIZE setting
	Database::exec("SET NAMES 'utf8' COLLATE 'utf8_unicode_ci', SQL_BIG_SELECTS=1");
	// Update the database schema
	Database::updateSchema('\Fisharebest\Webtrees\Schema', 'WT_SCHEMA_VERSION', WT_SCHEMA_VERSION);
} catch (PDOException $ex) {
	FlashMessages::addMessage($ex->getMessage(), 'danger');
	header('Location: ' . WT_BASE_URL . 'site-unavailable.php');
	throw $ex;
}

// The config.ini.php file must always be in a fixed location.
// Other user files can be stored elsewhere...
define('WT_DATA_DIR', realpath(Site::getPreference('INDEX_DIRECTORY') ? Site::getPreference('INDEX_DIRECTORY') : 'data') . DIRECTORY_SEPARATOR);

// If we have a preferred URL (e.g. www.example.com instead of www.isp.com/~example), then redirect to it.
$SERVER_URL = Site::getPreference('SERVER_URL');
if ($SERVER_URL && $SERVER_URL != WT_BASE_URL) {
	header('Location: ' . $SERVER_URL . WT_SCRIPT_NAME . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''), true, 301);
	exit;
}

// Request more resources - if we can/want to
if (!ini_get('safe_mode')) {
	$memory_limit = Site::getPreference('MEMORY_LIMIT');
	if ($memory_limit && strpos(ini_get('disable_functions'), 'ini_set') === false) {
		ini_set('memory_limit', $memory_limit);
	}
	$max_execution_time = Site::getPreference('MAX_EXECUTION_TIME');
	if ($max_execution_time && strpos(ini_get('disable_functions'), 'set_time_limit') === false) {
		set_time_limit($max_execution_time);
	}
}

$rule = Database::prepare(
	"SELECT SQL_CACHE rule FROM `##site_access_rule`" .
	" WHERE IFNULL(INET_ATON(?), 0) BETWEEN ip_address_start AND ip_address_end" .
	" AND ? LIKE user_agent_pattern" .
	" ORDER BY ip_address_end LIMIT 1"
)->execute(array(WT_CLIENT_IP, Filter::server('HTTP_USER_AGENT', null, '')))->fetchOne();

switch ($rule) {
case 'allow':
	$SEARCH_SPIDER = false;
	break;
case 'deny':
	http_response_code(403);
	exit;
case 'robot':
case 'unknown':
	// Search engines don’t send cookies, and so create a new session with every visit.
	// Make sure they always use the same one
	Session::setId('search-engine-' . str_replace('.', '-', WT_CLIENT_IP));
	$SEARCH_SPIDER = true;
	break;
case '':
	Database::prepare(
		"INSERT INTO `##site_access_rule` (ip_address_start, ip_address_end, user_agent_pattern, comment) VALUES (IFNULL(INET_ATON(?), 0), IFNULL(INET_ATON(?), 4294967295), ?, '')"
	)->execute(array(WT_CLIENT_IP, WT_CLIENT_IP, Filter::server('HTTP_USER_AGENT', null, '')));
	$SEARCH_SPIDER = true;
	break;
}

// Store our session data in the database.
session_set_save_handler(
	// open
	function () {
		return true;
	},
	// close
	function () {
		return true;
	},
	// read
	function ($id) {
		return (string) Database::prepare("SELECT session_data FROM `##session` WHERE session_id=?")->execute(array($id))->fetchOne();
	},
	// write
	function ($id, $data) {
		// Only update the session table once per minute, unless the session data has actually changed.
		Database::prepare(
			"INSERT INTO `##session` (session_id, user_id, ip_address, session_data, session_time)" .
			" VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP - SECOND(CURRENT_TIMESTAMP))" .
			" ON DUPLICATE KEY UPDATE" .
			" user_id      = VALUES(user_id)," .
			" ip_address   = VALUES(ip_address)," .
			" session_data = VALUES(session_data)," .
			" session_time = CURRENT_TIMESTAMP - SECOND(CURRENT_TIMESTAMP)"
		)->execute(array($id, (int) Auth::id(), WT_CLIENT_IP, $data));

		return true;
	},
	// destroy
	function ($id) {
		Database::prepare("DELETE FROM `##session` WHERE session_id=?")->execute(array($id));

		return true;
	},
	// gc
	function ($maxlifetime) {
		Database::prepare("DELETE FROM `##session` WHERE session_time < DATE_SUB(NOW(), INTERVAL ? SECOND)")->execute(array($maxlifetime));

		return true;
	}
);

Session::start(array(
	'gc_maxlifetime' => Site::getPreference('SESSION_TIME'),
	'cookie_path'    => parse_url(WT_BASE_URL, PHP_URL_PATH),
));

if (!Auth::isSearchEngine() && !Session::get('initiated')) {
	// A new session, so prevent session fixation attacks by choosing a new PHPSESSID.
	Session::regenerate(false);
	Session::put('initiated', true);
} else {
	// An existing session
}

// Set the tree for the page; (1) the request, (2) the session, (3) the site default, (4) any tree
foreach (array(Filter::post('ged'), Filter::get('ged'), Session::get('GEDCOM'), Site::getPreference('DEFAULT_GEDCOM')) as $tree_name) {
	$WT_TREE = Tree::findByName($tree_name);
	if ($WT_TREE) {
		Session::put('GEDCOM', $tree_name);
		break;
	}
}
// No chosen tree?  Use any one.
if (!$WT_TREE) {
	foreach (Tree::getAll() as $WT_TREE) {
		break;
	}
}

// With no parameters, init() looks to the environment to choose a language
define('WT_LOCALE', I18N::init());
Session::put('locale', WT_LOCALE);

// Note that the database/webservers may not be synchronised, so use DB time throughout.
define('WT_TIMESTAMP', (int) Database::prepare("SELECT UNIX_TIMESTAMP()")->fetchOne());

// Users get their own time-zone.  Visitors get the site time-zone.
if (Auth::check()) {
	date_default_timezone_set(Auth::user()->getPreference('TIMEZONE', 'UTC'));
} else {
	date_default_timezone_set(Site::getPreference('TIMEZONE') ?: 'UTC');
}
define('WT_TIMESTAMP_OFFSET', date_offset_get(new \DateTime('now')));

define('WT_CLIENT_JD', 2440588 + (int) ((WT_TIMESTAMP + WT_TIMESTAMP_OFFSET) / 86400));

// The login URL must be an absolute URL, and can be user-defined
if (Site::getPreference('LOGIN_URL')) {
	define('WT_LOGIN_URL', Site::getPreference('LOGIN_URL'));
} else {
	define('WT_LOGIN_URL', WT_BASE_URL . 'login.php');
}

// If there is no current tree and we need one, then redirect somewhere
if (WT_SCRIPT_NAME != 'admin_trees_manage.php' && WT_SCRIPT_NAME != 'admin_pgv_to_wt.php' && WT_SCRIPT_NAME != 'login.php' && WT_SCRIPT_NAME != 'logout.php' && WT_SCRIPT_NAME != 'import.php' && WT_SCRIPT_NAME != 'help_text.php' && WT_SCRIPT_NAME != 'message.php' && WT_SCRIPT_NAME != 'action.php') {
	if (!$WT_TREE || !$WT_TREE->getPreference('imported')) {
		if (Auth::isAdmin()) {
			header('Location: ' . WT_BASE_URL . 'admin_trees_manage.php');
		} else {
			// We're not an administrator, so we can only log in if there is a tree.
			if (Auth::id()) {
				Auth::logout();
				FlashMessages::addMessage(
					I18N::translate('This user account does not have access to any tree.')
				);
			}
			header('Location: ' . WT_LOGIN_URL . '?url=' . rawurlencode(WT_SCRIPT_NAME . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')), true, 301);

		}
		exit;
	}
}

// Update the last-login time no more than once a minute
if (WT_TIMESTAMP - Session::get('activity_time') >= 60) {
	Auth::user()->setPreference('sessiontime', WT_TIMESTAMP);
	Session::put('activity_time', WT_TIMESTAMP);
}

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
			if (Site::getPreference('ALLOW_USER_THEMES')) {
				Session::put('theme_id', $theme_id);
			}
			break;
		}
	}
}

// Search engines are only allowed to see certain pages.
if (Auth::isSearchEngine() && !in_array(WT_SCRIPT_NAME, array(
	'index.php', 'indilist.php', 'module.php', 'mediafirewall.php',
	'individual.php', 'family.php', 'mediaviewer.php', 'note.php', 'repo.php', 'source.php',
))) {
	http_response_code(403);
	$controller = new PageController;
	$controller->setPageTitle(I18N::translate('Search engine'));
	$controller->pageHeader();
	echo '<p class="ui-state-error">', I18N::translate('You do not have permission to view this page.'), '</p>';
	exit;
}
