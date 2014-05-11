<?php
// Startup and session logic
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2011 PGV Development Team.  All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

// WT_SCRIPT_NAME is defined in each script that the user is permitted to load.
if (!defined('WT_SCRIPT_NAME')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Identify ourself
define('WT_WEBTREES',     'webtrees');
define('WT_VERSION',      '1.5.4-dev');
define('WT_VERSION_TEXT', WT_VERSION); // Deprecated

// External URLs
define('WT_WEBTREES_URL',  'http://www.webtrees.net/');
define('WT_WEBTREES_WIKI', 'http://wiki.webtrees.net/');

// Optionally, specify a CDN server for static content (e.g. CSS, JS, PNG)
// For example, http://my.cdn.com/webtrees-static-1.3.1/
define('WT_STATIC_URL', ''); // For example, http://my.cdn.com/webtrees-static-1.3.1/

// Optionally, load major JS libraries from Google’s public CDN
define ('WT_USE_GOOGLE_API', false);
if (WT_USE_GOOGLE_API) {
	define('WT_JQUERY_URL',        'https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js');
	define('WT_JQUERYUI_URL',      'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js');
} else {
	define('WT_JQUERY_URL',        WT_STATIC_URL.'js/jquery-1.11.1.js');
	define('WT_JQUERYUI_URL',      WT_STATIC_URL.'js/jquery-ui-1.10.4.js');
}
define('WT_JQUERY_COLORBOX_URL',   WT_STATIC_URL.'js/jquery.colorbox-1.5.9.js');
define('WT_JQUERY_COOKIE_URL',     WT_STATIC_URL.'js/jquery.cookie-1.4.1.js');
define('WT_JQUERY_DATATABLES_URL', WT_STATIC_URL.'js/jquery.datatables-1.10.0.js');
define('WT_JQUERY_JEDITABLE_URL',  WT_STATIC_URL.'js/jquery.jeditable-1.7.3.js');
define('WT_JQUERY_WHEELZOOM_URL',  WT_STATIC_URL.'js/jquery.wheelzoom-2.0.0.js');
define('WT_MODERNIZR_URL',         WT_STATIC_URL.'js/modernizr.custom-2.6.2.js');
define('WT_WEBTREES_JS_URL',       WT_STATIC_URL.'js/webtrees-1.5.4.js');

// Location of our modules and themes.  These are used as URLs and folder paths.
define('WT_MODULES_DIR', 'modules_v3/'); // Update setup.php and build/Makefile when this changes
define('WT_THEMES_DIR',  'themes/' );

// Enable debugging output?
define('WT_DEBUG',      false);
define('WT_DEBUG_SQL',  false);
define('WT_DEBUG_LANG', false);

// Error reporting
define('WT_ERROR_LEVEL', 2); // 0=none, 1=minimal, 2=full

// Required version of database tables/columns/indexes/etc.
define('WT_SCHEMA_VERSION', 27);

// Regular expressions for validating user input, etc.
define('WT_MINIMUM_PASSWORD_LENGTH', 6);

define('WT_REGEX_XREF',     '[A-Za-z0-9:_-]+');
define('WT_REGEX_TAG',      '[_A-Z][_A-Z0-9]*');
define('WT_REGEX_INTEGER',  '-?\d+');
define('WT_REGEX_ALPHA',    '[a-zA-Z]+');
define('WT_REGEX_ALPHANUM', '[a-zA-Z0-9]+');
define('WT_REGEX_BYTES',    '[0-9]+[bBkKmMgG]?');
define('WT_REGEX_USERNAME', '[^<>"%{};]+');
define('WT_REGEX_PASSWORD', '.{'.WT_MINIMUM_PASSWORD_LENGTH.',}');

// UTF8 representation of various characters
define('WT_UTF8_BOM',    "\xEF\xBB\xBF"); // U+FEFF

// UTF8 control codes affecting the BiDirectional algorithm (see http://www.unicode.org/reports/tr9/)
define('WT_UTF8_LRM',    "\xE2\x80\x8E"); // U+200E  (Left to Right mark:  zero-width character with LTR directionality)
define('WT_UTF8_RLM',    "\xE2\x80\x8F"); // U+200F  (Right to Left mark:  zero-width character with RTL directionality)
define('WT_UTF8_LRO',    "\xE2\x80\xAD"); // U+202D  (Left to Right override: force everything following to LTR mode)
define('WT_UTF8_RLO',    "\xE2\x80\xAE"); // U+202E  (Right to Left override: force everything following to RTL mode)
define('WT_UTF8_LRE',    "\xE2\x80\xAA"); // U+202A  (Left to Right embedding: treat everything following as LTR text)
define('WT_UTF8_RLE',    "\xE2\x80\xAB"); // U+202B  (Right to Left embedding: treat everything following as RTL text)
define('WT_UTF8_PDF',    "\xE2\x80\xAC"); // U+202C  (Pop directional formatting: restore state prior to last LRO, RLO, LRE, RLE)

// Alternatives to BMD events for lists, charts, etc.
define('WT_EVENTS_BIRT', 'BIRT|CHR|BAPM|_BRTM|ADOP');
define('WT_EVENTS_DEAT', 'DEAT|BURI|CREM');
define('WT_EVENTS_MARR', 'MARR|_NMR');
define('WT_EVENTS_DIV',  'DIV|ANUL|_SEPR');

// Use these line endings when writing files on the server
define('WT_EOL', "\r\n");

// Gedcom specification/definitions
define ('WT_GEDCOM_LINE_LENGTH', 255-strlen(WT_EOL)); // Characters, not bytes

// Used in Google charts
define ('WT_GOOGLE_CHART_ENCODING', 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.');

// Privacy constants
define('WT_PRIV_PUBLIC',  2); // Allows visitors to view the marked information
define('WT_PRIV_USER',    1); // Allows members to access the marked information
define('WT_PRIV_NONE',    0); // Allows managers to access the marked information
define('WT_PRIV_HIDE',   -1); // Hide the item to all users

// For performance, it is quicker to refer to files using absolute paths
define ('WT_ROOT', realpath(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR);

// Keep track of time statistics, for the summary in the footer
$start_time=microtime(true);

// We want to know about all PHP errors
error_reporting(E_ALL | E_STRICT);

////////////////////////////////////////////////////////////////////////////////
// Provide password functions for PHP5.4 and earlier
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('password_hash')) {
	// The compatibility library requires the $2$y salt prefix, which is available
	// in PHP5.3.7 and *some* earlier/patched versions.
	$hash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
	if (crypt("password", $hash) === $hash) {
		require WT_ROOT.'library/ircmaxell/password-compat/lib/password.php';
	} else {
		// For older/unpatched versions of PHP, use the default crypt behaviour.
		function password_hash($password) {
			$salt = '$2a$12$';
			$salt_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
			for ($i = 0; $i < 22; ++$i) {
				$salt .= substr($salt_chars, mt_rand(0, 63), 1);
			}
			return crypt($password, $salt);
		}

		function password_needs_rehash() {
			return false;
		}

		function password_verify($password, $hash) {
			return crypt($password, $hash) === $hash;
		}

		define('PASSWORD_DEFAULT', 1);
	}
}

// PHP5.3 may be using magic-quotes :-(
if (version_compare(PHP_VERSION, '5.4', '<') && get_magic_quotes_gpc()) {
	// http://php.net/manual/en/security.magicquotes.disabling.php
	$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
	while (list($key, $val) = each($process)) {
		foreach ($val as $k => $v) {
			unset($process[$key][$k]);
			if (is_array($v)) {
				$process[$key][stripslashes($k)] = $v;
				$process[] = &$process[$key][stripslashes($k)];
			} else {
				$process[$key][stripslashes($k)] = stripslashes($v);
			}
		}
	}
	unset($process);
}

require WT_ROOT.'library/autoload.php';

// PHP requires a time zone to be set in php.ini
if (!ini_get('date.timezone')) {
	date_default_timezone_set(@date_default_timezone_get());
}

// Split the request protocol://host:port/path/to/script.php?var=value into parts
// WT_SERVER_NAME  = protocol://host:port
// WT_SCRIPT_PATH  = /path/to/   (begins and ends with /)
// WT_SCRIPT_NAME  = script.php  (already defined in the calling script)
// WT_QUERY_STRING = ?var=value  (generate as needed from $_GET.  lang=xx and theme=yy are removed as used.)
// TODO: we ought to generate this dynamically, but lots of code currently relies on this global
$QUERY_STRING=isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';

$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
define('WT_SERVER_NAME',
	($https ?  'https://' : 'http://').
	(empty($_SERVER['SERVER_NAME']) ? '' : $_SERVER['SERVER_NAME']).
	(empty($_SERVER['SERVER_PORT']) || (!$https && $_SERVER['SERVER_PORT']==80) || ($https && $_SERVER['SERVER_PORT']==443) ? '' : ':'.$_SERVER['SERVER_PORT'])
);

// REDIRECT_URL should be set in the case of Apache following a RedirectRule
// SCRIPT_NAME should always be correct, but is not always present.
// PHP_SELF should always be present, but may have trailing path: /path/to/script.php/FOO/BAR
if (!empty($_SERVER['REDIRECT_URL'])) {
	define('WT_SCRIPT_PATH', substr($_SERVER['REDIRECT_URL'], 0, stripos($_SERVER['REDIRECT_URL'], WT_SCRIPT_NAME)));
} elseif (!empty($_SERVER['SCRIPT_NAME'])) {
	define('WT_SCRIPT_PATH', substr($_SERVER['SCRIPT_NAME'], 0, stripos($_SERVER['SCRIPT_NAME'], WT_SCRIPT_NAME)));
} elseif (!empty($_SERVER['PHP_SELF'])) {
	define('WT_SCRIPT_PATH', substr($_SERVER['PHP_SELF'], 0, stripos($_SERVER['PHP_SELF'], WT_SCRIPT_NAME)));
} else {
	// No server settings - probably running as a command line script
	define('WT_SCRIPT_PATH', '/');
}

// Microsoft IIS servers don’t set REQUEST_URI, so generate it for them.
if (!isset($_SERVER['REQUEST_URI']))  {
	$_SERVER['REQUEST_URI']=substr($_SERVER['PHP_SELF'], 1);
	if (isset($_SERVER['QUERY_STRING'])) {
		$_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING'];
	}
}

// Some browsers do not send a user-agent string
if (!isset($_SERVER['HTTP_USER_AGENT'])) {
	$_SERVER['HTTP_USER_AGENT'] = '';
}

// Common functions
require WT_ROOT.'includes/functions/functions.php';
require WT_ROOT.'includes/functions/functions_db.php';
// TODO: Not all pages require all of these.  Only load them in scripts that need them?
require WT_ROOT.'includes/functions/functions_print.php';
require WT_ROOT.'includes/functions/functions_mediadb.php';
require WT_ROOT.'includes/functions/functions_date.php';
require WT_ROOT.'includes/functions/functions_charts.php';
require WT_ROOT.'includes/functions/functions_utf-8.php';

// Set a custom error handler
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
	if ((error_reporting() > 0)&&($errno<2048)) {
		if (WT_ERROR_LEVEL==0) {
			return;
		}
		$fmt_msg="<br>ERROR {$errno}: {$errstr}<br>";
		$log_msg="ERROR {$errno}: {$errstr};";
		// Although debug_backtrace should always exist in PHP5, without this check, PHP sometimes crashes.
		// Possibly calling it generates an error, which causes infinite recursion??
		if ($errno < 16 && function_exists("debug_backtrace") && strstr($errstr, "headers already sent by") === false) {
			$backtrace = debug_backtrace();
			$num = count($backtrace);
			if (WT_ERROR_LEVEL == 1) {
				$num = 1;
			}
			for ($i = 0; $i < $num; $i++) {
				if ($i === 0) {
					$fmt_msg .= "0 Error occurred on ";
					$log_msg .= "\n0 Error occurred on ";
				} else {
					$fmt_msg .= "{$i} called from ";
					$log_msg .= "\n{$i} called from ";
				}
				if (isset($backtrace[$i]["line"]) && isset($backtrace[$i]["file"])) {
					$fmt_msg .= "line <b>{$backtrace[$i]['line']}</b> of file <b>".basename($backtrace[$i]['file'])."</b>";
					$log_msg .= "line {$backtrace[$i]['line']} of file ".basename($backtrace[$i]['file']);
				}
				if ($i<$num-1) {
					$fmt_msg .= " in function <b>".$backtrace[$i+1]['function']."</b>";
					$log_msg .= " in function ".$backtrace[$i+1]['function'];
				}
				$fmt_msg .= "<br>";
			}
		}
		echo $fmt_msg;
		\WT\Log::addErrorLog($log_msg);
		if ($errno == 1) {
			die();
		}
	}
	return false;
});

