<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
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
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class user_welcome_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('My page');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the "My page" module */ WT_I18N::translate('A greeting message and useful links for a user.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $WT_IMAGES, $hitCount, $SHOW_COUNTER;

		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		$title='';
		$title .=/* I18N: A greeting; %s is the user's name */ WT_I18N::translate('Welcome %s', getUserFullName(WT_USER_ID));
		$content = "<table style=\"margin:auto;\"><tr>";
		$content .= "<td class=\"tab_active_bottom\" colspan=\"3\" ></td></tr><tr>";
		if (get_user_setting(WT_USER_ID, 'editaccount')) {
			$content .= "<td class=\"center details2\" style=\"width:33%; clear:none; vertical-align:top; margin-top:2px;\"><a href=\"edituser.php\"><img class=\"block\" src=\"".$WT_IMAGES["mypage"]."\" alt=\"".WT_I18N::translate('My account')."\"><br>".WT_I18N::translate('My account')."</a></td>";
		}
		if (WT_USER_GEDCOM_ID) {
			$content .= "<td class=\"center details2\" style=\"width:33%; clear:none; vertical-align:top; margin-top:2px;\"><a href=\"pedigree.php?rootid=".WT_USER_GEDCOM_ID."&amp;ged=".WT_GEDURL."\"><img class=\"block\" src=\"".$WT_IMAGES["pedigree"]."\" alt=\"".WT_I18N::translate('My pedigree')."\" title=\"".WT_I18N::translate('My pedigree')."\"><br>".WT_I18N::translate('My pedigree')."</a></td>";
			$content .= "<td class=\"center details2\" style=\"width:33%; clear:none; vertical-align:top; margin-top:2px;\"><a href=\"individual.php?pid=".WT_USER_GEDCOM_ID."&amp;ged=".WT_GEDURL."\"><img class=\"block\" src=\"".$WT_IMAGES["indis"]."\" alt=\"".WT_I18N::translate('My individual record')."\"><br>".WT_I18N::translate('My individual record')."</a></td>";
		}
		$content .= "</tr><tr><td class=\"center\" colspan=\"3\">";
		$content .= "<a href=\"#\" onclick=\"window.open('index_edit.php?name=".WT_USER_NAME."&amp;ctype=user"."', '_blank', indx_window_specs);\">".WT_I18N::translate('Change the blocks on this page')."</a>";
		$content .= "<br>".format_timestamp(client_time())."<br>";
		if ($SHOW_COUNTER)
			$content .=  WT_I18N::translate('Hit Count:')." ".$hitCount."<br>";
		$content .= "</td></tr></table>";

		if ($template) {
			require WT_THEME_DIR.'templates/block_main_temp.php';
		} else {
			return $content;
		}
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
