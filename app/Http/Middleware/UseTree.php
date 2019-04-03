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

namespace Fisharebest\Webtrees\Http\Middleware;

use Closure;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Middleware to set a global tree.
 */
class UseTree implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Most requests will need the current tree and user.
        $tree = Tree::findByName($request->get('ged')) ?? null;

        // No tree specified/available?  Choose one.
        if ($tree === null && $request->getMethod() === Request::METHOD_GET) {
            $tree = Tree::findByName(Site::getPreference('DEFAULT_GEDCOM')) ?? array_values(Tree::getAll())[0] ?? null;
        }

        // Most layouts will require a tree for the page header/footer
        View::share('tree', $tree);

        // Need a closure, as the container does not allow you to bind null.
        app()->bind(Tree::class, static function () use ($tree): ?Tree {
            return $tree;
        });

        return $next($request);
    }
}
