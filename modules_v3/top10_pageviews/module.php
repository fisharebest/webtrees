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

use WT\Auth;

class top10_pageviews_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Most viewed pages');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Most visited pages” module */ WT_I18N::translate('A list of the pages that have been viewed the most number of times.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype;

		$count_placement=get_block_setting($block_id, 'count_placement', 'before');
		$num=(int)get_block_setting($block_id, 'num', 10);
		$block=get_block_setting($block_id, 'block', false);
		if ($cfg) {
			foreach (array('count_placement', 'num', 'block') as $name) {
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
		$title .= $this->getTitle();

		$content = "";
		// load the lines from the file
		$top10=WT_DB::prepare(
			"SELECT page_parameter, page_count".
			" FROM `##hit_counter`".
			" WHERE gedcom_id=? AND page_name IN ('individual.php','family.php','source.php','repo.php','note.php','mediaviewer.php')".
			" ORDER BY page_count DESC LIMIT ".$num
		)->execute(array(WT_GED_ID))->FetchAssoc();


		if ($block) {
			$content .= "<table width=\"90%\">";
		} else {
			$content .= "<table>";
		}
		foreach ($top10 as $id=>$count) {
			$record=WT_GedcomRecord::getInstance($id);
			if ($record && $record->canShow()) {
				$content .= '<tr valign="top">';
				if ($count_placement=='before') {
					$content .= '<td dir="ltr" align="right">['.$count.']</td>';
				}
				$content .= '<td class="name2" ><a href="'.$record->getHtmlUrl().'">'.$record->getFullName().'</a></td>';
				if ($count_placement=='after') {
					$content .= '<td dir="ltr" align="right">['.$count.']</td>';
				}
				$content .= '</tr>';
			}
		}
		$content .= "</table>";

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
		return false;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
		if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
			set_block_setting($block_id, 'num',             WT_Filter::postInteger('num', 1, 10000, 10));
			set_block_setting($block_id, 'count_placement', WT_Filter::post('count_placement', 'before|after', 'before'));
			set_block_setting($block_id, 'block',           WT_Filter::postBool('block'));
			exit;
		}
		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$num=get_block_setting($block_id, 'num', 10);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Number of items to show');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="num" size="2" value="', $num, '">';
		echo '</td></tr>';

		$count_placement=get_block_setting($block_id, 'count_placement', 'left');
		echo "<tr><td class=\"descriptionbox wrap width33\">";
		echo WT_I18N::translate('Place counts before or after name?');
		echo "</td><td class=\"optionbox\">";
		echo select_edit_control('count_placement', array('before'=>WT_I18N::translate('before'), 'after'=>WT_I18N::translate('after')), null, $count_placement, '');
		echo '</td></tr>';

		$block=get_block_setting($block_id, 'block', false);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ WT_I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
