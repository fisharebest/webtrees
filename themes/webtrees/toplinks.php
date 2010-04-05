<?php
/**
 * Top-of-page menu for Standard theme
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
 * @version $Id: toplinks.php 7070 2010-02-28 23:38:36Z nigel $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$menubar = new MenuBar();
?>
<div style="width: 98%">
		<img src="<?php print $WT_IMAGE_DIR."/".$WT_IMAGES["hline"]["other"]; ?>" width="99%" height="3" alt="" />
	<table id="topMenu">
		<tr>
			<?php
			$menu = $menubar->getGedcomMenu();
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu();
				print "\t</td>\n";
			}
			$menu = $menubar->getMygedviewMenu();
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu();
				print "\t</td>\n";
			}
			$menu = $menubar->getChartsMenu();
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu();
				print "\t</td>\n";
			}
			$menu = $menubar->getListsMenu();
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu();
				print "\t</td>\n";
			}
			$menu = $menubar->getCalendarMenu();
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu();
				print "\t</td>\n";
			}
			$menu = $menubar->getReportsMenu();
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu();
				print "\t</td>\n";
			}

			$menu = $menubar->getSearchMenu();
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu();
				print "\t</td>\n";
			}

			$menu = $menubar->getOptionalMenu();
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu();
				print "\t</td>\n";
			}

			$menus = $menubar->getModuleMenus();
			foreach($menus as $m=>$menu) {
				if($menu->link != "") {
					print "\t<td width=\"7%\" valign=\"top\">\n";
					$menu->printMenu();
					print "\t</td>\n";
				}
			}

			$menu = $menubar->getHelpMenu();
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu();
				print "\t</td>\n";
			}
			?>
		</tr>
	</table>
	<img align="middle" src="<?php print $WT_IMAGE_DIR."/".$WT_IMAGES["hline"]["other"]; ?>" width="99%" height="3" alt="" />
</div>
</div>

<div id="content">
