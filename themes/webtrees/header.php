<?php
/**
 * Header for webtrees theme
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @subpackage Themes
 * @version $Id$
 */

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
	'<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />',
	'<link rel="stylesheet" href="', $stylesheet, '" type="text/css" media="all" />';


if (isset($_GET["mod_action"]) && $_GET["mod_action"]=="places_edit") {
	echo '<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />';
}

if (file_exists(WT_THEME_DIR.$BROWSERTYPE.'.css')) {
	echo '<link rel="stylesheet" href="', WT_THEME_DIR.$BROWSERTYPE, '.css" type="text/css" media="all" />';
}
// Additional css files required (Only if Lightbox installed)
if (WT_USE_LIGHTBOX) {
	if ($TEXT_DIRECTION=='rtl') {
		echo '<link rel="stylesheet" href="', WT_MODULES_DIR, 'lightbox/css/clearbox_music_RTL.css" type="text/css" />';
		echo '<link rel="stylesheet" href="', WT_MODULES_DIR, 'lightbox/css/album_page_RTL_ff.css" type="text/css" media="screen" />';
	} else {
		echo '<link rel="stylesheet" href="', WT_MODULES_DIR, 'lightbox/css/clearbox_music.css" type="text/css" />';
		echo '<link rel="stylesheet" href="', WT_MODULES_DIR, 'lightbox/css/album_page.css" type="text/css" media="screen" />';
	}
}

if (!empty($LINK_CANONICAL)) {
	echo '<link rel="canonical" href="', $LINK_CANONICAL, '" />';
}
if (!empty($META_DESCRIPTION)) {
	echo '<meta name="description" content="', htmlspecialchars($META_DESCRIPTION), '" />';
}
if (!empty($META_ROBOTS)) {
	echo '<meta name="robots" content="', htmlspecialchars($META_ROBOTS), '" />';
}
if (!empty($META_GENERATOR)) {
	echo '<meta name="generator" content="', $META_GENERATOR, '" />';
}

echo
	$javascript,
	'<link type="text/css" href="js/jquery/css/jquery-ui.custom.css" rel="Stylesheet" />',
	'<link type="text/css" href="', WT_THEME_DIR, 'jquery/jquery-ui_theme.css" rel="Stylesheet" />';
if ($TEXT_DIRECTION=='rtl') {
	echo '<link type="text/css" href="', WT_THEME_DIR, 'jquery/jquery-ui_theme_rtl.css" rel="Stylesheet" />';
}
echo
	'<link type="text/css" href="', WT_THEME_DIR, 'modules.css" rel="Stylesheet" />',
	'</head>',
	'<body id="body">';

// begin header section
if ($view!='simple') {
	echo '<div id="header" class="', $TEXT_DIRECTION, '">',
	'<table width="100%">',
		'<tr>',
			'<td><img src="', WT_THEME_DIR, 'images/header.jpg" width="281" height="50" alt="" /></td>',
			'<td width="100%">',
					'<div align="center">',
					'<b>';
	if (WT_USER_ID) {
		echo '<a href="edituser.php" class="link">', WT_I18N::translate('Logged in as '), ' (', WT_USER_NAME, ')</a> | ', logout_link();
	} elseif (empty($SEARCH_SPIDER)) {
		echo login_link();
	}
 	echo '</b>',

					'</div>',
					'<ul id="extra-menu" class="makeMenu">';
							if (!$SEARCH_SPIDER) {
								echo WT_MenuBar::getFavoritesMenu()->getMenuAsList();
								global $ALLOW_THEME_DROPDOWN;
								if ($ALLOW_THEME_DROPDOWN && get_site_setting('ALLOW_USER_THEMES')) {
									echo ' | ', WT_MenuBar::getThemeMenu()->getMenuAsList();
								}
								$language_menu=WT_MenuBar::getLanguageMenu();
								if ($language_menu) {
									echo ' | ', $language_menu->getMenuAsList();
								}
							}
					echo '</ul>',
			'</td>',
		'</tr>',
	'</table>'.
	'<table width="100%">',
		'<tr>',
			'<td>',
				'<div class="title">';
					print_gedcom_title_link(TRUE);
				echo '</div>',
			'</td>',
			'<td align="', $TEXT_DIRECTION=="rtl"?"left":"right", '" valign="middle" >';
				if (empty($SEARCH_SPIDER)) {
					echo '<form action="search.php" method="post">',
						'<input type="hidden" name="action" value="general" />',
						'<input type="hidden" name="topsearch" value="yes" />',
						'<input type="text" name="query" size="25" value="', WT_I18N::translate('Search'), '"',
							'onfocus="if (this.value==\'', WT_I18N::translate('Search'), '\') this.value=\'\'; focusHandler();"',
							'onblur="if (this.value==\'\') this.value=\'', WT_I18N::translate('Search'), '\';" />',
						'<input type="image" class="image" src="', $WT_IMAGES['search'], '" alt="', WT_I18N::translate('Search'), '" title="', WT_I18N::translate('Search'), '" />',
					'</form>';
				}
			echo '</td>',
		'</tr>',
	'</table>';

	echo '<div>',
		'<img src="', $WT_IMAGES['hline'], '" width="100%" height="3" alt="" />',
		'<table id="topMenu">',
			'<tr>';
				$menu=WT_MenuBar::getGedcomMenu();
				if ($menu) {
					echo '<td width="1" valign="top">', $menu->getMenu(), '</td>';
				}
				$menu=WT_MenuBar::getMyPageMenu();
				if ($menu) {
					echo '<td width="1" valign="top">', $menu->getMenu(), '</td>';
				}
				$menu=WT_MenuBar::getChartsMenu();
				if ($menu) {
					echo '<td width="1" valign="top">', $menu->getMenu(), '</td>';
				}
				$menu=WT_MenuBar::getListsMenu();
				if ($menu) {
					echo '<td width="1" valign="top">', $menu->getMenu(), '</td>';
				}
				$menu=WT_MenuBar::getCalendarMenu();
				if ($menu) {
					echo '<td width="1" valign="top">', $menu->getMenu(), '</td>';
				}
				$menu=WT_MenuBar::getReportsMenu();
				if ($menu) {
					echo '<td width="1" valign="top">', $menu->getMenu(), '</td>';
				}
				$menu=WT_MenuBar::getSearchMenu();
				if ($menu) {
					echo '<td width="1" valign="top">', $menu->getMenu(), '</td>';
				}
				$menus=WT_MenuBar::getModuleMenus();
				foreach ($menus as $m=>$menu) {
					if ($menu) {
						echo '<td width="1" valign="top">', $menu->getMenu(), '</td>';
					}
				}
				$menu=WT_MenuBar::getHelpMenu();
				if ($menu) {
					echo '<td width="1" valign="top">', $menu->getMenu(), '</td>';
				}
			echo '</tr>',
		'</table>',
		'<img align="middle" src="', $WT_IMAGES['hline'], '" width="100%" height="3" alt="" />',
	'</div>',
'</div>',
// end header section -->
// begin content section -->
'<div id="content">';
}
