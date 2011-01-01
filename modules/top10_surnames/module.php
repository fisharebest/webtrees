<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class top10_surnames_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return WT_I18N::translate('Top surnames');
	}

	// Extend class WT_Module
	public function getDescription() {
		return WT_I18N::translate('This block displays the most frequently occurring surnames in the database. The actual number of surnames shown in this block is configurable. Using the GEDCOM administration function, you can also configure names to remove from this list.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype, $WT_IMAGES, $SURNAME_LIST_STYLE;

		$COMMON_NAMES_REMOVE=get_gedcom_setting(WT_GED_ID, 'COMMON_NAMES_REMOVE');
		$COMMON_NAMES_THRESHOLD=get_gedcom_setting(WT_GED_ID, 'COMMON_NAMES_THRESHOLD');

		$num=get_block_setting($block_id, 'num', 10);
		$infoStyle=get_block_setting($block_id, 'infoStyle', 'table');
		$block=get_block_setting($block_id, 'block', false);
		if ($cfg) {
			foreach (array('num', 'infoStyle', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name=$cfg[$name];
				}
			}
		}

		// This next function is a bit out of date, and doesn't cope well with surname variants
		$top_surnames=get_top_surnames(WT_GED_ID, $COMMON_NAMES_THRESHOLD, $num);

		// Remove names found in the "Remove Names" list
		if ($COMMON_NAMES_REMOVE) {
			foreach (preg_split("/[,; ]+/", $COMMON_NAMES_REMOVE) as $delname) {
				unset($top_surnames[$delname]);
				unset($top_surnames[utf8_strtoupper($delname)]);
			}
		}

		$all_surnames=array();
		$i=0;
		foreach (array_keys($top_surnames) as $top_surname) {
			$all_surnames=array_merge($all_surnames, get_indilist_surns($top_surname, '', false, false, WT_GED_ID));
			if (++$i == $num) break;
		}
		if ($i < $num) $num=$i;
		$id=$this->getName().$block_id;
		$title='';
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			if ($ctype=="gedcom") {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id={$block_id}', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
			$title .= "<img class=\"adminicon\" src=\"".$WT_IMAGES["admin"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".WT_I18N::translate('Configure')."\" /></a>";
		}
		// I18N: There are separate lists of male/female names, containing %d names each
		$title .= WT_I18N::plural('Top surname', 'Top %d surnames', $num, $num);
		$title .= help_link('top_surnames', $this->getName());
		switch ($infoStyle) {
		case 'tagcloud':
			uksort($all_surnames,'utf8_strcasecmp');
			$content=format_surname_tagcloud($all_surnames, 'indilist', true);
			break;
		case 'list':
			uasort($all_surnames,array('top10_surnames_WT_Module', 'top_surname_sort'));
			$content=format_surname_list($all_surnames, '1', true);
			break;
		case 'array':
			uasort($all_surnames,array('top10_surnames_WT_Module', 'top_surname_sort'));
			$content=format_surname_list($all_surnames, '2', true);
			break;
		case 'table':
		default:
			uasort($all_surnames, array('top10_surnames_WT_Module', 'top_surname_sort'));
			$content=format_surname_table($all_surnames, 'indilist');
			break;
		}

		if ($template) {
			if ($block) {
				require WT_THEME_DIR.'templates/block_small_temp.php';
			} else {
				require WT_THEME_DIR.'templates/block_main_temp.php';
			}
		} else {
			return $content;
		}
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
		if (safe_POST_bool('save')) {
			set_block_setting($block_id, 'num',    safe_POST_integer('num', 1, 10000, 10));
			set_block_setting($block_id, 'infoStyle', safe_POST('infoStyle', array('list', 'array', 'table', 'tagcloud'), 'table'));
			set_block_setting($block_id, 'block',  safe_POST_bool('block'));
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$num=get_block_setting($block_id, 'num', 10);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Number of items to show');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="num" size="2" value="', $num, '" />';
		echo '</td></tr>';

		$infoStyle=get_block_setting($block_id, 'infoStyle', 'table');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Presentation style');
		echo '</td><td class="optionbox">';
		echo select_edit_control('infoStyle', array('list'=>WT_I18N::translate('bullet list'), 'array'=>WT_I18N::translate('compact list'), 'table'=>WT_I18N::translate('table'), 'tagcloud'=>WT_I18N::translate('tag cloud')), null, $infoStyle, '');
		echo '</td></tr>';

		$block=get_block_setting($block_id, 'block', false);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ WT_I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}

	public static function top_surname_sort($a, $b) {
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
}
