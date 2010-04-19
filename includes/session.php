<?php
/**
 * Startup and session logic
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @version $Id$
 */

// WT_SCRIPT_NAME is defined in each script that the user is permitted to load.
if (!defined('WT_SCRIPT_NAME')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Identify ourself
define('WT_WEBTREES',        'webtrees');
define('WT_VERSION',         '1.0.0');
define('WT_VERSION_RELEASE', 'svn'); // 'svn', 'beta', 'rc1', '', etc.
define('WT_VERSION_TEXT',    trim(WT_VERSION.' '.WT_VERSION_RELEASE));
define('WT_WEBTREES_URL',    'http://webtrees.net');
define('WT_WEBTREES_WIKI',   'http://wiki.webtrees.net');
define('WT_TRANSLATORS_URL', 'https://launchpad.net/webtrees');

// Enable debugging output?
define('WT_DEBUG',      false);
define('WT_DEBUG_SQL',  false);
define('WT_DEBUG_PRIV', false);

// Error reporting
define('WT_ERROR_LEVEL', 2); // 0=none, 1=minimal, 2=full

// Required version of database tables/columns/indexes/etc.
define('WT_SCHEMA_VERSION', 1);

// Regular expressions for validating user input, etc.
define('WT_REGEX_XREF',     '[A-Za-z0-9:_-]+');
define('WT_REGEX_TAG',      '[_A-Z][_A-Z0-9]*');
define('WT_REGEX_INTEGER',  '-?\d+');
define('WT_REGEX_ALPHA',    '[a-zA-Z]+');
define('WT_REGEX_ALPHANUM', '[a-zA-Z0-9]+');
define('WT_REGEX_BYTES',    '[0-9]+[bBkKmMgG]?');
define('WT_REGEX_USERNAME', '[^<>"%{};]+');
define('WT_REGEX_PASSWORD', '.{6,}');
define('WT_REGEX_NOSCRIPT', '[^<>"&%{};]+');
define('WT_REGEX_URL',      '[\/0-9A-Za-z_!~*\'().;?:@&=+$,%#-]+'); // Simple list of valid chars
define('WT_REGEX_EMAIL',    '[^\s<>"&%{};@]+@[^\s<>"&%{};@]+');
define('WT_REGEX_UNSAFE',   '[\x00-\xFF]*'); // Use with care and apply additional validation!

// UTF8 representation of various characters
define('WT_UTF8_BOM',    "\xEF\xBB\xBF"); // U+FEFF
define('WT_UTF8_MALE',   "\xE2\x99\x82"); // U+2642
define('WT_UTF8_FEMALE', "\xE2\x99\x80"); // U+2640

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
define('WT_EVENTS_MARR', 'MARR|MARB');
define('WT_EVENTS_DIV',  'DIV|ANUL|_SEPR');

// Use these line endings when writing files on the server
define('WT_EOL', "\r\n");

// Gedcom specification/definitions
define ('WT_GEDCOM_LINE_LENGTH', 255-strlen(WT_EOL)); // Characters, not bytes

// Use these tags to wrap embedded javascript consistently
define('WT_JS_START', "\n<script type=\"text/javascript\">\n//<![CDATA[\n");
define('WT_JS_END',   "\n//]]>\n</script>\n");

// Used in Google charts
define ('WT_GOOGLE_CHART_ENCODING', 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.');

// Maximum number of results in auto-complete fields
define('WT_AUTOCOMPLETE_LIMIT', 500);

// Privacy constants
define('WT_PRIV_PUBLIC',  2); // Allows non-authenticated public visitors to view the marked information
define('WT_PRIV_USER',    1); // Allows authenticated users to access the marked information
define('WT_PRIV_NONE',    0); // Allows admin users to access the marked information
define('WT_PRIV_HIDE',   -1); // Hide the item to all users including the admin

// For performance, it is quicker to refer to files using absolute paths
define ('WT_ROOT', realpath(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR);

//-- setup execution timer
$start_time=microtime(true);

ini_set('arg_separator.output', '&amp;');
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', '1');
error_reporting(E_ALL | E_STRICT);

// Invoke the Zend Framework Autoloader, so we can use Zend_XXXXX classes
set_include_path(WT_ROOT.'library'.PATH_SEPARATOR.get_include_path());
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

// Check configuration issues that affect older versions of PHP
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
	// magic quotes were deprecated in PHP5.3.0 and removed in PHP6.0.0
	set_magic_quotes_runtime(0);
	// magic_quotes_gpc can't be disabled at run-time, so clean them up as necessary.
	if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() ||
		ini_get('magic_quotes_sybase') && strtolower(ini_get('magic_quotes_sybase'))!='off') {
		$in = array(&$_GET, &$_POST, &$_REQUEST, &$_COOKIE);
		while (list($k,$v) = each($in)) {
			foreach ($v as $key => $val) {
				if (!is_array($val)) {
					$in[$k][$key] = stripslashes($val);
					continue;
				}
				$in[] =& $in[$k][$key];
			}
		}
		unset($in);
	}
}

// Split the request "protocol://host:port/path/to/script.php?var=value" into parts
// WT_SERVER_NAME  = protocol://host:port
// WT_SCRIPT_PATH  = /path/to/   (begins and ends with /)
// WT_SCRIPT_NAME  = script.php  (already defined in the calling script)
// WT_QUERY_STRING = ?var=value  (generate as needed from $_GET.  lang=xx and theme=yy are removed as used.)

define('WT_SERVER_NAME',
	(empty($_SERVER['HTTPS']) || !in_array($_SERVER['HTTPS'], array('1', 'on', 'On', 'ON')) ?  'http://' : 'https://').
	(empty($_SERVER['SERVER_NAME']) ? '' : $_SERVER['SERVER_NAME']).
	(empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT']==80 ? '' : ':'.$_SERVER['SERVER_PORT'])
);

// SCRIPT_NAME should always be correct, but is not always present.
// PHP_SELF should always be present, but may have trailing path: /path/to/script.php/FOO/BAR
if (!empty($_SERVER['SCRIPT_NAME'])) {
	// PHP 5.3 only
	//define('WT_SCRIPT_PATH', stristr($_SERVER['SCRIPT_NAME'], WT_SCRIPT_NAME, true));
	define('WT_SCRIPT_PATH', substr($_SERVER['SCRIPT_NAME'], 0, stripos($_SERVER['SCRIPT_NAME'], WT_SCRIPT_NAME)));
} elseif (!empty($_SERVER['PHP_SELF'])) {
	// PHP 5.3 only
	//define('WT_SCRIPT_PATH', stristr($_SERVER['PHP_SELF'], WT_SCRIPT_NAME, true));
	define('WT_SCRIPT_PATH', substr($_SERVER['PHP_SELF'], 0, stripos($_SERVER['PHP_SELF'], WT_SCRIPT_NAME)));
} else {
	// No server settings - probably running as a command line script
	define('WT_SCRIPT_PATH', '/');
}

// If we have a preferred URL (e.g. https instead of http, or www.example.com instead of
// www.isp.com/~example), then redirect to it.
if (!empty($SERVER_URL) && $SERVER_URL != WT_SERVER_NAME.WT_SCRIPT_PATH) {
	header('Location: '.$SERVER_URL);
	exit;
}

// Microsoft IIS servers don't set REQUEST_URI, so generate it for them.
if (!isset($_SERVER['REQUEST_URI']))  {
	$_SERVER['REQUEST_URI']=substr($_SERVER['PHP_SELF'], 1);
	if (isset($_SERVER['QUERY_STRING'])) {
		$_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING'];
	}
}

/**
 * Cleanup some variables
 */

if (empty($_SERVER['QUERY_STRING'])) {
	$QUERY_STRING='';
} else {
	$QUERY_STRING=str_replace(
		array('&','<', 'show_context_help=no', 'show_context_help=yes'),
		array('&amp;','&lt;', '', ''),
		$_SERVER['QUERY_STRING']
	);
}

// Common functions
require WT_ROOT.'includes/functions/functions.php';
require WT_ROOT.'includes/functions/functions_name.php';
require WT_ROOT.'includes/functions/functions_db.php';
require WT_ROOT.'includes/classes/class_wt_db.php';

set_error_handler('pgv_error_handler');

// Connect to the database
try {
	// Load our configuration file, so we can connect to the database
	if (file_exists(WT_ROOT.'data/config.ini.php')) {
		$dbconfig=parse_ini_file(WT_ROOT.'data/config.ini.php');
		// Invalid/unreadable config file?
		if (!is_array($dbconfig)) {
			header('Location: site-unavailable.php');
			exit;
		}
	} else {
		// No config file. Set one up.
		header('Location: setup.php');
		exit;
	}
	WT_DB::createInstance($dbconfig['dbhost'], $dbconfig['dbport'], $dbconfig['dbname'], $dbconfig['dbuser'], $dbconfig['dbpass']);
	$TBLPREFIX=$dbconfig['tblpfx'];
	unset($dbconfig);
	try {
		WT_DB::updateSchema(WT_ROOT.'includes/db_schema/', 'WT_SCHEMA_VERSION', WT_SCHEMA_VERSION);
	} catch (PDOException $ex) {
		// The schema update scripts should never fail.  If they do, there is no clean recovery.
		die($ex);
	}
} catch (PDOException $ex) {
	header('Location: site-unavailable.php');
	exit;
}

// We'll tidy these up later.  Some of them are used infrequently.
$INDEX_DIRECTORY                =get_site_setting('INDEX_DIRECTORY');
$WT_STORE_MESSAGES              =get_site_setting('STORE_MESSAGES');
$USE_REGISTRATION_MODULE        =get_site_setting('USE_REGISTRATION_MODULE');
$REQUIRE_ADMIN_AUTH_REGISTRATION=get_site_setting('REQUIRE_ADMIN_AUTH_REGISTRATION');
$ALLOW_USER_THEMES              =get_site_setting('ALLOW_USER_THEMES');
$ALLOW_CHANGE_GEDCOM            =get_site_setting('ALLOW_CHANGE_GEDCOM');
$LOGFILE_CREATE                 =get_site_setting('LOGFILE_CREATE');
$LOG_LANG_ERROR                 =get_site_setting('LOG_LANG_ERROR');
$WT_SESSION_SAVE_PATH           =get_site_setting('SESSION_SAVE_PATH');
$WT_SESSION_TIME                =get_site_setting('SESSION_TIME');
$SERVER_URL                     =get_site_setting('SERVER_URL');
$LOGIN_URL                      =get_site_setting('LOGIN_URL');
$MAX_VIEWS                      =get_site_setting('MAX_VIEWS');
$MAX_VIEW_TIME                  =get_site_setting('MAX_VIEW_TIME');

//-- allow user to cancel
ignore_user_abort(false);

if (!ini_get('safe_mode')) {
	ini_set('memory_limit', get_site_setting('MEMORY_LIMIT'));
	set_time_limit(get_site_setting('MAX_EXECUTION_TIME'));
}

// default: include/authentication.php
// Maybe Joomla/Drupal/etc. systems will provide their own?
require get_site_setting('AUTHENTICATION_MODULE');

// Determine browser type
$BROWSERTYPE = 'other';
if (!empty($_SERVER['HTTP_USER_AGENT'])) {
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Opera')) {
		$BROWSERTYPE = 'opera';
	} elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Netscape')) {
		$BROWSERTYPE = 'netscape';
	} elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Gecko')) {
		$BROWSERTYPE = 'mozilla';
	} elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
		$BROWSERTYPE = 'msie';
	}
}

