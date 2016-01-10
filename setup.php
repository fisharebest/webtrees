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

use Fisharebest\Webtrees\Functions\FunctionsEdit;
use PDOException;

error_reporting(E_ALL);

define('WT_SCRIPT_NAME', 'setup.php');
define('WT_CONFIG_FILE', 'config.ini.php');

require 'vendor/autoload.php';

// This script (uniquely) does not load session.php.
// session.php won’t run until a configuration file exists…
// This next block of code is a minimal version of session.php
define('WT_WEBTREES', 'webtrees');
define('WT_BASE_URL', '');
define('WT_DATA_DIR', 'data/');
define('WT_DEBUG_SQL', false);
define('WT_REQUIRED_MYSQL_VERSION', '5.0.13');
define('WT_REQUIRED_PHP_VERSION', '5.3.2');
define('WT_MODULES_DIR', 'modules_v3/');
define('WT_ROOT', '');
define('WT_CLIENT_IP', $_SERVER['REMOTE_ADDR']);

// Convert PHP errors into exceptions
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
	if (error_reporting() & $errno) {
		throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
	} else {
		return false;
	}
});

if (file_exists(WT_DATA_DIR . WT_CONFIG_FILE)) {
	header('Location: index.php');

	return;
}

if (version_compare(PHP_VERSION, WT_REQUIRED_PHP_VERSION) < 0) {
	echo
		'<h1>Sorry, the setup wizard cannot start.</h1>',
		'<p>This server is running PHP version ', PHP_VERSION, '</p>',
		'<p>PHP ', WT_REQUIRED_PHP_VERSION, ' (or any later version) is required</p>';

	return;
}

Session::start();

