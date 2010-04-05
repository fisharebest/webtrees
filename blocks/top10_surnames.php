<?php
/**
 * Top 10 Surnames Block
 *
 * This block will show the top 10 surnames that occur most frequently in the active gedcom
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
 * @version $Id$
 * @package webtrees
 * @subpackage Blocks
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_TOP10SURNAMES_PHP', '');

$WT_BLOCKS["print_block_name_top10"]["name"]		= i18n::translate('Top 10 Surnames');
$WT_BLOCKS["print_block_name_top10"]["descr"]		= i18n::translate('This block shows a table of the 10 most frequently occurring surnames in the database.  The actual number of surnames shown in this block is configurable.  You can configure the GEDCOM to remove names from this list.');
$WT_BLOCKS["print_block_name_top10"]["canconfig"]	= true;
$WT_BLOCKS["print_block_name_top10"]["config"]		= array(
	"cache"=>7,
	"num"=>10,
	);

function top_surname_sort($a, $b) {
	$counta=0;
	foreach ($a as $x) {
		$counta+=count($x);
	}
	$countb=0;
	foreach ($b as $x) {
		$countb+=count($x);
	}
	return $countb - $counta;
}

function print_block_name_top10($block=true, $config="", $side, $index) {
	global $COMMON_NAMES_ADD, $COMMON_NAMES_REMOVE, $COMMON_NAMES_THRESHOLD, $WT_BLOCKS, $ctype, $WT_IMAGES, $WT_IMAGE_DIR, $SURNAME_LIST_STYLE;

	if (empty($config)) {
		$config=$WT_BLOCKS["print_block_name_top10"]["config"];
	}

	// This next function is a bit out of date, and doesn't cope well with surname variants
	$top_surnames=get_top_surnames(WT_GED_ID, 1, $config["num"]);

	$all_surnames=array();
	foreach (array_keys($top_surnames) as $top_surname) {
		$all_surnames=array_merge($all_surnames, get_indilist_surns($top_surname, '', false, false, WT_GED_ID));
	}

	// Insert from the "Add Names" list if not already in there
	if ($COMMON_NAMES_ADD) {
		foreach (preg_split('/[,; ]+/', $COMMON_NAMES_ADD) as $addname) {
			$ADDNAME=utf8_strtoupper($addname);
			if (isset($all_surnames[$ADDNAME])) {
				$SURNAME=$ADDNAME;
				foreach (array_keys($all_surnames[$ADDNAME]) as $surname) {
					if ($SURNAME!=$surname && $SURNAME==utf8_strtoupper($surname)) {
						$all_surnames[$ADDNAME][$SURNAME]=$all_surnames[$ADDNAME][$surname];
						unset ($all_surnames[$ADDNAME][$surname]);
					}
				}
				if (isset($all_surnames[$ADDNAME][$SURNAME])) {
					$n=count($all_surnames[$ADDNAME][$SURNAME]);
					$all_surnames[$ADDNAME][$SURNAME]=array_fill(0, max($n, $COMMON_NAMES_THRESHOLD), true);
				} else {
					$all_surnames[$ADDNAME][$SURNAME]=array_fill(0, $COMMON_NAMES_THRESHOLD, true);
				}
			} else {
				$all_surnames[$ADDNAME][$ADDNAME]=array_fill(0, $COMMON_NAMES_THRESHOLD, true);
			}
		}
	}

	// Remove names found in the "Remove Names" list
	if ($COMMON_NAMES_REMOVE) {
		foreach (preg_split("/[,; ]+/", $COMMON_NAMES_REMOVE) as $delname) {
			unset($all_surnames[utf8_strtoupper($delname)]);
		}
	}

	$id="top10surnames";
	$title='';
	if ($WT_BLOCKS["print_block_name_top10"]["canconfig"]) {
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
	// I18N: There are separate lists of male/female names, containing %d names each
	$title .= i18n::plural('Top surname', 'Top %d surnames', $config['num'], $config['num']);
	$title .= help_link('index_common_names');

	switch ($SURNAME_LIST_STYLE) {
	case 'style3':
		uksort($all_surnames,'utf8_strcasecmp');	
		$content=format_surname_tagcloud($all_surnames, 'indilist', true);
		break;
	case 'style2':
	default:
		uasort($all_surnames, "top_surname_sort");	
		$content=format_surname_table($all_surnames, 'indilist');
		break;
	}

	global $THEME_DIR;
	if ($block) {
		require $THEME_DIR.'templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'templates/block_main_temp.php';
	}
}

function print_block_name_top10_config($config) {
	global $ctype, $WT_BLOCKS;
	if (empty($config)) $config = $WT_BLOCKS["print_block_name_top10"]["config"];
	if (!isset($config["cache"])) $config["cache"] = $WT_BLOCKS["print_block_name_top10"]["config"]["cache"];
?>
	<tr>
		<td class="descriptionbox wrap width33"><?php print i18n::translate('Number of items to show') ?></td>
	<td class="optionbox">
		<input type="text" name="num" size="2" value="<?php print $config["num"]; ?>" />
	</td></tr>

	<?php

	// Cache file life
	if ($ctype=="gedcom") {
  		print "<tr><td class=\"descriptionbox wrap width33\">";
			print i18n::translate('Cache file life');
			print help_link('cache_life');
		print "</td><td class=\"optionbox\">";
			print "<input type=\"text\" name=\"cache\" size=\"2\" value=\"".$config["cache"]."\" />";
		print "</td></tr>";
	}
}
?>