// Load our configuration file, so we can connect to the database
if (file_exists(WT_ROOT.'data/config.ini.php')) {
	$dbconfig=parse_ini_file(WT_ROOT.'data/config.ini.php');
	// Invalid/unreadable config file?
	if (!is_array($dbconfig)) {
		header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'site-unavailable.php');
		exit;
	}
	// Down for maintenance?
	if (file_exists(WT_ROOT.'data/offline.txt')) {
		header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'site-offline.php');
		exit;
	}
} else {
	// No config file. Set one up.
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'setup.php');
	exit;
}

$WT_REQUEST=new Zend_Controller_Request_Http();

require WT_ROOT.'includes/authentication.php';

// Connect to the database
try {
	WT_DB::createInstance($dbconfig['dbhost'], $dbconfig['dbport'], $dbconfig['dbname'], $dbconfig['dbuser'], $dbconfig['dbpass']);
	define('WT_TBLPREFIX', $dbconfig['tblpfx']);
	unset($dbconfig);
	// Some of the FAMILY JOIN HUSBAND JOIN WIFE queries can excede the MAX_JOIN_SIZE setting
	WT_DB::exec("SET NAMES 'utf8' COLLATE 'utf8_unicode_ci', SQL_BIG_SELECTS=1");
	try {
		WT_DB::updateSchema(WT_ROOT.'includes/db_schema/', 'WT_SCHEMA_VERSION', WT_SCHEMA_VERSION);
	} catch (PDOException $ex) {
		// The schema update scripts should never fail.  If they do, there is no clean recovery.
		die($ex);
	}
} catch (PDOException $ex) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'site-unavailable.php');
	exit;
}

