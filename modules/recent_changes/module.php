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

class recent_changes_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Recent Changes');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The Recent Changes block will list all of the changes that have been made to the database in the last month.  This block can help you stay current with the changes that have been made.  Changes are detected automatically, using the CHAN tag defined in the GEDCOM Standard.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $ctype, $WT_IMAGE_DIR, $WT_IMAGES, $THEME_DIR;

		$days      =get_block_setting($block_id, 'days', 30);
		$hide_empty=get_block_setting($block_id, 'hide_empty', false);

		$found_facts=get_recent_changes(client_jd()-$days);

		if (empty($found_facts) && $hide_empty) {
			return;
		}
		// Print block header
		$id=$this->getName().$block_id;
		$title='';
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=='user') {
			if ($ctype=="gedcom") {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('".encode_url("index_edit.php?action=configure&block_id={$block_id}")."', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
			$title .= "<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		}
		$title.=i18n::translate('Recent Changes').help_link('recent_changes');

		$content = "";
	// Print block content
		if (count($found_facts)==0) {
			$content .= i18n::translate('There have been no changes within the last %s days.', $days);
		} else {
			$content .= i18n::translate('Changes made within the last %s days', $days);
			// sortable table
			require_once WT_ROOT.'includes/functions/functions_print_lists.php';
			ob_start();
			print_changes_table($found_facts);
			$content .= ob_get_clean();
		}

		$block=get_block_setting($block_id, 'block', true);
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
		global $DAYS_TO_SHOW_LIMIT;

		if (safe_POST_bool('save')) {
			set_block_setting($block_id, 'days', safe_POST_integer('days', 1, $DAYS_TO_SHOW_LIMIT, $DAYS_TO_SHOW_LIMIT));
			set_block_setting($block_id, 'hide_empty', safe_POST_bool('hide_empty'));
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$days=get_block_setting($block_id, 'days', $DAYS_TO_SHOW_LIMIT);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Number of days to show'), help_link('days_to_show');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="days" size="2" value="', $days, '" />';
		echo '</td></tr>';

		$hide_empty=get_block_setting($block_id, 'hide_empty', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Should this block be hidden when it is empty?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('hide_empty', $hide_empty);
		echo '</td></tr>';
		echo '<tr><td colspan="2" class="optionbox wrap">';
		echo '<span class="error">', i18n::translate('If you hide an empty block, you will not be able to change its configuration until it becomes visible by no longer being empty.'), '</span>';
		echo '</td></tr>';
	}
}
