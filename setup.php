<?php
/**
 * Setup Wizard
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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
 * @package webtrees
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'setup.php');
define('WT_DATA_DIR',    'data/');
define('WT_MEDIA_DIR',   'media/');
define('WT_CONFIG_FILE', 'config.ini.php');
define('WT_REQUIRED_MYSQL_VERSION', '5.0.13'); // For: prepared statements within stored procedures

// magic quotes were deprecated in PHP5.3.0 and removed in PHP6.0.0
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
	set_magic_quotes_runtime(0);
	// magic_quotes_gpc can't be disabled at run-time, so clean them up as necessary.
	if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() ||
		ini_get('magic_quotes_sybase') && strtolower(ini_get('magic_quotes_sybase'))!='off') {
		$in = array(&$_POST);
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

if (!empty($_POST['action']) && $_POST['action']=='download') {
	header('Content-Type: text/plain');
	header('Content-Disposition: attachment; filename="'.WT_CONFIG_FILE.'"');
	echo '; <?php exit; ?> DO NOT DELETE THIS LINE'."\r\n";
	echo 'dbhost="', addcslashes($_POST['dbhost'], '"').'"'."\r\n";
	echo 'dbport="', addcslashes($_POST['dbport'], '"').'"'."\r\n";
	echo 'dbuser="', addcslashes($_POST['dbuser'], '"').'"'."\r\n";
	echo 'dbpass="', addcslashes($_POST['dbpass'], '"').'"'."\r\n";
	echo 'dbname="', addcslashes($_POST['dbname'], '"').'"'."\r\n";
	echo 'tblpfx="', addcslashes($_POST['tblpfx'], '"').'"'."\r\n";
	exit;
}

if (version_compare(PHP_VERSION, '5.2')<0) {
	// Our translation system requires PHP 5.2, so we cannot translate this message :-(
	echo
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
		'<html xmlns="http://www.w3.org/1999/xhtml">',
		'<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />',
		'<title>webtrees setup wizard</title>',
		'<h1>Sorry, the setup wizard cannot start.</h1>',
		'<p>This server is running PHP version ', PHP_VERSION, '</p>',
		'<p><b>webtrees</b> requires PHP 5.2 or later.  PHP 5.3 is recommended.</p>';
	if (version_compare(PHP_VERSION, '5.0')<0) {
		echo '<p>Many servers offer both PHP4 and PHP5.  You may be able to change your default to PHP5 using a control panel or a configuration setting.</p>';
	}
	exit;
}

// This script (uniquely) does not load session.php.
// session.php won't run until a configuration file exists...
// This next block of code is a minimal version of session.php
define('WT_WEBTREES', true);
define('WT_ROOT', '');
define('WT_GED_ID', 0);
set_include_path('library'.PATH_SEPARATOR.get_include_path());
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
require 'includes/functions/functions.php';
require 'includes/functions/functions_edit.php';
require 'includes/classes/class_i18n.php';
define('WT_LOCALE', i18n::init());

echo
	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
	'<html xmlns="http://www.w3.org/1999/xhtml" ', i18n::html_markup(), '>',
	'<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />',
	'<title>webtrees setup wizard</title>',
	'<style type="text/css">
		body { 	color: black; background-color: white; font: 14px tahoma, arial, helvetica, sans-serif;	padding:10px; }
		a {	color: black; font-weight: normal; text-decoration: none;}
		a:hover {color: #81A9CB;}
		h1 {color: #81A9CB; font-weight:normal;}
		legend {color:#81A9CB; font-style: italic; font-weight:bold; padding: 0 5px 5px; align: top;}
		.good {color: green;}
		.bad {color: red; font-weight: bold;}
		.indifferent {color: blue;}
	</style>',
	'</head><body>',
	'<h1>', i18n::translate('Setup wizard for <b>webtrees</b>'), '</h1>';

if (file_exists(WT_DATA_DIR.WT_CONFIG_FILE)) {
	$error=false;
	$warning=false;
	echo '<p class="good">', i18n::translate('The configuration file has been successfully uploaded to the server.'), '</p>';
	echo '<p>', i18n::translate('Checking the access permissions...'), '</p>';
	if (!is_readable(WT_DATA_DIR)) {
	 	echo '<p class="bad">', i18n::translate('The directory <b>%s</b> does not have read permission.  You must change this.', WT_DATA_DIR), '</p>';
		$error=true;
	} elseif (!is_writable(WT_DATA_DIR)) {
		echo '<p class="bad">', i18n::translate('The directory <b>%s</b> does not have write permission.  You must change this.', WT_DATA_DIR), '</p>';
		$error=true;
	} else {
		echo '<p class="good">', i18n::translate('The directory <b>%s</b> has read-write permission.  Good.', WT_DATA_DIR), '</p>';
	}
	if (!is_writable(WT_MEDIA_DIR)) {
		echo '<p class="bad">', i18n::translate('The directory <b>%s</b> does not have write permission.  You must change this.', WT_MEDIA_DIR), '</p>';
		$error=true;
	} elseif (!is_readable(WT_MEDIA_DIR)) {
	 	echo '<p class="bad">', i18n::translate('The directory <b>%s</b> does not have read permission.  You must change this.', WT_MEDIA_DIR), '</p>';
		$error=true;
	} else {
		echo '<p class="good">', i18n::translate('The directory <b>%s</b> has read-write permission.  Good.', WT_MEDIA_DIR), '</p>';
	}
	if (!is_readable(WT_DATA_DIR.WT_CONFIG_FILE)) {
	 	echo '<p class="bad">', i18n::translate('The file <b>%s</b> does not have read permission.  You must change this.', WT_DATA_DIR.WT_CONFIG_FILE), '</p>';
		$error=true;
	} elseif (is_writable(WT_DATA_DIR.WT_CONFIG_FILE) && DIRECTORY_SEPARATOR=='/') {
		echo '<p class="indifferent">', i18n::translate('The file <b>%s</b> has write permission.  This will work, but for better security, you should make it read only.', WT_DATA_DIR.WT_CONFIG_FILE), '</p>';
		$warning=true;
	} else {
		echo '<p class="good">', i18n::translate('The file <b>%s</b> has read-only permission.  Good.', WT_DATA_DIR.WT_CONFIG_FILE), '</p>';
	}
	if ($error || $warning) {
		echo '<p><a href="setup.php"><button>', i18n::translate('Test the permissions again'), '</button></a></p>';
	}
	if (!$error) {
		echo '<p><a href="index.php"><button>', i18n::translate('Start using webtrees'), '</button></a></p>';
	}
	// The config file exists - do not go any further.
	// This is an important security feature, to protect existing installations.
	exit;
}

echo '<form name="config" action="', WT_SCRIPT_NAME, '" method="post" autocomplete="off">';
echo '<input type="hidden" name="lang" value="', WT_LOCALE, '">';

////////////////////////////////////////////////////////////////////////////////
// Step one - choose language and confirm server configuration
////////////////////////////////////////////////////////////////////////////////

if (empty($_POST['maxcpu']) || empty($_POST['maxmem'])) {
	echo
		'<p>', i18n::translate('Change language'), ' ',
		edit_field_language('change_lang', WT_LOCALE, 'onChange="parent.location=\''.WT_SCRIPT_NAME.'?lang=\'+this.value;">'),
		'</p>',
		'<h2>', i18n::translate('Checking server configuration'), '</h2>';
	$warnings=false;
	$errors=false;

	// Mandatory extensions
	foreach (array('pcre', 'pdo', 'pdo_mysql', 'session') as $extension) {
		if (!extension_loaded($extension)) {
			echo '<p class="bad">', i18n::translate('PHP extension "%s" is disabled.  You cannot install webtrees until this is enabled.  Please ask your server\'s administrator to enable it.', $extension), '</p>';
			$errors=true;
		}
	}
	// Recommended extensions
	foreach (array(
		'calendar'=>i18n::translate('jewish calendar'),
		'gd'      =>i18n::translate('creating thumbnails of images'),
		'dom'     =>i18n::translate('exporting data in xml format'),
		'xml'     =>i18n::translate('reporting'),
	) as $extension=>$features) {
		if (!extension_loaded($extension)) {
			echo '<p class="bad">', i18n::translate('PHP extension "%1$s" is disabled.  Without it, the following features will not work: %2$s.  Please ask your server\'s administrator to enable it.', $extension, $features), '</p>';
			$warnings=true;
		}
	}
	// Settings
	foreach (array(
		'file_uploads'=>i18n::translate('upload files'),
		'date.timezone'=>i18n::translate('correct date and time in logs and messages'),
	) as $setting=>$features) {
		if (!ini_get($setting)) {
			echo '<p class="bad">', i18n::translate('PHP setting "%1$s" is disabled. Without it, the following features will not work: %2$s.  Please ask your server\'s administrator to enable it.', $setting, $features), '</p>';
			$warnings=true;
		}
	}
	if (!$warnings && !$errors) {
		echo '<p class="good">', i18n::translate('The server configuration is OK.'), '</p>';
	}
	echo '<h2>', i18n::translate('Checking server capacity'), '</h2>';
	// Memory
	$mem=to_mb(ini_get('memory_limit'));
	$maxmem=$mem;
	if (!ini_get('safe_mode')) {
		for ($i=$mem+1; $i<=1024; ++$i) {
			ini_set('memory_limit', $i.'M');
			$newmem=to_mb(ini_get('memory_limit'));
			if ($newmem>$mem) {
				$maxmem=$newmem;
			} else {
				break;
			}
		}
	}
	// CPU
	$cpu=ini_get('max_execution_time');
	$maxcpu=$cpu;
	/* Commented out temporarily.  How can we reliably determine the "master value" instead of the "local value"?
	if (!ini_get('safe_mode')) {
		for ($i=$cpu+1; $i<=300; ++$i) {
			set_time_limit('max_execution_time', $i);
			$newcpu=ini_get('max_execution_time');
			if ($newcpu>$cpu) {
				$maxcpu=$newcpu;
			} else {
				break;
			}
		}
	}
 */
	echo
		'<p>',
		i18n::translate('The memory and CPU time requirements depend on the number of individuals in your family tree.'),
		'<br/>',
		i18n::translate('The following list shows typical requirements.'),
		'</p><p>',
		i18n::translate('Small systems (500 individuals): 16-32MB, 10-20 seconds'),
		'<br/>',
		i18n::translate('Medium systems (5000 individuals): 32-64MB, 20-40 seconds'),
		'<br/>',
		i18n::translate('Large systems (50000 individuals): 64-128MB, 40-80 seconds'),
		'</p><p class="good">',
		i18n::translate('This server\'s memory limit is %dMB and its CPU time limit is %d seconds.', $maxmem, $maxcpu),
		'</p>';

	if ($maxmem<32 || $maxcpu<20) {
		echo '<p class="bad">', i18n::translate('If you try to exceed these limits, you may experience server time-outs and blank pages.'), '</p>';
	}
	echo '<p>', i18n::translate('To increase these limits, you should contact your server\'s administrator.'), '</p>';
	if (!$errors) {
		echo '<input type="hidden" name="maxcpu" value="'.$maxcpu.'">';
		echo '<input type="hidden" name="maxmem" value="'.$maxmem.'">';
		echo '<br/><hr/><input type="submit" value="'.i18n::translate('Continue').'">';

	}
	echo '</form></body></html>';
	exit;
} else {
	// Copy these values through to the next step
	echo '<input type="hidden" name="maxcpu" value="'.$_POST['maxcpu'].'">';
	echo '<input type="hidden" name="maxmem" value="'.$_POST['maxmem'].'">';
	@ini_set('max_execution_time', $_POST['maxcpu']);
	@ini_set('memory_limit', $_POST['maxmem'].'M');
}