// The config.ini.php file must always be in a fixed location.
// Other user files can be stored elsewhere...
define('WT_DATA_DIR', realpath(WT_Site::preference('INDEX_DIRECTORY') ? WT_Site::preference('INDEX_DIRECTORY') : 'data').DIRECTORY_SEPARATOR);

// If we have a preferred URL (e.g. www.example.com instead of www.isp.com/~example), then redirect to it.
$SERVER_URL=WT_Site::preference('SERVER_URL');
if ($SERVER_URL && $SERVER_URL != WT_SERVER_NAME.WT_SCRIPT_PATH) {
	header('Location: '.$SERVER_URL.WT_SCRIPT_NAME.($QUERY_STRING ? '?'.$QUERY_STRING : ''), true, 301);
	exit;
}

// Request more resources - if we can/want to
if (!ini_get('safe_mode')) {
	$memory_limit=WT_Site::preference('MEMORY_LIMIT');
	if ($memory_limit) {
		ini_set('memory_limit', $memory_limit);
	}
	$max_execution_time=WT_Site::preference('MAX_EXECUTION_TIME');
	if ($max_execution_time && strpos(ini_get('disable_functions'), 'set_time_limit')===false) {
		set_time_limit($max_execution_time);
	}
}

$rule=WT_DB::prepare(
	"SELECT SQL_CACHE rule FROM `##site_access_rule`" .
	" WHERE IFNULL(INET_ATON(?), 0) BETWEEN ip_address_start AND ip_address_end" .
	" AND ? LIKE user_agent_pattern" .
	" ORDER BY ip_address_end-ip_address_start"
)->execute(array($WT_REQUEST->getClientIp(), $_SERVER['HTTP_USER_AGENT']))->fetchOne();

