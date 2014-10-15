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

class stories_WT_Module extends WT_Module implements WT_Module_Tab, WT_Module_Config, WT_Module_Menu {
	/**
	 * {@inheritdoc}
	 */
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Stories');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDescription() {
		return /* I18N: Description of the “Stories” module */ WT_I18N::translate('Add narrative stories to individuals in the family tree.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function modAction($mod_action) {
		switch ($mod_action) {
		case 'admin_edit':
			$this->edit();
			break;
		case 'admin_delete':
			$this->delete();
			$this->config();
			break;
		case 'admin_config':
			$this->config();
			break;
		case 'show_list':
			$this->showList();
			break;
		default:
			header('HTTP/1.0 404 Not Found');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getConfigLink() {
		return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin_config';
	}

	/**
	 * {@inheritdoc}
	 */
	public function defaultTabOrder() {
		return 55;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTabContent() {
		global $controller;

		$block_ids =
			WT_DB::prepare(
				"SELECT block_id".
				" FROM `##block`".
				" WHERE module_name=?".
				" AND xref=?".
				" AND gedcom_id=?"
			)->execute(array(
				$this->getName(),
				$controller->record->getXref(),
				WT_GED_ID
			))->fetchOneColumn();

		$html = '';
		foreach ($block_ids as $block_id) {
			// Only show this block for certain languages
			$languages = get_block_setting($block_id, 'languages');
			if (!$languages || in_array(WT_LOCALE, explode(',', $languages))) {
				$html .= '<div class="story_title descriptionbox center rela">' . get_block_setting($block_id, 'title') . '</div>';
				$html .= '<div class="story_body optionbox">' . get_block_setting($block_id, 'story_body') . '</div>';
				if (WT_USER_CAN_EDIT) {
					$html .= '<div class="story_edit"><a href="module.php?mod=' . $this->getName() . '&amp;mod_action=admin_edit&amp;block_id=' . $block_id . '">';
					$html .= WT_I18N::translate('Edit story') . '</a></div>';
				}
			}
		}
		if (WT_USER_GEDCOM_ADMIN && !$html) {
			$html .= '<div class="news_title center">' . $this->getTitle() . '</div>';
			$html .= '<div><a href="module.php?mod=' . $this->getName() . '&amp;mod_action=admin_edit&amp;xref=' . $controller->record->getXref() . '">';
			$html .= WT_I18N::translate('Add a story') . '</a></div><br>';
		}

		return $html;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasTabContent() {
		return $this->getTabContent() <> '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGrayedOut() {
		global $controller;

		$count_of_stories =
			WT_DB::prepare(
				"SELECT COUNT(block_id)".
				" FROM `##block`".
				" WHERE module_name=?".
				" AND xref=?".
				" AND gedcom_id=?"
			)->execute(array(
				$this->getName(),
				$controller->record->getXref(),
				WT_GED_ID
			))->fetchOne();

		return $count_of_stories == 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function canLoadAjax() {
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPreLoadContent() {
		return '';
	}

	/**
	 * Show and process a form to edit a story.
	 */
	private function edit() {
		require_once WT_ROOT . 'includes/functions/functions_edit.php';

		if (WT_USER_CAN_EDIT) {
			if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
				$block_id = WT_Filter::postInteger('block_id');
				if ($block_id) {
					WT_DB::prepare(
						"UPDATE `##block` SET gedcom_id=?, xref=? WHERE block_id=?"
					)->execute(array(WT_Filter::postInteger('gedcom_id'), WT_Filter::post('xref', WT_REGEX_XREF), $block_id));
				} else {
					WT_DB::prepare(
						"INSERT INTO `##block` (gedcom_id, xref, module_name, block_order) VALUES (?, ?, ?, ?)"
					)->execute(array(
						WT_Filter::postInteger('gedcom_id'),
						WT_Filter::post('xref', WT_REGEX_XREF),
						$this->getName(),
						0
					));
					$block_id = WT_DB::getInstance()->lastInsertId();
				}
				set_block_setting($block_id, 'title', WT_Filter::post('title'));
				set_block_setting($block_id, 'story_body', WT_Filter::post('story_body'));
				$languages = array();
				foreach (WT_I18N::installed_languages() as $code => $name) {
					if (WT_Filter::postBool('lang_' . $code)) {
						$languages[] = $code;
					}
				}
				set_block_setting($block_id, 'languages', implode(',', $languages));
				$this->config();
			} else {
				$block_id = WT_Filter::getInteger('block_id');

				$controller = new WT_Controller_Page();
				if ($block_id) {
					$controller->setPageTitle(WT_I18N::translate('Edit story'));
					$title      = get_block_setting($block_id, 'title');
					$story_body = get_block_setting($block_id, 'story_body');
					$xref       = WT_DB::prepare(
						"SELECT xref FROM `##block` WHERE block_id=?"
					)->execute(array($block_id))->fetchOne();
				} else {
					$controller->setPageTitle(WT_I18N::translate('Add a story'));
					$title      = '';
					$story_body = '';
					$xref       = WT_Filter::get('xref', WT_REGEX_XREF);
				}
				$controller
					->pageHeader()
					->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
					->addInlineJavascript('autocomplete();');
				if (array_key_exists('ckeditor', WT_Module::getActiveModules())) {
					ckeditor_WT_Module::enableEditor($controller);
				}

				echo '<form name="story" method="post" action="module.php?mod=', $this->getName(), '&amp;mod_action=admin_edit">';
				echo WT_Filter::getCsrf();
				echo '<input type="hidden" name="save" value="1">';
				echo '<input type="hidden" name="block_id" value="', $block_id, '">';
				echo '<input type="hidden" name="gedcom_id" value="', WT_GED_ID, '">';
				echo '<table id="story_module">';
				echo '<tr><th>';
				echo WT_I18N::translate('Story title');
				echo '</th></tr><tr><td><textarea name="title" rows="1" cols="90" tabindex="2">', WT_Filter::escapeHtml($title), '</textarea></td></tr>';
				echo '<tr><th>';
				echo WT_I18N::translate('Story');
				echo '</th></tr><tr><td>';
				echo '<textarea name="story_body" class="html-edit" rows="10" cols="90" tabindex="2">', WT_Filter::escapeHtml($story_body), '</textarea>';
				echo '</td></tr>';
				echo '</table><table id="story_module2">';
				echo '<tr>';
				echo '<th>', WT_I18N::translate('Individual'), '</th>';
				echo '<th>', WT_I18N::translate('Show this block for which languages?'), '</th>';
				echo '</tr>';
				echo '<tr>';
				echo '<td class="optionbox">';
				echo '<input data-autocomplete-type="INDI" type="text" name="xref" id="pid" size="4" value="' . $xref . '">';
				echo print_findindi_link('pid');
				if ($xref) {
					$person = WT_Individual::getInstance($xref);
					if ($person) {
						echo ' ', $person->format_list('span');
					}
				}
				echo '</td>';
				$languages = get_block_setting($block_id, 'languages');
				echo '<td class="optionbox">';
				echo edit_language_checkboxes('lang_', $languages);
				echo '</td></tr></table>';
				echo '<p><input type="submit" value="', WT_I18N::translate('save'), '" tabindex="5">';
				echo '</p>';
				echo '</form>';

				exit;
			}
		} else {
			header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH);
			exit;
		}
	}

	/**
	 * Respond to a request to delete a story.
	 */
	private function delete() {
		if (WT_USER_CAN_EDIT) {
			$block_id = WT_Filter::getInteger('block_id');

			WT_DB::prepare(
				"DELETE FROM `##block_setting` WHERE block_id=?"
			)->execute(array($block_id));

			WT_DB::prepare(
				"DELETE FROM `##block` WHERE block_id=?"
			)->execute(array($block_id));
		} else {
			header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH);
			exit;
		}
	}

	/**
	 * The admin view - list, create, edit, delete stories.
	 */
	private function config() {
		require_once WT_ROOT . 'includes/functions/functions_edit.php';
		if (WT_USER_GEDCOM_ADMIN) {

			$controller = new WT_Controller_Page();
			$controller
				->setPageTitle($this->getTitle())
				->pageHeader()
				->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
				->addInlineJavascript('
					jQuery("#story_table").dataTable({
						dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
						' . WT_I18N::datatablesI18N() . ',
						autoWidth: false,
						paging: true,
						pagingType: "full_numbers",
						lengthChange: true,
						filter: true,
						info: true,
						jQueryUI: true,
						sorting: [[0,"asc"]],
						columns: [
							/* 0-name */ null,
							/* 1-NAME */ null,
							/* 2-NAME */ { sortable:false },
							/* 3-NAME */ { sortable:false }
						]
					});
				');

			$stories = WT_DB::prepare(
				"SELECT block_id, xref".
				" FROM `##block` b".
				" WHERE module_name=?".
				" AND gedcom_id=?".
				" ORDER BY xref"
			)->execute(array($this->getName(), WT_GED_ID))->fetchAll();

			echo
				'<form method="get" action="', WT_SCRIPT_NAME, '">',
				WT_I18N::translate('Family tree'), ' ',
				'<input type="hidden" name="mod" value="', $this->getName(), '">',
				'<input type="hidden" name="mod_action" value="admin_config">',
				select_edit_control('ged', WT_Tree::getNameList(), null, WT_GEDCOM),
				'<input type="submit" value="', WT_I18N::translate('show'), '">',
				'</form>';

			echo '<h3><a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_edit">', WT_I18N::translate('Add a story'), '</a></h3>';
			if (count($stories) > 0) {
				echo '<table id="story_table">';
				echo '<thead><tr>
					<th>', WT_I18N::translate('Story title'), '</th>
					<th>', WT_I18N::translate('Individual'), '</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					</tr></thead>';
			}
			echo '<tbody>';
			foreach ($stories as $story) {
				$story_title = get_block_setting($story->block_id, 'title');
				$indi        = WT_Individual::getInstance($story->xref);
				if ($indi) {
					echo '<tr><td><a href="', $indi->getHtmlUrl() . '#stories">', $story_title, '</a></td>
							  <td><a href="', $indi->getHtmlUrl() . '#stories">' . $indi->getFullName(), '</a></td>';
				} else {
					echo '<tr><td>', $story_title, '</td><td class="error">', $story->xref, '</td>';
				}
				echo '<td><a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_edit&amp;block_id=', $story->block_id, '"><div class="icon-edit">&nbsp;</div></a></td>
						 <td><a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_delete&amp;block_id=', $story->block_id, '" onclick="return confirm(\'', WT_I18N::translate('Are you sure you want to delete this story?'), '\');"><div class="icon-delete">&nbsp;</div></a></td>
						 </tr>';
			}
			echo '</tbody></table>';
		} else {
			header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH);
			exit;
		}
	}

	/**
	 * Show the list of stories
	 */
	private function showList() {
		global $controller;

		$controller = new WT_Controller_Page();
		$controller
			->setPageTitle($this->getTitle())
			->pageHeader()
			->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
			->addInlineJavascript('
				jQuery("#story_table").dataTable({
					dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
					' . WT_I18N::datatablesI18N() . ',
					autoWidth: false,
					paging: true,
					pagingType: "full_numbers",
					lengthChange: true,
					filter: true,
					info: true,
					jQueryUI: true,
					sorting: [[0,"asc"]],
					columns: [
						/* 0-name */ null,
						/* 1-NAME */ null
					]
				});
			');

		$stories = WT_DB::prepare(
			"SELECT block_id, xref".
			" FROM `##block` b".
			" WHERE module_name=?".
			" AND gedcom_id=?".
			" ORDER BY xref"
		)->execute(array($this->getName(), WT_GED_ID))->fetchAll();

		echo '<h2 class="center">', WT_I18N::translate('Stories'), '</h2>';
		if (count($stories) > 0) {
			echo '<table id="story_table" class="width100">';
			echo '<thead><tr>
				<th>', WT_I18N::translate('Story title'), '</th>
				<th>', WT_I18N::translate('Individual'), '</th>
				</tr></thead>
				<tbody>';
			foreach ($stories as $story) {
				$indi        = WT_Individual::getInstance($story->xref);
				$story_title = get_block_setting($story->block_id, 'title');
				$languages   = get_block_setting($story->block_id, 'languages');
				if (!$languages || in_array(WT_LOCALE, explode(',', $languages))) {
					if ($indi) {
						if ($indi->canShow()) {
							echo '<tr><td><a href="' . $indi->getHtmlUrl() . '#stories">' . $story_title . '</a></td><td><a href="' . $indi->getHtmlUrl() . '#stories">' . $indi->getFullName() . '</a></td></tr>';
						}
					} else {
						echo '<tr><td>', $story_title, '</td><td class="error">', $story->xref, '</td></tr>';
					}
				}
			}
			echo '</tbody></table>';
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function defaultMenuOrder() {
		return 30;
	}

	/**
	 * {@inheritdoc}
	 */
	public function defaultAccessLevel() {
		return WT_PRIV_HIDE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMenu() {
		global $SEARCH_SPIDER;

		if ($SEARCH_SPIDER) {
			return null;
		}

		$menu = new WT_Menu($this->getTitle(), 'module.php?mod=' . $this->getName() . '&amp;mod_action=show_list', 'menu-story');

		return $menu;
	}
}
