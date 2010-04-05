<?php
/**
 * Toplinks for Colors theme
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

$menubar = new MenuBar();
?>
<table id="toplinks">
	<tr>
		<td class="toplinks_left">
		<table align="<?php print $TEXT_DIRECTION=="ltr"?"left":"right" ?>">
			<tr> 
<?php
	$menu = $menubar->getGedcomMenu();
	if($menu->link != "") {
		echo "<td>";
		$menu->addLabel("", "none");
		$menu->printMenu();
		echo "</td>";
	}
	$menu = $menubar->getMygedviewMenu();
	if($menu->link != "") {
		echo "<td>";
		$menu->addLabel("", "none");
		$menu->printMenu();
		echo "</td>";
	}
	$menu = $menubar->getChartsMenu();
	if($menu->link != "") {
		echo "<td>";
		$menu->addLabel("", "none");
		$menu->printMenu();
		echo "</td>";
	}
	$menu = $menubar->getListsMenu();
	if($menu->link != "") {
		echo "<td>";
		$menu->addLabel("", "none");
		$menu->printMenu();
		echo "</td>";
	}
	$menu = $menubar->getCalendarMenu();
	if($menu->link != "") {
		echo "<td>";
		$menu->addLabel("", "none");
		$menu->printMenu();
		echo "</td>";
	}
	$menu = $menubar->getReportsMenu();
	if($menu->link != "") {
		echo "<td>";
		$menu->addLabel("", "none");
		$menu->printMenu();
		echo "</td>";
	}
	$menu = $menubar->getSearchMenu();
	if($menu->link != "") {
		echo "<td>";
		$menu->addLabel("", "none");
		$menu->printMenu();
		echo "</td>";
	}
	$menu = $menubar->getOptionalMenu(); 
	if($menu->link != "") {
		echo "<td>";
		$menu->addLabel("", "none");
		$menu->printMenu();
		echo "</td>";
	}
	$menus = $menubar->getModuleMenus();
		foreach($menus as $m=>$menu) { 
			if($menu->link != "") {
				echo "<td>";
				$menu->addLabel("", "none");
				$menu->printMenu();
				echo "</td>";
			}
		}
	$menu = $menubar->getHelpMenu();
	if($menu->link != "") {
		echo "<td>";
		$menu->addLabel("", "none");
		$menu->printMenu();
		echo "</td>";
	}
?>
			</tr>
		</table>
		</td>
<?php if(empty($SEARCH_SPIDER)) { ?>
		<td class="toplinks_right">
		<div align="<?php echo $TEXT_DIRECTION=="rtl"?"left":"right" ?>" >
		<?php echo color_theme_dropdown(); ?>
		</div>
		</td>
<?php } ?>
	</tr>
	</table>

<!-- close div for div id="header" -->
<div id="content">
