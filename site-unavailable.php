<?php
/**
 * Site Unavailable
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

define('WT_SCRIPT_NAME', 'site-unavailable.php');

// This script does not load session.php.
// session.php won't run until a configuration file and database connection exist...
// This next block of code is a minimal version of session.php
define('WT_WEBTREES', true);
define('WT_ROOT', '');
define('WT_GED_ID', 0);
set_include_path('library'.PATH_SEPARATOR.get_include_path());
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
require 'includes/functions/functions.php';
require 'includes/classes/class_i18n.php';
define('WT_LOCALE', i18n::init());

echo
	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
	'<html xmlns="http://www.w3.org/1999/xhtml" ', i18n::html_markup(), '>',
	'<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />',
	'<title>Site Unavailable - webtrees</title>',
	'<style type="text/css">
		body { 	color: gray; background-color: white; font: 14px tahoma, arial, helvetica, sans-serif;	padding:10px; }
		a {	color: #81A9CB; font-weight: bold; text-decoration: none;}
		a:hover {text-decoration: underline;}
		h1 {color: #81A9CB; font-weight:normal; text-align:center;}
		li {line-height:2;}
		blockquote {color:red;}
		.content { /*margin:auto; width:800px;*/ border:1px solid gray; padding:15px; -moz-border-radius:15px; -webkit-border-radius:15px;}
		.good {color: green;}
	</style>',
	'</head><body>',
	'<h1>', i18n::translate('<b>webtrees</b> site unavailable'), '</h1>',
	'<div class="content">',
	'<p>', i18n::translate('Oops!  The webserver is unable to connect to the database server.  It could be busy, undergoing maintenance, or simply broken.  You should <a href="index.php">try again</a> in a few minutes or contact the website administrator.'), '</p>';

$config_ini_php=parse_ini_file('data/config.ini.php');
if (is_array($config_ini_php) && array_key_exists('dbhost', $config_ini_php) && array_key_exists('dbport', $config_ini_php) && array_key_exists('dbuser', $config_ini_php) && array_key_exists('dbpass', $config_ini_php) && array_key_exists('dbname', $config_ini_php)) {
	try {
		$dbh=new PDO('mysql:host='.$config_ini_php['dbhost'].';port='.$config_ini_php['dbport'].';dbname='.$config_ini_php['dbname'], $config_ini_php['dbuser'], $config_ini_php['dbpass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_OBJ, PDO::ATTR_CASE=>PDO::CASE_LOWER, PDO::ATTR_AUTOCOMMIT=>true));
	} catch (PDOException $ex) {
		echo '<p>', i18n::translate('The database reported the following error message:'), '</p>';
		echo '<blockquote>', $ex->getMessage(), '</blockquote>';
	}
}

echo i18n::translate('If you are the website administrator, you should check that:');
echo '<ol>';
echo '<li>', i18n::translate('the database connection settings in the file <b>/data/config.ini.php</b> are still correct'), '</li>';
echo '<li>', i18n::translate('the directory <b>/data</b> and the file <b>/data/config.ini.php</b> have access permissions that allow the webserver to read them'), '</li>';
echo '<li>', i18n::translate('you can connect to the database using other applications, such as phpmyadmin'), '</li>';
echo '</ol>';
echo '<p class="good">', i18n::translate('If you cannot resolve the problem yourself, you can ask for help on the forums at <a href="http://webtrees.net">webtrees.net</a>'), '</p>';
echo '</div>';
echo '</body>';
echo '</html>';
