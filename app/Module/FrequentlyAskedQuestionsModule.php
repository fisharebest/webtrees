<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Tree;
use stdClass;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FrequentlyAskedQuestionsModule
 */
class FrequentlyAskedQuestionsModule extends AbstractModule implements ModuleMenuInterface, ModuleConfigInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module. Abbreviation for “Frequently Asked Questions” */
			I18N::translate('FAQ');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “FAQ” module */
			I18N::translate('A list of frequently asked questions and answers.');
	}

	/**
	 * The URL to a page where the user can modify the configuration of this module.
	 *
	 * @return string
	 */
	public function getConfigLink() {
		return route('module', ['module' => $this->getName(), 'action' => 'Admin']);
	}

	/**
	 * The user can re-order menus. Until they do, they are shown in this order.
	 *
	 * @return int
	 */
	public function defaultMenuOrder() {
		return 40;
	}

	/**
	 * A menu, to be added to the main application menu.
	 *
	 * @param Tree $tree
	 *
	 * @return Menu|null
	 */
	public function getMenu(Tree $tree) {
		$faqs = Database::prepare(
			"SELECT block_id FROM `##block`" .
			" JOIN `##block_setting` USING (block_id)" .
			" WHERE module_name = :module_name AND IFNULL(gedcom_id, :tree_id_1) = :tree_id_2" .
			" AND setting_name='languages' AND (setting_value LIKE CONCAT('%', :locale, '%') OR setting_value='')"
		)->execute([
			'module_name' => $this->getName(),
			'tree_id_1'   => $tree->getTreeId(),
			'tree_id_2'   => $tree->getTreeId(),
			'locale'      => WT_LOCALE,
		])->fetchAll();

		if ($faqs) {
			return new Menu($this->getTitle(), e(route('module', ['module' => 'faq', 'action' => 'Show', 'ged' => $tree->getName()])), 'menu-help');
		} else {
			return null;
		}
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function getAdminAction(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->layout = 'layouts/administration';

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
		)->execute([
			'module_name' => $this->getName(),
			'tree_id_1'   => $tree->getTreeId(),
			'tree_id_2'   => $tree->getTreeId(),
		])->fetchAll();

		$min_block_order = Database::prepare(
			"SELECT MIN(block_order) FROM `##block` WHERE module_name = 'faq' AND (gedcom_id = :tree_id OR gedcom_id IS NULL)"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchOne();

		$max_block_order = Database::prepare(
			"SELECT MAX(block_order) FROM `##block` WHERE module_name = 'faq' AND (gedcom_id = :tree_id OR gedcom_id IS NULL)"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchOne();

		$title = I18N::translate('Frequently asked questions') . ' — ' . $tree->getTitle();

		return $this->viewResponse('modules/faq/config', [
			'faqs'            => $faqs,
			'max_block_order' => $max_block_order,
			'min_block_order' => $min_block_order,
			'title'           => $title,
			'tree'            => $tree,
			'tree_names'      => Tree::getNameList(),
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function postAdminDeleteAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$block_id = (int) $request->get('block_id');

		Database::prepare(
			"DELETE FROM `##block_setting` WHERE block_id = :block_id"
		)->execute(['block_id' => $block_id]);

		Database::prepare(
			"DELETE FROM `##block` WHERE block_id = :block_id"
		)->execute(['block_id' => $block_id]);


		$url = route('module', ['module' => 'faq', 'action' => 'Admin', 'ged' => $tree->getName()]);

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function postAdminMoveDownAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$block_id = (int) $request->get('block_id');

		$block_order = Database::prepare(
			"SELECT block_order FROM `##block` WHERE block_id = :block_id"
		)->execute([
			'block_id' => $block_id,
		])->fetchOne();

		$swap_block = Database::prepare(
			"SELECT block_order, block_id" .
			" FROM `##block`" .
			" WHERE block_order=(" .
			"  SELECT MIN(block_order) FROM `##block` WHERE block_order > :block_order AND module_name = :module_name_1" .
			" ) AND module_name = :module_name_2" .
			" LIMIT 1"
		)->execute([
			'block_order'   => $block_order,
			'module_name_1' => $this->getName(),
			'module_name_2' => $this->getName(),
		])->fetchOneRow();

		if ($swap_block !== null) {
			Database::prepare(
				"UPDATE `##block` SET block_order = :block_order WHERE block_id = :block_id"
			)->execute([
				'block_order' => $swap_block->block_order,
				'block_id'    => $block_id,
			]);
			Database::prepare(
				"UPDATE `##block` SET block_order = :block_order WHERE block_id = :block_id"
			)->execute([
				'block_order' => $block_order,
				'block_id'    => $swap_block->block_id,
			]);
		}

		$url = route('module', ['module' => 'faq', 'action' => 'Admin', 'ged' => $tree->getName()]);

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function postAdminMoveUpAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$block_id = (int) $request->get('block_id');

		$block_order = Database::prepare(
			"SELECT block_order FROM `##block` WHERE block_id = :block_id"
		)->execute([
			'block_id' => $block_id,
			])->fetchOne();

		$swap_block = Database::prepare(
			"SELECT block_order, block_id" .
			" FROM `##block`" .
			" WHERE block_order = (" .
			"  SELECT MAX(block_order) FROM `##block` WHERE block_order < :block_order AND module_name = :module_name_1" .
			" ) AND module_name = :module_name_2" .
			" LIMIT 1"
		)->execute([
			'block_order'   => $block_order,
			'module_name_1' => $this->getName(),
			'module_name_2' => $this->getName(),
		])->fetchOneRow();

		if ($swap_block !== null) {
			Database::prepare(
				"UPDATE `##block` SET block_order = :block_order WHERE block_id = :block_id"
			)->execute([
				'block_order' => $swap_block->block_order,
				'block_id'    => $block_id,
			]);
			Database::prepare(
				"UPDATE `##block` SET block_order = :block_order WHERE block_id = :block_id"
			)->execute([
				'block_order' => $block_order,
				'block_id'    => $swap_block->block_id,
			]);
		}

		$url = route('module', ['module' => 'faq', 'action' => 'Admin', 'ged' => $tree->getName()]);

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function getAdminEditAction(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->layout = 'layouts/administration';

		$block_id = (int) $request->get('block_id');

		if ($block_id === 0) {
			// Creating a new faq
			$header      = '';
			$faqbody     = '';
			$block_order = Database::prepare(
				"SELECT IFNULL(MAX(block_order)+1, 0) FROM `##block` WHERE module_name = :module_name"
			)->execute([
				'module_name' => $this->getName(),
			])->fetchOne();
			$languages = [];

			$title = I18N::translate('Add an FAQ');
		} else {
			// Editing an existing faq
			$header      = $this->getBlockSetting($block_id, 'header');
			$faqbody     = $this->getBlockSetting($block_id, 'faqbody');
			$block_order = Database::prepare(
				"SELECT block_order FROM `##block` WHERE block_id = :block_id"
			)->execute(['block_id' => $block_id])->fetchOne();
			$languages = explode(',', $this->getBlockSetting($block_id, 'languages'));

			$title = I18N::translate('Edit the FAQ');
		}

		// @TODO enable CKEDITOR

		return $this->viewResponse('modules/faq/edit', [
			'block_id'    => $block_id,
			'block_order' => $block_order,
			'header'      => $header,
			'faqbody'     => $faqbody,
			'languages'   => $languages,
			'title'       => $title,
			'tree'        => $tree,
			'tree_names' => Tree::getNameList(),
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function postAdminEditAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$block_id  = (int) $request->get('block_id');
		$faqbody   = $request->get('faqbody', '');
		$header    = $request->get('header', '');
		$languages = $request->get('languages', []);

		if ($block_id !== 0) {
			Database::prepare(
				"UPDATE `##block` SET gedcom_id = NULLIF(:tree_id, '0'), block_order = :block_order WHERE block_id = :block_id"
			)->execute([
				'tree_id'     => Filter::postInteger('gedcom_id'),
				'block_order' => Filter::postInteger('block_order'),
				'block_id'    => $block_id,
			]);
		} else {
			Database::prepare(
				"INSERT INTO `##block` (gedcom_id, module_name, block_order) VALUES (NULLIF(:tree_id, '0'), :module_name, :block_order)"
			)->execute([
				'tree_id'     => Filter::postInteger('gedcom_id'),
				'module_name' => $this->getName(),
				'block_order' => Filter::postInteger('block_order'),
			]);

			$block_id = Database::getInstance()->lastInsertId();
		}

		$this->setBlockSetting($block_id, 'faqbody', $faqbody);
		$this->setBlockSetting($block_id, 'header', $header);
		$this->setBlockSetting($block_id, 'languages', implode(',', $languages));

		$url = route('module', ['module' => 'faq', 'action' => 'Admin', 'ged' => $tree->getName()]);

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function getShowAction(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

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
		)->execute([
			'module_name' => $this->getName(),
			'tree_id_1'   => $tree->getTreeId(),
			'tree_id_2'   => $tree->getTreeId(),
		])->fetchAll();

		// Filter foreign languages.
		$faqs = array_filter($faqs, function (stdClass $faq) {
			return $faq->languages === '' || in_array(WT_LOCALE, explode(',', $faq->languages));
		});

		return $this->viewResponse('modules/faq/show', [
			'faqs' => $faqs,
			'title' => I18N::translate('Frequently asked questions'),
			'tree' => $tree,
		]);
	}
}
