<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

/**
 * Update block config options.
 */
class UserPageBlockUpdate implements RequestHandlerInterface
{
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
        $tree     = Validator::attributes($request)->tree();
        $user     = Validator::attributes($request)->user();
        $block_id = Validator::attributes($request)->integer('block_id');

        $block = $this->home_page_service->userBlock($request, $user);
        $block->saveBlockConfiguration($request, $block_id);

        return redirect(route(UserPage::class, ['tree' => $tree->name()]));
    }
}
