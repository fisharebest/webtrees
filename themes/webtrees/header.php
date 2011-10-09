<?php
// Header for webtrees theme
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
	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
	'<html xmlns="http://www.w3.org/1999/xhtml" ', WT_I18N::html_markup(), '>',
	'<head>',
	'<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />',
	'<title>', htmlspecialchars($title), '</title>',
	header_links($META_DESCRIPTION, $META_ROBOTS, $META_GENERATOR, $LINK_CANONICAL),
	'<link rel="shortcut icon" href="', WT_THEME_URL, 'favicon.ico" type="image/x-icon" />',
	'<link rel="stylesheet" type="text/css" href="', WT_STATIC_URL, 'js/jquery/css/jquery-ui.custom.css" />',
	'<link rel="stylesheet" type="text/css" href="', $stylesheet, '" />';

switch ($BROWSERTYPE) {
//case 'chrome': uncomment when chrome.css file needs to be added, or add others as needed
case 'msie':
	echo '<link type="text/css" rel="stylesheet" href="', WT_THEME_URL, $BROWSERTYPE, '.css" />';
	break;
}

// Additional css files required (Only if Lightbox installed)
if (WT_USE_LIGHTBOX) {
	if ($TEXT_DIRECTION=='rtl') {
		echo '<link rel="stylesheet" type="text/css" href="', WT_STATIC_URL, WT_MODULES_DIR, 'lightbox/css/clearbox_music_RTL.css" />';
		echo '<link rel="stylesheet" type="text/css" href="', WT_STATIC_URL, WT_MODULES_DIR, 'lightbox/css/album_page_RTL_ff.css" media="screen" />';
	} else {
		echo '<link rel="stylesheet" type="text/css" href="', WT_STATIC_URL, WT_MODULES_DIR, 'lightbox/css/clearbox_music.css" />';
		echo '<link rel="stylesheet" type="text/css" href="', WT_STATIC_URL, WT_MODULES_DIR, 'lightbox/css/album_page.css" media="screen" />';
	}
}

echo
	'<link rel="stylesheet" type="text/css" href="', WT_THEME_URL, 'modules.css" />',
	$javascript,
	'</head>',
	'<body id="body">';

// begin header section
if ($view!='simple') {
	echo
		'<div id="header">',
		'<div class="header_img"><img src="', WT_THEME_URL, 'images/webtrees.png" width="242" height="50" alt="', WT_WEBTREES, '" /></div>';
	if ($SEARCH_SPIDER) {
		// Search engines get a reduced menu
		$menu_items=array(
			WT_MenuBar::getGedcomMenu(),
			WT_MenuBar::getListsMenu(),
			WT_MenuBar::getCalendarMenu()
		);
	} else {
		// Options for real users
		echo '<ul id="extra-menu" class="makeMenu">';
		if (WT_USER_ID) {
			echo '<li><a href="edituser.php">', WT_I18N::translate('Logged in as '), ' ', getUserFullName(WT_USER_ID), '</a></li> <li>', logout_link(), '</li>';
		} else {
			echo '<li>', login_link(), '</li> ';
		}
		$menu=WT_MenuBar::getFavoritesMenu();
			if ($menu) {echo $menu->GetMenuAsList();}
		$menu=WT_MenuBar::getThemeMenu();
			if ($menu) {echo $menu->GetMenuAsList();}
		$menu=WT_MenuBar::getLanguageMenu();
			if ($menu) {echo $menu->GetMenuAsList();}
		echo '</ul><div class="title">';
		print_gedcom_title_link(true);
		echo
			'</div>',
			'<div class="header_search">',
			'<form action="search.php" method="post">',
			'<input type="hidden" name="action" value="general" />',
			'<input type="hidden" name="topsearch" value="yes" />',
			'<input type="text" name="query" size="25" value="', WT_I18N::translate('Search'), '" ',
				'onfocus="if (this.value==\'', WT_I18N::translate('Search'), '\') this.value=\'\'; focusHandler();" ',
				'onblur="if (this.value==\'\') this.value=\'', WT_I18N::translate('Search'), '\';" />',
			'<input type="image" class="image" src="', $WT_IMAGES['search'], '" alt="', WT_I18N::translate('Search'), '" title="', WT_I18N::translate('Search'), '" />',
			'</form>',
			'</div>';
		$menu_items=array(
			WT_MenuBar::getGedcomMenu(),
			WT_MenuBar::getMyPageMenu(),
			WT_MenuBar::getChartsMenu(),
			WT_MenuBar::getListsMenu(),
			WT_MenuBar::getCalendarMenu(),
			WT_MenuBar::getReportsMenu(),
			WT_MenuBar::getSearchMenu(),
		);
		foreach (WT_MenuBar::getModuleMenus() as $menu) {
			$menu_items[]=$menu;
		}
		$menu_items[]=WT_MenuBar::getHelpMenu();
	}
	// Print the menu bar
	echo
		'<img src="', $WT_IMAGES['hline'], '" width="100%" height="3" alt="" />',
		'<div id="topMenu">',
		'<ul id="main-menu">';
	foreach ($menu_items as $menu) {
		if ($menu) {
			echo $menu->getMenuAsList();
		}
	}
	unset($menu_items, $menu);
	echo
		'</ul>',  // <ul id="main-menu">
		'</div>', // <div id="topMenu">
		'<img src="', $WT_IMAGES['hline'], '" width="100%" height="3" alt="" />',
		'</div>', // <div id="header">
		'<div id="content">';
}
