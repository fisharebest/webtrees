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

class user_welcome_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('User Welcome');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The User Welcome block shows the user the current date and time, quick links to modify his account or go to his own Pedigree chart, and a link to customize his My Page.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $WT_IMAGE_DIR, $WT_IMAGES, $THEME_DIR;

		$id=$this->getName().$block_id;
		$title=i18n::translate('Welcome')." ".getUserFullName(WT_USER_ID);

		$content = "<table class=\"blockcontent\" cellspacing=\"0\" cellpadding=\"0\" style=\" width: 100%; direction:ltr;\"><tr>";
		$content .= "<td class=\"tab_active_bottom\" colspan=\"3\" ></td></tr><tr>";
		if (get_user_setting(WT_USER_ID, 'editaccount')=='Y') {
			$content .= "<td class=\"center details2\" style=\" width: 33%; clear: none; vertical-align: top; margin-top: 2px;\"><a href=\"edituser.php\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["mygedview"]["small"]."\" border=\"0\" alt=\"".i18n::translate('My Account')."\" title=\"".i18n::translate('My Account')."\" /><br />".i18n::translate('My Account')."</a></td>";
		}
		if (WT_USER_GEDCOM_ID) {
			$content .= "<td class=\"center details2\" style=\" width: 33%; clear: none; vertical-align: top; margin-top: 2px;\"><a href=\"".encode_url("pedigree.php?rootid=".WT_USER_GEDCOM_ID)."\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["pedigree"]["small"]."\" border=\"0\" alt=\"".i18n::translate('My Pedigree')."\" title=\"".i18n::translate('My Pedigree')."\" /><br />".i18n::translate('My Pedigree')."</a></td>";
			$content .= "<td class=\"center details2\" style=\" width: 33%; clear: none; vertical-align: top; margin-top: 2px;\"><a href=\"".encode_url("individual.php?pid=".WT_USER_GEDCOM_ID)."\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["indis"]["small"]."\" border=\"0\" alt=\"".i18n::translate('My Individual Record')."\" title=\"".i18n::translate('My Individual Record')."\" /><br />".i18n::translate('My Individual Record')."</a></td>";
		}
		$content .= "</tr><tr><td class=\"center\" colspan=\"3\">";
		$content .= "<a href=\"javascript:;\" onclick=\"window.open('".encode_url("index_edit.php?name=".WT_USER_NAME."&ctype=user")."', '_blank', 'top=50,left=10,width=705,height=355,scrollbars=1,resizable=1');\">".i18n::translate('Customize My Page')."</a>";
		$content .= help_link('mygedview_customize');
		$content .= "<br />".format_timestamp(client_time());
		$content .= "</td></tr></table>";

		require $THEME_DIR.'templates/block_main_temp.php';
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return false;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
	}
}
