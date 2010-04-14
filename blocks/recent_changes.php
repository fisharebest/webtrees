<?php
/**
 * Recent Changes Block
 *
 * This block will print a list of recent changes
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

define('WT_RECENT_CHANGES_PHP', '');

$WT_BLOCKS['print_recent_changes']=array(
	'name'=>i18n::translate('Recent Changes'),
	'type'=>'both',
	'descr'=>i18n::translate('The Recent Changes block will list all of the changes that have been made to the database in the last month.  This block can help you stay current with the changes that have been made.  Changes are detected automatically, using the CHAN tag defined in the GEDCOM Standard.'),
	'canconfig'=>true,
	'config'=>array(
		'cache'=>1,
		'days'=>30,
		'hide_empty'=>'no'
	)
);

//-- Recent Changes block
//-- this block prints a list of changes that have occurred recently in your gedcom
function print_recent_changes($block=true, $config="", $side, $index) {
	global $ctype;
	global $WT_IMAGE_DIR, $WT_IMAGES, $WT_BLOCKS;

	$block = true;  // Always restrict this block's height

	if (empty($config)) $config = $WT_BLOCKS["print_recent_changes"]["config"];
	if ($config["days"]<1) $config["days"] = 30;
	if (isset($config["hide_empty"])) $HideEmpty = $config["hide_empty"];
	else $HideEmpty = "no";

	$found_facts=get_recent_changes(client_jd()-$config['days']);

// Start output
	if (count($found_facts)==0 and $HideEmpty=="yes") return false;
// Print block header
	$id="recent_changes";
	$title='';
	if ($WT_BLOCKS["print_recent_changes"]["canconfig"]) {
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			if ($ctype=="gedcom") {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('".encode_url("index_edit.php?name={$name}&ctype={$ctype}&action=configure&side={$side}&index={$index}")."', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
			$title .= "<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		}
	}
	$title.=i18n::translate('Recent Changes').help_link('recent_changes');

	$content = "";
// Print block content
	if (count($found_facts)==0) {
		$content .= i18n::translate('There have been no changes within the last %s days.', $config["days"]);
	} else {
		$content .= i18n::translate('Changes made within the last %s days', $config["days"]);
		// sortable table
		require_once WT_ROOT.'includes/functions/functions_print_lists.php';
		ob_start();
		print_changes_table($found_facts);
		$content .= ob_get_clean();
	}

	global $THEME_DIR;
	if ($block) {
		require $THEME_DIR.'templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'templates/block_main_temp.php';
	}
}

function print_recent_changes_config($config) {
	global $ctype, $WT_BLOCKS;
	if (empty($config)) $config = $WT_BLOCKS["print_recent_changes"]["config"];
	if (!isset($config["cache"])) $config["cache"] = $WT_BLOCKS["print_recent_changes"]["config"]["cache"];

	print "<tr><td class=\"descriptionbox wrap width33\">".i18n::translate('Number of days to show')."</td>";?>
	<td class="optionbox">
		<input type="text" name="days" size="2" value="<?php print $config["days"]; ?>" />
	</td></tr>

	<?php
	print "<tr><td class=\"descriptionbox wrap width33\">".i18n::translate('Should this block be hidden when it is empty?')."</td>";?>
	<td class="optionbox">
		<select name="hide_empty">
			<option value="no"<?php if ($config["hide_empty"]=="no") print " selected=\"selected\"";?>><?php print i18n::translate('No'); ?></option>
			<option value="yes"<?php if ($config["hide_empty"]=="yes") print " selected=\"selected\"";?>><?php print i18n::translate('Yes'); ?></option>
		</select>
	</td></tr>
	<tr><td colspan="2" class="optionbox wrap">
		<span class="error"><?php print i18n::translate('If you hide an empty block, you will not be able to change its configuration until it becomes visible by no longer being empty.'); ?></span>
	</td></tr>
	<?php

	// Cache file life
	if ($ctype=="gedcom") {
		echo "<tr><td class=\"descriptionbox wrap width33\">";
		echo i18n::translate('Cache file life'), help_link('cache_life');
		echo "</td><td class=\"optionbox\">";
		echo "<input type=\"text\" name=\"cache\" size=\"2\" value=\"".$config["cache"]."\" />";
		echo "</td></tr>";
	}
}
?>