switch ($rule) {
case 'allow':
	$SEARCH_SPIDER=false;
	break;
case 'deny':
	header('HTTP/1.1 403 Access Denied');
	exit;
case 'robot':
case 'unknown':
	// Search engines don’t send cookies, and so create a new session with every visit.
	// Make sure they always use the same one
	Zend_Session::setId('search-engine-'.str_replace('.', '-', $WT_REQUEST->getClientIp()));
	$SEARCH_SPIDER=true;
	break;
case '':
	WT_DB::prepare(
		"INSERT INTO `##site_access_rule` (ip_address_start, ip_address_end, user_agent_pattern, comment) VALUES (IFNULL(INET_ATON(?), 0), IFNULL(INET_ATON(?), 4294967295), ?, '')"
	)->execute(array($WT_REQUEST->getClientIp(), $WT_REQUEST->getClientIp(), $_SERVER['HTTP_USER_AGENT']));
	$SEARCH_SPIDER=true;
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
		return WT_DB::prepare("SELECT session_data FROM `##session` WHERE session_id=?")->execute(array($id))->fetchOne();
	},
	// write
	function ($id, $data) use ($WT_REQUEST) {
		// Only update the session table once per minute, unless the session data has actually changed.
		WT_DB::prepare(
			"INSERT INTO `##session` (session_id, user_id, ip_address, session_data, session_time)" .
			" VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP - SECOND(CURRENT_TIMESTAMP))" .
			" ON DUPLICATE KEY UPDATE" .
			" user_id      = VALUES(user_id)," .
			" ip_address   = VALUES(ip_address)," .
			" session_data = VALUES(session_data)," .
			" session_time = CURRENT_TIMESTAMP - SECOND(CURRENT_TIMESTAMP)"
		)->execute(array($id, (int)\WT\Auth::id(), $WT_REQUEST->getClientIp(), $data));
		return true;
	},
	// destroy
	function ($id) {
		WT_DB::prepare("DELETE FROM `##session` WHERE session_id=?")->execute(array($id));
		return true;
	},
	// gc
	function ($maxlifetime) {
		WT_DB::prepare("DELETE FROM `##session` WHERE session_time < DATE_SUB(NOW(), INTERVAL ? SECOND)")->execute(array($maxlifetime));
		return true;
	}
);

