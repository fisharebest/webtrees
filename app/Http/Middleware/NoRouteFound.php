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

use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\RequestHandlers\LoginPage;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function redirect;
use function route;

/**
 * Middleware to generate a response when no route was matched.
 */
class NoRouteFound implements MiddlewareInterface
{
    use ViewResponseTrait;

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() !== RequestMethodInterface::METHOD_GET) {
            throw new NotFoundHttpException();
        }

        $user = $request->getAttribute('user');

        // Choose the default tree (if it exists), or the first tree found.
        $default = Site::getPreference('DEFAULT_GEDCOM');
        $tree    = Tree::findByName($default) ?? Tree::all()->first();

        if ($tree instanceof Tree) {
            if ($tree->getPreference('imported') === '1') {
                // Logged in?  Go to the user's page.
                if ($user instanceof User) {
                    return redirect(route('user-page', ['tree' => $tree->name()]));
                }

                // Not logged in?  Go to the tree's page.
                return redirect(route('tree-page', ['ged' => $tree->name()]));
            }

            return redirect(route('admin-trees', ['ged' => $tree->name()]));
        }

        // No tree available?  Create one.
        if (Auth::isAdmin($user)) {
            return redirect(route('admin-trees'));
        }

        // Logged in, but no access to any tree.
        if ($user instanceof User) {
            return $this->viewResponse('errors/no-tree-access', ['title' => '']);
        }

        // Not logged in.
        return redirect(route(LoginPage::class, ['url' => $request->getAttribute('request_uri')]));
    }
}
