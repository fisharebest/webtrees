<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
namespace Fisharebest\Webtrees\Controller;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the user/tree's home page.
 */
class HomePageController extends PageController {
	/**
	 * Load block asynchronously.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function loadBlock(Request $request): Response {

	}

	/**
	 * Show a tree's home page.
	 */
	public function treePage() {
		$tree        = $this->tree();
		$main_blocks = $this->getTreeBlocks($tree, 'main');
		$side_blocks = $this->getTreeBlocks($tree, 'side');

		return $this->viewResponse('home-page', [
			'title'       => Html::escape($tree->getTitle()),
			'main_blocks' => $main_blocks,
			'side_blocks' => $side_blocks,
		]);
	}

	/**
	 * Show a form to edit the blocks on the home page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function treePageEdit(Request $request): Response {

	}

	/**
	 * Save updated hompe page blocks.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function treePageUpdate(Request $request): RedirectResponse {

	}

	/**
	 * Show a users's home page.
	 */
	public function userPage() {
		$tree        = $this->tree();
		$main_blocks = $this->getUserBlocks($tree, Auth::user(), 'main');
		$side_blocks = $this->getUserBlocks($tree, Auth::user(), 'side');

		return $this->viewResponse('my-page', [
			'title'       => I18N::translate('My page'),
			'main_blocks' => $main_blocks,
			'side_blocks' => $side_blocks,
		]);
	}

	/**
	 * Show a form to edit the blocks on the user's my page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function userPageEdit(Request $request): Response {

	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function userPageUpdate(Request $request): RedirectResponse {

	}

	/**
	 * Get the blocks for a specified user.
	 *
	 * @param Tree   $tree
	 * @param string $location "main" or "side"
	 *
	 * @return ModuleBlockInterface[]
	 */
	private static function getTreeBlocks(Tree $tree, string $location): array {
		$active_blocks = Module::getActiveBlocks($tree);

		if ($tree->getTreeId() < 0) {
			$access_level = Auth::PRIV_NONE;
		} else {
			$access_level = Auth::accessLevel($tree);
		}

		$rows = Database::prepare(
			"SELECT SQL_CACHE block_id, module_name" .
			" FROM  `##block`" .
			" JOIN  `##module` USING (module_name)" .
			" JOIN  `##module_privacy` USING (module_name, gedcom_id)" .
			" WHERE gedcom_id = :tree_id" .
			" AND   status='enabled'" .
			" AND   location = :location" .
			" AND   access_level >= :access_level" .
			" ORDER BY location, block_order"
		)->execute([
			'access_level' => $access_level,
			'location'     => $location,
			'tree_id'      => $tree->getTreeId(),
		])->fetchAssoc();

		// Modules may have been deleted on disk, or may no longer provide "block" functions.
		return array_filter(array_map(function(string $module_name) use ($active_blocks) {
			return $active_blocks[$module_name] ?? false;
		}, $rows));
	}

	/**
	 * Get the blocks for a specified user.
	 *
	 * @param Tree   $tree
	 * @param User   $user
	 * @param string $location "main" or "side"
	 *
	 * @return ModuleBlockInterface[]
	 */
	private static function getUserBlocks(Tree $tree, User $user, string $location): array {
		$active_blocks = Module::getActiveBlocks($tree);

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
			'access_level' => Auth::accessLevel($tree),
			'location'     => $location,
			'user_id'      => $user->getUserId(),
			'tree_id'      => $tree->getTreeId(),
		])->fetchAssoc();

		// Modules may have been deleted on disk, or may no longer provide "block" functions.
		return array_filter(array_map(function(string $module_name) use ($active_blocks) {
			return $active_blocks[$module_name] ?? false;
		}, $rows));
	}
}