// Use the Zend_Session object to start the session.
// This allows all the other Zend Framework components to integrate with the session
define('WT_SESSION_NAME', 'WT_SESSION');
$cfg=array(
	'name'            => WT_SESSION_NAME,
	'cookie_lifetime' => 0,
	'gc_maxlifetime'  => WT_Site::preference('SESSION_TIME'),
	'gc_probability'  => 1,
	'gc_divisor'      => 100,
	'cookie_path'     => WT_SCRIPT_PATH,
	'cookie_httponly' => true,
);

Zend_Session::start($cfg);

// Register a session “namespace” to store session data.  This is better than
// using $_SESSION, as we can avoid clashes with other modules or applications,
// and problems with servers that have enabled “register_globals”.
$WT_SESSION=new Zend_Session_Namespace('WEBTREES');

if (!$SEARCH_SPIDER && !$WT_SESSION->initiated) {
	// A new session, so prevent session fixation attacks by choosing a new PHPSESSID.
	Zend_Session::regenerateId();
	$WT_SESSION->initiated=true;
} else {
	// An existing session
}

// Who are we?
define('WT_USER_ID',       \WT\Auth::id());
define('WT_USER_NAME',     \WT\Auth::id() ? \WT\Auth::user()->getUserName() : '');

// Set the active GEDCOM
if (isset($_REQUEST['ged'])) {
	// .... from the URL or form action
	$GEDCOM=$_REQUEST['ged'];
} elseif ($WT_SESSION->GEDCOM) {
	// .... the most recently used one
	$GEDCOM=$WT_SESSION->GEDCOM;
} else {
	// Try the site default
	$GEDCOM=WT_Site::preference('DEFAULT_GEDCOM');
}

// Choose the selected tree (if it exists), or any valid tree otherwise
$WT_TREE=null;
foreach (WT_Tree::getAll() as $tree) {
	$WT_TREE=$tree;
	if ($WT_TREE->tree_name == $GEDCOM && ($WT_TREE->imported || \WT\Auth::isAdmin())) {
		break;
	}
}

