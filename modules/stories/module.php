<?php
/**
 * Stories module.
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

class stories_WT_Module extends WT_Module implements WT_Module_Block, WT_Module_Tab, WT_Module_Config {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Stories');
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
		$block_ids=
			WT_DB::prepare(
				"SELECT block_id".
				" FROM `##block`".
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
				$html.='<div class="news_title center">'.get_block_setting($block_id, 'title').'</div>';
				$html.='<div>'.get_block_setting($block_id, 'story_body').'</div><br />';
				if (WT_USER_CAN_EDIT) {
					$html.='<div><a href="module.php?mod='.$this->getName().'&amp;mod_action=edit&amp;block_id='.$block_id.'">';
					$html.=i18n::translate('Edit story').'</a></div><br />';
				}
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
		global $TEXT_DIRECTION, $ENABLE_AUTOCOMPLETE;

		require_once WT_ROOT.'includes/functions/functions_edit.php';
		if (WT_USER_CAN_EDIT) {
		
			if (safe_POST_bool('save')) {
				$block_id=safe_POST('block_id');
				if ($block_id) {
					WT_DB::prepare(
						"UPDATE `##block` SET gedcom_id=?, xref=? WHERE block_id=?"
					)->execute(array(safe_POST('gedcom_id'), safe_POST('xref'), $block_id));
				} else {
					WT_DB::prepare(
						"INSERT INTO `##block` (gedcom_id, xref, module_name, block_order) VALUES (?, ?, ?, ?)"
					)->execute(array(
						safe_POST('gedcom_id', array_keys(get_all_gedcoms())),
						safe_POST('xref'),
						$this->getName(),
						0
					));
					$block_id=WT_DB::getInstance()->lastInsertId();
				}
				set_block_setting($block_id, 'title',		safe_POST('title',		WT_REGEX_UNSAFE)); // allow html
				set_block_setting($block_id, 'story_body',  safe_POST('story_body', WT_REGEX_UNSAFE)); // allow html
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
					print_header(i18n::translate('Edit story'));
					$title=get_block_setting($block_id, 'title');
					$story_body=get_block_setting($block_id, 'story_body');
					$gedcom_id=WT_DB::prepare(
						"SELECT gedcom_id FROM `##block` WHERE block_id=?"
					)->execute(array($block_id))->fetchOne();
					$xref=WT_DB::prepare(
						"SELECT xref FROM `##block` WHERE block_id=?"
					)->execute(array($block_id))->fetchOne();
				} else {
					print_header(i18n::translate('Add story'));
					$title='';
					$story_body='';
					$gedcom_id=WT_GED_ID;
					$xref='';
				}
				?>
				<script language="JavaScript" type="text/javascript">
					var pastefield;
					function paste_id(value) {
						pastefield.value=value;
					}
				</script>
				<?php
				if ($ENABLE_AUTOCOMPLETE) {
					require WT_ROOT.'/js/autocomplete.js.htm';
				}
				echo '<form name="story" method="post" action="#">';
				echo '<input type="hidden" name="save" value="1" />';
				echo '<input type="hidden" name="block_id" value="', $block_id, '" />';
				echo '<input type="hidden" name="gedcom_id" value="', WT_GED_ID, '" />';
				echo '<table class="center list_table">';
				echo '<tr><td class="topbottombar" colspan="2">';
				echo i18n::translate('Add story'), help_link('add_story', $this->getName());
				echo '</td></tr><tr><td class="descriptionbox" colspan="2">';
				echo '<tr><td class="descriptionbox" colspan="2">';
				echo i18n::translate('Story title'), help_link('story_title', $this->getName());
				echo '</td></tr><tr><td class="optionbox" colspan="2"><textarea name="title" rows="1" cols="90" tabindex="2">', htmlspecialchars($title), '</textarea></td></tr>';
				echo '<tr><td class="descriptionbox" colspan="2">';
				echo i18n::translate('Story'), help_link('add_story', $this->getName());
				echo '</td></tr><tr><td class="optionbox" colspan="2">';
				if (array_key_exists('ckeditor', WT_Module::getActiveModules())) {
				// use CKeditor module
					require_once WT_ROOT.'modules/ckeditor/ckeditor.php';
					$oCKeditor = new CKEditor();
					$oCKeditor->basePath =  './modules/ckeditor/';
					$oCKeditor->config['width'] = 900;
					$oCKeditor->config['height'] = 400;
					$oCKeditor->config['AutoDetectLanguage'] = false ;
					$oCKeditor->config['DefaultLanguage'] = 'en';
					$oCKeditor->editor('story_body', $story_body);
				} else {
				//use standard textarea
					echo '<textarea name="story_body" rows="10" cols="90" tabindex="2">', htmlspecialchars($story_body), '</textarea>';
				}
				echo '</td></tr>';
				echo '<tr><td class="descriptionbox">';
				echo i18n::translate('Person');
				echo '</td><td class="optionbox ', $TEXT_DIRECTION, '">';
				echo '<input type="text" name="xref" id="pid" size="4" value="'.$xref.'" />';
				print_findindi_link("xref", "pid");
				echo '</td></tr>';
				$languages=get_block_setting($block_id, 'languages', WT_LOCALE);
				echo '<tr><td class="descriptionbox wrap width33">';
				echo i18n::translate('Show this block for which languages?');
				echo '</td><td class="optionbox ', $TEXT_DIRECTION, '">';
				echo edit_language_checkboxes('lang_', $languages);
				echo '</td></tr>';
				echo '<tr><td class="topbottombar" colspan="2"><input type="submit" value="', i18n::translate('Save'), '" tabindex="5"/>';
				echo '&nbsp;<input type="button" value="', i18n::translate('Cancel'), '" onclick="window.location=\''.$this->getConfigLink().'\';" tabindex="6" /></td></tr>';
				echo '</table>';
				echo '</form>';

				print_footer();
				exit;
			}
		} else {
			header("Location: index.php");
			exit;
		}
	}

	private function delete() {
		if (WT_USER_CAN_EDIT) {
			$block_id=safe_GET('block_id');

			$block_order=WT_DB::prepare(
				"SELECT block_order FROM `##block` WHERE block_id=?"
			)->execute(array($block_id))->fetchOne();

			WT_DB::prepare(
				"DELETE FROM `##block_setting` WHERE block_id=?"
			)->execute(array($block_id));

			WT_DB::prepare(
				"DELETE FROM `##block` WHERE block_id=?"
			)->execute(array($block_id));
		} else {
			header("Location: index.php");
			exit;
		}
	}

	private function config() {
		global $WT_IMAGES, $WT_IMAGE_DIR, $SHOW_ID_NUMBERS, $TEXT_DIRECTION;

		if (WT_USER_CAN_EDIT) {
			print_header($this->getTitle());

			$stories=WT_DB::prepare(
				"SELECT block_id, xref".
				" FROM `##block` b".
				" WHERE module_name=?".
				" AND gedcom_id=?".
				" ORDER BY xref"
			)->execute(array($this->getName(), WT_GED_ID))->fetchAll();

			echo '<table class="list_table">';
			echo '<tr><td class="list_label" colspan="4">';
			echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=edit">', i18n::translate('Add story'), '</a>';
			echo help_link('add_story', $this->getName());
			echo '</td></tr>';
			if (count($stories)>0) {
				echo '<tr><td class="optionbox center width20">', i18n::translate('Story title'), help_link('story_title', $this->getName());
				echo '</td><td class="optionbox center width20">', i18n::translate('Person');
				echo '</td><td class="optionbox center width20">', i18n::translate('Edit story'), help_link('edit_story', $this->getName());
				echo '</td><td class="optionbox center width20">', i18n::translate('Delete'), help_link('delete_story', $this->getName()), '</tr>';
			}
			foreach ($stories as $story) {
				$indi=Person::getInstance($story->xref);
				if ($indi) {
					$id='';
					if ($SHOW_ID_NUMBERS) {
						if ($TEXT_DIRECTION=='rtl') {
							$id="&nbsp;&nbsp;".getRLM()."(".$story->xref.")".getRLM();
						} else {
							$id="&nbsp;&nbsp;(".$story->xref.")";
						}
					}
					$name="<a href=\"".$indi->getLinkUrl()."#stories\">".$indi->getFullName().$id."</a>";
				} else {
					$name=$story->xref;
				}
				echo '<tr><td class="optionbox center width20">';
				echo get_block_setting($story->block_id, 'title');
				echo '<td class="list_value_wrap">', $name, '</td>';
				echo '<td class="optionbox center width20"><a href="module.php?mod=', $this->getName(), '&amp;mod_action=edit&amp;block_id=', $story->block_id, '">', i18n::translate('Edit'), '</a></td>';
				echo '<td class="optionbox center width20"><a href="module.php?mod=', $this->getName(), '&amp;mod_action=delete&amp;block_id=', $story->block_id, '" onclick="return confirm(\'', i18n::translate('Are you sure you want to delete this story?'), '\');">', i18n::translate('Delete'), '</a>';
				echo '</td></tr>';
			}
			echo '</table>';
			print_footer();
		} else {
			header("Location: index.php");
			exit;
		}
	}
}
