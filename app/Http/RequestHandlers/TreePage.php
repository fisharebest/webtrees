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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;

/**
 * Show a tree's page.
 */
class TreePage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private HomePageService $home_page_service;

    /**
     * @param HomePageService $home_page_service
     */
    public function __construct(HomePageService $home_page_service)
    {
        $this->home_page_service = $home_page_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();
        $user = Validator::attributes($request)->user();

        $has_blocks = DB::table('block')
            ->where('gedcom_id', '=', $tree->id())
            ->exists();

        if (!$has_blocks) {
            $this->home_page_service->checkDefaultTreeBlocksExist();

            // Copy the defaults
            DB::query()->from('block')->insertUsing(
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
            'main_blocks'      => $this->home_page_service->treeBlocks($tree, $user, ModuleBlockInterface::MAIN_BLOCKS),
            'side_blocks'      => $this->home_page_service->treeBlocks($tree, $user, ModuleBlockInterface::SIDE_BLOCKS),
            'title'            => e($tree->title()),
            'tree'             => $tree,
            'meta_robots'      => 'index,follow',
            'meta_description' => e($tree->getPreference('META_DESCRIPTION'))
        ]);
    }
}
