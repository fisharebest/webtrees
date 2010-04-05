<?php
/**
 * Top-of-page menu for Minimal theme
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (c) 2002 to 2008  John Finlay and others.  All rights reserved.
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
<table width="100%" border="1" cellspacing="0">
	<tr>
		<?php
		$menu = $menubar->getGedcomMenu();
		if ($menu->link != "") {
			print "\t<td width=\"7%\" valign=\"top\">\n";
			$menu->addIcon(null);
			$menu->printMenu();
			print "</td>";
		}

		$menu = $menubar->getMygedviewMenu();
		if ($menu->link != "") {
			print "\t<td width=\"7%\" valign=\"top\">\n";
			$menu->addIcon(null);
			$menu->printMenu();
			print "</td>";
		}

		$menu = $menubar->getChartsMenu();
		if ($menu->link != "") {
			print "\t<td width=\"7%\" valign=\"top\">\n";
			$menu->addIcon(null);
			$menu->printMenu();
			print "</td>";
		}

		$menu = $menubar->getListsMenu();
		if ($menu->link != "") {
			print "\t<td width=\"7%\" valign=\"top\">\n";
			$menu->addIcon(null);
			$menu->printMenu();
			print "</td>";
		}

		$menu = $menubar->getCalendarMenu();
		if ($menu->link != "") {
			print "\t<td width=\"7%\" valign=\"top\">\n";
			$menu->addIcon(null);
			$menu->printMenu();
			print "</td>";
		}

		$menu = $menubar->getReportsMenu();
		if ($menu->link != "") {
			print "\t<td width=\"7%\" valign=\"top\">\n";
			$menu->addIcon(null);
			$menu->printMenu();
			print "</td>";
		}

		$menu = $menubar->getSearchMenu();
		if ($menu->link != "") {
			print "\t<td width=\"7%\" valign=\"top\">\n";
			$menu->addIcon(null);
			$menu->printMenu();
			print "</td>";
		}

		$menu = $menubar->getOptionalMenu();
		if ($menu->link != "") {
			print "\t<td width=\"7%\" valign=\"top\">\n";
			$menu->addIcon(null);
			$menu->printMenu();
			print "</td>";
		}

		$menus = $menubar->getModuleMenus();
		foreach($menus as $m=>$menu) {
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->addIcon(null);
				$menu->printMenu();
				print "\t</td>\n";
			}
		}

		$menu = $menubar->getHelpMenu();
		if ($menu->link != "") {
			print "\t<td width=\"7%\" valign=\"top\">\n";
			$menu->addIcon(null);
			$menu->printMenu();
			print "</td>";
		}
	?>
	</tr>
</table>
<img src="<?php print $WT_IMAGE_DIR."/".$WT_IMAGES["hline"]["other"]; ?>" width="100%" height="3" alt="" />
</div>
<!-- close div for div id="header" -->

<?php print "<div id=\"content\">\n"; ?>
