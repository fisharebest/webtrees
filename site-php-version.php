<?php
// PHP version error
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

define('WT_SCRIPT_NAME', 'site-php-version.php');

// This script does not load session.php.
// It may well invoke code that won’t run on PHP5.2…
// This next block of code is a minimal version of session.php
define('WT_WEBTREES', 'webtrees');
define('WT_ROOT', '');
define('WT_GED_ID', 0);
define('WT_USER_ID', 0);
define('WT_DATA_DIR', realpath('data').DIRECTORY_SEPARATOR);
define('WT_DEBUG_LANG', false); // The translation library needs this
$WT_SESSION=new stdClass();
$WT_SESSION->locale='';
// Invoke the Zend Framework Autoloader, so we can use Zend_XXXXX and WT_XXXXX classes
set_include_path(WT_ROOT.'library'.PATH_SEPARATOR.get_include_path());
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->registerNamespace('WT_');
require 'includes/functions/functions.php';
require WT_ROOT.'includes/functions/functions_utf-8.php';
define('WT_LOCALE', 'en');

if (version_compare(PHP_VERSION, '5.3.2', '>=')) {
	header('Location: index.php');
}

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html lang="en">
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
			.content {margin:auto; width:800px; border:1px solid gray; padding:15px; border-radius:15px;}
			.good {color: green;}
		</style>
	</head>
	<body>
		<h1>
			This website is temporarily unavailable
		</h1>
		<div class="content">
			<p>
				This version of webtrees cannot be installed on this web-server.
			</p>
			<p>
				You have the following options:
			</p>
			<ul>
				<li>Upgrade the web-server from PHP <?php echo PHP_VERSION; ?> to PHP 5.3 or higher.</li>
				<li>Install (or re-install) webtrees <a href="https://launchpad.net/webtrees/1.4/1.4.4/+download/webtrees-1.4.4.zip">1.4.4</a></li>
			</ul>
		</div>
	</body>
</html>
