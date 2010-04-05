<?php
/**
 * Getting Started Block
 *
 * Prints out a helpful menu to help users get started
 *
 * Admins
 *  - upload a gedcom
 *  - start entering data
 *  - Add a gedcom on the server
 *  - Manage GEDCOMs
 *  - Admin Users
 * Editors
 * Users
 * Visitors
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2008  PGV Development Team.  All rights reserved.
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
 * @version $Id$
 * @package webtrees
 * @subpackage Blocks
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_GETTING_STARTED_BLOCK_PHP', '');



$WT_BLOCKS["getting_started_block"]["name"]		= i18n::translate('Get Started');
$WT_BLOCKS["getting_started_block"]["descr"]		= i18n::translate('The GEDCOM Welcome block works the same as the User Welcome block.  It welcomes the visitor to the site and displays the title of the currently active database as well as the current date and time.');
$WT_BLOCKS["getting_started_block"]["type"]		= "none";
$WT_BLOCKS["getting_started_block"]["canconfig"]	= false;
$WT_BLOCKS["getting_started_block"]["config"]		= array("cache"=>5);

//-- function to print the gedcom block
function getting_started_block($block = true, $config="", $side, $index) {
	$id = "getting_started";
	$title = i18n::translate('Get Started');
	$content = i18n::translate('Choose one of these options to get started using webtrees');
	$content .= '<ul>';
	$content .= '<li style="padding: 5px;"><a class="imenu" href="editconfig_gedcom.php?source=upload_form">'.i18n::translate('Upload a GEDCOM file').'</a></li>';
	$content .= '<li style="padding: 5px;"><a class="imenu" href="editconfig_gedcom.php?source=add_form">'.i18n::translate('Add a GEDCOM from a file location').'</a></li>';
	$content .= '<li style="padding: 5px;"><a class="imenu" href="editgedcoms.php">'.i18n::translate('Manage GEDCOMs and edit Privacy').'</a></li>';
	$content .= '<li style="padding: 5px;"><a class="imenu" href="useradmin.php">'.i18n::translate('User administration').'</a></li>';
	$content .= '<li style="padding: 5px;"><a class="imenu" href="admin.php">'.i18n::translate('Admin').'</a></li>';
	$content .= '</ul>';

	global $THEME_DIR;
	if ($block) {
		require $THEME_DIR.'/templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'/templates/block_main_temp.php';
	}
}
?>