define('WT_LOCALE', I18N::init(Filter::post('lang', '[a-zA-Z-]+', Filter::get('lang', '[a-zA-Z-]+'))));

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html <?php echo I18N::htmlAttributes(); ?>>
<head>
	<meta charset="UTF-8">
	<title>
		webtrees setup wizard
	</title>
	<style type="text/css">
		body {color: black; background-color: white; font: 14px tahoma, arial, helvetica, sans-serif; padding:10px; }
		a {color: black; font-weight: normal; text-decoration: none;}
		a:hover {color: #81A9CB;}
		h1 {color: #81A9CB; font-weight:normal;}
		legend {color:#81A9CB; font-style: italic; font-weight:bold; padding: 0 5px 5px;}
		.good {color: green;}
		.bad {color: red; font-weight: bold;}
		.info {color: blue;}
	</style>
	</head>
	<body>
		<h1>
			<?php echo I18N::translate('Setup wizard for webtrees'); ?>
		</h1>
<?php

echo '<form name="config" action="', WT_SCRIPT_NAME, '" method="post" onsubmit="this.btncontinue.disabled=\'disabled\';">';
echo '<input type="hidden" name="lang" value="', WT_LOCALE, '">';

////////////////////////////////////////////////////////////////////////////////
// Step one - choose language and confirm server configuration
////////////////////////////////////////////////////////////////////////////////

if (!isset($_POST['lang'])) {
	$installed_languages = array();
	foreach (I18N::installedLocales() as $installed_locale) {
		$installed_languages[$installed_locale->languageTag()] = $installed_locale->endonym();
	}

	echo
		'<p>', I18N::translate('Change language'), ' ',
		FunctionsEdit::selectEditControl('change_lang', $installed_languages, null, WT_LOCALE, 'onchange="window.location=\'' . WT_SCRIPT_NAME . '?lang=\'+this.value;">'),
		'</p>',
		'<h2>', I18N::translate('Checking server configuration'), '</h2>';
	$warnings = false;
	$errors   = false;

	// Mandatory functions
	$disable_functions = preg_split('/ *, */', ini_get('disable_functions'));
	foreach (array('parse_ini_file') as $function) {
		if (in_array($function, $disable_functions)) {
			echo '<p class="bad">', /* I18N: %s is a PHP function/module/setting */ I18N::translate('%s is disabled on this server.  You cannot install webtrees until it is enabled.  Please ask your server’s administrator to enable it.', $function . '()'), '</p>';
			$errors = true;
		}
	}
	// Mandatory extensions
	foreach (array('pcre', 'pdo', 'pdo_mysql', 'session', 'iconv') as $extension) {
		if (!extension_loaded($extension)) {
			echo '<p class="bad">', I18N::translate('PHP extension “%s” is disabled.  You cannot install webtrees until this is enabled.  Please ask your server’s administrator to enable it.', $extension), '</p>';
			$errors = true;
		}
	}
	// Recommended extensions
	foreach (array(
		'gd'        => /* I18N: a program feature */ I18N::translate('creating thumbnails of images'),
		'xml'       => /* I18N: a program feature */ I18N::translate('reporting'),
		'simplexml' => /* I18N: a program feature */ I18N::translate('reporting'),
	) as $extension => $features) {
		if (!extension_loaded($extension)) {
			echo '<p class="bad">', I18N::translate('PHP extension “%1$s” is disabled.  Without it, the following features will not work: %2$s.  Please ask your server’s administrator to enable it.', $extension, $features), '</p>';
			$warnings = true;
		}
	}
	// Settings
	foreach (array(
		'file_uploads' => /* I18N: a program feature */ I18N::translate('file upload capability'),
	) as $setting => $features) {
		if (!ini_get($setting)) {
			echo '<p class="bad">', I18N::translate('PHP setting “%1$s” is disabled.  Without it, the following features will not work: %2$s.  Please ask your server’s administrator to enable it.', $setting, $features), '</p>';
			$warnings = true;
		}
	}
	if (!$warnings && !$errors) {
		echo '<p class="good">', I18N::translate('The server configuration is OK.'), '</p>';
	}
	echo '<h2>', I18N::translate('Checking server capacity'), '</h2>';
	// Previously, we tried to determine the maximum value that we could set for these values.
	// However, this is unreliable, especially on servers with custom restrictions.
	// Now, we just show the default values.  These can (hopefully!) be changed using the
	// site settings page.
	$memory_limit = ini_get('memory_limit');
	if (substr_compare($memory_limit, 'M', -1) === 0) {
		$memory_limit = substr($memory_limit, 0, -1);
	} elseif (substr_compare($memory_limit, 'K', -1) === 0) {
		$memory_limit = substr($memory_limit, 0, -1) / 1024;
	} elseif (substr_compare($memory_limit, 'G', -1) === 0) {
		$memory_limit = substr($memory_limit, 0, -1) * 1024;
	}
	$max_execution_time = ini_get('max_execution_time');
	echo
		'<p>',
		I18N::translate('The memory and CPU time requirements depend on the number of individuals in your family tree.'),
		'<br>',
		I18N::translate('The following list shows typical requirements.'),
		'</p><p>',
		I18N::translate('Small systems (500 individuals): 16–32 MB, 10–20 seconds'),
		'<br>',
		I18N::translate('Medium systems (5,000 individuals): 32–64 MB, 20–40 seconds'),
		'<br>',
		I18N::translate('Large systems (50,000 individuals): 64–128 MB, 40–80 seconds'),
		'</p>',
		($memory_limit < 32 || $max_execution_time > 0 && $max_execution_time < 20) ? '<p class="bad">' : '<p class="good">',
		I18N::translate('This server’s memory limit is %s MB and its CPU time limit is %s seconds.', I18N::number($memory_limit), I18N::number($max_execution_time)),
		'</p><p>',
		I18N::translate('If you try to exceed these limits, you may experience server time-outs and blank pages.'),
		'</p><p>',
		I18N::translate('If your server’s security policy permits it, you will be able to request increased memory or CPU time using the webtrees administration page.  Otherwise, you will need to contact your server’s administrator.'),
		'</p>';
	if (!$errors) {
		echo '<br><hr><input type="submit" id="btncontinue" value="', /* I18N: button label */ I18N::translate('continue'), '">';

	}
	echo '</form></body></html>';

	return;
}

////////////////////////////////////////////////////////////////////////////////
// Step two - The data folder needs to be writable
////////////////////////////////////////////////////////////////////////////////

$text1 = uniqid();
$text2 = '';
try {
	file_put_contents(WT_DATA_DIR . 'test.txt', $text1);
	$text2 = file_get_contents(WT_DATA_DIR . 'test.txt');
	unlink(WT_DATA_DIR . 'test.txt');
} catch (\ErrorException $ex) {
}

if ($text1 !== $text2) {
	echo '<h2>', realpath(WT_DATA_DIR), '</h2>';
	echo '<p class="bad">', I18N::translate('Oops!  webtrees was unable to create files in this folder.'), '</p>';
	echo '<p>', I18N::translate('This usually means that you need to change the folder permissions to 777.'), '</p>';
	echo '<p>', I18N::translate('You must change this before you can continue.'), '</p>';
	echo '<br><hr><input type="submit" id="btncontinue" value="', I18N::translate('continue'), '">';
	echo '</form></body></html>';

	return;
}

////////////////////////////////////////////////////////////////////////////////
// Step three - Database connection.
////////////////////////////////////////////////////////////////////////////////

if (!isset($_POST['dbhost'])) {
	$_POST['dbhost'] = 'localhost';
}
if (!isset($_POST['dbport'])) {
	$_POST['dbport'] = '3306';
}
if (!isset($_POST['dbuser'])) {
	$_POST['dbuser'] = '';
}
if (!isset($_POST['dbpass'])) {
	$_POST['dbpass'] = '';
}
if (!isset($_POST['dbname'])) {
	$_POST['dbname'] = '';
}
if (!isset($_POST['tblpfx'])) {
	$_POST['tblpfx'] = 'wt_';
}

define('WT_TBLPREFIX', $_POST['tblpfx']);
$db_version_ok = false;
try {
	Database::createInstance(
		$_POST['dbhost'],
		$_POST['dbport'],
		'', // No DBNAME - we will connect to it explicitly
		$_POST['dbuser'],
		$_POST['dbpass']
	);
	Database::exec("SET NAMES 'utf8'");
	$row = Database::prepare("SHOW VARIABLES LIKE 'VERSION'")->fetchOneRow();
	if (version_compare($row->value, WT_REQUIRED_MYSQL_VERSION, '<')) {
		echo '<p class="bad">', I18N::translate('This database is only running MySQL version %s.  You cannot install webtrees here.', $row->value), '</p>';
	} else {
		$db_version_ok = true;
	}
} catch (PDOException $ex) {
	Database::disconnect();
	if ($_POST['dbuser']) {
		// If we’ve supplied a login, then show the error
		echo
			'<p class="bad">', I18N::translate('Unable to connect using these settings.  Your server gave the following error.'), '</p>',
			'<pre>', $ex->getMessage(), '</pre>',
			'<p class="bad">', I18N::translate('Check the settings and try again.'), '</p>';
	}
}

if (empty($_POST['dbuser']) || !Database::isConnected() || !$db_version_ok) {
	echo
		'<h2>', I18N::translate('Connection to database server'), '</h2>',
		'<p>', I18N::translate('webtrees needs a MySQL database, version %s or later.', WT_REQUIRED_MYSQL_VERSION), '</p>',
		'<p>', I18N::translate('Your server’s administrator will provide you with the connection details.'), '</p>',
		'<fieldset><legend>', I18N::translate('Database connection'), '</legend>',
		'<table border="0"><tr><td>',
		I18N::translate('Server name'), '</td><td>',
		'<input type="text" name="dbhost" value="', Filter::escapeHtml($_POST['dbhost']), '" dir="ltr"></td><td>',
		I18N::translate('Most sites are configured to use localhost.  This means that your database runs on the same computer as your web server.'),
		'</td></tr><tr><td>',
		I18N::translate('Port number'), '</td><td>',
		'<input type="text" name="dbport" value="', Filter::escapeHtml($_POST['dbport']), '"></td><td>',
		I18N::translate('Most sites are configured to use the default value of 3306.'),
		'</td></tr><tr><td>',
		I18N::translate('Database user account'), '</td><td>',
		'<input type="text" name="dbuser" value="', Filter::escapeHtml($_POST['dbuser']), '" autofocus></td><td>',
		I18N::translate('This is case sensitive.'),
		'</td></tr><tr><td>',
		I18N::translate('Database password'), '</td><td>',
		'<input type="password" name="dbpass" value="', Filter::escapeHtml($_POST['dbpass']), '"></td><td>',
		I18N::translate('This is case sensitive.'),
		'</td></tr><tr><td>',
		'</td></tr></table>',
		'</fieldset>',
		'<br><hr><input type="submit" id="btncontinue" value="', I18N::translate('continue'), '">',
		'</form>',
		'</body></html>';

	return;
} else {
	// Copy these values through to the next step
	echo '<input type="hidden" name="dbhost" value="', Filter::escapeHtml($_POST['dbhost']), '">';
	echo '<input type="hidden" name="dbport" value="', Filter::escapeHtml($_POST['dbport']), '">';
	echo '<input type="hidden" name="dbuser" value="', Filter::escapeHtml($_POST['dbuser']), '">';
	echo '<input type="hidden" name="dbpass" value="', Filter::escapeHtml($_POST['dbpass']), '">';
}

////////////////////////////////////////////////////////////////////////////////
// Step four - Database connection.
////////////////////////////////////////////////////////////////////////////////

// The character ` is not valid in database or table names (even if escaped).
// By removing it, we can ensure that our SQL statements are quoted correctly.
//
// Other characters may be invalid (objects must be valid filenames on the
// MySQL server’s filesystem), so block the usual ones.
$DBNAME    = str_replace(array('`', '"', '\'', ':', '/', '\\', '\r', '\n', '\t', '\0'), '', $_POST['dbname']);
$TBLPREFIX = str_replace(array('`', '"', '\'', ':', '/', '\\', '\r', '\n', '\t', '\0'), '', $_POST['tblpfx']);

// If we have specified a database, and we have not used invalid characters,
// try to connect to it.
$dbname_ok = false;
if ($DBNAME && $DBNAME == $_POST['dbname'] && $TBLPREFIX == $_POST['tblpfx']) {
	try {
		// Try to create the database, if it does not exist.
		Database::exec("CREATE DATABASE IF NOT EXISTS `{$DBNAME}` COLLATE utf8_unicode_ci");
	} catch (PDOException $ex) {
		// If we have no permission to do this, there’s nothing helpful we can say.
		// We’ll get a more helpful error message from the next test.
	}
	try {
		Database::exec("USE `{$DBNAME}`");
		$dbname_ok = true;
	} catch (PDOException $ex) {
		echo
			'<p class="bad">', I18N::translate('Unable to connect using these settings.  Your server gave the following error.'), '</p>',
			'<pre>', $ex->getMessage(), '</pre>',
			'<p class="bad">', I18N::translate('Check the settings and try again.'), '</p>';
	}
}

// If the database exists, check whether it is already used by another application.
if ($dbname_ok) {
	try {
		// PhpGedView (4.2.3 and earlier) and many other applications have a USERS table.
		// webtrees has a USER table
		Database::prepare("SELECT COUNT(*) FROM `##users`")->fetchOne();
		echo '<p class="bad">', I18N::translate('This database and table-prefix appear to be used by another application.  If you have an existing PhpGedView system, you should create a new webtrees system.  You can import your PhpGedView data and settings later.'), '</p>';
		$dbname_ok = false;
	} catch (PDOException $ex) {
		// Table not found?  Good!
	}
}
if ($dbname_ok) {
	try {
		// PhpGedView (4.2.4 and later) has a site_setting.site_setting_name column.
		// [We changed the column name in webtrees, so we can tell the difference!]
		Database::prepare("SELECT site_setting_value FROM `##site_setting` WHERE site_setting_name='PGV_SCHEMA_VERSION'")->fetchOne();
		echo '<p class="bad">', I18N::translate('This database and table-prefix appear to be used by another application.  If you have an existing PhpGedView system, you should create a new webtrees system.  You can import your PhpGedView data and settings later.'), '</p>';
		$dbname_ok = false;
	} catch (PDOException $ex) {
		// Table/column not found?  Good!
	}
}

if (!$dbname_ok) {
	echo
		'<h2>', I18N::translate('Database and table names'), '</h2>',
		'<p>', I18N::translate('A database server can store many separate databases.  You need to select an existing database (created by your server’s administrator) or create a new one (if your database user account has sufficient privileges).'), '</p>',
		'<fieldset><legend>', I18N::translate('Database name'), '</legend>',
		'<table border="0"><tr><td>',
		I18N::translate('Database name'), '</td><td>',
		'<input type="text" name="dbname" value="', Filter::escapeHtml($_POST['dbname']), '" autofocus></td><td>',
		I18N::translate('This is case sensitive.  If a database with this name does not already exist webtrees will attempt to create one for you.  Success will depend on permissions set for your web server, but you will be notified if this fails.'),
		'</td></tr><tr><td>',
		I18N::translate('Table prefix'), '</td><td>',
		'<input type="text" name="tblpfx" value="', Filter::escapeHtml($_POST['tblpfx']), '"></td><td>',
		I18N::translate('The prefix is optional, but recommended.  By giving the table names a unique prefix you can let several different applications share the same database.  “wt_” is suggested, but can be anything you want.'),
		'</td></tr></table>',
		'</fieldset>',
		'<br><hr><input type="submit" id="btncontinue" value="', I18N::translate('continue'), '">',
		'</form>',
		'</body></html>';

	return;
} else {
	// Copy these values through to the next step
	echo '<input type="hidden" name="dbname" value="', Filter::escapeHtml($_POST['dbname']), '">';
	echo '<input type="hidden" name="tblpfx" value="', Filter::escapeHtml($_POST['tblpfx']), '">';
}

////////////////////////////////////////////////////////////////////////////////
// Step five - site setup data
////////////////////////////////////////////////////////////////////////////////

if (!isset($_POST['wtname'])) {
	$_POST['wtname'] = '';
}
if (!isset($_POST['wtuser'])) {
	$_POST['wtuser'] = '';
}
if (!isset($_POST['wtpass'])) {
	$_POST['wtpass'] = '';
}
if (!isset($_POST['wtpass2'])) {
	$_POST['wtpass2'] = '';
}
if (!isset($_POST['wtemail'])) {
	$_POST['wtemail'] = '';
}

if (empty($_POST['wtname']) || empty($_POST['wtuser']) || strlen($_POST['wtpass']) < 6 || strlen($_POST['wtpass2']) < 6 || empty($_POST['wtemail']) || $_POST['wtpass'] != $_POST['wtpass2']) {
	if (strlen($_POST['wtpass']) > 0 && strlen($_POST['wtpass']) < 6) {
		echo '<p class="bad">', I18N::translate('The password needs to be at least six characters long.'), '</p>';
	} elseif ($_POST['wtpass'] != $_POST['wtpass2']) {
		echo '<p class="bad">', I18N::translate('The passwords do not match.'), '</p>';
	} elseif ((empty($_POST['wtname']) || empty($_POST['wtuser']) || empty($_POST['wtpass']) || empty($_POST['wtemail'])) && $_POST['wtname'] . $_POST['wtuser'] . $_POST['wtpass'] . $_POST['wtemail'] != '') {
		echo '<p class="bad">', I18N::translate('You must enter all the administrator account fields.'), '</p>';
	}
	echo
		'<h2>', I18N::translate('System settings'), '</h2>',
		'<h3>', I18N::translate('Administrator account'), '</h3>',
		'<p>', I18N::translate('You need to set up an administrator account.  This account can control all aspects of this webtrees installation.  Please choose a strong password.'), '</p>',
		'<fieldset><legend>', I18N::translate('Administrator account'), '</legend>',
		'<table border="0"><tr><td>',
		I18N::translate('Your name'), '</td><td>',
		'<input type="text" name="wtname" value="', Filter::escapeHtml($_POST['wtname']), '" autofocus></td><td>',
		I18N::translate('This is your real name, as you would like it displayed on screen.'),
		'</td></tr><tr><td>',
		I18N::translate('Login ID'), '</td><td>',
		'<input type="text" name="wtuser" value="', Filter::escapeHtml($_POST['wtuser']), '"></td><td>',
		I18N::translate('You will use this to login to webtrees.'),
		'</td></tr><tr><td>',
		I18N::translate('Password'), '</td><td>',
		'<input type="password" name="wtpass" value="', Filter::escapeHtml($_POST['wtpass']), '"></td><td>',
		I18N::translate('This must be at least six characters long.  It is case-sensitive.'),
		'</td></tr><tr><td>',
		'&nbsp;', '</td><td>',
		'<input type="password" name="wtpass2" value="', Filter::escapeHtml($_POST['wtpass2']), '"></td><td>',
		I18N::translate('Type your password again, to make sure you have typed it correctly.'),
		'</td></tr><tr><td>',
		I18N::translate('Email address'), '</td><td>',
		'<input type="email" name="wtemail" value="', Filter::escapeHtml($_POST['wtemail']), '"></td><td>',
		I18N::translate('This email address will be used to send password reminders, website notifications, and messages from other family members who are registered on the website.'),
		'</td></tr><tr><td>',
		'</td></tr></table>',
		'</fieldset>',
		'<br><hr><input type="submit" id="btncontinue" value="', I18N::translate('continue'), '">',
		'</form>',
		'</body></html>';

	return;
} else {
	// Copy these values through to the next step
	echo '<input type="hidden" name="wtname"  value="', Filter::escapeHtml($_POST['wtname']), '">';
	echo '<input type="hidden" name="wtuser"  value="', Filter::escapeHtml($_POST['wtuser']), '">';
	echo '<input type="hidden" name="wtpass"  value="', Filter::escapeHtml($_POST['wtpass']), '">';
	echo '<input type="hidden" name="wtpass2" value="', Filter::escapeHtml($_POST['wtpass2']), '">';
	echo '<input type="hidden" name="wtemail" value="', Filter::escapeHtml($_POST['wtemail']), '">';
}

////////////////////////////////////////////////////////////////////////////////
// Step six  We have a database connection and a writable folder.  Do it!
////////////////////////////////////////////////////////////////////////////////

try {
	// Create/update the database tables.
	Database::updateSchema('\Fisharebest\Webtrees\Schema', 'WT_SCHEMA_VERSION', 30);

	// Create the admin user
	$admin = User::create($_POST['wtuser'], $_POST['wtname'], $_POST['wtemail'], $_POST['wtpass']);
	$admin->setPreference('canadmin', '1');
	$admin->setPreference('language', WT_LOCALE);
	$admin->setPreference('verified', '1');
	$admin->setPreference('verified_by_admin', '1');
	$admin->setPreference('auto_accept', '0');
	$admin->setPreference('visibleonline', '1');

	// Write the config file.  We already checked that this would work.
	$config_ini_php =
		'; <' . '?php exit; ?' . '> DO NOT DELETE THIS LINE' . PHP_EOL .
		'dbhost="' . addcslashes($_POST['dbhost'], '"') . '"' . PHP_EOL .
		'dbport="' . addcslashes($_POST['dbport'], '"') . '"' . PHP_EOL .
		'dbuser="' . addcslashes($_POST['dbuser'], '"') . '"' . PHP_EOL .
		'dbpass="' . addcslashes($_POST['dbpass'], '"') . '"' . PHP_EOL .
		'dbname="' . addcslashes($_POST['dbname'], '"') . '"' . PHP_EOL .
		'tblpfx="' . addcslashes($_POST['tblpfx'], '"') . '"' . PHP_EOL;

	file_put_contents(WT_DATA_DIR . 'config.ini.php', $config_ini_php);

	// Done - start using webtrees!
	echo '<script>document.location=document.location;</script>';
	echo '</form></body></html>';
} catch (PDOException $ex) {
	echo
		'<p class="bad">', I18N::translate('An unexpected database error occurred.'), '</p>',
		'<pre>', $ex->getMessage(), '</pre>',
		'<p class="info">', I18N::translate('The webtrees developers would be very interested to learn about this error.  If you contact them, they will help you resolve the problem.'), '</p>';
}
