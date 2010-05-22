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

class faq_WT_Module extends WT_Module implements WT_Module_Block, WT_Module_Config {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('FAQ');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('A list of Frequenty Asked Questions.');
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
		case 'moveup':
			$this->moveup();
			$this->config();
			break;
		case 'movedown':
			$this->movedown();
			$this->config();
			break;
		case 'show':
			$this->show();
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
		global $ctype, $WT_IMAGE_DIR, $WT_IMAGES, $THEME_DIR;

		// Only show this block for certain languages
		$languages=get_block_setting($block_id, 'languages');
		if ($languages && !in_array(WT_LOCALE, explode(',', $languages))) {
			return;
		}

		$id=$this->getName().$block_id;
		$header='';
		$content=embed_globals(get_block_setting($block_id, 'html'));

		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user") {
			$content .= "<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id={$block_id}', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">"
			."<img class=\"adminicon\" src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['admin']['small']}\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure').'" /></a>';
		}

		$block=get_block_setting($block_id, 'block', false);
		if ($block) {
			require $THEME_DIR.'templates/block_small_temp.php';
		} else {
			require $THEME_DIR.'templates/block_main_temp.php';
		}
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
		if (safe_POST_bool('save')) {
			set_block_setting($block_id, 'html', $_POST['html']);
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
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$useFCK = file_exists(WT_ROOT.'modules/FCKeditor/fckeditor.php');
		if($useFCK){
			require WT_ROOT.'modules/FCKeditor/fckeditor.php';
		}

?>
		<tr>
		<td class="optionbox" colspan="2"><?php
		if ($useFCK) { // use FCKeditor module
			$oFCKeditor = new FCKeditor('html') ;
			$oFCKeditor->BasePath =  './modules/FCKeditor/';
			$oFCKeditor->Value = get_block_setting($block_id, 'html');
			$oFCKeditor->Width = 700;
			$oFCKeditor->Height = 250;
			$oFCKeditor->Config['AutoDetectLanguage'] = false ;
			$oFCKeditor->Config['DefaultLanguage'] = WT_LOCALE;
			$oFCKeditor->Create() ;
		} else { //use standard textarea
			echo '<textarea name="html" rows="10" cols="80">', htmlspecialchars(get_block_setting($block_id, 'html')), '</textarea>';
		}
		?></td>
		</tr>
		<?php

		$languages=get_block_setting($block_id, 'languages', WT_LOCALE);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Show this block for which languages?');
		echo '</td><td class="optionbox">';
		echo edit_language_checkboxes('lang_', $languages);
		echo '</td></tr>';
	}

	// Action from the configuration page
	private function edit() {
		global $TBLPREFIX;

		if (safe_POST_bool('save')) {
			$block_id=safe_POST('block_id');
			if ($block_id) {
				WT_DB::prepare(
					"UPDATE {$TBLPREFIX}block SET gedcom_id=?, block_order=? WHERE block_id=?"
				)->execute(array(
					safe_POST('gedcom_id'),
					(int)safe_POST('block_order'),
					$block_id
				));
			} else {
				WT_DB::prepare(
					"INSERT INTO {$TBLPREFIX}block (gedcom_id, module_name, block_order) VALUES (?, ?, ?)"
				)->execute(array(
					safe_POST('gedcom_id', array_keys(get_all_gedcoms())),
					$this->getName(),
					(int)safe_POST('block_order')
				));
				$block_id=WT_DB::getInstance()->lastInsertId();
			}
			set_block_setting($block_id, 'header', safe_POST('header'));
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
				print_header(i18n::translate('Edit FAQ item'));
				$header=get_block_setting($block_id, 'header');
				$body=get_block_setting($block_id, 'body');
				$block_order=WT_DB::prepare(
					"SELECT block_order FROM {$TBLPREFIX}block WHERE block_id=?"
				)->execute(array($block_id))->fetchOne();
				$gedcom_id=WT_DB::prepare(
					"SELECT gedcom_id FROM {$TBLPREFIX}block WHERE block_id=?"
				)->execute(array($block_id))->fetchOne();
			} else {
				print_header(i18n::translate('Add FAQ item'));
				$header='';
				$body='';
				$block_order=WT_DB::prepare(
					"SELECT IFNULL(MAX(block_order)+1, 0) FROM {$TBLPREFIX}block WHERE module_name=?"
				)->execute(array($this->getName()))->fetchOne();
				$gedcom_id=WT_GED_ID;
			}

			echo '<form name="faq" method="post" action="#">';
			echo '<input type="hidden" name="save" value="1" />';
			echo '<input type="hidden" name="block_id" value="', $block_id, '" />';
			echo '<table class="center list_table">';
			echo '<tr><td class="topbottombar" colspan="2">';
			echo i18n::translate('Add FAQ item'), help_link('add_faq_item');
			echo '</td></tr><tr><td class="descriptionbox" colspan="2">';
			echo i18n::translate('FAQ header'), help_link('add_faq_header');
			echo '</td></tr><tr><td class="optionbox" colspan="2"><input type="text" name="header" size="90" tabindex="1" value="'.htmlspecialchars($header).'"/></td></tr>';
			echo '<tr><td class="descriptionbox" colspan="2">';
			echo i18n::translate('FAQ body'), help_link("add_faq_body","qm","add_faq_body");
			echo '</td></tr><tr><td class="optionbox" colspan="2"><textarea name="body" rows="10" cols="90" tabindex="2">', htmlspecialchars($body), '</textarea></td></tr>';
			echo '<tr><td class="descriptionbox">';
			echo i18n::translate('FAQ position'), help_link('add_faq_order');
			echo '</td><td class="descriptionbox">';
			echo i18n::translate('FAQ visibility'), help_link('add_faq_visibility');
			echo '</td></tr><tr><td class="optionbox"><input type="text" name="block_order" size="3" tabindex="3" value="', $block_order, '" /></td>';
			echo '<td class="optionbox">';
				echo '<select name="gedcom_id" tabindex="4" />';
					echo '<option value="">', i18n::translate('All'), '</option>';
					echo '<option value="', WT_GED_ID, '" selected="selected">', htmlspecialchars(WT_GEDCOM), '</option';
				echo '</select>';
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

	private function moveup() {
		global $TBLPREFIX;

		$block_id=safe_GET('block_id');

		$block_order=WT_DB::prepare(
			"SELECT block_order FROM {$TBLPREFIX}block WHERE block_id=?"
		)->execute(array($block_id))->fetchOne();

		$swap_block=WT_DB::prepare(
			"SELECT block_order, block_id".
			" FROM {$TBLPREFIX}block".
			" WHERE block_order=(".
			"  SELECT MAX(block_order) FROM {$TBLPREFIX}block WHERE block_order<? AND module_name=?".
			" )".
			" LIMIT 1"
		)->execute(array($block_order, $this->getName()))->fetchOneRow();
		if ($swap_block) {
			WT_DB::prepare(
				"UPDATE {$TBLPREFIX}block SET block_order=? WHERE block_id=?"
			)->execute(array($swap_block->block_order, $block_id));
			WT_DB::prepare(
				"UPDATE {$TBLPREFIX}block SET block_order=? WHERE block_id=?"
			)->execute(array($block_order, $swap_block->block_id));
		}
	}

	private function movedown() {
		global $TBLPREFIX;

		$block_id=safe_GET('block_id');

		$block_order=WT_DB::prepare(
			"SELECT block_order FROM {$TBLPREFIX}block WHERE block_id=?"
		)->execute(array($block_id))->fetchOne();

		$swap_block=WT_DB::prepare(
			"SELECT block_order, block_id".
			" FROM {$TBLPREFIX}block".
			" WHERE block_order=(".
			"  SELECT MIN(block_order) FROM {$TBLPREFIX}block WHERE block_order>? AND module_name=?".
			" )".
			" LIMIT 1"
		)->execute(array($block_order, $this->getName()))->fetchOneRow();
		if ($swap_block) {
			WT_DB::prepare(
				"UPDATE {$TBLPREFIX}block SET block_order=? WHERE block_id=?"
			)->execute(array($swap_block->block_order, $block_id));
			WT_DB::prepare(
				"UPDATE {$TBLPREFIX}block SET block_order=? WHERE block_id=?"
			)->execute(array($block_order, $swap_block->block_id));
		}
	}

	private function show() {
		global $TBLPREFIX;

		print_header($this->getTitle());

		$faqs=WT_DB::prepare(
			"SELECT block_id, bs1.setting_value AS header, bs2.setting_value AS body".
			" FROM {$TBLPREFIX}block b".
			" JOIN {$TBLPREFIX}block_setting bs1 USING (block_id)".
			" JOIN {$TBLPREFIX}block_setting bs2 USING (block_id)".
			" WHERE module_name=?".
			" AND bs1.setting_name='header'".
			" AND bs2.setting_name='body'".
			" AND (gedcom_id IS NULL OR gedcom_id=?)".
			" ORDER BY block_order"
		)->execute(array($this->getName(), WT_GED_ID))->fetchAll();

		echo '<table class="list_table width100">';
		foreach ($faqs as $faq) {
			echo
				'<tr><td class="optionbox center">',
				$faq->header,
				'</td><td class="optionbox center">',
				substr($faq_body, 0, 1)=='<' ? $faq->body : nl2br($faq->body),
				'</td></tr>';
		}
		echo '</table>';

		print_footer();
	}

	private function config() {
		global $TBLPREFIX, $WT_IMAGES, $WT_IMAGE_DIR;

		print_header($this->getTitle());

		$faqs=WT_DB::prepare(
			"SELECT block_id, block_order, gedcom_id, bs1.setting_value AS header, bs2.setting_value AS body".
			" FROM {$TBLPREFIX}block b".
			" JOIN {$TBLPREFIX}block_setting bs1 USING (block_id)".
			" JOIN {$TBLPREFIX}block_setting bs2 USING (block_id)".
			" WHERE module_name=?".
			" AND bs1.setting_name='header'".
			" AND bs2.setting_name='body'".
			" AND (gedcom_id IS NULL OR gedcom_id=?)".
			" ORDER BY block_order"
		)->execute(array($this->getName(), WT_GED_ID))->fetchAll();

		$min_block_order=WT_DB::prepare(
			"SELECT MIN(block_order) FROM {$TBLPREFIX}block WHERE module_name=?"
		)->execute(array($this->getName()))->fetchOne();

		$max_block_order=WT_DB::prepare(
			"SELECT MAX(block_order) FROM {$TBLPREFIX}block WHERE module_name=?"
		)->execute(array($this->getName()))->fetchOne();

		echo '<table class="list_table width100">';
		echo '<tr><td class="width20 list_label" colspan="5">';
		echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=edit">', i18n::translate('Add FAQ item'), '</a>';
		echo help_link('add_faq_item');
		echo '</td></tr>';
		if (empty($faqs)) {
			echo '<tr><td class="error center">', i18n::translate('The FAQ list is empty.'), '</td></tr>';
		} else {
			foreach ($faqs as $faq) {
				echo '<tr>';
				// NOTE: Print the position of the current item
				echo '<td class="descriptionbox width20 $TEXT_DIRECTION" colspan="4">';
				echo i18n::translate('Position item'), ': ', $faq->block_order, ', ';
				if ($faq->gedcom_id==null) {
					echo i18n::translate('All');
				} else {
					echo get_gedcom_from_id($faq->gedcom_id);
				}
				echo '</td>';
				echo '<td class="descriptionbox">', $faq->header, '</td>';
				echo '<tr>';
				// NOTE: Print the edit options of the current item
				echo '<td class="optionbox center">';
				if ($faq->block_order==$min_block_order) {
					echo '&nbsp;';
				} else {
					echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=moveup&amp;block_id=', $faq->block_id, '"><img src="', $WT_IMAGE_DIR, '/', $WT_IMAGES["uarrow"]["other"], '" border="0" alt="" /></a>';
					echo help_link('moveup_faq_item');
				}
				echo '</td><td class="optionbox center">';
				if ($faq->block_order==$max_block_order) {
					echo '&nbsp;';
				} else {
					echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=movedown&amp;block_id=', $faq->block_id, '"><img src="', $WT_IMAGE_DIR, '/', $WT_IMAGES["darrow"]["other"], '" border="0" alt="" /></a>';
					echo help_link('movedown_faq_item');
				}
				echo '</td><td class="optionbox center">';
				echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=edit&amp;block_id=', $faq->block_id, '">', i18n::translate('Edit'), '</a>';
				echo help_link('edit_faq_item');
				echo '</td><td class="optionbox center">';
				echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=delete&amp;block_id=', $faq->block_id, '" onclick="return confirm(\'', i18n::translate('Are you sure you want to delete this FAQ entry?'), '\');">', i18n::translate('Delete'), '</a>';
				echo help_link('delete_faq_item');
				echo '</td>';
				// NOTE: Print the body text of the current item
				echo '<td class="list_value_wrap">', nl2br($faq->body), '</td></tr>';
			}
			echo '</table>';
		}

		print_footer();
	}
}
