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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for the user/tree's home page.
 */
class HomePageController extends AbstractBaseController {
	/**
	 * Show a form to edit block config options.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 * @throws AccessDeniedHttpException
	 */
	public function treePageBlockEdit(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$block_id = (int) $request->get('block_id');
		$block    = $this->treeBlock($request);
		$title    = $block->getTitle() . ' — ' . I18N::translate('Preferences');

		return $this->viewResponse('blocks/edit-config', [
			'block'      => $block,
			'block_id'   => $block_id,
			'cancel_url' => route('tree-page', ['ged' => $tree->getName()]),
			'title'      => $title,
		]);
	}

	/**
	 * Update block config options.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function treePageBlockUpdate(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree     = $request->attributes->get('tree');
		$block_id = (int) $request->get('block_id');
		$block    = $this->treeBlock($request);

		$block->configureBlock($block_id);

		return new RedirectResponse(route('tree-page', ['ged' => $tree->getName()]));
	}

	/**
	 * Load a block and check we have permission to edit it.
	 *
	 * @param Request $request
	 *
	 * @return ModuleBlockInterface
	 * @throws NotFoundHttpException
	 * @throws AccessDeniedHttpException
	 */
	private function treeBlock(Request $request): ModuleBlockInterface {
		/** @var User $user */
		$user     = $request->attributes->get('user');
		$block_id = (int) $request->get('block_id');

		$block_info = Database::prepare(
			"SELECT module_Name, user_id FROM `##block` WHERE block_id = :block_id"
		)->execute([
			'block_id' => $block_id,
		])->fetchOneRow();

		if ($block_info === null) {
			throw new NotFoundHttpException;
		}

		$block = Module::getModuleByName($block_info->module_name);

		if (!$block instanceof ModuleBlockInterface) {
			throw new NotFoundHttpException;
		}

		if ($block_info->user_id !== $user->getUserId() && !Auth::isAdmin()) {
			throw new AccessDeniedHttpException;
		}

		return $block;
	}

	/**
	 * Show a form to edit block config options.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 * @throws AccessDeniedHttpException
	 */
	public function userPageBlockEdit(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$block_id = (int) $request->get('block_id');
		$block    = $this->userBlock($request);
		$title    = $block->getTitle() . ' — ' . I18N::translate('Preferences');

		return $this->viewResponse('blocks/edit-config', [
			'block'      => $block,
			'block_id'   => $block_id,
			'cancel_url' => route('user-page', ['ged' => $tree->getName()]),
			'title'      => $title,
		]);
	}

	/**
	 * Update block config options.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function userPageBlockUpdate(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree     = $request->attributes->get('tree');
		$block_id = (int) $request->get('block_id');
		$block    = $this->userBlock($request);

		$block->configureBlock($block_id);

		return new RedirectResponse(route('user-page', ['ged' => $tree->getName()]));
	}

	/**
	 * Load a block and check we have permission to edit it.
	 *
	 * @param Request $request
	 *
	 * @return ModuleBlockInterface
	 * @throws NotFoundHttpException
	 * @throws AccessDeniedHttpException
	 */
	private function userBlock(Request $request): ModuleBlockInterface {
		/** @var User $user */
		$user = $request->attributes->get('user');

		$block_id = (int) $request->get('block_id');

		$block_info = Database::prepare(
			"SELECT module_Name, user_id FROM `##block` WHERE block_id = :block_id"
		)->execute([
			'block_id' => $block_id,
		])->fetchOneRow();

		if ($block_info === null) {
			throw new NotFoundHttpException('This block does not exist');
		}

		$block = Module::getModuleByName($block_info->module_name);

		if (!$block instanceof ModuleBlockInterface) {
			throw new NotFoundHttpException($block_info->module_name . ' is not a block');
		}

		$block_owner_id = (int) $block_info->user_id;

		if ($block_owner_id !== $user->getUserId() && !Auth::isAdmin()) {
			throw new AccessDeniedHttpException('You are not allowed to edit this block');
		}

		return $block;
	}

