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

class top10_givnnames_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Top 10 Given Names');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('This block shows a table of the 10 most frequently occurring given names in the database.  The actual number of given names shown in this block is configurable.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $TEXT_DIRECTION, $WT_BLOCKS, $ctype, $WT_IMAGES, $WT_IMAGE_DIR;

		$num=get_block_setting($block_id, 'num', 10);
		$infoStyle=get_block_setting($block_id, 'infoStyle', 'table');
		$showUnknown=get_block_setting($block_id, 'showUnknown', true);
		$block=get_block_setting($block_id, 'block', false);

		require_once WT_ROOT.'includes/classes/class_stats.php';
		$stats=new Stats(WT_GEDCOM);

		$id=$this->getName().$block_id;
		$title='';
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id={$block_id}', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
			$title .= "<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		}
		// I18N: There are separate lists of male/female names, containing %d names each
		$title .= i18n::plural('Top Given Name', 'Top %d Given Names', $num, $num);
		$title .= help_link('index_common_given_names');

		$content = '<div class="normal_inner_block">';
		//Select List or Table
		switch ($infoStyle) {
		case "list":	// Output style 1:  Simple list style.  Better suited to left side of page.
			if ($TEXT_DIRECTION=='ltr') $padding = 'padding-left: 15px';
			else $padding = 'padding-right: 15px';
			$params=array(1,$num,'rcount');
			//List Female names
			$totals=$stats->commonGivenFemaleTotals($params);
			if ($totals) {
				$content.='<b>'.i18n::translate('Female').'</b><div class="wrap" style="'.$padding.'">'.$totals.'</div><br />';
			}
			//List Male names
			$totals=$stats->commonGivenMaleTotals($params);
			if ($totals) {
				$content.='<b>'.i18n::translate('Male').'</b><div class="wrap" style="'.$padding.'">'.$totals.'</div><br />';
			}
			//List Unknown names
			$totals=$stats->commonGivenUnknownTotals($params);
			if ($totals && $showUnknown) {
				$content.='<b>'.i18n::translate('unknown').'</b><div class="wrap" style="'.$padding.'">'.$totals.'</div><br />';
			}
			break;
		case "table":	// Style 2: Tabular format.  Narrow, 2 or 3 column table, good on right side of page
			$params=array(1,$num,'rcount');
			$content.='<table class="center"><tr valign="top"><td>'.$stats->commonGivenFemaleTable($params).'</td>';
			$content.='<td>'.$stats->commonGivenMaleTable($params).'</td>';
			if ($showUnknown) {
				$content.='<td>'.$stats->commonGivenUnknownTable($params).'</td>';
			}
			$content.='</tr></table>';
			break;
		}
		$content .=  "</div>";

		global $THEME_DIR;
		$block=get_block_setting($block_id, 'block', false);
		if ($block) {
			require $THEME_DIR.'templates/block_small_temp.php';
		} else {
			require $THEME_DIR.'templates/block_main_temp.php';
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
			set_block_setting($block_id, 'num', safe_POST_integer('num', 1, 10000));
			set_block_setting($block_id, 'infoStyle', safe_POST('infoStyle', array('list', 'table'), 'table'));
			set_block_setting($block_id, 'showUnknown', safe_POST_bool('showUnknown'));
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
		echo i18n::translate('Presentation Style'), help_link('style');
		echo '</td><td class="optionbox">';
		echo select_edit_control('infoStyle', array('list'=>i18n::translate('List'), 'table'=>i18n::translate('Table')), null, $infoStyle, '');
		echo '</td></tr>';

		$showUnknown=get_block_setting($block_id, 'showUnknown', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Show unknown gender'), help_link('showUnknown');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('showUnknown', $showUnknown);
		echo '</td></tr>';

		$block=get_block_setting($block_id, 'block', false);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
