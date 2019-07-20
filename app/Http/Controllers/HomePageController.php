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
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\FamilyTreeFavoritesModule;
use Fisharebest\Webtrees\Module\FamilyTreeNewsModule;
use Fisharebest\Webtrees\Module\FamilyTreeStatisticsModule;
use Fisharebest\Webtrees\Module\LoggedInUsersModule;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\OnThisDayModule;
use Fisharebest\Webtrees\Module\ReviewChangesModule;
use Fisharebest\Webtrees\Module\SlideShowModule;
use Fisharebest\Webtrees\Module\UpcomingAnniversariesModule;
use Fisharebest\Webtrees\Module\UserFavoritesModule;
use Fisharebest\Webtrees\Module\UserMessagesModule;
use Fisharebest\Webtrees\Module\UserWelcomeModule;
use Fisharebest\Webtrees\Module\WelcomeBlockModule;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for the user/tree's home page.
 */
class HomePageController extends AbstractBaseController
{
    // We show blocks in two columns on the tree/user pages.
    private const MAIN_BLOCKS = 'main';
    private const SIDE_BLOCKS = 'side';

    private const DEFAULT_TREE_PAGE_BLOCKS = [
        self::MAIN_BLOCKS => [
            1 => FamilyTreeStatisticsModule::class,
            2 => FamilyTreeNewsModule::class,
            3 => FamilyTreeFavoritesModule::class,
            4 => ReviewChangesModule::class,
        ],
        self::SIDE_BLOCKS => [
            1 => WelcomeBlockModule::class,
            2 => SlideShowModule::class,
            3 => OnThisDayModule::class,
            4 => LoggedInUsersModule::class,
        ],
    ];

    private const DEFAULT_USER_PAGE_BLOCKS = [
        self::MAIN_BLOCKS => [
            1 => OnThisDayModule::class,
            2 => UserMessagesModule::class,
            3 => UserFavoritesModule::class,
        ],
        self::SIDE_BLOCKS => [
            1 => UserWelcomeModule::class,
            2 => SlideShowModule::class,
            3 => UpcomingAnniversariesModule::class,
            4 => LoggedInUsersModule::class,
        ],
    ];

    /**
     * @var ModuleService
     */
    private $module_service;

    /**
     * @var UserService
     */
    private $user_service;

    /**
     * HomePageController constructor.
     *
     * @param ModuleService $module_service
     * @param UserService   $user_service
     */
    public function __construct(ModuleService $module_service, UserService $user_service)
    {
        $this->module_service = $module_service;
        $this->user_service   = $user_service;
    }

    /**
     * Show a form to edit block config options.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function treePageBlockEdit(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        $block_id = (int) $request->getQueryParams()['block_id'];
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
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function treePageBlockUpdate(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        $block    = $this->treeBlock($request, $tree, $user);
        $block_id = (int) $request->getQueryParams()['block_id'];

        $block->saveBlockConfiguration($request, $block_id);

        return redirect(route('tree-page', ['ged' => $tree->name()]));
    }

    /**
     * Load a block and check we have permission to edit it.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ModuleBlockInterface
     */
    private function treeBlock(ServerRequestInterface $request, Tree $tree, UserInterface $user): ModuleBlockInterface
    {
        $block_id = (int) $request->getQueryParams()['block_id'];

        $block = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->where('gedcom_id', '=', $tree->id())
            ->whereNull('user_id')
            ->first();

        if ($block === null) {
            throw new NotFoundHttpException();
        }

        $module = $this->module_service->findByName($block->module_name);

        if (!$module instanceof ModuleBlockInterface) {
            throw new NotFoundHttpException();
        }

        if ($block->user_id !== $user->id() && !Auth::isAdmin()) {
            throw new AccessDeniedHttpException();
        }

        return $module;
    }

