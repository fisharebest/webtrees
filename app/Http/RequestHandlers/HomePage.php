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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

/**
 * Redirect to a user/tree page.
 */
class HomePage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private TreeService $tree_service;

    /**
     * @param TreeService $tree_service
     */
    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $default = Site::getPreference('DEFAULT_GEDCOM');
        $tree    = $this->tree_service->all()->get($default) ?? $this->tree_service->all()->first();
        $user    = Validator::attributes($request)->user();

        if ($tree instanceof Tree) {
            if ($tree->getPreference('imported') === '1') {
                // Logged in?  Go to the user's page.
                if ($user instanceof User) {
                    return redirect(route(UserPage::class, ['tree' => $tree->name()]));
                }

                // Not logged in?  Go to the tree's page.
                return redirect(route(TreePage::class, ['tree' => $tree->name()]));
            }

            if (Auth::isManager($tree, $user)) {
                return redirect(route(ManageTrees::class, ['tree' => $tree->name()]));
            }
        }

        // No tree available?  Create one.
        if (Auth::isAdmin($user)) {
            return redirect(route(CreateTreePage::class));
        }

        // Logged in, but no access to any tree.
        if ($user instanceof User) {
            return $this->viewResponse('errors/no-tree-access', ['title' => '', 'tree' => null]);
        }

        // Not logged in.
        return redirect(route(LoginPage::class, ['url' => '']));
    }
}
