<?php
// Setup Wizard
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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

// To embed webtrees code in other applications, we must explicitly declare any global variables that we create.
global $WT_REQUEST, $WT_SESSION;

define('WT_SCRIPT_NAME', 'setup.php');
define('WT_CONFIG_FILE', 'config.ini.php');

require 'library/autoload.php';

// This script (uniquely) does not load session.php.
// session.php won’t run until a configuration file exists…
// This next block of code is a minimal version of session.php
define('WT_WEBTREES',    'webtrees');
define('WT_SERVER_NAME', '');
define('WT_SCRIPT_PATH', '');
require 'includes/functions/functions_db.php'; // for get/setSiteSetting()
define('WT_DATA_DIR',    'data/');
define('WT_DEBUG_SQL',   false);
define('WT_REQUIRED_MYSQL_VERSION', '5.0.13');
define('WT_REQUIRED_PHP_VERSION',   '5.3.2');
define('WT_MODULES_DIR', 'modules_v3/');
define('WT_ROOT', '');
define('WT_GED_ID', null);
define('WT_PRIV_PUBLIC', 2);
define('WT_PRIV_USER',   1);
define('WT_PRIV_NONE',   0);
define('WT_PRIV_HIDE',  -1);

if (file_exists(WT_DATA_DIR . WT_CONFIG_FILE)) {
	header('Location: index.php');
	exit;
}

if (version_compare(PHP_VERSION, WT_REQUIRED_PHP_VERSION)<0) {
	// We cannot translate these messages without a modern PHP
	echo
		'<h1>Sorry, the setup wizard cannot start.</h1>',
		'<p>This server is running PHP version ', PHP_VERSION, '</p>',
		'<p>PHP ', WT_REQUIRED_PHP_VERSION , ' (or any later version) is required</p>';
	exit;
}

require 'includes/functions/functions.php';
require 'includes/functions/functions_edit.php';

$WT_REQUEST = new Zend_Controller_Request_Http();
$WT_SESSION = new stdClass;
$WT_SESSION->locale  = null; // Needed for WT_I18N
$WT_SESSION->wt_user = null; // Needed for WT_Auth
define('WT_LOCALE', WT_I18N::init(WT_Filter::post('lang', '[@a-zA-Z_]+')));

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html <?php echo WT_I18N::html_markup(); ?>>
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
			<?php echo WT_I18N::translate('Setup wizard for webtrees'); ?>
		</h1>
<?php

echo '<form name="config" action="', WT_SCRIPT_NAME, '" method="post" onsubmit="this.btncontinue.disabled=\'disabled\';">';
echo '<input type="hidden" name="lang" value="', WT_LOCALE, '">';

////////////////////////////////////////////////////////////////////////////////
// Step one - choose language and confirm server configuration
////////////////////////////////////////////////////////////////////////////////

if (!isset($_POST['lang'])) {
	echo
		'<p>', WT_I18N::translate('Change language'), ' ',
		edit_field_language('change_lang', WT_LOCALE, 'onchange="window.location=\'' .  WT_SCRIPT_NAME . '?lang=\'+this.value;">'),
		'</p>',
		'<h2>', WT_I18N::translate('Checking server configuration'), '</h2>';
	$warnings=false;
	$errors=false;

	// Mandatory functions
	$disable_functions=preg_split('/ *, */', ini_get('disable_functions'));
	foreach (array('ini_set', 'parse_ini_file') as $function) {
		if (in_array($function, $disable_functions)) {
			echo '<p class="bad">', /* I18N: %s is a PHP function/module/setting */ WT_I18N::translate('%s is disabled on this server.  You cannot install webtrees until it is enabled.  Please ask your server’s administrator to enable it.', $function.'()'), '</p>';
			$errors=true;
		}
	}
	// Mandatory extensions
	foreach (array('pcre', 'pdo', 'pdo_mysql', 'session', 'iconv') as $extension) {
		if (!extension_loaded($extension)) {
			echo '<p class="bad">', WT_I18N::translate('PHP extension “%s” is disabled.  You cannot install webtrees until this is enabled.  Please ask your server’s administrator to enable it.', $extension), '</p>';
			$errors=true;
		}
	}
	// Recommended extensions
	foreach (array(
		'gd'        => /* I18N: a program feature */ WT_I18N::translate('creating thumbnails of images'),
		'xml'       => /* I18N: a program feature */ WT_I18N::translate('reporting'),
		'simplexml' => /* I18N: a program feature */ WT_I18N::translate('reporting'),
	) as $extension=>$features) {
		if (!extension_loaded($extension)) {
			echo '<p class="bad">', WT_I18N::translate('PHP extension “%1$s” is disabled.  Without it, the following features will not work: %2$s.  Please ask your server’s administrator to enable it.', $extension, $features), '</p>';
			$warnings=true;
		}
	}
	// Settings
	foreach (array(
		'file_uploads'=>/* I18N: a program feature */ WT_I18N::translate('file upload capability'),
	) as $setting=>$features) {
		if (!ini_get($setting)) {
			echo '<p class="bad">', WT_I18N::translate('PHP setting “%1$s” is disabled.  Without it, the following features will not work: %2$s.  Please ask your server’s administrator to enable it.', $setting, $features), '</p>';
			$warnings=true;
		}
	}
	if (!$warnings && !$errors) {
		echo '<p class="good">', WT_I18N::translate('The server configuration is OK.'), '</p>';
	}
	echo '<h2>', WT_I18N::translate('Checking server capacity'), '</h2>';
	// Previously, we tried to determine the maximum value that we could set for these values.
	// However, this is unreliable, especially on servers with custom restrictions.
	// Now, we just show the default values.  These can (hopefully!) be changed using the
	// site settings page.
	$memory_limit = ini_get('memory_limit');
	switch (strtoupper(substr($memory_limit, -1))) {
	case 'K':
		$maxmem = (int)(substr($memory_limit, 0, strlen($memory_limit)-1)/1024);
	case 'M':
		$maxmem = (int)(substr($memory_limit, 0, strlen($memory_limit)-1));
	case 'G':
		$maxmem = (int)(1024*substr($memory_limit, 0, strlen($memory_limit)-1));
	default:
		$maxmem = $memory_limit;
	}
	$maxcpu=ini_get('max_execution_time');
	echo
		'<p>',
		WT_I18N::translate('The memory and CPU time requirements depend on the number of individuals in your family tree.'),
		'<br>',
		WT_I18N::translate('The following list shows typical requirements.'),
		'</p><p>',
		WT_I18N::translate('Small systems (500 individuals): 16–32 MB, 10–20 seconds'),
		'<br>',
		WT_I18N::translate('Medium systems (5,000 individuals): 32–64 MB, 20–40 seconds'),
		'<br>',
		WT_I18N::translate('Large systems (50,000 individuals): 64–128 MB, 40–80 seconds'),
		'</p>',
		($maxmem<32 || $maxcpu<20) ? '<p class="bad">' : '<p class="good">',
		WT_I18N::translate('This server’s memory limit is %d MB and its CPU time limit is %d seconds.', $maxmem, $maxcpu),
		'</p><p>',
		WT_I18N::translate('If you try to exceed these limits, you may experience server time-outs and blank pages.'),
		'</p><p>',
		WT_I18N::translate('If your server’s security policy permits it, you will be able to request increased memory or CPU time using the webtrees administration page.  Otherwise, you will need to contact your server’s administrator.'),
		'</p>';
	if (!$errors) {
		echo '<input type="hidden" name="maxcpu" value="', $maxcpu, '">';
		echo '<input type="hidden" name="maxmem" value="', $maxmem, '">';
		echo '<br><hr><input type="submit" id="btncontinue" value="', /* I18N: button label */ WT_I18N::translate('continue'), '">';

	}
	echo '</form></body></html>';
	exit;
} else {
	// Copy these values through to the next step
	echo '<input type="hidden" name="maxcpu" value="', $_POST['maxcpu'], '">';
	echo '<input type="hidden" name="maxmem" value="', $_POST['maxmem'], '">';
}