    /**
     * Show a form to edit block config options.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function userPageBlockEdit(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        $block_id = (int) $request->getQueryParams()['block_id'];
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
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function userPageBlockUpdate(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        $block    = $this->userBlock($request, $user);
        $block_id = (int) $request->getQueryParams()['block_id'];

        $block->saveBlockConfiguration($request, $block_id);

        return redirect(route('user-page', ['ged' => $tree->name()]));
    }

    /**
     * Load a block and check we have permission to edit it.
     *
     * @param ServerRequestInterface $request
     * @param UserInterface          $user
     *
     * @return ModuleBlockInterface
     */
    private function userBlock(ServerRequestInterface $request, UserInterface $user): ModuleBlockInterface
    {
        $block_id = (int) $request->getQueryParams()['block_id'];

        $block = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->where('user_id', '=', $user->id())
            ->whereNull('gedcom_id')
            ->first();

        if ($block === null) {
            throw new NotFoundHttpException('This block does not exist');
        }

        $module = $this->module_service->findByName($block->module_name);

        if (!$module instanceof ModuleBlockInterface) {
            throw new NotFoundHttpException($block->module_name . ' is not a block');
        }

        $block_owner_id = (int) $block->user_id;

        if ($block_owner_id !== $user->id() && !Auth::isAdmin()) {
            throw new AccessDeniedHttpException('You are not allowed to edit this block');
        }

        return $module;
    }

