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

class top10_pageviews_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Most Viewed Items');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('This block will show the 10 records that have been viewed the most.  This block requires that Hit Counters be enabled in the GEDCOM configuration settings.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $ctype, $TBLPREFIX, $INDEX_DIRECTORY, $WT_IMAGES, $WT_IMAGE_DIR, $SHOW_COUNTER, $SHOW_SOURCES, $TEXT_DIRECTION;

		$count_placement=get_block_setting($block_id, 'count_placement', 'before');
		$num=get_block_setting($block_id, 'num', 10);
		$block=get_block_setting($block_id, 'block', false);

		$id=$this->getName().$block_id;
		$title='';
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id={$block_id}', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
			$title .= "<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
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
				$num
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
						if ($count_placement=='before') {
							$content .= '<td dir="ltr" align="right">['.$count.']</td>';
						}
						$content .= '<td class="name2" ><a href="'.encode_url($record->getLinkUrl()).'">'.PrintReady($record->getFullName()).'</a></td>';
						if ($count_placement=='after') {
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

	// Implement class WT_Module_Block
	public function canLoadAjax() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
		if (safe_POST_bool('save')) {
			set_block_setting($block_id, 'num',  safe_POST_integer('num', 1, 10000));
			set_block_setting($block_id, 'count_placement',  safe_POST('count_placement', array('before', 'after'), 'before'));
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

		$count_placement=get_block_setting($block_id, 'count_placement', 'left');
		print "<tr><td class=\"descriptionbox wrap width33\">";
		print i18n::translate('Place counts before or after name?');
		print "</td><td class=\"optionbox\">";
		echo select_edit_control('count_placement', array('before'=>i18n::translate('before'), 'after'=>i18n::translate('after')), null, $count_placement, '');
		echo '</td></tr>';
	}
}
