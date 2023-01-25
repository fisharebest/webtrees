<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

/**
 * Save the updated blocks on a user's page.
 */
class UserPageUpdate implements RequestHandlerInterface
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
        $defaults = Validator::parsedBody($request)->boolean('defaults', false);

        if ($defaults) {
            $default_tree = new Tree(-1, 'DEFAULT', 'DEFAULT');

            $main_blocks = $this->home_page_service
                ->userBlocks($default_tree, $user, ModuleBlockInterface::MAIN_BLOCKS)
                ->map(static function (ModuleBlockInterface $block) {
                    return $block->name();
                });
            $side_blocks = $this->home_page_service
                ->userBlocks($default_tree, $user, ModuleBlockInterface::SIDE_BLOCKS)
                ->map(static function (ModuleBlockInterface $block) {
                    return $block->name();
                });
        } else {
            $main_blocks = new Collection(Validator::parsedBody($request)->array(ModuleBlockInterface::MAIN_BLOCKS));
            $side_blocks = new Collection(Validator::parsedBody($request)->array(ModuleBlockInterface::SIDE_BLOCKS));
        }

        $this->home_page_service->updateUserBlocks($user->id(), $main_blocks, $side_blocks);

        return redirect(route(UserPage::class, ['tree' => $tree->name()]));
    }
}
