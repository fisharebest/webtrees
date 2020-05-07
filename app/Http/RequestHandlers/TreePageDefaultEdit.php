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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function route;

/**
 * Show a form to edit the default blocks for new trees.
 */
class TreePageDefaultEdit implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var HomePageService */
    private $home_page_service;

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
        $this->layout = 'layouts/administration';

        $this->home_page_service->checkDefaultTreeBlocksExist();

        $default_tree = new Tree(-1, 'DEFAULT', 'DEFAULT');
        $default_user = new User(-1, 'DEFAULT', 'DEFAULT', 'DEFAULT');

        $main_blocks = $this->home_page_service->treeBlocks($default_tree, $default_user, ModuleBlockInterface::MAIN_BLOCKS);
        $side_blocks = $this->home_page_service->treeBlocks($default_tree, $default_user, ModuleBlockInterface::SIDE_BLOCKS);

        $all_blocks = $this->home_page_service->availableTreeBlocks($default_tree, $default_user);
        $title      = I18N::translate('Set the default blocks for new family trees');
        $url_cancel = route(ControlPanel::class);
        $url_save   = route(TreePageDefaultUpdate::class);

        return $this->viewResponse('edit-blocks-page', [
            'all_blocks'  => $all_blocks,
            'can_reset'   => false,
            'main_blocks' => $main_blocks,
            'side_blocks' => $side_blocks,
            'title'       => $title,
            'url_cancel'  => $url_cancel,
            'url_save'    => $url_save,
        ]);
    }
}
