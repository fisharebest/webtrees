<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ModuleBlockInterface - Classes and libraries for module system
 */
interface ModuleBlockInterface extends ModuleInterface
{
    // Places we can display blocks
    public const CONTEXT_EMBED     = 'embed';
    public const CONTEXT_TREE_PAGE = 'tree';
    public const CONTEXT_USER_PAGE = 'user';

    // We show blocks in two columns on the tree/user pages.
    public const MAIN_BLOCKS = 'main';
    public const SIDE_BLOCKS = 'side';

    public const DEFAULT_TREE_PAGE_BLOCKS = [
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

    public const DEFAULT_USER_PAGE_BLOCKS = [
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
     * Generate the HTML content of this block.
     *
     * @param Tree                 $tree
     * @param int                  $block_id
     * @param string               $context
     * @param array<string,string> $config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string;

    /**
     * Should this block load asynchronously using AJAX?
     *
     * Simple blocks are faster in-line, more complex ones can be loaded later.
     *
     * @return bool
     */
    public function loadAjax(): bool;

    /**
     * Can this block be shown on the user’s home page?
     *
     * @return bool
     */
    public function isUserBlock(): bool;

    /**
     * Can this block be shown on the tree’s home page?
     *
     * @return bool
     */
    public function isTreeBlock(): bool;

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return string
     */
    public function editBlockConfiguration(Tree $tree, int $block_id): string;

    /**
     * Update the configuration for a block.
     *
     * @param ServerRequestInterface $request
     * @param int     $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void;
}
