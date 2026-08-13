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
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;
use function route;

final class UserPageEdit
{
    use ViewResponseTrait;

    public function __construct(
        private readonly HomePageService $home_page_service
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {

        $tree        = Validator::attributes($request)->tree();
        $user        = Validator::attributes($request)->user();
        $main_blocks = $this->home_page_service->userBlocks($tree, $user, ModuleBlockInterface::MAIN_BLOCKS);
        $side_blocks = $this->home_page_service->userBlocks($tree, $user, ModuleBlockInterface::SIDE_BLOCKS);
        $all_blocks  = $this->home_page_service->availableUserBlocks($tree, $user);
        $title       = I18N::translate('Change the “My page” blocks');
        $url_cancel  = route(UserPage::class, ['tree' => $tree->name()]);
        $url_save    = route(UserPageEdit::class, ['tree' => $tree->name()]);

        return $this->viewResponse('edit-blocks-page', [
            'all_blocks'  => $all_blocks,
            'can_reset'   => true,
            'main_blocks' => $main_blocks,
            'side_blocks' => $side_blocks,
            'title'       => $title,
            'tree'        => $tree,
            'url_cancel'  => $url_cancel,
            'url_save'    => $url_save,
        ]);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {

        $tree     = Validator::attributes($request)->tree();
        $user     = Validator::attributes($request)->user();
        $defaults = Validator::parsedBody($request)->boolean('defaults', false);

        if ($defaults) {
            $default_tree = new Tree(-1, '', '', '', '', true, true, null, null);

            $main_blocks = $this->home_page_service
                ->userBlocks($default_tree, $user, ModuleBlockInterface::MAIN_BLOCKS)
                ->map(static fn (ModuleBlockInterface $block) => $block->name());
            $side_blocks = $this->home_page_service
                ->userBlocks($default_tree, $user, ModuleBlockInterface::SIDE_BLOCKS)
                ->map(static fn (ModuleBlockInterface $block) => $block->name());
        } else {
            $main_blocks = new Collection(Validator::parsedBody($request)->list(ModuleBlockInterface::MAIN_BLOCKS));
            $side_blocks = new Collection(Validator::parsedBody($request)->list(ModuleBlockInterface::SIDE_BLOCKS));
        }

        $this->home_page_service->updateUserBlocks($user->id(), $main_blocks, $side_blocks);

        return redirect(route(UserPage::class, ['tree' => $tree->name()]));
    }
}