    /**
     * Show a tree's page.
     *
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function treePage(Tree $tree): ResponseInterface
    {
        $has_blocks = DB::table('block')
            ->where('gedcom_id', '=', $tree->id())
            ->exists();

        if (!$has_blocks) {
            $this->checkDefaultTreeBlocksExist();

            // Copy the defaults
            (new Builder(DB::connection()))->from('block')->insertUsing(
                ['gedcom_id', 'location', 'block_order', 'module_name'],
                static function (Builder $query) use ($tree): void {
                    $query
                        ->select([new Expression($tree->id()), 'location', 'block_order', 'module_name'])
                        ->from('block')
                        ->where('gedcom_id', '=', -1);
                }
            );
        }

        return $this->viewResponse('tree-page', [
            'main_blocks' => $this->treeBlocks($tree->id(), self::MAIN_BLOCKS),
            'side_blocks' => $this->treeBlocks($tree->id(), self::SIDE_BLOCKS),
            'title'       => e($tree->title()),
            'meta_robots' => 'index,follow',
        ]);
    }

    /**
     * Load block asynchronously.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function treePageBlock(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $block_id = $request->getQueryParams()['block_id'];

        $block_id = (int) DB::table('block')
            ->where('block_id', '=', $block_id)
            ->where('gedcom_id', '=', $tree->id())
            ->value('block_id');

        $module = $this->getBlockModule($tree, $block_id);

        $html = view('layouts/ajax', [
            'content' => $module->getBlock($tree, $block_id, ModuleBlockInterface::CONTEXT_TREE_PAGE),
        ]);

        return response($html);
    }

    /**
     * Show a form to edit the default blocks for new trees.
     *
     * @return ResponseInterface
     */
    public function treePageDefaultEdit(): ResponseInterface
    {
        $this->checkDefaultTreeBlocksExist();

        $main_blocks = $this->treeBlocks(-1, self::MAIN_BLOCKS);
        $side_blocks = $this->treeBlocks(-1, self::SIDE_BLOCKS);

        $all_blocks = $this->availableTreeBlocks();
        $title      = I18N::translate('Set the default blocks for new family trees');
        $url_cancel = route('admin-control-panel');
        $url_save   = route('tree-page-default-update');

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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function treePageDefaultUpdate(ServerRequestInterface $request): ResponseInterface
    {
        $main_blocks = $request->getParsedBody()[self::MAIN_BLOCKS] ?? [];
        $side_blocks = $request->getParsedBody()[self::SIDE_BLOCKS] ?? [];

        $this->updateTreeBlocks(-1, $main_blocks, $side_blocks);

        return redirect(route('admin-control-panel'));
    }

    /**
     * Show a form to edit the blocks on a tree's page.
     *
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function treePageEdit(Tree $tree): ResponseInterface
    {
        $main_blocks = $this->treeBlocks($tree->id(), self::MAIN_BLOCKS);
        $side_blocks = $this->treeBlocks($tree->id(), self::SIDE_BLOCKS);

        $all_blocks = $this->availableTreeBlocks();
        $title      = I18N::translate('Change the “Home page” blocks');
        $url_cancel = route('tree-page', ['ged' => $tree->name()]);
        $url_save   = route('tree-page-update', ['ged' => $tree->name()]);

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
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function treePageUpdate(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $params = $request->getParsedBody();

        $defaults = (bool) ($params['defaults'] ?? false);

        if ($defaults) {
            $main_blocks = $this->treeBlocks(-1, self::MAIN_BLOCKS)
                ->map(static function (ModuleBlockInterface $block) {
                    return $block->name();
                })
                ->all();
            $side_blocks = $this->treeBlocks(-1, self::SIDE_BLOCKS)
                ->map(static function (ModuleBlockInterface $block) {
                    return $block->name();
                })
                ->all();
        } else {
            $main_blocks = $params[self::MAIN_BLOCKS] ?? [];
            $side_blocks = $params[self::SIDE_BLOCKS] ?? [];
        }

        $this->updateTreeBlocks($tree->id(), $main_blocks, $side_blocks);

        return redirect(route('tree-page', ['ged' => $tree->name()]));
    }

    /**
     * Show a users's page.
     *
     * @param UserInterface $user
     *
     * @return ResponseInterface
     */
    public function userPage(UserInterface $user): ResponseInterface
    {
        $has_blocks = DB::table('block')
            ->where('user_id', '=', $user->id())
            ->exists();

        if (!$has_blocks) {
            $this->checkDefaultUserBlocksExist();

            // Copy the defaults
            (new Builder(DB::connection()))->from('block')->insertUsing(
                ['user_id', 'location', 'block_order', 'module_name'],
                static function (Builder $query) use ($user): void {
                    $query
                        ->select([new Expression($user->id()), 'location', 'block_order', 'module_name'])
                        ->from('block')
                        ->where('user_id', '=', -1);
                }
            );
        }

        return $this->viewResponse('user-page', [
            'main_blocks' => $this->userBlocks($user->id(), self::MAIN_BLOCKS),
            'side_blocks' => $this->userBlocks($user->id(), self::SIDE_BLOCKS),
            'title'       => I18N::translate('My page'),
        ]);
    }

    /**
     * Load block asynchronously.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function userPageBlock(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        $block_id = $request->getQueryParams()['block_id'];

        $block_id = (int) DB::table('block')
            ->where('block_id', '=', $block_id)
            ->where('user_id', '=', $user->id())
            ->value('block_id');

        $module = $this->getBlockModule($tree, $block_id);

        $html = view('layouts/ajax', [
            'content' => $module->getBlock($tree, $block_id, ModuleBlockInterface::CONTEXT_USER_PAGE),
        ]);

        return response($html);
    }

    /**
     * Show a form to edit the default blocks for new uesrs.
     *
     * @return ResponseInterface
     */
    public function userPageDefaultEdit(): ResponseInterface
    {
        $this->checkDefaultUserBlocksExist();

        $main_blocks = $this->userBlocks(-1, self::MAIN_BLOCKS);
        $side_blocks = $this->userBlocks(-1, self::SIDE_BLOCKS);
        $all_blocks  = $this->availableUserBlocks();
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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function userPageDefaultUpdate(ServerRequestInterface $request): ResponseInterface
    {
        $main_blocks = $request->getParsedBody()[self::MAIN_BLOCKS] ?? [];
        $side_blocks = $request->getParsedBody()[self::SIDE_BLOCKS] ?? [];

        $this->updateUserBlocks(-1, $main_blocks, $side_blocks);

        return redirect(route('admin-control-panel'));
    }

    /**
     * Show a form to edit the blocks on the user's page.
     *
     * @param Tree          $tree
     * @param UserInterface $user
     *
     * @return ResponseInterface
     */
    public function userPageEdit(Tree $tree, UserInterface $user): ResponseInterface
    {
        $main_blocks = $this->userBlocks($user->id(), self::MAIN_BLOCKS);
        $side_blocks = $this->userBlocks($user->id(), self::SIDE_BLOCKS);
        $all_blocks  = $this->availableUserBlocks();
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
     * Save the updated blocks on a user's page.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function userPageUpdate(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        $params = $request->getParsedBody();

        $defaults = (bool) ($params['defaults'] ?? false);

        if ($defaults) {
            $main_blocks = $this->userBlocks(-1, self::MAIN_BLOCKS)
                ->map(static function (ModuleBlockInterface $block) {
                    return $block->name();
                })
                ->all();
            $side_blocks = $this->userBlocks(-1, self::SIDE_BLOCKS)
                ->map(static function (ModuleBlockInterface $block) {
                    return $block->name();
                })
                ->all();
        } else {
            $main_blocks = $params[self::MAIN_BLOCKS] ?? [];
            $side_blocks = $params[self::SIDE_BLOCKS] ?? [];
        }

        $this->updateUserBlocks($user->id(), $main_blocks, $side_blocks);

        return redirect(route('user-page', ['ged' => $tree->name()]));
    }

    /**
     * Show a form to edit the blocks for another user's page.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function userPageUserEdit(ServerRequestInterface $request): ResponseInterface
    {
        $user_id     = (int) $request->getQueryParams()['user_id'];
        $user        = $this->user_service->find($user_id);

        if ($user === null) {
            throw new NotFoundHttpException(I18N::translate('%1$s does not exist', 'user_id:' . $user_id));
        }

        $main_blocks = $this->userBlocks($user->id(), self::MAIN_BLOCKS);
        $side_blocks = $this->userBlocks($user->id(), self::SIDE_BLOCKS);
        $all_blocks  = $this->availableUserBlocks();
        $title       = I18N::translate('Change the blocks on this user’s “My page”') . ' - ' . e($user->userName());
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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function userPageUserUpdate(ServerRequestInterface $request): ResponseInterface
    {
        $user_id     = (int) $request->getQueryParams()['user_id'];
        $main_blocks = $request->getParsedBody()[self::MAIN_BLOCKS] ?? [];
        $side_blocks = $request->getParsedBody()[self::SIDE_BLOCKS] ?? [];

        $this->updateUserBlocks($user_id, $main_blocks, $side_blocks);

        return redirect(route('admin-control-panel'));
    }

    /**
     * Get a specific block.
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return ModuleBlockInterface
     * @throws NotFoundHttpException
     */
    private function getBlockModule(Tree $tree, int $block_id): ModuleBlockInterface
    {
        $active_blocks = $this->module_service->findByComponent(ModuleBlockInterface::class, $tree, Auth::user());

        $module_name = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->value('module_name');

        $block = $active_blocks->first(static function (ModuleInterface $module) use ($module_name): bool {
            return $module->name() === $module_name;
        });

        if ($block === null) {
            throw new NotFoundHttpException('Block not found');
        }

        return $block;
    }

    /**
     * Get all the available blocks for a tree page.
     *
     * @return Collection
     */
    private function availableTreeBlocks(): Collection
    {
        return $this->module_service->findByInterface(ModuleBlockInterface::class, false, true)
            ->filter(static function (ModuleBlockInterface $block): bool {
                return $block->isTreeBlock();
            })
            ->mapWithKeys(static function (ModuleInterface $block): array {
                return [$block->name() => $block];
            });
    }

    /**
     * Get all the available blocks for a user page.
     *
     * @return Collection
     */
    private function availableUserBlocks(): Collection
    {
        return $this->module_service->findByInterface(ModuleBlockInterface::class, false, true)
            ->filter(static function (ModuleBlockInterface $block): bool {
                return $block->isUserBlock();
            })
            ->mapWithKeys(static function (ModuleInterface $block): array {
                return [$block->name() => $block];
            });
    }

    /**
     * Get the blocks for a specified tree.
     *
     * @param int    $tree_id
     * @param string $location "main" or "side"
     *
     * @return Collection
     */
    private function treeBlocks(int $tree_id, string $location): Collection
    {
        $rows = DB::table('block')
            ->where('gedcom_id', '=', $tree_id)
            ->where('location', '=', $location)
            ->orderBy('block_order')
            ->pluck('module_name', 'block_id');

        return $this->filterActiveBlocks($rows, $this->availableTreeBlocks());
    }

    /**
     * Make sure that default blocks exist for a tree.
     *
     * @return void
     */
    private function checkDefaultTreeBlocksExist(): void
    {
        $has_blocks = DB::table('block')
            ->where('gedcom_id', '=', -1)
            ->exists();

        // No default settings?  Create them.
        if (!$has_blocks) {
            foreach ([self::MAIN_BLOCKS, self::SIDE_BLOCKS] as $location) {
                foreach (self::DEFAULT_TREE_PAGE_BLOCKS[$location] as $block_order => $class) {
                    $module_name = $this->module_service->findByInterface($class)->first()->name();

                    DB::table('block')->insert([
                        'gedcom_id'   => -1,
                        'location'    => $location,
                        'block_order' => $block_order,
                        'module_name' => $module_name,
                    ]);
                }
            }
        }
    }

    /**
     * Get the blocks for a specified user.
     *
     * @param int    $user_id
     * @param string $location "main" or "side"
     *
     * @return Collection
     */
    private function userBlocks(int $user_id, string $location): Collection
    {
        $rows = DB::table('block')
            ->where('user_id', '=', $user_id)
            ->where('location', '=', $location)
            ->orderBy('block_order')
            ->pluck('module_name', 'block_id');

        return $this->filterActiveBlocks($rows, $this->availableUserBlocks());
    }

    /**
     * Make sure that default blocks exist for a user.
     *
     * @return void
     */
    private function checkDefaultUserBlocksExist(): void
    {
        $has_blocks = DB::table('block')
            ->where('user_id', '=', -1)
            ->exists();

        // No default settings?  Create them.
        if (!$has_blocks) {
            foreach ([self::MAIN_BLOCKS, self::SIDE_BLOCKS] as $location) {
                foreach (self::DEFAULT_USER_PAGE_BLOCKS[$location] as $block_order => $class) {
                    $module_name = $this->module_service->findByInterface($class)->first()->name();

                    DB::table('block')->insert([
                        'user_id'     => -1,
                        'location'    => $location,
                        'block_order' => $block_order,
                        'module_name' => $module_name,
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
    private function updateUserBlocks(int $user_id, array $main_blocks, array $side_blocks): void
    {
        $existing_block_ids = DB::table('block')
            ->where('user_id', '=', $user_id)
            ->pluck('block_id');

        // Deleted blocks
        foreach ($existing_block_ids as $existing_block_id) {
            if (!in_array($existing_block_id, $main_blocks, false) && !in_array($existing_block_id, $side_blocks, false)) {
                DB::table('block_setting')
                    ->where('block_id', '=', $existing_block_id)
                    ->delete();

                DB::table('block')
                    ->where('block_id', '=', $existing_block_id)
                    ->delete();
            }
        }

        $updates = [
            self::MAIN_BLOCKS => $main_blocks,
            self::SIDE_BLOCKS => $side_blocks,
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
                        'user_id'     => $user_id,
                        'location'    => $location,
                        'block_order' => $block_order,
                        'module_name' => $block_id,
                    ]);
                }
            }
        }
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
    private function updateTreeBlocks(int $tree_id, array $main_blocks, array $side_blocks): void
    {
        $existing_block_ids = DB::table('block')
            ->where('gedcom_id', '=', $tree_id)
            ->pluck('block_id');

        // Deleted blocks
        foreach ($existing_block_ids as $existing_block_id) {
            if (!in_array($existing_block_id, $main_blocks, false) && !in_array($existing_block_id, $side_blocks, false)) {
                DB::table('block_setting')
                    ->where('block_id', '=', $existing_block_id)
                    ->delete();

                DB::table('block')
                    ->where('block_id', '=', $existing_block_id)
                    ->delete();
            }
        }

        $updates = [
            self::MAIN_BLOCKS => $main_blocks,
            self::SIDE_BLOCKS => $side_blocks,
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
     * Take a list of block names, and return block (module) objects.
     *
     * @param Collection $blocks
     * @param Collection $active_blocks
     *
     * @return Collection
     */
    private function filterActiveBlocks(Collection $blocks, Collection $active_blocks): Collection
    {
        return $blocks->map(static function (string $block_name) use ($active_blocks): ?ModuleBlockInterface {
            return $active_blocks->filter(static function (ModuleInterface $block) use ($block_name): bool {
                return $block->name() === $block_name;
            })->first();
        })->filter();
    }
}
