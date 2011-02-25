<?php
/**
 * Header for Clouds theme
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView Cloudy theme
 * Original author w.a. bastein http://genealogy.bastein.biz
 * Copyright (C) 2010 PGV Development Team.  All rights reserved.
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
	'<html xmlns="http://www.w3.org/1999/xhtml" ', WT_I18N::html_markup(), '>',
	'<head>',
	'<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />',
	'<title>', htmlspecialchars($title), '</title>',
	'<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />';

if (WT_USE_LIGHTBOX) {
	if ($TEXT_DIRECTION=='rtl') {
		echo
			'<link rel="stylesheet" href="', WT_MODULES_DIR, 'lightbox/css/clearbox_music_RTL.css" type="text/css" />',
			'<link rel="stylesheet" href="', WT_MODULES_DIR, 'lightbox/css/album_page_RTL_ff.css" type="text/css" media="screen" />';
	} else {
		echo
			'<link rel="stylesheet" href="', WT_MODULES_DIR, 'lightbox/css/clearbox_music.css" type="text/css" />',
			'<link rel="stylesheet" href="', WT_MODULES_DIR, 'lightbox/css/album_page.css" type="text/css" media="screen" />';
	}
}

if (!empty($LINK_CANONICAL)) {
	echo '<link rel="canonical" href="', $LINK_CANONICAL, '" />';
}
if (!empty($META_DESCRIPTION)) {
	echo '<meta name="description" content="', htmlspecialchars($META_DESCRIPTION), '" />';
}
echo '<meta name="robots" content="', $META_ROBOTS, '" />';
if (!empty($META_GENERATOR)) {
	echo '<meta name="generator" content="', $META_GENERATOR, '" />';
}

echo
	'<link type="text/css" href="js/jquery/css/jquery-ui.custom.css" rel="Stylesheet" />';
?>

<link type="text/css" href="<?php echo WT_THEME_DIR; ?>jquery/jquery-ui_theme.css" rel="Stylesheet" />
<link rel="stylesheet" href="<?php echo $print_stylesheet; ?>" type="text/css" media="print" />

<?php
if ($TEXT_DIRECTION=='rtl') { ?>
	<link type="text/css" href="<?php echo WT_THEME_DIR; ?>jquery/jquery-ui_theme_rtl.css" rel="Stylesheet" />
<?php }

echo
	'<link rel="stylesheet" href="', $modules, '" type="text/css" />',
	'<link rel="stylesheet" href="', $stylesheet, '" type="text/css" media="all" />';

if (file_exists(WT_THEME_DIR.$BROWSERTYPE.'.css')) { ?>
	<link rel="stylesheet" href="<?php echo WT_THEME_DIR.$BROWSERTYPE; ?>.css" type="text/css" media="all" />
<?php
}


if ((!empty($rtl_stylesheet))&&($TEXT_DIRECTION=="rtl")) { ?>
	<link rel="stylesheet" href="<?php echo $rtl_stylesheet; ?>" type="text/css" media="all" />
<?php }
	echo $javascript;
	echo '</head><body id="body">';
?>

<!-- begin header section -->
<?php if ($view!='simple') {
	echo '<div id="rapcontainer"><div id="header" class="', $TEXT_DIRECTION, '">';
?>
<table class="header" style="background:url('<?php echo WT_THEME_DIR; ?>images/clouds.gif')" >
<?php
	echo 
		'<tr><td align="', $TEXT_DIRECTION=="ltr"?"left":"right", '" valign="middle" >',
		'<div class="title">';
	print_gedcom_title_link(TRUE);
	echo '</div></td>';
if (empty($SEARCH_SPIDER)) {
	echo '<td  align="center" valign="middle"><div class="blanco" style="COLOR: #6699ff;" >';
	if (WT_USER_ID) {
		echo '<a href="edituser.php" class="link">', WT_I18N::translate('Logged in as '), ' (', WT_USER_NAME, ')</a>', logout_link();
	} else {
		echo login_link();
	}
	echo '</div></td>',
		 '<td align="', $TEXT_DIRECTION=="ltr"?"left":"right", '" valign="middle" >';
?>
	<div style="white-space: normal;" align="<?php echo $TEXT_DIRECTION=="rtl"?"left":"right"; ?>">
<?php
	echo '<form action="search.php" method="post">',
		 '<input type="hidden" name="action" value="general" />',
		 '<input type="hidden" name="topsearch" value="yes" />',
		 '<input type="text" class="formbut" name="query" size="15" value="', WT_I18N::translate('Search'), '" onfocus="if (this.value==\'', WT_I18N::translate('Search'), '\') this.value=\'\'; focusHandler();" onblur="if (this.value==\'\') this.value=\'', WT_I18N::translate('Search'), '\';" />',
		 '<input type="image" src="', WT_THEME_DIR, 'images/go.gif', '" align="top" alt="', WT_I18N::translate('Search'), '" title="', WT_I18N::translate('Search'), '" />',
		 '</form>',
		 '</div>';
}
	echo '</td></tr></table></div>';
?>
<!-- end header section -->
<!-- begin menu section -->
<?php
echo '<table id="toplinks">',
	 '<tr>',
		'<td class="toplinks_left">',
		'<table align="', $TEXT_DIRECTION=="ltr"?"left":"right", '">',
			'<tr>';
	$menu=WT_MenuBar::getGedcomMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menu=WT_MenuBar::getMyPageMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menu=WT_MenuBar::getChartsMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menu=WT_MenuBar::getListsMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menu=WT_MenuBar::getCalendarMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menu=WT_MenuBar::getReportsMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menu=WT_MenuBar::getSearchMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	$menus=WT_MenuBar::getModuleMenus();
		foreach ($menus as $menu) {
			if ($menu) {
				$menu->addLabel('', 'none');
				echo '<td>', $menu->getMenu(), '</td>';
			}
		}
	$menu=WT_MenuBar::getHelpMenu();
	if ($menu) {
		$menu->addLabel('', 'none');
		echo '<td>', $menu->getMenu(), '</td>';
	}
	echo '</tr></table></td>';

	if (empty($SEARCH_SPIDER)) {
		echo '<td class="toplinks_right">';
		echo '<div style="float:', WT_CSS_REVERSE_ALIGN, ';"><ul class="makeMenu">';
		echo WT_MenuBar::getFavoritesMenu()->getMenuAsList();
		global $ALLOW_THEME_DROPDOWN;
		$language_menu=WT_MenuBar::getLanguageMenu();
		if ($language_menu) {
			echo $language_menu->getMenuAsList();
		}
		if ($ALLOW_THEME_DROPDOWN && get_site_setting('ALLOW_USER_THEMES')) {
			echo WT_MenuBar::getThemeMenu()->getMenuAsList();
		}
		echo '</ul></div></td>';
	}
	echo '</tr></table>';
}
?>
<!-- end menu section -->
<!-- begin content section -->
<div id="content">
