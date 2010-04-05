<?php
/**
 * Top 10 Pageviews Block
 *
 * This block will show the top 10 records from the Gedcom that have been viewed the most
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

define('WT_TOP10_PAGEVIEWS_PHP', '');

$WT_BLOCKS["top10_pageviews"]["name"]		= i18n::translate('Most Viewed Items');
$WT_BLOCKS["top10_pageviews"]["descr"]		= i18n::translate('This block will show the 10 records that have been viewed the most.  This block requires that Hit Counters be enabled in the GEDCOM configuration settings.');
$WT_BLOCKS["top10_pageviews"]["canconfig"]	= true;
$WT_BLOCKS["top10_pageviews"]["config"]	= array(
	"cache"=>1,
	"num"=>10,
	"count_placement"=>"left"
	);

function top10_pageviews($block=true, $config="", $side, $index) {
	global $TBLPREFIX, $INDEX_DIRECTORY, $WT_BLOCKS, $ctype, $WT_IMAGES, $WT_IMAGE_DIR, $SHOW_COUNTER, $SHOW_SOURCES, $TEXT_DIRECTION;

	if (empty($config)) {
		$config = $WT_BLOCKS["top10_pageviews"]["config"];
	}

	if (isset($config["count_placement"])) {
		$CountSide = $config["count_placement"];
	} else {
		$CountSide = "left";
	}

	$id = "top10hits";
	$title='';
	if ($WT_BLOCKS["top10_pageviews"]["canconfig"]) {
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
	$title .= i18n::translate('Most Viewed Items');
	$title .= help_link('index_top10_pageviews');
	$content = "";

	// if the counter file does not exist then don't do anything
	if (!$SHOW_COUNTER) {
		if (WT_USER_IS_ADMIN) {
			$content .= "<span class=\"error\">".i18n::translate('Hit counters must be enabled in the GEDCOM configuration, Display and Layout section, Hide and Show group.')."</span>";
		}
	} else {
		// load the lines from the file
		$top10=WT_DB::prepareLimit(
			"SELECT page_parameter, page_count".
			" FROM {$TBLPREFIX}hit_counter".
			" WHERE gedcom_id=? AND page_name IN ('individual.php','family.php','source.php','repo.php','note.php','mediaviewer.php')".
			" ORDER BY page_count DESC",
			$config['num']
		)->execute(array(WT_GED_ID))->FetchAssoc();


		if ($top10) {
			if ($block) {
				$content .= "<table width=\"90%\">";
			} else {
				$content .= "<table>";
			}
			foreach ($top10 as $id=>$count) {
				$record=GedcomRecord::getInstance($id);
				if ($record && $record->canDisplayDetails()) {
					$content .= '<tr valign="top">';
					if ($CountSide=='left') {
						$content .= '<td dir="ltr" align="right">['.$count.']</td>';
					}
					$content .= '<td class="name2" ><a href="'.encode_url($record->getLinkUrl()).'">'.PrintReady($record->getFullName()).'</a></td>';
					if ($CountSide=='right') {
						$content .= '<td dir="ltr" align="right">['.$count.']</td>';
					}
					$content .= '</tr>';
				}
			}
			$content .= "</table>";
		} else {
			$content .= "<b>".i18n::translate('There are currently no hits to show.')."</b>";
		}
	}

	global $THEME_DIR;
	if ($block) {
		require $THEME_DIR.'templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'templates/block_main_temp.php';
	}
}

function top10_pageviews_config($config) {
	global $ctype, $WT_BLOCKS;
	if (empty($config)) $config = $WT_BLOCKS["top10_pageviews"]["config"];
	if (!isset($config["cache"])) $config["cache"] = $WT_BLOCKS["top10_pageviews"]["config"]["cache"];

	// Number of items to show
	print "<tr><td class=\"descriptionbox wrap width33\">";
	print i18n::translate('Number of items to show');
	print "</td><td class=\"optionbox\">";
	print "<input type=\"text\" name=\"num\" size=\"2\" value=\"".$config["num"]."\" />";
	print "</td></tr>";

	// Count position
	print "<tr><td class=\"descriptionbox wrap width33\">";
	print i18n::translate('Place counts before or after name?');
	print "</td><td class=\"optionbox\">";
	print "<select name=\"count_placement\">";
	print "<option value=\"left\"";
	if ($config["count_placement"]=="left") print " selected=\"selected\"";
	print ">".i18n::translate('before')."</option>";
	print "<option value=\"right\"";
	if ($config["count_placement"]=="right") print " selected=\"selected\"";
	print ">".i18n::translate('after')."</option>";
	print "</select>";
	print "</td></tr>";

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