////////////////////////////////////////////////////////////////////////////////
// Step two - The data folder needs to be writable
////////////////////////////////////////////////////////////////////////////////

@file_put_contents(WT_DATA_DIR . 'test.txt', 'FAB!');
$FAB = @file_get_contents(WT_DATA_DIR . 'test.txt');
@unlink(WT_DATA_DIR . 'test.txt');

if ($FAB != 'FAB!') {
	echo '<h2>', realpath(WT_DATA_DIR), '</h2>';
	echo '<p class="bad">', WT_I18N::translate('Oops!  webtrees was unable to create files in this folder.'), '</p>';
	echo '<p>', WT_I18N::translate('This usually means that you need to change the folder permissions to 777.'), '</p>';
	echo '<p>', WT_I18N::translate('You must change this before you can continue.'), '</p>';
	echo '<br><hr><input type="submit" id="btncontinue" value="', WT_I18N::translate('continue'), '">';
	echo '</form></body></html>';
	exit;
}

////////////////////////////////////////////////////////////////////////////////
// Step three - Database connection.
////////////////////////////////////////////////////////////////////////////////

if (!isset($_POST['dbhost'])) $_POST['dbhost']='localhost';
if (!isset($_POST['dbport'])) $_POST['dbport']='3306';
if (!isset($_POST['dbuser'])) $_POST['dbuser']='';
if (!isset($_POST['dbpass'])) $_POST['dbpass']='';
if (!isset($_POST['dbname'])) $_POST['dbname']='';
if (!isset($_POST['tblpfx'])) $_POST['tblpfx']='wt_';

define('WT_TBLPREFIX', $_POST['tblpfx']);
try {
	$db_version_ok=false;
	WT_DB::createInstance(
		$_POST['dbhost'],
		$_POST['dbport'],
		'',               // No DBNAME - we will connect to it explicitly
		$_POST['dbuser'],
		$_POST['dbpass']
	);
	WT_DB::exec("SET NAMES 'utf8'");
	$row=WT_DB::prepare("SHOW VARIABLES LIKE 'VERSION'")->fetchOneRow();
	if (version_compare($row->value, WT_REQUIRED_MYSQL_VERSION, '<')) {
		echo '<p class="bad">', WT_I18N::translate('This database is only running MySQL version %s.  You cannot install webtrees here.', $row->value), '</p>';
	} else {
		$db_version_ok=true;
	}
} catch (PDOException $ex) {
	WT_DB::disconnect();
	if ($_POST['dbuser']) {
		// If we’ve supplied a login, then show the error
		echo
			'<p class="bad">', WT_I18N::translate('Unable to connect using these settings.  Your server gave the following error.'), '</p>',
			'<pre>', $ex->getMessage(), '</pre>',
			'<p class="bad">', WT_I18N::translate('Check the settings and try again.'), '</p>';
	}
}

