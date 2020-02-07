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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

/**
 * Show a users's page.
 */
class UserPage implements RequestHandlerInterface
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
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $user = $request->getAttribute('user');
        assert($user instanceof UserInterface);

        $has_blocks = DB::table('block')
            ->where('user_id', '=', $user->id())
            ->exists();

        if (!$has_blocks) {
            $this->home_page_service->checkDefaultUserBlocksExist();

            // Copy the defaults
            (new Builder(DB::connection()))->from('block')->insertUsing(
                ['user_id', 'location', 'block_order', 'module_name'],
                static function (Builder $query) use ($user): void {
                    $query
                        ->select([new Expression($user->id()), 'location', 'block_order', 'module_name'])
                        ->from('block')
                        ->where('user_id', '=', -1);
                }
            );
        }

        return $this->viewResponse('user-page', [
            'main_blocks' => $this->home_page_service->userBlocks($tree, $user, ModuleBlockInterface::MAIN_BLOCKS),
            'side_blocks' => $this->home_page_service->userBlocks($tree, $user, ModuleBlockInterface::SIDE_BLOCKS),
            'title'       => I18N::translate('My page'),
            'tree'        => $tree,
        ]);
    }
}
