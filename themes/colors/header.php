<?php
// Header for colors theme
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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

global $WT_IMAGES, $subColor;

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

// Remove list from home when only 1 gedcom
$this->addInlineJavaScript(
	'if (jQuery("#menu-tree ul li").length == 2) jQuery("#menu-tree ul li:last-child").remove();'
);



$menu_items = array(
	WT_MenuBar::getGedcomMenu(),
	WT_MenuBar::getMyPageMenu(),
	WT_MenuBar::getChartsMenu(),
	WT_MenuBar::getListsMenu(),
	WT_MenuBar::getCalendarMenu(),
	WT_MenuBar::getReportsMenu(),
	WT_MenuBar::getSearchMenu(),
);
foreach (WT_MenuBar::getModuleMenus() as $menu) {
	$menu_items[] = $menu;
}

?>
<!DOCTYPE html>
<html <?php echo WT_I18N::html_markup(); ?>>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php echo header_links($META_DESCRIPTION, $META_ROBOTS, $META_GENERATOR, $LINK_CANONICAL); ?>
	<title><?php echo WT_Filter::escapeHtml($title); ?></title>
	<link rel="icon" href="<?php echo WT_CSS_URL; ?>favicon.png" type="image/png">
	<link rel="stylesheet" type="text/css" href="<?php echo WT_THEME_URL; ?>jquery-ui-1.11.2/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="<?php echo WT_CSS_URL; ?>css/colors.css">
	<link rel="stylesheet" type="text/css" href="<?php echo WT_CSS_URL; ?>css/<?php echo $subColor; ?>.css">
	<?php if (stristr($_SERVER['HTTP_USER_AGENT'], 'iPad')) { ?>
	<link type="text/css" rel="stylesheet" href="<?php echo WT_CSS_URL; ?>ipad.css">
	<?php } ?>
	<!--[if IE 8]>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
	<![endif]-->
</head>
<body id="body">
	<?php if ($view !== 'simple') { ?>
	<header>
		<div id="header">
			<h1><?php echo WT_TREE_TITLE; ?></h1>
			<div id="header-user-links">
				<ul class="makeMenu">
					<?php
					if (Auth::check()) {
						echo '<li><a href="edituser.php" class="link">', WT_Filter::escapeHtml(Auth::user()->getRealName()), '</a></li><li>', logout_link(), '</li>';
						if (WT_USER_CAN_ACCEPT && exists_pending_change()) {
							echo ' <li><a href="#" onclick="window.open(\'edit_changes.php\', \'_blank\', chan_window_specs); return false;" style="color:red;">', WT_I18N::translate('Pending changes'), '</a></li>';
						}
					} else {
						echo '<li>', login_link(),'</li>';
					}
					?>
					<?php echo WT_MenuBar::getFavoritesMenu(); ?>
					<?php echo WT_MenuBar::getLanguageMenu(); ?>
					<?php echo WT_MenuBar::getThemeMenu(); ?>
					<?php if (WT_MenuBar::getThemeMenu()) { echo color_theme_dropdown(); } ?>
					<li>
						<form style="display:inline;" action="search.php" method="post">
							<input type="hidden" name="action" value="general">
							<input type="hidden" name="ged" value="<?php echo WT_GEDCOM; ?>">
							<input type="hidden" name="topsearch" value="yes">
							<input type="search" name="query" size="15" placeholder="<?php echo WT_I18N::translate('Search'); ?>">
							<input class="search-icon" type="image" src="<?php echo $WT_IMAGES['search']; ?>" alt="<?php echo WT_I18N::translate('Search'); ?>" title="<?php echo WT_I18N::translate('Search'); ?>">
						</form>
					</li>
				</ul>
			</div>
		</div>
		<nav>
			<ul id="main-menu">
				<?php
				foreach (array_filter($menu_items) as $menu) {
					echo getMenuAsCustomList($menu);
				}
				?>
			</ul>
		</nav>
	</header>
	<?php } ?>
	<?php echo WT_FlashMessages::getHtmlMessages(); ?>
	<main id="content">
