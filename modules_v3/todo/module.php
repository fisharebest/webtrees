<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use Rhumsaa\Uuid\Uuid;
use WT\Auth;

class todo_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module.  Tasks that need further research.  */ WT_I18N::translate('Research tasks');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of “Research tasks” module */ WT_I18N::translate('A list of tasks and activities that are linked to the family tree.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype, $controller;

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

		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && WT_USER_GEDCOM_ADMIN || $ctype === 'user' && Auth::check()) {
			$title = '<i class="icon-admin" title="'.WT_I18N::translate('Configure').'" onclick="modalDialog(\'block_edit.php?block_id='.$block_id.'\', \''.$this->getTitle().'\');"></i>';
		} else {
			$title = '';
		}
		$title .= $this->getTitle().help_link('todo', $this->getName());

		$table_id = Uuid::uuid4(); // create a unique ID

		$controller
			->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
			->addInlineJavascript('
			jQuery("#' . $table_id . '").dataTable({
				dom: \'t\',
				'.WT_I18N::datatablesI18N().',
				autoWidth: false,
				paginate: false,
				lengthChange: false,
				filter: false,
				info: true,
				jQueryUI: true,
				columns: [
					/* 0-DATE */     { visible: false },
					/* 1-Date */     { dataSort: 0 },
					/* 1-Record */   null,
					/* 2-Username */ null,
					/* 3-Text */     null
				]
			});
			jQuery("#' . $table_id . '").css("visibility", "visible");
			jQuery(".loading-image").css("display", "none");
		');

		$content='';
		$content .= '<div class="loading-image">&nbsp;</div>';
		$content .= '<table id="'.$table_id.'" style="visibility:hidden;">';
		$content .= '<thead><tr>';
		$content .= '<th>DATE</th>'; //hidden by datables code
		$content .= '<th>'.WT_Gedcom_Tag::getLabel('DATE').'</th>';
		$content .= '<th>'.WT_I18N::translate('Record').'</th>';
		if ($show_unassigned || $show_other) {
			$content .= '<th>'.WT_I18N::translate('Username').'</th>';
		}
		$content .= '<th>'.WT_Gedcom_Tag::getLabel('TEXT').'</th>';
		$content .= '</tr></thead><tbody>';

		$found=false;
		$end_jd=$show_future ? 99999999 : WT_CLIENT_JD;
		foreach (get_calendar_events(0, $end_jd, '_TODO', WT_GED_ID) as $fact) {
			$record = $fact->getParent();
			$user_name = $fact->getAttribute('_WT_USER');
			if ($user_name === Auth::user()->getUserName() || !$user_name && $show_unassigned || $user_name && $show_other) {
				$content.='<tr>';
				//-- Event date (sortable)
				$content .= '<td>'; //hidden by datables code
				$content .= $fact->getDate()->JD();
				$content .= '</td>';
				$content.='<td class="wrap">'. $fact->getDate()->display(empty($SEARCH_SPIDER)).'</td>';
				$content.='<td class="wrap"><a href="'.$record->getHtmlUrl().'">'.$record->getFullName().'</a></td>';
				if ($show_unassigned || $show_other) {
					$content.='<td class="wrap">'.$user_name.'</td>';
				}
				$text = $fact->getValue();
				$content.='<td class="wrap">'.$text.'</td>';
				$content.='</tr>';
				$found=true;
			}
		}

		$content .= '</tbody></table>';
		if (!$found) {
			$content.='<p>'.WT_I18N::translate('There are no research tasks in this family tree.').'</p>';
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
		return false;
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
		if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
			set_block_setting($block_id, 'show_other',      WT_Filter::postBool('show_other'));
			set_block_setting($block_id, 'show_unassigned', WT_Filter::postBool('show_unassigned'));
			set_block_setting($block_id, 'show_future',     WT_Filter::postBool('show_future'));
			set_block_setting($block_id, 'block',           WT_Filter::postBool('block'));
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$show_other=get_block_setting($block_id, 'show_other', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Show research tasks that are assigned to other users');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('show_other', $show_other);
		echo '</td></tr>';

		$show_unassigned=get_block_setting($block_id, 'show_unassigned', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Show research tasks that are not assigned to any user');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('show_unassigned', $show_unassigned);
		echo '</td></tr>';

		$show_future=get_block_setting($block_id, 'show_future', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Show research tasks that have a date in the future');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('show_future', $show_future);
		echo '</td></tr>';

		$block=get_block_setting($block_id, 'block', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ WT_I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
