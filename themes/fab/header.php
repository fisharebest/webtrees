<?php
// Header for FAB theme
//
// webtrees: Web based Family History software
// Copyright (C) 2010 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// @package webtrees
// @subpackage Themes
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

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
	'<html xmlns="http://www.w3.org/1999/xhtml" ',  i18n::html_markup(), '>',
	'<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />',
	'<title>', htmlspecialchars($GEDCOM_TITLE), '</title>',
	'<link rel="shortcut icon" href="', $FAVICON, '" type="image/x-icon">',
	'<link rel="stylesheet" href="', $stylesheet, '" type="text/css" media="all" />';

if ($ENABLE_RSS && !$REQUIRE_AUTHENTICATION) {
	echo '<link href="', urlencode($SERVER_URL.'rss.php?ged='.WT_GEDCOM), '" rel="alternate" type="', $applicationType, '" title="', htmlspecialchars($GEDCOM_TITLE), '" />';
}

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
	'<meta name="author" content="', htmlspecialchars($META_AUTHOR), '" />',
	'<meta name="publisher" content="', htmlspecialchars($META_PUBLISHER), '" />',
	'<meta name="copyright" content="', htmlspecialchars($META_COPYRIGHT), '" />',
	'<meta name="description" content="', htmlspecialchars($META_DESCRIPTION), '" />',
	'<meta name="page-topic" content="', htmlspecialchars($META_PAGE_TOPIC), '" />',
	'<meta name="audience" content="', htmlspecialchars($META_AUDIENCE), '" />',
	'<meta name="page-type" content="', htmlspecialchars($META_PAGE_TYPE), '" />',
	'<meta name="robots" content="', htmlspecialchars($META_ROBOTS), '" />',
	'<meta name="revisit-after" content="', htmlspecialchars($META_REVISIT), '" />',
	'<meta name="keywords" content="', htmlspecialchars($META_KEYWORDS), '" />',
	'<meta name="generator" content="', WT_WEBTREES, ' ', WT_VERSION_TEXT, '" />';

echo
	$javascript, $head, 
	'<script type="text/javascript" src="js/jquery/jquery.min.js"></script>',
	'<script type="text/javascript" src="js/jquery/jquery-ui.min.js"></script>',
	'<link type="text/css" href="js/jquery/css/jquery-ui.custom.css" rel="Stylesheet" />';
	
?>

<link type="text/css" href="<?php echo WT_THEME_DIR?>jquery/jquery-ui_theme.css" rel="Stylesheet" />

<?php if ($TEXT_DIRECTION=='rtl') {?>
	<link type="text/css" href="<?php echo WT_THEME_DIR?>jquery/jquery-ui_theme_rtl.css" rel="Stylesheet" />
<?php }?>

<link rel="stylesheet" href="<?php echo $rtl_stylesheet; ?>" type="text/css" media="all" />

<?php
echo
	'</head><body id="body" ', $bodyOnLoad, '>';
flush(); // Allow the browser to start fetching external stylesheets, javascript, etc.

echo '<div id="header" class="block">'; // Every page has a header
if ($view!='simple') {
	echo
		'<div style="float:', WT_CSS_ALIGN, '; font-size:250%;"><a style="color:#888888;" href="', $HOME_SITE_URL, '?>">', $HOME_SITE_TEXT, '</div>';
	// Print the user links
	if ($SEARCH_SPIDER) {
		// Search engines get a reduced menu
		$menu_items=array(
			MenuBar::getGedcomMenu(),
			MenuBar::getListsMenu(),
			MenuBar::getCalendarMenu()
		);
	} else {
		// Options for real users
		echo '<div style="float:', WT_CSS_REVERSE_ALIGN, ';"><ul class="makeMenu">';
		if (WT_USER_ID) {
			echo
				'<li><a href="edituser.php" class="link">', getUserFullName(WT_USER_ID), '</a></li>',
				' | <li><a href="index.php?logout=1" class="link">', i18n::translate('Logout'), '</a></li>';
			if (WT_USER_GEDCOM_ADMIN) {
				echo ' | <li><a href="admin.php" class="link">', i18n::translate('Admin'), '</a></li>';
			}
			if (WT_USER_CAN_ACCEPT && exists_pending_change()) {
				echo ' | <li><a href="javascript:;" onclick="window.open(\'edit_changes.php\',\'_blank\',\'width=600,height=500,resizable=1,scrollbars=1\'); return false;" style="color:red;">', i18n::translate('Pending Changes'), '</a></li>';
			}
		} else {
			global $LOGIN_URL;
			if (WT_SCRIPT_NAME==basename($LOGIN_URL)) {
				echo '<li><a href="', $LOGIN_URL, '" class="link">', i18n::translate('Login'), '</a></li>';
			} else {
				$QUERY_STRING = normalize_query_string($QUERY_STRING.'&amp;logout=');
				echo '<li><a href="', $LOGIN_URL, '?url=', WT_SCRIPT_PATH, WT_SCRIPT_NAME, decode_url(normalize_query_string($QUERY_STRING.'&amp;ged='.WT_GEDCOM)), '" class="link">', i18n::translate('Login'), '</a></li>';
			}
		}
			echo '<span class="link"> | ', MenuBar::getFavoritesMenu()->getMenuAsList();
			echo ' | ', MenuBar::getLanguageMenu()->getMenuAsList();
			global $ALLOW_THEME_DROPDOWN, $ALLOW_USER_THEMES;
			if ($ALLOW_THEME_DROPDOWN && $ALLOW_USER_THEMES) {
				echo ' | ', MenuBar::getThemeMenu()->getMenuAsList();
			}
		echo
			'</span> | <form style="display:inline;" action="search.php" method="get">',
			'<input type="hidden" name="action" value="general" />',
			'<input type="hidden" name="topsearch" value="yes" />',
			'<input type="text" name="query" size="20" value="', i18n::translate('Search'), '" onfocus="if (this.value==\'', i18n::translate('Search'), '\') this.value=\'\'; focusHandler();" onblur="if (this.value==\'\') this.value=\'', i18n::translate('Search'), '\';" />',
			'</form>',
			'</ul></div>';
		$menu_items=array(
			MenuBar::getGedcomMenu(),
			MenuBar::getMygedviewMenu(),
			MenuBar::getChartsMenu(),
			MenuBar::getListsMenu(),
			MenuBar::getCalendarMenu(),
			MenuBar::getReportsMenu(),
			MenuBar::getSearchMenu(),
			MenuBar::getOptionalMenu()
		);
		foreach (MenuBar::getModuleMenus() as $menu) {
			$menu_items[]=$menu;
		}

		// Help menu
		$menu = new Menu(i18n::translate('Help'), "#", "down");
		$menu->addOnclick('return helpPopup("'.WT_SCRIPT_NAME.'");');
		$menu_items[]=$menu;
		echo
			'<div style="float:', WT_CSS_ALIGN, '; clear:', WT_CSS_ALIGN, '; font-size:175%;">',
			htmlspecialchars($GEDCOM_TITLE),
			'</div>';
	}
	// Print the menu bar
	echo '<div id="topMenu"><ul class="makeMenu">';
	foreach ($menu_items as $n=>$menu) {
		if ($menu && $menu->link) {
			if ($n>0) {
				echo ' | ';
			}
			echo $menu->getMenuAsList();
		}
	}
	unset($menu_items, $n, $menu);
	echo '</ul></div>';
}
echo '</div><div id="content">';
flush(); // Allow the browser to format the header/menus while we generate the page
