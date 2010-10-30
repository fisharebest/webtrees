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

class todo_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('&quot;To Do&quot; tasks');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The To Do block lists all outstanding _TODO facts in the database.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype, $WT_IMAGES, $THEME_DIR;

		$show_unassigned=get_block_setting($block_id, 'show_unassigned', true);
		$show_other     =get_block_setting($block_id, 'show_other',      true);
		$show_future    =get_block_setting($block_id, 'show_future',     true);
		$block          =get_block_setting($block_id, 'block',           true);
		if ($cfg) {
			foreach (array('show_unassigned', 'show_other', 'show_future', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name=$cfg[$name];
				}
			}
		}

		$id=$this->getName().$block_id;
		$title='';
		if ($ctype=='gedcom' && WT_USER_GEDCOM_ADMIN || $ctype=='user' && WT_USER_ID) {
			if ($ctype=='gedcom') {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
			$title.="<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id={$block_id}', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
			$title.="<img class=\"adminicon\" src=\"".$WT_IMAGES["admin"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		}
		$title.=i18n::translate('&quot;To Do&quot; tasks').help_link('todo', $this->getName());
		$content='';

		require_once WT_ROOT.'js/sorttable.js.htm';
		require_once WT_ROOT.'includes/classes/class_gedcomrecord.php';

		$table_id = 'ID'.floor(microtime()*1000000); // sorttable requires a unique ID
		$content .= '<table id="'.$table_id.'" class="sortable list_table center">';
		$content .= '<tr>';
		$content .= '<th class="list_label">'.translate_fact('DATE').'</th>';
		$content .= '<th class="list_label">'.i18n::translate('Record').'</th>';
		if ($show_unassigned || $show_other) {
			$content .= '<th class="list_label">'.i18n::translate('User name').'</th>';
		}
		$content .= '<th class="list_label">'.translate_fact('TEXT').'</th>';
		$content .= '</tr>';

		$found=false;
		$end_jd=$show_future ? 99999999 : WT_CLIENT_JD;
		foreach (get_calendar_events(0, $end_jd, '_TODO', WT_GED_ID) as $todo) {
			$record=GedcomRecord::getInstance($todo['id']);
			if ($record && $record->canDisplayDetails()) {
				$user_name=get_gedcom_value('_WT_USER', 2, $todo['factrec']);
				if ($user_name==WT_USER_NAME || !$user_name && $show_unassigned || $user_name && $show_other) {
					$content.='<tr valign="top">';
					$content.='<td class="list_value_wrap">'.str_replace('<a', '<a name="'.$todo['date']->MinJD().'"', $todo['date']->Display(false)).'</td>';
					$name=$record->getListName();
					$content.='<td class="list_value_wrap" align="'.get_align(WT_GEDCOM).'"><a href="'.$record->getHtmlUrl().'">'.PrintReady($name).'</a></td>';
					if ($show_unassigned || $show_other) {
						$content.='<td class="list_value_wrap">'.$user_name.'</td>';
					}
					$text=get_gedcom_value('_TODO', 1, $todo['factrec']);
					$content.='<td class="list_value_wrap" align="'.get_align($text).'">'.PrintReady($text).'</td>';
					$content.='</tr>';
					$found=true;
				}
			}
		}

		$content .= '</table>';
		if (!$found) {
			$content.='<p>'.i18n::translate('There are no &quot;To Do&quot; tasks.').'</p>';
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
			set_block_setting($block_id, 'show_other',      safe_POST_bool('show_other'));
			set_block_setting($block_id, 'show_unassigned', safe_POST_bool('show_unassigned'));
			set_block_setting($block_id, 'show_future',     safe_POST_bool('show_future'));
			set_block_setting($block_id, 'block',  safe_POST_bool('block'));
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$show_other=get_block_setting($block_id, 'show_other', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Show other users\' tasks'), help_link('todo_show_other', $this->getName());
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('show_other', $show_other);
		echo '</td></tr>';

		$show_unassigned=get_block_setting($block_id, 'show_unassigned', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Show unassigned tasks'), help_link('todo_show_unassigned', $this->getName());
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('show_unassigned', $show_unassigned);
		echo '</td></tr>';

		$show_future=get_block_setting($block_id, 'show_future', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Show future tasks'), help_link('todo_show_future', $this->getName());
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('show_future', $show_future);
		echo '</td></tr>';

		$block=get_block_setting($block_id, 'block', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ i18n::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
