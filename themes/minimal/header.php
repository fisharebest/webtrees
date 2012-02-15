<?php
// Header for Minimal theme
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

echo
	'<!DOCTYPE html>',
	'<html ', WT_I18N::html_markup(), '>',
	'<head>',
	'<meta charset="UTF-8">',
	'<title>', htmlspecialchars($title), '</title>',
	header_links($META_DESCRIPTION, $META_ROBOTS, $META_GENERATOR, $LINK_CANONICAL),
	'<link rel="icon" href="', WT_THEME_URL, 'favicon.png" type="image/png">',
	'<link rel="stylesheet" type="text/css" href="', WT_STATIC_URL, 'js/jquery/css/jquery-ui.custom.css">',
	'<link rel="stylesheet" type="text/css" href="', $stylesheet, '">';

switch ($BROWSERTYPE) {
//case 'chrome': uncomment when chrome.css file needs to be added, or add others as needed
case 'msie':
	echo '<link type="text/css" rel="stylesheet" href="', WT_THEME_URL, $BROWSERTYPE, '.css">';
	break;
}

// Additional css files required (Only if Lightbox installed)
if (WT_USE_LIGHTBOX) {
		echo '<link rel="stylesheet" type="text/css" href="', WT_STATIC_URL, WT_MODULES_DIR, 'lightbox/css/album_page.css" media="screen">';
}

echo
	'</head>',
	'<body id="body">';

// begin header section
if ($view!='simple') {
	echo 
		'<div id="header" class="', $TEXT_DIRECTION, '">',
		'<span class="title">',
			htmlspecialchars($GEDCOM_TITLE),
		'</span>',
		'<span class="hlogin">';
	if (WT_USER_ID) {
		echo '<a href="edituser.php" class="link">', WT_I18N::translate('Logged in as '), ' ', getUserFullName(WT_USER_ID), '</a> | ', logout_link();
	} else {
		echo login_link();
	}
	echo '</span>';
	echo '<span class="htheme">';
	$menu=WT_MenuBar::getThemeMenu();
	if ($menu) {
		echo $menu->getMenuAsDropdown();
	}
	$menu=WT_MenuBar::getLanguageMenu();
	if ($menu) {
		echo $menu->getMenuAsDropdown();
	}
	echo
		'</span>',
		'<div class="hsearch">',
		'<form style="display:inline;" action="search.php" method="get">',
		'<input type="hidden" name="action" value="general">',
		'<input type="hidden" name="topsearch" value="yes">',
		'<input type="text" name="query" size="15" placeholder="', WT_I18N::translate('Search'), '">',
		'<input type="submit" name="search" value=" &gt; ">',
		'</form>';
	print_favorite_selector();
	echo 
		'</div>';
	
	//  begin top links section
	echo 
		'<div id="topMenu">', 
		'<ul class="makeMenu">'; 

	$menu=WT_MenuBar::getGedcomMenu();
	if ($menu) {
		$menu->addIcon(null);
		echo $menu->getMenuAsList();
	}
	$menu=WT_MenuBar::getMyPageMenu();
	if ($menu) {
		echo $menu->getMenuAsList();
	}
	$menu=WT_MenuBar::getChartsMenu();
	if ($menu) {
		$menu->addIcon(null);
		echo $menu->getMenuAsList();
	}
	$menu=WT_MenuBar::getListsMenu();
	if ($menu) {
		$menu->addIcon(null);
		echo $menu->getMenuAsList();
	}
	$menu=WT_MenuBar::getCalendarMenu();
	if ($menu) {
		$menu->addIcon(null);
		echo $menu->getMenuAsList();
	}
	$menu=WT_MenuBar::getReportsMenu();
	if ($menu) {
		$menu->addIcon(null);
		echo $menu->getMenuAsList();
	}
	$menu=WT_MenuBar::getSearchMenu();
	if ($menu) {
		$menu->addIcon(null);
		echo $menu->getMenuAsList();
	}
	$menus=WT_MenuBar::getModuleMenus();
	foreach ($menus as $menu) {
		if ($menu) {
			$menu->addIcon(null);
		echo $menu->getMenuAsList();
		}
	}
	echo 
		'</ul>',
		'</div>'; //<div id="topMenu">
	// Display feedback from asynchronous actions
	echo '<div id="flash-messages">';
	foreach (Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages() as $message) {
		echo '<p class="ui-state-highlight">', $message, '</p>';
	}
	echo '</div>'; // <div id="flash-messages">
	echo '</div>'; // <div id="header">
}
echo $javascript, '<div id="content">';
