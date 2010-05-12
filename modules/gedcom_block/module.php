<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2010 John Finlay
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
 * @version $Id: class_media.php 5451 2009-05-05 22:15:34Z fisharebest $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';

class gedcom_block_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('GEDCOM Welcome');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The GEDCOM Welcome block works the same as the User Welcome block.  It welcomes the visitor to the site and displays the title of the currently active database as well as the current date and time.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $hitCount, $SHOW_COUNTER, $THEME_DIR;

		$id=$this->getName().$block_id;
		$title=get_gedcom_setting(WT_GED_ID, 'title');
		$content = "<div class=\"center\">";
		$content .= "<br />".format_timestamp(client_time())."<br />\n";
		if ($SHOW_COUNTER)
			$content .=  i18n::translate('Hit Count:')." ".$hitCount."<br />\n";
		$content .=  "\n<br />";
		if (WT_USER_GEDCOM_ADMIN) {
			$content .=  "<a href=\"javascript:;\" onclick=\"window.open('".encode_url("index_edit.php?name=".WT_GEDCOM."&ctype=gedcom")."', '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1'); return false;\">".i18n::translate('Customize this GEDCOM Home Page')."</a><br />\n";
		}
		$content .=  "</div>";

		require $THEME_DIR.'templates/block_main_temp.php';
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
	}
}
