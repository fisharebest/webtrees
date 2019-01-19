<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for the user/tree's home page.
 */
class HomePageController extends AbstractBaseController
{
    /**
     * Show a form to edit block config options.
     *
     * @param Request $request
     * @param Tree    $tree
     * @param User    $user
     *
     * @return Response
     */
    public function treePageBlockEdit(Request $request, Tree $tree, User $user): Response
    {
        $block_id = (int) $request->get('block_id');
        $block    = $this->treeBlock($request, $tree, $user);
        $title    = $block->title() . ' — ' . I18N::translate('Preferences');

        return $this->viewResponse('modules/edit-block-config', [
            'block'      => $block,
            'block_id'   => $block_id,
            'cancel_url' => route('tree-page', ['ged' => $tree->name()]),
            'title'      => $title,
            'tree'       => $tree,
        ]);
    }

    /**
     * Update block config options.
     *
     * @param Request $request
     * @param Tree    $tree
     * @param User    $user
     *
     * @return RedirectResponse
     */
    public function treePageBlockUpdate(Request $request, Tree $tree, User $user): RedirectResponse
    {
        $block    = $this->treeBlock($request, $tree, $user);
        $block_id = (int) $request->get('block_id');

        $block->saveBlockConfiguration($request, $block_id);

        return new RedirectResponse(route('tree-page', ['ged' => $tree->name()]));
    }

    /**
     * Load a block and check we have permission to edit it.
     *
     * @param Request $request
     * @param Tree    $tree
     * @param User    $user
     *
     * @return ModuleBlockInterface
     */
    private function treeBlock(Request $request, Tree $tree, User $user): ModuleBlockInterface
    {
        $block_id = (int) $request->get('block_id');

        $block = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->where('gedcom_id', '=', $tree->id())
            ->whereNull('user_id')
            ->first();

        if ($block === null) {
            throw new NotFoundHttpException();
        }

        $module = Module::getModuleByName($block->module_name);

        if (!$module instanceof ModuleBlockInterface) {
            throw new NotFoundHttpException();
        }

        if ($block->user_id !== $user->getUserId() && !Auth::isAdmin()) {
            throw new AccessDeniedHttpException();
        }

        return $module;
    }

    /**
     * Show a form to edit block config options.
     *
     * @param Request $request
     * @param Tree    $tree
     * @param User    $user
     *
     * @return Response
     */
    public function userPageBlockEdit(Request $request, Tree $tree, User $user): Response
    {
        $block_id = (int) $request->get('block_id');
        $block    = $this->userBlock($request, $user);
        $title    = $block->title() . ' — ' . I18N::translate('Preferences');

        return $this->viewResponse('modules/edit-block-config', [
            'block'      => $block,
            'block_id'   => $block_id,
            'cancel_url' => route('user-page', ['ged' => $tree->name()]),
            'title'      => $title,
            'tree'       => $tree,
        ]);
    }

    /**
     * Update block config options.
     *
     * @param Request $request
     * @param Tree    $tree
     * @param User    $user
     *
     * @return RedirectResponse
     */
    public function userPageBlockUpdate(Request $request, Tree $tree, User $user): RedirectResponse
    {
        $block    = $this->userBlock($request, $user);
        $block_id = (int) $request->get('block_id');

        $block->saveBlockConfiguration($request, $block_id);

        return new RedirectResponse(route('user-page', ['ged' => $tree->name()]));
    }

    /**
     * Load a block and check we have permission to edit it.
     *
     * @param Request $request
     * @param User    $user
     *
     * @return ModuleBlockInterface
     */
    private function userBlock(Request $request, User $user): ModuleBlockInterface
    {
        $block_id = (int) $request->get('block_id');

        $block = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->where('user_id', '=', $user->getUserId())
            ->whereNull('gedcom_id')
            ->first();

        if ($block === null) {
            throw new NotFoundHttpException('This block does not exist');
        }

        $module = Module::getModuleByName($block->module_name);

        if (!$module instanceof ModuleBlockInterface) {
            throw new NotFoundHttpException($block->module_name . ' is not a block');
        }

        $block_owner_id = (int) $block->user_id;

        if ($block_owner_id !== $user->getUserId() && !Auth::isAdmin()) {
            throw new AccessDeniedHttpException('You are not allowed to edit this block');
        }

        return $module;
    }

