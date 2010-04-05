<?php
/**
 * Menu Extension
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2007 to 2009  PGV Development Team.  All rights reserved.
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
 /*
 * This is a sample customizable menu, with sub-menus, that will appear in the top links of each
 * page.
 *
 * To make this menu appear between the Search and the Help menus at the top of each page, this
 * file needs to be named "optional_menu.php".  Furthermore, the individual menu and sub-menu
 * entries need to be valid.  You can remove the extra comments but, for copyright reasons, the
 * first comment block at the top of this file should be retained.
 *
 * Please note:
 * This menu is NEVER visible when a Search robot is accessing the site.  This is controlled by
 * "includes/classes/class_menubar.php".
 *
 * Use the code in "includes/classes/class_menubar.php" as a guide to how valid menus and sub-menus 
 * should be constructed.
 */
 /*
 * Explanation of the '$menu = new Menu("whatever 1", "whatever 2")' and
 * '$submenu = new Menu("whatever 1", "whatever 2")'line:
 *	'whatever 1' is the text that is to appear for this sub-menu entry.  If you code this as
 *		shown, the text will appear exactly as entered no matter what the page language is.
 *
 * 'whatever 2' is the URL required to launch the desired module, web site, or webtrees
 *		script.  You need to provide all of the input parameters or variables that the script
 *		needs.  For example, to get to the Yahoo web site, you'd replace '"whatever 2"' with
 *		'"http://www.yahoo.com"'.  Note that the URL you enter here is enclosed in quotation marks.
 *
 *		If the URL requires something enclosed in quotation marks, you should precede each of them
 *		with a backslash or enclose the entire URL in apostrophes instead of quotation marks.
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

	//-- main Optional menu item
	$menu = new Menu("Optional Menu name", "custom link #1", "down");
	if (!empty($WT_IMAGES["gedcom"]["large"]))
		$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["gedcom"]["large"]);
	$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");

	// First sub-menu (visible even when Search robots are looking at the site)
	$submenu = new Menu("Custom Menu Item 1", "custom link #1");
	$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
	$menu->addSubmenu($submenu);

	// Second sub-menu (invisible to Search robots)
	if (empty($SEARCH_SPIDER)) {
		$submenu = new Menu("Custom Menu Item 2", "custom link #2");
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);
	}

	// Third sub-menu (visible only to users with site Admin rights)
	if (WT_USER_IS_ADMIN) {
		$submenu = new Menu("Custom Menu Item 3", "custom link #2");
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);
	}

	// Fourth sub-menu (visible only to users with GEDCOM Admin rights)
	if (WT_USER_GEDCOM_ADMIN) {
		$submenu = new Menu("Custom Menu Item 4", "custom link #2");
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);
	}

	// Fifth sub-menu (visible only Clippings Cart is enabled and not a Search robot)
	if (empty($SEARCH_SPIDER) && $GLOBALS["ENABLE_CLIPPINGS_CART"]) {
		$submenu = new Menu("Custom Menu Item 5", "custom link #2");
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);
	}
?>