if (empty($_POST['dbuser']) || !WT_DB::isConnected() || !$db_version_ok) {
	echo
		'<h2>', WT_I18N::translate('Connection to database server'), '</h2>',
		'<p>', WT_I18N::translate('webtrees needs a MySQL database, version %s or later.', WT_REQUIRED_MYSQL_VERSION), '</p>',
		'<p>', WT_I18N::translate('Your server’s administrator will provide you with the connection details.'), '</p>',
		'<fieldset><legend>', WT_I18N::translate('Database connection'), '</legend>',
		'<table border="0"><tr><td>',
		WT_I18N::translate('Server name'), '</td><td>',
		'<input type="text" name="dbhost" value="', WT_Filter::escapeHtml($_POST['dbhost']), '" dir="ltr"></td><td>',
		WT_I18N::translate('Most sites are configured to use localhost.  This means that your database runs on the same computer as your web server.'),
		'</td></tr><tr><td>',
		WT_I18N::translate('Port number'), '</td><td>',
		'<input type="text" name="dbport" value="', WT_Filter::escapeHtml($_POST['dbport']), '"></td><td>',
		WT_I18N::translate('Most sites are configured to use the default value of 3306.'),
		'</td></tr><tr><td>',
		WT_I18N::translate('Database user account'), '</td><td>',
		'<input type="text" name="dbuser" value="', WT_Filter::escapeHtml($_POST['dbuser']), '" autofocus></td><td>',
		WT_I18N::translate('This is case sensitive.'),
		'</td></tr><tr><td>',
		WT_I18N::translate('Database password'), '</td><td>',
		'<input type="password" name="dbpass" value="', WT_Filter::escapeHtml($_POST['dbpass']), '"></td><td>',
		WT_I18N::translate('This is case sensitive.'),
		'</td></tr><tr><td>',
		'</td></tr></table>',
		'</fieldset>',
		'<br><hr><input type="submit" id="btncontinue" value="', WT_I18N::translate('continue'), '">',
		'</form>',
		'</body></html>';
		exit;
} else {
	// Copy these values through to the next step
	echo '<input type="hidden" name="dbhost" value="', WT_Filter::escapeHtml($_POST['dbhost']), '">';
	echo '<input type="hidden" name="dbport" value="', WT_Filter::escapeHtml($_POST['dbport']), '">';
	echo '<input type="hidden" name="dbuser" value="', WT_Filter::escapeHtml($_POST['dbuser']), '">';
	echo '<input type="hidden" name="dbpass" value="', WT_Filter::escapeHtml($_POST['dbpass']), '">';
}

////////////////////////////////////////////////////////////////////////////////
// Step four - Database connection.
////////////////////////////////////////////////////////////////////////////////

// The character ` is not valid in database or table names (even if escaped).
// By removing it, we can ensure that our SQL statements are quoted correctly.
//
// Other characters may be invalid (objects must be valid filenames on the
// MySQL server’s filesystem), so block the usual ones.
$DBNAME   =str_replace(array('`', '"', '\'', ':', '/', '\\', '\r', '\n', '\t', '\0'), '', $_POST['dbname']);
$TBLPREFIX=str_replace(array('`', '"', '\'', ':', '/', '\\', '\r', '\n', '\t', '\0'), '', $_POST['tblpfx']);

// If we have specified a database, and we have not used invalid characters,
// try to connect to it.
$dbname_ok=false;
if ($DBNAME && $DBNAME==$_POST['dbname'] && $TBLPREFIX==$_POST['tblpfx']) {
	try {
		// Try to create the database, if it does not exist.
		WT_DB::exec("CREATE DATABASE IF NOT EXISTS `{$DBNAME}` COLLATE utf8_unicode_ci");
	} catch (PDOException $ex) {
		// If we have no permission to do this, there’s nothing helpful we can say.
		// We’ll get a more helpful error message from the next test.
	}
	try {
		WT_DB::exec("USE `{$DBNAME}`");
		$dbname_ok=true;
	} catch (PDOException $ex) {
		echo
			'<p class="bad">', WT_I18N::translate('Unable to connect using these settings.  Your server gave the following error.'), '</p>',
			'<pre>', $ex->getMessage(), '</pre>',
			'<p class="bad">', WT_I18N::translate('Check the settings and try again.'), '</p>';
	}
}

// If the database exists, check whether it is already used by another application.
if ($dbname_ok) {
	try {
		// PhpGedView (4.2.3 and earlier) and many other applications have a USERS table.
		// webtrees has a USER table
		$dummy=WT_DB::prepare("SELECT COUNT(*) FROM `##users`")->fetchOne();
		echo '<p class="bad">', WT_I18N::translate('This database and table-prefix appear to be used by another application.  If you have an existing PhpGedView system, you should create a new webtrees system.  You can import your PhpGedView data and settings later.'), '</p>';
		$dbname_ok=false;
	} catch (PDOException $ex) {
		// Table not found?  Good!
	}
}
if ($dbname_ok) {
	try {
		// PhpGedView (4.2.4 and later) has a site_setting.site_setting_name column.
		// [We changed the column name in webtrees, so we can tell the difference!]
		$dummy=WT_DB::prepare("SELECT site_setting_value FROM `##site_setting` WHERE site_setting_name='PGV_SCHEMA_VERSION'")->fetchOne();
		echo '<p class="bad">', WT_I18N::translate('This database and table-prefix appear to be used by another application.  If you have an existing PhpGedView system, you should create a new webtrees system.  You can import your PhpGedView data and settings later.'), '</p>';
		$dbname_ok=false;
	} catch (PDOException $ex) {
		// Table/column not found?  Good!
	}
}

if (!$dbname_ok) {
	echo
		'<h2>', WT_I18N::translate('Database and table names'), '</h2>',
		'<p>', WT_I18N::translate('A database server can store many separate databases.  You need to select an existing database (created by your server’s administrator) or create a new one (if your database user account has sufficient privileges).'), '</p>',
		'<fieldset><legend>', WT_I18N::translate('Database name'), '</legend>',
		'<table border="0"><tr><td>',
		WT_I18N::translate('Database name'), '</td><td>',
		'<input type="text" name="dbname" value="', WT_Filter::escapeHtml($_POST['dbname']), '" autofocus></td><td>',
		WT_I18N::translate('This is case sensitive.  If a database with this name does not already exist webtrees will attempt to create one for you.  Success will depend on permissions set for your web server, but you will be notified if this fails.'),
		'</td></tr><tr><td>',
		WT_I18N::translate('Table prefix'), '</td><td>',
		'<input type="text" name="tblpfx" value="', WT_Filter::escapeHtml($_POST['tblpfx']), '"></td><td>',
		WT_I18N::translate('The prefix is optional, but recommended.  By giving the table names a unique prefix you can let several different applications share the same database. “wt_” is suggested, but can be anything you want.'),
		'</td></tr></table>',
		'</fieldset>',
		'<br><hr><input type="submit" id="btncontinue" value="', WT_I18N::translate('continue'), '">',
		'</form>',
		'</body></html>';
		exit;
} else {
	// Copy these values through to the next step
	echo '<input type="hidden" name="dbname" value="', WT_Filter::escapeHtml($_POST['dbname']), '">';
	echo '<input type="hidden" name="tblpfx" value="', WT_Filter::escapeHtml($_POST['tblpfx']), '">';
}

