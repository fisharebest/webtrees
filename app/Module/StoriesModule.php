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
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use stdClass;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StoriesModule
 */
class StoriesModule extends AbstractModule implements ModuleTabInterface, ModuleConfigInterface, ModuleMenuInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */
			I18N::translate('Stories');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Stories” module */
			I18N::translate('Add narrative stories to individuals in the family tree.');
	}

	/**
	 * The URL to a page where the user can modify the configuration of this module.
	 *
	 * @return string
	 */
	public function getConfigLink() {
		return route('module', ['module' => $this->getName(), 'action' => 'Admin']);
	}

	/** {@inheritdoc} */
	public function defaultTabOrder() {
		return 55;
	}

	/** {@inheritdoc} */
	public function getTabContent(Individual $individual) {
		return view('modules/stories/tab', [
			'is_admin'   => Auth::isAdmin(),
			'individual' => $individual,
			'stories'    => $this->getStoriesForIndividual($individual),
		]);
	}

	/** {@inheritdoc} */
	public function hasTabContent(Individual $individual) {
		return Auth::isManager($individual->getTree()) || !empty($this->getStoriesForIndividual($individual));
	}

	/** {@inheritdoc} */
	public function isGrayedOut(Individual $individual) {
		return !empty($this->getStoriesForIndividual($individual));
	}

	/** {@inheritdoc} */
	public function canLoadAjax() {
		return false;
	}

	/**
	 * @param Individual $individual
	 *
	 * @return stdClass[]
	 */
	private function getStoriesForIndividual(Individual $individual): array {
		$block_ids =
			Database::prepare(
				"SELECT SQL_CACHE block_id" .
				" FROM `##block`" .
				" WHERE module_name = :module_name" .
				" AND xref          = :xref" .
				" AND gedcom_id     = :tree_id"
			)->execute([
				'module_name' => $this->getName(),
				'xref'        => $individual->getXref(),
				'tree_id'     => $individual->getTree()->getTreeId(),
			])->fetchOneColumn();

		$stories = [];
		foreach ($block_ids as $block_id) {
			// Only show this block for certain languages
			$languages = $this->getBlockSetting($block_id, 'languages', '');
			if ($languages === '' || in_array(WT_LOCALE, explode(',', $languages))) {
				$stories[] = (object) [
					'block_id'   => $block_id,
					'title'      => $this->getBlockSetting($block_id, 'title'),
					'story_body' => $this->getBlockSetting($block_id, 'story_body'),
				];
			}
		}

		return $stories;
	}

	/**
	 * The user can re-order menus. Until they do, they are shown in this order.
	 *
	 * @return int
	 */
	public function defaultMenuOrder() {
		return 30;
	}

	/**
	 * What is the default access level for this module?
	 *
	 * Some modules are aimed at admins or managers, and are not generally shown to users.
	 *
	 * @return int
	 */
	public function defaultAccessLevel() {
		return Auth::PRIV_HIDE;
	}

	/**
	 * A menu, to be added to the main application menu.
	 *
	 * @param Tree $tree
	 *
	 * @return Menu|null
	 */
	public function getMenu(Tree $tree) {
		$menu = new Menu($this->getTitle(), e(route('module', ['module' => $this->getName(), 'action' => 'ShowList'])), 'menu-story');

		return $menu;
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

		$stories = Database::prepare(
			"SELECT block_id, xref, gedcom_id" .
			" FROM `##block` b" .
			" WHERE module_name = :module_name" .
			" AND gedcom_id = :tree_id" .
			" ORDER BY gedcom_id, xref"
		)->execute([
			'tree_id'     => $tree->getTreeId(),
			'module_name' => $this->getName(),
		])->fetchAll();

		foreach ($stories as $story) {
			$story->individual = Individual::getInstance($story->xref, $tree);
			$story->title      = $this->getBlockSetting($story->block_id, 'title');
			$story->languages  = $this->getBlockSetting($story->block_id, 'languages');
		}

		return $this->viewResponse('modules/stories/config', [
			'stories'    => $stories,
			'title'      => $this->getTitle() . ' — ' . $tree->getTitle(),
			'tree'       => $tree,
			'tree_names' => Tree::getNameList(),
		]);
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
			// Creating a new story
			$individual  = Individual::getInstance($request->get('xref', ''), $tree);
			$story_title = '';
			$story_body  = '';
			$languages   = [];

			$title = I18N::translate('Add a story') . ' — ' . e($tree->getTitle());
		} else {
			// Editing an existing story
			$xref = Database::prepare(
				"SELECT xref FROM `##block` WHERE block_id = :block_id"
			)->execute([
				'block_id' => $block_id,
			])->fetchOne();

			$individual  = Individual::getInstance($xref, $tree);
			$story_title = $this->getBlockSetting($block_id, 'title', '');
			$story_body  = $this->getBlockSetting($block_id, 'story_body', '');
			$languages   = explode(',', $this->getBlockSetting($block_id, 'languages'));

			$title = I18N::translate('Edit the story') . ' — ' . e($tree->getTitle());
		}

		return $this->viewResponse('modules/stories/edit', [
			'block_id'    => $block_id,
			'languages'   => $languages,
			'story_body'  => $story_body,
			'story_title' => $story_title,
			'title'       => $title,
			'tree'        => $tree,
			'individual'  => $individual,
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

		$block_id    = (int) $request->get('block_id');
		$xref        = $request->get('xref', '');
		$story_body  = $request->get('story_body', '');
		$story_title = $request->get('story_title', '');
		$languages   = $request->get('languages', []);

		if ($block_id !== 0) {
			Database::prepare(
				"UPDATE `##block` SET gedcom_id = :tree_id, xref = :xref WHERE block_id = :block_id"
			)->execute([
				'tree_id'  => $tree->getTreeId(),
				'xref'     => $xref,
				'block_id' => $block_id,
			]);
		} else {
			Database::prepare(
				"INSERT INTO `##block` (gedcom_id, xref, module_name, block_order) VALUES (:tree_id, :xref, 'stories', 0)"
			)->execute([
				'tree_id' => $tree->getTreeId(),
				'xref'    => $xref,
			]);

			$block_id = Database::getInstance()->lastInsertId();
		}

		$this->setBlockSetting($block_id, 'story_body', $story_body);
		$this->setBlockSetting($block_id, 'title', $story_title);
		$this->setBlockSetting($block_id, 'languages', implode(',', $languages));

		$url = route('module', ['module' => 'stories', 'action' => 'Admin', 'ged' => $tree->getName()]);

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function postAdminDeleteAction(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$block_id = (int) $request->get('block_id');

		Database::prepare(
			"DELETE FROM `##block_setting` WHERE block_id = :block_id"
		)->execute([
			'block_id' => $block_id,
		]);

		Database::prepare(
			"DELETE FROM `##block` WHERE block_id = :block_id"
		)->execute([
			'block_id' => $block_id,
		]);

		$url = route('module', ['module' => 'stories', 'action' => 'Admin', 'ged' => $tree->getName()]);

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function getShowListAction(Request $request): Response {
		/** @var Tree $tree
		 */
		$tree = $request->attributes->get('tree');

		$stories = Database::prepare(
			"SELECT block_id, xref" .
			" FROM `##block` b" .
			" WHERE module_name = :module_name" .
			" AND gedcom_id = :tree_id" .
			" ORDER BY xref"
		)->execute([
			'module_name' => $this->getName(),
			'tree_id'     => $tree->getTreeId(),
		])->fetchAll();

		foreach ($stories as $story) {
			$story->individual = Individual::getInstance($story->xref, $tree);
			$story->title      = $this->getBlockSetting($story->block_id, 'title');
			$story->languages  = $this->getBlockSetting($story->block_id, 'languages');
		}

		// Filter non-existant and private individuals.
		$stories = array_filter($stories, function (stdClass $story) {
			return $story->individual !== null && $story->individual->canShow();
		});

		// Filter foreign languages.
		$stories = array_filter($stories, function (stdClass $story) {
			return $story->language === '' || in_array(WT_LOCALE, explode(',', $story->language));
		});

		return $this->viewResponse('modules/stories/list', [
			'stories' => $stories,
			'title'   => $this->getTitle(),
		]);
	}
}
