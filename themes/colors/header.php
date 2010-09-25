<?php
/**
 * Header for colors theme
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

global $modules;

// Definitions to simplify logic on pages with right-to-left languages
// TODO: merge this into the trunk?
if ($TEXT_DIRECTION=='ltr') {
	define ('WT_CSS_ALIGN',         'left');
	define ('WT_CSS_REVERSE_ALIGN', 'right');
} else {
	define ('WT_CSS_ALIGN',         'right');
	define ('WT_CSS_REVERSE_ALIGN', 'left');
}

echo
	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
	'<html xmlns="http://www.w3.org/1999/xhtml" ', i18n::html_markup(), '>',
	'<head>',
	'<title>', htmlspecialchars($title), '</title>',
	'<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />';

if (WT_USE_LIGHTBOX) {
	if ($TEXT_DIRECTION=='rtl') {
		echo
			'<link rel="stylesheet" href="modules/lightbox/css/clearbox_music_RTL.css" type="text/css" />',
			'<link rel="stylesheet" href="modules/lightbox/css/album_page_RTL_ff.css" type="text/css" media="screen" />';
	} else {
		echo
			'<link rel="stylesheet" href="modules/lightbox/css/clearbox_music.css" type="text/css" />',
			'<link rel="stylesheet" href="modules/lightbox/css/album_page.css" type="text/css" media="screen" />';
	}
}

echo
	'<meta name="description" content="', htmlspecialchars($META_DESCRIPTION), '" />',
	'<meta name="robots" content="', htmlspecialchars($META_ROBOTS), '" />',
	'<meta name="generator" content="', WT_WEBTREES, ' ', WT_VERSION_TEXT, '" />';


echo
	$javascript, 
	'<script type="text/javascript" src="js/jquery/jquery.min.js"></script>',
	'<script type="text/javascript" src="js/jquery/jquery-ui.min.js"></script>',
	'<script type="text/javascript" src="js/jquery/jquery.tablesorter.js"></script>',
	'<script type="text/javascript" src="js/jquery/jquery.tablesorter.pager.js"></script>',
	'<link type="text/css" href="js/jquery/css/jquery-ui.custom.css" rel="Stylesheet" />';
?>

<link type="text/css" href="<?php echo WT_THEME_DIR?>jquery/jquery-ui_theme.css" rel="Stylesheet" />
<link rel="stylesheet" href="<?php echo $print_stylesheet; ?>" type="text/css" media="print" />

<?php
if ($TEXT_DIRECTION=='rtl') { ?>
	<link type="text/css" href="<?php echo WT_THEME_DIR?>jquery/jquery-ui_theme_rtl.css" rel="Stylesheet" />
<?php } 

echo
	'<link rel="stylesheet" href="', $modules, '" type="text/css" />',
	'<link rel="stylesheet" href="', $stylesheet, '" type="text/css" media="all" />';
	
if ($BROWSERTYPE!='other') { ?>
	<link rel="stylesheet" href="<?php echo $THEME_DIR.$BROWSERTYPE; ?>.css" type="text/css" media="all" />
<?php 
}
	
	
if ((!empty($rtl_stylesheet))&&($TEXT_DIRECTION=="rtl")) {?> 
	<link rel="stylesheet" href="<?php echo $rtl_stylesheet; ?>" type="text/css" media="all" /> 
<?php }
	echo '</head><body id="body" ', $bodyOnLoad, '>';
?>

<!-- Remove header for edit windows -->
<!-- begin header section -->
<?php 
if ($view!='simple') {
	echo
		'<div id="header" class="', $TEXT_DIRECTION, '">',
		'<table class="header">',
		'<tr>',
		'<td align="', $TEXT_DIRECTION=="ltr"?"left":"right", '" valign="middle" >',
		'<div class="title">';
	print_gedcom_title_link(TRUE);
	echo '</div></td><td>';
	if (empty($SEARCH_SPIDER)) { 
		echo '<div style="float:', WT_CSS_REVERSE_ALIGN, ';"><ul class="makeMenu">';
		if (WT_USER_ID) {
			echo
				'<li><a href="edituser.php" class="icon_color">', getUserFullName(WT_USER_ID), '</a></li>',
				' | <li><a href="index.php?logout=1" class="icon_color">', i18n::translate('Logout'), '</a></li>';
			if (WT_USER_GEDCOM_ADMIN) {
				echo ' | <li><a href="admin.php" class="icon_color">', i18n::translate('Admin'), '</a></li>';
			}
			if (WT_USER_CAN_ACCEPT && exists_pending_change()) {
				echo ' | <li><a href="javascript:;" onclick="window.open(\'edit_changes.php\',\'_blank\',\'width=600,height=500,resizable=1,scrollbars=1\'); return false;" style="color:red;">', i18n::translate('Pending Changes'), '</a></li>';
			}
		} else {
			$LOGIN_URL=get_site_setting('LOGIN_URL');
			if (WT_SCRIPT_NAME==$LOGIN_URL) {
				echo '<li><a class="icon_color" href="', $LOGIN_URL, '">', i18n::translate('Login'), '</a></li>';
			} else {
				$QUERY_STRING = normalize_query_string($QUERY_STRING.'&amp;logout=');
				echo '<li><a class="icon_color" href="', $LOGIN_URL, '?url=', WT_SCRIPT_PATH, WT_SCRIPT_NAME, decode_url(normalize_query_string($QUERY_STRING.'&amp;ged='.WT_GEDCOM)), '">', i18n::translate('Login'), '</a></li>';
			}
		}
		echo '<span class="link"> | ', MenuBar::getFavoritesMenu()->getMenuAsList();
		echo ' | ', MenuBar::getLanguageMenu()->getMenuAsList();
		global $ALLOW_THEME_DROPDOWN;
		if ($ALLOW_THEME_DROPDOWN && get_site_setting('ALLOW_USER_THEMES')) {
			echo ' | ', MenuBar::getThemeMenu()->getMenuAsList();
		}
		echo 
			'</span> | <form style="display:inline;" action="search.php" method="get">',
			'<input type="hidden" name="action" value="general" />',
			'<input type="hidden" name="topsearch" value="yes" />',
			'<input type="text" name="query" size="15" value="', i18n::translate('Search'), '" onfocus="if (this.value==\'', i18n::translate('Search'), '\') this.value=\'\'; focusHandler();" onblur="if (this.value==\'\') this.value=\'', i18n::translate('Search'), '\';" />',
			'<input type="image" src="', WT_THEME_DIR, 'images/go.gif', '" align="top" alt="', i18n::translate('Search'), '" title="', i18n::translate('Search'), '" />',
			'</form>',
			'</ui></div>'; 
	}

	echo '</td></tr></table></div>';
?>
<!--end header section -->
<!--begin menu section -->
<?php 
	echo
		'<table id="toplinks"><tr><td class="toplinks_left">',
		'<table align="', $TEXT_DIRECTION=="ltr"?"left":"right", '">',
		'<tr>';
	$menu=MenuBar::getGedcomMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menu=MenuBar::getMyPageMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menu=MenuBar::getChartsMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menu=MenuBar::getListsMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menu=MenuBar::getCalendarMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menu=MenuBar::getReportsMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menu=MenuBar::getSearchMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menus=MenuBar::getModuleMenus();
	foreach ($menus as $m=>$menu) { 
		if ($menu) {
			$menu->addLabel('', 'none');
			echo '<td>', $menu->getMenu(), '</td>';
		}
	}
	$menu=MenuBar::getHelpMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	echo '</tr></table></td>';
	if (empty($SEARCH_SPIDER)) { 
		echo '<td class="toplinks_right"><div align="', $TEXT_DIRECTION=="rtl"?"left":"right", '">';
		echo color_theme_dropdown();
		echo '</div></td>';
	}
	echo '</tr></table>';
}
?>
<!-- end menu section -->
<!-- begin content section -->
<div id="content">
