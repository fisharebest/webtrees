<?php
/**
 * User Welcome Block
 *
 * This block will print basic information and links for the user.
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

define('WT_USER_WELCOME_PHP', '');

$WT_BLOCKS['print_welcome_block']=array(
	'name'=>i18n::translate('User Welcome'),
	'descr'=>i18n::translate('The User Welcome block shows the user the current date and time, quick links to modify his account or go to his own Pedigree chart, and a link to customize his My Page.'),
	'type'=>'user',
	'canconfig'=>false,
	'config'=>array(
		'cache'=>0
	)
);

//-- function to print the welcome block
function print_welcome_block($block=true, $config="", $side, $index) {
	global $WT_IMAGE_DIR, $WT_IMAGES;

	$id="user_welcome";
	$title = i18n::translate('Welcome')." ".getUserFullName(WT_USER_ID);

	$content = "<table class=\"blockcontent\" cellspacing=\"0\" cellpadding=\"0\" style=\" width: 100%; direction:ltr;\"><tr>";
	$content .= "<td class=\"tab_active_bottom\" colspan=\"3\" ></td></tr><tr>";
	if (get_user_setting(WT_USER_ID, 'editaccount')=='Y') {
		$content .= "<td class=\"center details2\" style=\" width: 33%; clear: none; vertical-align: top; margin-top: 2px;\"><a href=\"edituser.php\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["mygedview"]["small"]."\" border=\"0\" alt=\"".i18n::translate('My Account')."\" title=\"".i18n::translate('My Account')."\" /><br />".i18n::translate('My Account')."</a></td>";
	}
	if (WT_USER_GEDCOM_ID) {
		$content .= "<td class=\"center details2\" style=\" width: 34%; clear: none; vertical-align: top; margin-top: 2px;\"><a href=\"".encode_url("pedigree.php?rootid=".WT_USER_GEDCOM_ID)."\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["pedigree"]["small"]."\" border=\"0\" alt=\"".i18n::translate('My Pedigree')."\" title=\"".i18n::translate('My Pedigree')."\" /><br />".i18n::translate('My Pedigree')."</a></td>";
		$content .= "<td class=\"center details2\" style=\" width: 33%; clear: none; vertical-align: top; margin-top: 2px;\"><a href=\"".encode_url("individual.php?pid=".WT_USER_GEDCOM_ID)."\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["indis"]["small"]."\" border=\"0\" alt=\"".i18n::translate('My Individual Record')."\" title=\"".i18n::translate('My Individual Record')."\" /><br />".i18n::translate('My Individual Record')."</a></td>";
	}
	$content .= "</tr><tr><td class=\"center\" colspan=\"3\">";
	$content .= "<a href=\"javascript:;\" onclick=\"window.open('".encode_url("index_edit.php?name=".WT_USER_NAME."&ctype=user")."', '_blank', 'top=50,left=10,width=705,height=355,scrollbars=1,resizable=1');\">".i18n::translate('Customize My Page')."</a>";
	$content .= help_link('mygedview_customize');
	$content .= "<br />".format_timestamp(client_time());
	$content .= "</td>";
	$content .= "</tr></table>";

	global $THEME_DIR;
	if ($block) {
		require $THEME_DIR.'templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'templates/block_main_temp.php';
	}
}
?>