////////////////////////////////////////////////////////////////////////////////
// Step five - site setup data
////////////////////////////////////////////////////////////////////////////////

if (!isset($_POST['wtname'    ])) $_POST['wtname'    ]='';
if (!isset($_POST['wtuser'    ])) $_POST['wtuser'    ]='';
if (!isset($_POST['wtpass'    ])) $_POST['wtpass'    ]='';
if (!isset($_POST['wtpass2'   ])) $_POST['wtpass2'   ]='';
if (!isset($_POST['wtemail'   ])) $_POST['wtemail'   ]='';

if (empty($_POST['wtname']) || empty($_POST['wtuser']) || strlen($_POST['wtpass'])<6 || strlen($_POST['wtpass2'])<6 || empty($_POST['wtemail']) || $_POST['wtpass']<>$_POST['wtpass2']) {
	if (strlen($_POST['wtpass'])>0 && strlen($_POST['wtpass'])<6) {
		echo '<p class="bad">', WT_I18N::translate('The password needs to be at least six characters long.'), '</p>';
	} elseif ($_POST['wtpass']<>$_POST['wtpass2']) {
		echo '<p class="bad">', WT_I18N::translate('The passwords do not match.'), '</p>';
	} elseif ((empty($_POST['wtname']) || empty($_POST['wtuser']) || empty($_POST['wtpass']) || empty($_POST['wtemail'])) && $_POST['wtname'].$_POST['wtuser'].$_POST['wtpass'].$_POST['wtemail']!='') {
		echo '<p class="bad">', WT_I18N::translate('You must enter all the administrator account fields.'), '</p>';
	}
	echo
		'<h2>', WT_I18N::translate('System settings'), '</h2>',
		'<h3>', WT_I18N::translate('Administrator account'), '</h3>',
		'<p>', WT_I18N::translate('You need to set up an administrator account.  This account can control all aspects of this webtrees installation.  Please choose a strong password.'), '</p>',
		'<fieldset><legend>', WT_I18N::translate('Administrator account'), '</legend>',
		'<table border="0"><tr><td>',
		WT_I18N::translate('Your name'), '</td><td>',
		'<input type="text" name="wtname" value="', WT_Filter::escapeHtml($_POST['wtname']), '" autofocus></td><td>',
		WT_I18N::translate('This is your real name, as you would like it displayed on screen.'),
		'</td></tr><tr><td>',
		WT_I18N::translate('Login ID'), '</td><td>',
		'<input type="text" name="wtuser" value="', WT_Filter::escapeHtml($_POST['wtuser']), '"></td><td>',
		WT_I18N::translate('You will use this to login to webtrees.'),
		'</td></tr><tr><td>',
		WT_I18N::translate('Password'), '</td><td>',
		'<input type="password" name="wtpass" value="', WT_Filter::escapeHtml($_POST['wtpass']), '"></td><td>',
		WT_I18N::translate('This must to be at least six characters.  It is case-sensitive.'),
		'</td></tr><tr><td>',
		'&nbsp;', '</td><td>',
		'<input type="password" name="wtpass2" value="', WT_Filter::escapeHtml($_POST['wtpass2']), '"></td><td>',
		WT_I18N::translate('Type your password again, to make sure you have typed it correctly.'),
		'</td></tr><tr><td>',
		WT_I18N::translate('Email address'), '</td><td>',
		'<input type="email" name="wtemail" value="', WT_Filter::escapeHtml($_POST['wtemail']), '"></td><td>',
		WT_I18N::translate('This email address will be used to send you password reminders, site notifications, and messages from other family members who are registered on the site.'),
		'</td></tr><tr><td>',
		'</td></tr></table>',
		'</fieldset>',
		'<br><hr><input type="submit" id="btncontinue" value="', WT_I18N::translate('continue'), '">',
		'</form>',
		'</body></html>';
		exit;
} else {
	// Copy these values through to the next step
	echo '<input type="hidden" name="wtname"     value="', WT_Filter::escapeHtml($_POST['wtname']), '">';
	echo '<input type="hidden" name="wtuser"     value="', WT_Filter::escapeHtml($_POST['wtuser']), '">';
	echo '<input type="hidden" name="wtpass"     value="', WT_Filter::escapeHtml($_POST['wtpass']), '">';
	echo '<input type="hidden" name="wtpass2"    value="', WT_Filter::escapeHtml($_POST['wtpass2']), '">';
	echo '<input type="hidden" name="wtemail"    value="', WT_Filter::escapeHtml($_POST['wtemail']), '">';
}

////////////////////////////////////////////////////////////////////////////////
// Step six  We have a database connection and a writable folder.  Do it!
////////////////////////////////////////////////////////////////////////////////