//-- load up the code to check for spiders
require WT_ROOT.'includes/session_spider.php';

// Start the php session
session_set_cookie_params(date('D M j H:i:s T Y', time()+$WT_SESSION_TIME), WT_SCRIPT_PATH);

if ($WT_SESSION_TIME>0) {
	session_cache_expire($WT_SESSION_TIME/60);
}
if (!empty($WT_SESSION_SAVE_PATH)) {
	session_save_path($WT_SESSION_SAVE_PATH);
}
if (isset($MANUAL_SESSION_START) && !empty($SID)) {
	session_id($SID);
}

session_start();

if (!$SEARCH_SPIDER && !isset($_SESSION['initiated'])) {
	// A new session, so prevent session fixation attacks by choosing a new PHPSESSID.
	session_regenerate_id(true);
	$_SESSION['initiated']=true;
} else {
	// An existing session
}

// Set the active GEDCOM
if (isset($_REQUEST['ged'])) {
	// .... from the URL or form action
	$GEDCOM=$_REQUEST['ged'];
} elseif (isset($_REQUEST['GEDCOM'])) {
	// .... is this used ????
	$GEDCOM=$_REQUEST['GEDCOM'];
} elseif (isset($_SESSION['GEDCOM'])) {
	// .... the most recently used one
	$GEDCOM=$_SESSION['GEDCOM'];
} else {
	// .... we'll need to query the DB to find one
	$GEDCOM='';
}