// These attributes of the currently-selected tree are used frequently
if ($WT_TREE) {
	define('WT_GEDCOM',            $WT_TREE->tree_name);
	define('WT_GED_ID',            $WT_TREE->tree_id);
	define('WT_GEDURL',            $WT_TREE->tree_name_url);
	define('WT_TREE_TITLE',        $WT_TREE->tree_title_html);
	define('WT_IMPORTED',          $WT_TREE->imported);
	define('WT_USER_GEDCOM_ADMIN', \WT\Auth::isManager($WT_TREE));
	define('WT_USER_CAN_ACCEPT',   \WT\Auth::isModerator($WT_TREE));
	define('WT_USER_CAN_EDIT',     \WT\Auth::isEditor($WT_TREE));
	define('WT_USER_CAN_ACCESS',   \WT\Auth::isMember($WT_TREE));
	define('WT_USER_GEDCOM_ID',    $WT_TREE->userPreference(WT_USER_ID, 'gedcomid'));
	define('WT_USER_ROOT_ID',      $WT_TREE->userPreference(WT_USER_ID, 'rootid') ? $WT_TREE->userPreference(WT_USER_ID, 'rootid') : WT_USER_GEDCOM_ID);
	define('WT_USER_PATH_LENGTH',  $WT_TREE->userPreference(WT_USER_ID, 'RELATIONSHIP_PATH_LENGTH'));
	if (WT_USER_GEDCOM_ADMIN) {
		define('WT_USER_ACCESS_LEVEL', WT_PRIV_NONE);
	} elseif (WT_USER_CAN_ACCESS) {
		define('WT_USER_ACCESS_LEVEL', WT_PRIV_USER);
	} else {
		define('WT_USER_ACCESS_LEVEL', WT_PRIV_PUBLIC);
	}
	load_gedcom_settings(WT_GED_ID);
} else {
	define('WT_GEDCOM',            '');
	define('WT_GED_ID',            null);
	define('WT_GEDURL',            '');
	define('WT_TREE_TITLE',        WT_WEBTREES);
	define('WT_IMPORTED',          false);
	define('WT_USER_GEDCOM_ADMIN', false);
	define('WT_USER_CAN_ACCEPT',   false);
	define('WT_USER_CAN_EDIT',     false);
	define('WT_USER_CAN_ACCESS',   false);
	define('WT_USER_GEDCOM_ID',    '');
	define('WT_USER_ROOT_ID',      '');
	define('WT_USER_PATH_LENGTH',  0);
	define('WT_USER_ACCESS_LEVEL', WT_PRIV_PUBLIC);
}
$GEDCOM=WT_GEDCOM;

// With no parameters, init() looks to the environment to choose a language
define('WT_LOCALE', WT_I18N::init());
$WT_SESSION->locale=WT_I18N::$locale;

// Set our gedcom selection as a default for the next page
$WT_SESSION->GEDCOM=WT_GEDCOM;

if (empty($WEBTREES_EMAIL)) {
	$WEBTREES_EMAIL='webtrees-noreply@'.preg_replace('/^www\./i', '', $_SERVER['SERVER_NAME']);
}

// Note that the database/webservers may not be synchronised, so use DB time throughout.
define('WT_TIMESTAMP', (int)WT_DB::prepare("SELECT UNIX_TIMESTAMP()")->fetchOne());

// Server timezone is defined in php.ini
define('WT_SERVER_TIMESTAMP', WT_TIMESTAMP + (int)date('Z'));

if (WT_USER_ID) {
	define('WT_CLIENT_TIMESTAMP', WT_TIMESTAMP - $WT_SESSION->timediff);
} else {
	define('WT_CLIENT_TIMESTAMP', WT_SERVER_TIMESTAMP);
}
define('WT_CLIENT_JD', 2440588 + (int)(WT_CLIENT_TIMESTAMP/86400));

// Application configuration data - things that aren’t (yet?) user-editable
require WT_ROOT . 'includes/config_data.php';

// The login URL must be an absolute URL, and can be user-defined
if (WT_Site::preference('LOGIN_URL')) {
	define('WT_LOGIN_URL', WT_Site::preference('LOGIN_URL'));
} else {
	define('WT_LOGIN_URL', WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php');
}

// If there is no current tree and we need one, then redirect somewhere
if (WT_SCRIPT_NAME!='admin_trees_manage.php' && WT_SCRIPT_NAME!='admin_pgv_to_wt.php' && WT_SCRIPT_NAME!='login.php' && WT_SCRIPT_NAME!='logout.php' && WT_SCRIPT_NAME!='import.php' && WT_SCRIPT_NAME!='help_text.php' && WT_SCRIPT_NAME!='message.php') {
	if (!$WT_TREE || !WT_IMPORTED) {
		if (\WT\Auth::isAdmin()) {
			header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'admin_trees_manage.php');
		} else {
			header('Location: '.WT_LOGIN_URL.'?url='.rawurlencode(WT_SCRIPT_NAME.'?'.$QUERY_STRING));
		}
		exit;
	}
}

