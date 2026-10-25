<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function response;
use function view;

final class TreePageBlock
{
    public function __construct(
        private HomePageService $home_page_service,
    ) {
    }

    public function get(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $block_id = Validator::queryParams($request)->integer('block_id');

        $block_id = (int) DB::table('block')
            ->where('block_id', '=', $block_id)
            ->where('gedcom_id', '=', $tree->id())
            ->value('block_id');

        $module = $this->home_page_service->getBlockModule($tree, $block_id);

        $html = view('layouts/ajax', [
            'content' => $module->getBlock($tree, $block_id, ModuleBlockInterface::CONTEXT_TREE_PAGE),
        ]);

        return response($html);
    }
}