require WT_ROOT.'config_gedcom.php'; // Load default gedcom settings

// Missing/invalid gedcom - pick any one!
try {
	// Does the requested GEDCOM exist?
	$ged_id=get_id_from_gedcom($GEDCOM);
	if (!$ged_id) {
		// Try the site default
		$GEDCOM=get_site_setting('DEFAULT_GEDCOM');
		$ged_id=get_id_from_gedcom($GEDCOM);
		// Try any one
		if (!$ged_id) {
			foreach (get_all_gedcoms() as $ged_id=>$GEDCOM) {
				if (get_gedcom_setting($ged_id, 'imported')) {
					break;
				}
			}
		}
	}
	define('WT_GEDCOM', $GEDCOM);
	define('WT_GED_ID', $ged_id);
	load_privacy_file(WT_GED_ID);
	require get_config_file(WT_GED_ID); // Load current gedcom settings
} catch (PDOException $ex) {
	// No DB available?
	require 'privacy.php';
	define('WT_GEDCOM', '');
	define('WT_GED_ID', 0);
}

// Set our gedcom selection as a default for the next page
$_SESSION['GEDCOM']=WT_GEDCOM;

if (empty($WEBTREES_EMAIL)) {
	$WEBTREES_EMAIL='phpgedview-noreply@'.preg_replace('/^www\./i', '', $_SERVER['SERVER_NAME']);
}

