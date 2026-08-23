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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Services\HomePageService;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;

final class UserPage
{
    use ViewResponseTrait;

    public function __construct(
        private UserInterface $user,
        private HomePageService $home_page_service,
    ) {
    }

    public function get(Tree $tree): ResponseInterface
    {
        $has_blocks = DB::table('block')
            ->where('user_id', '=', $this->user->id())
            ->exists();

        if (!$has_blocks) {
            $this->home_page_service->checkDefaultUserBlocksExist();

            $user = $this->user;

            // Copy the defaults
            DB::query()->from('block')->insertUsing(
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
            'main_blocks' => $this->home_page_service->userBlocks($tree, $this->user, ModuleBlockInterface::MAIN_BLOCKS),
            'side_blocks' => $this->home_page_service->userBlocks($tree, $this->user, ModuleBlockInterface::SIDE_BLOCKS),
            'title'       => I18N::translate('My page'),
            'tree'        => $tree,
        ]);
    }
}
