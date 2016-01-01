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

use PDO;
use PDOException;

define('WT_SCRIPT_NAME', 'site-unavailable.php');

// This script does not load session.php.
// session.php won’t run until a configuration file and database connection exist...
// This next block of code is a minimal version of session.php
define('WT_WEBTREES', 'webtrees');
define('WT_BASE_URL', '');
define('WT_ROOT', '');
define('WT_DATA_DIR', realpath('data') . DIRECTORY_SEPARATOR);
define('WT_MODULES_DIR', 'modules_v3/');

// No configuration file?  Start the setup wizard
if (!file_exists(WT_DATA_DIR . 'config.ini.php')) {
	header('Location: setup.php');

	return;
}

require 'vendor/autoload.php';

Session::start();

define('WT_LOCALE', I18N::init());

http_response_code(503);
header('Content-Type: text/html; charset=UTF-8');

// The page which redirected here may have provided an error message.
$messages = '';
foreach (FlashMessages::getMessages() as $message) {
	$messages .=
		'<blockquote>' . Filter::escapeHtml($message->text) . '</blockquote>';
}

// If we can't connect to the database at all, give the reason why
$config_ini_php = parse_ini_file('data/config.ini.php');
if (is_array($config_ini_php) && array_key_exists('dbhost', $config_ini_php) && array_key_exists('dbport', $config_ini_php) && array_key_exists('dbuser', $config_ini_php) && array_key_exists('dbpass', $config_ini_php) && array_key_exists('dbname', $config_ini_php)) {
	try {
		new PDO('mysql:host=' . $config_ini_php['dbhost'] . ';port=' . $config_ini_php['dbport'] . ';dbname=' . $config_ini_php['dbname'], $config_ini_php['dbuser'], $config_ini_php['dbpass'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_CASE => PDO::CASE_LOWER, PDO::ATTR_AUTOCOMMIT => true));
	} catch (PDOException $ex) {
		$messages .= '<p>' . I18N::translate('The database reported the following error message:') . '</p>';
		$messages .= '<blockquote>' . $ex->getMessage() . '</blockquote>';
	}
}

?>
<!DOCTYPE html>
<html <?php echo I18N::htmlAttributes(); ?>>
	<head>
		<meta charset="UTF-8">
		<title><?php echo WT_WEBTREES; ?></title>
		<meta name="robots" content="noindex,follow">
		<style type="text/css">
			body {color: gray; background-color: white; font: 14px tahoma, arial, helvetica, sans-serif; padding:10px; }
			a {color: #81A9CB; font-weight: bold; text-decoration: none;}
			a:hover {text-decoration: underline;}
			h1 {color: #81A9CB; font-weight:normal; text-align:center;}
			li {line-height:2;}
			blockquote {color:red;}
			.content { border:1px solid gray; padding:15px; margin: 15px; border-radius:15px;}
			.good {color: green;}
		</style>
	</head>
	<body>
		<h1><?php echo I18N::translate('This website is temporarily unavailable'); ?></h1>
		<div class="content">
			<p>
				<?php echo I18N::translate('Oops!  The webserver is unable to connect to the database server.  It could be busy, undergoing maintenance, or simply broken.  You should <a href="index.php">try again</a> in a few minutes or contact the website administrator.'); ?>
			</p>
			<?php echo $messages; ?>
			<?php echo I18N::translate('If you are the website administrator, you should check that:'); ?>
			<ol>
				<li>
					<?php echo /* I18N: [you should check that:] ... */ I18N::translate('the database connection settings in the file “/data/config.ini.php” are still correct'); ?>
				</li>
				<li>
					<?php echo /* I18N: [you should check that:] ... */ I18N::translate('the folder “/data” and the file “/data/config.ini.php” have access permissions that allow the webserver to read them'); ?>
				</li>
				<li>
					<?php echo /* I18N: [you should check that:] ... */ I18N::translate('you can connect to the database using other applications, such as phpmyadmin'); ?>
				</li>
			</ol>
			<p class="good">
				<?php echo I18N::translate('If you cannot resolve the problem yourself, you can ask for help on the forums at <a href="http://webtrees.net">webtrees.net</a>.'); ?>
			</p>
		</div>
	</body>
</html>
