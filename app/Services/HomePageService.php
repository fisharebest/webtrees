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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

use function assert;
use function is_numeric;

/**
 * Logic and content for the home-page blocks.
 */
class HomePageService
{
    /** @var ModuleService */
    private $module_service;

    /**
     * HomePageController constructor.
     *
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * Load a block and check we have permission to edit it.
     *
     * @param ServerRequestInterface $request
     * @param UserInterface          $user
     *
     * @return ModuleBlockInterface
     */
    public function treeBlock(ServerRequestInterface $request, UserInterface $user): ModuleBlockInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $block_id = (int) $request->getQueryParams()['block_id'];

        $block = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->where('gedcom_id', '=', $tree->id())
            ->whereNull('user_id')
            ->first();

        if (!$block instanceof stdClass) {
            throw new HttpNotFoundException();
        }

        $module = $this->module_service->findByName($block->module_name);

        if (!$module instanceof ModuleBlockInterface) {
            throw new HttpNotFoundException();
        }

        if ($block->user_id !== $user->id() && !Auth::isAdmin()) {
            throw new HttpAccessDeniedException();
        }

        return $module;
    }

    /**
     * Load a block and check we have permission to edit it.
     *
     * @param ServerRequestInterface $request
     * @param UserInterface          $user
     *
     * @return ModuleBlockInterface
     */
    public function userBlock(ServerRequestInterface $request, UserInterface $user): ModuleBlockInterface
    {
        $block_id = (int) $request->getQueryParams()['block_id'];

        $block = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->where('user_id', '=', $user->id())
            ->whereNull('gedcom_id')
            ->first();

        if (!$block instanceof stdClass) {
            throw new HttpNotFoundException('This block does not exist');
        }

        $module = $this->module_service->findByName($block->module_name);

        if (!$module instanceof ModuleBlockInterface) {
            throw new HttpNotFoundException($block->module_name . ' is not a block');
        }

        $block_owner_id = (int) $block->user_id;

        if ($block_owner_id !== $user->id() && !Auth::isAdmin()) {
            throw new HttpAccessDeniedException('You are not allowed to edit this block');
        }

        return $module;
    }

    /**
     * Get a specific block.
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return ModuleBlockInterface
     */
    public function getBlockModule(Tree $tree, int $block_id): ModuleBlockInterface
    {
        $active_blocks = $this->module_service->findByComponent(ModuleBlockInterface::class, $tree, Auth::user());

        $module_name = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->value('module_name');

        $block = $active_blocks->first(static function (ModuleInterface $module) use ($module_name): bool {
            return $module->name() === $module_name;
        });

        if ($block instanceof ModuleBlockInterface) {
            return $block;
        }

        throw new HttpNotFoundException('Block not found');
    }

    /**
     * Get all the available blocks for a tree page.
     *
     * @param Tree          $tree
     * @param UserInterface $user
     *
     * @return Collection<string,ModuleBlockInterface>
     */
    public function availableTreeBlocks(Tree $tree, UserInterface $user): Collection
    {
        return $this->module_service->findByComponent(ModuleBlockInterface::class, $tree, $user)
            ->filter(static function (ModuleBlockInterface $block): bool {
                return $block->isTreeBlock();
            })
            ->mapWithKeys(static function (ModuleBlockInterface $block): array {
                return [$block->name() => $block];
            });
    }

    /**
     * Get all the available blocks for a user page.
     *
     * @param Tree          $tree
     * @param UserInterface $user
     *
     * @return Collection<string,ModuleBlockInterface>
     */
    public function availableUserBlocks(Tree $tree, UserInterface $user): Collection
    {
        return $this->module_service->findByComponent(ModuleBlockInterface::class, $tree, $user)
            ->filter(static function (ModuleBlockInterface $block): bool {
                return $block->isUserBlock();
            })
            ->mapWithKeys(static function (ModuleBlockInterface $block): array {
                return [$block->name() => $block];
            });
    }

    /**
     * Get the blocks for a specified tree.
     *
     * @param Tree          $tree
     * @param UserInterface $user
     * @param string        $location "main" or "side"
     *
     * @return Collection<string,ModuleBlockInterface>
     */
    public function treeBlocks(Tree $tree, UserInterface $user, string $location): Collection
    {
        $rows = DB::table('block')
            ->where('gedcom_id', '=', $tree->id())
            ->where('location', '=', $location)
            ->orderBy('block_order')
            ->pluck('module_name', 'block_id');

        return $this->filterActiveBlocks($rows, $this->availableTreeBlocks($tree, $user));
    }

    /**
     * Make sure that default blocks exist for a tree.
     *
     * @return void
     */
    public function checkDefaultTreeBlocksExist(): void
    {
        $has_blocks = DB::table('block')
            ->where('gedcom_id', '=', -1)
            ->exists();

        // No default settings?  Create them.
        if (!$has_blocks) {
            foreach ([ModuleBlockInterface::MAIN_BLOCKS, ModuleBlockInterface::SIDE_BLOCKS] as $location) {
                foreach (ModuleBlockInterface::DEFAULT_TREE_PAGE_BLOCKS[$location] as $block_order => $class) {
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
     * @param Tree          $tree
     * @param UserInterface $user
     * @param string        $location "main" or "side"
     *
     * @return Collection<string,ModuleBlockInterface>
     */
    public function userBlocks(Tree $tree, UserInterface $user, string $location): Collection
    {
        $rows = DB::table('block')
            ->where('user_id', '=', $user->id())
            ->where('location', '=', $location)
            ->orderBy('block_order')
            ->pluck('module_name', 'block_id');

        return $this->filterActiveBlocks($rows, $this->availableUserBlocks($tree, $user));
    }

    /**
     * Make sure that default blocks exist for a user.
     *
     * @return void
     */
    public function checkDefaultUserBlocksExist(): void
    {
        $has_blocks = DB::table('block')
            ->where('user_id', '=', -1)
            ->exists();

        // No default settings?  Create them.
        if (!$has_blocks) {
            foreach ([ModuleBlockInterface::MAIN_BLOCKS, ModuleBlockInterface::SIDE_BLOCKS] as $location) {
                foreach (ModuleBlockInterface::DEFAULT_USER_PAGE_BLOCKS[$location] as $block_order => $class) {
                    $module = $this->module_service->findByInterface($class)->first();

                    if ($module instanceof ModuleBlockInterface) {
                        DB::table('block')->insert([
                            'user_id'     => -1,
                            'location'    => $location,
                            'block_order' => $block_order,
                            'module_name' => $module->name(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Save the updated blocks for a user.
     *
     * @param int                $user_id
     * @param Collection<string> $main_block_ids
     * @param Collection<string> $side_block_ids
     *
     * @return void
     */
    public function updateUserBlocks(int $user_id, Collection $main_block_ids, Collection $side_block_ids): void
    {
        $existing_block_ids = DB::table('block')
            ->where('user_id', '=', $user_id)
            ->whereIn('location', [ModuleBlockInterface::MAIN_BLOCKS, ModuleBlockInterface::SIDE_BLOCKS])
            ->pluck('block_id');

        // Deleted blocks
        foreach ($existing_block_ids as $existing_block_id) {
            if (!$main_block_ids->contains($existing_block_id) && !$side_block_ids->contains($existing_block_id)) {
                DB::table('block_setting')
                    ->where('block_id', '=', $existing_block_id)
                    ->delete();

                DB::table('block')
                    ->where('block_id', '=', $existing_block_id)
                    ->delete();
            }
        }

        $updates = [
            ModuleBlockInterface::MAIN_BLOCKS => $main_block_ids,
            ModuleBlockInterface::SIDE_BLOCKS => $side_block_ids,
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
     * @param int                $tree_id
     * @param Collection<string> $main_block_ids
     * @param Collection<string> $side_block_ids
     *
     * @return void
     */
    public function updateTreeBlocks(int $tree_id, Collection $main_block_ids, Collection $side_block_ids): void
    {
        $existing_block_ids = DB::table('block')
            ->where('gedcom_id', '=', $tree_id)
            ->whereIn('location', [ModuleBlockInterface::MAIN_BLOCKS, ModuleBlockInterface::SIDE_BLOCKS])
            ->pluck('block_id');

        // Deleted blocks
        foreach ($existing_block_ids as $existing_block_id) {
            if (!$main_block_ids->contains($existing_block_id) && !$side_block_ids->contains($existing_block_id)) {
                DB::table('block_setting')
                    ->where('block_id', '=', $existing_block_id)
                    ->delete();

                DB::table('block')
                    ->where('block_id', '=', $existing_block_id)
                    ->delete();
            }
        }

        $updates = [
            ModuleBlockInterface::MAIN_BLOCKS => $main_block_ids,
            ModuleBlockInterface::SIDE_BLOCKS => $side_block_ids,
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
     * @param Collection<string>                      $blocks
     * @param Collection<string,ModuleBlockInterface> $active_blocks
     *
     * @return Collection<string,ModuleBlockInterface>
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
