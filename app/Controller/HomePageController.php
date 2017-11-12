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
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Config;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\View;
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
		$blocks = FunctionsDb::getTreeBlocks($this->tree()->getTreeId());
		$active_blocks = Module::getActiveBlocks($this->tree());

		$main_blocks = array_filter($blocks['main'], function(string $block_name) use ($active_blocks) {
			return array_key_exists($block_name, $active_blocks);
		});
		$side_blocks = array_filter($blocks['side'], function(string $block_name) use ($active_blocks) {
			return array_key_exists($block_name, $active_blocks);
		});

		$main_blocks = array_map(function($module_name) use ($active_blocks) {
			return $active_blocks[$module_name];
		}, $main_blocks);

		$side_blocks = array_map(function($module_name) use ($active_blocks) {
			return $active_blocks[$module_name];
		}, $side_blocks);

		$content = View::make('home-page', [
			'main_blocks' => $main_blocks,
			'side_blocks' => $side_blocks
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
		$blocks = FunctionsDb::getUserBlocks(Auth::id());
		$active_blocks = Module::getActiveBlocks($this->tree());

		$main_blocks = array_filter($blocks['main'], function(string $block_name) use ($active_blocks) {
			return array_key_exists($block_name, $active_blocks);
		});
		$side_blocks = array_filter($blocks['side'], function(string $block_name) use ($active_blocks) {
			return array_key_exists($block_name, $active_blocks);
		});

		$main_blocks = array_map(function($module_name) use ($active_blocks) {
			return $active_blocks[$module_name];
		}, $main_blocks);

		$side_blocks = array_map(function($module_name) use ($active_blocks) {
			return $active_blocks[$module_name];
		}, $side_blocks);

		$content = View::make('my-page', [
			'main_blocks' => $main_blocks,
			'side_blocks' => $side_blocks
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
}