////////////////////////////////////////////////////////////////////////////////
// Step two - Database connection.
////////////////////////////////////////////////////////////////////////////////

if (empty($_POST['dbhost'])) $_POST['dbhost']='localhost';
if (empty($_POST['dbport'])) $_POST['dbport']='3306';
if (empty($_POST['dbuser'])) $_POST['dbuser']='';
if (empty($_POST['dbpass'])) $_POST['dbpass']='';

try {
	$db_version_ok=false;
	$dbh=new PDO('mysql:host='.$_POST['dbhost'].';port='.$_POST['dbport'], $_POST['dbuser'], $_POST['dbpass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_OBJ, PDO::ATTR_CASE=>PDO::CASE_LOWER, PDO::ATTR_AUTOCOMMIT=>true));
	$dbh->exec("SET NAMES 'utf8'");
	foreach ($dbh->query("SHOW VARIABLES LIKE 'VERSION'") as $row) {
		if (version_compare($row->value, WT_REQUIRED_MYSQL_VERSION, '<')) {
			echo '<p class="bad">', i18n::translate('This database is only running MySQL version %s.  You cannot install webtrees here.', $row->value), '</p>';
		} else {
			$db_version_ok=true;
		}
	}
} catch (PDOException $ex) {
	$dbh=null;
	if ($_POST['dbuser']) {
		// If we've supplied a login, then show the error
		echo
			'<p class="bad">', i18n::translate('Unable to connect using these settings.  Your server gave the following error.'), '</p>',
			'<pre>', $ex->getMessage(), '</pre>',
			'<p class="bad">', i18n::translate('Check the settings and try again.'), '</p>';
	}
}

if (empty($_POST['dbuser']) || !$dbh || !$db_version_ok) {
	echo
		'<h2>', i18n::translate('Connection to database server'), '</h2>',
		'<p>', i18n::translate('<b>webtrees</b> needs a MySQL database, version %s or later.', WT_REQUIRED_MYSQL_VERSION), '</p>',
		'<p>', i18n::translate('Your server\'s administrator will provide you with the connection details.'), '</p>',
		'<fieldset><legend>', i18n::translate('Database connection'), '</legend>',
		'<table border="0"><tr><td>',
		i18n::translate('Server name'), '</td><td>',
		'<input type="text" name="dbhost" value="', htmlspecialchars($_POST['dbhost']), '"></td><td>',
		i18n::translate('Most sites are configured to use localhost.  This means that your database runs on the same computer as your web server.'),
		'</td></tr><tr><td>',
		i18n::translate('Port number'), '</td><td>',
		'<input type="text" name="dbport" value="', htmlspecialchars($_POST['dbport']), '"></td><td>',
		i18n::translate('Most sites are configured to use the default value of 3306.'),
		'</td></tr><tr><td>',
		i18n::translate('Database user account'), '</td><td>',
		'<input type="text" name="dbuser" value="', htmlspecialchars($_POST['dbuser']), '"></td><td>',
		i18n::translate('This is case sensitive.'),
		'</td></tr><tr><td>',
		i18n::translate('Database password'), '</td><td>',
		'<input type="password" name="dbpass" value="', htmlspecialchars($_POST['dbpass']), '"></td><td>',
		i18n::translate('This is case sensitive.'),
		'</td></tr><tr><td>',
		'</td></tr></table>',
		'</fieldset>',
		'<br/><hr/><input type="submit" value="'.i18n::translate('Continue').'">',
		'</form>',
		"\n<script type=\"text/javascript\">\n//<![CDATA[\n",
		'document.config.dbuser.focus();',
		"\n//]]>\n</script>\n",
		'</body></html>';
		exit;
} else {
	// Copy these values through to the next step
	echo '<input type="hidden" name="dbhost" value="'.htmlspecialchars($_POST['dbhost']).'">';
	echo '<input type="hidden" name="dbport" value="'.htmlspecialchars($_POST['dbport']).'">';
	echo '<input type="hidden" name="dbuser" value="'.htmlspecialchars($_POST['dbuser']).'">';
	echo '<input type="hidden" name="dbpass" value="'.htmlspecialchars($_POST['dbpass']).'">';
}

////////////////////////////////////////////////////////////////////////////////
// Step three - Database connection.
////////////////////////////////////////////////////////////////////////////////

if (empty($_POST['dbname'])) $_POST['dbname']='';
if (empty($_POST['tblpfx'])) $_POST['tblpfx']='wt_';

$dbname_ok=false;
if ($_POST['dbname']) {
	try {
		// Try to create the database, if it does not exist.
		$dbh->exec('CREATE DATABASE IF NOT EXISTS `'.$_POST['dbname'].'` COLLATE utf8_unicode_ci');
	} catch (PDOException $ex) {
		// If we have no permission to do this, there's nothing helpful we can say.
		// We'll get a more helpful error message from the next test.
	}
	try {
		$dbh->exec('USE `'.addcslashes($_POST['dbname'], '`').'`');
		$dbname_ok=true;
	} catch (PDOException $ex) {
		echo
			'<p class="bad">', i18n::translate('Unable to connect using these settings.  Your server gave the following error.'), '</p>',
			'<pre>', $ex->getMessage(), '</pre>',
			'<p class="bad">', i18n::translate('Check the settings and try again.'), '</p>';
	}
}

// If the database exists, check whether it is already used by another application.
if ($dbname_ok) {
	try {
		// PhpGedView (4.2.3 and earlier) and many other applications have a USERS table.
		// webtrees has a USER table
		$dummy=$dbh->query("SELECT COUNT(*) FROM `".addcslashes($_POST['tblpfx'], '`')."users`");
		echo '<p class="bad">', i18n::translate('This database and table-prefix appear to be used by another application.  If you have an existing PhpGedView system, you should create a new webtrees system.  You can import your PhpGedView data and settings later.'), '</p>';
		$dbname_ok=false;
	} catch (PDOException $ex) {
		// Table not found?  Good!
	}
}
if ($dbname_ok) {
	try {
		// PhpGedView (4.2.4 and later) has a site_setting.site_setting_name column.
		// [We changed the column name in webtrees, so we can tell the difference!]
		$dummy=$dbh->query("SELECT site_setting_value FROM `".addcslashes($_POST['tblpfx'], '`')."site_setting` WHERE site_setting_name='PGV_SCHEMA_VERSION'");
		echo '<p class="bad">', i18n::translate('This database and table-prefix appear to be used by another application.  If you have an existing PhpGedView system, you should create a new webtrees system.  You can import your PhpGedView data and settings later.'), '</p>';
		$dbname_ok=false;
	} catch (PDOException $ex) {
		// Table/column not found?  Good!
	}
}

if (!$dbname_ok) {
	echo
		'<h2>', i18n::translate('Database and table names'), '</h2>',
		'<p>', i18n::translate('A database server can store many separate databases.  You need to select an existing database (created by your server\'s administrator) or create a new one (if your database user account has sufficient privileges).'), '</p>',
		'<fieldset><legend>', i18n::translate('Database name'), '</legend>',
		'<table border="0"><tr><td>',
		i18n::translate('Database name'), '</td><td>',
		'<input type="text" name="dbname" value="', htmlspecialchars($_POST['dbname']), '"></td><td>',
		 i18n::translate('This is case sensitive.'),
		'</td></tr><tr><td>',
		i18n::translate('Table prefix'), '</td><td>',
		'<input type="text" name="tblpfx" value="', htmlspecialchars($_POST['tblpfx']), '"></td><td>',
		i18n::translate('The prefix is optional, but recommended.  By giving the table names a unique prefix you can let several different applications share the same database. "wt_" is suggested, but can be anything you want.'),
		'</td></tr></table>',
		'</fieldset>',
		'<br/><hr/><input type="submit" value="'.i18n::translate('Continue').'">',
		'</form>',
		"\n<script type=\"text/javascript\">\n//<![CDATA[\n",
		'document.config.dbname.focus();',
		"\n//]]>\n</script>\n",
		'</body></html>';
		exit;
} else {
	// Copy these values through to the next step
	echo '<input type="hidden" name="dbname" value="'.htmlspecialchars($_POST['dbname']).'">';
	echo '<input type="hidden" name="tblpfx" value="'.htmlspecialchars($_POST['tblpfx']).'">';
}

////////////////////////////////////////////////////////////////////////////////
// Step four - site setup data
////////////////////////////////////////////////////////////////////////////////

if (empty($_POST['wtname'    ])) $_POST['wtname'    ]='';
if (empty($_POST['wtuser'    ])) $_POST['wtuser'    ]='';
if (empty($_POST['wtpass'    ])) $_POST['wtpass'    ]='';
if (empty($_POST['wtpass2'   ])) $_POST['wtpass2'   ]='';
if (empty($_POST['wtemail'   ])) $_POST['wtemail'   ]='';
if (empty($_POST['smtpuse'   ])) $_POST['smtpuse'   ]='internal';
if (empty($_POST['smtpserv'  ])) $_POST['smtpserv'  ]='localhost';
if (empty($_POST['smtpport'  ])) $_POST['smtpport'  ]='25';
if (empty($_POST['smtpusepw' ])) $_POST['smtpusepw' ]=1;
if (empty($_POST['smtpuser'  ])) $_POST['smtpuser'  ]='';
if (empty($_POST['smtppass'  ])) $_POST['smtppass'  ]='';
if (empty($_POST['smtpsmpl'  ])) $_POST['smtpsmpl'  ]=0;
if (empty($_POST['smtpsecure'])) $_POST['smtpsecure']='none';
if (empty($_POST['smtpfrom'  ])) $_POST['smtpfrom'  ]='webmaster@localhost';
if (empty($_POST['smtpsender'])) $_POST['smtpsender']=$_POST['smtpfrom'];

if (empty($_POST['wtname']) || empty($_POST['wtuser']) || strlen($_POST['wtpass'])<6 || strlen($_POST['wtpass2'])<6 || empty($_POST['wtemail']) || $_POST['wtpass']<>$_POST['wtpass2']) {
	if (strlen($_POST['wtpass'])>0 && strlen($_POST['wtpass'])<6) {
		echo '<p class="bad">', i18n::translate('The password needs to be at least six characters long.'), '</p>';
	} elseif ($_POST['wtpass']<>$_POST['wtpass2']) {
		echo '<p class="bad">', i18n::translate('The passwords do not match.'), '</p>';
	} elseif ((empty($_POST['wtname']) || empty($_POST['wtuser']) || empty($_POST['wtpass']) || empty($_POST['wtemail'])) && $_POST['wtname'].$_POST['wtuser'].$_POST['wtpass'].$_POST['wtemail']!='') {
		echo '<p class="bad">', i18n::translate('You must enter all the administrator account fields.'), '</p>';
	}
	echo
		'<h2>', i18n::translate('System settings'), '</h2>',
		'<h3>', i18n::translate('1. Administrator account'), '</h3>',
		'<p>', i18n::translate('You need to set up an administrator account.  This account can control all aspects of this <b>webtrees</b> installation.  Please choose a strong password.'), '</p>',
		'<fieldset><legend>', i18n::translate('Administrator account'), '</legend>',
		'<table border="0"><tr><td>',
		i18n::translate('Your name'), '</td><td>',
		'<input type="text" name="wtname" value="', htmlspecialchars($_POST['wtname']), '"></td><td>',
		i18n::translate('This is your real name, as you would like it displayed on screen.'),
		'</td></tr><tr><td>',
		i18n::translate('Login ID'), '</td><td>',
		'<input type="text" name="wtuser" value="', htmlspecialchars($_POST['wtuser']), '"></td><td>',
		i18n::translate('You will use this to login to webtrees.'),
		'</td></tr><tr><td>',
		i18n::translate('Password'), '</td><td>',
		'<input type="password" name="wtpass" value="', htmlspecialchars($_POST['wtpass']), '"></td><td>',
		i18n::translate('This must to be at least six characters.  It is case-sensitive.'),
		'</td></tr><tr><td>',
		'&nbsp;', '</td><td>',
		'<input type="password" name="wtpass2" value="', htmlspecialchars($_POST['wtpass2']), '"></td><td>',
		i18n::translate('Type your password again, to make sure you have typed it correctly.'),
		'</td></tr><tr><td>',
		i18n::translate('Email address'), '</td><td>',
		'<input type="text" name="wtemail" value="', htmlspecialchars($_POST['wtemail']), '"></td><td>',
		i18n::translate('This will be used to send you password reminders, site notifications and messages from other family members who register on your site.'),
		'</td></tr><tr><td>',
		'</td></tr></table>',
		'</fieldset>',
		'<br /><br />',
		'<h3>', i18n::translate('2. Email details'), '</h3>',
		'<p>', i18n::translate('<b>webtrees</b> needs to send emails, such as password reminders and site notifications.  To do this, it can use this server\'s built in PHP mail facility (which is not always available) or an external SMTP (mail-relay) service, for which you will need to provide the connection details.'), '</p>',
		'<p>', i18n::translate('To use a Google mail account, use the following settings: server=smtp.gmail.com, port=587, security=tls, username=xxxxx@gmail.com, password=[your gmail password]'), '</p>',
		'<p>', i18n::translate('If you do not know these settings, leave the default values.  They may work.  You can change them later.'), '</p>',
		'<fieldset><legend>', i18n::translate('SMTP mail server'), '</legend>',
		'<table border="0"><tr><td>',
		i18n::translate('Messages'), '</td><td>',
		'<select name="smtpuse" onchange="document.config.smtpserv.disabled=(this.value!=\'external\');document.config.smtpport.disabled=(this.value!=\'external\');document.config.smtpusepw.disabled=(this.value!=\'external\');document.config.smtpuser.disabled=(this.value!=\'external\');document.config.smtppass.disabled=(this.value!=\'external\');document.config.smtpsmpl.disabled=(this.value!=\'external\');document.config.smtpsecure.disabled=(this.value!=\'external\');document.config.smtpfrom.disabled=(this.value!=\'external\');document.config.smtpsender.disabled=(this.value!=\'external\');">',
		'<option value="internal" ',
		$_POST['smtpuse']=='internal' ? 'selected="selected"' : '',
		'>', i18n::translate('Use PHP mail to send messages'), '</option>',
		'<option value="external" ',
		$_POST['smtpuse']=='external' ? 'selected="selected"' : '',
		'>', i18n::translate('Use SMTP to send messages'), '</option>',
		'<option value="disabled" ',
		$_POST['smtpuse']=='disbled' ? 'selected="selected"' : '',
		'>', i18n::translate('Do not send messages'), '</option>',
		'</select></td><td>',
		i18n::translate('If you don\'t want to send mail, for example when running webtrees with a single user or on a standalone computer, you can disable this feature.'),
		'</td></tr><tr><td>',
		i18n::translate('Server'), '</td><td>',
		'<input type="text" name="smtpserv" value="', htmlspecialchars($_POST['smtpserv']), '"', $_POST['smtpuse']=='exernal' ? '' : 'disabled', ' /></td><td>',
		i18n::translate('This is the name of the SMTP server. \'localhost\' means that the mail service is running on the same computer as your web server.'),
		'</td></tr><tr><td>',
		i18n::translate('Port'), '</td><td>',
		'<input type="text" name="smtpport" value="', htmlspecialchars($_POST['smtpport']), '"', $_POST['smtpuse']=='exernal' ? '' : 'disabled', ' /></td><td>',
		i18n::translate('By default, SMTP works on port 25.'),
		'</td></tr><tr><td>',
		i18n::translate('Use password'), '</td><td>',
		'<select name="smtpusepw"', $_POST['smtpuse']=='exernal' ? '' : 'disabled', '>',
		'<option value="yes" ',
		$_POST['smtpusepw'] ? 'selected="selected"' : '',
		'>', i18n::translate('yes'), '</option>',
		'<option value="no" ',
		!$_POST['smtpusepw'] ? 'selected="selected"' : '',
		'>', i18n::translate('no'), '</option>',
		'</select></td><td>',
		i18n::translate('Most SMTP servers require a password.'),
		'</td></tr><tr><td>',
		i18n::translate('Username'), '</td><td>',
		'<input type="text" name="smtpuser" value="', htmlspecialchars($_POST['smtpuser']), '"', $_POST['smtpuse']=='exernal' ? '' : 'disabled', ' /></td><td>',
		'&nbsp;',
		'</td></tr><tr><td>',
		i18n::translate('Password'), '</td><td>',
		'<input type="password" name="smtppass" value="', htmlspecialchars($_POST['smtppass']), '"', $_POST['smtpuse']=='exernal' ? '' : 'disabled', ' /></td><td>',
		'&nbsp;',
		'</td></tr><tr><td>',
		i18n::translate('Use simple mail headers'), '</td><td>',
		'<select name="smtpsmpl"', $_POST['smtpuse']=='exernal' ? '' : 'disabled', '>',
		'<option value="yes" ',
		$_POST['smtpsmpl'] ? 'selected="selected"' : '',
		'>', i18n::translate('yes'), '</option>',
		'<option value="no" ',
		!$_POST['smtpsmpl'] ? 'selected="selected"' : '',
		'>', i18n::translate('no'), '</option>',
		'</select></td><td>',
		'</td></tr><tr><td>',
		i18n::translate('Security'), '</td><td>',
		'<select name="smtpsecure"', $_POST['smtpuse']=='exernal' ? '' : 'disabled', '>',
		'<option value="none" ',
		$_POST['smtpusepw']=='none' ? 'selected="selected"' : '',
		'>', i18n::translate('none'), '</option>',
		'<option value="tls" ',
		$_POST['smtpusepw']=='tls' ? 'selected="selected"' : '',
		'>', /* I18n: Transport Layer Security - a secure communications protocol */ i18n::translate('tls'), '</option>',
		'<option value="ssl" ',
		$_POST['smtpusepw']=='ssl' ? 'selected="selected"' : '',
		'>', /* I18n: Secure Sockets Layer - a secure communications protocol*/ i18n::translate('ssl'), '</option>',
		'</select></td><td>',
		i18n::translate('Most servers do not use secure connections.'),
		'</td></tr><tr><td>',
		i18n::translate('From email address'), '</td><td>',
		'<input type="text" name="smtpfrom" size="40" value="', htmlspecialchars($_POST['smtpfrom']), '"', $_POST['smtpuse']=='exernal' ? '' : 'disabled', ' /></td><td>',
		i18n::translate('This is used in the "From:" header when sending mails.'),
		'</td></tr><tr><td>',
		i18n::translate('Sender email address'), '</td><td>',
		'<input type="text" name="smtpsender" size="40" value="', htmlspecialchars($_POST['smtpsender']), '"', $_POST['smtpuse']=='exernal' ? '' : 'disabled', ' /></td><td>',
		i18n::translate('This is used in the "Sender:" header when sending mails.  It is often the same as the "From:" header.'),
		'</td></tr><tr><td>',
		'</td></tr></table>',
		'</fieldset>',
		'<br/><hr/><input type="submit" value="'.i18n::translate('Continue').'">',
		'</form>',
		"\n<script type=\"text/javascript\">\n//<![CDATA[\n",
		'document.config.wtname.focus();',
		"\n//]]>\n</script>\n",
		'</body></html>';
		exit;
} else {
	// Copy these values through to the next step
	echo '<input type="hidden" name="wtname"     value="'.htmlspecialchars($_POST['wtname']).'">';
	echo '<input type="hidden" name="wtuser"     value="'.htmlspecialchars($_POST['wtuser']).'">';
	echo '<input type="hidden" name="wtpass"     value="'.htmlspecialchars($_POST['wtpass']).'">';
	echo '<input type="hidden" name="wtpass2"    value="'.htmlspecialchars($_POST['wtpass2']).'">';
	echo '<input type="hidden" name="wtemail"    value="'.htmlspecialchars($_POST['wtemail']).'">';
	echo '<input type="hidden" name="smtpuse"    value="'.htmlspecialchars($_POST['smtpuse']).'">';
	echo '<input type="hidden" name="smtpserv"   value="'.htmlspecialchars($_POST['smtpserv']).'">';
	echo '<input type="hidden" name="smtpport"   value="'.htmlspecialchars($_POST['smtpport']).'">';
	echo '<input type="hidden" name="smtpusepw"  value="'.htmlspecialchars($_POST['smtpusepw']).'">';
	echo '<input type="hidden" name="smtpuser"   value="'.htmlspecialchars($_POST['smtpuser']).'">';
	echo '<input type="hidden" name="smtppass"   value="'.htmlspecialchars($_POST['smtppass']).'">';
	echo '<input type="hidden" name="smtpsecure" value="'.htmlspecialchars($_POST['smtpsecure']).'">';
	echo '<input type="hidden" name="smtpfrom"   value="'.htmlspecialchars($_POST['smtpfrom']).'">';
	echo '<input type="hidden" name="smtpsender" value="'.htmlspecialchars($_POST['smtpsender']).'">';
}

////////////////////////////////////////////////////////////////////////////////
// Step five - We have a database connection.  Create the tables.
////////////////////////////////////////////////////////////////////////////////

try {
	// These shouldn't fail.
	$TBLPREFIX=$_POST['tblpfx'];
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}gedcom (".
		" gedcom_id     INTEGER AUTO_INCREMENT                        NOT NULL,".
		" gedcom_name   VARCHAR(255)                                  NOT NULL,".
		" import_gedcom LONGBLOB                                      NOT NULL DEFAULT '',".
		" import_offset INTEGER UNSIGNED                              NOT NULL DEFAULT 1,".
		" PRIMARY KEY     (gedcom_id),".
		" UNIQUE  KEY ux1 (gedcom_name)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}site_setting (".
		" setting_name  VARCHAR(32)  NOT NULL,".
		" setting_value VARCHAR(255) NOT NULL,".
		" PRIMARY KEY (setting_name)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}gedcom_setting (".
		" gedcom_id     INTEGER      NOT NULL,".
		" setting_name  VARCHAR(32)  NOT NULL,".
		" setting_value VARCHAR(255) NOT NULL,".
		" PRIMARY KEY     (gedcom_id, setting_name),".
		" FOREIGN KEY fk1 (gedcom_id) REFERENCES {$TBLPREFIX}gedcom (gedcom_id) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}user (".
		" user_id   INTEGER AUTO_INCREMENT NOT NULL,".
		" user_name VARCHAR(32)            NOT NULL,".
		" real_name VARCHAR(64)            NOT NULL,".
		" email     VARCHAR(64)            NOT NULL,".
		" password  VARCHAR(64)            NOT NULL,".
		" PRIMARY KEY     (user_id),".
		" UNIQUE  KEY ux1 (user_name),".
		" UNIQUE  KEY ux2 (email)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}user_setting (".
		" user_id       INTEGER      NOT NULL,".
		" setting_name  VARCHAR(32)  NOT NULL,".
		" setting_value VARCHAR(255) NOT NULL,".
		" PRIMARY KEY     (user_id, setting_name),".
		" FOREIGN KEY fk1 (user_id) REFERENCES {$TBLPREFIX}user (user_id) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}user_gedcom_setting (".
		" user_id       INTEGER      NOT NULL,".
		" gedcom_id     INTEGER      NOT NULL,".
		" setting_name  VARCHAR(32)  NOT NULL,".
		" setting_value VARCHAR(255) NOT NULL,".
		" PRIMARY KEY     (user_id, gedcom_id, setting_name),".
		" FOREIGN KEY fk1 (user_id)   REFERENCES {$TBLPREFIX}user   (user_id)   /* ON DELETE CASCADE */,".
		" FOREIGN KEY fk2 (gedcom_id) REFERENCES {$TBLPREFIX}gedcom (gedcom_id) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}log (".
		" log_id      INTEGER AUTO_INCREMENT NOT NULL,".
		" log_time    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,".
		" log_type    ENUM('auth', 'change', 'config', 'debug', 'edit', 'error', 'media', 'search') NOT NULL,".
		" log_message TEXT         NOT NULL,".
		" ip_address  VARCHAR(40)  NOT NULL,".
		" user_id     INTEGER          NULL,".
		" gedcom_id   INTEGER          NULL,".
		" PRIMARY KEY     (log_id),".
		"         KEY ix1 (log_time),".
		"         KEY ix2 (log_type),".
		"         KEY ix3 (ip_address),".
		" FOREIGN KEY fk1 (user_id)   REFERENCES {$TBLPREFIX}user   (user_id)   /* ON DELETE SET NULL */,".
		" FOREIGN KEY fk2 (gedcom_id) REFERENCES {$TBLPREFIX}gedcom (gedcom_id) /* ON DELETE SET NULL */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}change (".
		" change_id      INTEGER AUTO_INCREMENT                  NOT NULL,".
		" change_time    TIMESTAMP                               NOT NULL DEFAULT CURRENT_TIMESTAMP,".
		" status         ENUM('accepted', 'pending', 'rejected') NOT NULL DEFAULT 'pending',".
		" gedcom_id      INTEGER                                 NOT NULL,".
		" xref           VARCHAR(20)                             NOT NULL,".
		" old_gedcom     LONGTEXT                                NOT NULL,".
		" new_gedcom     LONGTEXT                                NOT NULL,".
		" user_id        INTEGER                                 NOT NULL,".
		" PRIMARY KEY     (change_id),".
		"         KEY ix1 (gedcom_id, status, xref),".
		" FOREIGN KEY fk1 (user_id)   REFERENCES {$TBLPREFIX}user   (user_id)   /* ON DELETE RESTRICT */,".
		" FOREIGN KEY fk2 (gedcom_id) REFERENCES {$TBLPREFIX}gedcom (gedcom_id) /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}messages (".
		" m_id      INTEGER AUTO_INCREMENT NOT NULL,".
		" m_from    VARCHAR(255)           NOT NULL,".
		" m_to      VARCHAR(32)            NOT NULL,". // TODO: user_id
		" m_subject VARCHAR(255)           NOT NULL,".
		" m_body    TEXT                   NOT NULL,".
		" m_created VARCHAR(255)           NOT NULL,". // TODO: timestamp
		" PRIMARY KEY     (m_id),".
		"         KEY ix1 (m_to)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}favorites (".
		" fv_id       INTEGER AUTO_INCREMENT NOT NULL,".
	 	" fv_username VARCHAR(32)            NOT NULL,". // TODO user_id, ged_id
		" fv_gid      VARCHAR(20)                NULL,".
		" fv_type     VARCHAR(15)                NULL,".
		" fv_file     VARCHAR(100)               NULL,".
		" fv_url      VARCHAR(255)               NULL,".
	 	" fv_title    VARCHAR(255)               NULL,".
		" fv_note     TEXT                       NULL,".
		" PRIMARY KEY (fv_id),".
		"         KEY ix1 (fv_username)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}blocks (".
		" b_id       INTEGER AUTO_INCREMENT NOT NULL,".
		" b_username VARCHAR(100)           NOT NULL,".
		" b_location VARCHAR(30)            NOT NULL,".
	 	" b_order    INTEGER                NOT NULL,".
		" b_name     VARCHAR(255)           NOT NULL,".
		" b_config   TEXT                   NOT NULL,".
		" PRIMARY KEY (b_id),".
		"         KEY ix1 (b_username)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}news (".
		" n_id       INTEGER AUTO_INCREMENT NOT NULL,".
		" n_username VARCHAR(100)           NOT NULL,".
		" n_date     INTEGER                NOT NULL,".
		" n_title    VARCHAR(255)           NOT NULL,".
		" n_text     TEXT                   NOT NULL,".
		" PRIMARY KEY     (n_id),".
		"         KEY ix1 (n_username)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}individuals (".
		" i_id     VARCHAR(20)         NOT NULL,".
		" i_file   INTEGER             NOT NULL,".
		" i_rin    VARCHAR(20)         NOT NULL,".
		" i_isdead INTEGER             NOT NULL,".
		" i_sex    ENUM('U', 'M', 'F') NOT NULL,".
		" i_gedcom LONGTEXT            NOT NULL,".
		" PRIMARY KEY     (i_id, i_file),".
		" UNIQUE  KEY ux1 (i_file, i_id)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}families (".
		" f_id      VARCHAR(20)  NOT NULL,".
		" f_file    INTEGER      NOT NULL,".
		" f_husb    VARCHAR(20)      NULL,".
		" f_wife    VARCHAR(20)      NULL,".
		" f_chil    TEXT             NULL,".
		" f_gedcom  LONGTEXT     NOT NULL,".
		" f_numchil INTEGER      NOT NULL,".
		" PRIMARY KEY     (f_id, f_file),".
		" UNIQUE  KEY ux1 (f_file, f_id),".
		"         KEY ix1 (f_husb),".
		"         KEY ix2 (f_wife)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}places (".
		" p_id          INTEGER AUTO_INCREMENT NOT NULL,".
		" p_place       VARCHAR(150)               NULL,".
		" p_level       INTEGER                    NULL,".
		" p_parent_id   INTEGER                    NULL,".
		" p_file        INTEGER               NOT  NULL,".
		" p_std_soundex TEXT                       NULL,".
		" p_dm_soundex  TEXT                       NULL,".
		" PRIMARY KEY     (p_id),".
		"         KEY ix1 (p_place),".
		"         KEY ix2 (p_level),".
		"         KEY ix3 (p_parent_id),".
		"         KEY ix4 (p_file)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}placelinks (".
		" pl_p_id INTEGER NOT NULL,".
		" pl_gid  VARCHAR(20)  NOT NULL,".
		" pl_file INTEGER  NOT NULL,".
		" PRIMARY KEY (pl_p_id, pl_gid, pl_file),".
		"         KEY ix1 (pl_p_id),".
		"         KEY ix2 (pl_gid),".
		"         KEY ix3 (pl_file)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}dates (".
		" d_day        TINYINT     NOT NULL,".
		" d_month      CHAR(5)         NULL,".
		" d_mon        TINYINT     NOT NULL,".
		" d_year       SMALLINT    NOT NULL,".
		" d_julianday1 MEDIUMINT   NOT NULL,".
		" d_julianday2 MEDIUMINT   NOT NULL,".
		" d_fact       VARCHAR(15) NOT NULL,".
		" d_gid        VARCHAR(20) NOT NULL,".
		" d_file       INTEGER     NOT NULL,".
		" d_type       ENUM ('@#DGREGORIAN@', '@#DJULIAN@', '@#DHEBREW@', '@#DFRENCH R@', '@#DHIJRI@', '@#DROMAN@') NOT NULL,".
		" KEY ix1 (d_day),".
		" KEY ix2 (d_month),".
		" KEY ix3 (d_mon),".
		" KEY ix4 (d_year),".
		" KEY ix5 (d_julianday1),".
		" KEY ix6 (d_julianday2),".
		" KEY ix7 (d_gid),".
		" KEY ix8 (d_file),".
		" KEY ix9 (d_type),".
		" KEY ix10 (d_fact, d_gid)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}media (".
		" m_id      INTEGER AUTO_INCREMENT NOT NULL,".
		" m_media   VARCHAR(20)            NOT NULL,".
		" m_ext     VARCHAR(6)                 NULL,".
		" m_titl    VARCHAR(255)               NULL,".
		" m_file    VARCHAR(255)               NULL,".
		" m_gedfile INTEGER                NOT NULL,".
		" m_gedrec  LONGTEXT                   NULL,".
		" PRIMARY KEY (m_id),".
		"         KEY ix1 (m_media, m_gedfile)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}remotelinks (".
		" r_gid    VARCHAR(20)  NOT NULL,".
		" r_linkid VARCHAR(255)     NULL,".
		" r_file   INTEGER      NOT NULL,".
		" KEY ix1 (r_gid),".
		" KEY ix2 (r_linkid),".
		" KEY ix3 (r_file)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}media_mapping (".
		" mm_id      INTEGER     NOT NULL,".
		" mm_media   VARCHAR(20) NOT NULL,".
		" mm_gid     VARCHAR(20) NOT NULL,".
		" mm_order   INTEGER     NOT NULL DEFAULT '0',".
		" mm_gedfile INTEGER     NOT NULL,".
		" mm_gedrec  LONGTEXT    NOT NULL,".
		" PRIMARY KEY (mm_id),".
		"         KEY ix1 (mm_media, mm_gedfile),".
		"         KEY ix2 (mm_gid, mm_gedfile),".
		"         KEY ix3 (mm_gedfile)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}nextid (".
		" ni_id      INTEGER     NOT NULL,". // TODO: use auto-increment columns
		" ni_type    VARCHAR(15) NOT NULL,".
		" ni_gedfile INTEGER     NOT NULL,".
		" PRIMARY KEY (ni_type, ni_gedfile)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}other (".
		" o_id     VARCHAR(20) NOT NULL,".
		" o_file   INTEGER     NOT NULL,".
		" o_type   VARCHAR(15) NOT NULL,".
		" o_gedcom LONGTEXT        NULL,".
		" PRIMARY KEY     (o_id, o_file),".
		" UNIQUE  KEY ux1 (o_file, o_id)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}sources (".
		" s_id     VARCHAR(20)    NOT NULL,".
		" s_file   INTEGER        NOT NULL,".
		" s_name   VARCHAR(255)   NOT NULL,".
		" s_gedcom LONGTEXT       NOT NULL,".
		" s_dbid   ENUM('N', 'Y')     NULL,".
		" PRIMARY KEY     (s_id, s_file),".
		" UNIQUE  KEY ux1 (s_file, s_id),".
		"         KEY ix1 (s_name),".
		"         KEY ix2 (s_dbid)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}link (".
		" l_file    INTEGER     NOT NULL,".
		" l_from    VARCHAR(20) NOT NULL,".
		" l_type    VARCHAR(15) NOT NULL,".
		" l_to      VARCHAR(20) NOT NULL,".
		" PRIMARY KEY      (l_from, l_file, l_type, l_to),".
		" UNIQUE INDEX ux1 (l_to, l_file, l_type, l_from)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}name (".
		" n_file             INTEGER      NOT NULL,".
		" n_id               VARCHAR(20)  NOT NULL,".
		" n_num              INTEGER      NOT NULL,".
		" n_type             VARCHAR(15)  NOT NULL,".
		" n_sort             VARCHAR(255) NOT NULL,". // e.g. "GOGH,VINCENT WILLEM"
		" n_full             VARCHAR(255) NOT NULL,". // e.g. "Vincent Willem van GOGH"
		" n_list             VARCHAR(255) NOT NULL,". // e.g. "van GOGH, Vincent Willem"
		// These fields are only used for INDI records
		" n_surname          VARCHAR(255)     NULL,". // e.g. "van GOGH"
		" n_surn             VARCHAR(255)     NULL,". // e.g. "GOGH"
		" n_givn             VARCHAR(255)     NULL,". // e.g. "Vincent Willem"
		" n_soundex_givn_std VARCHAR(255)     NULL,".
		" n_soundex_surn_std VARCHAR(255)     NULL,".
		" n_soundex_givn_dm  VARCHAR(255)     NULL,".
		" n_soundex_surn_dm  VARCHAR(255)     NULL,".
		" PRIMARY KEY (n_id, n_file, n_num),".
		"         KEY ix1 (n_full, n_id, n_file),".
		"         KEY ix2 (n_file, n_surn)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}module (".
		" module_name   VARCHAR(32)                 NOT NULL,".
		" status        ENUM('enabled', 'disabled') NOT NULL DEFAULT 'enabled',".
		" tab_order     TINYINT                     NULL, ".
		" menu_order    TINYINT                     NULL, ".
		" sidebar_order TINYINT                     NULL,".
		" PRIMARY KEY (module_name)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}module_privacy (".
		" module_name   VARCHAR(32)                    NOT NULL,".
		" gedcom_id     INTEGER                        NOT NULL,".
		" component     ENUM('menu', 'sidebar', 'tab') NOT NULL,".
		" access_level  TINYINT                        NOT NULL,".
		" PRIMARY KEY     (module_name, gedcom_id, component),".
		" FOREIGN KEY fk1 (module_name) REFERENCES {$TBLPREFIX}module (module_name) /* ON DELETE CASCADE */,".
		" FOREIGN KEY fk2 (gedcom_id  ) REFERENCES {$TBLPREFIX}gedcom (gedcom_id)   /* ON DELETE CASCADE */".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}hit_counter (".
		" gedcom_id      INTEGER     NOT NULL,".
		" page_name      VARCHAR(32) NOT NULL,".
		" page_parameter VARCHAR(32) NOT NULL,".
		" page_count     INTEGER     NOT NULL,".
		" PRIMARY KEY (gedcom_id, page_name, page_parameter)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);
	$dbh->exec(
		"CREATE TABLE IF NOT EXISTS {$TBLPREFIX}ip_address (".
		" ip_address VARCHAR(40)                                NOT NULL,". // long enough for IPv6
		" category   ENUM('banned', 'search-engine', 'allowed') NOT NULL,".
		" comment    VARCHAR(255)                               NOT NULL,".
		" PRIMARY KEY (ip_address)".
		") COLLATE utf8_unicode_ci ENGINE=InnoDB"
	);

	$dbh->exec(
		"INSERT IGNORE INTO {$TBLPREFIX}user (user_id, user_name, real_name, email, password) VALUES ".
		" (1, '".addcslashes($_POST['wtuser'], "'")."', '".addcslashes($_POST['wtname'], "'")."', '".addcslashes($_POST['wtemail'], "'")."', '".crypt($_POST['wtpass'])."')"
	);
	$dbh->exec(
		"INSERT IGNORE INTO {$TBLPREFIX}user_setting (user_id, setting_name, setting_value) VALUES ".
		" (1, 'canadmin', 'Y')"
	);
	$dbh->exec(
		"INSERT IGNORE INTO {$TBLPREFIX}user_setting (user_id, setting_name, setting_value) VALUES ".
		" (1, 'language', '".Zend_Registry::get('Zend_Locale')."')"
	);
	$dbh->exec(
		"INSERT IGNORE INTO {$TBLPREFIX}user_setting (user_id, setting_name, setting_value) VALUES ".
		" (1, 'verified', 'yes')"
	);
	$dbh->exec(
		"INSERT IGNORE INTO {$TBLPREFIX}user_setting (user_id, setting_name, setting_value) VALUES ".
		" (1, 'verified_by_admin', 'yes')"
	);
	$dbh->exec(
		"INSERT IGNORE INTO {$TBLPREFIX}user_setting (user_id, setting_name, setting_value) VALUES ".
		" (1, 'editaccount', 'Y')"
	);
	$dbh->exec(
		"INSERT IGNORE INTO {$TBLPREFIX}user_setting (user_id, setting_name, setting_value) VALUES ".
		" (1, 'auto_accept', 'N')"
	);
	$dbh->exec(
		"INSERT IGNORE INTO {$TBLPREFIX}user_setting (user_id, setting_name, setting_value) VALUES ".
		" (1, 'visibleonline', 'Y')"
	);
	$dbh->exec(
		"INSERT IGNORE INTO {$TBLPREFIX}site_setting (setting_name, setting_value) VALUES ".
		"('WT_SCHEMA_VERSION',               '1'),".
		"('INDEX_DIRECTORY',                 'data/'),".
		"('AUTHENTICATION_MODULE',           'includes/authentication.php'),".
		"('STORE_MESSAGES',                  '1'),".
		"('USE_REGISTRATION_MODULE',         '1'),".
		"('REQUIRE_ADMIN_AUTH_REGISTRATION', '1'),".
		"('ALLOW_USER_THEMES',               '1'),".
		"('ALLOW_CHANGE_GEDCOM',             '1'),".
		"('SESSION_SAVE_PATH',               ''),".
		"('SESSION_TIME',                    '7200'),".
		"('SERVER_URL',                      ''),".
		"('LOGIN_URL',                       'login.php'),".
		"('MAX_VIEWS',                       '20'),".
		"('MAX_VIEW_TIME',                   '1'),".
		"('MEMORY_LIMIT',                    '".addcslashes($_POST['maxmem'], "'")."M'),".
		"('MAX_EXECUTION_TIME',              '".addcslashes($_POST['maxcpu'], "'")."'),".
		"('SMTP_ACTIVE',                     '".addcslashes($_POST['smtpuse'], "'")."'),".
		"('SMTP_HOST',                       '".addcslashes($_POST['smtpserv'], "'")."'),".
		"('SMTP_HELO',                       '".addcslashes($_POST['smtpsender'], "'")."'),".
		"('SMTP_PORT',                       '".addcslashes($_POST['smtpport'], "'")."'),".
		"('SMTP_AUTH',                       '".($_POST['smtpusepw']==1)."'),".
		"('SMTP_AUTH_USER',                  '".addcslashes($_POST['smtpuser'], "'")."'),".
		"('SMTP_AUTH_PASS',                  '".addcslashes($_POST['smtppass'], "'")."'),".
		"('SMTP_SSL',                        '".addcslashes($_POST['smtpsecure'], "'")."'),".
		"('SMTP_SIMPLE_MAIL',                '".addcslashes($_POST['smtpsmpl'], "'")."'),".
		"('SMTP_FROM_NAME',                  '".addcslashes($_POST['smtpfrom'], "'")."')"
	);
	echo
		'<p>', i18n::translate('Your system is almost ready for use.  The final step is to download a configuration file <b>%1$s</b> and copy this to the <b>%2$s</b> directory on your webserver.  This is a security measure to ensure only the website\'s owner can configure it.', WT_CONFIG_FILE, realpath(WT_DATA_DIR)), '</p>';
	if (DIRECTORY_SEPARATOR=='/') {
		// These hints only apply to UNIX based servers.
		// For Windows/XAMPP, defaults are fine
		// For Windows/IIS, the defaults are probably fine.  Anyone confirm?
		echo
			'<p>', i18n::translate('You should set the directory <b>%s</b> so that the webserver has read-write access.<br/>This normally means setting the permissions to "777" or "drwxrwxrwx".', WT_DATA_DIR), '</p>',
			'<p>', i18n::translate('You should set the file <b>%s</b> so that the webserver has read-only access.<br/>This normally means setting the permissions to "444" or "-r--r--r--".', WT_DATA_DIR.WT_CONFIG_FILE), '</p>',
			'<p>', i18n::translate('<b>webtrees</b> will check the permissions in the next step.'), '</p>';
	}
	echo
		'<input type="hidden" name="action" value="download">',
		'<input type="submit" value="'. /* I18N: %s is a filename */ i18n::translate('Download %s', WT_CONFIG_FILE).'" onclick="document.continue.continue.disabled=false; return true;">',
		'</form>',
		'<p>', i18n::translate('After you have copied this file to the webserver and set the access permissions, click here to continue'), '</p>',
		'<form name="continue" action="', WT_SCRIPT_NAME, '" method="get" onsubmit="alert(\'', i18n::translate('Reminder: you must copy %s to your webserver', WT_CONFIG_FILE), '\');return true;">',
		'<input type="submit" name="continue" value="'.i18n::translate('Continue').'" disabled>',
		'</form></body></html>';
} catch (PDOException $ex) {
	echo
		'<p class="bad">', i18n::translate('An unexpected database error occured.'), '</p>',
		'<pre>', $ex->getMessage(), '</pre>',
		'<p class="indifferent">', i18n::translate('The webtrees developers would be very interested to learn about this error.  If you contact them, they will help you resolve the problem.'), '</p>';
}
echo '</form>';
echo '</body>';
echo '</html>';

function to_mb($str) {
	if (substr($str, -1, 1)=='K') {
		return floor(substr($str, 0, strlen($str)-1)/1024);
	}
	if (substr($str, -1, 1)=='M') {
		return floor(substr($str, 0, strlen($str)-1));
	}
	if (substr($str, -1, 1)=='G') {
		return floor(1024*substr($str, 0, strlen($str)-1));
	}
	
}
