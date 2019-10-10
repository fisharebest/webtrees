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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ServerRequestInterface;

use function route;

/**
 * Trait ModuleBlockTrait - default implementation of ModuleBlockInterface
 */
trait ModuleBlockTrait
{
    /**
     * @param Tree   $tree
     * @param string $context
     * @param int    $block_id
     *
     * @return string
     */
    protected function configUrl(Tree $tree, string $context, int $block_id): string
    {
        if ($context === self::CONTEXT_TREE_PAGE && Auth::isManager($tree)) {
            return route('tree-page-block-edit', [
                'block_id' => $block_id,
                'tree'     => $tree->name(),
            ]);
        }

        if ($context === self::CONTEXT_USER_PAGE && Auth::check()) {
            return route('user-page-block-edit', [
                'block_id' => $block_id,
                'ged'      => $tree->name(),
            ]);
        }

        return '';
    }

    /**
     * Update the configuration for a block.
     *
     * @param ServerRequestInterface $request
     * @param int                    $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void
    {
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return string
     */
    public function editBlockConfiguration(Tree $tree, int $block_id): string
    {
        return '';
    }
}