require WT_ROOT.'includes/functions/functions_print.php';
require WT_ROOT.'includes/functions/functions_rtl.php';

if ($MULTI_MEDIA) {
	require WT_ROOT.'includes/functions/functions_mediadb.php';
}
require WT_ROOT.'includes/functions/functions_date.php';

if (empty($PEDIGREE_GENERATIONS)) {
	$PEDIGREE_GENERATIONS=$DEFAULT_PEDIGREE_GENERATIONS;
}

// With no parameters, init() looks to the environment to choose a language
require WT_ROOT.'includes/classes/class_i18n.php';
define('WT_LOCALE', i18n::init());

// Application configuration data - things that aren't (yet?) user-editable
require WT_ROOT.'includes/config_data.php';

// Tell the database to sort/compare using the language's preferred collatation settings
try {
	// I18N: This is the name of the MySQL collation that applies to your language.  A list is available at http://dev.mysql.com/doc/refman/5.0/en/charset-unicode-sets.html
	WT_DB::exec("SET NAMES utf8 COLLATE '".i18n::$collation."'");
} catch (PDOException $ex) {
	// Always set a unicode collation
	WT_DB::exec("SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'");
}

//-- load the privacy functions
require WT_ROOT.'includes/functions/functions_privacy.php';

// The current user's profile - from functions in authentication.php
define('WT_USER_ID', getUserId());
if (WT_DB::isConnected()) {
	define('WT_USER_NAME',         getUserName   ());
	define('WT_USER_IS_ADMIN',     userIsAdmin   (WT_USER_ID));
	define('WT_USER_AUTO_ACCEPT',  userAutoAccept(WT_USER_ID));
	define('WT_ADMIN_USER_EXISTS', WT_USER_IS_ADMIN     || adminUserExists());
	define('WT_USER_GEDCOM_ADMIN', WT_USER_IS_ADMIN     || userGedcomAdmin(WT_USER_ID, WT_GED_ID));
	define('WT_USER_CAN_ACCEPT',   WT_USER_GEDCOM_ADMIN || userCanAccept  (WT_USER_ID, WT_GED_ID));
	define('WT_USER_CAN_EDIT',     WT_USER_CAN_ACCEPT   || userCanEdit    (WT_USER_ID, WT_GED_ID));
	define('WT_USER_CAN_ACCESS',   WT_USER_CAN_EDIT     || userCanAccess  (WT_USER_ID, WT_GED_ID));
	define('WT_USER_ACCESS_LEVEL', getUserAccessLevel(WT_USER_ID, WT_GED_ID));
	define('WT_USER_GEDCOM_ID',    getUserGedcomId   (WT_USER_ID, WT_GED_ID));
	define('WT_USER_ROOT_ID',      getUserRootId     (WT_USER_ID, WT_GED_ID));
} else {
	// No DB?  Just set the basics, for install.php
	define('WT_ADMIN_USER_EXISTS', false);
}