if (\WT\Auth::id()) {
	//-- update the login time every 5 minutes
	if (WT_TIMESTAMP - $WT_SESSION->activity_time > 300) {
		\WT\Auth::user()->setSetting('sessiontime', WT_TIMESTAMP);
		$WT_SESSION->activity_time = WT_TIMESTAMP;
	}
}

// Set the theme
if (substr(WT_SCRIPT_NAME, 0, 5)=='admin' || WT_SCRIPT_NAME=='module.php' && substr(WT_Filter::get('mod_action'), 0, 5)=='admin') {
	// Administration scripts begin with “admin” and use a special administration theme
	define('WT_THEME_DIR', WT_THEMES_DIR.'_administration/');
} else {
	if (WT_Site::preference('ALLOW_USER_THEMES')) {
		// Requested change of theme?
		$THEME_DIR = WT_Filter::get('theme');
		unset($_GET['theme']);
		if (!in_array($THEME_DIR, get_theme_names())) {
			$THEME_DIR = '';
		}
		// Last theme used?
		if (!$THEME_DIR && in_array($WT_SESSION->theme_dir, get_theme_names())) {
			$THEME_DIR=$WT_SESSION->theme_dir;
		}
	} else {
		$THEME_DIR='';
	}
	if (!$THEME_DIR) {
		// User cannot choose (or has not chosen) a theme.
		// 1) gedcom setting
		// 2) site setting
		// 3) webtrees
		// 4) first one found
		if (WT_GED_ID) {
			$THEME_DIR=get_gedcom_setting(WT_GED_ID, 'THEME_DIR');
		}
		if (!in_array($THEME_DIR, get_theme_names())) {
			$THEME_DIR=WT_Site::preference('THEME_DIR');
		}
		if (!in_array($THEME_DIR, get_theme_names())) {
			$THEME_DIR='webtrees';
		}
		if (!in_array($THEME_DIR, get_theme_names())) {
			list($THEME_DIR)=get_theme_names();
		}
	}
	define('WT_THEME_DIR', WT_THEMES_DIR.$THEME_DIR.'/');
	// Remember this setting
	if (WT_THEME_DIR!=WT_THEMES_DIR.'_administration/') {
		$WT_SESSION->theme_dir=$THEME_DIR;
	}
}
// If we have specified a CDN, use it for static theme resources
define('WT_THEME_URL', WT_STATIC_URL.WT_THEME_DIR);

require WT_ROOT.WT_THEME_DIR.'theme.php';

// Page hit counter - load after theme, as we need theme formatting
if ($WT_TREE && $WT_TREE->preference('SHOW_COUNTER') && !$SEARCH_SPIDER) {
	require WT_ROOT.'includes/hitcount.php';
} else {
	$hitCount='';
}

// define constants to be used when setting permissions after creating files/directories
if (substr(PHP_SAPI, 0, 3) == 'cgi') {  // cgi-mode, should only be writable by owner
	define('WT_PERM_EXE',  0755);  // to be used on directories, php files, etc.
	define('WT_PERM_FILE', 0644);  // to be used on images, text files, etc.
} else { // mod_php mode, should be writable by everyone
	define('WT_PERM_EXE',  0777);
	define('WT_PERM_FILE', 0666);
}

// Lightbox needs custom integration in many places.  Only check for the module once.
define('WT_USE_LIGHTBOX', !$SEARCH_SPIDER && array_key_exists('lightbox', WT_Module::getActiveModules()));

// Search engines are only allowed to see certain pages.
if ($SEARCH_SPIDER && !in_array(WT_SCRIPT_NAME , array(
	'index.php', 'indilist.php', 'module.php', 'mediafirewall.php',
	'individual.php', 'family.php', 'mediaviewer.php', 'note.php', 'repo.php', 'source.php',
))) {
	header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
	$controller=new WT_Controller_Page();
	$controller->setPageTitle(WT_I18N::translate('Search engine'));
	$controller->pageHeader();
	echo '<p class="ui-state-error">', WT_I18N::translate('You do not have permission to view this page.'), '</p>';
	exit;
}
