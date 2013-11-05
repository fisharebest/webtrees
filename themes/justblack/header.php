<?php
// Header for the JustBlack theme
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
// Copyright (C) 2013 JustCarmen.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
//
// $Id: header.php 2013-09-15 JustCarmen $

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$this
	// This theme uses the jQuery “colorbox” plugin to display images
	->addExternalJavascript(WT_JQUERY_COLORBOX_URL)
	->addExternalJavascript(WT_JQUERY_WHEELZOOM_URL)
	// JustBlack
	->addExternalJavascript(WT_THEME_JUSTBLACK.'justblack.js');
	
ob_start();	
echo
	'<!DOCTYPE html>',
	'<html ', WT_I18N::html_markup(), '>',
	'<head>';
	
echo
	'<meta charset="utf-8">',
	'<title>', htmlspecialchars($title), '</title>',
	header_links($META_DESCRIPTION, $META_ROBOTS, $META_GENERATOR, $LINK_CANONICAL),
	'<link rel="icon" href="', WT_CSS_URL, 'favicon.png" type="image/png">',
	'<link rel="stylesheet" type="text/css" href="', WT_THEME_URL, 'jquery-ui-1.10.3/jquery-ui-1.10.3.custom.css">',
	'<link rel="stylesheet" type="text/css" href="', WT_THEME_URL, 'colorbox-1.4.15/colorbox.css', '">',
	'<link rel="stylesheet" type="text/css" href="', WT_CSS_URL, 'style.css', '">';

switch ($BROWSERTYPE) {
case 'msie':
	echo '<link type="text/css" rel="stylesheet" href="', WT_CSS_URL, $BROWSERTYPE, '.css">';
	break;
}	

if ($view=='simple') {
	// Popup windows need space for the save/close buttons
	echo '<style>body{margin-bottom:50px;}</style>';
}

echo
	'</head>',
	'<body id="body">';

if ($view!='simple') { // no headers for dialogs
		getJBScriptVars();
		// begin header section
		echo getJBheader();	
		
		echo
		'<div id="optionsmenu">',		
			'<div id="fav-menu"><ul class="dropdown">', WT_MenuBar::getFavoritesMenu(), '</ul></div>',
			'<div id="search-menu">', getJBSearch(), '</div>', getJBFlags(),
		'</div>',
		'<div class="clearfloat"></div>';
	
		// Print the TopMenu
		echo 
		'<div id="topMenu">'.getJBTopMenu().'</div>',			
	    '<div class="divider"></div>'.
        WT_FlashMessages::getHtmlMessages(); // Feedback from asynchronous actions	
}

echo $javascript, '<div id="content">';