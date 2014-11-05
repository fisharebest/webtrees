<?php
// Site Unavailable
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

define('WT_SCRIPT_NAME', 'site-offline.php');

require 'library/autoload.php';

// This script does not load session.php.
// session.php won’t run until a configuration file and database connection exist...
// This next block of code is a minimal version of session.php
define('WT_WEBTREES', 'webtrees');
define('WT_SERVER_NAME', '');
define('WT_SCRIPT_PATH', '');
define('WT_ROOT', '');
define('WT_GED_ID', 0);
define('WT_DATA_DIR', realpath('data').DIRECTORY_SEPARATOR);

$WT_SESSION=new stdClass();
$WT_SESSION->locale='';

require 'includes/functions/functions.php';

define('WT_LOCALE', WT_I18N::init());

if (file_exists(WT_DATA_DIR.'offline.txt')) {
	$offline_txt=file_get_contents(WT_DATA_DIR.'offline.txt');
} else {
	// offline.txt has gone - we're back online!
	header('Location: index.php');
	exit;
}

header('Content-Type: text/html; charset=UTF-8');
header($_SERVER['SERVER_PROTOCOL'].' 503 Service Temporarily Unavailable');

echo
	'<!DOCTYPE html>',
	'<html ', WT_I18N::html_markup(), '>',
	'<head>',
	'<meta charset="UTF-8">',
	'<title>', WT_WEBTREES, '</title>',
	'<meta name="robots" content="noindex,follow">',
	'<style type="text/css">
		body {color: gray; background-color: white; font: 14px tahoma, arial, helvetica, sans-serif; padding:10px; }
		a {color: #81A9CB; font-weight: bold; text-decoration: none;}
		a:hover {text-decoration: underline;}
		h1 {color: #81A9CB; font-weight:normal; text-align:center;}
		li {line-height:2;}
		blockquote {color:red;}
		.content { /*margin:auto; width:800px;*/ border:1px solid gray; padding:15px; border-radius:15px;}
		.good {color: green;}
	</style>',
	'</head><body>',
	'<h1>', WT_I18N::translate('This website is temporarily unavailable'), '</h1>',
	'<div class="content"><p>';

if ($offline_txt) {
	echo $offline_txt;
} else {
	echo WT_I18N::translate('The site is down for maintenance.  You should <a href="index.php">try again</a> in a few minutes.');
}
echo '</p>';
echo '</div>';
echo '</body>';
echo '</html>';
