<?php
/**
 * Family story module.
 *
 * This is a block, so we can take advantage of block storage.
 * It does not display on index.php.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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

class family_story_WT_Module extends WT_Module implements WT_Module_Block, WT_Module_Tab, WT_Module_Config {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Family story');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('Add a narrative story to a person.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'edit':
			$this->edit();
			break;
		case 'delete':
			$this->delete();
			$this->config();
			break;
		case 'config':
			$this->config();
			break;
		default:
			die("Internal error - unknown action: $mod_action");
		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&mod_action=config';
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
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

	// Implement class WT_Module_Tab
	public function defaultTabOrder() {
		return 55;
	}

	// Implement class WT_Module_Tab
	public function getTabContent() {
		global $TBLPREFIX;

		$block_ids=
			WT_DB::prepare(
				"SELECT block_id".
				" FROM {$TBLPREFIX}block".
				" WHERE module_name=?".
				" AND xref=?".
				" AND gedcom_id=?"
			)->execute(array(
				$this->getName(),
				$xref=$this->controller->indi->getXref(),
				WT_GED_ID
			))->fetchOneColumn();

		$html='';
		foreach ($block_ids as $block_id) {
			// Only show this block for certain languages
			$languages=get_block_setting($block_id, 'languages');
			if (!$languages || in_array(WT_LOCALE, explode(',', $languages))) {
				$html.='<div>'.get_block_setting($block_id, 'body').'</div>';
			}
		}
		return $html;		
	}

	// Implement class WT_Module_Tab
	public function hasTabContent() {
		return $this->getTabContent() <> '';
	}

	// Implement class WT_Module_Tab
	public function canLoadAjax() {
		return false;
	}

	// Implement class WT_Module_Tab
	public function getPreLoadContent() {
		return '';
	}

	// Implement class WT_Module_Tab
	public function getJSCallback() {
		return '';
	}

	// Action from the configuration page
	private function edit() {
		global $TBLPREFIX;

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		if (safe_POST_bool('save')) {
			$block_id=safe_POST('block_id');
			if ($block_id) {
				WT_DB::prepare(
					"UPDATE {$TBLPREFIX}block SET gedcom_id=?, xref=? WHERE block_id=?"
				)->execute(array(safe_POST('gedcom_id'), safe_POST('xref'), $block_id));
			} else {
				WT_DB::prepare(
					"INSERT INTO {$TBLPREFIX}block (gedcom_id, xref, module_name, block_order) VALUES (?, ?, ?, ?)"
				)->execute(array(
					safe_POST('gedcom_id', array_keys(get_all_gedcoms())),
					safe_POST('xref'),
					$this->getName(),
					0
				));
				$block_id=WT_DB::getInstance()->lastInsertId();
			}
			set_block_setting($block_id, 'body',   safe_POST('body', WT_REGEX_UNSAFE)); // allow html
			$languages=array();
			foreach (i18n::installed_languages() as $code=>$name) {
				if (safe_POST_bool('lang_'.$code)) {
					$languages[]=$code;
				}
			}
			if (!$languages) {
				$languages[]=WT_LOCALE;
			}
			set_block_setting($block_id, 'languages', implode(',', $languages));
			$this->config();
		} else {
			$block_id=safe_GET('block_id');
			if ($block_id) {
				print_header(i18n::translate('Edit family story'));
				$body=get_block_setting($block_id, 'body');
				$gedcom_id=WT_DB::prepare(
					"SELECT gedcom_id FROM {$TBLPREFIX}block WHERE block_id=?"
				)->execute(array($block_id))->fetchOne();
				$xref=WT_DB::prepare(
					"SELECT xref FROM {$TBLPREFIX}block WHERE block_id=?"
				)->execute(array($block_id))->fetchOne();
			} else {
				print_header(i18n::translate('Add family story'));
				$body='';
				$gedcom_id=WT_GED_ID;
				$xref='';
			}

			echo '<form name="story" method="post" action="#">';
			echo '<input type="hidden" name="save" value="1" />';
			echo '<input type="hidden" name="block_id" value="', $block_id, '" />';
			echo '<input type="hidden" name="gedcom_id" value="', WT_GED_ID, '" />';
			echo '<table class="center list_table">';
			echo '<tr><td class="topbottombar" colspan="2">';
			echo i18n::translate('Add family story'), help_link('add_family_story');
			echo '</td></tr><tr><td class="descriptionbox" colspan="2">';
			echo '<tr><td class="descriptionbox" colspan="2">';
			echo i18n::translate('Family story'), help_link("add_family_story");
			echo '</td></tr><tr><td class="optionbox" colspan="2"><textarea name="body" rows="10" cols="90" tabindex="2">', htmlspecialchars($body), '</textarea></td></tr>';
			echo '<tr><td class="descriptionbox">';
			echo i18n::translate('Person');
			echo '</td><td class="optionbox">';
			echo '<input name="xref" size="4" value="'.$xref.'" tabindex="3"/>';
			echo '</td></tr>';

			$languages=get_block_setting($block_id, 'languages', WT_LOCALE);
			echo '<tr><td class="descriptionbox wrap width33">';
			echo i18n::translate('Show this block for which languages?');
			echo '</td><td class="optionbox">';
			echo edit_language_checkboxes('lang_', $languages);
			echo '</td></tr>';
			echo '<tr><td class="topbottombar" colspan="2"><input type="submit" value="', i18n::translate('Save'), '" tabindex="5"/>';
			echo '&nbsp;<input type="button" value="', i18n::translate('Cancel'), '" onclick="window.location=\''.$this->getConfigLink().'\';" tabindex="6" /></td></tr>';
			echo '</table>';
			echo '</form>';

			print_footer();
			exit;
		}
	}

	private function delete() {
		global $TBLPREFIX;

		$block_id=safe_GET('block_id');

		$block_order=WT_DB::prepare(
			"SELECT block_order FROM {$TBLPREFIX}block WHERE block_id=?"
		)->execute(array($block_id))->fetchOne();

		WT_DB::prepare(
			"DELETE FROM {$TBLPREFIX}block_setting WHERE block_id=?"
		)->execute(array($block_id));

		WT_DB::prepare(
			"DELETE FROM {$TBLPREFIX}block WHERE block_id=?"
		)->execute(array($block_id));
	}

	private function config() {
		global $TBLPREFIX, $WT_IMAGES, $WT_IMAGE_DIR;

		print_header($this->getTitle());

		$stories=WT_DB::prepare(
			"SELECT block_id, xref".
			" FROM {$TBLPREFIX}block b".
			" WHERE module_name=?".
			" AND gedcom_id=?".
			" ORDER BY xref"
		)->execute(array($this->getName(), WT_GED_ID))->fetchAll();

		echo '<table class="list_table">';
		echo '<tr><td class="list_label" colspan="3">';
		echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=edit">', i18n::translate('Add family story'), '</a>';
		echo help_link('add_story');
		echo '</td></tr>';
		foreach ($stories as $story) {
			$indi=Person::getInstance($story->xref);
			if ($indi) {
				$name=$indi->getFullName();
			} else {
				$name=$story->xref;
			}
			echo '<tr><td class="optionbox center width20">';
			echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=edit&amp;block_id=', $story->block_id, '">', i18n::translate('Edit'), '</a>';
			echo help_link('edit_faq_item');
			echo '</td><td class="optionbox center width20">';
			echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=delete&amp;block_id=', $story->block_id, '" onclick="return confirm(\'', i18n::translate('Are you sure you want to delete this family story?'), '\');">', i18n::translate('Delete'), '</a>';
			echo help_link('delete_faq_item');
			echo '</td>';
			echo '<td class="list_value_wrap">', $name, '</td></tr>';
		}
		echo '</table>';

		print_footer();
	}
}