	/**
	 * Show a tree's page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function treePage(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$tree_id      = $tree->getTreeId();
		$access_level = Auth::accessLevel($tree);
		$main_blocks  = $this->getBlocksForTreePage($tree_id, $access_level, 'main');
		$side_blocks  = $this->getBlocksForTreePage($tree_id, $access_level, 'side');
		$title        = e($tree->getTitle());

		// @TODO - ModuleBlockInterface::getBlock() currently relies on these globals
		global $WT_TREE, $ctype, $controller;
		$WT_TREE    = $tree;
		$ctype      = 'gedcom';
		$controller = $this;

		return $this->viewResponse('tree-page', [
			'main_blocks' => $main_blocks,
			'side_blocks' => $side_blocks,
			'title'       => $title,
			'meta_robots' => 'index,follow',
		]);
	}

	/**
	 * Load block asynchronously.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function treePageBlock(Request $request): Response {
		/** @var Tree $tree */
		$tree     = $request->attributes->get('tree');
		$block_id = (int) $request->get('block_id');

		$block = Database::prepare(
			"SELECT * FROM `##block` WHERE block_id = :block_id AND gedcom_id = :tree_id AND user_id IS NULL"
		)->execute([
			'block_id' => $block_id,
			'tree_id'  => $tree->getTreeId(),
		])->fetchOneRow();

		$module = $this->getBlockModule($tree, $block_id);

		if ($block === null || $module === null) {
			return new Response('', 404);
		}

		// @TODO - ModuleBlockInterface::getBlock() currently relies on these globals
		global $WT_TREE, $ctype, $controller;
		$WT_TREE    = $tree;
		$ctype      = 'gedcom';
		$controller = $this;

		$html = view('layouts/ajax', [
			'content' => $module->getBlock($block_id, true),
		]);


		// Use HTTP headers and some jQuery to add debug to the current page.
		DebugBar::sendDataInHeaders();

