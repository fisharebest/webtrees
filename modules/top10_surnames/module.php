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
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';

class top10_surnames_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Top Surnames');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('This block shows a table of the most frequently occurring surnames in the database.  The actual number of surnames shown is configurable.  You can configure the GEDCOM to remove names from this list.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true) {
		global $ctype, $WT_IMAGES, $SURNAME_LIST_STYLE, $THEME_DIR;
		
		$COMMON_NAMES_REMOVE=get_gedcom_setting(WT_GED_ID, 'COMMON_NAMES_REMOVE');
		$COMMON_NAMES_THRESHOLD=get_gedcom_setting(WT_GED_ID, 'COMMON_NAMES_THRESHOLD');
		
		$num=get_block_setting($block_id, 'num', 10);
		$infoStyle=get_block_setting($block_id, 'infoStyle', 'table');
		$block=get_block_setting($block_id, 'block', false);

		// This next function is a bit out of date, and doesn't cope well with surname variants
		$top_surnames=get_top_surnames(WT_GED_ID, $COMMON_NAMES_THRESHOLD, '');

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

		$id=$this->getName().$block_id;
		$title='';
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			if ($ctype=="gedcom") {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('".encode_url("index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id={$block_id}")."', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
			$title .= "<img class=\"adminicon\" src=\"".$WT_IMAGES["admin"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		}
		// I18N: There are separate lists of male/female names, containing %d names each
		$title .= i18n::plural('Top surname', 'Top %d surnames', $num, $num);
		$title .= help_link('top10_surnames', $this->getName());
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
				require $THEME_DIR.'templates/block_small_temp.php';
			} else {
				require $THEME_DIR.'templates/block_main_temp.php';
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
		echo i18n::translate('Number of items to show');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="num" size="2" value="', $num, '" />';
		echo '</td></tr>';

		$infoStyle=get_block_setting($block_id, 'infoStyle', 'table');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Presentation style'), help_link('style', $this->getName());
		echo '</td><td class="optionbox">';
		echo select_edit_control('infoStyle', array('list'=>i18n::translate('list'), 'array'=>i18n::translate('array'), 'table'=>i18n::translate('table'), 'tagcloud'=>i18n::translate('tag cloud')), null, $infoStyle, '');
		echo '</td></tr>';

		$block=get_block_setting($block_id, 'block', false);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Add a scrollbar when block contents grow'), help_link('scrollbars');
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
?>