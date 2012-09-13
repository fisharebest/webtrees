<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class faq_WT_Module extends WT_Module implements WT_Module_Menu, WT_Module_Block, WT_Module_Config {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module.  Abbreviation for "Frequently Asked Questions" */ WT_I18N::translate('FAQ');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the "FAQ" module */ WT_I18N::translate('A list of frequently asked questions and answers.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
			$this->config();
			break;
		case 'admin_delete':
			$this->delete();
			$this->config();
			break;
		case 'admin_edit':
			$this->edit();
			break;
		case 'admin_movedown':
			$this->movedown();
			$this->config();
			break;
		case 'admin_moveup':
			$this->moveup();
			$this->config();
			break;
		case 'show':
			$this->show();
			break;
		default:
			header('HTTP/1.0 404 Not Found');
		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&amp;mod_action=admin_config';
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return false;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
	}

	// Action from the configuration page
	private function edit() {
		require_once WT_ROOT.'includes/functions/functions_edit.php';

		if (safe_POST_bool('save')) {
			$block_id=safe_POST('block_id');
			if ($block_id) {
				WT_DB::prepare(
					"UPDATE `##block` SET gedcom_id=NULLIF(?, ''), block_order=? WHERE block_id=?"
				)->execute(array(
					safe_POST('gedcom_id'),
					(int)safe_POST('block_order'),
					$block_id
				));
			} else {
				WT_DB::prepare(
					"INSERT INTO `##block` (gedcom_id, module_name, block_order) VALUES (NULLIF(?, ''), ?, ?)"
				)->execute(array(
					safe_POST('gedcom_id'),
					$this->getName(),
					(int)safe_POST('block_order')
				));
				$block_id=WT_DB::getInstance()->lastInsertId();
			}
			set_block_setting($block_id, 'header',  safe_POST('header',  WT_REGEX_UNSAFE));
			set_block_setting($block_id, 'faqbody', safe_POST('faqbody', WT_REGEX_UNSAFE)); // allow html
			$languages=array();
			foreach (WT_I18N::installed_languages() as $code=>$name) {
				if (safe_POST_bool('lang_'.$code)) {
					$languages[]=$code;
				}
			}
			set_block_setting($block_id, 'languages', implode(',', $languages));
			$this->config();
		} else {
			$block_id=safe_GET('block_id');
			$controller=new WT_Controller_Base();
			if ($block_id) {
				$controller->setPageTitle(WT_I18N::translate('Edit FAQ item'));
				$header=get_block_setting($block_id, 'header');
				$faqbody=get_block_setting($block_id, 'faqbody');
				$block_order=WT_DB::prepare(
					"SELECT block_order FROM `##block` WHERE block_id=?"
				)->execute(array($block_id))->fetchOne();
				$gedcom_id=WT_DB::prepare(
					"SELECT gedcom_id FROM `##block` WHERE block_id=?"
				)->execute(array($block_id))->fetchOne();
			} else {
				$controller->setPageTitle(WT_I18N::translate('Add FAQ item'));
				$header='';
				$faqbody='';
				$block_order=WT_DB::prepare(
					"SELECT IFNULL(MAX(block_order)+1, 0) FROM `##block` WHERE module_name=?"
				)->execute(array($this->getName()))->fetchOne();
				$gedcom_id=WT_GED_ID;
			}
			$controller->pageHeader();
			if (array_key_exists('ckeditor', WT_Module::getActiveModules())) {
				ckeditor_WT_Module::enableEditor($controller);
			}

			// "Help for this page" link
			echo '<div id="page_help">', help_link('add_faq_item', $this->getName()), '</div>';
			echo '<form name="faq" method="post" action="#">';
			echo '<input type="hidden" name="save" value="1">';
			echo '<input type="hidden" name="block_id" value="', $block_id, '">';
			echo '<table id="faq_module">';
			echo '<tr><th>';
			echo WT_I18N::translate('Question');
			echo '</th></tr><tr><td><input type="text" name="header" size="90" tabindex="1" value="'.htmlspecialchars($header).'"></td></tr>';
			echo '<tr><th>';
			echo WT_I18N::translate('Answer');
			echo '</th></tr><tr><td>';
			echo '<textarea name="faqbody" class="html-edit" rows="10" cols="90" tabindex="2">', htmlspecialchars($faqbody), '</textarea>';
			echo '</td></tr>';
			echo '</table><table id="faq_module2">';
			echo '<tr>';
			echo '<th>', WT_I18N::translate('Show this block for which languages?'), '</th>';
			echo '<th>', WT_I18N::translate('FAQ position'), help_link('add_faq_order', $this->getName()), '</th>';
			echo '<th>', WT_I18N::translate('FAQ visibility'), help_link('add_faq_visibility', $this->getName()), '</th>';
			echo '</tr><tr>';
			echo '<td>';
			$languages=get_block_setting($block_id, 'languages');
			echo edit_language_checkboxes('lang_', $languages);
			echo '</td><td>';
			echo '<input type="text" name="block_order" size="3" tabindex="3" value="', $block_order, '"></td>';
			echo '</td><td>';
			echo select_edit_control('gedcom_id', WT_Tree::getList(), '', $gedcom_id, 'tabindex="4"');
			echo '</td></tr>';
			echo '</table>';

			echo '<p><input type="submit" value="', WT_I18N::translate('Save'), '" tabindex="5">';
			echo '&nbsp;<input type="button" value="', WT_I18N::translate('Cancel'), '" onclick="window.location=\''.$this->getConfigLink().'\';" tabindex="6"></p>';
			echo '</form>';
			exit;
		}
	}

	private function delete() {
		$block_id=safe_GET('block_id');

		WT_DB::prepare(
			"DELETE FROM `##block_setting` WHERE block_id=?"
		)->execute(array($block_id));

		WT_DB::prepare(
			"DELETE FROM `##block` WHERE block_id=?"
		)->execute(array($block_id));
	}

	private function moveup() {
		$block_id=safe_GET('block_id');

		$block_order=WT_DB::prepare(
			"SELECT block_order FROM `##block` WHERE block_id=?"
		)->execute(array($block_id))->fetchOne();

		$swap_block=WT_DB::prepare(
			"SELECT block_order, block_id".
			" FROM `##block`".
			" WHERE block_order=(".
			"  SELECT MAX(block_order) FROM `##block` WHERE block_order < ? AND module_name=?".
			" ) AND module_name=?".
			" LIMIT 1"
		)->execute(array($block_order, $this->getName(), $this->getName()))->fetchOneRow();
		if ($swap_block) {
			WT_DB::prepare(
				"UPDATE `##block` SET block_order=? WHERE block_id=?"
			)->execute(array($swap_block->block_order, $block_id));
			WT_DB::prepare(
				"UPDATE `##block` SET block_order=? WHERE block_id=?"
			)->execute(array($block_order, $swap_block->block_id));
		}
	}

	private function movedown() {
		$block_id=safe_GET('block_id');

		$block_order=WT_DB::prepare(
			"SELECT block_order FROM `##block` WHERE block_id=?"
		)->execute(array($block_id))->fetchOne();

		$swap_block=WT_DB::prepare(
			"SELECT block_order, block_id".
			" FROM `##block`".
			" WHERE block_order=(".
			"  SELECT MIN(block_order) FROM `##block` WHERE block_order>? AND module_name=?".
			" ) AND module_name=?".
			" LIMIT 1"
		)->execute(array($block_order, $this->getName(), $this->getName()))->fetchOneRow();
		if ($swap_block) {
			WT_DB::prepare(
				"UPDATE `##block` SET block_order=? WHERE block_id=?"
			)->execute(array($swap_block->block_order, $block_id));
			WT_DB::prepare(
				"UPDATE `##block` SET block_order=? WHERE block_id=?"
			)->execute(array($block_order, $swap_block->block_id));
		}
	}

	private function show() {
		global $controller;
		$controller=new WT_Controller_Base();
		$controller->setPageTitle($this->getTitle());
		$controller->pageHeader();

		$faqs=WT_DB::prepare(
			"SELECT block_id, bs1.setting_value AS header, bs2.setting_value AS body".
			" FROM `##block` b".
			" JOIN `##block_setting` bs1 USING (block_id)".
			" JOIN `##block_setting` bs2 USING (block_id)".
			" WHERE module_name=?".
			" AND bs1.setting_name='header'".
			" AND bs2.setting_name='faqbody'".
			" AND IFNULL(gedcom_id, ?)=?".
			" ORDER BY block_order"
		)->execute(array($this->getName(), WT_GED_ID, WT_GED_ID))->fetchAll();

		// Define your colors for the alternating rows
		echo '<h2 class="center">', WT_I18N::translate('Frequently asked questions'), '</h2>';
		// Instructions
		echo '<div class="faq_italic">', WT_I18N::translate('Click on a title to go straight to it, or scroll down to read them all');
			if (WT_USER_GEDCOM_ADMIN) {
				echo '<div class="faq_edit">',
						'<a href="module.php?mod=faq&amp;mod_action=admin_config">', WT_I18N::translate('Click here to Add, Edit, or Delete'), '</a>',
				'</div>';
			}
		echo '</div>';
		//Start the table to contain the list of headers
		$row_count = 0;
		echo '<table class="faq">';
		// List of titles
		foreach ($faqs as $id => $faq) {
			$header   =get_block_setting($faq->block_id, 'header');
			$faqbody  =get_block_setting($faq->block_id, 'faqbody');
			$languages=get_block_setting($faq->block_id, 'languages');
			if (!$languages || in_array(WT_LOCALE, explode(',', $languages))) {
				$row_color = ($row_count % 2) ? 'odd' : 'even';
				// NOTE: Print the header of the current item
				echo '<tr class="', $row_color, '"><td style="padding: 5px;">';
				echo '<a href="#faq', $id, '">', $faq->header, '</a>';
				echo '</td></tr>';
				$row_count++;
			}
		}
		echo '</table><hr>';
		// Detailed entries
		foreach ($faqs as $id => $faq) {
			$header   =get_block_setting($faq->block_id, 'header');
			$faqbody  =get_block_setting($faq->block_id, 'faqbody');
			$languages=get_block_setting($faq->block_id, 'languages');
			if (!$languages || in_array(WT_LOCALE, explode(',', $languages))) {
				// NOTE: Print the body text of the current item, with its header
				echo '<div class="faq_title" id="faq', $id, '">', $faq->header;
				echo '<div class="faq_top faq_italic">';
				echo '<a href="#body">', WT_I18N::translate('back to top'), '</a>';
				echo '</div>';
				echo '</div>';
				echo '<div class="faq_body">', substr($faqbody, 0, 1)=='<' ? $faqbody : nl2br($faqbody), '</div>';
				echo '<hr>';
			}
		}
	}

	private function config() {
		require_once 'includes/functions/functions_edit.php';

		$controller=new WT_Controller_Base();
		$controller->setPageTitle($this->getTitle());
		$controller->pageHeader();

		$faqs=WT_DB::prepare(
			"SELECT block_id, block_order, gedcom_id, bs1.setting_value AS header, bs2.setting_value AS faqbody".
			" FROM `##block` b".
			" JOIN `##block_setting` bs1 USING (block_id)".
			" JOIN `##block_setting` bs2 USING (block_id)".
			" WHERE module_name=?".
			" AND bs1.setting_name='header'".
			" AND bs2.setting_name='faqbody'".
			" AND IFNULL(gedcom_id, ?)=?".
			" ORDER BY block_order"
		)->execute(array($this->getName(), WT_GED_ID, WT_GED_ID))->fetchAll();

		$min_block_order=WT_DB::prepare(
			"SELECT MIN(block_order) FROM `##block` WHERE module_name=?"
		)->execute(array($this->getName()))->fetchOne();

		$max_block_order=WT_DB::prepare(
			"SELECT MAX(block_order) FROM `##block` WHERE module_name=?"
		)->execute(array($this->getName()))->fetchOne();

		echo
			'<p><form method="get" action="', WT_SCRIPT_NAME ,'">',
			WT_I18N::translate('Family tree'), ' ',
			'<input type="hidden" name="mod", value="', $this->getName(), '">',
			'<input type="hidden" name="mod_action", value="admin_config">',
			select_edit_control('ged', WT_Tree::getList(), null, WT_GEDCOM),
			'<input type="submit" value="', WT_I18N::translate('show'), '">',
			'</form></p>';

		echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_edit">', WT_I18N::translate('Add FAQ item'), '</a>';
		echo help_link('add_faq_item', $this->getName());

		echo '<table id="faq_edit">';
		if (empty($faqs)) {
			echo '<tr><td class="error center" colspan="5">', WT_I18N::translate('The FAQ list is empty.'), '</td></tr></table>';
		} else {
			foreach ($faqs as $faq) {
				// NOTE: Print the position of the current item
				echo '<tr class="faq_edit_pos"><td>';
				echo WT_I18N::translate('Position item'), ': ', ($faq->block_order+1), ', ';
				if ($faq->gedcom_id==null) {
					echo WT_I18N::translate('All');
				} else {
					echo WT_I18N::translate('%s', get_gedcom_setting($faq->gedcom_id, 'title'));
				}
				echo '</td>';
				// NOTE: Print the edit options of the current item
				echo '<td>';
				if ($faq->block_order==$min_block_order) {
					echo '&nbsp;';
				} else {
					echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_moveup&amp;block_id=', $faq->block_id, '" class="icon-uarrow"></a>';
					echo help_link('moveup_faq_item', $this->getName());
				}
				echo '</td><td>';
				if ($faq->block_order==$max_block_order) {
					echo '&nbsp;';
				} else {
					echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_movedown&amp;block_id=', $faq->block_id, '" class="icon-darrow"></a>';
					echo help_link('movedown_faq_item', $this->getName());
				}
				echo '</td><td>';
				echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_edit&amp;block_id=', $faq->block_id, '">', WT_I18N::translate('Edit'), '</a>';
				echo help_link('edit_faq_item', $this->getName());
				echo '</td><td>';
				echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_delete&amp;block_id=', $faq->block_id, '" onclick="return confirm(\'', WT_I18N::translate('Are you sure you want to delete this FAQ entry?'), '\');">', WT_I18N::translate('Delete'), '</a>';
				echo help_link('delete_faq_item', $this->getName());
				echo '</td></tr>';
				// NOTE: Print the title text of the current item
				echo '<tr><td colspan="5">';
				echo '<div class="faq_edit_item">';
				echo '<div class="faq_edit_title">', $faq->header, '</div>';
				// NOTE: Print the body text of the current item
				echo '<div class="faq_edit_content">', substr($faq->faqbody, 0, 1)=='<' ? $faq->faqbody : nl2br($faq->faqbody), '</div></div></td></tr>';
			}
			echo '</table>';
		}
	}

	// Implement WT_Module_Menu
	public function defaultMenuOrder() {
		return 40;
	}

	// Implement WT_Module_Menu
	public function getMenu() {
		global $SEARCH_SPIDER;

		if ($SEARCH_SPIDER) {
			return null;
		}

		$faqs=WT_DB::prepare(
			"SELECT block_id FROM `##block` b WHERE module_name=? AND IFNULL(gedcom_id, ?)=?"
		)->execute(array($this->getName(), WT_GED_ID, WT_GED_ID))->fetchAll();
		
		if (!$faqs) {
			return null;
		}

		$menu = new WT_Menu(WT_I18N::translate('FAQ'), 'module.php?mod=faq&amp;mod_action=show', 'menu-help');
		return $menu;
	}
}