		return new Response($html);
	}

	/**
	 * Show a form to edit the default blocks for new trees.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function treePageDefaultEdit(Request $request): Response {
		$main_blocks = $this->getBlocksForTreePage(-1, Auth::PRIV_NONE, 'main');
		$side_blocks = $this->getBlocksForTreePage(-1, Auth::PRIV_NONE, 'side');
		$all_blocks  = $this->getAvailableTreeBlocks();
		$title       = I18N::translate('Set the default blocks for new family trees');
		$url_cancel  = route('admin-control-panel');
		$url_save    = route('tree-page-default-update');

		return $this->viewResponse('edit-blocks-page', [
			'all_blocks'  => $all_blocks,
			'can_reset'   => false,
			'main_blocks' => $main_blocks,
			'side_blocks' => $side_blocks,
			'title'       => $title,
			'url_cancel'  => $url_cancel,
			'url_save'    => $url_save,
		]);
	}

	/**
	 * Save updated default blocks for new trees.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function treePageDefaultUpdate(Request $request): RedirectResponse {
		$main_blocks = (array) $request->get('main');
		$side_blocks = (array) $request->get('side');

		$this->updateTreeBlocks(-1, $main_blocks, $side_blocks);

		return new RedirectResponse(route('admin-control-panel'));
	}

	/**
	 * Show a form to edit the blocks on a tree's page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function treePageEdit(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$main_blocks = $this->getBlocksForTreePage($tree->getTreeId(), Auth::accessLevel($tree), 'main');
		$side_blocks = $this->getBlocksForTreePage($tree->getTreeId(), Auth::accessLevel($tree), 'side');
		$all_blocks  = $this->getAvailableTreeBlocks();
		$title       = I18N::translate('Change the “Home page” blocks');
		$url_cancel  = route('tree-page', ['ged' => $tree->getName()]);
		$url_save    = route('tree-page-update', ['ged' => $tree->getName()]);

		return $this->viewResponse('edit-blocks-page', [
			'all_blocks'  => $all_blocks,
			'can_reset'   => true,
			'main_blocks' => $main_blocks,
			'side_blocks' => $side_blocks,
			'title'       => $title,
			'url_cancel'  => $url_cancel,
			'url_save'    => $url_save,
		]);
	}

	/**
	 * Save updated blocks on a tree's page.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function treePageUpdate(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$defaults = (bool) $request->get('defaults');

		if ($defaults) {
			$main_blocks = $this->getBlocksForTreePage(-1, AUth::PRIV_NONE, 'main');
			$side_blocks = $this->getBlocksForTreePage(-1, Auth::PRIV_NONE, 'side');
		} else {
			$main_blocks = (array) $request->get('main');
			$side_blocks = (array) $request->get('side');
		}

		$this->updateTreeBlocks($tree->getTreeId(), $main_blocks, $side_blocks);

		return new RedirectResponse(route('tree-page', ['ged' => $tree->getName()]));
	}

	/**
	 * Show a users's page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function userPage(Request $request) {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		/** @var User $user */
		$user = $request->attributes->get('user');

		$tree_id      = $tree->getTreeId();
		$user_id      = $user->getUserId();
		$access_level = Auth::accessLevel($tree, $user);
		$main_blocks  = $this->getBlocksForUserPage($tree_id, $user_id, $access_level, 'main');
		$side_blocks  = $this->getBlocksForUserPage($tree_id, $user_id, $access_level, 'side');
		$title        = I18N::translate('My page');

		// @TODO - ModuleBlockInterface::getBlock() currently relies on these globals
		global $WT_TREE, $ctype, $controller;
		$WT_TREE    = $tree;
		$ctype      = 'user';
		$controller = $this;

		return $this->viewResponse('user-page', [
			'main_blocks' => $main_blocks,
			'side_blocks' => $side_blocks,
			'title'       => $title,
		]);
	}

	/**
	 * Load block asynchronously.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function userPageBlock(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		/** @var User $user */
		$user = $request->attributes->get('user');

		$block_id = (int) $request->get('block_id');

		$block = Database::prepare(
			"SELECT * FROM `##block` WHERE block_id = :block_id AND gedcom_id IS NULL AND user_id = :user_id"
		)->execute([
			'block_id' => $block_id,
			'user_id'  => $user->getUserId(),
		])->fetchOneRow();

		$module = $this->getBlockModule($tree, $block_id);

		if ($block === null || $module === null) {
			return new Response('Block not found', 404);
		}

		// @TODO - ModuleBlockInterface::getBlock() relies on these globals :-(
		global $WT_TREE, $ctype, $controller;
		$WT_TREE    = $tree;
		$ctype      = 'user';
		$controller = $this;

		$html = view('layouts/ajax', [
			'content' => $module->getBlock($block_id, true),
		]);

		// Use HTTP headers and some jQuery to add debug to the current page.
		DebugBar::sendDataInHeaders();

		return new Response($html);
	}

	/**
	 * Show a form to edit the default blocks for new uesrs.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function userPageDefaultEdit(Request $request): Response {
		$main_blocks = $this->getBlocksForUserPage(-1, -1, Auth::PRIV_NONE, 'main');
		$side_blocks = $this->getBlocksForUserPage(-1, -1, Auth::PRIV_NONE, 'side');
		$all_blocks  = $this->getAvailableUserBlocks();
		$title       = I18N::translate('Set the default blocks for new users');
		$url_cancel  = route('admin-users');
		$url_save    = route('user-page-default-update');

		return $this->viewResponse('edit-blocks-page', [
			'all_blocks'  => $all_blocks,
			'can_reset'   => false,
			'main_blocks' => $main_blocks,
			'side_blocks' => $side_blocks,
			'title'       => $title,
			'url_cancel'  => $url_cancel,
			'url_save'    => $url_save,
		]);
	}

	/**
	 * Save the updated default blocks for new users.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function userPageDefaultUpdate(Request $request): RedirectResponse {
		$main_blocks = (array) $request->get('main');
		$side_blocks = (array) $request->get('side');

		$this->updateUserBlocks(-1, $main_blocks, $side_blocks);

		return new RedirectResponse(route('admin-control-panel'));
	}

	/**
	 * Show a form to edit the blocks on the user's page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function userPageEdit(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		/** @var User $user */
		$user = $request->attributes->get('user');

		$main_blocks = $this->getBlocksForUserPage($tree->getTreeId(), $user->getUserId(), Auth::accessLevel($tree, $user), 'main');
		$side_blocks = $this->getBlocksForUserPage($tree->getTreeId(), $user->getUserId(), Auth::accessLevel($tree, $user), 'side');
		$all_blocks  = $this->getAvailableUserBlocks();
		$title       = I18N::translate('Change the “My page” blocks');
		$url_cancel  = route('user-page', ['ged' => $tree->getName()]);
		$url_save    = route('user-page-update', ['ged' => $tree->getName()]);

		return $this->viewResponse('edit-blocks-page', [
			'all_blocks'  => $all_blocks,
			'can_reset'   => true,
			'main_blocks' => $main_blocks,
			'side_blocks' => $side_blocks,
			'title'       => $title,
			'url_cancel'  => $url_cancel,
			'url_save'    => $url_save,
		]);
	}

	/**
	 * Save the updted blocks on a user's page.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function userPageUpdate(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		/** @var User $user */
		$user = $request->attributes->get('user');

		$defaults = (bool) $request->get('defaults');

		if ($defaults) {
			$main_blocks = $this->getBlocksForUserPage(-1, -1, AUth::PRIV_NONE, 'main');
			$side_blocks = $this->getBlocksForUserPage(-1, -1, Auth::PRIV_NONE, 'side');
		} else {
			$main_blocks = (array) $request->get('main');
			$side_blocks = (array) $request->get('side');
		}

		$this->updateUserBlocks($user->getUserId(), $main_blocks, $side_blocks);

		return new RedirectResponse(route('user-page', ['ged' => $tree->getName()]));
	}

	/**
	 * Show a form to edit the blocks for another user's page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function userPageUserEdit(Request $request): Response {
		$user_id     = (int) $request->get('user_id');
		$user        = User::find($user_id);
		$main_blocks = $this->getBlocksForUserPage(-1, $user_id, Auth::PRIV_NONE, 'main');
		$side_blocks = $this->getBlocksForUserPage(-1, $user_id, Auth::PRIV_NONE, 'side');
		$all_blocks  = $this->getAvailableUserBlocks();
		$title       = I18N::translate('Change the blocks on this user’s “My page”') . ' - ' . e($user->getUserName());
		$url_cancel  = route('admin-users');
		$url_save    = route('user-page-user-update', ['user_id' => $user_id]);

		return $this->viewResponse('edit-blocks-page', [
			'all_blocks'  => $all_blocks,
			'can_reset'   => false,
			'main_blocks' => $main_blocks,
			'side_blocks' => $side_blocks,
			'title'       => $title,
			'url_cancel'  => $url_cancel,
			'url_save'    => $url_save,
		]);
	}

	/**
	 * Save the updated blocks for another user's page.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function userPageUserUpdate(Request $request): RedirectResponse {
		$user_id     = (int) $request->get('user_id');
		$main_blocks = (array) $request->get('main');
		$side_blocks = (array) $request->get('side');

		$this->updateUserBlocks($user_id, $main_blocks, $side_blocks);

		return new RedirectResponse(route('admin-control-panel'));
	}

	/**
	 * Get a specific block.
	 *
	 * @param Tree $tree
	 * @param int  $block_id
	 *
	 * @return ModuleBlockInterface|null
	 */
	private function getBlockModule(Tree $tree, int $block_id) {
		$active_blocks = Module::getActiveBlocks($tree);

		$module_name = Database::prepare(
			"SELECT module_name FROM `##block`" .
			" JOIN  `##module` USING (module_name)" .
			" WHERE block_id = :block_id AND status = 'enabled'"
		)->execute([
			'block_id' => $block_id,
		])->fetchOne();

		return $active_blocks[$module_name] ?? null;
	}

	/**
	 * Get all the available blocks for a tree page.
	 *
	 * @return ModuleBlockInterface[]
	 */
	private function getAvailableTreeBlocks(): array {
		$blocks = Module::getAllModulesByComponent('block');
		$blocks = array_filter($blocks, function (ModuleBlockInterface $block) {
			return $block->isGedcomBlock();
		});

		return $blocks;
	}

	/**
	 * Get all the available blocks for a user page.
	 *
	 * @return ModuleBlockInterface[]
	 */
	private function getAvailableUserBlocks(): array {
		$blocks = Module::getAllModulesByComponent('block');
		$blocks = array_filter($blocks, function (ModuleBlockInterface $block) {
			return $block->isUserBlock();
		});

		return $blocks;
	}

	/**
	 * Get the blocks for a specified tree (or the default tree).
	 *
	 * @param int    $tree_id
	 * @param int    $access_level
	 * @param string $location "main" or "side"
	 *
	 * @return ModuleBlockInterface[]
	 */
	private function getBlocksForTreePage(int $tree_id, int $access_level, string $location): array {
		$rows = Database::prepare(
			"SELECT SQL_CACHE block_id, module_name" .
			" FROM  `##block`" .
			" JOIN  `##module` USING (module_name)" .
			" JOIN  `##module_privacy` USING (module_name, gedcom_id)" .
			" WHERE gedcom_id = :tree_id" .
			" AND   status = 'enabled'" .
			" AND   location = :location" .
			" AND   access_level >= :access_level" .
			" ORDER BY location, block_order"
		)->execute([
			'access_level' => $access_level,
			'location'     => $location,
			'tree_id'      => $tree_id,
		])->fetchAssoc();

		return $this->filterActiveBlocks($rows, $this->getAvailableTreeBlocks());
	}

	/**
	 * Get the blocks for a specified user (or the default user).
	 *
	 * @param int    $tree_id
	 * @param int    $user_id
	 * @param int    $access_level
	 * @param string $location "main" or "side"
	 *
	 * @return ModuleBlockInterface[]
	 */
	private function getBlocksForUserPage(int $tree_id, int $user_id, int $access_level, string $location): array {
		$rows = Database::prepare(
			"SELECT SQL_CACHE block_id, module_name" .
			" FROM  `##block`" .
			" JOIN  `##module` USING (module_name)" .
			" JOIN  `##module_privacy` USING (module_name)" .
			" WHERE user_id = :user_id" .
			" AND   status = 'enabled'" .
			" AND   location = :location" .
			" AND   `##module_privacy`.gedcom_id = :tree_id" .
			" AND   access_level >= :access_level" .
			" ORDER BY block_order"
		)->execute([
			'access_level' => $access_level,
			'location'     => $location,
			'user_id'      => $user_id,
			'tree_id'      => $tree_id,
		])->fetchAssoc();

		return $this->filterActiveBlocks($rows, $this->getAvailableUserBlocks());
	}

	/**
	 * Take a list of block names, and return block (module) objects.
	 *
	 * @param string[] $blocks
	 * @param array    $active_blocks
	 *
	 * @return ModuleBlockInterface[]
	 */
	private function filterActiveBlocks(array $blocks, array $active_blocks): array {
		return array_filter(array_map(function (string $module_name) use ($active_blocks) {
			return $active_blocks[$module_name] ?? false;
		}, $blocks));
	}

	/**
	 * Save the updated blocks for a tree.
	 *
	 * @param int   $tree_id
	 * @param array $main_blocks
	 * @param array $side_blocks
	 */
	private function updateTreeBlocks(int $tree_id, array $main_blocks, array $side_blocks) {
		$existing_block_ids = Database::prepare(
			"SELECT block_id FROM `##block` WHERE gedcom_id = :tree_id"
		)->execute([
			'tree_id' => $tree_id,
		])->fetchOneColumn();

		// Deleted blocks
		foreach ($existing_block_ids as $existing_block_id) {
			if (!in_array($existing_block_id, $main_blocks) && !in_array($existing_block_id, $side_blocks)) {
				Database::prepare(
					"DELETE FROM `##block_setting` WHERE block_id = :block_id"
				)->execute([
					'block_id' => $existing_block_id,
				]);
				Database::prepare(
					"DELETE FROM `##block` WHERE block_id = :block_id"
				)->execute([
					'block_id' => $existing_block_id,
				]);
			}
		}

		$updates = [
			'main' => $main_blocks,
			'side' => $side_blocks,
		];

		foreach ($updates as $location => $updated_blocks) {
			foreach ($updated_blocks as $block_order => $block_id) {
				if (is_numeric($block_id)) {
					// Updated block
					Database::prepare(
						"UPDATE `##block`" .
						" SET block_order = :block_order, location = :location" .
						" WHERE block_id = :block_id"
					)->execute([
						'block_order' => $block_order,
						'block_id'    => $block_id,
						'location'    => $location,
					]);
				} else {
					// New block
					Database::prepare(
						"INSERT INTO `##block` (gedcom_id, location, block_order, module_name)" .
						" VALUES (:tree_id, :location, :block_order, :module_name)"
					)->execute([
						'tree_id'     => $tree_id,
						'location'    => $location,
						'block_order' => $block_order,
						'module_name' => $block_id,
					]);
				}
			}
		}
	}

	/**
	 * Save the updated blocks for a user.
	 *
	 * @param int   $user_id
	 * @param array $main_blocks
	 * @param array $side_blocks
	 */
	private function updateUserBlocks(int $user_id, array $main_blocks, array $side_blocks) {
		$existing_block_ids = Database::prepare(
			"SELECT block_id FROM `##block` WHERE user_id = :user_id"
		)->execute([
			'user_id' => $user_id,
		])->fetchOneColumn();

		// Deleted blocks
		foreach ($existing_block_ids as $existing_block_id) {
			if (!in_array($existing_block_id, $main_blocks) && !in_array($existing_block_id, $side_blocks)) {
				Database::prepare(
					"DELETE FROM `##block_setting` WHERE block_id = :block_id"
				)->execute([
					'block_id' => $existing_block_id,
				]);
				Database::prepare(
					"DELETE FROM `##block` WHERE block_id = :block_id"
				)->execute([
					'block_id' => $existing_block_id,
				]);
			}
		}

		foreach (['main' => $main_blocks, 'side' => $side_blocks] as $location => $updated_blocks) {
			foreach ($updated_blocks as $block_order => $block_id) {
				if (is_numeric($block_id)) {
					// Updated block
					Database::prepare(
						"UPDATE `##block`" .
						" SET block_order = :block_order, location = :location" .
						" WHERE block_id = :block_id"
					)->execute([
						'block_order' => $block_order,
						'block_id'    => $block_id,
						'location'    => $location,
					]);
				} else {
					// New block
					Database::prepare(
						"INSERT INTO `##block` (user_id, location, block_order, module_name)" .
						" VALUES (:user_id, :location, :block_order, :module_name)"
					)->execute([
						'user_id'     => $user_id,
						'location'    => $location,
						'block_order' => $block_order,
						'module_name' => $block_id,
					]);
				}
			}
		}
	}
}
