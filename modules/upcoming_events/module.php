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
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

class upcoming_events_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Upcoming Events');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The Upcoming Events block shows anniversaries of events that will occur in the near future.  You can configure the amount of detail shown, and the administrator can configure how far into the future this block will look.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true) {
		global $ctype, $WT_IMAGE_DIR, $WT_IMAGES, $THEME_DIR;

		$days=get_block_setting($block_id, 'days', 7);
		$filter=get_block_setting($block_id, 'filter',     true);
		$onlyBDM=get_block_setting($block_id, 'onlyBDM',    false);
		$infoStyle=get_block_setting($block_id, 'infoStyle', 'table');
		$sortStyle=get_block_setting($block_id, 'sortStyle',  'alpha');

		$startjd=WT_CLIENT_JD+1;
		$endjd  =WT_CLIENT_JD+$days;

		// Output starts here
		$id=$this->getName().$block_id;
		$title='';
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
 			$title.="<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id={$block_id}', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
			$title.="<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		}
		$title.= i18n::translate('Upcoming Events').help_link('upcoming_events', $this->getName());

		$content = "";
		switch ($infoStyle) {
		case "list":
			// Output style 1:  Old format, no visible tables, much smaller text.  Better suited to right side of page.
			$content.=print_events_list($startjd, $endjd, $onlyBDM?'BIRT MARR DEAT':'', $filter, 'style1');
			break;
		case "table":
			// Style 2: New format, tables, big text, etc.  Not too good on right side of page
			ob_start();
			$content.=print_events_table($startjd, $endjd, $onlyBDM?'BIRT MARR DEAT':'', $filter, 'style2');
			$content.=ob_get_clean();
			break;
		}

		if ($template) {
			if (get_block_setting($block_id, 'block', true)) {
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
			set_block_setting($block_id, 'days',          safe_POST_integer('days', 1, 30, 7));
			set_block_setting($block_id, 'filter',        safe_POST_bool('filter'));
			set_block_setting($block_id, 'onlyBDM',       safe_POST_bool('onlyBDM'));
			set_block_setting($block_id, 'infoStyle',     safe_POST('infoStyle', array('list', 'table'), 'table'));
			set_block_setting($block_id, 'sortStyle',     safe_POST('sortStyle', array('alpha', 'anniv'), 'alpha'));
			set_block_setting($block_id, 'block',  safe_POST_bool('block'));
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$days=get_block_setting($block_id, 'days', 7);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Number of days to show');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="days" size="2" value="', $days, '" />';
		echo ' <i>', i18n::plural('maximum %d day', 'maximum %d days', 30, 30) ,'</i>';
		echo '</td></tr>';

		$filter=get_block_setting($block_id, 'filter',     true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Show only events of living people?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('filter', $filter);
		echo '</td></tr>';

		$onlyBDM=get_block_setting($block_id, 'onlyBDM',    false);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Show only Births, Deaths, and Marriages?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('onlyBDM', $onlyBDM);
		echo '</td></tr>';

		$infoStyle=get_block_setting($block_id, 'infoStyle', 'table');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Presentation style');
		echo '</td><td class="optionbox">';
		echo select_edit_control('infoStyle', array('list'=>i18n::translate('List'), 'table'=>i18n::translate('Table')), null, $infoStyle, '');
		echo '</td></tr>';

		$sortStyle=get_block_setting($block_id, 'sortStyle',  'alpha');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Sort Style');
		echo '</td><td class="optionbox">';
		echo select_edit_control('sortStyle', array('alpha'=>i18n::translate('Alphabetically'), 'anniv'=>i18n::translate('By Anniversary')), null, $sortStyle, '');
		echo '</td></tr>';

		$block=get_block_setting($block_id, 'block', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