// If we are logged in, and logout=1 has been added to the URL, log out
if (WT_USER_ID && safe_GET_bool('logout')) {
	userLogout(WT_USER_ID);
	header("Location: ".WT_SERVER_NAME.WT_SCRIPT_PATH);
	exit;
}

// Check for page views exceeding the limit
CheckPageViews();

$show_context_help = '';
if (!empty($_REQUEST['show_context_help'])) $show_context_help = $_REQUEST['show_context_help'];
if (!isset($_SESSION['show_context_help'])) $_SESSION['show_context_help'] = $SHOW_CONTEXT_HELP;
if (!isset($_SESSION['pgv_user'])) $_SESSION['pgv_user'] = '';
if (isset($SHOW_CONTEXT_HELP) && $show_context_help==='yes') $_SESSION['show_context_help'] = true;
if (isset($SHOW_CONTEXT_HELP) && $show_context_help==='no') $_SESSION['show_context_help'] = false;
if (!isset($USE_THUMBS_MAIN)) $USE_THUMBS_MAIN = false;
if (WT_SCRIPT_NAME!='install.php' && WT_SCRIPT_NAME!='help_text.php') {
	if (!WT_DB::isConnected() || !WT_ADMIN_USER_EXISTS) {
		header('Location: install.php');
		exit;
	}

	if (!get_gedcom_setting(WT_GED_ID, 'imported') && !in_array(WT_SCRIPT_NAME, array('editconfig_gedcom.php', 'help_text.php', 'editgedcoms.php', 'downloadgedcom.php', 'uploadgedcom.php', 'login.php', 'siteconfig.php', 'admin.php', 'config_download.php', 'addnewgedcom.php', 'validategedcom.php', 'addmedia.php', 'importgedcom.php', 'client.php', 'edit_privacy.php', 'gedcheck.php', 'printlog.php', 'useradmin.php', 'export_gedcom.php', 'edit_changes.php'))) {
		header('Location: editgedcoms.php');
		exit;
	}

	if ($REQUIRE_AUTHENTICATION && !WT_USER_ID && !in_array(WT_SCRIPT_NAME, array('login.php', 'login_register.php', 'client.php', 'genservice.php', 'help_text.php', 'message.php'))) {
		if (!empty($_REQUEST['auth']) && $_REQUEST['auth']=='basic') {
			// if user is attempting basic authentication
			// TODO: Update if digest auth is ever implemented
			basicHTTPAuthenticateUser();
		} else {
			if (WT_SCRIPT_NAME=='index.php') {
				$url='index.php?ctype=gedcom&ged='.WT_GEDCOM;
			} else {
				$url=WT_SCRIPT_NAME.'?'.$QUERY_STRING;
			}
			if ($LOGIN_URL) {
				// Specify an absolute URL, as $LOGIN_URL could be anywhere
				header('Location: '.$LOGIN_URL.'?url='.urlencode(WT_SERVER_NAME.WT_SCRIPT_PATH.$url));
			} else {
				header('Location: login.php?url='.urlencode($url));
			}
			exit;
		}
	}

	// -- setup session information for tree clippings cart features
	if ((!isset($_SESSION['cart'])) || (!empty($_SESSION['last_spider_name']))) { // reset cart everytime for spiders
		$_SESSION['cart'] = array();
	}
	$cart = $_SESSION['cart'];

	if (!isset($_SESSION['timediff'])) {
		$_SESSION['timediff'] = 0;
	}

	if (empty($LOGIN_URL)) {
		$LOGIN_URL = 'login.php';
	}
}

