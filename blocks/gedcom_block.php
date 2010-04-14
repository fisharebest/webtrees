<?php
/**
 * Gedcom Welcome Block
 *
 * This block prints basic information about the active gedcom
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

define('WT_GEDCOM_BLOCK_PHP', '');

$WT_BLOCKS['print_gedcom_block']=array(
	'name'=>i18n::translate('GEDCOM Welcome'),
	'type'=>'gedcom',
	'descr'=>i18n::translate('The GEDCOM Welcome block works the same as the User Welcome block.  It welcomes the visitor to the site and displays the title of the currently active database as well as the current date and time.'),
	'canconfig'=>false,
	'config'=>array(
		'cache'=>0
	)
);

//-- function to print the gedcom block
function print_gedcom_block($block = true, $config="", $side, $index) {
	global $hitCount, $SHOW_COUNTER;

	$id = "gedcom_welcome";
	$title = PrintReady(get_gedcom_setting(WT_GED_ID, 'title'));
	$content = "<div class=\"center\">";
	$content .= "<br />".format_timestamp(client_time())."<br />\n";
	if ($SHOW_COUNTER)
		$content .=  i18n::translate('Hit Count:')." ".$hitCount."<br />\n";
	$content .=  "\n<br />";
	if (WT_USER_GEDCOM_ADMIN) {
		$content .=  "<a href=\"javascript:;\" onclick=\"window.open('".encode_url("index_edit.php?name=".WT_GEDCOM."&ctype=gedcom")."', '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1'); return false;\">".i18n::translate('Customize this GEDCOM Home Page')."</a><br />\n";
	}
	$content .=  "</div>";

	global $THEME_DIR;
	require $THEME_DIR.'templates/block_main_temp.php';
}
?>