    /**
     * Show a tree's page.
     *
     * @param Tree $tree
     *
     * @return Response
     */
    public function treePage(Tree $tree): Response
    {
        $tree_id      = $tree->id();
        $access_level = Auth::accessLevel($tree);
        $main_blocks  = $this->getBlocksForTreePage($tree_id, $access_level, 'main');
        $side_blocks  = $this->getBlocksForTreePage($tree_id, $access_level, 'side');
        $title        = e($tree->title());

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
     * @param Tree    $tree
     *
     * @return Response
     */
    public function treePageBlock(Request $request, Tree $tree): Response
    {
        $block_id = (int) $request->get('block_id');

        $block = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->where('gedcom_id', '=', $tree->id())
            ->whereNull('user_id')
            ->first();

        $module = $this->getBlockModule($tree, $block_id);

        if ($block === null || $module === null) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $html = view('layouts/ajax', [
            'content' => $module->getBlock($tree, $block_id, 'gedcom'),
        ]);


        // Use HTTP headers and some jQuery to add debug to the current page.
        DebugBar::sendDataInHeaders();

        return new Response($html);
    }

    /**
     * Show a form to edit the default blocks for new trees.
     *
     * @return Response
     */
    public function treePageDefaultEdit(): Response
    {
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
    public function treePageDefaultUpdate(Request $request): RedirectResponse
    {
        $main_blocks = (array) $request->get('main');
        $side_blocks = (array) $request->get('side');

        $this->updateTreeBlocks(-1, $main_blocks, $side_blocks);

        return new RedirectResponse(route('admin-control-panel'));
    }

    /**
     * Show a form to edit the blocks on a tree's page.
     *
     * @param Tree $tree
     *
     * @return Response
     */
    public function treePageEdit(Tree $tree): Response
    {
        $main_blocks = $this->getBlocksForTreePage($tree->id(), Auth::accessLevel($tree), 'main');
        $side_blocks = $this->getBlocksForTreePage($tree->id(), Auth::accessLevel($tree), 'side');
        $all_blocks  = $this->getAvailableTreeBlocks();
        $title       = I18N::translate('Change the “Home page” blocks');
        $url_cancel  = route('tree-page', ['ged' => $tree->name()]);
        $url_save    = route('tree-page-update', ['ged' => $tree->name()]);

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
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function treePageUpdate(Request $request, Tree $tree): RedirectResponse
    {
        $defaults = (bool) $request->get('defaults');

        if ($defaults) {
            $main_blocks = $this->getBlocksForTreePage(-1, AUth::PRIV_NONE, 'main');
            $side_blocks = $this->getBlocksForTreePage(-1, Auth::PRIV_NONE, 'side');
        } else {
            $main_blocks = (array) $request->get('main');
            $side_blocks = (array) $request->get('side');
        }

        $this->updateTreeBlocks($tree->id(), $main_blocks, $side_blocks);

        return new RedirectResponse(route('tree-page', ['ged' => $tree->name()]));
    }

    /**
     * Show a users's page.
     *
     * @param Tree $tree
     * @param User $user
     *
     * @return Response
     */
    public function userPage(Tree $tree, User $user): Response
    {
        $tree_id      = $tree->id();
        $user_id      = $user->getUserId();
        $access_level = Auth::accessLevel($tree, $user);
        $main_blocks  = $this->getBlocksForUserPage($tree_id, $user_id, $access_level, 'main');
        $side_blocks  = $this->getBlocksForUserPage($tree_id, $user_id, $access_level, 'side');
        $title        = I18N::translate('My page');

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
     * @param Tree    $tree
     * @param User    $user
     *
     * @return Response
     */
    public function userPageBlock(Request $request, Tree $tree, User $user): Response
    {
        $block_id = (int) $request->get('block_id');

        $block = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->where('user_id', '=', $user->getUserId())
            ->whereNull('gedcom_id')
            ->first();

        $module = $this->getBlockModule($tree, $block_id);

        if ($block === null || $module === null) {
            return new Response('Block not found', Response::HTTP_NOT_FOUND);
        }

        $html = view('layouts/ajax', [
            'content' => $module->getBlock($tree, $block_id, 'user'),
        ]);

        // Use HTTP headers and some jQuery to add debug to the current page.
        DebugBar::sendDataInHeaders();

        return new Response($html);
    }

    /**
     * Show a form to edit the default blocks for new uesrs.
     *
     * @return Response
     */
    public function userPageDefaultEdit(): Response
    {
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
    public function userPageDefaultUpdate(Request $request): RedirectResponse
    {
        $main_blocks = (array) $request->get('main');
        $side_blocks = (array) $request->get('side');

        $this->updateUserBlocks(-1, $main_blocks, $side_blocks);

        return new RedirectResponse(route('admin-control-panel'));
    }

    /**
     * Show a form to edit the blocks on the user's page.
     *
     * @param Tree $tree
     * @param User $user
     *
     * @return Response
     */
    public function userPageEdit(Tree $tree, User $user): Response
    {
        $main_blocks = $this->getBlocksForUserPage($tree->id(), $user->getUserId(), Auth::accessLevel($tree, $user), 'main');
        $side_blocks = $this->getBlocksForUserPage($tree->id(), $user->getUserId(), Auth::accessLevel($tree, $user), 'side');
        $all_blocks  = $this->getAvailableUserBlocks();
        $title       = I18N::translate('Change the “My page” blocks');
        $url_cancel  = route('user-page', ['ged' => $tree->name()]);
        $url_save    = route('user-page-update', ['ged' => $tree->name()]);

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
     * @param Tree    $tree
     * @param User    $user
     *
     * @return RedirectResponse
     */
    public function userPageUpdate(Request $request, Tree $tree, User $user): RedirectResponse
    {
        $defaults = (bool) $request->get('defaults');

        if ($defaults) {
            $main_blocks = $this->getBlocksForUserPage(-1, -1, AUth::PRIV_NONE, 'main');
            $side_blocks = $this->getBlocksForUserPage(-1, -1, Auth::PRIV_NONE, 'side');
        } else {
            $main_blocks = (array) $request->get('main');
            $side_blocks = (array) $request->get('side');
        }

        $this->updateUserBlocks($user->getUserId(), $main_blocks, $side_blocks);

        return new RedirectResponse(route('user-page', ['ged' => $tree->name()]));
    }

    /**
     * Show a form to edit the blocks for another user's page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function userPageUserEdit(Request $request): Response
    {
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
    public function userPageUserUpdate(Request $request): RedirectResponse
    {
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
    private function getBlockModule(Tree $tree, int $block_id)
    {
        $active_blocks = Module::activeBlocks($tree);

        $module_name = DB::table('block')
            ->join('module', 'block.module_name', '=', 'module.module_name')
            ->where('block_id', '=', $block_id)
            ->where('status', '=', 'enabled')
            ->value('module.module_name');

        return $active_blocks->filter(function (ModuleInterface $module) use ($module_name): bool {
            return $module->getName() === $module_name;
        })->first();
    }

    /**
     * Get all the available blocks for a tree page.
     *
     * @return Collection|ModuleBlockInterface[]
     */
    private function getAvailableTreeBlocks(): Collection
    {
        return Module::getAllModulesByInterface(ModuleBlockInterface::class)
            ->filter(function (ModuleBlockInterface $block): bool {
                return $block->isGedcomBlock();
            })
            ->mapWithKeys(function (ModuleInterface $block): array {
                return [$block->getName() => $block];
            });
    }

    /**
     * Get all the available blocks for a user page.
     *
     * @return Collection|ModuleBlockInterface[]
     */
    private function getAvailableUserBlocks(): Collection
    {
        return Module::getAllModulesByInterface(ModuleBlockInterface::class)
            ->filter(function (ModuleBlockInterface $block): bool {
                return $block->isUserBlock();
            })
            ->mapWithKeys(function (ModuleInterface $block): array {
                return [$block->getName() => $block];
            });
    }

    /**
     * Get the blocks for a specified tree (or the default tree).
     *
     * @param int    $tree_id
     * @param int    $access_level
     * @param string $location "main" or "side"
     *
     * @return Collection|ModuleBlockInterface[]
     */
    private function getBlocksForTreePage(int $tree_id, int $access_level, string $location): Collection
    {
        $rows = DB::table('block')
            ->join('module', 'module.module_name', '=', 'block.module_name')
            ->join('module_privacy', 'module_privacy.module_name', '=', 'module.module_name')
            ->where('block.gedcom_id', '=', $tree_id)
            ->where('module_privacy.gedcom_id', '=', $tree_id)
            ->where('location', '=', $location)
            ->where('status', '=', 'enabled')
            ->where('access_level', '>=', $access_level)
            ->orderBy('block_order')
            ->pluck('block.module_name', 'block_id');

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
     * @return Collection|ModuleBlockInterface[]
     */
    private function getBlocksForUserPage(int $tree_id, int $user_id, int $access_level, string $location): Collection
    {
        $rows = DB::table('block')
            ->join('module', 'module.module_name', '=', 'block.module_name')
            ->join('module_privacy', 'module_privacy.module_name', '=', 'module.module_name')
            ->where('user_id', '=', $user_id)
            ->where('module_privacy.gedcom_id', '=', $tree_id)
            ->where('location', '=', $location)
            ->where('status', '=', 'enabled')
            ->where('access_level', '>=', $access_level)
            ->orderBy('block_order')
            ->pluck('block.module_name', 'block_id');

        return $this->filterActiveBlocks($rows, $this->getAvailableUserBlocks());
    }

    /**
     * Take a list of block names, and return block (module) objects.
     *
     * @param Collection $blocks
     * @param Collection $active_blocks
     *
     * @return Collection|ModuleBlockInterface[]
     */
    private function filterActiveBlocks(Collection $blocks, Collection $active_blocks): Collection
    {
        return $blocks->map(function (string $block_name) use ($active_blocks): ?ModuleBlockInterface {
                return $active_blocks->filter(function (ModuleInterface $block) use ($block_name): bool {
                    return $block->getName() === $block_name;
                })->first();
            })
            ->filter();
    }

    /**
     * Save the updated blocks for a tree.
     *
     * @param int   $tree_id
     * @param array $main_blocks
     * @param array $side_blocks
     *
     * @return void
     */
    private function updateTreeBlocks(int $tree_id, array $main_blocks, array $side_blocks)
    {
        $existing_block_ids = DB::table('block')
            ->where('gedcom_id', '=', $tree_id)
            ->pluck('block_id');

        // Deleted blocks
        foreach ($existing_block_ids as $existing_block_id) {
            if (!in_array($existing_block_id, $main_blocks) && !in_array($existing_block_id, $side_blocks)) {
                DB::table('block_setting')
                    ->where('block_id', '=', $existing_block_id)
                    ->delete();

                DB::table('block')
                    ->where('block_id', '=', $existing_block_id)
                    ->delete();
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
                    DB::table('block')
                        ->where('block_id', '=', $block_id)
                        ->update([
                            'block_order' => $block_order,
                            'location'    => $location,
                        ]);
                } else {
                    // New block
                    DB::table('block')->insert([
                        'gedcom_id'   => $tree_id,
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
     *
     * @return void
     */
    private function updateUserBlocks(int $user_id, array $main_blocks, array $side_blocks)
    {
        $existing_block_ids = DB::table('block')
            ->where('user_id', '=', $user_id)
            ->pluck('block_id');

        // Deleted blocks
        foreach ($existing_block_ids as $existing_block_id) {
            if (!in_array($existing_block_id, $main_blocks) && !in_array($existing_block_id, $side_blocks)) {
                DB::table('block_setting')
                    ->where('block_id', '=', $existing_block_id)
                    ->delete();

                DB::table('block')
                    ->where('block_id', '=', $existing_block_id)
                    ->delete();
            }
        }

        foreach ([
             'main' => $main_blocks,
             'side' => $side_blocks,
         ] as $location => $updated_blocks) {
            foreach ($updated_blocks as $block_order => $block_id) {
                if (is_numeric($block_id)) {
                    // Updated block
                    DB::table('block')
                        ->where('block_id', '=', $block_id)
                        ->update([
                            'block_order' => $block_order,
                            'location'    => $location,
                        ]);
                } else {
                    // New block
                    DB::table('block')->insert([
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
