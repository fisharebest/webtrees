<?php
/**
 * Theme Select Block
 *
 * This block will print a form that allows the visitor to change the theme
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
 * @subpackage Blocks
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_THEME_SELECT_PHP', '');

$WT_BLOCKS["print_block_theme_select"]["name"]			= i18n::translate('Theme Select');
$WT_BLOCKS["print_block_theme_select"]["descr"]		= i18n::translate('The Theme Select block displays the Theme selector even when the Change Theme feature is disabled.');
$WT_BLOCKS["print_block_theme_select"]["type"]			= "gedcom";
$WT_BLOCKS["print_block_theme_select"]["canconfig"]	= false;
$WT_BLOCKS["print_block_theme_select"]["config"]		= array("cache"=>-1);

function print_block_theme_select($style=0, $config="", $side, $index) {
	global $ALLOW_THEME_DROPDOWN, $ALLOW_USER_THEMES, $THEME_DIR, $themeformcount;

	$id="theme_select";
	$title=i18n::translate('Change Theme').help_link('change_theme');

	$theme_menu=MenuBar::getThemeMenu();
	$content='<div class="center theme_form"><br />'.$theme_menu->getMenuAsDropdown().'<br /<br /></div>';

	global $THEME_DIR;
	if ($style) {
		require $THEME_DIR.'templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'templates/block_main_temp.php';
	}
}
?>
