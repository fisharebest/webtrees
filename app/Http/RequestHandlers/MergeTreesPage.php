<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\AdminService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Merge two family trees.
 */
class MergeTreesPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private AdminService $admin_service;

    private TreeService $tree_service;

    /**
     * @param AdminService   $admin_service
     * @param TreeService    $tree_service
     */
    public function __construct(AdminService $admin_service, TreeService $tree_service)
    {
        $this->admin_service   = $admin_service;
        $this->tree_service    = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $tree1_name = Validator::queryParams($request)->string('tree1_name', '');
        $tree2_name = Validator::queryParams($request)->string('tree2_name', '');

        $tree1 = $this->tree_service->all()->get($tree1_name);
        $tree2 = $this->tree_service->all()->get($tree2_name);

        if ($tree1 !== null && $tree2 !== null && $tree1->id() !== $tree2->id()) {
            $xrefs = $this->admin_service->countCommonXrefs($tree1, $tree2);
        } else {
            $xrefs = 0;
        }

        $title = I18N::translate(I18N::translate('Merge family trees'));

        return $this->viewResponse('admin/trees-merge', [
            'tree_list' => $this->tree_service->titles(),
            'tree1'     => $tree1,
            'tree2'     => $tree2,
            'title'     => $title,
            'xrefs'     => $xrefs,
        ]);
    }
}
