<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class FrequentlyAskedQuestionsModule
 */
class FrequentlyAskedQuestionsModule extends Module implements ModuleMenuInterface, ModuleConfigInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module.  Abbreviation for “Frequently Asked Questions” */ I18N::translate('FAQ');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “FAQ” module */ I18N::translate('A list of frequently asked questions and answers.');
	}

	/** {@inheritdoc} */
	public function modAction($mod_action) {
		switch ($mod_action) {
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
			http_response_code(404);
		}
	}

	/** {@inheritdoc} */
	public function getConfigLink() {
		return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin_config';
	}

	/**
	 * Action from the configuration page
	 */
	private function edit() {
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			$block_id = Filter::postInteger('block_id');
			if ($block_id) {
				Database::prepare(
					"UPDATE `##block` SET gedcom_id = NULLIF(:tree_id, '0'), block_order = :block_order WHERE block_id = :block_id"
				)->execute(array(
					'tree_id'     => Filter::postInteger('gedcom_id'),
					'block_order' => Filter::postInteger('block_order'),
					'block_id'    => $block_id
				));
			} else {
				Database::prepare(
					"INSERT INTO `##block` (gedcom_id, module_name, block_order) VALUES (NULLIF(:tree_id, '0'), :module_name, :block_order)"
				)->execute(array(
					'tree_id'     => Filter::postInteger('gedcom_id'),
					'module_name' => $this->getName(),
					'block_order' => Filter::postInteger('block_order'),
				));
				$block_id = Database::getInstance()->lastInsertId();
			}
			set_block_setting($block_id, 'header', Filter::post('header'));
			set_block_setting($block_id, 'faqbody', Filter::post('faqbody'));

			$languages = Filter::postArray('lang', null, array_keys(I18N::installedLanguages()));
			set_block_setting($block_id, 'languages', implode(',', $languages));
			$this->config();
		} else {
			$block_id   = Filter::getInteger('block_id');
			$controller = new PageController;
			if ($block_id) {
				$controller->setPageTitle(I18N::translate('Edit FAQ item'));
				$header      = get_block_setting($block_id, 'header');
				$faqbody     = get_block_setting($block_id, 'faqbody');
				$block_order = Database::prepare(
					"SELECT block_order FROM `##block` WHERE block_id = :block_id"
				)->execute(array('block_id' => $block_id))->fetchOne();
				$gedcom_id   = Database::prepare(
					"SELECT gedcom_id FROM `##block` WHERE block_id = :block_id"
				)->execute(array('block_id' => $block_id))->fetchOne();
			} else {
				$controller->setPageTitle(I18N::translate('Add an FAQ item'));
				$header      = '';
				$faqbody     = '';
				$block_order = Database::prepare(
					"SELECT IFNULL(MAX(block_order)+1, 0) FROM `##block` WHERE module_name = :module_name"
				)->execute(array('module_name' => $this->getName()))->fetchOne();
				$gedcom_id   = WT_GED_ID;
			}
			$controller->pageHeader();
			if (Module::getModuleByName('ckeditor')) {
				CkeditorModule::enableEditor($controller);
			}

			?>
			<ol class="breadcrumb small">
				<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
				<li><a href="admin_modules.php"><?php echo I18N::translate('Module administration'); ?></a></li>
				<li><a href="module.php?mod=<?php echo $this->getName(); ?>&mod_action=admin_config"><?php echo I18N::translate('Frequently asked questions'); ?></a></li>
				<li class="active"><?php echo $controller->getPageTitle(); ?></li>
			</ol>
			<h2><?php echo $controller->getPageTitle(); ?></h2>
			<?php

			echo '<form name="faq" method="post" action="module.php?mod=', $this->getName(), '&amp;mod_action=admin_edit">';
			echo Filter::getCsrf();
			echo '<input type="hidden" name="save" value="1">';
			echo '<input type="hidden" name="block_id" value="', $block_id, '">';
			echo '<table id="faq_module">';
			echo '<tr><th>';
			echo I18N::translate('Question');
			echo '</th></tr><tr><td><input type="text" name="header" size="90" tabindex="1" value="' . Filter::escapeHtml($header) . '"></td></tr>';
			echo '<tr><th>';
			echo I18N::translate('Answer');
			echo '</th></tr><tr><td>';
			echo '<textarea name="faqbody" class="html-edit" rows="10" cols="90" tabindex="2">', Filter::escapeHtml($faqbody), '</textarea>';
			echo '</td></tr>';
			echo '</table><table id="faq_module2">';
			echo '<tr>';
			echo '<th>', I18N::translate('Show this block for which languages?'), '</th>';
			echo '<th>', I18N::translate('FAQ position'), '</th>';
			echo '<th>', I18N::translate('FAQ visibility'), '<br><small>', I18N::translate('A FAQ item can be displayed on just one of the family trees, or on all the family trees.'), '</small></th>';
			echo '</tr><tr>';
			echo '<td>';
			$languages = explode(',', get_block_setting($block_id, 'languages'));
			echo edit_language_checkboxes('lang', $languages);
			echo '</td><td>';
			echo '<input type="text" name="block_order" size="3" tabindex="3" value="', $block_order, '"></td>';
			echo '</td><td>';
			echo select_edit_control('gedcom_id', Tree::getIdList(), I18N::translate('All'), $gedcom_id, 'tabindex="4"');
			echo '</td></tr>';
			echo '</table>';

			echo '<p><input type="submit" value="', I18N::translate('save'), '" tabindex="5">';
			echo '</form>';
		}
	}

	/**
	 * Respond to a request to delete a FAQ.
	 */
	private function delete() {
		$block_id = Filter::getInteger('block_id');

		Database::prepare(
			"DELETE FROM `##block_setting` WHERE block_id = :block_id"
		)->execute(array('block_id' => $block_id));

		Database::prepare(
			"DELETE FROM `##block` WHERE block_id = :block_id"
		)->execute(array('block_id' => $block_id));
	}

	/**
	 * Respond to a request to move a FAQ up the list.
	 */
	private function moveup() {
		$block_id = Filter::getInteger('block_id');

		$block_order = Database::prepare(
			"SELECT block_order FROM `##block` WHERE block_id = :block_id"
		)->execute(array('block_id' => $block_id))->fetchOne();

		$swap_block = Database::prepare(
			"SELECT block_order, block_id" .
			" FROM `##block`" .
			" WHERE block_order = (" .
			"  SELECT MAX(block_order) FROM `##block` WHERE block_order < :block_order AND module_name = :module_name_1" .
			" ) AND module_name = :module_name_2" .
			" LIMIT 1"
		)->execute(array(
			'block_order'   => $block_order,
			'module_name_1' => $this->getName(),
			'module_name_2' => $this->getName()
		))->fetchOneRow();
		if ($swap_block) {
			Database::prepare(
				"UPDATE `##block` SET block_order = :block_order WHERE block_id = :block_id"
			)->execute(array(
				'block_order' => $swap_block->block_order,
				'block_id'    => $block_id,
			));
			Database::prepare(
				"UPDATE `##block` SET block_order = :block_order WHERE block_id = :block_id"
			)->execute(array(
				'block_order' => $block_order,
				'block_id'    => $swap_block->block_id,
			));
		}
	}

	/**
	 * Respond to a request to move a FAQ down the list.
	 */
	private function movedown() {
		$block_id = Filter::get('block_id');

		$block_order = Database::prepare(
			"SELECT block_order FROM `##block` WHERE block_id = :block_id"
		)->execute(array(
			'block_id' => $block_id,
		))->fetchOne();

		$swap_block = Database::prepare(
			"SELECT block_order, block_id" .
			" FROM `##block`" .
			" WHERE block_order=(" .
			"  SELECT MIN(block_order) FROM `##block` WHERE block_order > :block_order AND module_name = :module_name_1" .
			" ) AND module_name = :module_name_2" .
			" LIMIT 1"
		)->execute(array(
			'block_order'   => $block_order,
			'module_name_1' => $this->getName(),
			'module_name_2' => $this->getName(),
			))->fetchOneRow();
		if ($swap_block) {
			Database::prepare(
				"UPDATE `##block` SET block_order = :block_order WHERE block_id = :block_id"
			)->execute(array(
				'block_order' => $swap_block->block_order,
				'block_id'    => $block_id,
			));
			Database::prepare(
				"UPDATE `##block` SET block_order = :block_order WHERE block_id = :block_id"
			)->execute(array(
				'block_order' => $block_order,
				'block_id'    => $swap_block->block_id,
			));
		}
	}

	/**
	 * Show a list of FAQs
	 */
	private function show() {
		global $controller;
		$controller = new PageController;
		$controller
			->setPageTitle(I18N::translate('Frequently asked questions'))
			->pageHeader();

		$faqs = Database::prepare(
			"SELECT block_id, bs1.setting_value AS header, bs2.setting_value AS body, bs3.setting_value AS languages" .
			" FROM `##block` b" .
			" JOIN `##block_setting` bs1 USING (block_id)" .
			" JOIN `##block_setting` bs2 USING (block_id)" .
			" JOIN `##block_setting` bs3 USING (block_id)" .
			" WHERE module_name = :module_name" .
			" AND bs1.setting_name = 'header'" .
			" AND bs2.setting_name = 'faqbody'" .
			" AND bs3.setting_name = 'languages'" .
			" AND IFNULL(gedcom_id, :tree_id_1) = :tree_id_2" .
			" ORDER BY block_order"
		)->execute(array(
			'module_name' => $this->getName(),
			'tree_id_1'   => WT_GED_ID,
			'tree_id_2'   => WT_GED_ID,
		))->fetchAll();

		// Define your colors for the alternating rows
		echo '<h2 class="center">', I18N::translate('Frequently asked questions'), '</h2>';
		// Instructions
		echo '<div class="faq_italic">', I18N::translate('Click on a title to go straight to it, or scroll down to read them all');
		if (WT_USER_GEDCOM_ADMIN) {
			echo '<div class="faq_edit"><a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_config">', I18N::translate('Click here to add, edit, or delete'), '</a></div>';
		}
		echo '</div>';
		$row_count = 0;
		echo '<table class="faq">';
		// List of titles
		foreach ($faqs as $id => $faq) {
			if (!$faq->languages || in_array(WT_LOCALE, explode(',', $faq->languages))) {
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
			if (!$faq->languages || in_array(WT_LOCALE, explode(',', $faq->languages))) {
				echo '<div class="faq_title" id="faq', $id, '">', $faq->header;
				echo '<div class="faq_top faq_italic">';
				echo '<a href="#content">', I18N::translate('back to top'), '</a>';
				echo '</div>';
				echo '</div>';
				echo '<div class="faq_body">', substr($faq->body, 0, 1) == '<' ? $faq->body : nl2br($faq->body, false), '</div>';
				echo '<hr>';
			}
		}
	}

	/**
	 * Provide a form to manage the FAQs.
	 */
	private function config() {
		$controller = new PageController;
		$controller
			->setPageTitle(I18N::translate('Frequently asked questions'))
			->pageHeader();

		$faqs = Database::prepare(
			"SELECT block_id, block_order, gedcom_id, bs1.setting_value AS header, bs2.setting_value AS faqbody" .
			" FROM `##block` b" .
			" JOIN `##block_setting` bs1 USING (block_id)" .
			" JOIN `##block_setting` bs2 USING (block_id)" .
			" WHERE module_name = :module_name" .
			" AND bs1.setting_name = 'header'" .
			" AND bs2.setting_name = 'faqbody'" .
			" AND IFNULL(gedcom_id, :tree_id_1) = :tree_id_2" .
			" ORDER BY block_order"
		)->execute(array(
			'module_name' => $this->getName(),
			'tree_id_1'   => WT_GED_ID,
			'tree_id_2'   => WT_GED_ID,
			))->fetchAll();

		$min_block_order = Database::prepare(
			"SELECT MIN(block_order) FROM `##block` WHERE module_name=?"
		)->execute(array($this->getName()))->fetchOne();

		$max_block_order = Database::prepare(
			"SELECT MAX(block_order) FROM `##block` WHERE module_name=?"
		)->execute(array($this->getName()))->fetchOne();

		?>
		<ol class="breadcrumb small">
			<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
			<li><a href="admin_modules.php"><?php echo I18N::translate('Module administration'); ?></a></li>
			<li class="active"><?php echo $controller->getPageTitle(); ?></li>
		</ol>
		<h2><?php echo $controller->getPageTitle(); ?></h2>
		<p>
			<?php echo I18N::translate('FAQs are lists of questions and answers, which allow you to explain the site’s rules, policies, and procedures to your visitors.  Questions are typically concerned with privacy, copyright, user-accounts, unsuitable content, requirement for source-citations, etc.'); ?>
			<?php echo I18N::translate('You may use HTML to format the answer and to add links to other websites.'); ?>
		</p>
		<?php

		echo
			'<p><form>',
			I18N::translate('Family tree'), ' ',
			'<input type="hidden" name="mod", value="', $this->getName(), '">',
			'<input type="hidden" name="mod_action" value="admin_config">',
			select_edit_control('ged', Tree::getNameList(), null, WT_GEDCOM),
			'<input type="submit" value="', I18N::translate('show'), '">',
			'</form></p>';

		echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_edit">', I18N::translate('Add an FAQ item'), '</a>';

		echo '<table id="faq_edit">';
		if (empty($faqs)) {
			echo '<tr><td class="error center" colspan="5">', I18N::translate('The FAQ list is empty.'), '</td></tr></table>';
		} else {
			$trees = Tree::getAll();
			foreach ($faqs as $faq) {
				// NOTE: Print the position of the current item
				echo '<tr class="faq_edit_pos"><td>';
				echo I18N::translate('Position item'), ': ', ($faq->block_order + 1), ', ';
				if ($faq->gedcom_id == null) {
					echo I18N::translate('All');
				} else {
					echo $trees[$faq->gedcom_id]->getTitleHtml();
				}
				echo '</td>';
				// NOTE: Print the edit options of the current item
				echo '<td>';
				if ($faq->block_order == $min_block_order) {
					echo '&nbsp;';
				} else {
					echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_moveup&amp;block_id=', $faq->block_id, '" class="icon-uarrow"></a>';
				}
				echo '</td><td>';
				if ($faq->block_order == $max_block_order) {
					echo '&nbsp;';
				} else {
					echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_movedown&amp;block_id=', $faq->block_id, '" class="icon-darrow"></a>';
				}
				echo '</td><td>';
				echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_edit&amp;block_id=', $faq->block_id, '">', I18N::translate('Edit'), '</a>';
				echo '</td><td>';
				echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_delete&amp;block_id=', $faq->block_id, '" onclick="return confirm(\'', I18N::translate('Are you sure you want to delete this FAQ entry?'), '\');">', I18N::translate('Delete'), '</a>';
				echo '</td></tr>';
				// NOTE: Print the title text of the current item
				echo '<tr><td colspan="5">';
				echo '<div class="faq_edit_item">';
				echo '<div class="faq_edit_title">', $faq->header, '</div>';
				// NOTE: Print the body text of the current item
				echo '<div class="faq_edit_content">', substr($faq->faqbody, 0, 1) == '<' ? $faq->faqbody : nl2br($faq->faqbody, false), '</div></div></td></tr>';
			}
			echo '</table>';
		}
	}

	/** {@inheritdoc} */
	public function defaultMenuOrder() {
		return 40;
	}

	/** {@inheritdoc} */
	public function getMenu() {
		if (Auth::isSearchEngine()) {
			return null;
		}

		$faqs = Database::prepare(
			"SELECT block_id FROM `##block` WHERE module_name = :module_name AND IFNULL(gedcom_id, :tree_id_1) = :tree_id_2"
		)->execute(array(
			'module_name' => $this->getName(),
			'tree_id_1'   => WT_GED_ID,
			'tree_id_2'   => WT_GED_ID,
		))->fetchAll();

		if (!$faqs) {
			return null;
		}

		$menu = new Menu(I18N::translate('FAQ'), 'module.php?mod=faq&amp;mod_action=show', 'menu-help');

		return $menu;
	}
}