//-- load the user specific theme
if (WT_USER_ID) {
	//-- update the login time every 5 minutes
	if (!isset($_SESSION['activity_time']) || (time()-$_SESSION['activity_time'])>300) {
		userUpdateLogin(WT_USER_ID);
		$_SESSION['activity_time'] = time();
	}

	$usertheme = get_user_setting(WT_USER_ID, 'theme');
	if ((!empty($_POST['user_theme']))&&(!empty($_POST['oldusername']))&&($_POST['oldusername']==WT_USER_ID)) $usertheme = $_POST['user_theme'];
	if ((!empty($usertheme)) && (file_exists($usertheme.'theme.php')))  {
		$THEME_DIR = $usertheme;
	}
}

if (isset($_SESSION['theme_dir'])) {
	$THEME_DIR = $_SESSION['theme_dir'];
	if (WT_USER_ID) {
		if (get_user_setting(WT_USER_ID, 'editaccount')=='Y') unset($_SESSION['theme_dir']);
	}
}

if (empty($THEME_DIR) || !file_exists("{$THEME_DIR}theme.php")) {
	$THEME_DIR = 'themes/webtrees/';
}

define('WT_THEME_DIR', $THEME_DIR);

require WT_THEME_DIR.'theme.php';

// Page hit counter - load after theme, as we need theme formatting
if ($SHOW_COUNTER && !$SEARCH_SPIDER) {
	require WT_ROOT.'includes/hitcount.php';
} else {
	$hitCount='';
}

// Characters with weak-directionality can confuse the browser's BIDI algorithm.
// Make sure that they follow the directionality of the page, not that of the
// enclosed text.
if ($TEXT_DIRECTION=='ltr') {
	define ('WT_LPARENS', '&lrm;(');
	define ('WT_RPARENS', ')&lrm;');
} else {
	define ('WT_LPARENS', '&rlm;(');
	define ('WT_RPARENS', ')&rlm;');
}

// define constants to be used when setting permissions after creating files/directories
if (substr(PHP_SAPI, 0, 3) == 'cgi') {  // cgi-mode, should only be writable by owner
	define('WT_PERM_EXE',  0755);  // to be used on directories, php files and htaccess files
	define('WT_PERM_FILE', 0644);  // to be used on images, text files, etc
} else { // mod_php mode, should be writable by everyone
	define('WT_PERM_EXE',  0777);
	define('WT_PERM_FILE', 0666);
}

// Lightbox needs custom integration in many places.  Only check for the module once.
define('WT_USE_LIGHTBOX', !$SEARCH_SPIDER && $MULTI_MEDIA && file_exists(WT_ROOT.'modules/lightbox.php') && is_dir(WT_ROOT.'modules/lightbox'));
