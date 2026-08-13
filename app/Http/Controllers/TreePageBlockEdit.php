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

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;
use function route;

final class TreePageBlockEdit
{
    use ViewResponseTrait;

    public function __construct(
        private readonly HomePageService $home_page_service
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {

        $tree     = Validator::attributes($request)->tree();
        $block_id = Validator::attributes($request)->integer('block_id');

        $block = $this->home_page_service->treeBlock($request);
        $title = $block->title() . ' — ' . I18N::translate('Preferences');

        return $this->viewResponse('modules/edit-block-config', [
            'block'      => $block,
            'block_id'   => $block_id,
            'cancel_url' => route(TreePage::class, ['tree' => $tree->name()]),
            'save_url'   => route(TreePageBlock::class, ['tree' => $tree->name(), 'block_id' => $block_id]),
            'title'      => $title,
            'tree'       => $tree,
        ]);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {

        $tree     = Validator::attributes($request)->tree();
        $block_id = Validator::attributes($request)->integer('block_id');

        $block = $this->home_page_service->treeBlock($request);

        $block->saveBlockConfiguration($request, $block_id);

        return redirect(route(TreePage::class, ['tree' => $tree->name()]));
    }
}