try {
	// These shouldn’t fail.
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##gedcom` (".
		" gedcom_id     INTEGER AUTO_INCREMENT NOT NULL,".
		" gedcom_name   VARCHAR(255)           NOT NULL,".
		" sort_order    INTEGER                NOT NULL DEFAULT 0,".
		" PRIMARY KEY                (gedcom_id),".
		" UNIQUE  KEY `##gedcom_ix1` (gedcom_name),".
		"         KEY `##gedcom_ix2` (sort_order)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##site_setting` (".
		" setting_name  VARCHAR(32)  NOT NULL,".
		" setting_value VARCHAR(255) NOT NULL,".
		" PRIMARY KEY (setting_name)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##gedcom_setting` (".
		" gedcom_id     INTEGER      NOT NULL,".
		" setting_name  VARCHAR(32)  NOT NULL,".
		" setting_value VARCHAR(255) NOT NULL,".
		" PRIMARY KEY                        (gedcom_id, setting_name),".
		" FOREIGN KEY `##gedcom_setting_fk1` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##user` (".
		" user_id   INTEGER AUTO_INCREMENT NOT NULL,".
		" user_name VARCHAR(32)            NOT NULL,".
		" real_name VARCHAR(64)            NOT NULL,".
		" email     VARCHAR(64)            NOT NULL,".
		" password  VARCHAR(128)           NOT NULL,".
		" PRIMARY KEY              (user_id),".
		" UNIQUE  KEY `##user_ix1` (user_name),".
		" UNIQUE  KEY `##user_ix2` (email)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##user_setting` (".
		" user_id       INTEGER      NOT NULL,".
		" setting_name  VARCHAR(32)  NOT NULL,".
		" setting_value VARCHAR(255) NOT NULL,".
		" PRIMARY KEY                      (user_id, setting_name),".
		" FOREIGN KEY `##user_setting_fk1` (user_id) REFERENCES `##user` (user_id) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##user_gedcom_setting` (".
		" user_id       INTEGER      NOT NULL,".
		" gedcom_id     INTEGER      NOT NULL,".
		" setting_name  VARCHAR(32)  NOT NULL,".
		" setting_value VARCHAR(255) NOT NULL,".
		" PRIMARY KEY                             (user_id, gedcom_id, setting_name),".
		" FOREIGN KEY `##user_gedcom_setting_fk1` (user_id)   REFERENCES `##user` (user_id) /* ON DELETE CASCADE */,".
		" FOREIGN KEY `##user_gedcom_setting_fk2` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##log` (".
		" log_id      INTEGER AUTO_INCREMENT NOT NULL,".
		" log_time    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,".
		" log_type    ENUM('auth', 'config', 'debug', 'edit', 'error', 'media', 'search') NOT NULL,".
		" log_message TEXT         NOT NULL,".
		" ip_address  VARCHAR(40)  NOT NULL,".
		" user_id     INTEGER          NULL,".
		" gedcom_id   INTEGER          NULL,".
		" PRIMARY KEY             (log_id),".
		"         KEY `##log_ix1` (log_time),".
		"         KEY `##log_ix2` (log_type),".
		"         KEY `##log_ix3` (ip_address),".
		" FOREIGN KEY `##log_fk1` (user_id)   REFERENCES `##user`(user_id) /* ON DELETE SET NULL */,".
		" FOREIGN KEY `##log_fk2` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) /* ON DELETE SET NULL */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##change` (".
		" change_id      INTEGER AUTO_INCREMENT                  NOT NULL,".
		" change_time    TIMESTAMP                               NOT NULL DEFAULT CURRENT_TIMESTAMP,".
		" status         ENUM('accepted', 'pending', 'rejected') NOT NULL DEFAULT 'pending',".
		" gedcom_id      INTEGER                                 NOT NULL,".
		" xref           VARCHAR(20)                             NOT NULL,".
		" old_gedcom     MEDIUMTEXT                              NOT NULL,".
		" new_gedcom     MEDIUMTEXT                              NOT NULL,".
		" user_id        INTEGER                                 NOT NULL,".
		" PRIMARY KEY                (change_id),".
		"         KEY `##change_ix1` (gedcom_id, status, xref),".
		" FOREIGN KEY `##change_fk1` (user_id)   REFERENCES `##user` (user_id) /* ON DELETE RESTRICT */,".
		" FOREIGN KEY `##change_fk2` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##message` (".
		" message_id INTEGER AUTO_INCREMENT NOT NULL,".
		" sender     VARCHAR(64)            NOT NULL,". // username or email address
		" ip_address VARCHAR(40)            NOT NULL,". // long enough for IPv6
		" user_id    INTEGER                NOT NULL,".
		" subject    VARCHAR(255)           NOT NULL,".
		" body       TEXT                   NOT NULL,".
		" created    TIMESTAMP              NOT NULL DEFAULT CURRENT_TIMESTAMP,".
		" PRIMARY KEY                 (message_id),".
		" FOREIGN KEY `##message_fk1` (user_id)   REFERENCES `##user` (user_id) /* ON DELETE RESTRICT */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##default_resn` (".
		" default_resn_id INTEGER AUTO_INCREMENT                             NOT NULL,".
		" gedcom_id       INTEGER                                            NOT NULL,".
		" xref            VARCHAR(20)                                            NULL,".
		" tag_type        VARCHAR(15)                                            NULL,".
		" resn            ENUM ('none', 'privacy', 'confidential', 'hidden') NOT NULL,".
		" comment         VARCHAR(255)                                           NULL,".
		" updated         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,".
		" PRIMARY KEY                      (default_resn_id),".
		" UNIQUE  KEY `##default_resn_ix1` (gedcom_id, xref, tag_type),".
		" FOREIGN KEY `##default_resn_fk1` (gedcom_id)  REFERENCES `##gedcom` (gedcom_id)".
		") ENGINE=InnoDB COLLATE=utf8_unicode_ci"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##individuals` (".
		" i_id     VARCHAR(20)         NOT NULL,".
		" i_file   INTEGER             NOT NULL,".
		" i_rin    VARCHAR(20)         NOT NULL,".
		" i_sex    ENUM('U', 'M', 'F') NOT NULL,".
		" i_gedcom MEDIUMTEXT          NOT NULL,".
		" PRIMARY KEY                     (i_id, i_file),".
		" UNIQUE  KEY `##individuals_ix1` (i_file, i_id)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##families` (".
		" f_id      VARCHAR(20)  NOT NULL,".
		" f_file    INTEGER      NOT NULL,".
		" f_husb    VARCHAR(20)      NULL,".
		" f_wife    VARCHAR(20)      NULL,".
		" f_gedcom  MEDIUMTEXT   NOT NULL,".
		" f_numchil INTEGER      NOT NULL,".
		" PRIMARY KEY                  (f_id, f_file),".
		" UNIQUE  KEY `##families_ix1` (f_file, f_id),".
		"         KEY `##families_ix2` (f_husb),".
		"         KEY `##families_ix3` (f_wife)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##places` (".
		" p_id          INTEGER AUTO_INCREMENT NOT NULL,".
		" p_place       VARCHAR(150)               NULL,".
		" p_parent_id   INTEGER                    NULL,".
		" p_file        INTEGER               NOT  NULL,".
		" p_std_soundex TEXT                       NULL,".
		" p_dm_soundex  TEXT                       NULL,".
		" PRIMARY KEY                (p_id),".
		"         KEY `##places_ix1` (p_file, p_place),".
		" UNIQUE  KEY `##places_ix2` (p_parent_id, p_file, p_place)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##placelinks` (".
		" pl_p_id INTEGER NOT NULL,".
		" pl_gid  VARCHAR(20)  NOT NULL,".
		" pl_file INTEGER  NOT NULL,".
		" PRIMARY KEY                    (pl_p_id, pl_gid, pl_file),".
		"         KEY `##placelinks_ix1` (pl_p_id),".
		"         KEY `##placelinks_ix2` (pl_gid),".
		"         KEY `##placelinks_ix3` (pl_file)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##dates` (".
		" d_day        TINYINT     NOT NULL,".
		" d_month      CHAR(5)         NULL,".
		" d_mon        TINYINT     NOT NULL,".
		" d_year       SMALLINT    NOT NULL,".
		" d_julianday1 MEDIUMINT   NOT NULL,".
		" d_julianday2 MEDIUMINT   NOT NULL,".
		" d_fact       VARCHAR(15) NOT NULL,".
		" d_gid        VARCHAR(20) NOT NULL,".
		" d_file       INTEGER     NOT NULL,".
		" d_type       ENUM ('@#DGREGORIAN@', '@#DJULIAN@', '@#DHEBREW@', '@#DFRENCH R@', '@#DHIJRI@', '@#DROMAN@', '@#DJALALI@') NOT NULL,".
		" KEY `##dates_ix1` (d_day),".
		" KEY `##dates_ix2` (d_month),".
		" KEY `##dates_ix3` (d_mon),".
		" KEY `##dates_ix4` (d_year),".
		" KEY `##dates_ix5` (d_julianday1),".
		" KEY `##dates_ix6` (d_julianday2),".
		" KEY `##dates_ix7` (d_gid),".
		" KEY `##dates_ix8` (d_file),".
		" KEY `##dates_ix9` (d_type),".
		" KEY `##dates_ix10` (d_fact, d_gid)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##media` (".
		" m_id       VARCHAR(20)            NOT NULL,".
		" m_ext      VARCHAR(6)                 NULL,".
		" m_type     VARCHAR(20)                NULL,".
		" m_titl     VARCHAR(255)               NULL,".
		" m_filename VARCHAR(512)               NULL,".
		" m_file     INTEGER                NOT NULL,".
		" m_gedcom   MEDIUMTEXT                 NULL,".
		" PRIMARY KEY               (m_file, m_id),".
		" UNIQUE  KEY `##media_ix1` (m_id, m_file),".
		"         KEY `##media_ix2` (m_ext, m_type),".
		"         KEY `##media_ix3` (m_titl)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##next_id` (".
		" gedcom_id   INTEGER     NOT NULL,".
		" record_type VARCHAR(15) NOT NULL,".
		" next_id     DECIMAL(20) NOT NULL,".
		" PRIMARY KEY                 (gedcom_id, record_type),".
		" FOREIGN KEY `##next_id_fk1` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##other` (".
		" o_id     VARCHAR(20) NOT NULL,".
		" o_file   INTEGER     NOT NULL,".
		" o_type   VARCHAR(15) NOT NULL,".
		" o_gedcom MEDIUMTEXT      NULL,".
		" PRIMARY KEY               (o_id, o_file),".
		" UNIQUE  KEY `##other_ix1` (o_file, o_id)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##sources` (".
		" s_id     VARCHAR(20)    NOT NULL,".
		" s_file   INTEGER        NOT NULL,".
		" s_name   VARCHAR(255)   NOT NULL,".
		" s_gedcom MEDIUMTEXT     NOT NULL,".
		" PRIMARY KEY                 (s_id, s_file),".
		" UNIQUE  KEY `##sources_ix1` (s_file, s_id),".
		"         KEY `##sources_ix2` (s_name)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##link` (".
		" l_file    INTEGER     NOT NULL,".
		" l_from    VARCHAR(20) NOT NULL,".
		" l_type    VARCHAR(15) NOT NULL,".
		" l_to      VARCHAR(20) NOT NULL,".
		" PRIMARY KEY              (l_from, l_file, l_type, l_to),".
		" UNIQUE  KEY `##link_ix1` (l_to, l_file, l_type, l_from)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##name` (".
		" n_file             INTEGER      NOT NULL,".
		" n_id               VARCHAR(20)  NOT NULL,".
		" n_num              INTEGER      NOT NULL,".
		" n_type             VARCHAR(15)  NOT NULL,".
		" n_sort             VARCHAR(255) NOT NULL,". // e.g. “GOGH,VINCENT WILLEM”
		" n_full             VARCHAR(255) NOT NULL,". // e.g. “Vincent Willem van GOGH”
		// These fields are only used for INDI records
		" n_surname          VARCHAR(255)     NULL,". // e.g. “van GOGH”
		" n_surn             VARCHAR(255)     NULL,". // e.g. “GOGH”
		" n_givn             VARCHAR(255)     NULL,". // e.g. “Vincent Willem”
		" n_soundex_givn_std VARCHAR(255)     NULL,".
		" n_soundex_surn_std VARCHAR(255)     NULL,".
		" n_soundex_givn_dm  VARCHAR(255)     NULL,".
		" n_soundex_surn_dm  VARCHAR(255)     NULL,".
		" PRIMARY KEY              (n_id, n_file, n_num),".
		"         KEY `##name_ix1` (n_full, n_id, n_file),".
		"         KEY `##name_ix2` (n_surn, n_file, n_type, n_id),".
		"         KEY `##name_ix3` (n_givn, n_file, n_type, n_id)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##module` (".
		" module_name   VARCHAR(32)                 NOT NULL,".
		" status        ENUM('enabled', 'disabled') NOT NULL DEFAULT 'enabled',".
		" tab_order     INTEGER                         NULL, ".
		" menu_order    INTEGER                         NULL, ".
		" sidebar_order INTEGER                         NULL,".
		" PRIMARY KEY (module_name)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##module_setting` (".
		" module_name   VARCHAR(32) NOT NULL,".
		" setting_name  VARCHAR(32) NOT NULL,".
		" setting_value MEDIUMTEXT  NOT NULL,".
		" PRIMARY KEY                        (module_name, setting_name),".
		" FOREIGN KEY `##module_setting_fk1` (module_name) REFERENCES `##module` (module_name) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##module_privacy` (".
		" module_name   VARCHAR(32) NOT NULL,".
		" gedcom_id     INTEGER     NOT NULL,".
		" component     ENUM('block', 'chart', 'menu', 'report', 'sidebar', 'tab', 'theme') NOT NULL,".
		" access_level  TINYINT     NOT NULL,".
		" PRIMARY KEY                        (module_name, gedcom_id, component),".
		" FOREIGN KEY `##module_privacy_fk1` (module_name) REFERENCES `##module` (module_name) /* ON DELETE CASCADE */,".
		" FOREIGN KEY `##module_privacy_fk2` (gedcom_id)   REFERENCES `##gedcom` (gedcom_id)   /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##block` (".
		" block_id    INTEGER AUTO_INCREMENT NOT NULL,".
		" gedcom_id   INTEGER                    NULL,".
		" user_id     INTEGER                    NULL,".
		" xref        VARCHAR(20)                NULL,".
		" location    ENUM('main', 'side')       NULL,".
		" block_order INTEGER                NOT NULL,".
		" module_name VARCHAR(32)            NOT NULL,".
		" PRIMARY KEY               (block_id),".
		" FOREIGN KEY `##block_fk1` (gedcom_id)   REFERENCES `##gedcom` (gedcom_id),  /* ON DELETE CASCADE */".
		" FOREIGN KEY `##block_fk2` (user_id)     REFERENCES `##user`   (user_id),    /* ON DELETE CASCADE */".
		" FOREIGN KEY `##block_fk3` (module_name) REFERENCES `##module` (module_name) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##block_setting` (".
		" block_id      INTEGER     NOT NULL,".
		" setting_name  VARCHAR(32) NOT NULL,".
		" setting_value TEXT        NOT NULL,".
		" PRIMARY KEY                       (block_id, setting_name),".
		" FOREIGN KEY `##block_setting_fk1` (block_id) REFERENCES `##block` (block_id) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##hit_counter` (".
		" gedcom_id      INTEGER     NOT NULL,".
		" page_name      VARCHAR(32) NOT NULL,".
		" page_parameter VARCHAR(32) NOT NULL,".
		" page_count     INTEGER     NOT NULL,".
		" PRIMARY KEY                     (gedcom_id, page_name, page_parameter),".
		" FOREIGN KEY `##hit_counter_fk1` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##ip_address` (".
		" ip_address VARCHAR(40)                                NOT NULL,". // long enough for IPv6
		" category   ENUM('banned', 'search-engine', 'allowed') NOT NULL,".
		" comment    VARCHAR(255)                               NOT NULL,".
		" PRIMARY KEY (ip_address)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##session` (".
		" session_id   CHAR(128)   NOT NULL,".
		" session_time TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,".
		" user_id      INTEGER     NOT NULL,".
		" ip_address   VARCHAR(32) NOT NULL,".
		" session_data MEDIUMBLOB  NOT NULL,".
		" PRIMARY KEY                 (session_id),".
		"         KEY `##session_ix1` (session_time),".
		"         KEY `##session_ix2` (user_id, ip_address)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##gedcom_chunk` (".
		" gedcom_chunk_id INTEGER AUTO_INCREMENT NOT NULL,".
		" gedcom_id       INTEGER                NOT NULL,".
		" chunk_data      MEDIUMBLOB             NOT NULL,".
		" imported        BOOLEAN                NOT NULL DEFAULT FALSE,".
		" PRIMARY KEY                      (gedcom_chunk_id),".
		"         KEY `##gedcom_chunk_ix1` (gedcom_id, imported),".
		" FOREIGN KEY `##gedcom_chunk_fk1` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	//WT_DB::exec(
	//	"CREATE TABLE IF NOT EXISTS `##language` (".
	//	" language_tag       VARCHAR(16)                      NOT NULL,".
	//	" iso15924_code      CHAR(4)                          NOT NULL,".
	//	" cldr_code          VARCHAR(16)                      NOT NULL,".
	//	" launchpad_code     VARCHAR(16)                      NOT NULL,".
	//	" collation          VARCHAR(16)                      NOT NULL,".
	//	" language_name      VARCHAR(64)                      NOT NULL,".
	//	" language_name_base VARCHAR(64)                      NOT NULL,".
	//	" enabled            ENUM ('yes', 'no') DEFAULT 'yes' NOT NULL,".
	//	" PRIMARY KEY        (language_tag),".
	//	" INDEX              (language_name_base, language_name)".
	//	") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	//);

	WT_DB::exec(
		"CREATE TABLE IF NOT EXISTS `##site_access_rule` (".
		" site_access_rule_id INTEGER          NOT NULL AUTO_INCREMENT,".
		" ip_address_start     INTEGER UNSIGNED NOT NULL DEFAULT 0,".
		" ip_address_end       INTEGER UNSIGNED NOT NULL DEFAULT 4294967295,".
		" user_agent_pattern   VARCHAR(255)     NOT NULL,".
		" rule                 ENUM('allow', 'deny', 'robot', 'unknown') NOT NULL DEFAULT 'unknown',".
		" comment              VARCHAR(255)     NOT NULL,".
		" updated              TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,".
		" PRIMARY KEY                          (site_access_rule_id),".
		" UNIQUE  KEY `##site_access_rule_ix1` (ip_address_end, ip_address_start, user_agent_pattern, rule),".
		"         KEY `##site_access_rule_ix2` (rule)".
		") ENGINE=InnoDB COLLATE=utf8_unicode_ci"
	);

	WT_DB::exec(
		"INSERT IGNORE INTO `##site_access_rule` (user_agent_pattern, rule, comment) VALUES".
		" ('Mozilla/5.0 (%) Gecko/% %/%', 'allow', 'Gecko-based browsers'),".
		" ('Mozilla/5.0 (%) AppleWebKit/% (KHTML, like Gecko)%', 'allow', 'WebKit-based browsers'),".
		" ('Opera/% (%) Presto/% Version/%', 'allow', 'Presto-based browsers'),".
		" ('Mozilla/% (compatible; MSIE %', 'allow', 'Trident-based browsers'),".
		" ('Mozilla/% (Windows%; Trident%; rv:%) like Gecko', 'allow', 'Modern Internet Explorer'),".
		" ('Mozilla/5.0 (compatible; Konqueror/%', 'allow', 'Konqueror browser')"
	);

	WT_DB::prepare(
		"INSERT IGNORE INTO `##gedcom` (gedcom_id, gedcom_name) VALUES ".
		" (-1, 'DEFAULT_TREE')"
	)->execute();

	WT_DB::prepare(
		"INSERT IGNORE INTO `##user` (user_id, user_name, real_name, email, password) VALUES ".
		" (-1, 'DEFAULT_USER', 'DEFAULT_USER', 'DEFAULT_USER', 'DEFAULT_USER'), (1, ?, ?, ?, ?)"
	)->execute(array(
		$_POST['wtuser'], $_POST['wtname'], $_POST['wtemail'], password_hash($_POST['wtpass'], PASSWORD_DEFAULT)
	));

	WT_DB::prepare(
		"INSERT IGNORE INTO `##user_setting` (user_id, setting_name, setting_value) VALUES ".
		" (1, 'canadmin',          ?),".
		" (1, 'language',          ?),".
		" (1, 'verified',          ?),".
		" (1, 'verified_by_admin', ?),".
		" (1, 'editaccount',       ?),".
		" (1, 'auto_accept',       ?),".
		" (1, 'visibleonline',     ?)"
	)->execute(array(
		1, WT_LOCALE, 1, 1, 1, 0, 1
	));

	WT_DB::prepare(
		"INSERT IGNORE INTO `##site_setting` (setting_name, setting_value) VALUES ".
		"('WT_SCHEMA_VERSION',               '-2'),".
		"('INDEX_DIRECTORY',                 'data/'),".
		"('USE_REGISTRATION_MODULE',         '1'),".
		"('REQUIRE_ADMIN_AUTH_REGISTRATION', '1'),".
		"('ALLOW_USER_THEMES',               '1'),".
		"('ALLOW_CHANGE_GEDCOM',             '1'),".
		"('SESSION_TIME',                    '7200'),".
		"('SMTP_ACTIVE',                     'internal'),".
		"('SMTP_HOST',                       'localhost'),".
		"('SMTP_PORT',                       '25'),".
		"('SMTP_AUTH',                       '1'),".
		"('SMTP_AUTH_USER',                  ''),".
		"('SMTP_AUTH_PASS',                  ''),".
		"('SMTP_SSL',                        'none'),".
		"('SMTP_HELO',                       ?),".
		"('SMTP_FROM_NAME',                  ?)"
	)->execute(array(
		$_SERVER['SERVER_NAME'], $_SERVER['SERVER_NAME']
	));

	// Search for all installed modules, and enable them.
	WT_Module::getInstalledModules('enabled');

	// Create the default settings for new users/family trees
	WT_DB::prepare(
		"INSERT INTO `##block` (user_id, location, block_order, module_name) VALUES (-1, 'main', 1, 'todays_events'), (-1, 'main', 2, 'user_messages'), (-1, 'main', 3, 'user_favorites'), (-1, 'side', 1, 'user_welcome'), (-1, 'side', 2, 'random_media'), (-1, 'side', 3, 'upcoming_events'), (-1, 'side', 4, 'logged_in')"
	)->execute();
	WT_DB::prepare(
		"INSERT INTO `##block` (gedcom_id, location, block_order, module_name) VALUES (-1, 'main', 1, 'gedcom_stats'), (-1, 'main', 2, 'gedcom_news'), (-1, 'main', 3, 'gedcom_favorites'), (-1, 'main', 4, 'review_changes'), (-1, 'side', 1, 'gedcom_block'), (-1, 'side', 2, 'random_media'), (-1, 'side', 3, 'todays_events'), (-1, 'side', 4, 'logged_in')"
	)->execute();
	// Create the blocks for the admin user
	WT_DB::prepare(
		"INSERT INTO `##block` (user_id, location, block_order, module_name)" .
		" SELECT 1, location, block_order, module_name" .
		" FROM `##block`" .
		" WHERE user_id=-1"
	)->execute();


	// Write the config file.  We already checked that this would work.
	$config_ini_php=
		'; <'.'?php exit; ?'.'> DO NOT DELETE THIS LINE'      . PHP_EOL.
		'dbhost="' . addcslashes($_POST['dbhost'], '"') . '"' . PHP_EOL.
		'dbport="' . addcslashes($_POST['dbport'], '"') . '"' . PHP_EOL.
		'dbuser="' . addcslashes($_POST['dbuser'], '"') . '"' . PHP_EOL.
		'dbpass="' . addcslashes($_POST['dbpass'], '"') . '"' . PHP_EOL.
		'dbname="' . addcslashes($_POST['dbname'], '"') . '"' . PHP_EOL.
		'tblpfx="' . addcslashes($_POST['tblpfx'], '"') . '"' . PHP_EOL;

	file_put_contents(WT_DATA_DIR . 'config.ini.php', $config_ini_php);

	// Done - start using webtrees
	echo
		'<script>document.location=document.location;</script>',
		'</form></body></html>';
	exit;
} catch (PDOException $ex) {
	echo
		'<p class="bad">', WT_I18N::translate('An unexpected database error occurred.'), '</p>',
		'<pre>', $ex->getMessage(), '</pre>',
		'<p class="info">', WT_I18N::translate('The webtrees developers would be very interested to learn about this error.  If you contact them, they will help you resolve the problem.'), '</p>';
}
echo '</form>';
echo '</body>';
echo '</html>';
