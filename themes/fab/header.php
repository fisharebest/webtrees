<?php
// Header for FAB theme
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Modifications Copyright (c) 2010 Greg Roach
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

use WT\Auth;

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// This theme uses the jQuery “colorbox” plugin to display images
$this
	->addExternalJavascript(WT_JQUERY_COLORBOX_URL)
	->addExternalJavascript(WT_JQUERY_WHEELZOOM_URL)
	->addInlineJavascript('activate_colorbox();')
	->addInlineJavascript('jQuery.extend(jQuery.colorbox.settings, {width:"85%", height:"85%", transition:"none", slideshowStart:"'. WT_I18N::translate('Play').'", slideshowStop:"'. WT_I18N::translate('Stop').'"})')
	->addInlineJavascript('
		jQuery.extend(jQuery.colorbox.settings, {
			title: function() {
				var img_title = jQuery(this).data("title");
				return img_title;
			}
		});
	');
echo
	'<!DOCTYPE html>',
	'<html ', WT_I18N::html_markup(), '>',
	'<head>',
	'<meta charset="UTF-8">',
	'<meta http-equiv="X-UA-Compatible" content="IE=edge">',
	header_links($META_DESCRIPTION, $META_ROBOTS, $META_GENERATOR, $LINK_CANONICAL),
	'<title>', WT_Filter::escapeHtml($title), '</title>',
	'<link rel="icon" href="', WT_CSS_URL, 'favicon.png" type="image/png">',
	'<link type="text/css" rel="stylesheet" href="', WT_THEME_URL, 'jquery-ui-1.10.3/jquery-ui-1.10.3.custom.css">',
	'<link type="text/css" rel="stylesheet" href="', WT_CSS_URL, 'style.css">',
	'<!--[if IE]>',
	'<link type="text/css" rel="stylesheet" href="', WT_CSS_URL, 'msie.css">',
	'<![endif]-->',
	'</head>',
	'<body id="body">';

if ($view != 'simple') { // Use “simple” headers for popup windows
	echo '<div id="header" class="block">';
	echo '<div id="header-user-links"><ul class="makeMenu">';
	if (Auth::check()) {
		echo '<li><a href="edituser.php">', WT_Filter::escapeHtml(Auth::user()->getRealName()), '</a></li> <li>', logout_link(), '</li>';
		if (WT_USER_CAN_ACCEPT && exists_pending_change()) {
			echo ' <li><a href="#" onclick="window.open(\'edit_changes.php\',\'_blank\',chan_window_specs); return false;" style="color:red;">', WT_I18N::translate('Pending changes'), '</a></li>';
		}
	} else {
		echo '<li>', login_link(), '</li> ';
	}
	$menu=WT_MenuBar::getFavoritesMenu();
	if ($menu) {
		echo $menu->getMenuAsList();
	}
	$menu=WT_MenuBar::getLanguageMenu();
	if ($menu) {
		echo $menu->getMenuAsList();
	}
	$menu=WT_MenuBar::getThemeMenu();
	if ($menu) {
		echo $menu->getMenuAsList();
	}
	echo
		'<li><form style="display:inline;" action="search.php" method="post">',
		'<input type="hidden" name="action" value="general">',
		'<input type="hidden" name="ged" value="', WT_GEDCOM, '">',
		'<input type="hidden" name="topsearch" value="yes">',
		'<input type="search" name="query" size="20" placeholder="', WT_I18N::translate('Search'), '">',
		'</form></li>',
		'</ul></div>';
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

	echo '<div id="header-title" dir="auto">', WT_TREE_TITLE, '</div>';

	// Print the menu bar
	echo '<div id="header-menu"><ul class="makeMenu">';
	foreach ($menu_items as $menu) {
		if ($menu) {
			echo $menu->getMenuAsList();
		}
	}
	unset($menu_items, $menu);
	echo
		'</ul>',  // <ul class="makeMenu">
		'</div>', // <div id="header-menu">
		'</div>'; // <div id="header">
}
echo
	$javascript,
	WT_FlashMessages::getHtmlMessages(), // Feedback from asynchronous actions
	'<div id="content">';
